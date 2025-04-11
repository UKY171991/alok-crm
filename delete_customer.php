<?php
include 'inc/db.php';
$id = intval($_POST['id']);
$conn->query("DELETE FROM customers WHERE id=$id");
$conn->close();
