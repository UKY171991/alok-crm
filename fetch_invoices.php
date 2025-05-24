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
        echo "<tr>
            <td>" . $serial++ . "</td>
            <td>" . htmlspecialchars($row['invoice_no']) . "</td>
            <td>" . htmlspecialchars($row['customer_name'] ?? $row['customer_id']) . "</td>
            <td>" . htmlspecialchars($row['invoice_date']) . "</td>
            <!--<td>" . htmlspecialchars($row['destination']) . "</td>-->
            <td>" . htmlspecialchars($row['total_amount']) . "</td>
            <td>" . htmlspecialchars($row['gst_amount']) . "</td>
            <td>" . htmlspecialchars($row['grand_total']) . "</td>
            <td>" . htmlspecialchars($row['created_at']) . "</td>
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
