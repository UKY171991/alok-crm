<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include 'inc/db.php';
include 'inc/header.php';
include 'inc/sidebar.php';

// Ensure database connection is valid
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$invoice_no = generateInvoiceNo($conn);
?>

<main class="content-wrapper">
    <div class="container-fluid p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Invoices Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#invoiceModal"><i class="fas fa-plus"></i> Add Invoice</button>
        </div>

        <!-- Add/Edit Invoice Modal -->
        <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="invoiceModalLabel">Add New Invoice</h5>
                        <button type="button" class="btn btn-danger btn-sm ms-auto" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                    <form id="invoiceForm">
                        <div class="modal-body p-4">
                            <div class="row g-3 mb-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Customer</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <select name="customer_id" id="customer_id" class="form-select" required>
                                            <option value="">Select Customer</option>
                                            <?php
                                            $query = "SELECT id, name FROM customers ORDER BY name ASC";
                                            $result = $conn->query($query);
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-3">
                            <h5 class="fw-bold mb-3 text-primary">Invoice Line Items</h5>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered align-middle" id="lineItemsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Booking Date</th>
                                            <th>Consignment No.</th>
                                            <th>Destination</th>
                                            <th>Weight or N</th>
                                            <th>Amt.</th>
                                            <th>Way Bill Value</th>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="date" name="line_items[0][booking_date]" class="form-control" required></td>
                                            <td><input type="text" name="line_items[0][consignment_no]" class="form-control" required></td>
                                            <td>
                                                <select name="line_items[0][destination_city]" class="form-select" required>
                                                    <option value="">Select Destination</option>
                                                    <?php
                                                    $query = "SELECT name FROM destinations ORDER BY name ASC";
                                                    $result = $conn->query($query);
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="number" step="0.001" name="line_items[0][weight]" class="form-control" required></td>
                                            <td><input type="number" step="0.01" name="line_items[0][amt]" class="form-control"></td>
                                            <td><input type="number" step="0.01" name="line_items[0][way_bill_value]" class="form-control"></td>
                                            <td><input type="text" name="line_items[0][description]" class="form-control"></td>
                                            <td><input type="number" name="line_items[0][quantity]" class="form-control"></td>
                                            <td><input type="number" step="0.01" name="line_items[0][rate]" class="form-control"></td>
                                            <td><input type="number" step="0.01" name="line_items[0][amount]" class="form-control"></td>
                                            <td><button type="button" class="btn btn-outline-danger btn-sm remove-row">Remove</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-success btn-sm" id="addRowBtn"><i class="fas fa-plus"></i> Add Row</button>
                            </div>
                            <hr class="my-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Total Amount</label>
                                    <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">GST Amount</label>
                                    <input type="number" step="0.01" name="gst_amount" id="gst_amount" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Grand Total</label>
                                    <input type="number" step="0.01" name="grand_total" id="grand_total" class="form-control" required>
                                </div>
                            </div>
                            <div id="message" class="mt-3"></div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Add Invoice</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Invoices List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Invoices List</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th><th>Invoice No</th><th>Customer ID</th><th>Invoice Date</th>
                                <!--<th>Destination</th>--><th>Total Amount</th><th>GST Amount</th><th>Grand Total</th><th>Created</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceTableBody">
                            <tr id="fallbackRow"><td colspan="10" class="text-center">Loading or no data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Invoice Details Modal -->
<div class="modal fade" id="viewInvoiceModal" tabindex="-1" aria-labelledby="viewInvoiceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewInvoiceModalLabel">Invoice Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="viewInvoiceModalBody">
        <!-- Invoice details will be loaded here -->
      </div>
    </div>
  </div>
</div>

<!-- Add a debug modal for AJAX response
<div class="modal fade" id="debugModal" tabindex="-1" aria-labelledby="debugModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="debugModalLabel">Debug: Loaded Line Items</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="debugModalBody" style="white-space:pre-wrap;font-size:12px;"></div>
    </div>
  </div>
</div>

<!-- Add a div for AJAX/JS errors -->
<div id="ajaxError" class="alert alert-danger d-none mt-3"></div>

<script>
$(function () {
    // ... existing code ...
    function loadInvoices() {
        $.get("fetch_invoices.php", function (data) {
            console.log('AJAX response:', data);
            if (!data || data.indexOf('No invoices found') !== -1) {
                $("#ajaxError").removeClass('d-none').text('No invoices found or failed to load invoices.');
                $("#invoiceTableBody").html('<tr><td colspan="10" class="text-center text-danger">No invoices found or failed to load invoices.</td></tr>');
            } else {
                $("#ajaxError").addClass('d-none').text('');
                $("#invoiceTableBody").html(data);
            }
        }).fail(function(xhr, status, error) {
            $("#ajaxError").removeClass('d-none').text('AJAX error: ' + error);
            $("#invoiceTableBody").html('<tr><td colspan="10" class="text-center text-danger">AJAX error: ' + error + '</td></tr>');
        });
    }
    // ... existing code ...
    // Ensure Add Invoice button is always enabled
    $("#invoiceModal").on('hidden.bs.modal', function () {
        $("#addRowBtn").prop('disabled', false);
    });
    // Catch-all JS error handler
    window.onerror = function(message, source, lineno, colno, error) {
        $("#ajaxError").removeClass('d-none').text('JS Error: ' + message + ' at ' + source + ':' + lineno);
        return false;
    };
});
</script>