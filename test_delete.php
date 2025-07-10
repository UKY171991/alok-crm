<?php
// Test delete invoice endpoint

// Simulate POST data
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['id'] = '1';

// Capture output
ob_start();
include 'ajax/delete_invoice.php';
$output = ob_get_clean();

echo "Output: " . $output . "\n";
?>
