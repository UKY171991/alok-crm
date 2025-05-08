<?php
// ajax/upload_orders.php
header('Content-Type: application/json');
require_once '../vendor/autoload.php';
include '../inc/db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$fields = [
    'date','docket','location','destination','mode','no_of_pcs','pincode','content','dox_non_dox','material_value','fr_weight','valumatric','manual_weight','invoice_wt','round_off_weight','clinet_billing_value','credit_cust_amt','regular_cust_amt','customer_type','sender_detail','payment_status','sender_contact_no','address','adhaar_no','customer_attend_by','today_date','pending','td_delivery_status','td_delivery_date','t_receiver_name','receiver_contact_no','receiver_name_as_per_sendor','ref','complain_no_update','shipment_cost_by_other_mode','remarks','pod_status','pending_days'
];

if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'File upload failed.']);
    exit;
}

$tmpFile = $_FILES['excel_file']['tmp_name'];
try {
    // Set higher memory and execution time for large files
    ini_set('memory_limit', '512M');
    set_time_limit(300);

    $spreadsheet = IOFactory::load($tmpFile);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);
    if (count($rows) < 2) {
        echo json_encode(['success' => false, 'message' => 'Excel file is empty or missing data.']);
        $conn->close();
        exit;
    }
    $header = array_map('trim', array_values($rows[1]));
    unset($rows[1]);
    $inserted = 0;
    foreach ($rows as $row) {
        $data = [];
        foreach ($fields as $i => $field) {
            $data[$field] = isset($row[array_keys($row)[$i]]) ? $row[array_keys($row)[$i]] : null;
        }
        // Skip empty rows
        if (empty(array_filter($data, fn($v) => $v !== null && $v !== ''))) continue;
        $placeholders = implode(',', array_fill(0, count($fields), '?'));
        $stmt = $conn->prepare("INSERT INTO orders (".implode(",", $fields).") VALUES ($placeholders)");
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'DB error: ' . $conn->error]);
            $conn->close();
            exit;
        }
        $stmt->bind_param(str_repeat('s', count($fields)), ...array_values($data));
        if ($stmt->execute()) {
            $inserted++;
        }
        $stmt->close();
    }
    echo json_encode(['success' => true, 'message' => "$inserted orders imported successfully."]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Excel read error: ' . $e->getMessage()]);
}
$conn->close();
