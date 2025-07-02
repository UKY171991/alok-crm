<?php
header('Content-Type: application/json');
if (!isset($_POST['invoice_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing invoice_id.']);
    exit;
}
$invoice_id = intval($_POST['invoice_id']);
if ($invoice_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid invoice_id.']);
    exit;
}

include '../inc/db.php';

// Fetch invoice
$invoice = null;
$customer = null;
$line_items = [];
$res = $conn->query("SELECT * FROM invoices WHERE id = $invoice_id LIMIT 1");
if ($res && $res->num_rows > 0) {
    $invoice = $res->fetch_assoc();
} else {
    echo json_encode(['success' => false, 'message' => 'Invoice not found.']);
    exit;
}
// Fetch customer
$customer_id = intval($invoice['customer_id']);
$res = $conn->query("SELECT * FROM customers WHERE id = $customer_id LIMIT 1");
if ($res && $res->num_rows > 0) {
    $customer = $res->fetch_assoc();
} else {
    echo json_encode(['success' => false, 'message' => 'Customer not found.']);
    exit;
}
// Fetch line items
$res = $conn->query("SELECT * FROM invoice_items WHERE invoice_id = $invoice_id ORDER BY booking_date, id");
while ($row = $res && $res->num_rows > 0 ? $res->fetch_assoc() : null) {
    $line_items[] = $row;
}

// Prepare invoice HTML (simple, reuse from invoice_details.php)
$subject = 'Your Invoice: ' . htmlspecialchars($invoice['invoice_no']);
$to = $customer['email'];
$from = 'no-reply@yourdomain.com';
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: Courier CRM <no-reply@yourdomain.com>\r\n";

$html = '<h2>Invoice Details</h2>';
$html .= '<table border="0" cellpadding="6" style="font-family:sans-serif;font-size:15px;">';
$html .= '<tr><td><b>Invoice No:</b></td><td>' . htmlspecialchars($invoice['invoice_no']) . '</td></tr>';
$html .= '<tr><td><b>Invoice Date:</b></td><td>' . htmlspecialchars($invoice['invoice_date']) . '</td></tr>';
$html .= '<tr><td><b>Customer:</b></td><td>' . htmlspecialchars($customer['name']) . '</td></tr>';
$html .= '<tr><td><b>Email:</b></td><td>' . htmlspecialchars($customer['email']) . '</td></tr>';
$html .= '<tr><td><b>Phone:</b></td><td>' . htmlspecialchars($customer['phone']) . '</td></tr>';
$html .= '</table>';
$html .= '<h3>Line Items</h3>';
$html .= '<table border="1" cellpadding="6" style="border-collapse:collapse;font-family:sans-serif;font-size:14px;">';
$html .= '<tr><th>Sr.</th><th>Booking Date</th><th>Consignment No.</th><th>Destination City</th><th>Dox/Non Dox</th><th>No of Pcs</th><th>Weight</th><th>Amt.</th><th>Way Bill Value</th></tr>';
foreach ($line_items as $idx => $item) {
    $html .= '<tr>';
    $html .= '<td>' . ($idx + 1) . '</td>';
    $html .= '<td>' . htmlspecialchars($item['booking_date']) . '</td>';
    $html .= '<td>' . htmlspecialchars($item['consignment_no']) . '</td>';
    $html .= '<td>' . htmlspecialchars($item['destination_city']) . '</td>';
    $html .= '<td>' . htmlspecialchars($item['dox_non_dox']) . '</td>';
    $html .= '<td>' . htmlspecialchars($item['quantity']) . '</td>';
    $html .= '<td>' . htmlspecialchars($item['weight']) . '</td>';
    $html .= '<td>' . htmlspecialchars($item['amt']) . '</td>';
    $html .= '<td>' . htmlspecialchars($item['way_bill_value']) . '</td>';
    $html .= '</tr>';
}
$html .= '</table>';

// Send email
if (mail($to, $subject, $html, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Invoice mailed to ' . htmlspecialchars($to)]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
}
$conn->close(); 