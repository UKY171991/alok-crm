<?php
// filepath: c:\xampp\htdocs\alok-crm\add_destination.php
session_start();
if (!isset($_SESSION['user'])) {
    echo "Unauthorized access!";
    exit;
}

include 'inc/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];

    // Check if status column exists
    $check_column = "SHOW COLUMNS FROM destinations LIKE 'status'";
    $column_result = $conn->query($check_column);
    
    if ($column_result && $column_result->num_rows > 0) {
        // Status column exists, include it
        $stmt = $conn->prepare("INSERT INTO destinations (name, status) VALUES (?, 1)");
        $stmt->bind_param("s", $name);
    } else {
        // Status column doesn't exist, use old format
        $stmt = $conn->prepare("INSERT INTO destinations (name) VALUES (?)");
        $stmt->bind_param("s", $name);
    }

    if ($stmt->execute()) {
        echo "Destination added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>