<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include 'inc/db.php';
include 'inc/header.php';
include 'inc/sidebar.php';
?>

<main class="content-wrapper">
    <div class="container-fluid p-3">
        <h2>Invoice Reports</h2>
        <p>Search past invoices by multiple filters.</p>

        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Filter Invoices</h3>
            </div>
            <div class="card-body">
                <form id="invoiceReportForm">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Customer ID</label>
                            <input type="text" name="customer_id" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Destination</label>
                            <input type="text" name="destination" class="form-control">
                        </div>
                        <div class="col-md-3 mt-2">
                            <label>Invoice ID</label>
                            <input type="text" name="invoice_id" class="form-control">
                        </div>
                        <div class="col-md-3 mt-4">
                            <button type="submit" class="btn btn-primary mt-2">Search</button>
                            <button type="button" id="downloadReport" class="btn btn-success mt-2">
                                <i class="fas fa-download"></i> Download CSV
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Invoice Report Results</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Invoice No</th>
                                <th>Customer ID</th>
                                <th>Customer Name</th>
                                <th>Invoice Date</th>
                                <th>Total Amount</th>
                                <th>GST</th>
                                <th>Grand Total</th>
                                <th>Status</th>
                                <th>Destination</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <tr><td colspan="11" class="text-center">Please search to view results.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'inc/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script>
$(function () {
    // Handle search form submission
    $('#invoiceReportForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'fetch_invoice_reports.php',
            type: 'GET',
            data: $(this).serialize(),
            success: function (data) {
                $('#reportTableBody').html(data);
            }
        });
    });

    // Handle download button click
    $('#downloadReport').on('click', function() {
        // Get the current form data
        var formData = $('#invoiceReportForm').serialize();
        // Open download URL in new window
        window.open('download_report.php?' + formData, '_blank');
    });
});
</script>
