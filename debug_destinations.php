<?php
session_start();

// Redirect to login.php if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

include 'inc/db.php';

echo "<h2>Database Debug Information</h2>";

// Check if destinations table exists
$tables = $conn->query("SHOW TABLES LIKE 'destinations'");
if ($tables->num_rows > 0) {
    echo "<p>✓ Destinations table exists</p>";
    
    // Check columns
    echo "<h3>Table Structure:</h3>";
    $columns = $conn->query("SHOW COLUMNS FROM destinations");
    while ($col = $columns->fetch_assoc()) {
        echo "<p>- " . $col['Field'] . " (" . $col['Type'] . ")</p>";
    }
    
    // Check data
    echo "<h3>Current Data:</h3>";
    $data = $conn->query("SELECT * FROM destinations");
    if ($data->num_rows > 0) {
        echo "<table border='1'><tr>";
        $data2 = $conn->query("SELECT * FROM destinations LIMIT 1");
        $row = $data2->fetch_assoc();
        foreach (array_keys($row) as $col) {
            echo "<th>$col</th>";
        }
        echo "</tr>";
        
        $data->data_seek(0);
        while ($row = $data->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No data in destinations table</p>";
    }
} else {
    echo "<p>✗ Destinations table does not exist</p>";
}

$conn->close();
?>
<p><a href="destination.php">Back to Zone Master</a></p>
