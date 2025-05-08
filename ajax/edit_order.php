<?php
// ajax/edit_order.php
include '../inc/db.php';
$fields = [
    'date','docket','location','destination','mode','no_of_pcs','pincode','content','dox_non_dox','material_value','fr_weight','valumatric','manual_weight','invoice_wt','round_off_weight','clinet_billing_value','credit_cust_amt','regular_cust_amt','customer_type','sender_detail','payment_status','sender_contact_no','address','adhaar_no','customer_attend_by','today_date','pending','td_delivery_status','td_delivery_date','t_receiver_name','receiver_contact_no','receiver_name_as_per_sendor','ref','complain_no_update','shipment_cost_by_other_mode','remarks','pod_status','pending_days'
];

if ($_POST['action'] === 'fetch' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $result = $conn->query("SELECT * FROM orders WHERE id = $id LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        echo '<input type="hidden" name="id" value="'.htmlspecialchars($row['id']).'">';
        echo '<div class="row">';
        foreach ($fields as $f) {
            $type = (strpos($f, 'date') !== false) ? 'date' : (strpos($f, 'amt') !== false || strpos($f, 'value') !== false || strpos($f, 'weight') !== false || strpos($f, 'wt') !== false || strpos($f, 'cost') !== false || strpos($f, 'days') !== false ? 'number' : 'text');
            $step = ($type === 'number') ? 'step=\"0.01\"' : '';
            echo '<div class="col-md-3 mb-2">';
            echo '<input type="'.$type.'" name="'.$f.'" class="form-control" placeholder="'.ucwords(str_replace('_',' ', $f)).'" value="'.htmlspecialchars($row[$f]).'" '.$step.'>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="alert alert-warning">Order not found.</div>';
    }
    $conn->close();
    exit;
}

if ($_POST['action'] === 'update' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $set = [];
    $params = [];
    foreach ($fields as $f) {
        $set[] = "$f = ?";
        $params[] = isset($_POST[$f]) ? $_POST[$f] : null;
    }
    $params[] = $id;
    $stmt = $conn->prepare("UPDATE orders SET ".implode(",", $set)." WHERE id = ?");
    $stmt->bind_param(str_repeat('s', count($fields)).'i', ...$params);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}
echo json_encode(['success' => false, 'message' => 'Invalid request.']);
