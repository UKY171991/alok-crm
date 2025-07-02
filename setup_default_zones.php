<?php
session_start();

// Redirect to login.php if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

include 'inc/db.php';

echo "<h2>Setting up default zones...</h2>";

// First check if destinations table exists
$check_table = "SHOW TABLES LIKE 'destinations'";
$table_result = $conn->query($check_table);

if ($table_result->num_rows == 0) {
    echo "<p>Creating destinations table...</p>";
    $create_table = "CREATE TABLE `destinations` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `code` varchar(20) DEFAULT NULL,
        `status` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    if ($conn->query($create_table) === TRUE) {
        echo "<p>✓ Destinations table created successfully!</p>";
    } else {
        echo "<p>✗ Error creating table: " . $conn->error . "</p>";
        exit;
    }
}

// Check if status column exists, if not add it
$check_column = "SHOW COLUMNS FROM destinations LIKE 'status'";
$column_result = $conn->query($check_column);

if ($column_result->num_rows == 0) {
    echo "<p>Adding status column...</p>";
    $alter_sql = "ALTER TABLE destinations ADD COLUMN status TINYINT(1) NOT NULL DEFAULT 1";
    
    if ($conn->query($alter_sql) === TRUE) {
        echo "<p>✓ Status column added successfully!</p>";
    } else {
        echo "<p>✗ Error adding status column: " . $conn->error . "</p>";
    }
}

// Default zones from the screenshot
$default_zones = ['CENTRAL', 'EAST', 'NORTH', 'SOUTH', 'WEST'];

foreach ($default_zones as $zone) {
    // Check if zone already exists
    $check_sql = "SELECT id FROM destinations WHERE name = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $zone);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows == 0) {
        // Insert the zone if it doesn't exist
        $insert_sql = "INSERT INTO destinations (name, status) VALUES (?, 1)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("s", $zone);
        
        if ($insert_stmt->execute()) {
            echo "<p>✓ Added zone: $zone</p>";
        } else {
            echo "<p>✗ Failed to add zone: $zone - " . $insert_stmt->error . "</p>";
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
