<?php
// ajax/edit_order.php
include '../inc/db.php';
$fields = [
    'customer_id',
    'date','docket','location','destination','mode','no_of_pcs','pincode','content','dox_non_dox','material_value','fr_weight','valumatric','manual_weight','invoice_wt','round_off_weight','clinet_billing_value','credit_cust_amt','regular_cust_amt','customer_type','sender_detail','payment_status','sender_contact_no','address','adhaar_no','customer_attend_by','today_date','pending','td_delivery_status','td_delivery_date','t_receiver_name','receiver_contact_no','receiver_name_as_per_sendor','ref','complain_no_update','shipment_cost_by_other_mode','remarks','pod_status','pending_days'
];

if ($_POST['action'] === 'fetch' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $result = $conn->query("SELECT * FROM orders WHERE id = $id LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        echo '<input type="hidden" name="id" value="'.htmlspecialchars($row['id']).'">';
        echo '<div class="row">';
        foreach ($fields as $f) {
            if ($f === 'customer_id') {
                // Customer select field with label
                echo '<div class="col-md-3 mb-2">';
                echo '<label for="edit_customer_id" class="form-label">Customer</label>';
                echo '<select name="customer_id" id="edit_customer_id" class="form-select form-control" required><option value="">Loading...</option></select>';
                echo '</div>';
                continue;
            }
            $type = (strpos($f, 'date') !== false) ? 'date' : (strpos($f, 'amt') !== false || strpos($f, 'value') !== false || strpos($f, 'weight') !== false || strpos($f, 'wt') !== false || strpos($f, 'cost') !== false || strpos($f, 'days') !== false ? 'number' : 'text');
            $step = ($type === 'number') ? 'step="0.01"' : '';
            $label = ucwords(str_replace('_',' ', $f));
            echo '<div class="col-md-3 mb-2">';
            echo '<label for="edit_'.$f.'" class="form-label">'.$label.'</label>';
            echo '<input type="'.$type.'" name="'.$f.'" id="edit_'.$f.'" class="form-control" placeholder="'.$label.'" value="'.htmlspecialchars($row[$f]).'" '.$step.'>';
            echo '</div>';
        }
        echo '</div>';
        // JS to populate customer select and set selected value
        echo '<script>$(function() { var selectedId = "'.htmlspecialchars($row['customer_id'] ?? '').'"; var $select = $("#edit_customer_id"); $.get("ajax/fetch_customers_select.php", function(data) { $select.html(data); if(selectedId) $select.val(selectedId); }); });<\/script>';
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
    $types = '';
    foreach ($fields as $f) {
        $set[] = "$f = ?";
        // customer_id should be int, rest are string
        if ($f === 'customer_id') {
            $types .= 'i';
            $params[] = isset($_POST[$f]) ? intval($_POST[$f]) : null;
        } else {
            $types .= 's';
            $params[] = isset($_POST[$f]) ? $_POST[$f] : null;
        }
    }
    $params[] = $id;
    $types .= 'i';
    $stmt = $conn->prepare("UPDATE orders SET ".implode(",", $set)." WHERE id = ?");
    $stmt->bind_param($types, ...$params);
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
