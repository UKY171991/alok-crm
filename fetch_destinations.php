<?php
// filepath: c:\xampp\htdocs\alok-crm\fetch_destinations.php
session_start();
if (!isset($_SESSION['user'])) {
    echo "<tr><td colspan='3'>Unauthorized access!</td></tr>";
    exit;
}

// Database connection
include 'inc/db.php'; 

$sql = "SELECT id, name FROM destinations ORDER BY name ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>";
        echo "<button class='btn btn-warning btn-sm edit-btn' data-id='{$row['id']}' data-name='{$row['name']}'>Edit</button> ";
        echo "<button class='btn btn-danger btn-sm delete-btn' data-id='{$row['id']}'>Delete</button>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>No destinations found.</td></tr>";
}

$conn->close();
?>