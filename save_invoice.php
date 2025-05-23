<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'inc/db.php';

$id = isset($_POST['id']) ? $_POST['id'] : '';
$customer_id = intval($_POST['customer_id']);
$invoice_date = isset($_POST['invoice_date']) && $_POST['invoice_date'] !== '' ? $_POST['invoice_date'] : date('Y-m-d');
$destination = mysqli_real_escape_string($conn, $_POST['destination']);
$total_amount = floatval($_POST['total_amount']);
$gst_amount = isset($_POST['gst_amount']) ? floatval($_POST['gst_amount']) : 0;
$grand_total = floatval($_POST['grand_total']);

if (!function_exists('generateInvoiceNo')) {
function generateInvoiceNo($conn) {
    $query = "SELECT MAX(id) AS max_id FROM invoices";
    $result = $conn->query($query);
    $nextId = 1;
    if ($result && $row = $result->fetch_assoc()) {
        $nextId = isset($row['max_id']) ? $row['max_id'] + 1 : 1;
    }
    return "INV-" . str_pad($nextId, 6, "0", STR_PAD_LEFT);
}
}

if ($id == '') {
    $invoice_no = generateInvoiceNo($conn);
    $check = $conn->query("SELECT id FROM invoices WHERE invoice_no = '$invoice_no'");
    if ($check && $check->num_rows > 0) {
        echo "<div class='alert alert-danger'>Error: Invoice No already exists.</div>";
        $conn->close();
        exit;
    }
    $sql = "INSERT INTO invoices (invoice_no, customer_id, invoice_date, destination, total_amount, gst_amount, grand_total)
            VALUES ('$invoice_no', '$customer_id', '$invoice_date', '$destination', '$total_amount', '$gst_amount', '$grand_total')";
    $msg = "Invoice added successfully.";
} else {
    // Get the current invoice_no for this id
    $result = $conn->query("SELECT invoice_no FROM invoices WHERE id = '$id'");
    $invoice_no = ($result && $row = $result->fetch_assoc()) ? $row['invoice_no'] : '';
    $check = $conn->query("SELECT id FROM invoices WHERE invoice_no = '$invoice_no' AND id != '$id'");
    if ($check && $check->num_rows > 0) {
        echo "<div class='alert alert-danger'>Error: Invoice No already exists.</div>";
        $conn->close();
        exit;
    }
    $sql = "UPDATE invoices SET customer_id='$customer_id', invoice_date='$invoice_date',
            destination='$destination', total_amount='$total_amount', gst_amount='$gst_amount', grand_total='$grand_total' WHERE id=$id";
    $msg = "Invoice updated successfully.";
}

if ($conn->query($sql)) {
    // Get invoice ID
    $invoice_id = ($id == '') ? $conn->insert_id : $id;

    // Handle line items
    if (!empty($_POST['line_items']) && is_array($_POST['line_items'])) {
        // On edit, delete old line items first
        if ($id != '') {
            $conn->query("DELETE FROM invoice_items WHERE invoice_id = $invoice_id");
        }
        foreach ($_POST['line_items'] as $item) {
            $booking_date = $conn->real_escape_string($item['booking_date']);
            $consignment_no = $conn->real_escape_string($item['consignment_no']);
            $destination_city = $conn->real_escape_string($item['destination_city']);
            $weight = isset($item['weight']) ? floatval($item['weight']) : 0;
            $amt = isset($item['amt']) ? floatval($item['amt']) : 0;
            $way_bill_value = isset($item['way_bill_value']) ? floatval($item['way_bill_value']) : 0;
            $conn->query("INSERT INTO invoice_items (invoice_id, booking_date, consignment_no, destination_city, weight, amt, way_bill_value) VALUES ($invoice_id, '$booking_date', '$consignment_no', '$destination_city', '$weight', '$amt', '$way_bill_value')");
        }
    }
    echo "<div class='alert alert-success'>$msg</div>";
} else {
    echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
}

$conn->close();
?>
