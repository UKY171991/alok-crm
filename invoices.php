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

// Fetch destinations for JS use
$destinations = [];
$destResult = $conn->query("SELECT name FROM destinations ORDER BY name ASC");
while ($row = $destResult->fetch_assoc()) {
    $destinations[] = $row['name'];
}
?>

<!-- Ensure jQuery is loaded first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Then Bootstrap JS (if used) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
#invoiceModal .table th, #invoiceModal .table td {
    vertical-align: middle;
    padding: 0.4rem 0.5rem;
}
#invoiceModal .form-control-sm, #invoiceModal .form-select-sm {
    min-width: 80px;
}
#invoiceModal .table-primary {
    background-color: #e9f5ff;
}
</style>

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
                        <input type="hidden" name="id" id="invoice_id">
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
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Invoice Date</label>
                                    <input type="date" name="invoice_date" id="invoice_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Invoice No</label>
                                    <input type="text" name="invoice_no" id="invoice_no" class="form-control" value="<?= htmlspecialchars($invoice_no) ?>" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Destination (optional)</label>
                                    <select name="destination" id="destination" class="form-select">
                                        <option value="">Select Destination</option>
                                        <?php foreach ($destinations as $dest): ?>
                                            <option value="<?= htmlspecialchars($dest) ?>"><?= htmlspecialchars($dest) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <hr class="my-3">
                            <h5 class="fw-bold mb-3 text-primary">Invoice Line Items</h5>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-hover align-middle" id="lineItemsTable">
                                    <thead class="table-primary">
                                        <tr>
                                            <th class="text-center">Booking Date</th>
                                            <th class="text-center">Consignment No.</th>
                                            <th class="text-center">Destination</th>
                                            <th class="text-center">Weight or N</th>
                                            <th class="text-center">Amt.</th>
                                            <th class="text-center">Way Bill Value</th>
                                            <th class="text-center">Description</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-center">Rate</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="date" name="line_items[0][booking_date]" class="form-control" required></td>
                                            <td><input type="text" name="line_items[0][consignment_no]" class="form-control" required></td>
                                            <td>
                                                <select name="line_items[0][destination_city]" class="form-select" required>
                                                    <option value="">Select Destination</option>
                                                    <?php foreach ($destinations as $dest): ?>
                                                        <option value="<?= htmlspecialchars($dest) ?>"><?= htmlspecialchars($dest) ?></option>
                                                    <?php endforeach; ?>
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
                                <th>#</th><th>Invoice No</th><th>Customer</th><th>Invoice Date</th><th>Total Amount</th><th>GST Amount</th><th>Grand Total</th><th>Created</th><th>Actions</th>
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
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewInvoiceModalLabel">Invoice Details</h5>
        <button type="button" class="btn btn-secondary btn-sm me-2" id="printInvoiceModalBtn"><i class="fas fa-print"></i> Print</button>
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
// Destination options for JS
const destinationOptions = `<?php foreach ($destinations as $dest): ?><option value="<?= htmlspecialchars($dest) ?>"><?= htmlspecialchars($dest) ?></option><?php endforeach; ?>`;

// Add/Remove line item rows
document.addEventListener('DOMContentLoaded', function() {
    let rowIdx = 1;
    $(document).on('click', '#addRowBtn', function() {
        const row = `<tr>
            <td><input type='date' name='line_items[${rowIdx}][booking_date]' class='form-control' required></td>
            <td><input type='text' name='line_items[${rowIdx}][consignment_no]' class='form-control' required></td>
            <td><select name='line_items[${rowIdx}][destination_city]' class='form-select' required><option value=''>Select Destination</option>${destinationOptions}</select></td>
            <td><input type='number' step='0.001' name='line_items[${rowIdx}][weight]' class='form-control' required></td>
            <td><input type='number' step='0.01' name='line_items[${rowIdx}][amt]' class='form-control'></td>
            <td><input type='number' step='0.01' name='line_items[${rowIdx}][way_bill_value]' class='form-control'></td>
            <td><input type='text' name='line_items[${rowIdx}][description]' class='form-control'></td>
            <td><input type='number' name='line_items[${rowIdx}][quantity]' class='form-control'></td>
            <td><input type='number' step='0.01' name='line_items[${rowIdx}][rate]' class='form-control'></td>
            <td><input type='number' step='0.01' name='line_items[${rowIdx}][amount]' class='form-control'></td>
            <td><button type='button' class='btn btn-outline-danger btn-sm remove-row'>Remove</button></td>
        </tr>`;
        $('#lineItemsTable tbody').append(row);
        rowIdx++;
    });
    $(document).on('click', '.remove-row', function() {
        if ($('#lineItemsTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
        }
    });
});

$(function () {
    function loadInvoices() {
        $.get("fetch_invoices.php", function (data) {
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
    loadInvoices();

    // AJAX form submission for invoiceForm
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
            // Show message
            $("#message").html('<div class="alert alert-' + (type === 'success' ? 'success' : (type === 'error' ? 'danger' : 'info')) + '">' + msg + '</div>');
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
    $("#invoiceModal").on('hidden.bs.modal', function () {
        $("#addRowBtn").prop('disabled', false);
    });
    window.onerror = function(message, source, lineno, colno, error) {
        $("#ajaxError").removeClass('d-none').text('JS Error: ' + message + ' at ' + source + ':' + lineno);
        return false;
    };
    $(document).on("click", ".view-btn", function () {
        var invoiceId = $(this).data('id');
        $('#viewInvoiceModalBody').html('<div class="text-center p-4">Loading...</div>');
        $('#viewInvoiceModal').modal('show');
        $.get('invoice_details.php', {id: invoiceId}, function (data) {
            var mainContent = $(data).find('main.content-wrapper').html();
            $('#viewInvoiceModalBody').html(mainContent ? mainContent : data);
        });
    });
    $(document).on("click", ".edit-btn", function () {
        $("#invoiceModalLabel").text("Update Invoice");
        $("#submitBtn").text("Update Invoice");
        $("#invoice_id").val($(this).data("id"));
        $("#customer_id").val($(this).data("customer_id"));
        $("#invoice_date").val($(this).data("invoice_date"));
        $("#invoice_no").val($(this).data("invoice_no"));
        $("#destination").val($(this).data("destination"));
        $("#total_amount").val($(this).data("total_amount"));
        $("#gst_amount").val($(this).data("gst_amount"));
        $("#grand_total").val($(this).data("grand_total"));
        var invoiceId = $(this).data("id");
        $.get("fetch_invoice_items.php", {invoice_id: invoiceId}, function (data) {
            var items = [];
            try { items = typeof data === 'string' ? JSON.parse(data) : data; } catch (e) {}
            var tbody = "";
            if (items.length > 0) {
                for (var i = 0; i < items.length; i++) {
                    var item = items[i] || {};
                    tbody += `<tr>
                        <td><input type='date' name='line_items[${i}][booking_date]' class='form-control form-control-sm' value='${item.booking_date || ''}' required></td>
                        <td><input type='text' name='line_items[${i}][consignment_no]' class='form-control form-control-sm' value='${item.consignment_no || ''}' required></td>
                        <td>
                          <select name='line_items[${i}][destination_city]' class='form-select form-select-sm' required>
                            <option value=''>Select Destination</option>
                            ${destinationOptions}
                          </select>
                        </td>
                        <td><input type='number' step='0.001' name='line_items[${i}][weight]' class='form-control form-control-sm text-center' value='${item.weight != null ? item.weight : ''}' required></td>
                        <td><input type='number' step='0.01' name='line_items[${i}][amt]' class='form-control form-control-sm text-center' value='${item.amt != null ? item.amt : ''}'></td>
                        <td><input type='number' step='0.01' name='line_items[${i}][way_bill_value]' class='form-control form-control-sm text-center' value='${item.way_bill_value != null ? item.way_bill_value : ''}'></td>
                        <td><input type='text' name='line_items[${i}][description]' class='form-control form-control-sm' value='${item.description || ''}'></td>
                        <td><input type='number' name='line_items[${i}][quantity]' class='form-control form-control-sm text-center' value='${item.quantity != null ? item.quantity : ''}'></td>
                        <td><input type='number' step='0.01' name='line_items[${i}][rate]' class='form-control form-control-sm text-center' value='${item.rate != null ? item.rate : ''}'></td>
                        <td><input type='number' step='0.01' name='line_items[${i}][amount]' class='form-control form-control-sm text-center' value='${item.amount != null ? item.amount : ''}'></td>
                        <td class='text-center'><button type='button' class='btn btn-outline-danger btn-sm remove-row'>Rem</button></td>
                    </tr>`;
                }
            } else {
                tbody = `<tr>
                    <td><input type='date' name='line_items[0][booking_date]' class='form-control' required></td>
                    <td><input type='text' name='line_items[0][consignment_no]' class='form-control' required></td>
                    <td><select name='line_items[0][destination_city]' class='form-select' required><option value=''>Select Destination</option>${destinationOptions}</select></td>
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
            // Set destination select value for each row
            $("#lineItemsTable tbody tr").each(function(index) {
                var item = items[index];
                if (item && item.destination_city) {
                    $(this).find("select[name^='line_items']").val(item.destination_city);
                }
            });
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
                        if (response && response.toLowerCase().includes('error')) {
                            type = 'error';
                            msg = response;
                        } else if (response && response.toLowerCase().includes('success')) {
                            type = 'success';
                            msg = response;
                        } else {
                            msg = response || msg;
                        }
                    }
                } catch (e) {
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
    $(document).on("click", ".print-btn", function () {
        var invoiceId = $(this).data('id');
        $('#viewInvoiceModalBody').html('<div class="text-center p-4">Loading...</div>');
        $('#viewInvoiceModal').modal('show');
        $.get('invoice_details.php', {id: invoiceId}, function (data) {
            var mainContent = $(data).find('main.content-wrapper').html();
            $('#viewInvoiceModalBody').html(mainContent ? mainContent : data);
            setTimeout(function() {
                printInvoiceModalContent();
            }, 500);
        });
    });
    $('#printInvoiceModalBtn').on('click', function() {
        printInvoiceModalContent();
    });
    function printInvoiceModalContent() {
        var printContents = document.getElementById('viewInvoiceModalBody').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload(); // To restore JS events and DOM
    }
});

function showToast(msg, type) {
    var alertType = 'info';
    if (type === 'success') alertType = 'success';
    else if (type === 'error') alertType = 'danger';
    else if (type === 'warning') alertType = 'warning';
    var alertHtml = '<div class="alert alert-' + alertType + ' alert-dismissible fade show" role="alert">' +
        msg +
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
        '</div>';
    $("#ajaxError").removeClass('d-none').html(alertHtml);
    setTimeout(function() { $("#ajaxError").addClass('d-none').html(''); }, 5000);
}

$(document).on('click', '[data-bs-target="#invoiceModal"]', function () {
    // Reset form fields
    $('#invoiceForm')[0].reset();
    $('#invoice_id').val('');
    $('#invoiceModalLabel').text('Add New Invoice');
    $('#submitBtn').text('Add Invoice');
    $('#invoice_no').val('<?= htmlspecialchars($invoice_no) ?>');
    // Reset destination dropdown
    $('#destination').val('');
    // Reset line items to a single empty row
    var tbody = `<tr>
        <td><input type='date' name='line_items[0][booking_date]' class='form-control form-control-sm' required></td>
        <td><input type='text' name='line_items[0][consignment_no]' class='form-control form-control-sm' required></td>
        <td><select name='line_items[0][destination_city]' class='form-select form-select-sm' required><option value=''>Select Destination</option>${destinationOptions}</select></td>
        <td><input type='number' step='0.001' name='line_items[0][weight]' class='form-control form-control-sm text-center' required></td>
        <td><input type='number' step='0.01' name='line_items[0][amt]' class='form-control form-control-sm text-center'></td>
        <td><input type='number' step='0.01' name='line_items[0][way_bill_value]' class='form-control form-control-sm text-center'></td>
        <td><input type='text' name='line_items[0][description]' class='form-control form-control-sm'></td>
        <td><input type='number' name='line_items[0][quantity]' class='form-control form-control-sm text-center'></td>
        <td><input type='number' step='0.01' name='line_items[0][rate]' class='form-control form-control-sm text-center'></td>
        <td><input type='number' step='0.01' name='line_items[0][amount]' class='form-control form-control-sm text-center'></td>
        <td class='text-center'><button type='button' class='btn btn-outline-danger btn-sm remove-row'>Rem</button></td>
    </tr>`;
    $('#lineItemsTable tbody').html(tbody);
});

// Auto-calculate amount and total when rate or quantity changes
$(document).on('input', 'input[name^="line_items"][name$="[rate]"], input[name^="line_items"][name$="[quantity]"]', function() {
    var $row = $(this).closest('tr');
    var qty = parseFloat($row.find('input[name$="[quantity]"]').val()) || 0;
    var rate = parseFloat($row.find('input[name$="[rate]"]').val()) || 0;
    var amount = qty * rate;
    $row.find('input[name$="[amount]"]').val(amount.toFixed(2));
    updateTotalAmount();
});

// Also recalculate total if amount is edited directly
$(document).on('input', 'input[name^="line_items"][name$="[amount]"]', function() {
    updateTotalAmount();
});

function updateTotalAmount() {
    var total = 0;
    $('input[name^="line_items"][name$="[amount]"]').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
    $('#total_amount').val(total.toFixed(2));
}
</script>

<?php include 'inc/footer.php'; ?>