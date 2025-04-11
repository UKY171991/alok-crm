<?php
include 'inc/db.php';

$sql = "SELECT * FROM invoices ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['invoice_no']}</td>
            <td>{$row['customer_id']}</td>
            <td>{$row['invoice_date']}</td>
            <td>{$row['total_amount']}</td>
            <td>{$row['gst_amount']}</td>
            <td>{$row['grand_total']}</td>
            <td>{$row['created_at']}</td>
            <td>
                <button class='btn btn-warning btn-sm edit-btn'
                    data-id='{$row['id']}'
                    data-invoice_no='{$row['invoice_no']}'
                    data-customer_id='{$row['customer_id']}'
                    data-invoice_date='{$row['invoice_date']}'
                    data-total_amount='{$row['total_amount']}'
                    data-gst_amount='{$row['gst_amount']}'
                    data-grand_total='{$row['grand_total']}'
                >Edit</button>
                <button class='btn btn-danger btn-sm delete-btn' data-id='{$row['id']}'>Delete</button>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='9' class='text-center'>No invoices found</td></tr>";
}

$conn->close();
