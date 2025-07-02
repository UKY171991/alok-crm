<?php
// Test script to check if all required tables exist for Customer Rate Master
require_once 'inc/config.php';
require_once 'inc/db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Table Check for Customer Rate Master</h2>\n";
    
    // Check if tables exist
    $tables_to_check = [
        'customers' => 'Customer table (existing)',
        'destinations' => 'Destinations/Zones table (existing)',
        'cr_modes' => 'Customer Rate Modes table (new)',
        'cr_consignment_types' => 'Customer Rate Consignment Types table (new)',
        'customer_rates' => 'Customer Rates table (new)'
    ];
    
    foreach ($tables_to_check as $table => $description) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        
        if ($stmt->rowCount() > 0) {
            echo "✅ $description ($table) - EXISTS\n";
            
            // Count records
            $count_stmt = $pdo->prepare("SELECT COUNT(*) as count FROM `$table`");
            $count_stmt->execute();
            $count = $count_stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "   - Records: $count\n";
        } else {
            echo "❌ $description ($table) - MISSING\n";
        }
        echo "\n";
    }
    
    // Test data fetch for dropdowns
    echo "<h3>Testing Data Fetch</h3>\n";
    
    // Test customers
    try {
        $stmt = $pdo->prepare("SELECT id as customer_id, name as customer_name FROM customers LIMIT 5");
        $stmt->execute();
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "✅ Customers fetch: " . count($customers) . " customers found\n";
        foreach ($customers as $customer) {
            echo "   - " . $customer['customer_name'] . " (ID: " . $customer['customer_id'] . ")\n";
        }
    } catch (Exception $e) {
        echo "❌ Customers fetch error: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test destinations
    try {
        $stmt = $pdo->prepare("SELECT id, zone_name FROM destinations LIMIT 5");
        $stmt->execute();
        $zones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "✅ Zones fetch: " . count($zones) . " zones found\n";
        foreach ($zones as $zone) {
            echo "   - " . $zone['zone_name'] . " (ID: " . $zone['id'] . ")\n";
        }
    } catch (Exception $e) {
        echo "❌ Zones fetch error: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test modes
    try {
        $stmt = $pdo->prepare("SELECT mode_id, mode_name FROM cr_modes WHERE status = 'active' LIMIT 5");
        $stmt->execute();
        $modes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "✅ Modes fetch: " . count($modes) . " active modes found\n";
        foreach ($modes as $mode) {
            echo "   - " . $mode['mode_name'] . "\n";
        }
    } catch (Exception $e) {
        echo "❌ Modes fetch error: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test consignment types
    try {
        $stmt = $pdo->prepare("SELECT consignment_type_id, type_name FROM cr_consignment_types WHERE status = 'active' LIMIT 5");
        $stmt->execute();
        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "✅ Consignment Types fetch: " . count($types) . " active types found\n";
        foreach ($types as $type) {
            echo "   - " . $type['type_name'] . "\n";
        }
    } catch (Exception $e) {
        echo "❌ Consignment Types fetch error: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test customer rates
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM customer_rates");
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "✅ Customer Rates: $count records found\n";
    } catch (Exception $e) {
        echo "❌ Customer Rates fetch error: " . $e->getMessage() . "\n";
    }
    
    echo "\n<h3>Test Complete</h3>\n";
    echo "If any tables are missing, run the SQL script: setup_customer_rate_master.sql\n";
    
} catch (Exception $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "\n";
}
?>
