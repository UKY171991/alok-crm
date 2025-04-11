<?php
// filepath: c:\xampp\htdocs\alok-crm\fetch_destinations.php
session_start();
if (!isset($_SESSION['user'])) {
    echo "Unauthorized access!";
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'alok_crm');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM destinations";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>
                    <button class='btn btn-sm btn-warning edit-btn' data-id='{$row['id']}' data-name='{$row['name']}'>Edit</button>
                    <button class='btn btn-sm btn-danger delete-btn' data-id='{$row['id']}'>Delete</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='3' class='text-center'>No destinations found</td></tr>";
}

$conn->close();
?>