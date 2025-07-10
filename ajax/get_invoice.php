<?php
header('Content-Type: application/json');
require_once '../inc/config.php';

// Mock invoice data for when database is not available
$mockInvoice = [
    'id' => $_GET['id'] ?? 1,
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
];

try {
    require_once '../inc/db.php';
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        throw new Exception("Invalid invoice ID");
    }
    
    $sql = "SELECT i.*, c.name as customer_name 
            FROM invoices i 
            LEFT JOIN customers c ON i.customer_id = c.id 
            WHERE i.id = ?";
            
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo json_encode([
                'success' => true,
                'data' => $row,
                'source' => 'database'
            ]);
        } else {
            throw new Exception("Invoice not found");
        }
        
        $stmt->close();
    } else {
        throw new Exception("Failed to prepare query: " . $conn->error);
    }
    
} catch (Exception $e) {
    // Return mock data when database is not available
    error_log("get_invoice.php using mock data: " . $e->getMessage());
    
    echo json_encode([
        'success' => true,
        'data' => $mockInvoice,
        'source' => 'mock',
        'message' => 'Using demo data - database not available'
    ]);
}
?>
