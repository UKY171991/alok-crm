<?php
// ajax/delete_order.php
header('Content-Type: application/json');
include '../inc/db.php';
if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID missing.']);
    exit;
}
$id = intval($_POST['id']);
$stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}
$stmt->close();
$conn->close();
