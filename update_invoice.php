<?php
header('Content-Type: application/json');
require_once 'inc/config.php';
require_once 'inc/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    $id = $_POST['invoice_id'] ?? $_POST['id'] ?? '';
    
    if (empty($id)) {
        throw new Exception('Invoice ID is required');
    }
    
    $id = intval($id);
    
    // Validate required fields
    $customerId = $_POST['customer_id'] ?? '';
    $invoiceDate = $_POST['invoice_date'] ?? '';
    
    if (empty($customerId) || empty($invoiceDate)) {
        throw new Exception('Customer and invoice date are required');
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
    
    // Validate invoice exists
    $invoiceCheck = $conn->prepare("SELECT id FROM invoices WHERE id = ?");
    $invoiceCheck->bind_param('i', $id);
    $invoiceCheck->execute();
    if ($invoiceCheck->get_result()->num_rows === 0) {
        throw new Exception('Invoice not found');
    }
    
    // Calculate grand total if not provided
    if ($grandTotal == 0) {
        $grandTotal = $totalAmount + $gstAmount;
    }
    
    // Update invoice
    $sql = "UPDATE invoices SET 
            customer_id = ?, 
            destination = ?, 
            invoice_date = ?, 
            from_date = ?, 
            to_date = ?, 
            total_amount = ?, 
            gst_amount = ?, 
            grand_total = ?, 
            status = ?,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isssssddsi', $customerId, $destination, $invoiceDate, $fromDate, $toDate, $totalAmount, $gstAmount, $grandTotal, $status, $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Invoice updated successfully',
            'data' => [
                'id' => $id
            ]
        ]);
    } else {
        throw new Exception('Failed to update invoice: ' . $conn->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
