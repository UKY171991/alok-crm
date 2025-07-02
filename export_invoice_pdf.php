<?php
require_once __DIR__ . '/vendor/autoload.php'; // mPDF autoload
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include 'inc/db.php';

$invoice_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($invoice_id <= 0) {
    die('Invalid invoice ID.');
}

// Fetch invoice, customer, and line items (reuse logic from invoice_details.php)
$line_items = [];
$customer = null;
$invoice = null;
if ($invoice_id > 0) {
    $result = $conn->query("SELECT * FROM invoice_items WHERE invoice_id = $invoice_id ORDER BY booking_date, id");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $line_items[] = $row;
        }
    }
    $inv_result = $conn->query("SELECT * FROM invoices WHERE id = $invoice_id LIMIT 1");
    $invoice = ($inv_result && $row = $inv_result->fetch_assoc()) ? $row : null;
    $customer_id = $invoice ? intval($invoice['customer_id']) : 0;
    if ($customer_id > 0) {
        $cust_result = $conn->query("SELECT * FROM customers WHERE id = $customer_id LIMIT 1");
        if ($cust_result && $cust_row = $cust_result->fetch_assoc()) {
            $customer = $cust_row;
        }
    }
}

if (!$invoice) {
    die('Invoice not found.');
}

// Prepare HTML for PDF (mPDF-safe, clean layout)
$invoice_no = htmlspecialchars($invoice['invoice_no']);
$invoice_date = date('d-M-Y', strtotime($invoice['invoice_date']));
$period = isset($invoice['period']) ? htmlspecialchars($invoice['period']) : '';
$sac_code = isset($invoice['sac_code']) ? htmlspecialchars($invoice['sac_code']) : '---';

$html = '<style>
body { font-family: DejaVu Sans, Arial, sans-serif; color: #222; }
.outer-wrap { background: #fff; border: 1.5px solid #bbb; border-radius: 8px; margin: 18px 0; padding: 18px 24px; }
.header-bar { background: #000; color: #fff; padding: 18px 24px; font-size: 1.7em; font-weight: bold; border-radius: 8px 8px 0 0; letter-spacing: 1px; }
.company-table { width: 100%; margin-bottom: 10px; }
.company-table td { vertical-align: top; border: none; padding: 0; }
.company-info { font-size: 1em; }
.invoice-meta { font-size: 1em; text-align: right; }
.section { margin: 18px 0 10px 0; }
.to-section { background: #f6fafd; border: 1px solid #e3eefd; border-radius: 6px; padding: 14px 20px; margin-bottom: 18px; font-size: 1em; }
table.invoice-table { border-collapse: collapse; width: 100%; font-size: 0.98em; margin-top: 10px; }
table.invoice-table th, table.invoice-table td { border: 1px solid #b5b5b5; padding: 7px 8px; }
table.invoice-table th { background: #e3eefd; color: #222; font-weight: bold; }
table.invoice-table td.text-center { text-align: center; }
table.invoice-table td.text-end { text-align: right; }
tfoot td { background: #e3eefd; font-weight: bold; color: #0d6efd; }
tr:nth-child(even) td { background: #f9fbfd; }
.section-title { font-size: 1.1em; font-weight: bold; color: #0d6efd; margin-bottom: 6px; }
.footer { margin-top: 30px; text-align: center; color: #888; font-size: 0.95em; border-top: 1px solid #eee; padding-top: 10px; }
</style>';
$html .= '<div class="outer-wrap">';
$html .= '<div class="header-bar">Awdhoot Global Solutions</div>';
$html .= '<table class="company-table"><tr><td class="company-info" style="width:60%">'
    . '<strong>Awdhoot Global Solutions</strong><br>'
    . 'Shop No.: 570/326, VIP Road, Sainik Nagar,<br>'
    . 'Lucknow - 226002 - Uttar Pradesh<br>'
    . 'Phone: 8853099924<br>'
    . 'GST No: 09BLUPS9727E1ZT, Uttar Pradesh'
    . '</td><td class="invoice-meta" style="width:40%">'
    . '<table style="width:100%; border:none; font-size:1em;">
      <tr><td style="border:none;">Invoice No:</td><td style="border:none; font-weight:bold; color:#0d6efd;">' . $invoice_no . '</td></tr>
      <tr><td style="border:none;">Invoice Date:</td><td style="border:none; font-weight:bold;">' . $invoice_date . '</td></tr>
      <tr><td style="border:none;">Period:</td><td style="border:none;">' . $period . '</td></tr>
      <tr><td style="border:none;">SAC Code:</td><td style="border:none;">' . $sac_code . '</td></tr>
    </table>'
    . '</td></tr></table>';
$html .= '<div class="section to-section"><strong>To,</strong><br>';
if ($customer) {
    $html .= '<span style="font-size:1.1em; font-weight:bold;">' . htmlspecialchars($customer['name']) . '</span><br>';
    $html .= 'Address: ' . htmlspecialchars($customer['address']) . '<br>';
    $html .= 'Phone: ' . htmlspecialchars($customer['phone']) . '<br>';
    $html .= 'Email: ' . htmlspecialchars($customer['email']) . '<br>';
    $html .= 'GSTN No: ' . htmlspecialchars($customer['gst_no']) . '<br>';
} else {
    $html .= '<em>Customer details not found.</em>';
}
$html .= '</div>';

$html .= '<div class="section-title">Invoice Details</div>';
$html .= '<table class="invoice-table"><thead><tr>';
$html .= '<th>Sr.</th><th class="text-center">Order ID</th><th>Booking Date</th><th>Consignment No.</th><th>Destination City</th><th>Dox / Non Dox</th><th>Service</th><th class="text-end">No of Pcs</th><th class="text-end">Weight or No</th><th class="text-end">Amt.</th><th class="text-end">Way Bill Value</th>';
$html .= '</tr></thead><tbody>';
$total_amt = $total_waybill = $total_pcs = $total_weight = 0;
foreach ($line_items as $idx => $item) {
    $total_amt += floatval($item['amt']);
    $total_waybill += floatval($item['way_bill_value']);
    $total_pcs += isset($item['quantity']) ? floatval($item['quantity']) : 0;
    $total_weight += isset($item['weight']) ? floatval($item['weight']) : 0;
    $html .= '<tr>';
    $html .= '<td>' . ($idx + 1) . '</td>';
    $html .= '<td class="text-center">' . (isset($item['order_id']) ? htmlspecialchars($item['order_id']) : '') . '</td>';
    $html .= '<td>' . (!empty($item['booking_date']) && $item['booking_date'] != '0000-00-00' ? date('d-m-Y', strtotime($item['booking_date'])) : '–') . '</td>';
    $html .= '<td>' . htmlspecialchars($item['consignment_no']) . '</td>';
    $html .= '<td>' . htmlspecialchars($item['destination_city']) . '</td>';
    $html .= '<td>' . htmlspecialchars($item['dox_non_dox']) . '</td>';
    $html .= '<td>' . htmlspecialchars($item['service']) . '</td>';
    $html .= '<td class="text-end">' . htmlspecialchars($item['quantity']) . '</td>';
    $html .= '<td class="text-end">' . htmlspecialchars($item['weight']) . '</td>';
    $html .= '<td class="text-end">' . number_format($item['amt'], 2) . '</td>';
    $html .= '<td class="text-end">' . number_format($item['way_bill_value'], 2) . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody>';
$html .= '<tfoot><tr>';
$html .= '<td colspan="7" class="text-end">Totals:</td>';
$html .= '<td class="text-end" style="color:#0d6efd;">' . number_format($total_pcs, 2) . '</td>';
$html .= '<td class="text-end" style="color:#0d6efd;">' . number_format($total_weight, 2) . '</td>';
$html .= '<td class="text-end" style="color:#0d6efd;">' . number_format($total_amt, 2) . '</td>';
$html .= '<td class="text-end" style="color:#0d6efd;">' . number_format($total_waybill, 2) . '</td>';
$html .= '</tr></tfoot></table>';

$html .= '<div class="footer">Thank you for your business!<br>For queries, contact: 8853099924 | info@awadhootglobalsolutions.com</div>';
$html .= '</div>';

// Output PDF
$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
$mpdf->SetTitle('Invoice ' . $invoice_no);
$mpdf->WriteHTML($html);
$mpdf->Output('Invoice_' . $invoice_no . '.pdf', 'D');
exit; 