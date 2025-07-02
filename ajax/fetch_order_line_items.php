<?php
include '../inc/db.php';
header('Content-Type: application/json');
$order_ids = isset($_POST['order_ids']) ? $_POST['order_ids'] : [];
if (!is_array($order_ids) || empty($order_ids)) {
    echo json_encode([]);
    exit;
}
$ids = array_map('intval', $order_ids);
$id_list = implode(',', $ids);
$sql = "SELECT * FROM orders WHERE id IN ($id_list)";
$result = $conn->query($sql);
$items = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'booking_date' => $row['date'],
            'consignment_no' => $row['docket'],
            'destination' => $row['destination'],
            'weight' => $row['weight'] ?? '',
            'amt' => $row['clinet_billing_value'] ?? '',
            'way_bill_value' => $row['material_value'] ?? '',
            'description' => $row['content'],
            'quantity' => $row['no_of_pcs'],
            'dox_non_dox' => $row['dox_non_dox'] ?? '',
            'service' => $row['service'] ?? '',
            'rate' => '',
            'amount' => '',
        ];
    }
}
echo json_encode($items);
$conn->close(); 