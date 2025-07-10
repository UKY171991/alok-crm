<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

function getBookings() {
    global $conn;
    
    try {
        // Get filter parameters
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? BOOKING_PAGINATION_LIMIT);
        $offset = ($page - 1) * $limit;
        
        $booking_date = $_GET['booking_date'] ?? '';
        $billing_status = $_GET['billing_status'] ?? '';
        $search = $_GET['search'] ?? '';
        
        // Check database connection
        if (!$conn || $conn->connect_error) {
            return handleFallbackGet();
        }
        
        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        $types = '';
        
        if (!empty($booking_date)) {
            $whereConditions[] = "b.booking_date = ?";
            $params[] = $booking_date;
            $types .= 's';
        }
        
        if (!empty($billing_status)) {
            $whereConditions[] = "b.billing_status = ?";
            $params[] = $billing_status;
            $types .= 's';
        }
        
        if (!empty($search)) {
            $whereConditions[] = "(b.consignment_no LIKE ? OR c.name LIKE ? OR b.city_description LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'sss';
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total 
                     FROM bookings b 
                     LEFT JOIN customers c ON b.customer_id = c.id 
                     $whereClause";
        
        $countStmt = $conn->prepare($countSql);
        if (!empty($params)) {
            $countStmt->bind_param($types, ...$params);
        }
        $countStmt->execute();
        $totalResult = $countStmt->get_result();
        $totalRecords = $totalResult->fetch_assoc()['total'];
        
        // Get bookings
        $sql = "SELECT b.*, c.name as customer_name 
                FROM bookings b 
                LEFT JOIN customers c ON b.customer_id = c.id 
                $whereClause 
                ORDER BY b.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $params[] = $limit;
            $params[] = $offset;
            $types .= 'ii';
            $stmt->bind_param($types, ...$params);
        } else {
            $stmt->bind_param('ii', $limit, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = [
                'id' => intval($row['id']),
                'consignment_no' => $row['consignment_no'],
                'customer_name' => $row['customer_name'] ?? 'Unknown Customer',
                'doc_type' => $row['doc_type'],
                'service_type' => $row['service_type'],
                'pincode' => $row['pincode'],
                'city_description' => $row['city_description'],
                'weight' => number_format(floatval($row['weight']), 3),
                'courier_amt' => number_format(floatval($row['courier_amt']), 2),
                'vas_amount' => number_format(floatval($row['vas_amount']), 2),
                'chargeable_amt' => number_format(floatval($row['chargeable_amt']), 2),
                'billing_status' => $row['billing_status'],
                'booking_date' => $row['booking_date']
            ];
        }
        
        $totalPages = ceil($totalRecords / $limit);
        
        return [
            'success' => true,
            'data' => $bookings,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords,
                'per_page' => $limit
            ]
        ];
        
    } catch (Exception $e) {
        error_log("Get Bookings Error: " . $e->getMessage());
        
        // Try fallback if database error
        if (strpos($e->getMessage(), 'database') !== false || 
            strpos($e->getMessage(), 'connection') !== false ||
            !$conn || $conn->connect_error) {
            return handleFallbackGet();
        }
        
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'error_code' => 'GET_ERROR'
        ];
    }
}

function handleFallbackGet() {
    try {
        // Generate demo bookings data
        $customers = ['STARLIT MEDICAL CENTER PVT LTD', 'ABC LOGISTICS PVT LTD', 'XYZ ENTERPRISES', 'PQR INTERNATIONAL'];
        $cities = ['LKO', 'DEL', 'HYD', 'BLR', 'MUM', 'CCU'];
        $docTypes = ['DOX', 'SPX', 'NDX'];
        $serviceTypes = ['AIR', 'SURFACE'];
        
        $bookings = [];
        for ($i = 1; $i <= 12; $i++) {
            $consignmentNo = 'U36414' . (665 + $i);
            $weight = (mt_rand(100, 10000) / 1000);
            $courierAmt = mt_rand(2000, 10000) / 100;
            $vasAmt = 0;
            
            $bookings[] = [
                'id' => $i,
                'consignment_no' => $consignmentNo,
                'customer_name' => $customers[array_rand($customers)],
                'doc_type' => $docTypes[array_rand($docTypes)],
                'service_type' => $serviceTypes[array_rand($serviceTypes)],
                'pincode' => '110030',
                'city_description' => $cities[array_rand($cities)],
                'weight' => number_format($weight, 3),
                'vas_amount' => number_format($vasAmt, 2),
                'courier_amt' => number_format($courierAmt, 2),
                'chargeable_amt' => number_format($courierAmt + $vasAmt, 2),
                'billing_status' => mt_rand(0, 1) ? 'billed' : 'non-billed',
                'booking_date' => date('Y-m-d')
            ];
        }
        
        // Log the demo data generation
        error_log("DEMO MODE: Generated " . count($bookings) . " demo bookings");
        
        return [
            'success' => true,
            'data' => $bookings,
            'pagination' => [
                'current_page' => 1,
                'total_pages' => 1,
                'total_records' => count($bookings),
                'per_page' => count($bookings)
            ],
            'source' => 'mock',
            'demo_mode' => true,
            'note' => 'This is demonstration data. No actual database connection.'
        ];
        
    } catch (Exception $e) {
        error_log("Fallback Get Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'error_code' => 'FALLBACK_GET_ERROR',
            'demo_mode' => true
        ];
    }
}

// Execute the function and return JSON response
try {
    $response = getBookings();
    http_response_code($response['success'] ? 200 : 400);
    echo json_encode($response);
} catch (Exception $e) {
    error_log("Critical Get Bookings Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error occurred',
        'error_code' => 'CRITICAL_ERROR'
    ]);
}
?>
