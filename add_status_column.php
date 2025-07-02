<?php
session_start();

// Redirect to login.php if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

include 'inc/db.php';

echo "<h2>Adding status column to destinations table...</h2>";

// Check if status column already exists
$check_column = "SHOW COLUMNS FROM destinations LIKE 'status'";
$result = $conn->query($check_column);

if ($result->num_rows == 0) {
    // Add status column
    $alter_sql = "ALTER TABLE destinations ADD COLUMN status TINYINT(1) NOT NULL DEFAULT 1";
    
    if ($conn->query($alter_sql) === TRUE) {
        echo "<p>✓ Status column added successfully!</p>";
        
        // Update all existing records to have status = 1 (active)
        $update_sql = "UPDATE destinations SET status = 1";
        if ($conn->query($update_sql) === TRUE) {
            echo "<p>✓ All existing zones set to active status!</p>";
        } else {
            echo "<p>✗ Error updating existing zones: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>✗ Error adding status column: " . $conn->error . "</p>";
    }
} else {
    echo "<p>- Status column already exists!</p>";
}

echo "<p><strong>Migration complete!</strong></p>";
echo "<p><a href='destination.php'>Go to Zone Master</a></p>";

$conn->close();
?>
