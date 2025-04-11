<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include 'inc/db.php';

    // Verify database connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="invoice_report_' . date('Y-m-d') . '.csv"');

    // Create output stream
    $output = fopen('php://output', 'w');
    if ($output === false) {
        throw new Exception("Failed to create output stream");
    }

    // Write CSV headers
    fputcsv($output, [
        'ID',
        'Invoice No',
        'Customer',
        'Invoice Date',
        'Total Amount',
        'GST Amount',
        'Grand Total',
        'Status'
    ]);

    // Build query based on filters
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

    // Debug: Log the constructed SQL query
    error_log("Query parameters: " . print_r($_GET, true));

    // Build the SQL query with customer information
    $sql = "SELECT 
                i.id,
                i.invoice_number as invoice_no,
                c.name as customer_name,
                DATE_FORMAT(i.date, '%Y-%m-%d') as invoice_date,
                i.total_amount,
                i.gst_amount,
                (i.total_amount + COALESCE(i.gst_amount, 0)) as grand_total,
                i.status
            FROM invoices i 
            LEFT JOIN customers c ON i.customer_id = c.id";

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY i.date DESC";

    // Debug: Log the final SQL query
    error_log("Executing SQL query: " . $sql);

    $result = $conn->query($sql);

    if ($result === false) {
        throw new Exception("Query failed: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Debug: Log each row
            error_log("Processing row: " . print_r($row, true));
            
            $data = [
                $row['id'] ?? 'N/A',
                $row['invoice_no'] ?? 'N/A',
                $row['customer_name'] ?? 'Unknown Customer',
                $row['invoice_date'] ?? 'N/A',
                number_format($row['total_amount'] ?? 0, 2),
                number_format($row['gst_amount'] ?? 0, 2),
                number_format($row['grand_total'] ?? 0, 2),
                $row['status'] ?? 'Unknown'
            ];
            fputcsv($output, $data);
        }
    } else {
        fputcsv($output, ['No records found in the database']);
        error_log("No records found in query result");
    }

    fclose($output);
    $conn->close();

} catch (Exception $e) {
    error_log("Error in download_report.php: " . $e->getMessage());
    
    if (!headers_sent()) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="invoice_report_error_' . date('Y-m-d') . '.csv"');
    }
    
    $error_output = fopen('php://output', 'w');
    fputcsv($error_output, ['Error occurred while generating report']);
    fputcsv($error_output, ['Error details: ' . $e->getMessage()]);
    fclose($error_output);
}
?> 