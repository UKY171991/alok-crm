<?php
header('Content-Type: application/json');
if (!isset($_GET['invoice_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing invoice_id.']);
    exit;
}
$invoice_id = intval($_GET['invoice_id']);
if ($invoice_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid invoice_id.']);
    exit;
}
include '../inc/db.php';
$res = $conn->query("SELECT c.phone, c.name, c.email FROM invoices i LEFT JOIN customers c ON i.customer_id = c.id WHERE i.id = $invoice_id LIMIT 1");
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    echo json_encode([
        'success' => true,
        'phone' => preg_replace('/[^0-9]/', '', $row['phone']),
        'name' => $row['name'],
        'email' => $row['email']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Customer not found.']);
}
$conn->close(); 