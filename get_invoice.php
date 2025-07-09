<?php
header('Content-Type: application/json');
require_once 'inc/config.php';
require_once 'inc/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Invalid request method');
    }
    
    $id = $_GET['id'] ?? '';
    
    if (empty($id)) {
        throw new Exception('Invoice ID is required');
    }
    
    $sql = "SELECT i.*, c.name as customer_name 
            FROM invoices i 
            LEFT JOIN customers c ON i.customer_id = c.id 
            WHERE i.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Invoice not found');
    }
    
    $invoice = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $invoice['id'],
            'invoice_no' => $invoice['invoice_no'],
            'customer_id' => $invoice['customer_id'],
            'customer_name' => $invoice['customer_name'],
            'destination' => $invoice['destination'],
            'invoice_date' => $invoice['invoice_date'],
            'from_date' => $invoice['from_date'],
            'to_date' => $invoice['to_date'],
            'total_amount' => $invoice['total_amount'],
            'gst_amount' => $invoice['gst_amount'],
            'grand_total' => $invoice['grand_total'],
            'status' => $invoice['status'],
            'created_at' => $invoice['created_at'],
            'updated_at' => $invoice['updated_at']
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
