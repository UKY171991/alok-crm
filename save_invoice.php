<?php
include 'inc/db.php';

$id = $_POST['id'];
$invoice_no = mysqli_real_escape_string($conn, $_POST['invoice_no']);
$customer_id = intval($_POST['customer_id']);
$invoice_date = $_POST['invoice_date'];
$total_amount = floatval($_POST['total_amount']);
$gst_amount = isset($_POST['gst_amount']) ? floatval($_POST['gst_amount']) : 0;
$grand_total = floatval($_POST['grand_total']);

if ($id == '') {
    $sql = "INSERT INTO invoices (invoice_no, customer_id, invoice_date, total_amount, gst_amount, grand_total)
            VALUES ('$invoice_no', '$customer_id', '$invoice_date', '$total_amount', '$gst_amount', '$grand_total')";
    $msg = "Invoice added successfully.";
} else {
    $sql = "UPDATE invoices SET invoice_no='$invoice_no', customer_id='$customer_id', invoice_date='$invoice_date',
            total_amount='$total_amount', gst_amount='$gst_amount', grand_total='$grand_total' WHERE id=$id";
    $msg = "Invoice updated successfully.";
}

if ($conn->query($sql)) {
    echo "<div class='alert alert-success'>$msg</div>";
} else {
    echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
}

$conn->close();
