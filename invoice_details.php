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

// Fetch invoice line items
$line_items = [];
if ($invoice_id > 0) {
    $result = $conn->query("SELECT * FROM invoice_items WHERE invoice_id = $invoice_id ORDER BY booking_date, id");
    while ($row = $result && $result->fetch_assoc()) {
        $line_items[] = $row;
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
                <p>Aronva Healthcare<br>
                Address: Ashiyana, Lucknow - 226012<br>
                Phone: ---<br>
                GSTN No: 09BQIPS8917H1ZS</p>
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
                                <th>Booking Date</th>
                                <th>Consignment No.</th>
                                <th>Destination</th>
                                <th class="text-end">Weight or N</th>
                                <th class="text-end">Amt.</th>
                                <th class="text-end">Way Bill Value</th>
                                <th>Description</th>
                                <th class="text-end">Quantity</th>
                                <th class="text-end">Rate</th>
                                <th class="text-end text-primary">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_amt = $total_waybill = $total_amount = 0;
                            if (!empty($line_items)):
                                foreach ($line_items as $idx => $item):
                                    $total_amt += floatval($item['amt']);
                                    $total_waybill += floatval($item['way_bill_value']);
                                    $total_amount += floatval($item['amount']);
                            ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td><?= !empty($item['booking_date']) && $item['booking_date'] != '0000-00-00' ? date('d-m-Y', strtotime($item['booking_date'])) : '–' ?></td>
                                    <td><?= !empty($item['consignment_no']) ? htmlspecialchars($item['consignment_no']) : '–' ?></td>
                                    <td><?= !empty($item['destination_city']) ? htmlspecialchars($item['destination_city']) : '–' ?></td>
                                    <td class="text-end"><?= $item['weight'] !== null ? htmlspecialchars($item['weight']) : '–' ?></td>
                                    <td class="text-end"><?= $item['amt'] !== null ? number_format($item['amt'], 2) : '–' ?></td>
                                    <td class="text-end"><?= $item['way_bill_value'] !== null ? number_format($item['way_bill_value'], 2) : '–' ?></td>
                                    <td><?= !empty($item['description']) ? htmlspecialchars($item['description']) : '–' ?></td>
                                    <td class="text-end"><?= $item['quantity'] !== null ? htmlspecialchars($item['quantity']) : '–' ?></td>
                                    <td class="text-end"><?= $item['rate'] !== null ? number_format($item['rate'], 2) : '–' ?></td>
                                    <td class="text-end text-primary fw-bold"><?= $item['amount'] !== null ? number_format($item['amount'], 2) : '–' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary fw-bold">
                                <td colspan="5" class="text-end">Totals:</td>
                                <td class="text-end"><?= number_format($total_amt, 2) ?></td>
                                <td class="text-end"><?= number_format($total_waybill, 2) ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-end text-primary"><?= number_format($total_amount, 2) ?></td>
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
