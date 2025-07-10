<?php
echo "=== Testing API Fallback Endpoints ===\n\n";

// Test customers endpoint
echo "Testing customers endpoint:\n";
$_GET['endpoint'] = 'customers';
unset($_GET['page']); // Clean previous data
ob_start();
include 'api_fallback.php';
$customers_result = ob_get_clean();
echo $customers_result . "\n\n";

// Reset $_GET for next test
unset($_GET['endpoint']);

// Test invoices endpoint  
echo "Testing invoices endpoint:\n";
$_GET['endpoint'] = 'invoices';
$_GET['page'] = '1';
ob_start();
include 'api_fallback.php';
$invoices_result = ob_get_clean();
echo $invoices_result . "\n\n";

echo "=== Test Complete ===\n";
?>
