<?php
// filepath: c:\xampp\htdocs\alok-crm\fetch_destinations.php
session_start();
if (!isset($_SESSION['user'])) {
    exit('Unauthorized access!');
}

// Database connection
include 'inc/db.php'; 

// Check if connection exists
if (!$conn) {
    echo '<tr><td colspan="4" style="text-align:center;">Database connection failed.</td></tr>';
    exit;
}

// Get all destinations
// First check if status column exists
$check_column = "SHOW COLUMNS FROM destinations LIKE 'status'";
$column_result = $conn->query($check_column);
$has_status_column = ($column_result && $column_result->num_rows > 0);

if ($has_status_column) {
    $sql = "SELECT id, name, status FROM destinations ORDER BY name ASC";
} else {
    $sql = "SELECT id, name FROM destinations ORDER BY name ASC";
}

$result = $conn->query($sql);

// Check for SQL errors
if (!$result) {
    echo '<tr><td colspan="4" style="text-align:center;">Error: ' . $conn->error . '</td></tr>';
    $conn->close();
    exit;
}

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
    // Output for Zone Master interface
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Determine zone type based on name or set default
            $zone_type = 'ZONE'; // Default type
            if (in_array(strtoupper($row['name']), ['CENTRAL', 'EAST', 'NORTH', 'SOUTH', 'WEST'])) {
                $zone_type = strtoupper($row['name']);
            }
            
            // Determine status display
            if ($has_status_column && isset($row['status'])) {
                $status = intval($row['status']);
                $statusDisplay = $status ? '✓' : '✗';
                $statusClass = $status ? 'active-checkmark' : 'inactive-cross';
            } else {
                // Default to active if no status column
                $status = 1;
                $statusDisplay = '✓';
                $statusClass = 'active-checkmark';
            }
            
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['name']) . '</td>';
            echo '<td>' . $zone_type . '</td>';
            echo '<td class="active-indicator"><span class="status-toggle ' . $statusClass . '" data-id="' . htmlspecialchars($row['id']) . '" data-status="' . $status . '">' . $statusDisplay . '</span></td>';
            echo '<td>';
            echo '<button class="edit-zone-btn" data-id="' . htmlspecialchars($row['id']) . '" data-name="' . htmlspecialchars($row['name']) . '">Edit</button> ';
            echo '<button class="delete-zone-btn" data-id="' . htmlspecialchars($row['id']) . '">Delete</button>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="4" style="text-align:center;">No zones found.</td></tr>';
    }
}

$conn->close();
?>