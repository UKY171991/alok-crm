<?php
header('Content-Type: application/json');
require_once 'inc/config.php';
require_once 'inc/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Validate required fields
    $customerId = $_POST['customer_id'] ?? '';
    $invoiceDate = $_POST['invoice_date'] ?? '';
    $invoiceNo = $_POST['invoice_no'] ?? '';
    
    if (empty($customerId) || empty($invoiceDate) || empty($invoiceNo)) {
        throw new Exception('Customer, invoice date, and invoice number are required');
    }
    
    // Sanitize inputs
    $customerId = intval($customerId);
    $destination = sanitize_input($_POST['destination'] ?? '');
    $fromDate = $_POST['from_date'] ?? null;
    $toDate = $_POST['to_date'] ?? null;
    $totalAmount = floatval($_POST['total_amount'] ?? 0);
    $gstAmount = floatval($_POST['gst_amount'] ?? 0);
    $grandTotal = floatval($_POST['grand_total'] ?? 0);
    $status = sanitize_input($_POST['status'] ?? 'pending');
    
    // Validate customer exists
    $customerCheck = $conn->prepare("SELECT id FROM customers WHERE id = ?");
    $customerCheck->bind_param('i', $customerId);
    $customerCheck->execute();
    if ($customerCheck->get_result()->num_rows === 0) {
        throw new Exception('Invalid customer selected');
    }
    
    // Check if invoice number already exists
    $invoiceCheck = $conn->prepare("SELECT id FROM invoices WHERE invoice_no = ?");
    $invoiceCheck->bind_param('s', $invoiceNo);
    $invoiceCheck->execute();
    if ($invoiceCheck->get_result()->num_rows > 0) {
        throw new Exception('Invoice number already exists');
    }
    
    // Calculate grand total if not provided
    if ($grandTotal == 0) {
        $grandTotal = $totalAmount + $gstAmount;
    }
    
    // Insert invoice
    $sql = "INSERT INTO invoices (invoice_no, customer_id, destination, invoice_date, from_date, to_date, total_amount, gst_amount, grand_total, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sissssddds', $invoiceNo, $customerId, $destination, $invoiceDate, $fromDate, $toDate, $totalAmount, $gstAmount, $grandTotal, $status);
    
    if ($stmt->execute()) {
        $invoiceId = $conn->insert_id;
        
        echo json_encode([
            'success' => true,
            'message' => 'Invoice created successfully',
            'data' => [
                'id' => $invoiceId,
                'invoice_no' => $invoiceNo
            ]
        ]);
    } else {
        throw new Exception('Failed to create invoice: ' . $conn->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
