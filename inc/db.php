<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'alok_crm');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>