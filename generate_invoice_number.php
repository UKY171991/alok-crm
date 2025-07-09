<?php
header('Content-Type: application/json');
require_once 'inc/config.php';
require_once 'inc/db.php';

try {
    // Generate unique invoice number
    $prefix = 'INV-' . date('Ymd');
    $counter = 1;
    
    do {
        $invoiceNo = $prefix . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
        
        // Check if this invoice number exists
        $checkSql = "SELECT COUNT(*) as count FROM invoices WHERE invoice_no = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param('s', $invoiceNo);
        $checkStmt->execute();
        $result = $checkStmt->get_result()->fetch_assoc();
        
        if ($result['count'] == 0) {
            // Found unique number
            echo json_encode([
                'success' => true,
                'invoice_no' => $invoiceNo
            ]);
            exit;
        }
        
        $counter++;
    } while ($counter <= 9999);
    
    throw new Exception('Unable to generate unique invoice number');
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
