<?php
include 'inc/db.php';

$where = [];

if (!empty($_GET['start_date'])) {
    $start_date = $conn->real_escape_string($_GET['start_date']);
    $where[] = "invoice_date >= '$start_date'";
}
if (!empty($_GET['end_date'])) {
    $end_date = $conn->real_escape_string($_GET['end_date']);
    $where[] = "invoice_date <= '$end_date'";
}
if (!empty($_GET['customer_id'])) {
    $customer_id = intval($_GET['customer_id']);
    $where[] = "customer_id = $customer_id";
}
if (!empty($_GET['invoice_id'])) {
    $invoice_id = intval($_GET['invoice_id']);
    $where[] = "id = $invoice_id";
}

// Final SQL
$sql = "SELECT * FROM invoices";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY invoice_date DESC";

$result = $conn->query($sql);

if (!$result) {
    echo "<tr><td colspan='9' class='text-danger'>SQL Error: " . $conn->error . "</td></tr>";
    exit;
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['invoice_no']}</td>
            <td>{$row['customer_id']}</td>
            <td>-</td> <!-- Destination placeholder --> 
            <td>{$row['invoice_date']}</td>
            <td>₹" . number_format($row['total_amount'], 2) . "</td>
            <td>₹" . number_format($row['gst_amount'], 2) . "</td>
            <td>₹" . number_format($row['grand_total'], 2) . "</td>
            <td>{$row['created_at']}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='9' class='text-center'>No matching invoices found.</td></tr>";
}

$conn->close();
