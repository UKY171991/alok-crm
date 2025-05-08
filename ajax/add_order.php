<?php
// ajax/add_order.php
header('Content-Type: application/json');
include '../inc/db.php';

$fields = [
    'date','docket','location','destination','mode','no_of_pcs','pincode','content','dox_non_dox','material_value','fr_weight','valumatric','manual_weight','invoice_wt','round_off_weight','clinet_billing_value','credit_cust_amt','regular_cust_amt','customer_type','sender_detail','payment_status','sender_contact_no','address','adhaar_no','customer_attend_by','today_date','pending','td_delivery_status','td_delivery_date','t_receiver_name','receiver_contact_no','receiver_name_as_per_sendor','ref','complain_no_update','shipment_cost_by_other_mode','remarks','pod_status','pending_days'
];

$data = [];
foreach ($fields as $f) {
    $data[$f] = isset($_POST[$f]) ? $_POST[$f] : null;
}

$stmt = $conn->prepare("INSERT INTO orders (".implode(",", $fields).") VALUES (".str_repeat('?,', count($fields)-1)."?)");
$stmt->bind_param(
    str_repeat('s', count($fields)),
    ...array_values($data)
);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}
$stmt->close();
$conn->close();
