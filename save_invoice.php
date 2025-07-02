<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'inc/db.php';

// Get POST data
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : 0;
$invoice_date = !empty($_POST['invoice_date']) ? $_POST['invoice_date'] : date('Y-m-d');
$invoice_no = '';
$total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
$gst_amount = isset($_POST['gst_amount']) ? floatval($_POST['gst_amount']) : 0;
$grand_total = isset($_POST['grand_total']) ? floatval($_POST['grand_total']) : 0;

// Add or update invoice header
if ($id === 0) {
    $invoice_no = generateInvoiceNo($conn);
    $sql = "INSERT INTO invoices (invoice_no, customer_id, invoice_date, total_amount, gst_amount, grand_total)
            VALUES ('$invoice_no', '$customer_id', '$invoice_date', '$total_amount', '$gst_amount', '$grand_total')";
    if (!$conn->query($sql)) {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        $conn->close();
        exit;
    }
    $invoice_id = $conn->insert_id;
    $msg = "Invoice added successfully.";
} else {
    // Get invoice_no for update
    $result = $conn->query("SELECT invoice_no FROM invoices WHERE id = $id");
    $invoice_no = ($result && $row = $result->fetch_assoc()) ? $row['invoice_no'] : '';
    $sql = "UPDATE invoices SET customer_id='$customer_id', invoice_date='$invoice_date', total_amount='$total_amount', gst_amount='$gst_amount', grand_total='$grand_total' WHERE id=$id";
    if (!$conn->query($sql)) {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        $conn->close();
        exit;
    }
    $invoice_id = $id;
    $msg = "Invoice updated successfully.";
    // Remove old line items
    $conn->query("DELETE FROM invoice_items WHERE invoice_id = $invoice_id");
}

// Insert line items
if (!empty($_POST['line_items']) && is_array($_POST['line_items'])) {
    error_log('Line items received: ' . print_r($_POST['line_items'], true));
    foreach ($_POST['line_items'] as $item) {
        $order_id = isset($item['order_id']) ? intval($item['order_id']) : 0;
        if ($order_id > 0) {
            $exists = $conn->query("SELECT 1 FROM invoice_items WHERE order_id = $order_id LIMIT 1");
            if ($exists && $exists->num_rows > 0) continue; // Skip if already exists
        }
        $booking_date = $conn->real_escape_string($item['booking_date'] ?? '');
        $consignment_no = $conn->real_escape_string($item['consignment_no'] ?? '');
        $destination_city = $conn->real_escape_string($item['destination_city'] ?? '');
        $dox_non_dox = $conn->real_escape_string($item['dox_non_dox'] ?? '');
        $service = $conn->real_escape_string($item['service'] ?? '');
        $weight = isset($item['weight']) ? floatval($item['weight']) : 0;
        $amt = isset($item['amt']) ? floatval($item['amt']) : 0;
        $way_bill_value = isset($item['way_bill_value']) ? floatval($item['way_bill_value']) : 0;
        $description = $conn->real_escape_string($item['description'] ?? '');
        $quantity = isset($item['quantity']) ? floatval($item['quantity']) : 0;
        $rate = isset($item['rate']) ? floatval($item['rate']) : 0;
        $amount = isset($item['amount']) ? floatval($item['amount']) : 0;

        $insert_sql = "INSERT INTO invoice_items (
            invoice_id, booking_date, consignment_no, destination_city, dox_non_dox, service, weight, amt, way_bill_value, description, quantity, rate, amount, order_id
        ) VALUES (
            $invoice_id, '$booking_date', '$consignment_no', '$destination_city', '$dox_non_dox', '$service', '$weight', '$amt', '$way_bill_value', '$description', '$quantity', '$rate', '$amount', $order_id
        )";
        error_log('Invoice item insert SQL: ' . $insert_sql);
        if (!$conn->query($insert_sql)) {
            error_log('Invoice item insert error: ' . $conn->error . ' | SQL: ' . $insert_sql);
            echo "<div class='alert alert-danger'>Error saving line item: " . $conn->error . "</div>";
            $conn->close();
            exit;
        }
    }
} else {
    error_log('No line items found in POST data.');
    echo "<div class='alert alert-danger'>No line items found in submitted data.</div>";
    $conn->close();
    exit;
}

echo "<div class='alert alert-success'>$msg</div>";
$conn->close();
?>
