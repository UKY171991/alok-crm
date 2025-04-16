<?php
include 'inc/db.php';

$id = $_POST['id'];
$name = mysqli_real_escape_string($conn, $_POST['name']);
$address = mysqli_real_escape_string($conn, $_POST['address']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$gst = mysqli_real_escape_string($conn, $_POST['gst_no']);
$hsn = mysqli_real_escape_string($conn, $_POST['hsn_code']);
$pan = mysqli_real_escape_string($conn, $_POST['pan_no']);
$cin = mysqli_real_escape_string($conn, $_POST['cin_no']);
$aadhaar = mysqli_real_escape_string($conn, $_POST['aadhaar_no']);
$destination = json_encode(array_filter($_POST['destination']));
$parcel_type = json_encode(array_filter($_POST['parcel_type']));
$weight = json_encode(array_filter($_POST['weight']));
$price = json_encode(array_filter($_POST['price']));

if ($id == '') {
    $sql = "INSERT INTO customers (name, address, phone, email, gst_no, hsn_code, pan_no, cin_no, aadhaar_no, destination, parcel_type, weight, price) 
            VALUES ('$name', '$address', '$phone', '$email', '$gst', '$hsn', '$pan', '$cin', '$aadhaar', '$destination', '$parcel_type', '$weight', '$price')";
    $msg = "Customer added successfully.";
} else {
    $sql = "UPDATE customers SET name='$name', address='$address', phone='$phone', email='$email',
            gst_no='$gst', hsn_code='$hsn', pan_no='$pan', cin_no='$cin', aadhaar_no='$aadhaar',
            destination='$destination', parcel_type='$parcel_type', weight='$weight', price='$price'
            WHERE id=$id";
    $msg = "Customer updated successfully.";
}

if ($conn->query($sql)) {
    echo "<div class='alert alert-success'>$msg</div>";
} else {
    echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
}

$conn->close();
