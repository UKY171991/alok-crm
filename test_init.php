<?php
echo "Initializing database...\n";
require_once 'inc/config.php';
echo "Config loaded\n";

require_once 'inc/db.php';
echo "Database connection loaded\n";

// Test connection
if (isset($conn)) {
    echo "Connection exists\n";
    
    // Test tables
    $tables = ['customers', 'invoices', 'destinations'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "Table $table exists\n";
            
            $countResult = $conn->query("SELECT COUNT(*) as count FROM $table");
            if ($countResult) {
                $count = $countResult->fetch_assoc()['count'];
                echo "Table $table has $count records\n";
            }
        } else {
            echo "Table $table does NOT exist\n";
        }
    }
} else {
    echo "No database connection found\n";
}

echo "Done.\n";
?>
