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

    // Write CSV headers with all fields
    fputcsv($output, [
        'Invoice ID',
        'Invoice Number',
        'Invoice Date',
        'Customer ID',
        'Customer Name',
        'Customer Email',
        'Customer Phone',
        'Customer Address',
        'Customer GST No',
        'Customer HSN Code',
        'Customer PAN No',
        'Customer CIN No',
        'Customer Aadhaar No',
        'Total Amount',
        'GST Amount',
        'Grand Total',
        'Status',
        'Created At',
        'Updated At'
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

    // Build the SQL query with all customer information
    $sql = "SELECT 
                i.id,
                i.invoice_no,
                i.invoice_date,
                i.customer_id,
                c.name as customer_name,
                c.email as customer_email,
                c.phone as customer_phone,
                c.address as customer_address,
                c.gst_no as customer_gst,
                c.hsn_code as customer_hsn,
                c.pan_no as customer_pan,
                c.cin_no as customer_cin,
                c.aadhaar_no as customer_aadhaar,
                i.total_amount,
                i.gst_amount,
                i.grand_total,
                i.status,
                i.created_at,
                i.updated_at
            FROM invoices i 
            LEFT JOIN customers c ON i.customer_id = c.id";

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY i.invoice_date DESC";

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
                $row['id'],
                $row['invoice_no'],
                $row['invoice_date'],
                $row['customer_id'],
                $row['customer_name'] ?? 'N/A',
                $row['customer_email'] ?? 'N/A',
                $row['customer_phone'] ?? 'N/A',
                $row['customer_address'] ?? 'N/A',
                $row['customer_gst'] ?? 'N/A',
                $row['customer_hsn'] ?? 'N/A',
                $row['customer_pan'] ?? 'N/A',
                $row['customer_cin'] ?? 'N/A',
                $row['customer_aadhaar'] ?? 'N/A',
                $row['total_amount'],
                $row['gst_amount'],
                $row['grand_total'],
                $row['status'] ?? 'pending',
                $row['created_at'],
                $row['updated_at']
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