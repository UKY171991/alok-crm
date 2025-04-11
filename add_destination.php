<?php
// filepath: c:\xampp\htdocs\alok-crm\add_destination.php
session_start();
if (!isset($_SESSION['user'])) {
    echo "Unauthorized access!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'alok_crm');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO destinations (name) VALUES (?)");
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        echo "Destination added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>