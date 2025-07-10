<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

function deleteBooking() {
    global $conn;
    
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Invalid request method');
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? $_POST['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('Booking ID is required');
        }
        
        $id = intval($id);
        
        // Check database connection
        if (!$conn || $conn->connect_error) {
            return handleFallbackDelete($id);
        }
        
        // Check if booking exists
        $checkSql = "SELECT consignment_no FROM bookings WHERE id = ?";
        $checkStmt = $conn->prepare($checkSql);
        
        if (!$checkStmt) {
            throw new Exception('Database prepare error: ' . $conn->error);
        }
        
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Booking not found');
        }
        
        $booking = $result->fetch_assoc();
        $consignmentNo = $booking['consignment_no'];
        
        // Delete the booking
        $deleteSql = "DELETE FROM bookings WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        
        if (!$deleteStmt) {
            throw new Exception('Database prepare error: ' . $conn->error);
        }
        
        $deleteStmt->bind_param('i', $id);
        
        if ($deleteStmt->execute()) {
            return [
                'success' => true,
                'message' => "Booking {$consignmentNo} deleted successfully",
                'booking_id' => $id,
                'consignment_no' => $consignmentNo
            ];
        } else {
            throw new Exception('Failed to delete booking: ' . $conn->error);
        }
        
    } catch (Exception $e) {
        error_log("Delete Booking Error: " . $e->getMessage());
        
        // Try fallback if database error
        if (strpos($e->getMessage(), 'database') !== false || 
            strpos($e->getMessage(), 'connection') !== false ||
            !$conn || $conn->connect_error) {
            return handleFallbackDelete($id);
        }
        
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'error_code' => 'DELETE_ERROR'
        ];
    }
}

function handleFallbackDelete($id) {
    try {
        // In demo/fallback mode, simulate successful deletion
        $demoBookings = [
            1 => 'U36414666',
            2 => 'U36414667',
            3 => 'U36414668',
            4 => 'U36414669',
            5 => 'U36414670'
        ];
        
        if (!isset($demoBookings[$id])) {
            throw new Exception('Demo booking not found');
        }
        
        $consignmentNo = $demoBookings[$id];
        
        // Log the demo deletion
        error_log("DEMO MODE: Simulated deletion of booking ID: $id, Consignment: $consignmentNo");
        
        return [
            'success' => true,
            'message' => "Demo Booking {$consignmentNo} deleted successfully (Demo Mode)",
            'booking_id' => $id,
            'consignment_no' => $consignmentNo,
            'demo_mode' => true,
            'note' => 'This is a demonstration. No actual data was deleted.'
        ];
        
    } catch (Exception $e) {
        error_log("Fallback Delete Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'error_code' => 'FALLBACK_DELETE_ERROR',
            'demo_mode' => true
        ];
    }
}

// Execute the function and return JSON response
try {
    $response = deleteBooking();
    http_response_code($response['success'] ? 200 : 400);
    echo json_encode($response);
} catch (Exception $e) {
    error_log("Critical Delete Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error occurred',
        'error_code' => 'CRITICAL_ERROR'
    ]);
}
?>
