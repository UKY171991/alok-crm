<?php
header('Content-Type: application/json');

// Mock data for when database is not available
$mockCustomers = [
    ['id' => 1, 'name' => 'ABC Corporation', 'email' => 'abc@example.com', 'phone' => '9876543210', 'address' => '123 Business Street, Mumbai'],
    ['id' => 2, 'name' => 'XYZ Ltd', 'email' => 'xyz@example.com', 'phone' => '9876543211', 'address' => '456 Commercial Road, Delhi'],
    ['id' => 3, 'name' => 'PQR Enterprises', 'email' => 'pqr@example.com', 'phone' => '9876543212', 'address' => '789 Industrial Area, Bangalore'],
    ['id' => 4, 'name' => 'Quick Logistics', 'email' => 'quick@example.com', 'phone' => '9876543213', 'address' => '321 Transport Hub, Chennai'],
    ['id' => 5, 'name' => 'Fast Courier', 'email' => 'fast@example.com', 'phone' => '9876543214', 'address' => '654 Express Lane, Pune']
];

$mockInvoices = [
    [
        'id' => 1,
        'invoice_no' => 'INV-20241201-001',
        'customer_id' => 1,
        'customer_name' => 'ABC Corporation',
        'destination' => 'Mumbai',
        'invoice_date' => '2024-12-01',
        'from_date' => '2024-11-01',
        'to_date' => '2024-11-30',
        'total_amount' => '1000.00',
        'gst_amount' => '180.00',
        'grand_total' => '1180.00',
        'status' => 'pending',
        'created_at' => '2024-12-01 10:30:00',
        'updated_at' => '2024-12-01 10:30:00'
    ],
    [
        'id' => 2,
        'invoice_no' => 'INV-20241201-002',
        'customer_id' => 2,
        'customer_name' => 'XYZ Ltd',
        'destination' => 'Delhi',
        'invoice_date' => '2024-12-01',
        'from_date' => '2024-11-01',
        'to_date' => '2024-11-30',
        'total_amount' => '1500.00',
        'gst_amount' => '270.00',
        'grand_total' => '1770.00',
        'status' => 'paid',
        'created_at' => '2024-12-01 11:15:00',
        'updated_at' => '2024-12-01 11:15:00'
    ],
    [
        'id' => 3,
        'invoice_no' => 'INV-20241201-003',
        'customer_id' => 3,
        'customer_name' => 'PQR Enterprises',
        'destination' => 'Bangalore',
        'invoice_date' => '2024-12-01',
        'from_date' => '2024-11-01',
        'to_date' => '2024-11-30',
        'total_amount' => '800.00',
        'gst_amount' => '144.00',
        'grand_total' => '944.00',
        'status' => 'pending',
        'created_at' => '2024-12-01 12:00:00',
        'updated_at' => '2024-12-01 12:00:00'
    ]
];

try {
    // Try to connect to database first
    require_once 'inc/config.php';
    require_once 'inc/db.php';
    
    // If we get here, database is available and $conn should be defined
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Database connection not available");
    }
    
    $endpoint = $_GET['endpoint'] ?? '';
    
    if ($endpoint === 'customers') {
        // Get parameters
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        $whereClause = '';
        $params = [];
        
        if (!empty($search)) {
            $whereClause = "WHERE (name LIKE ? OR phone LIKE ? OR email LIKE ?)";
            $searchParam = "%$search%";
            $params = [$searchParam, $searchParam, $searchParam];
        }
        
        $sql = "SELECT id, name, email, phone, address FROM customers $whereClause ORDER BY name ASC";
        
        if ($stmt = $conn->prepare($sql)) {
            if (!empty($params)) {
                $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $customers = [];
            while ($row = $result->fetch_assoc()) {
                $customers[] = $row;
            }
            
            $stmt->close();
            
            echo json_encode([
                'success' => true,
                'data' => $customers,
                'source' => 'database'
            ]);
        } else {
            throw new Exception("Failed to prepare customers query: " . $conn->error);
        }
        
    } else if ($endpoint === 'invoices') {
        // Invoice logic here
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT i.*, c.name as customer_name 
                FROM invoices i 
                LEFT JOIN customers c ON i.customer_id = c.id 
                ORDER BY i.invoice_date DESC, i.created_at DESC 
                LIMIT ? OFFSET ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('ii', $perPage, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $invoices = [];
            while ($row = $result->fetch_assoc()) {
                $invoices[] = $row;
            }
            
            // Get total count
            if ($countResult = $conn->query("SELECT COUNT(*) as total FROM invoices")) {
                $totalRecords = $countResult->fetch_assoc()['total'];
                $totalPages = ceil($totalRecords / $perPage);
                
                echo json_encode([
                    'success' => true,
                    'data' => $invoices,
                    'pagination' => [
                        'current_page' => $page,
                        'total_pages' => $totalPages,
                        'total_records' => $totalRecords,
                        'per_page' => $perPage,
                        'has_next' => $page < $totalPages,
                        'has_prev' => $page > 1
                    ],
                    'source' => 'database'
                ]);
            } else {
                throw new Exception("Failed to get invoice count: " . $conn->error);
            }
            
            $stmt->close();
        } else {
            throw new Exception("Failed to prepare invoices query: " . $conn->error);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Unknown endpoint: ' . $endpoint
        ]);
    }
    
} catch (Exception $e) {
    // Database not available, use mock data
    error_log("API Fallback using mock data: " . $e->getMessage());
    $endpoint = $_GET['endpoint'] ?? '';
    
    if ($endpoint === 'customers') {
        echo json_encode([
            'success' => true,
            'data' => $mockCustomers,
            'source' => 'mock',
            'message' => 'Using demo data - database not available'
        ]);
    } else if ($endpoint === 'invoices') {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 10;
        $totalRecords = count($mockInvoices);
        $totalPages = ceil($totalRecords / $perPage);
        
        // Paginate mock data
        $start = ($page - 1) * $perPage;
        $pageData = array_slice($mockInvoices, $start, $perPage);
        
        echo json_encode([
            'success' => true,
            'data' => $pageData,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords,
                'per_page' => $perPage,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ],
            'source' => 'mock',
            'message' => 'Using demo data - database not available'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Unknown endpoint or database error: ' . $e->getMessage()
        ]);
    }
}
?>
