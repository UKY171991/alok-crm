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
    
    // Get form data (supporting both old and new parameter names)
    $customer_id = isset($_POST['customer_id']) ? trim($_POST['customer_id']) : '';
    $zone_id = isset($_POST['zone_id']) ? trim($_POST['zone_id']) : (isset($_POST['zone_wise']) ? trim($_POST['zone_wise']) : '');
    $mode_id = isset($_POST['mode_id']) ? trim($_POST['mode_id']) : (isset($_POST['mode']) ? trim($_POST['mode']) : '');
    $consignment_type_id = isset($_POST['consignment_type_id']) ? trim($_POST['consignment_type_id']) : (isset($_POST['consignment_type']) ? trim($_POST['consignment_type']) : '');
    
    // Handle weight parameters (supporting both naming conventions)
    $weight_from = isset($_POST['weight_from']) ? floatval($_POST['weight_from']) : (isset($_POST['from_weight']) ? floatval($_POST['from_weight']) : 0);
    $weight_to = isset($_POST['weight_to']) ? floatval($_POST['weight_to']) : (isset($_POST['to_weight']) ? floatval($_POST['to_weight']) : 0);
    $rate_per_kg = isset($_POST['rate_per_kg']) ? floatval($_POST['rate_per_kg']) : (isset($_POST['rate']) ? floatval($_POST['rate']) : 0);
    $minimum_rate = isset($_POST['minimum_rate']) ? floatval($_POST['minimum_rate']) : 0;
    
    // Validation
    $errors = array();
    
    if (empty($customer_id)) {
        $errors[] = 'Customer is required';
    }
    
    // For zone_id, if it's not provided or empty, we'll try to find or create a default zone
    if (empty($zone_id)) {
        // Try to find a default zone or use the first available zone
        $zone_stmt = $pdo->prepare("SELECT id FROM destinations WHERE status = 'active' ORDER BY id LIMIT 1");
        $zone_stmt->execute();
        $default_zone = $zone_stmt->fetch(PDO::FETCH_ASSOC);
        if ($default_zone) {
            $zone_id = $default_zone['id'];
        } else {
            $errors[] = 'No active zones available';
        }
    }
    
    // Similar logic for mode_id
    if (empty($mode_id)) {
        $mode_stmt = $pdo->prepare("SELECT mode_id FROM cr_modes WHERE status = 'active' ORDER BY mode_id LIMIT 1");
        $mode_stmt->execute();
        $default_mode = $mode_stmt->fetch(PDO::FETCH_ASSOC);
        if ($default_mode) {
            $mode_id = $default_mode['mode_id'];
        } else {
            $errors[] = 'No active modes available';
        }
    }
    
    // Similar logic for consignment_type_id
    if (empty($consignment_type_id)) {
        $type_stmt = $pdo->prepare("SELECT consignment_type_id FROM cr_consignment_types WHERE status = 'active' ORDER BY consignment_type_id LIMIT 1");
        $type_stmt->execute();
        $default_type = $type_stmt->fetch(PDO::FETCH_ASSOC);
        if ($default_type) {
            $consignment_type_id = $default_type['consignment_type_id'];
        } else {
            $errors[] = 'No active consignment types available';
        }
    }
    
    if ($weight_from < 0) {
        $errors[] = 'Weight from must be non-negative';
    }
    
    if ($weight_to <= 0) {
        $errors[] = 'Weight to must be greater than 0';
    }
    
    if ($weight_from >= $weight_to) {
        $errors[] = 'Weight from must be less than weight to';
    }
    
    if ($rate_per_kg <= 0) {
        $errors[] = 'Rate per kg must be greater than 0';
    }
    
    if ($minimum_rate < 0) {
        $errors[] = 'Minimum rate must be non-negative';
    }
    
    if (!empty($errors)) {
        if ($return_json) {
            http_response_code(400);
            echo json_encode(array('error' => implode(', ', $errors)));
        } else {
            echo 'Error: ' . implode(', ', $errors);
        }
        exit;
    }
    
    // Check for overlapping weight ranges for the same customer, zone, mode, and consignment type
    $check_sql = "SELECT rate_id FROM customer_rates 
                  WHERE customer_id = :customer_id 
                  AND zone_id = :zone_id 
                  AND mode_id = :mode_id 
                  AND consignment_type_id = :consignment_type_id
                  AND (
                      (:weight_from >= weight_from AND :weight_from < weight_to) OR
                      (:weight_to > weight_from AND :weight_to <= weight_to) OR
                      (:weight_from <= weight_from AND :weight_to >= weight_to)
                  )";
    
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute(array(
        ':customer_id' => $customer_id,
        ':zone_id' => $zone_id,
        ':mode_id' => $mode_id,
        ':consignment_type_id' => $consignment_type_id,
        ':weight_from' => $weight_from,
        ':weight_to' => $weight_to
    ));
    
    if ($check_stmt->rowCount() > 0) {
        if ($return_json) {
            http_response_code(400);
            echo json_encode(array('error' => 'Weight range overlaps with existing rate'));
        } else {
            echo 'Error: Weight range overlaps with existing rate';
        }
        exit;
    }
    
    // Insert new rate
    $sql = "INSERT INTO customer_rates (customer_id, zone_id, mode_id, consignment_type_id, weight_from, weight_to, rate_per_kg, minimum_rate, status, created_at) 
            VALUES (:customer_id, :zone_id, :mode_id, :consignment_type_id, :weight_from, :weight_to, :rate_per_kg, :minimum_rate, 'active', NOW())";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(array(
        ':customer_id' => $customer_id,
        ':zone_id' => $zone_id,
        ':mode_id' => $mode_id,
        ':consignment_type_id' => $consignment_type_id,
        ':weight_from' => $weight_from,
        ':weight_to' => $weight_to,
        ':rate_per_kg' => $rate_per_kg,
        ':minimum_rate' => $minimum_rate
    ));
    
    if ($result) {
        if ($return_json) {
            echo json_encode(array('success' => true, 'message' => 'Customer rate added successfully'));
        } else {
            echo 'Customer rate added successfully';
        }
    } else {
        if ($return_json) {
            http_response_code(500);
            echo json_encode(array('error' => 'Failed to add customer rate'));
        } else {
            echo 'Error: Failed to add customer rate';
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
