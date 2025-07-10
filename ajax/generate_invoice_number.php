<?php
header('Content-Type: application/json');
require_once '../inc/config.php';

try {
    require_once '../inc/db.php';
    
    // Generate invoice number
    $query = "SELECT MAX(id) AS max_id FROM invoices";
    $result = $conn->query($query);
    $maxId = 1;

    if ($result && $row = $result->fetch_assoc()) {
        $maxId = intval($row['max_id']) + 1;
    }

    $invoiceNo = "INV-" . date('Ymd') . "-" . str_pad($maxId, 3, "0", STR_PAD_LEFT);
    
    echo json_encode([
        'success' => true,
        'invoice_no' => $invoiceNo
    ]);
    
} catch (Exception $e) {
    // Fallback for when database is not available
    $invoiceNo = "INV-" . date('Ymd') . "-" . str_pad(rand(1, 999), 3, "0", STR_PAD_LEFT);
    
    echo json_encode([
        'success' => true,
        'invoice_no' => $invoiceNo,
        'source' => 'mock'
    ]);
}
?>
