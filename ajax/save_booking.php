<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

function saveBooking() {
    global $conn;
    
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Invalid request method');
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        // Extract booking data
        $consignment_no = $input['consignment_no'] ?? '';
        $customer_id = intval($input['customer_id'] ?? 0);
        $doc_type = $input['doc_type'] ?? 'DOX';
        $service_type = $input['service_type'] ?? 'AIR';
        $pincode = $input['pincode'] ?? '';
        $city_description = $input['city_description'] ?? '';
        $weight = floatval($input['weight'] ?? 0);
        $courier_amt = floatval($input['courier_amt'] ?? 0);
        $vas_amount = floatval($input['vas_amount'] ?? 0);
        $chargeable_amt = floatval($input['chargeable_amt'] ?? 0);
        
        // Validation
        if (empty($consignment_no)) {
            throw new Exception("Consignment number is required");
        }
        
        if ($customer_id <= 0) {
            throw new Exception("Please select a customer");
        }
        
        if ($weight <= 0) {
            throw new Exception("Weight must be greater than 0");
        }
        
        if ($courier_amt <= 0) {
            throw new Exception("Courier amount must be greater than 0");
        }
        
        // Check database connection
        if (!$conn || $conn->connect_error) {
            return handleFallbackSave($input);
        }
        
        // Create bookings table if not exists
        $createTableSql = "CREATE TABLE IF NOT EXISTS `bookings` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `consignment_no` varchar(50) NOT NULL,
            `customer_id` int(11) NOT NULL,
            `doc_type` varchar(10) NOT NULL DEFAULT 'DOX',
            `service_type` varchar(20) NOT NULL DEFAULT 'AIR',
            `pincode` varchar(10) DEFAULT NULL,
            `city_description` varchar(100) DEFAULT NULL,
            `weight` decimal(10,3) NOT NULL DEFAULT 0.000,
            `courier_amt` decimal(10,2) NOT NULL DEFAULT 0.00,
            `vas_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
            `chargeable_amt` decimal(10,2) NOT NULL DEFAULT 0.00,
            `billing_status` enum('billed','non-billed','pending') NOT NULL DEFAULT 'non-billed',
            `booking_date` date NOT NULL DEFAULT CURRENT_DATE,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `consignment_no` (`consignment_no`),
            KEY `customer_id` (`customer_id`),
            KEY `booking_date` (`booking_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        
        $conn->query($createTableSql);
        
        // Insert booking
        $insertSql = "INSERT INTO bookings (
            consignment_no, customer_id, doc_type, service_type, 
            pincode, city_description, weight, courier_amt, 
            vas_amount, chargeable_amt, booking_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE())";
        
        $stmt = $conn->prepare($insertSql);
        if (!$stmt) {
            throw new Exception('Database prepare error: ' . $conn->error);
        }
        
        $stmt->bind_param('sissssdddd', 
            $consignment_no, $customer_id, $doc_type, $service_type,
            $pincode, $city_description, $weight, $courier_amt,
            $vas_amount, $chargeable_amt
        );
        
        if ($stmt->execute()) {
            $booking_id = $conn->insert_id;
            
            return [
                'success' => true,
                'message' => 'Booking saved successfully',
                'booking_id' => $booking_id,
                'consignment_no' => $consignment_no
            ];
        } else {
            throw new Exception('Failed to save booking: ' . $stmt->error);
        }
        
    } catch (Exception $e) {
        error_log("Save Booking Error: " . $e->getMessage());
        
        // Try fallback if database error
        if (strpos($e->getMessage(), 'database') !== false || 
            strpos($e->getMessage(), 'connection') !== false ||
            !$conn || $conn->connect_error) {
            return handleFallbackSave($input);
        }
        
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'error_code' => 'SAVE_ERROR'
        ];
    }
}

function handleFallbackSave($input) {
    try {
        // In demo/fallback mode, simulate successful save
        $consignment_no = $input['consignment_no'] ?? 'U' . time();
        
        // Log the demo save
        error_log("DEMO MODE: Simulated save of booking: $consignment_no");
        
        return [
            'success' => true,
            'message' => "Demo Booking {$consignment_no} saved successfully (Demo Mode)",
            'booking_id' => rand(1000, 9999),
            'consignment_no' => $consignment_no,
            'demo_mode' => true,
            'note' => 'This is a demonstration. No actual data was saved.'
        ];
        
    } catch (Exception $e) {
        error_log("Fallback Save Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'error_code' => 'FALLBACK_SAVE_ERROR',
            'demo_mode' => true
        ];
    }
}

// Execute the function and return JSON response
try {
    $response = saveBooking();
    http_response_code($response['success'] ? 200 : 400);
    echo json_encode($response);
} catch (Exception $e) {
    error_log("Critical Save Booking Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error occurred',
        'error_code' => 'CRITICAL_ERROR'
    ]);
}
?>
