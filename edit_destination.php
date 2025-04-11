<?php
// filepath: c:\xampp\htdocs\alok-crm\edit_destination.php
session_start();
if (!isset($_SESSION['user'])) {
    echo "Unauthorized access!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';

    if ($id > 0 && !empty($name)) {
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'alok_crm');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("UPDATE destinations SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);

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