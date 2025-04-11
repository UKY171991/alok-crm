<?php
include 'inc/db.php';

$id = $_POST['id'];
$destination = $_POST['destination'];
$type = $_POST['type'];
$weight = $_POST['weight_category'];
$rate = floatval($_POST['rate']);

$sql = "UPDATE rates SET destination='$destination', type='$type', weight_category='$weight', rate='$rate' WHERE id=$id";
$conn->query($sql);
$conn->close();
