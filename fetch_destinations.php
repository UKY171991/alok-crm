<?php
// filepath: c:\xampp\htdocs\alok-crm\fetch_destinations.php
session_start();
if (!isset($_SESSION['user'])) {
    exit('Unauthorized access!');
}

// Database connection
include 'inc/db.php'; 

$sql = "SELECT id, name FROM destinations ORDER BY name ASC";
$result = $conn->query($sql);

if (isset($_GET['mode']) && $_GET['mode'] === 'json') {
    // Return JSON for dropdowns
    $destinations = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $destinations[] = [
                'id' => $row['id'],
                'name' => $row['name']
            ];
        }
    }
    header('Content-Type: application/json');
    echo json_encode($destinations);
} else {
    // Return HTML for admin table
    $serial = 1;
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $serial++ . '</td>';
            echo '<td>' . htmlspecialchars($row['name']) . '</td>';
            echo '<td>';
            echo '<button class="btn btn-sm btn-primary edit-btn" data-id="' . htmlspecialchars($row['id']) . '" data-name="' . htmlspecialchars($row['name']) . '">Edit</button> ';
            echo '<button class="btn btn-sm btn-danger delete-btn" data-id="' . htmlspecialchars($row['id']) . '">Delete</button>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="3" class="text-center">No destinations found.</td></tr>';
    }
}

$conn->close();
?>