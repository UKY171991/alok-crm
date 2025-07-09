<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once 'inc/config.php';
require_once 'inc/db.php';

$invoiceId = $_GET['id'] ?? '';

if (empty($invoiceId)) {
    echo "Invalid invoice ID";
    exit;
}

try {
    // Fetch invoice details
    $invoiceSql = "SELECT i.*, c.name as customer_name, c.email, c.phone, c.address, c.gst_no 
                   FROM invoices i 
                   LEFT JOIN customers c ON i.customer_id = c.id 
                   WHERE i.id = ?";
    
    $invoiceStmt = $conn->prepare($invoiceSql);
    $invoiceStmt->bind_param('i', $invoiceId);
    $invoiceStmt->execute();
    $invoiceResult = $invoiceStmt->get_result();
    
    if ($invoiceResult->num_rows === 0) {
        echo "Invoice not found";
        exit;
    }
    
    $invoice = $invoiceResult->fetch_assoc();
    
    // Fetch invoice items
    $itemsSql = "SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id";
    $itemsStmt = $conn->prepare($itemsSql);
    $itemsStmt->bind_param('i', $invoiceId);
    $itemsStmt->execute();
    $itemsResult = $itemsStmt->get_result();
    
    $items = [];
    while ($item = $itemsResult->fetch_assoc()) {
        $items[] = $item;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?= htmlspecialchars($invoice['invoice_no']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .invoice-header {
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-info {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .company-info h1 {
            color: #3b82f6;
            margin: 0;
            font-size: 28px;
        }
        
        .company-info p {
            color: #666;
            margin: 5px 0;
        }
        
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .invoice-details div {
            flex: 1;
        }
        
        .invoice-details h3 {
            color: #374151;
            margin-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .invoice-table th {
            background: #3b82f6;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        .invoice-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .invoice-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .invoice-summary {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .summary-row.total {
            font-weight: bold;
            font-size: 18px;
            color: #3b82f6;
            border-bottom: 3px solid #3b82f6;
        }
        
        .invoice-footer {
            margin-top: 50px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
        }
        
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            body {
                background: white;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn btn-primary" onclick="window.print()">Print Invoice</button>
        <a href="generate_invoice.php" class="btn btn-secondary">Back to Invoices</a>
    </div>

    <div class="invoice-container">
        <div class="invoice-header">
            <div class="company-info">
                <h1>Courier CRM</h1>
                <p>Professional Courier & Logistics Services</p>
                <p>Email: info@couriercrm.com | Phone: +91-1234567890</p>
            </div>
        </div>

        <div class="invoice-details">
            <div>
                <h3>Bill To:</h3>
                <p><strong><?= htmlspecialchars($invoice['customer_name']) ?></strong></p>
                <?php if (!empty($invoice['address'])): ?>
                    <p><?= htmlspecialchars($invoice['address']) ?></p>
                <?php endif; ?>
                <?php if (!empty($invoice['email'])): ?>
                    <p>Email: <?= htmlspecialchars($invoice['email']) ?></p>
                <?php endif; ?>
                <?php if (!empty($invoice['phone'])): ?>
                    <p>Phone: <?= htmlspecialchars($invoice['phone']) ?></p>
                <?php endif; ?>
                <?php if (!empty($invoice['gst_no'])): ?>
                    <p>GST No: <?= htmlspecialchars($invoice['gst_no']) ?></p>
                <?php endif; ?>
            </div>
            
            <div>
                <h3>Invoice Details:</h3>
                <p><strong>Invoice No:</strong> <?= htmlspecialchars($invoice['invoice_no']) ?></p>
                <p><strong>Invoice Date:</strong> <?= date('d M Y', strtotime($invoice['invoice_date'])) ?></p>
                <?php if (!empty($invoice['from_date']) && !empty($invoice['to_date'])): ?>
                    <p><strong>Service Period:</strong> <?= date('d M Y', strtotime($invoice['from_date'])) ?> to <?= date('d M Y', strtotime($invoice['to_date'])) ?></p>
                <?php endif; ?>
                <p><strong>Status:</strong> <span style="text-transform: capitalize; color: <?= $invoice['status'] === 'paid' ? '#10b981' : ($invoice['status'] === 'cancelled' ? '#ef4444' : '#f59e0b') ?>"><?= htmlspecialchars($invoice['status']) ?></span></p>
            </div>
        </div>

        <?php if (!empty($items)): ?>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Sr.</th>
                    <th>Booking Date</th>
                    <th>Consignment No</th>
                    <th>Destination</th>
                    <th>Service</th>
                    <th>Weight</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= !empty($item['booking_date']) ? date('d M Y', strtotime($item['booking_date'])) : '-' ?></td>
                    <td><?= htmlspecialchars($item['consignment_no'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($item['destination'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($item['service'] ?? '-') ?></td>
                    <td><?= !empty($item['weight']) ? number_format($item['weight'], 2) . ' kg' : '-' ?></td>
                    <td>₹<?= number_format($item['rate'] ?? 0, 2) ?></td>
                    <td>₹<?= number_format($item['amount'] ?? 0, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <div class="invoice-summary">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>₹<?= number_format($invoice['total_amount'], 2) ?></span>
            </div>
            <div class="summary-row">
                <span>GST (18%):</span>
                <span>₹<?= number_format($invoice['gst_amount'], 2) ?></span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span>₹<?= number_format($invoice['grand_total'], 2) ?></span>
            </div>
        </div>

        <div style="clear: both;"></div>

        <div class="invoice-footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>This is a computer-generated invoice. No signature required.</p>
            <p>For any queries, please contact us at info@couriercrm.com</p>
        </div>
    </div>
</body>
</html>
