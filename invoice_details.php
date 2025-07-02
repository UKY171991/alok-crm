<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include 'inc/db.php';
include 'inc/header.php';
include 'inc/sidebar.php';

// Fetch invoice ID from GET or another source
$invoice_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch invoice line items and customer details
$line_items = [];
$customer = null;
if ($invoice_id > 0) {
    $result = $conn->query("SELECT * FROM invoice_items WHERE invoice_id = $invoice_id ORDER BY booking_date, id");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $line_items[] = $row;
        }
    }
    // Fetch customer_id from the invoice
    $inv_result = $conn->query("SELECT customer_id FROM invoices WHERE id = $invoice_id LIMIT 1");
    $customer_id = ($inv_result && $row = $inv_result->fetch_assoc()) ? intval($row['customer_id']) : 0;
    if ($customer_id > 0) {
        $cust_result = $conn->query("SELECT * FROM customers WHERE id = $customer_id LIMIT 1");
        if ($cust_result && $cust_row = $cust_result->fetch_assoc()) {
            $customer = $cust_row;
        }
    }
    error_log('Fetched line_items: ' . print_r($line_items, true));
}

?>

<main class="content-wrapper">
    <div class="container-fluid p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Invoice Details</h2>
        </div>

        <!-- Invoice Header -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Awdhoot Global Solutions</h5>
                        <p>Shop No.: 570/326, VIP Road, Sainik Nagar,<br>
                        Lucknow - 226002 - Uttar Pradesh<br>
                        Phone: 8853099924<br>
                        GST No: 09BLUPS9727E1ZT, Uttar Pradesh</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p>Invoice No: <strong>AG525-26/783</strong></p>
                        <p>Invoice Date: <strong>30-Apr-25</strong></p>
                        <p>Period: <strong>1 Apr 25 to 30 Apr 25</strong></p>
                        <p>SAC Code: <strong>---</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="card mb-3">
            <div class="card-body">
                <h5>To,</h5>
                <?php if ($customer): ?>
                <p>
                    <?= htmlspecialchars($customer['name']) ?><br>
                    Address: <?= htmlspecialchars($customer['address']) ?><br>
                    Phone: <?= htmlspecialchars($customer['phone']) ?><br>
                    Email: <?= htmlspecialchars($customer['email']) ?><br>
                    GSTN No: <?= htmlspecialchars($customer['gst_no']) ?>
                </p>
                <?php else: ?>
                <p><em>Customer details not found.</em></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Invoice Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Sr.</th>
                                <th class="text-center">Order ID</th>
                                <th>Booking Date</th>
                                <th>Consignment No.</th>
                                <th>Destination City</th>
                                <th>Dox / Non Dox</th>
                                <th>Service</th>
                                <th class="text-end">No of Pcs</th>
                                <th class="text-end">Weight or No</th>
                                <th class="text-end">Amt.</th>
                                <th class="text-end">Way Bill Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_amt = $total_waybill = $total_pcs = $total_weight = 0;
                            if (!empty($line_items)):
                                foreach ($line_items as $idx => $item):
                                    $total_amt += floatval($item['amt']);
                                    $total_waybill += floatval($item['way_bill_value']);
                                    $total_pcs += isset($item['quantity']) ? floatval($item['quantity']) : 0;
                                    $total_weight += isset($item['weight']) ? floatval($item['weight']) : 0;
                            ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td class="text-center"><?= isset($item['order_id']) ? htmlspecialchars($item['order_id']) : '' ?></td>
                                <td><?= !empty($item['booking_date']) && $item['booking_date'] != '0000-00-00' ? date('d-m-Y', strtotime($item['booking_date'])) : '–' ?></td>
                                <td><?= !empty($item['consignment_no']) ? htmlspecialchars($item['consignment_no']) : '–' ?></td>
                                <td><?= !empty($item['destination_city']) ? htmlspecialchars($item['destination_city']) : '–' ?></td>
                                <td><?= !empty($item['dox_non_dox']) ? htmlspecialchars($item['dox_non_dox']) : '–' ?></td>
                                <td><?= !empty($item['service']) ? htmlspecialchars($item['service']) : '–' ?></td>
                                <td class="text-end"><?= isset($item['quantity']) ? htmlspecialchars($item['quantity']) : '–' ?></td>
                                <td class="text-end"><?= isset($item['weight']) ? htmlspecialchars($item['weight']) : '–' ?></td>
                                <td class="text-end"><?= isset($item['amt']) ? number_format($item['amt'], 2) : '–' ?></td>
                                <td class="text-end"><?= isset($item['way_bill_value']) ? number_format($item['way_bill_value'], 2) : '–' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary fw-bold">
                                <td colspan="7" class="text-end">Totals:</td>
                                <td class="text-end text-primary"><?= number_format($total_pcs, 2) ?></td>
                                <td class="text-end text-primary"><?= number_format($total_weight, 2) ?></td>
                                <td class="text-end text-primary"><?= number_format($total_amt, 2) ?></td>
                                <td class="text-end text-primary"><?= number_format($total_waybill, 2) ?></td>
                            </tr>
                        </tfoot>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center">No line items found.</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'inc/footer.php'; ?>
