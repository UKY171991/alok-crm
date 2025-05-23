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
    $result = $conn->query("SELECT * FROM invoice_items WHERE invoice_id = $invoice_id AND (booking_date IS NOT NULL AND booking_date != '' AND consignment_no IS NOT NULL AND consignment_no != '' AND destination_city IS NOT NULL AND destination_city != '' AND weight IS NOT NULL AND amt IS NOT NULL AND way_bill_value IS NOT NULL) ORDER BY booking_date, id");
    while ($row = $result && $result->fetch_assoc()) {
        $line_items[] = $row;
    }
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
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Sr.</th>
                                <th>Booking Date</th>
                                <th>Consignment No.</th>
                                <th>Destination City</th>
                                <th>Weight or N</th>
                                <th>Amt.</th>
                                <th>Way Bill Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($line_items)): ?>
                                <?php foreach ($line_items as $idx => $item): ?>
                                    <tr>
                                        <td><?= $idx + 1 ?></td>
                                        <td><?= !empty($item['booking_date']) ? htmlspecialchars($item['booking_date']) : '–' ?></td>
                                        <td><?= !empty($item['consignment_no']) ? htmlspecialchars($item['consignment_no']) : '–' ?></td>
                                        <td><?= !empty($item['destination_city']) ? htmlspecialchars($item['destination_city']) : '–' ?></td>
                                        <td><?= !empty($item['weight']) ? htmlspecialchars($item['weight']) : '–' ?></td>
                                        <td><?= !empty($item['amt']) ? htmlspecialchars($item['amt']) : '–' ?></td>
                                        <td><?= !empty($item['way_bill_value']) ? htmlspecialchars($item['way_bill_value']) : '–' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center">No line items found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'inc/footer.php'; ?>
