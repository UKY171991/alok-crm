<?php
// ajax/view_order.php
include '../inc/db.php';
if (!isset($_POST['id'])) {
    echo '<div class="alert alert-danger">Order ID missing.</div>';
    exit;
}
$id = intval($_POST['id']);
$sql = "SELECT o.*, c.name AS customer_name FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE o.id = $id LIMIT 1";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    echo '<table class="table table-bordered">';
    // Show customer name at the top
    echo '<tr><th>Customer</th><td>' . htmlspecialchars($row['customer_name'] ?? '') . '</td></tr>';
    foreach ($row as $key => $value) {
        if ($key === 'customer_name') continue;
        echo '<tr><th>'.ucwords(str_replace('_',' ', $key)).'</th><td>'.htmlspecialchars($value).'</td></tr>';
    }
    echo '</table>';
} else {
    echo '<div class="alert alert-warning">Order not found.</div>';
}
$conn->close();
