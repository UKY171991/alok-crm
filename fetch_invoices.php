<?php
include 'inc/db.php';


// Fetch customer names for all invoices in one query
$sql = "SELECT i.*, c.name AS customer_name FROM invoices i LEFT JOIN customers c ON i.customer_id = c.id ORDER BY i.id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>" . htmlspecialchars($row['invoice_no']) . "</td>
            <td>" . htmlspecialchars($row['customer_name'] ?? $row['customer_id']) . "</td>
            <td>" . htmlspecialchars($row['invoice_date']) . "</td>
            <td>" . htmlspecialchars($row['destination']) . "</td>
            <td>" . htmlspecialchars($row['total_amount']) . "</td>
            <td>" . htmlspecialchars($row['gst_amount']) . "</td>
            <td>" . htmlspecialchars($row['grand_total']) . "</td>
            <td>" . htmlspecialchars($row['created_at']) . "</td>
            <td>
                <button class='btn btn-warning btn-sm edit-btn'
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
    echo "<tr><td colspan='10' class='text-center'>No invoices found</td></tr>";
}

$conn->close();
