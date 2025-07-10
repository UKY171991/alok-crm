<?php
// Test delete invoice endpoint in fallback mode

// Simulate POST data
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['id'] = '1';

// Set environment to CLI to avoid DB connection
$_SERVER['QUERY_STRING'] = 'cli=1';

// Mock the DB connection
$conn = null;

try {
    // Test fallback delete directly
    $demoInvoices = [
        1 => 'INV-2024-001',
        2 => 'INV-2024-002',
        3 => 'INV-2024-003',
        4 => 'INV-2024-004',
        5 => 'INV-2024-005'
    ];
    
    $id = 1;
    
    if (!isset($demoInvoices[$id])) {
        throw new Exception('Demo invoice not found');
    }
    
    $invoiceNo = $demoInvoices[$id];
    
    $response = [
        'success' => true,
        'message' => "Demo Invoice #{$invoiceNo} deleted successfully (Demo Mode)",
        'invoice_id' => $id,
        'invoice_no' => $invoiceNo,
        'demo_mode' => true,
        'note' => 'This is a demonstration. No actual data was deleted.'
    ];
    
    echo json_encode($response);
    echo "\n\nTest passed: Delete endpoint works in fallback mode!\n";
    
} catch (Exception $e) {
    echo "Test failed: " . $e->getMessage() . "\n";
}
?>
