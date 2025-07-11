<?php
// Detect if we're on localhost
$is_localhost = true; // Default to localhost

// Check if we're in a web context and detect environment
if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
    $server_name = $_SERVER['SERVER_NAME'] ?? '';
    $server_addr = $_SERVER['SERVER_ADDR'] ?? '';
    
    $is_localhost = ($host == 'localhost' || 
                    $host == '127.0.0.1' || 
                    $server_name == 'localhost' || 
                    $server_addr == '127.0.0.1' ||
                    strpos($host, 'localhost') !== false);
} elseif (isset($_SERVER['SERVER_NAME'])) {
    $server_name = $_SERVER['SERVER_NAME'];
    $server_addr = $_SERVER['SERVER_ADDR'] ?? '';
    
    $is_localhost = ($server_name == 'localhost' || 
                    $server_addr == '127.0.0.1');
}

// Set database credentials based on environment
if ($is_localhost) {
    // Local database credentials
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'alok_crm';
} else {
    // Live server credentials
    $db_host = 'localhost';
    $db_user = 'fnkjyinw_alok_crm';
    $db_pass = ')joaUE#f~h6F';
    $db_name = 'fnkjyinw_alok_crm';
}

// Create connection with error handling
try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    // Log the error but don't die, let api_fallback handle it gracefully
    error_log("Database connection error: " . $e->getMessage());
    
    // Check if we're in api_fallback.php or similar API scripts
    $script_name = basename($_SERVER['SCRIPT_NAME'] ?? '');
    $is_api_script = ($script_name === 'api_fallback.php' || 
                     strpos($script_name, 'api_') === 0 || 
                     strpos($script_name, 'ajax') !== false ||
                     strpos($script_name, 'test_') === 0);
    
    if ($is_api_script) {
        throw $e; // Re-throw to be caught by api_fallback
    } else {
        // For web pages, set connection to null and continue with fallback mode
        $conn = null;
        // Don't die - let the page load and use AJAX fallback
    }
}

// Set timezone
date_default_timezone_set('Asia/Kolkata');

function generateInvoiceNo($conn) {
    $query = "SELECT MAX(id) AS max_id FROM invoices";
    $result = $conn->query($query);
    $maxId = 1;

    if ($result && $row = $result->fetch_assoc()) {
        $maxId = intval($row['max_id']) + 1;
    }

    return "INV-" . str_pad($maxId, 6, "0", STR_PAD_LEFT);
}
?>