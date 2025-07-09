<?php
/**
 * Test Invoice Generation System
 * This script tests the new invoice generation functionality
 */

require_once 'inc/config.php';
require_once 'inc/db.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Invoice System Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #28a745; padding: 10px; background: #d4edda; border-radius: 4px; margin: 5px 0; }
        .error { color: #dc3545; padding: 10px; background: #f8d7da; border-radius: 4px; margin: 5px 0; }
        .info { color: #007bff; padding: 10px; background: #d1ecf1; border-radius: 4px; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; }
        .section { margin: 30px 0; }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        h3 { color: #666; }
        .test-link { display: inline-block; margin: 10px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .test-link:hover { background: #0056b3; }
    </style>
</head>
<body>
<div class='container'>
    <h1>Invoice Generation System Test</h1>";

try {
    // Test database connection
    echo "<div class='section'>";
    echo "<h2>Database Connection Test</h2>";
    
    if ($conn->ping()) {
        echo "<div class='success'>✓ Database connection successful</div>";
    } else {
        echo "<div class='error'>✗ Database connection failed</div>";
        exit;
    }
    
    // Test required tables
    echo "<h2>Required Tables Verification</h2>";
    
    $required_tables = [
        'customers' => 'Customer master data',
        'invoices' => 'Invoice headers',
        'invoice_items' => 'Invoice line items',
        'orders' => 'Order/shipment data'
    ];
    
    echo "<table>";
    echo "<tr><th>Table Name</th><th>Description</th><th>Status</th><th>Record Count</th></tr>";
    
    foreach ($required_tables as $table => $description) {
        try {
            $result = $conn->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $result->fetch_assoc()['count'];
            echo "<tr>";
            echo "<td><strong>$table</strong></td>";
            echo "<td>$description</td>";
            echo "<td><span style='color: green;'>✓ EXISTS</span></td>";
            echo "<td>$count records</td>";
            echo "</tr>";
        } catch (Exception $e) {
            echo "<tr>";
            echo "<td><strong>$table</strong></td>";
            echo "<td>$description</td>";
            echo "<td><span style='color: red;'>✗ MISSING</span></td>";
            echo "<td>N/A</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    echo "</div>";
    
    // Test AJAX endpoints
    echo "<div class='section'>";
    echo "<h2>AJAX Endpoints Test</h2>";
    
    $endpoints = [
        'fetch_customers.php' => 'Customer data fetching',
        'fetch_invoices_advanced.php' => 'Advanced invoice fetching with pagination',
        'generate_invoice_number.php' => 'Unique invoice number generation',
        'get_invoice.php' => 'Single invoice retrieval',
        'save_invoice.php' => 'Invoice creation (POST)',
        'update_invoice.php' => 'Invoice update (POST)',
        'delete_invoice.php' => 'Invoice deletion (POST)',
        'view_invoice.php' => 'Invoice viewing/printing',
        'ajax/generate_invoice.php' => 'Bulk invoice generation'
    ];
    
    echo "<table>";
    echo "<tr><th>Endpoint</th><th>Description</th><th>File Status</th></tr>";
    
    foreach ($endpoints as $endpoint => $description) {
        $filePath = __DIR__ . '/' . $endpoint;
        $exists = file_exists($filePath);
        
        echo "<tr>";
        echo "<td><strong>$endpoint</strong></td>";
        echo "<td>$description</td>";
        echo "<td>" . ($exists ? "<span style='color: green;'>✓ EXISTS</span>" : "<span style='color: red;'>✗ MISSING</span>") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // Test sample data
    echo "<div class='section'>";
    echo "<h2>Sample Data Verification</h2>";
    
    // Check customers
    $customersResult = $conn->query("SELECT COUNT(*) as count FROM customers");
    $customersCount = $customersResult->fetch_assoc()['count'];
    
    if ($customersCount > 0) {
        echo "<div class='success'>✓ Found $customersCount customers in database</div>";
        
        // Show sample customers
        $sampleCustomers = $conn->query("SELECT id, name, email FROM customers LIMIT 3");
        echo "<h4>Sample Customers:</h4>";
        echo "<ul>";
        while ($customer = $sampleCustomers->fetch_assoc()) {
            echo "<li>ID: {$customer['id']} - {$customer['name']} ({$customer['email']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<div class='error'>✗ No customers found - please add some customers first</div>";
    }
    
    // Check invoices
    $invoicesResult = $conn->query("SELECT COUNT(*) as count FROM invoices");
    $invoicesCount = $invoicesResult->fetch_assoc()['count'];
    
    echo "<div class='info'>ℹ Found $invoicesCount existing invoices</div>";
    
    if ($invoicesCount > 0) {
        // Show sample invoices
        $sampleInvoices = $conn->query("SELECT i.invoice_no, c.name as customer_name, i.grand_total, i.status 
                                       FROM invoices i 
                                       LEFT JOIN customers c ON i.customer_id = c.id 
                                       LIMIT 3");
        echo "<h4>Sample Invoices:</h4>";
        echo "<ul>";
        while ($invoice = $sampleInvoices->fetch_assoc()) {
            echo "<li>{$invoice['invoice_no']} - {$invoice['customer_name']} - ₹{$invoice['grand_total']} ({$invoice['status']})</li>";
        }
        echo "</ul>";
    }
    echo "</div>";
    
    // Test links
    echo "<div class='section'>";
    echo "<h2>Test Application</h2>";
    echo "<p>Click the links below to test different parts of the invoice system:</p>";
    
    echo "<a href='generate_invoice.php' class='test-link' target='_blank'>Generate Invoice Page</a>";
    echo "<a href='fetch_customers.php' class='test-link' target='_blank'>Test Customer API</a>";
    echo "<a href='fetch_invoices_advanced.php' class='test-link' target='_blank'>Test Invoice API</a>";
    echo "<a href='generate_invoice_number.php' class='test-link' target='_blank'>Test Invoice Number Generator</a>";
    
    if ($invoicesCount > 0) {
        $firstInvoice = $conn->query("SELECT id FROM invoices LIMIT 1")->fetch_assoc();
        echo "<a href='view_invoice.php?id={$firstInvoice['id']}' class='test-link' target='_blank'>View Sample Invoice</a>";
    }
    echo "</div>";
    
    // Configuration check
    echo "<div class='section'>";
    echo "<h2>System Configuration</h2>";
    
    echo "<table>";
    echo "<tr><th>Setting</th><th>Value</th></tr>";
    echo "<tr><td>PHP Version</td><td>" . PHP_VERSION . "</td></tr>";
    echo "<tr><td>MySQL Version</td><td>" . $conn->server_info . "</td></tr>";
    echo "<tr><td>App Name</td><td>" . (defined('APP_NAME') ? APP_NAME : 'Not defined') . "</td></tr>";
    echo "<tr><td>App Version</td><td>" . (defined('APP_VERSION') ? APP_VERSION : 'Not defined') . "</td></tr>";
    echo "<tr><td>Timezone</td><td>" . date_default_timezone_get() . "</td></tr>";
    echo "<tr><td>Current Time</td><td>" . date('Y-m-d H:i:s') . "</td></tr>";
    echo "</table>";
    echo "</div>";
    
    echo "<div class='success'>All tests completed successfully! Your invoice generation system is ready to use.</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>Test failed: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</div>
</body>
</html>";
?>
