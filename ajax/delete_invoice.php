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
require_once __DIR__ . '/../api_fallback.php';

function deleteInvoice() {
    global $conn;
    
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Invalid request method');
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? $_POST['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('Invoice ID is required');
        }
        
        $id = intval($id);
        
        // Check database connection
        if (!$conn || $conn->connect_error) {
            return handleFallbackDelete($id);
        }
        
        // Check if invoice exists
        $checkSql = "SELECT invoice_no FROM invoices WHERE id = ?";
        $checkStmt = $conn->prepare($checkSql);
        
        if (!$checkStmt) {
            throw new Exception('Database prepare error: ' . $conn->error);
        }
        
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Invoice not found');
        }
        
        $invoice = $result->fetch_assoc();
        $invoiceNo = $invoice['invoice_no'];
        
        // Start transaction
        $conn->autocommit(false);
        
        try {
            // Delete related invoice items first
            $deleteItemsSql = "DELETE FROM invoice_items WHERE invoice_id = ?";
            $deleteItemsStmt = $conn->prepare($deleteItemsSql);
            
            if (!$deleteItemsStmt) {
                throw new Exception('Database prepare error for items: ' . $conn->error);
            }
            
            $deleteItemsStmt->bind_param('i', $id);
            
            if (!$deleteItemsStmt->execute()) {
                throw new Exception('Failed to delete invoice items: ' . $conn->error);
            }
            
            // Delete the invoice
            $deleteSql = "DELETE FROM invoices WHERE id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            
            if (!$deleteStmt) {
                throw new Exception('Database prepare error for invoice: ' . $conn->error);
            }
            
            $deleteStmt->bind_param('i', $id);
            
            if (!$deleteStmt->execute()) {
                throw new Exception('Failed to delete invoice: ' . $conn->error);
            }
            
            // Commit transaction
            $conn->commit();
            $conn->autocommit(true);
            
            return [
                'success' => true,
                'message' => "Invoice #{$invoiceNo} deleted successfully",
                'invoice_id' => $id,
                'invoice_no' => $invoiceNo
            ];
            
        } catch (Exception $e) {
            // Rollback transaction
            $conn->rollback();
            $conn->autocommit(true);
            throw $e;
        }
        
    } catch (Exception $e) {
        error_log("Delete Invoice Error: " . $e->getMessage());
        
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
        $demoInvoices = [
            1 => 'INV-2024-001',
            2 => 'INV-2024-002',
            3 => 'INV-2024-003',
            4 => 'INV-2024-004',
            5 => 'INV-2024-005'
        ];
        
        if (!isset($demoInvoices[$id])) {
            throw new Exception('Demo invoice not found');
        }
        
        $invoiceNo = $demoInvoices[$id];
        
        // Log the demo deletion
        error_log("DEMO MODE: Simulated deletion of invoice ID: $id, Number: $invoiceNo");
        
        return [
            'success' => true,
            'message' => "Demo Invoice #{$invoiceNo} deleted successfully (Demo Mode)",
            'invoice_id' => $id,
            'invoice_no' => $invoiceNo,
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
    $response = deleteInvoice();
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
