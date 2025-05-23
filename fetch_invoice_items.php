<?php
include 'inc/db.php';

$invoice_id = isset($_GET['invoice_id']) ? intval($_GET['invoice_id']) : 0;
$items = [];
if ($invoice_id > 0) {
    $result = $conn->query("SELECT booking_date, consignment_no, destination_city, weight, amt, way_bill_value FROM invoice_items WHERE invoice_id = $invoice_id ORDER BY booking_date, id");
    while ($row = $result && $result->fetch_assoc()) {
        $items[] = $row;
    }
}
header('Content-Type: application/json');
echo json_encode($items);
$conn->close(); 