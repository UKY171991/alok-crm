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
    
    // Check if this is a simple rate edit (from existing UI) or full edit
    if (isset($_POST['id']) && isset($_POST['rate']) && !isset($_POST['rate_id'])) {
        // Simple rate edit from existing UI
        $id = intval($_POST['id']);
        $rate = floatval($_POST['rate']);
        
        if ($id <= 0) {
            if ($return_json) {
                http_response_code(400);
                echo json_encode(array('error' => 'Invalid rate ID'));
            } else {
                echo 'Error: Invalid rate ID';
            }
            exit;
        }
        
        if ($rate <= 0) {
            if ($return_json) {
                http_response_code(400);
                echo json_encode(array('error' => 'Rate must be greater than 0'));
            } else {
                echo 'Error: Rate must be greater than 0';
            }
            exit;
        }
        
        // Check if rate exists
        $check_sql = "SELECT rate_id FROM customer_rates WHERE rate_id = :rate_id";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute(array(':rate_id' => $id));
        
        if ($check_stmt->rowCount() === 0) {
            if ($return_json) {
                http_response_code(404);
                echo json_encode(array('error' => 'Rate not found'));
            } else {
                echo 'Error: Rate not found';
            }
            exit;
        }
        
        // Update only the rate
        $sql = "UPDATE customer_rates SET rate_per_kg = :rate, updated_at = NOW() WHERE rate_id = :rate_id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute(array(':rate' => $rate, ':rate_id' => $id));
        
        if ($result) {
            if ($return_json) {
                echo json_encode(array('success' => true, 'message' => 'Rate updated successfully'));
            } else {
                echo 'Rate updated successfully';
            }
        } else {
            if ($return_json) {
                http_response_code(500);
                echo json_encode(array('error' => 'Failed to update rate'));
            } else {
                echo 'Error: Failed to update rate';
            }
        }
    } else {
        // Full edit (new UI format)
        $rate_id = isset($_POST['rate_id']) ? intval($_POST['rate_id']) : 0;
        $customer_id = isset($_POST['customer_id']) ? trim($_POST['customer_id']) : '';
        $zone_id = isset($_POST['zone_id']) ? trim($_POST['zone_id']) : '';
        $mode_id = isset($_POST['mode_id']) ? trim($_POST['mode_id']) : '';
        $consignment_type_id = isset($_POST['consignment_type_id']) ? trim($_POST['consignment_type_id']) : '';
        $weight_from = isset($_POST['weight_from']) ? floatval($_POST['weight_from']) : 0;
        $weight_to = isset($_POST['weight_to']) ? floatval($_POST['weight_to']) : 0;
        $rate_per_kg = isset($_POST['rate_per_kg']) ? floatval($_POST['rate_per_kg']) : 0;
        $minimum_rate = isset($_POST['minimum_rate']) ? floatval($_POST['minimum_rate']) : 0;
        
        // Validation
        $errors = array();
        
        if ($rate_id <= 0) {
            $errors[] = 'Invalid rate ID';
        }
        
        if (empty($customer_id)) {
            $errors[] = 'Customer is required';
        }
        
        if (empty($zone_id)) {
            $errors[] = 'Zone is required';
        }
        
        if (empty($mode_id)) {
            $errors[] = 'Mode is required';
        }
        
        if (empty($consignment_type_id)) {
            $errors[] = 'Consignment type is required';
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
        
        // Check for overlapping weight ranges (excluding current rate)
        $overlap_sql = "SELECT rate_id FROM customer_rates 
                        WHERE customer_id = :customer_id 
                        AND zone_id = :zone_id 
                        AND mode_id = :mode_id 
                        AND consignment_type_id = :consignment_type_id
                        AND rate_id != :rate_id
                        AND (
                            (:weight_from >= weight_from AND :weight_from < weight_to) OR
                            (:weight_to > weight_from AND :weight_to <= weight_to) OR
                            (:weight_from <= weight_from AND :weight_to >= weight_to)
                        )";
        
        $overlap_stmt = $pdo->prepare($overlap_sql);
        $overlap_stmt->execute(array(
            ':customer_id' => $customer_id,
            ':zone_id' => $zone_id,
            ':mode_id' => $mode_id,
            ':consignment_type_id' => $consignment_type_id,
            ':rate_id' => $rate_id,
            ':weight_from' => $weight_from,
            ':weight_to' => $weight_to
        ));
        
        if ($overlap_stmt->rowCount() > 0) {
            if ($return_json) {
                http_response_code(400);
                echo json_encode(array('error' => 'Weight range overlaps with existing rate'));
            } else {
                echo 'Error: Weight range overlaps with existing rate';
            }
            exit;
        }
        
        // Update rate
        $sql = "UPDATE customer_rates 
                SET customer_id = :customer_id, 
                    zone_id = :zone_id, 
                    mode_id = :mode_id, 
                    consignment_type_id = :consignment_type_id, 
                    weight_from = :weight_from, 
                    weight_to = :weight_to, 
                    rate_per_kg = :rate_per_kg, 
                    minimum_rate = :minimum_rate, 
                    updated_at = NOW()
                WHERE rate_id = :rate_id";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute(array(
            ':rate_id' => $rate_id,
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
                echo json_encode(array('success' => true, 'message' => 'Customer rate updated successfully'));
            } else {
                echo 'Customer rate updated successfully';
            }
        } else {
            if ($return_json) {
                http_response_code(500);
                echo json_encode(array('error' => 'Failed to update customer rate'));
            } else {
                echo 'Error: Failed to update customer rate';
            }
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
