<?php
echo "<h2>Database Connection Diagnostics</h2>";

echo "<h3>1. Environment Detection</h3>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "<br>";
echo "SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'Not set') . "<br>";
echo "SERVER_ADDR: " . ($_SERVER['SERVER_ADDR'] ?? 'Not set') . "<br>";

echo "<h3>2. Database Configuration</h3>";
require_once 'inc/db.php';

$is_localhost = true;
if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
    $server_name = $_SERVER['SERVER_NAME'] ?? '';
    $server_addr = $_SERVER['SERVER_ADDR'] ?? '';
    
    $is_localhost = ($host == 'localhost' || 
                    $host == '127.0.0.1' || 
                    $server_name == 'localhost' || 
                    $server_addr == '127.0.0.1' ||
                    strpos($host, 'localhost') !== false);
}

echo "Environment detected as: " . ($is_localhost ? 'Localhost' : 'Production') . "<br>";

if ($is_localhost) {
    echo "Database Host: localhost<br>";
    echo "Database User: root<br>";
    echo "Database Name: alok_crm<br>";
} else {
    echo "Database Host: localhost<br>";
    echo "Database User: fnkjyinw_alok_crm<br>";
    echo "Database Name: fnkjyinw_alok_crm<br>";
}

echo "<h3>3. Connection Test</h3>";
try {
    if (isset($conn) && $conn->ping()) {
        echo "<span style='color: green;'>✓ Database connection successful!</span><br>";
        
        // Test tables
        $tables = ['customers', 'invoices'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "<span style='color: green;'>✓ Table '$table' exists</span><br>";
                
                // Count records
                $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
                if ($count_result) {
                    $count = $count_result->fetch_assoc()['count'];
                    echo "&nbsp;&nbsp; - Records in $table: $count<br>";
                }
            } else {
                echo "<span style='color: red;'>✗ Table '$table' does not exist</span><br>";
            }
        }
    } else {
        echo "<span style='color: red;'>✗ Database connection failed</span><br>";
    }
} catch (Exception $e) {
    echo "<span style='color: red;'>✗ Database connection error: " . $e->getMessage() . "</span><br>";
}

echo "<h3>4. MySQL Service Check</h3>";
echo "To check if MySQL is running on Windows:<br>";
echo "<code>services.msc</code> - Look for 'MySQL' service<br>";
echo "Or run: <code>sc query mysql</code> in Command Prompt<br>";

echo "<h3>5. Common Solutions</h3>";
echo "<ul>";
echo "<li>Start MySQL service: <code>net start mysql</code></li>";
echo "<li>Start XAMPP/WAMP if using those</li>";
echo "<li>Check if MySQL is installed</li>";
echo "<li>Verify database credentials</li>";
echo "</ul>";
?>
