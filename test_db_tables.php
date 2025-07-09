<?php
require_once 'inc/config.php';
require_once 'inc/db.php';

echo "Testing database tables...\n";

// Test if invoices table exists
$result = $conn->query('SHOW TABLES LIKE "invoices"');
if ($result->num_rows > 0) {
    echo "Invoices table exists\n";
    
    // Check table structure
    $result = $conn->query('DESCRIBE invoices');
    while ($row = $result->fetch_assoc()) {
        echo "Column: " . $row['Field'] . " Type: " . $row['Type'] . "\n";
    }
    
    // Check if there are any records
    $result = $conn->query('SELECT COUNT(*) as count FROM invoices');
    $count = $result->fetch_assoc()['count'];
    echo "Number of invoice records: $count\n";
} else {
    echo "Invoices table does not exist\n";
}

// Test customers table
$result = $conn->query('SHOW TABLES LIKE "customers"');
if ($result->num_rows > 0) {
    echo "\nCustomers table exists\n";
    $result = $conn->query('SELECT COUNT(*) as count FROM customers');
    $count = $result->fetch_assoc()['count'];
    echo "Number of customer records: $count\n";
} else {
    echo "\nCustomers table does not exist\n";
}

// Test the specific query from fetch_invoices_advanced.php
echo "\nTesting fetch_invoices_advanced query...\n";
try {
    $sql = "SELECT i.*, c.name as customer_name 
            FROM invoices i 
            LEFT JOIN customers c ON i.customer_id = c.id 
            ORDER BY i.invoice_date DESC, i.created_at DESC 
            LIMIT 5";
    
    $result = $conn->query($sql);
    if ($result) {
        echo "Query executed successfully\n";
        echo "Number of rows returned: " . $result->num_rows . "\n";
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "Invoice: " . $row['invoice_no'] . " Customer: " . $row['customer_name'] . "\n";
            }
        }
    } else {
        echo "Query failed: " . $conn->error . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
