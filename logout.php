<?php
// filepath: c:\xampp\htdocs\alok-crm\logout.php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page
header('Location: login.php');
exit;
?>