<?php
echo "=== Testing Fixed Endpoints ===\n\n";

// Test fetch_invoices_advanced.php
echo "Testing fetch_invoices_advanced.php:\n";
$_GET['page'] = '1';
ob_start();
include 'fetch_invoices_advanced.php';
$invoices_result = ob_get_clean();
echo $invoices_result . "\n\n";

echo "=== Test Complete ===\n";
?>
