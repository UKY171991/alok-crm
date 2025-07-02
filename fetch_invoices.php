<?php
include 'inc/db.php';


// Fetch customer names for all invoices in one query
$sql = "SELECT i.id, i.invoice_no, i.customer_id, c.name AS customer_name, i.invoice_date, i.destination, i.total_amount, i.gst_amount, i.grand_total, i.created_at
        FROM invoices i
        LEFT JOIN customers c ON i.customer_id = c.id
        ORDER BY i.id DESC";
// Debug: output the SQL query as an HTML comment
echo "<!-- SQL: $sql -->\n";
$result = $conn->query($sql);
if (!$result) {
    // Debug: output the error as an HTML comment
    echo "<!-- SQL Error: " . $conn->error . " -->\n";
    die("Query failed: " . $conn->error);
}

if ($result && $result->num_rows > 0) {
    // Debug: output the number of rows as an HTML comment
    echo "<!-- Rows: {$result->num_rows} -->\n";
    $serial = 1;
    while ($row = $result->fetch_assoc()) {
        // Fetch the first line item for this invoice
        $item_sql = "SELECT order_id, booking_date, consignment_no, destination_city, dox_non_dox, service, quantity, weight, amt, way_bill_value FROM invoice_items WHERE invoice_id = {$row['id']} ORDER BY id ASC LIMIT 1";
        $item_result = $conn->query($item_sql);
        $item = $item_result && $item_result->num_rows > 0 ? $item_result->fetch_assoc() : null;
        echo "<tr>
            <td>" . $serial++ . "</td>
            <td>" . htmlspecialchars($row['invoice_no']) . "</td>
            <td>" . htmlspecialchars($row['customer_name'] ?? $row['customer_id']) . "</td>
            <td>" . htmlspecialchars($row['invoice_date']) . "</td>
            <td>" . ($item ? htmlspecialchars($item['booking_date']) : '') . "</td>
            <td>" . ($item ? htmlspecialchars($item['consignment_no']) : '') . "</td>
            <td>" . ($item ? htmlspecialchars($item['destination_city']) : '') . "</td>
            <td>" . ($item ? htmlspecialchars($item['dox_non_dox']) : '') . "</td>
            <td>" . ($item ? htmlspecialchars($item['quantity']) : '') . "</td>
            <td>" . ($item ? htmlspecialchars($item['weight']) : '') . "</td>
            <td>" . ($item ? htmlspecialchars($item['amt']) : '') . "</td>
            <td>" . ($item ? htmlspecialchars($item['way_bill_value']) : '') . "</td>
            <td>
                <button class='btn btn-info btn-sm view-btn' data-id='" . htmlspecialchars($row['id']) . "'>View</button>\n" .
                "<button class='btn btn-warning btn-sm edit-btn'
                    data-id='" . htmlspecialchars($row['id']) . "'
                    data-invoice_no='" . htmlspecialchars($row['invoice_no']) . "'
                    data-customer_id='" . htmlspecialchars($row['customer_id']) . "'
                    data-invoice_date='" . htmlspecialchars($row['invoice_date']) . "'
                    data-destination='" . htmlspecialchars($row['destination']) . "'
                    data-total_amount='" . htmlspecialchars($row['total_amount']) . "'
                    data-gst_amount='" . htmlspecialchars($row['gst_amount']) . "'
                    data-grand_total='" . htmlspecialchars($row['grand_total']) . "'
                >Edit</button>
                <button class='btn btn-danger btn-sm delete-btn' data-id='" . htmlspecialchars($row['id']) . "'>Delete</button>
            </td>
        </tr>";
    }
} else {
    // Debug: output a message in the table
    echo "<tr><td colspan='10' class='text-center'>No invoices found<!-- Debug: No rows returned --></td></tr>";
}

$conn->close();
