<?php
session_start();
include 'inc/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $destination = mysqli_real_escape_string($conn, $_POST['destination']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $weight_category = mysqli_real_escape_string($conn, $_POST['weight_category']);
    $rate = floatval($_POST['rate']);

    $sql = "INSERT INTO rates (destination, type, weight_category, rate) 
            VALUES ('$destination', '$type', '$weight_category', '$rate')";

    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Rate added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }

    $conn->close();
}
?>
