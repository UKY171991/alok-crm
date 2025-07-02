<?php
session_start();

// Redirect to login.php if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

include 'inc/db.php';

// Default zones from the screenshot
$default_zones = ['CENTRAL', 'EAST', 'NORTH', 'SOUTH', 'WEST'];

echo "<h2>Setting up default zones...</h2>";

foreach ($default_zones as $zone) {
    // Check if zone already exists
    $check_sql = "SELECT id FROM destinations WHERE name = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $zone);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows == 0) {
        // Insert the zone if it doesn't exist
        $insert_sql = "INSERT INTO destinations (name) VALUES (?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("s", $zone);
        
        if ($insert_stmt->execute()) {
            echo "<p>✓ Added zone: $zone</p>";
        } else {
            echo "<p>✗ Failed to add zone: $zone</p>";
        }
        $insert_stmt->close();
    } else {
        echo "<p>- Zone already exists: $zone</p>";
    }
    
    $check_stmt->close();
}

echo "<p><strong>Setup complete!</strong></p>";
echo "<p><a href='destination.php'>Go to Zone Master</a></p>";

$conn->close();
?>
