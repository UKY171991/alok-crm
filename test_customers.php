<?php
require_once 'inc/db.php';

echo "Database connection status: " . ($conn ? "Connected" : "Failed") . "\n";

if ($conn) {
    $sql = "SELECT id, name FROM customers ORDER BY name ASC LIMIT 10";
    $result = $conn->query($sql);
    
    if ($result) {
        echo "Found " . $result->num_rows . " customers:\n";
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id'] . " - Name: " . $row['name'] . "\n";
        }
    } else {
        echo "Query error: " . $conn->error . "\n";
    }
} else {
    echo "Database connection failed\n";
}
?>
