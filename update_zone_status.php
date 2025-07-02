<?php
session_start();

// Redirect to login.php if not logged in
if (!isset($_SESSION['user'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access!']);
    exit;
}

include 'inc/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $current_status = intval($_POST['status']);
    
    // Check if status column exists
    $check_column = "SHOW COLUMNS FROM destinations LIKE 'status'";
    $column_result = $conn->query($check_column);
    
    if ($column_result && $column_result->num_rows > 0) {
        // Toggle status (if current is 1, make it 0; if current is 0, make it 1)
        $new_status = $current_status ? 0 : 1;
        
        $sql = "UPDATE destinations SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $new_status, $id);
        
        if ($stmt->execute()) {
            $status_text = $new_status ? 'activated' : 'deactivated';
            echo "Zone $status_text successfully!";
        } else {
            echo "Error updating zone status!";
        }
        
        $stmt->close();
    } else {
        // Add status column first
        $alter_sql = "ALTER TABLE destinations ADD COLUMN status TINYINT(1) NOT NULL DEFAULT 1";
        if ($conn->query($alter_sql) === TRUE) {
            // Now update the status
            $new_status = $current_status ? 0 : 1;
            $sql = "UPDATE destinations SET status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $new_status, $id);
            
            if ($stmt->execute()) {
                $status_text = $new_status ? 'activated' : 'deactivated';
                echo "Zone $status_text successfully!";
            } else {
                echo "Error updating zone status!";
            }
            $stmt->close();
        } else {
            echo "Error setting up status column!";
        }
    }
} else {
    echo "Invalid request method!";
}

$conn->close();
?>
