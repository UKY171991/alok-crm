<?php
include 'inc/db.php';

$invoice_id = isset($_GET['invoice_id']) ? intval($_GET['invoice_id']) : 0;
$items = [];
file_put_contents('debug_invoice_items.log', "invoice_id: $invoice_id\n", FILE_APPEND);
if ($invoice_id > 0) {
    $result = $conn->query("SELECT DATE_FORMAT(booking_date, '%Y-%m-%d') as booking_date, consignment_no, destination_city, weight, amt, way_bill_value, description, quantity, rate, amount FROM invoice_items WHERE invoice_id = $invoice_id ORDER BY booking_date, id");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    file_put_contents('debug_invoice_items.log', print_r($items, true), FILE_APPEND);
}
header('Content-Type: application/json');
echo json_encode($items);
$conn->close(); 