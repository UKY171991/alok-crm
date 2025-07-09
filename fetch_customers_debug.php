<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
require_once 'inc/config.php';
require_once 'inc/db.php';

// Debug: Check if connection exists
if (!isset($conn)) {
    echo json_encode(['success' => false, 'message' => 'Database connection not found']);
    exit;
}

try {
    // Test customers table
    $result = $conn->query("SELECT COUNT(*) as count FROM customers");
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Customers table not found: ' . $conn->error]);
        exit;
    }
    $count = $result->fetch_assoc()['count'];
    
    // Fetch all customers
    $query = "SELECT id, name, email, phone, address FROM customers ORDER BY name";
    $result = $conn->query($query);
    
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Query failed: ' . $conn->error]);
        exit;
    }
    
    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'address' => $row['address']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'debug' => [
            'customers_count' => $count
        ],
        'data' => $customers
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
