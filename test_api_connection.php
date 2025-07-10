<?php
// Test script to check if our API fallback is working
echo "Testing API endpoints...\n\n";

// Test customers endpoint
echo "Testing customers endpoint:\n";
$customers_url = "http://localhost/alok-crm/api_fallback.php?endpoint=customers";
$customers_result = file_get_contents($customers_url);
echo "Customers response: " . $customers_result . "\n\n";

// Test invoices endpoint
echo "Testing invoices endpoint:\n";
$invoices_url = "http://localhost/alok-crm/api_fallback.php?endpoint=invoices&page=1";
$invoices_result = file_get_contents($invoices_url);
echo "Invoices response: " . $invoices_result . "\n\n";

echo "Test completed.\n";
?>
