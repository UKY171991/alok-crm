<?php
include 'inc/db.php';

$where = [];

if (!empty($_GET['start_date'])) {
    $start_date = $conn->real_escape_string($_GET['start_date']);
    $where[] = "i.invoice_date >= '$start_date'";
}
if (!empty($_GET['end_date'])) {
    $end_date = $conn->real_escape_string($_GET['end_date']);
    $where[] = "i.invoice_date <= '$end_date'";
}
if (!empty($_GET['customer_id'])) {
    $customer_id = intval($_GET['customer_id']);
    $where[] = "i.customer_id = $customer_id";
}
if (!empty($_GET['invoice_id'])) {
    $invoice_id = intval($_GET['invoice_id']);
    $where[] = "i.id = $invoice_id";
}

// Modified SQL with JOIN to get customer name
$sql = "SELECT i.*, c.name as customer_name 
        FROM invoices i 
        LEFT JOIN customers c ON i.customer_id = c.id";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY i.invoice_date DESC";

$result = $conn->query($sql);

if (!$result) {
    echo "<tr><td colspan='10' class='text-danger'>SQL Error: " . $conn->error . "</td></tr>";
    exit;
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $status = isset($row['status']) ? $row['status'] : 'pending'; // Default to pending if status is null
        $destination = isset($row['destination']) ? $row['destination'] : 'N/A'; // Default to N/A if destination is null
        $customer_name = isset($row['customer_name']) ? htmlspecialchars($row['customer_name']) : 'N/A'; // Default to N/A if customer name is null
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['invoice_no']}</td>
            <td>{$row['customer_id']}</td>
            <td>{$customer_name}</td>
            <td>{$row['invoice_date']}</td>
            <td>{$destination}</td>
            <td>₹{$row['total_amount']}</td>
            <td>₹{$row['gst_amount']}</td>
            <td>₹{$row['grand_total']}</td>
            <td>" . ucfirst($status) . "</td>
            <td>{$row['created_at']}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='10' class='text-center'>No matching invoices found.</td></tr>";
}

$conn->close();
