<?php
require_once 'inc/config.php';
require_once 'inc/db.php';

// Check if we should return JSON or plain text
$return_json = isset($_POST['json']) && $_POST['json'] === 'true';

if ($return_json) {
    header('Content-Type: application/json');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($return_json) {
        http_response_code(405);
        echo json_encode(array('error' => 'Method not allowed'));
    } else {
        echo 'Method not allowed';
    }
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get rate ID (supporting both 'id' and 'rate_id' parameters)
    $rate_id = isset($_POST['rate_id']) ? intval($_POST['rate_id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
    
    if ($rate_id <= 0) {
        if ($return_json) {
            http_response_code(400);
            echo json_encode(array('error' => 'Invalid rate ID'));
        } else {
            echo 'Error: Invalid rate ID';
        }
        exit;
    }
    
    // Check if rate exists
    $check_sql = "SELECT rate_id FROM customer_rates WHERE rate_id = :rate_id";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute(array(':rate_id' => $rate_id));
    
    if ($check_stmt->rowCount() === 0) {
        if ($return_json) {
            http_response_code(404);
            echo json_encode(array('error' => 'Rate not found'));
        } else {
            echo 'Error: Rate not found';
        }
        exit;
    }
    
    // Delete the rate
    $sql = "DELETE FROM customer_rates WHERE rate_id = :rate_id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(array(':rate_id' => $rate_id));
    
    if ($result) {
        if ($return_json) {
            echo json_encode(array('success' => true, 'message' => 'Customer rate deleted successfully'));
        } else {
            echo 'Customer rate deleted successfully';
        }
    } else {
        if ($return_json) {
            http_response_code(500);
            echo json_encode(array('error' => 'Failed to delete customer rate'));
        } else {
            echo 'Error: Failed to delete customer rate';
        }
    }
    
} catch(PDOException $e) {
    if ($return_json) {
        http_response_code(500);
        echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
    } else {
        echo 'Database error: ' . $e->getMessage();
    }
}
?>
