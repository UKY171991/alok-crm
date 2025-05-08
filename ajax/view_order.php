<?php
// ajax/view_order.php
include '../inc/db.php';
if (!isset($_POST['id'])) {
    echo '<div class="alert alert-danger">Order ID missing.</div>';
    exit;
}
$id = intval($_POST['id']);
$result = $conn->query("SELECT * FROM orders WHERE id = $id LIMIT 1");
if ($result && $row = $result->fetch_assoc()) {
    echo '<table class="table table-bordered">';
    foreach ($row as $key => $value) {
        echo '<tr><th>'.ucwords(str_replace('_',' ', $key)).'</th><td>'.htmlspecialchars($value).'</td></tr>';
    }
    echo '</table>';
} else {
    echo '<div class="alert alert-warning">Order not found.</div>';
}
$conn->close();
