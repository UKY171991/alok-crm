<?php
session_start();

// Redirect to login.php if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

echo "<h2>Database Connection Test</h2>";

// Test database connection
include 'inc/db.php';

if ($conn) {
    echo "<p>✓ Database connection successful</p>";
    
    // Test if destinations table exists
    $check_table = "SHOW TABLES LIKE 'destinations'";
    $result = $conn->query($check_table);
    
    if ($result && $result->num_rows > 0) {
        echo "<p>✓ Destinations table exists</p>";
        
        // Check table structure
        echo "<h3>Table Structure:</h3>";
        $columns = $conn->query("SHOW COLUMNS FROM destinations");
        echo "<ul>";
        while ($col = $columns->fetch_assoc()) {
            echo "<li>" . $col['Field'] . " (" . $col['Type'] . ")</li>";
        }
        echo "</ul>";
        
        // Check if there's data
        $count = $conn->query("SELECT COUNT(*) as total FROM destinations");
        $total = $count->fetch_assoc()['total'];
        echo "<p>Total records: " . $total . "</p>";
        
        if ($total > 0) {
            echo "<h3>Sample Data:</h3>";
            $sample = $conn->query("SELECT * FROM destinations LIMIT 5");
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Name</th><th>Status</th><th>Created</th></tr>";
            while ($row = $sample->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . (isset($row['status']) ? $row['status'] : 'N/A') . "</td>";
                echo "<td>" . $row['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p><a href='setup_default_zones.php'>Setup Default Zones</a></p>";
        }
    } else {
        echo "<p>✗ Destinations table does not exist</p>";
        echo "<p><a href='setup_default_zones.php'>Setup Default Zones</a></p>";
    }
    
    $conn->close();
} else {
    echo "<p>✗ Database connection failed</p>";
}

echo "<p><a href='destination.php'>Back to Zone Master</a></p>";
?>
