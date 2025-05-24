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
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <tbody id="invoiceTableBody"></tbody>
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

<?php include 'inc/footer.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(function () {
    // Configure Toastr options (optional)
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    // Show Toastr notification
    function showToast(msg, type) {
        msg = msg || ''; // Ensure msg is not null or undefined
        if (type === 'success') {
            toastr.success(msg);
        } else if (type === 'error') {
            toastr.error(msg);
        } else if (type === 'warning') {
            toastr.warning(msg);
        } else {
            toastr.info(msg);
        }
    }

    function loadInvoices() {
        $.get("fetch_invoices.php", function (data) {
            $("#invoiceTableBody").html(data);
        });
    }

    loadInvoices();

    $("#invoiceForm").on("submit", function (e) {
        e.preventDefault();
        $.post("save_invoice.php", $(this).serialize(), function (res) {
            var msg = '';
            var type = 'info';
            try {
                var json = typeof res === 'string' ? JSON.parse(res) : res;
                if (json && typeof json === 'object' && json.message) {
                    msg = json.message;
                    type = json.success ? 'success' : 'error';
                } else {
                    msg = res;
                }
            } catch (e) {
                var m = (typeof res === 'string') ? res.match(/<div[^>]*>(.*?)<\/div>/i) : null;
                msg = m ? m[1] : (typeof res === 'string' ? res : 'Action completed');
                if (res && res.toLowerCase().includes('success')) type = 'success';
                else if (res && (res.toLowerCase().includes('error') || res.toLowerCase().includes('fail'))) type = 'error';
                else if (res && res.toLowerCase().includes('warning')) type = 'warning';
            }
            showToast(msg, type);
            if (type === 'success') {
                $("#invoiceForm")[0].reset();
                $("#invoiceModalLabel").text("Add New Invoice");
                $("#submitBtn").text("Add Invoice");
                $("#invoice_id").val('');
                var modal = bootstrap.Modal.getInstance(document.getElementById('invoiceModal'));
                if (modal) modal.hide();
                loadInvoices();
            }
        });
    });

    $(document).on("click", ".edit-btn", function () {
        $("#invoiceModalLabel").text("Update Invoice");
        $("#submitBtn").text("Update Invoice");
        $("#invoice_id").val($(this).data("id"));
        $("#customer_id").val($(this).data("customer_id"));
        $("#destination").val($(this).data("destination"));
        $("#total_amount").val($(this).data("total_amount"));
        $("#gst_amount").val($(this).data("gst_amount"));
        $("#grand_total").val($(this).data("grand_total"));
        // Load line items via AJAX
        var invoiceId = $(this).data("id");
        $.get("fetch_invoice_items.php", {invoice_id: invoiceId}, function (data) {
            var items = [];
            try { items = JSON.parse(data); } catch (e) {}
            console.log('Loaded items:', items);
            var tbody = "";
            if (items.length > 0) {
                for (var i = 0; i < items.length; i++) {
                    var destOptions = destinationOptions.replace(
                        `value='${items[i].destination_city}'`,
                        `value='${items[i].destination_city}' selected`
                    );
                    tbody += `<tr>
                        <td><input type='date' name='line_items[${i}][booking_date]' class='form-control' value='${items[i].booking_date || ""}' required></td>
                        <td><input type='text' name='line_items[${i}][consignment_no]' class='form-control' value='${items[i].consignment_no || ""}' required></td>
                        <td><select name='line_items[${i}][destination_city]' class='form-select' required>${destOptions}</select></td>
                        <td><input type='number' step='0.001' name='line_items[${i}][weight]' class='form-control' value='${items[i].weight || ""}' required></td>
                        <td><input type='number' step='0.01' name='line_items[${i}][amt]' class='form-control' value='${items[i].amt || ""}'></td>
                        <td><input type='number' step='0.01' name='line_items[${i}][way_bill_value]' class='form-control' value='${items[i].way_bill_value || ""}'></td>
                        <td><input type='text' name='line_items[${i}][description]' class='form-control' value='${items[i].description || ""}'></td>
                        <td><input type='number' name='line_items[${i}][quantity]' class='form-control' value='${items[i].quantity || ""}'></td>
                        <td><input type='number' step='0.01' name='line_items[${i}][rate]' class='form-control' value='${items[i].rate || ""}'></td>
                        <td><input type='number' step='0.01' name='line_items[${i}][amount]' class='form-control' value='${items[i].amount || ""}'></td>
                        <td><button type='button' class='btn btn-outline-danger btn-sm remove-row'>Remove</button></td>
                    </tr>`;
                }
            } else {
                tbody = `<tr>
                    <td><input type='date' name='line_items[0][booking_date]' class='form-control' required></td>
                    <td><input type='text' name='line_items[0][consignment_no]' class='form-control' required></td>
                    <td><select name='line_items[0][destination_city]' class='form-select' required>${destinationOptions}</select></td>
                    <td><input type='number' step='0.001' name='line_items[0][weight]' class='form-control' required></td>
                    <td><input type='number' step='0.01' name='line_items[0][amt]' class='form-control'></td>
                    <td><input type='number' step='0.01' name='line_items[0][way_bill_value]' class='form-control'></td>
                    <td><input type='text' name='line_items[0][description]' class='form-control'></td>
                    <td><input type='number' name='line_items[0][quantity]' class='form-control'></td>
                    <td><input type='number' step='0.01' name='line_items[0][rate]' class='form-control'></td>
                    <td><input type='number' step='0.01' name='line_items[0][amount]' class='form-control'></td>
                    <td><button type='button' class='btn btn-outline-danger btn-sm remove-row'>Remove</button></td>
                </tr>`;
            }
            $("#lineItemsTable tbody").html(tbody);
        });
        var modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
        modal.show();
    });

    $(document).on("click", ".delete-btn", function () {
        const id = $(this).data("id");

        if (confirm("Are you sure you want to delete this invoice?")) {
            $.post("delete_invoice.php", { id: id }, function (response) {
                var msg = 'Invoice deleted successfully!';
                var type = 'success';
                try {
                    var json = typeof response === 'string' ? JSON.parse(response) : response;
                    if (json && typeof json === 'object') {
                        msg = json.message || msg;
                        type = json.success ? 'success' : 'error';
                    } else {
                        // Attempt to infer from string response
                        if (response && response.toLowerCase().includes('error')) {
                            type = 'error';
                            msg = response;
                        } else if (response && response.toLowerCase().includes('success')) {
                            type = 'success';
                            msg = response;
                        } else {
                            msg = response || msg; // Use response if available, otherwise default
                        }
                    }
                } catch (e) {
                    // If parsing fails, treat response as a plain string
                    if (typeof response === 'string' && response.toLowerCase().includes('error')) {
                        type = 'error';
                    }
                    msg = typeof response === 'string' ? response : 'Error processing response.';
                }
                showToast(msg, type);
                if (type === 'success') {
                    loadInvoices();
                }
            }).fail(function() {
                showToast('Error deleting invoice. Please try again.', 'error');
            });
        }
    });

    $(document).ready(function () {
        let rowIdx = 1;
        // Get destination options HTML
        var destinationOptions = '';
        <?php
        $query = "SELECT name FROM destinations ORDER BY name ASC";
        $result = $conn->query($query);
        $options = "<option value=''>Select Destination</option>";
        while ($row = $result->fetch_assoc()) {
            $options .= "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
        }
        ?>
        destinationOptions = `<?php echo $options; ?>`;
        $('#addRowBtn').click(function () {
            const row = `<tr>
                <td><input type="date" name="line_items[${rowIdx}][booking_date]" class="form-control" required></td>
                <td><input type="text" name="line_items[${rowIdx}][consignment_no]" class="form-control" required></td>
                <td><select name="line_items[${rowIdx}][destination_city]" class="form-select" required>${destinationOptions}</select></td>
                <td><input type="number" step="0.001" name="line_items[${rowIdx}][weight]" class="form-control" required></td>
                <td><input type="number" step="0.01" name="line_items[${rowIdx}][amt]" class="form-control"></td>
                <td><input type="number" step="0.01" name="line_items[${rowIdx}][way_bill_value]" class="form-control"></td>
                <td><input type="text" name="line_items[${rowIdx}][description]" class="form-control"></td>
                <td><input type="number" name="line_items[${rowIdx}][quantity]" class="form-control"></td>
                <td><input type="number" step="0.01" name="line_items[${rowIdx}][rate]" class="form-control"></td>
                <td><input type="number" step="0.01" name="line_items[${rowIdx}][amount]" class="form-control"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
            </tr>`;
            $('#lineItemsTable tbody').append(row);
            rowIdx++;
        });
        $(document).on('click', '.remove-row', function () {
            if ($('#lineItemsTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
            }
        });
    });

    $(document).on('click', '.view-btn', function () {
        var invoiceId = $(this).data('id');
        $('#viewInvoiceModalBody').html('<div class="text-center p-4">Loading...</div>');
        $('#viewInvoiceModal').modal('show');
        $.get('invoice_details.php', {id: invoiceId}, function (data) {
            // Extract only the main content from the response
            var mainContent = $(data).find('main.content-wrapper').html();
            $('#viewInvoiceModalBody').html(mainContent ? mainContent : data);
        });
    });

});
</script>