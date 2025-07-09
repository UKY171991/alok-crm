<?php
header('Content-Type: application/json');
require_once 'inc/config.php';
require_once 'inc/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    $id = $_POST['id'] ?? '';
    
    if (empty($id)) {
        throw new Exception('Invoice ID is required');
    }
    
    $id = intval($id);
    
    // Check if invoice exists
    $checkSql = "SELECT invoice_no FROM invoices WHERE id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param('i', $id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Invoice not found');
    }
    
    $invoice = $result->fetch_assoc();
    
    // Delete related invoice items first
    $deleteItemsSql = "DELETE FROM invoice_items WHERE invoice_id = ?";
    $deleteItemsStmt = $conn->prepare($deleteItemsSql);
    $deleteItemsStmt->bind_param('i', $id);
    $deleteItemsStmt->execute();
    
    // Delete the invoice
    $deleteSql = "DELETE FROM invoices WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param('i', $id);
    
    if ($deleteStmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Invoice deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete invoice: ' . $conn->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
