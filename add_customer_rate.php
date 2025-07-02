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
    $zone_wise = isset($_POST['zone_id']) ? trim($_POST['zone_id']) : (isset($_POST['zone_wise']) ? trim($_POST['zone_wise']) : '');
    $mode = isset($_POST['mode_id']) ? trim($_POST['mode_id']) : (isset($_POST['mode']) ? trim($_POST['mode']) : '');
    $consignment_type = isset($_POST['consignment_type_id']) ? trim($_POST['consignment_type_id']) : (isset($_POST['consignment_type']) ? trim($_POST['consignment_type']) : '');
    
    // Handle weight parameters (supporting both naming conventions)
    $from_weight = isset($_POST['weight_from']) ? floatval($_POST['weight_from']) : (isset($_POST['from_weight']) ? floatval($_POST['from_weight']) : 0);
    $to_weight = isset($_POST['weight_to']) ? floatval($_POST['weight_to']) : (isset($_POST['to_weight']) ? floatval($_POST['to_weight']) : 0);
    $rate = isset($_POST['rate_per_kg']) ? floatval($_POST['rate_per_kg']) : (isset($_POST['rate']) ? floatval($_POST['rate']) : 0);
    $additional_rate = isset($_POST['minimum_rate']) ? floatval($_POST['minimum_rate']) : 0;
    
    // Validation
    $errors = array();
    
    if (empty($customer_id)) {
        $errors[] = 'Customer is required';
    }
    
    // For zone_wise, if it's not provided, use a default
    if (empty($zone_wise)) {
        $zone_wise = 'General';
    }
    
    // For mode, if it's not provided, use a default
    if (empty($mode)) {
        $mode = 'Standard';
    }
    
    // For consignment_type, if it's not provided, use a default
    if (empty($consignment_type)) {
        $consignment_type = 'General';
    }
    
    if ($from_weight < 0) {
        $errors[] = 'Weight from must be non-negative';
    }
    
    if ($to_weight <= 0) {
        $errors[] = 'Weight to must be greater than 0';
    }
    
    if ($from_weight >= $to_weight) {
        $errors[] = 'Weight from must be less than weight to';
    }
    
    if ($rate <= 0) {
        $errors[] = 'Rate must be greater than 0';
    }
    
    if ($additional_rate < 0) {
        $errors[] = 'Additional rate must be non-negative';
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
    $check_sql = "SELECT id FROM customer_rates 
                  WHERE customer_id = :customer_id 
                  AND zone_wise = :zone_wise 
                  AND mode = :mode 
                  AND consignment_type = :consignment_type
                  AND (
                      (:from_weight >= from_weight AND :from_weight < to_weight) OR
                      (:to_weight > from_weight AND :to_weight <= to_weight) OR
                      (:from_weight <= from_weight AND :to_weight >= to_weight)
                  )";
    
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute(array(
        ':customer_id' => $customer_id,
        ':zone_wise' => $zone_wise,
        ':mode' => $mode,
        ':consignment_type' => $consignment_type,
        ':from_weight' => $from_weight,
        ':to_weight' => $to_weight
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
    $sql = "INSERT INTO customer_rates (customer_id, zone_wise, state_wise, city_wise, mode, consignment_type, from_weight, to_weight, rate, additional_rate, created_at) 
            VALUES (:customer_id, :zone_wise, 'General', 'General', :mode, :consignment_type, :from_weight, :to_weight, :rate, :additional_rate, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(array(
        ':customer_id' => $customer_id,
        ':zone_wise' => $zone_wise,
        ':mode' => $mode,
        ':consignment_type' => $consignment_type,
        ':from_weight' => $from_weight,
        ':to_weight' => $to_weight,
        ':rate' => $rate,
        ':additional_rate' => $additional_rate
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
