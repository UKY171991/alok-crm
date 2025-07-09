<?php
header('Content-Type: application/json');
require_once 'inc/config.php';
require_once 'inc/db.php';

try {
    // Get parameters
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 50;
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    
    if (!empty($search)) {
        $whereConditions[] = "(name LIKE ? OR phone LIKE ? OR email LIKE ? OR gst_no LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM customers $whereClause";
    $countStmt = $conn->prepare($countSql);
    if (!empty($params)) {
        $countStmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $countStmt->execute();
    $totalRecords = $countStmt->get_result()->fetch_assoc()['total'];
    
    // Get customers
    $offset = ($page - 1) * $perPage;
    $sql = "SELECT id, name, email, phone, address, gst_no, hsn_code, pan_no, cin_no, aadhaar_no 
            FROM customers 
            $whereClause 
            ORDER BY name ASC 
            LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);
    
    // Add pagination parameters
    $params[] = $perPage;
    $params[] = $offset;
    
    if (!empty($params)) {
        $types = str_repeat('s', count($params) - 2) . 'ii'; // Last two are integers
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'address' => $row['address'],
            'gst_no' => $row['gst_no'],
            'hsn_code' => $row['hsn_code'],
            'pan_no' => $row['pan_no'],
            'cin_no' => $row['cin_no'],
            'aadhaar_no' => $row['aadhaar_no']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $customers,
        'pagination' => [
            'total_records' => $totalRecords,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($totalRecords / $perPage)
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching customers: ' . $e->getMessage()
    ]);
}
?>
