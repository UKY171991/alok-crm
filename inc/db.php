<?php
// Detect if we're on localhost
$is_localhost = ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1');

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

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Set timezone
date_default_timezone_set('Asia/Kolkata');
?>