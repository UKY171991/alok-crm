<?php
// filepath: c:\xampp\htdocs\alok-crm\delete_destination.php
session_start();
if (!isset($_SESSION['user'])) {
    echo "Unauthorized access!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'alok_crm');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM destinations WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Destination deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>