<?php
// Test the API endpoints
echo "=== Testing API Fallback ===\n\n";

// Test customers endpoint
echo "Testing customers endpoint:\n";
$_GET['endpoint'] = 'customers';
ob_start();
include 'api_fallback.php';
$customers_result = ob_get_clean();
echo $customers_result . "\n\n";

// Test invoices endpoint  
echo "Testing invoices endpoint:\n";
$_GET['endpoint'] = 'invoices';
ob_start();
include 'api_fallback.php';
$invoices_result = ob_get_clean();
echo $invoices_result . "\n\n";

echo "=== Test Complete ===\n";
?>
