<?php
// filepath: c:\xampp\htdocs\alok-crm\edit_destination.php
session_start();
if (!isset($_SESSION['user'])) {
    echo "Unauthorized access!";
    exit;
}

include 'inc/db.php'; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $type = isset($_POST['type']) ? trim($_POST['type']) : 'ZONE';

    if ($id > 0 && !empty($name)) {
        // Check if type column exists
        $check_type = "SHOW COLUMNS FROM destinations LIKE 'type'";
        $type_result = $conn->query($check_type);
        $has_type = ($type_result && $type_result->num_rows > 0);
        
        if ($has_type) {
            // Update both name and type
            $stmt = $conn->prepare("UPDATE destinations SET name = ?, type = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $type, $id);
        } else {
            // Only update name
            $stmt = $conn->prepare("UPDATE destinations SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
        }

        if ($stmt->execute()) {
            echo "Destination updated successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Invalid input data!";
    }
} else {
    echo "Invalid request method!";
}
?>