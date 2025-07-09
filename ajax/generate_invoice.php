<?php
header('Content-Type: application/json');
require_once 'inc/config.php';
require_once 'inc/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    $invoiceDate = $_POST['invoice_date'] ?? '';
    $fromDate = $_POST['from_date'] ?? '';
    $toDate = $_POST['to_date'] ?? '';
    
    if (empty($invoiceDate) || empty($fromDate) || empty($toDate)) {
        throw new Exception('Invoice date and booking date range are required');
    }
    
    if (strtotime($fromDate) > strtotime($toDate)) {
        throw new Exception('From date cannot be greater than to date');
    }
    
    // Generate unique invoice number
    $invoiceNo = generateUniqueInvoiceNumber($conn);
    
    // Find orders within the date range that haven't been invoiced yet
    $ordersSql = "SELECT o.*, c.name as customer_name 
                  FROM orders o 
                  LEFT JOIN customers c ON o.customer_id = c.id 
                  WHERE DATE(o.date) BETWEEN ? AND ? 
                  AND o.id NOT IN (
                      SELECT DISTINCT COALESCE(ii.order_id, 0) 
                      FROM invoice_items ii 
                      WHERE ii.order_id IS NOT NULL
                  )
                  ORDER BY o.customer_id, o.date";
    
    $ordersStmt = $conn->prepare($ordersSql);
    $ordersStmt->bind_param('ss', $fromDate, $toDate);
    $ordersStmt->execute();
    $ordersResult = $ordersStmt->get_result();
    
    if ($ordersResult->num_rows === 0) {
        throw new Exception('No unbilled orders found in the selected date range');
    }
    
    // Group orders by customer
    $customerOrders = [];
    while ($order = $ordersResult->fetch_assoc()) {
        $customerId = $order['customer_id'] ?? 0;
        if (!isset($customerOrders[$customerId])) {
            $customerOrders[$customerId] = [
                'customer_name' => $order['customer_name'] ?? 'Unknown Customer',
                'orders' => []
            ];
        }
        $customerOrders[$customerId]['orders'][] = $order;
    }
    
    $createdInvoices = [];
    
    // Create invoices for each customer
    foreach ($customerOrders as $customerId => $customerData) {
        $customerInvoiceNo = $invoiceNo . '-' . str_pad($customerId, 3, '0', STR_PAD_LEFT);
        
        // Calculate totals
        $totalAmount = 0;
        foreach ($customerData['orders'] as $order) {
            $totalAmount += ($order['clinet_billing_value'] ?? 0);
        }
        
        $gstAmount = $totalAmount * 0.18; // 18% GST
        $grandTotal = $totalAmount + $gstAmount;
        
        // Insert invoice
        $invoiceSql = "INSERT INTO invoices (invoice_no, customer_id, invoice_date, from_date, to_date, total_amount, gst_amount, grand_total, status) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        $invoiceStmt = $conn->prepare($invoiceSql);
        $invoiceStmt->bind_param('sisssddd', $customerInvoiceNo, $customerId, $invoiceDate, $fromDate, $toDate, $totalAmount, $gstAmount, $grandTotal);
        
        if ($invoiceStmt->execute()) {
            $invoiceId = $conn->insert_id;
            
            // Insert invoice items
            foreach ($customerData['orders'] as $order) {
                $itemSql = "INSERT INTO invoice_items (invoice_id, booking_date, consignment_no, destination, service, mode, no_of_pcs, weight, rate, amount) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $itemStmt = $conn->prepare($itemSql);
                $itemStmt->bind_param('isssssiddd', 
                    $invoiceId,
                    $order['date'],
                    $order['docket'],
                    $order['destination'],
                    $order['dox_non_dox'],
                    $order['mode'],
                    $order['no_of_pcs'],
                    $order['invoice_wt'],
                    $order['regular_cust_amt'],
                    $order['clinet_billing_value']
                );
                $itemStmt->execute();
            }
            
            $createdInvoices[] = [
                'invoice_no' => $customerInvoiceNo,
                'customer_name' => $customerData['customer_name'],
                'total_amount' => $totalAmount,
                'grand_total' => $grandTotal,
                'order_count' => count($customerData['orders'])
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => count($createdInvoices) . ' invoice(s) generated successfully',
        'data' => $createdInvoices
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function generateUniqueInvoiceNumber($conn) {
    $prefix = 'INV-' . date('Ymd');
    $counter = 1;
    
    do {
        $invoiceNo = $prefix . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
        $checkSql = "SELECT COUNT(*) as count FROM invoices WHERE invoice_no LIKE ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkParam = $invoiceNo . '%';
        $checkStmt->bind_param('s', $checkParam);
        $checkStmt->execute();
        $result = $checkStmt->get_result()->fetch_assoc();
        
        if ($result['count'] == 0) {
            return $invoiceNo;
        }
        $counter++;
    } while ($counter <= 9999);
    
    throw new Exception('Unable to generate unique invoice number');
}
?>
