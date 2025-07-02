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
    $type = isset($_POST['type']) ? $_POST['type'] : 'ZONE';

    // Check if status and type columns exist
    $check_status = "SHOW COLUMNS FROM destinations LIKE 'status'";
    $status_result = $conn->query($check_status);
    $has_status = ($status_result && $status_result->num_rows > 0);
    
    $check_type = "SHOW COLUMNS FROM destinations LIKE 'type'";
    $type_result = $conn->query($check_type);
    $has_type = ($type_result && $type_result->num_rows > 0);
    
    if ($has_status && $has_type) {
        // Both status and type columns exist
        $stmt = $conn->prepare("INSERT INTO destinations (name, type, status) VALUES (?, ?, 1)");
        $stmt->bind_param("ss", $name, $type);
    } else if ($has_status) {
        // Only status column exists
        $stmt = $conn->prepare("INSERT INTO destinations (name, status) VALUES (?, 1)");
        $stmt->bind_param("s", $name);
    } else {
        // Neither column exists, use old format
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