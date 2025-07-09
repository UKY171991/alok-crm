<?php
echo "Creating database...\n";

// Database credentials
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'alok_crm';

try {
    // Connect to MySQL without selecting a database
    $conn = new mysqli($db_host, $db_user, $db_pass);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "Connected to MySQL\n";
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
    if ($conn->query($sql) === TRUE) {
        echo "Database '$db_name' created or already exists\n";
    } else {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select the database
    $conn->select_db($db_name);
    echo "Selected database '$db_name'\n";
    
    // Now test if we can create a simple table
    $testSql = "CREATE TABLE IF NOT EXISTS test_table (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50))";
    if ($conn->query($testSql) === TRUE) {
        echo "Test table created successfully\n";
        
        // Drop the test table
        $conn->query("DROP TABLE test_table");
        echo "Test table dropped\n";
    } else {
        throw new Exception("Error creating test table: " . $conn->error);
    }
    
    $conn->close();
    echo "Database setup complete!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
