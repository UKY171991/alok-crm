<?php
// filepath: c:\xampp\htdocs\alok-crm\fetch_destinations.php
session_start();
if (!isset($_SESSION['user'])) {
    exit('Unauthorized access!');
}

// Database connection
include 'inc/db.php'; 

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 10;
$offset = ($page - 1) * $per_page;

// Get total count
$count_result = $conn->query("SELECT COUNT(*) as cnt FROM destinations");
$total = ($count_result && $row = $count_result->fetch_assoc()) ? intval($row['cnt']) : 0;

$sql = "SELECT id, name FROM destinations ORDER BY name ASC LIMIT $per_page OFFSET $offset";
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
    // Output pagination info for frontend
    echo '<tr style="display:none"><td colspan="3" id="pagination-info" data-total="' . $total . '" data-page="' . $page . '" data-per_page="' . $per_page . '"></td></tr>';
    $serial = $offset + 1;
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