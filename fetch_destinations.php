<?php
// filepath: c:\xampp\htdocs\alok-crm\fetch_destinations.php
session_start();
if (!isset($_SESSION['user'])) {
    echo json_encode(["error" => "Unauthorized access!"]);
    exit;
}

// Database connection
include 'inc/db.php'; 

$sql = "SELECT id, name FROM destinations ORDER BY name ASC";
$result = $conn->query($sql);

$destinations = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $destinations[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($destinations);
$conn->close();
?>