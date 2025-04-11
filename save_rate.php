<?php
include 'inc/db.php';

$id = $_POST['id'];
$destination = mysqli_real_escape_string($conn, $_POST['destination']);
$type = mysqli_real_escape_string($conn, $_POST['type']);
$weight_category = mysqli_real_escape_string($conn, $_POST['weight_category']);
$rate = floatval($_POST['rate']);

if ($id == '') {
    $sql = "INSERT INTO rates (destination, type, weight_category, rate) 
            VALUES ('$destination', '$type', '$weight_category', '$rate')";
    $msg = "Rate added successfully.";
} else {
    $sql = "UPDATE rates SET 
            destination='$destination', 
            type='$type', 
            weight_category='$weight_category', 
            rate='$rate' 
            WHERE id=$id";
    $msg = "Rate updated successfully.";
}

if ($conn->query($sql)) {
    echo "<div class='alert alert-success'>$msg</div>";
} else {
    echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
}

$conn->close();
