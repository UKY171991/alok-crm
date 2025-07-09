<?php
/**
 * Test Database Setup Script
 * This script tests the database initialization functionality
 */

require_once 'inc/config.php';
require_once 'inc/db.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Setup Test</title>
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
    </style>
</head>
<body>
<div class='container'>
    <h1>Database Setup Verification</h1>";

try {
    // Test database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='success'>✓ Database connection successful</div>";
    
    // Test initialization function
    echo "<div class='section'>";
    echo "<h2>Running Database Initialization</h2>";
    
    $result = initialize_database();
    if ($result) {
        echo "<div class='success'>✓ Database initialization completed successfully</div>";
    } else {
        echo "<div class='error'>✗ Database initialization failed</div>";
    }
    echo "</div>";
    
    // Check all required tables
    echo "<div class='section'>";
    echo "<h2>Table Verification</h2>";
    
    $required_tables = [
        'customers' => 'Customer master data',
        'destinations' => 'Destination/zones master',
        'users' => 'System users',
        'invoices' => 'Invoice headers',
        'invoice_items' => 'Invoice line items',
        'orders' => 'Order/shipment data',
        'rates' => 'General rates master',
        'cr_modes' => 'Customer rate modes',
        'cr_consignment_types' => 'Customer rate consignment types',
        'customer_rates' => 'Customer-specific rates'
    ];
    
    echo "<table>";
    echo "<tr><th>Table Name</th><th>Description</th><th>Status</th><th>Record Count</th></tr>";
    
    foreach ($required_tables as $table => $description) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `$table`");
            $stmt->execute();
            $count = $stmt->fetchColumn();
            echo "<tr>";
            echo "<td><strong>$table</strong></td>";
            echo "<td>$description</td>";
            echo "<td><span style='color: green;'>✓ EXISTS</span></td>";
            echo "<td>$count records</td>";
            echo "</tr>";
        } catch (PDOException $e) {
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
    
    // Check table structures
    echo "<div class='section'>";
    echo "<h2>Table Structure Verification</h2>";
    
    $critical_columns = [
        'customers' => ['id', 'name', 'email', 'phone'],
        'destinations' => ['id', 'name', 'zone_name', 'type', 'status'],
        'customer_rates' => ['id', 'customer_id', 'mode', 'consignment_type', 'zone_wise', 'from_weight', 'to_weight', 'rate'],
        'cr_modes' => ['mode_id', 'mode_name', 'status'],
        'cr_consignment_types' => ['consignment_type_id', 'type_name', 'status']
    ];
    
    foreach ($critical_columns as $table => $columns) {
        echo "<h3>$table</h3>";
        echo "<table>";
        echo "<tr><th>Column</th><th>Status</th></tr>";
        
        try {
            foreach ($columns as $column) {
                $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE '$column'");
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    echo "<tr><td>$column</td><td><span style='color: green;'>✓ EXISTS</span></td></tr>";
                } else {
                    echo "<tr><td>$column</td><td><span style='color: red;'>✗ MISSING</span></td></tr>";
                }
            }
        } catch (PDOException $e) {
            echo "<tr><td colspan='2'><span style='color: red;'>Table does not exist</span></td></tr>";
        }
        echo "</table>";
    }
    echo "</div>";
    
    // Check foreign key constraints
    echo "<div class='section'>";
    echo "<h2>Foreign Key Constraints</h2>";
    
    $fk_constraints = [
        'customer_rates' => 'fk_customer_rates_customer',
        'invoices' => 'fk_invoices_customer',
        'invoice_items' => 'fk_invoice_items_invoice',
        'orders' => 'fk_orders_customer'
    ];
    
    echo "<table>";
    echo "<tr><th>Table</th><th>Constraint Name</th><th>Status</th></tr>";
    
    foreach ($fk_constraints as $table => $constraint) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                              WHERE TABLE_SCHEMA = DATABASE() 
                              AND TABLE_NAME = '$table' 
                              AND CONSTRAINT_NAME = '$constraint'");
        $stmt->execute();
        $exists = $stmt->fetchColumn() > 0;
        
        echo "<tr>";
        echo "<td>$table</td>";
        echo "<td>$constraint</td>";
        echo "<td>" . ($exists ? "<span style='color: green;'>✓ EXISTS</span>" : "<span style='color: orange;'>○ MISSING</span>") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // Sample data verification
    echo "<div class='section'>";
    echo "<h2>Sample Data Verification</h2>";
    
    $sample_data_tables = ['customers', 'destinations', 'users', 'cr_modes', 'cr_consignment_types'];
    
    echo "<table>";
    echo "<tr><th>Table</th><th>Sample Records</th></tr>";
    
    foreach ($sample_data_tables as $table) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM `$table` LIMIT 3");
            $stmt->execute();
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<tr>";
            echo "<td><strong>$table</strong></td>";
            echo "<td>";
            if (count($records) > 0) {
                foreach ($records as $record) {
                    $display_field = isset($record['name']) ? $record['name'] : 
                                   (isset($record['mode_name']) ? $record['mode_name'] : 
                                   (isset($record['type_name']) ? $record['type_name'] : 
                                   (isset($record['username']) ? $record['username'] : 
                                   'Record ' . $record['id'])));
                    echo "• " . htmlspecialchars($display_field) . "<br>";
                }
            } else {
                echo "<span style='color: orange;'>No sample data</span>";
            }
            echo "</td>";
            echo "</tr>";
        } catch (PDOException $e) {
            echo "<tr><td><strong>$table</strong></td><td><span style='color: red;'>Error accessing table</span></td></tr>";
        }
    }
    echo "</table>";
    echo "</div>";
    
    echo "<div class='info'>Database setup verification completed. If any issues are found above, please check the error logs or contact the administrator.</div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>✗ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</div>
</body>
</html>";
?>
