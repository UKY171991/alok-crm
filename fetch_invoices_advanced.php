<?php
header('Content-Type: application/json');
require_once 'inc/config.php';
require_once 'inc/db.php';

try {
    // Pagination parameters
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = 10;
    $offset = ($page - 1) * $perPage;
    
    // Filter parameters
    $customerId = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $invoiceDate = isset($_GET['invoice_date']) ? $_GET['invoice_date'] : '';
    $fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : '';
    $toDate = isset($_GET['to_date']) ? $_GET['to_date'] : '';
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    
    if (!empty($customerId)) {
        $whereConditions[] = "i.customer_id = ?";
        $params[] = $customerId;
    }
    
    if (!empty($search)) {
        $whereConditions[] = "(c.name LIKE ? OR i.invoice_no LIKE ? OR i.destination LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    if (!empty($invoiceDate)) {
        $whereConditions[] = "DATE(i.invoice_date) = ?";
        $params[] = $invoiceDate;
    }
    
    if (!empty($fromDate)) {
        $whereConditions[] = "DATE(i.invoice_date) >= ?";
        $params[] = $fromDate;
    }
    
    if (!empty($toDate)) {
        $whereConditions[] = "DATE(i.invoice_date) <= ?";
        $params[] = $toDate;
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Count total records
    $countSql = "SELECT COUNT(*) as total 
                 FROM invoices i 
                 LEFT JOIN customers c ON i.customer_id = c.id 
                 $whereClause";
    
    $countStmt = $conn->prepare($countSql);
    if (!empty($params)) {
        $countStmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $countStmt->execute();
    $totalRecords = $countStmt->get_result()->fetch_assoc()['total'];
    $totalPages = ceil($totalRecords / $perPage);
    
    // Fetch invoices
    $sql = "SELECT i.*, c.name as customer_name 
            FROM invoices i 
            LEFT JOIN customers c ON i.customer_id = c.id 
            $whereClause 
            ORDER BY i.invoice_date DESC, i.created_at DESC 
            LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);
    
    // Add pagination parameters
    $params[] = $perPage;
    $params[] = $offset;
    
    if (!empty($params)) {
        $types = str_repeat('s', count($params) - 2) . 'ii'; // Last two are integers (LIMIT, OFFSET)
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $invoices = [];
    while ($row = $result->fetch_assoc()) {
        $invoices[] = [
            'id' => $row['id'],
            'invoice_no' => $row['invoice_no'],
            'customer_id' => $row['customer_id'],
            'customer_name' => $row['customer_name'] ?: 'Unknown Customer',
            'destination' => $row['destination'],
            'invoice_date' => $row['invoice_date'],
            'from_date' => $row['from_date'],
            'to_date' => $row['to_date'],
            'total_amount' => $row['total_amount'],
            'gst_amount' => $row['gst_amount'],
            'grand_total' => $row['grand_total'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
    }
    
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
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching invoices: ' . $e->getMessage()
    ]);
}
?>
