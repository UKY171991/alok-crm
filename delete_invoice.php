<?php
include 'inc/db.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // First delete related invoice items
    $conn->query("DELETE FROM invoice_items WHERE invoice_id = $id");

    // Then delete invoice
    $sql = "DELETE FROM invoices WHERE id = $id";
    if ($conn->query($sql)) {
        echo "Deleted successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
