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
/* Custom close button for modal */
.btn-close-custom {
    background: #fff;
    color: #dc3545;
    border: 2px solid #dc3545;
    transition: background 0.2s, color 0.2s;
}
.btn-close-custom:hover {
    background: #dc3545;
    color: #fff;
}
.modal-content {
    border-radius: 1.5rem !important;
    box-shadow: 0 8px 32px rgba(0,0,0,0.18) !important;
}
.modal-header {
    border-bottom: none;
}
.modal-footer {
    border-top: none;
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
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <div class="modal-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between" style="padding: 1.2rem 2rem;">
                        <h5 class="modal-title fw-bold" id="invoiceModalLabel">Add New Invoice</h5>
                        <button type="button" class="btn btn-light btn-close-custom ms-auto" data-bs-dismiss="modal" aria-label="Close" style="font-size:1.5rem; border-radius:50%; width:2.5rem; height:2.5rem; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="invoiceForm">
                        <input type="hidden" name="id" id="invoice_id">
                        <div class="modal-body p-4 bg-light rounded-bottom-4">
                            <div class="row g-3 mb-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Customer</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <select name="customer_id" id="customer_id" class="form-select" required>
                                            <option value="">Select Customer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Invoice Date</label>
                                    <input type="date" name="invoice_date" id="invoice_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Invoice No</label>
                                    <input type="text" name="invoice_no" id="invoice_no" class="form-control" value="<?= htmlspecialchars($invoice_no) ?>" readonly>
                                </div>
                            </div>
                            <div id="orderListContainer" class="mb-2"><!-- Orders will be loaded here --></div>
                            <div class="table-responsive mb-3" id="lineItemsTableContainer" style="display:none;">
                                <table class="table table-bordered table-hover align-middle mb-0" id="lineItemsTable">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Order ID</th>
                                            <th>Booking Date</th>
                                            <th>Consignment No.</th>
                                            <th>Destination City</th>
                                            <th>Dox / Non Dox</th>
                                            <th>Service</th>
                                            <th>No of Pcs</th>
                                            <th>Weight or No</th>
                                            <th>Amt.</th>
                                            <th>Way Bill Value</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div id="message" class="mt-3"></div>
                        </div>
                        <div class="modal-footer bg-white rounded-bottom-4" style="padding: 1rem 2rem;">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-pill" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Close</button>
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill" id="submitBtn"><i class="fas fa-save me-1"></i> Add Invoice</button>
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
                                <th>#</th>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                <th>Invoice Date</th>
                                <th>Booking Date</th>
                                <th>Consignment No.</th>
                                <th>Destination City</th>
                                <th>Dox / Non Dox</th>
                                <th>No of Pcs</th>
                                <th>Weight or No</th>
                                <th>Amt.</th>
                                <th>Way Bill Value</th>
                                <th>Actions</th>
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
    <div class="modal-content rounded-4">
      <div class="modal-header d-flex align-items-center justify-content-between bg-white rounded-top-4" style="padding: 1.2rem 2rem;">
        <h5 class="modal-title" id="viewInvoiceModalLabel">Invoice Details</h5>
        <div class="d-flex align-items-center gap-2">
          <button type="button" class="btn btn-secondary btn-sm me-2" id="printInvoiceModalBtn"><i class="fas fa-print"></i> Print</button>
          <button type="button" class="btn btn-success btn-sm me-2" id="saveInvoiceModalBtn"><i class="fas fa-download"></i> Save</button>
          <button type="button" class="btn btn-info btn-sm me-2" id="mailInvoiceModalBtn"><i class="fas fa-envelope"></i> Mail</button>
          <button type="button" class="btn btn-success btn-sm me-2" id="whatsappInvoiceModalBtn" style="background-color:#25D366;"><i class="fab fa-whatsapp"></i> WhatsApp</button>
          <button type="button" class="btn btn-light btn-close-custom ms-auto" data-bs-dismiss="modal" aria-label="Close" style="font-size:1.5rem; border-radius:50%; width:2.5rem; height:2.5rem; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
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

// Populate customer select
function loadCustomersForInvoice() {
    $('#customer_id').html('<option>Loading...</option>');
    $.get('ajax/fetch_customers_select.php', function(data) {
        $('#customer_id').html(data);
    }).fail(function(xhr, status, error) {
        $('#customer_id').html('<option value="">Error loading customers</option>');
        alert('Failed to load customers: ' + error);
    });
}

// Load orders for selected customer
function loadOrdersForCustomer(customerId) {
    if (!customerId) {
        $('#orderListContainer').html('<div class="alert alert-info">Please select a customer to load their orders.</div>');
        return;
    }
    $('#orderListContainer').html('<div class="text-center p-3"><span class="spinner-border"></span> Loading orders...</div>');
    $.get('ajax/fetch_orders.php', { customer_id: customerId, for_invoice: 1 }, function(html) {
        $('#orderListContainer').html(html);
    });
}

$(document).on('change', '#customer_id', function() {
    loadOrdersForCustomer($(this).val());
});

$(document).ready(function() {
    loadCustomersForInvoice();
});

// Update the JS that builds the line items from checked rows to include order_id as a hidden input
function updateLineItemInputNames() {
    var checkedRows = $('#orderListContainer table tbody tr').filter(function() {
        return $(this).find('.order-checkbox').is(':checked');
    });
    checkedRows.each(function(idx) {
        var orderId = $(this).find('.order-checkbox').val();
        $(this).find('.editable-cell').each(function() {
            var input = $(this).find('input, select');
            if (input.length) {
                var name = $(this).data('name');
                input.attr('name', 'line_items[' + idx + '][' + name + ']');
            }
        });
        // Add or update hidden order_id input
        var hiddenOrderId = $(this).find('input[type="hidden"][name$="[order_id]"]');
        if (hiddenOrderId.length === 0) {
            $(this).append('<input type="hidden" name="line_items[' + idx + '][order_id]" value="' + orderId + '">');
        } else {
            hiddenOrderId.attr('name', 'line_items[' + idx + '][order_id]').val(orderId);
        }
    });
}

$(document).on('change', '.order-checkbox', function() {
    var $row = $(this).closest('tr');
    if ($(this).is(':checked')) {
        $row.find('.editable-cell').each(function() {
            var val = $(this).find('input, select').length ? $(this).find('input, select').val() : $(this).text();
            var name = $(this).data('name');
            // Use select for destination_city, input for others
            if (name === 'destination_city') {
                $(this).html(`<select class='form-select form-select-sm' name=''><option value=''>Select Destination</option>${destinationOptions}</select>`);
                $(this).find('select').val(val);
            } else {
                var inputType = $(this).data('type') || 'text';
                $(this).html(`<input type='${inputType}' class='form-control form-control-sm' value='${val}' name=''>`);
            }
        });
        updateLineItemInputNames();
    } else {
        $row.find('.editable-cell').each(function() {
            var input = $(this).find('input, select');
            if (input.length) {
                $(this).text(input.val());
            }
        });
        updateLineItemInputNames();
    }
});

// Remove any duplicate #invoiceForm submit handler and merge validation + AJAX submit into one
$('#invoiceForm').off('submit').on('submit', function(e) {
    e.preventDefault();
    // Remove required from hidden fields
    $(this).find(':input[required]').each(function() {
        if (!$(this).is(':visible')) {
            $(this).prop('required', false);
        }
    });
    updateLineItemInputNames();
    var checkedRows = $('#orderListContainer table tbody tr').filter(function() {
        return $(this).find('.order-checkbox').is(':checked');
    });
    var missing = false;
    var errorMsg = '';
    checkedRows.each(function(index) {
        var booking = $(this).find('input[name$="[booking_date]"]').val();
        var consignment = $(this).find('input[name$="[consignment_no]"]').val();
        var dest = $(this).find('select[name$="[destination_city]"]').val();
        var dox = $(this).find('input[name$="[dox_non_dox]"]').val();
        var service = $(this).find('input[name$="[service]"]').val();
        var pcs = $(this).find('input[name$="[quantity]"]').val();
        var weight = $(this).find('input[name$="[weight]"]').val();
        var amt = $(this).find('input[name$="[amt]"]').val();
        var waybill = $(this).find('input[name$="[way_bill_value]"]').val();

        if (!booking || booking === '0000-00-00' || booking === 'dd-mm-yyyy') {
            missing = true;
            errorMsg = 'Please enter a valid Booking Date for all checked rows.';
            return false;
        }
        if (!consignment) {
            missing = true;
            errorMsg = 'Please enter Consignment No. for all checked rows.';
            return false;
        }
        if (!dest) {
            missing = true;
            errorMsg = 'Please select Destination City for all checked rows.';
            return false;
        }
        if (!dox) {
            missing = true;
            errorMsg = 'Please enter Dox / Non Dox for all checked rows.';
            return false;
        }
        if (!service || parseFloat(service) === 0) {
            missing = true;
            errorMsg = 'Please enter Service for all checked rows.';
            return false;
        }
        if (!pcs || parseInt(pcs) === 0) {
            missing = true;
            errorMsg = 'Please enter No of Pcs for all checked rows.';
            return false;
        }
        if (!weight || parseFloat(weight) === 0) {
            missing = true;
            errorMsg = 'Please enter Weight or No for all checked rows.';
            return false;
        }
        if (!amt || parseFloat(amt) === 0) {
            missing = true;
            errorMsg = 'Please enter Amount for all checked rows.';
            return false;
        }
        if (!waybill || parseFloat(waybill) === 0) {
            missing = true;
            errorMsg = 'Please enter Way Bill Value for all checked rows.';
            return false;
        }
    });
    if (checkedRows.length === 0) {
        $('#message').html('<div class="alert alert-danger">Please add at least one line item before submitting.</div>');
        return false;
    }
    if (missing) {
        $('#message').html('<div class="alert alert-danger">' + errorMsg + '</div>');
        return false;
    }
    // If validation passes, do AJAX submit
    $.post('save_invoice.php', $(this).serialize(), function (res) {
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
        $('#message').html('<div class="alert alert-' + (type === 'success' ? 'success' : (type === 'error' ? 'danger' : 'info')) + '">' + msg + '</div>');
        if (type === 'success') {
            $('#invoiceForm')[0].reset();
            $('#invoiceModalLabel').text('Add New Invoice');
            $('#submitBtn').text('Add Invoice');
            $('#invoice_id').val('');
            var modal = bootstrap.Modal.getInstance(document.getElementById('invoiceModal'));
            if (modal) modal.hide();
            // Remove any lingering modal-backdrop and modal-open classes
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            setTimeout(function() {
                loadInvoices();
                console.log('Invoices table refreshed after form submit.');
            }, 400);
        }
    });
    return false;
});

// When opening the modal (Add Invoice button), only reset the form and modal state, do not remove the backdrop or modal-open class
$(document).on('click', '[data-bs-target="#invoiceModal"]', function () {
    // Reset form fields
    $('#invoiceForm')[0].reset();
    $('#invoice_id').val('');
    $('#invoiceModalLabel').text('Add New Invoice');
    $('#submitBtn').text('Add Invoice');
    $('#invoice_no').val('<?= htmlspecialchars($invoice_no) ?>');
    $('#destination').val('');
    // Hide the line items table and clear its rows when adding a new invoice
    $('#lineItemsTableContainer').hide();
    $('#lineItemsTable tbody').html('');
    $('#message').html('');
});

// Move this function to global scope
function loadInvoices() {
    $("#invoiceTableBody").html('<tr><td colspan="10" class="text-center">Loading...</td></tr>');
    $.get("fetch_invoices.php", { t: Date.now() }, function (data) {
        if (!data) {
            $("#ajaxError").removeClass('d-none').text('Failed to load invoices (server error).');
            $("#invoiceTableBody").html('<tr><td colspan="10" class="text-center text-danger">Failed to load invoices (server error).</td></tr>');
        } else if (data.indexOf('No invoices found') !== -1) {
            $("#ajaxError").addClass('d-none').text('');
            $("#invoiceTableBody").html('<tr><td colspan="10" class="text-center text-muted">No invoices found.</td></tr>');
        } else {
            $("#ajaxError").addClass('d-none').text('');
            $("#invoiceTableBody").html(data);
        }
    }).fail(function(xhr, status, error) {
        $("#ajaxError").removeClass('d-none').text('AJAX error: ' + error);
        $("#invoiceTableBody").html('<tr><td colspan="10" class="text-center text-danger">AJAX error: ' + error + '</td></tr>');
    });
}

$(function () {
    loadInvoices();

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
        loadOrdersForCustomer($("#customer_id").val());
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
                        <td>${i + 1}</td>
                        <td>${item.order_id != null ? item.order_id : ''}</td>
                        <td><input type='date' name='line_items[${i}][booking_date]' class='form-control form-control-sm' value='${item.booking_date || ''}' required></td>
                        <td><input type='text' name='line_items[${i}][consignment_no]' class='form-control form-control-sm' value='${item.consignment_no || ''}' required></td>
                        <td><select name='line_items[${i}][destination_city]' class='form-select form-select-sm' required><option value=''>Select Destination</option>${destinationOptions}</select></td>
                        <td><input type='text' name='line_items[${i}][dox_non_dox]' class='form-control form-control-sm' value='${item.dox_non_dox || ''}'></td>
                        <td><input type='text' name='line_items[${i}][service]' class='form-control form-control-sm' value='${item.service || ''}'></td>
                        <td><input type='number' name='line_items[${i}][quantity]' class='form-control form-control-sm text-center' value='${item.quantity != null ? item.quantity : ''}'></td>
                        <td><input type='number' step='0.001' name='line_items[${i}][weight]' class='form-control form-control-sm text-center' value='${item.weight != null ? item.weight : ''}'></td>
                        <td><input type='number' step='0.01' name='line_items[${i}][amt]' class='form-control form-control-sm text-center' value='${item.amt != null ? item.amt : ''}'></td>
                        <td><input type='number' step='0.01' name='line_items[${i}][way_bill_value]' class='form-control form-control-sm text-center' value='${item.way_bill_value != null ? item.way_bill_value : ''}'></td>
                        <td><input type='hidden' name='line_items[${i}][order_id]' value='${item.order_id != null ? item.order_id : ''}'></td>
                        <td class='text-center'><button type='button' class='btn btn-outline-danger btn-sm remove-row'>Rem</button></td>
                    </tr>`;
                }
            } else {
                tbody = `<tr>
                    <td>${i + 1}</td>
                    <td>${item.order_id != null ? item.order_id : ''}</td>
                    <td><input type='date' name='line_items[0][booking_date]' class='form-control' required></td>
                    <td><input type='text' name='line_items[0][consignment_no]' class='form-control' required></td>
                    <td><select name='line_items[0][destination_city]' class='form-select' required><option value=''>Select Destination</option>${destinationOptions}</select></td>
                    <td><input type='text' name='line_items[0][dox_non_dox]' class='form-control'></td>
                    <td><input type='text' name='line_items[0][service]' class='form-control'></td>
                    <td><input type='number' name='line_items[0][quantity]' class='form-control'></td>
                    <td><input type='number' step='0.001' name='line_items[0][weight]' class='form-control' required></td>
                    <td><input type='number' step='0.01' name='line_items[0][amt]' class='form-control'></td>
                    <td><input type='number' step='0.01' name='line_items[0][way_bill_value]' class='form-control'></td>
                    <td><input type='hidden' name='line_items[0][order_id]' value='${item.order_id != null ? item.order_id : ''}'></td>
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

let currentInvoiceId = null;
$(function() {
  // Store invoice ID when opening the modal
  $(document).on("click", ".view-btn, .print-btn", function () {
    currentInvoiceId = $(this).data('id');
  });
  // Print
  $('#printInvoiceModalBtn').on('click', function() {
    printInvoiceModalContent();
  });
  // Save (download PDF)
  $('#saveInvoiceModalBtn').attr('title', 'Download PDF').tooltip({placement: 'bottom'});
  $('#saveInvoiceModalBtn').on('click', function() {
    if (!currentInvoiceId) { showToast('Invoice ID not found.', 'error'); return; }
    window.open('export_invoice_pdf.php?id=' + encodeURIComponent(currentInvoiceId), '_blank');
  });
  // Mail
  $('#mailInvoiceModalBtn').on('click', function() {
    if (!currentInvoiceId) { showToast('Invoice ID not found.', 'error'); return; }
    var btn = $(this);
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Sending...');
    $.post('ajax/send_invoice_mail.php', { invoice_id: currentInvoiceId }, function(res) {
      btn.prop('disabled', false).html('<i class="fas fa-envelope"></i> Mail');
      let msg = res && res.message ? res.message : (typeof res === 'string' ? res : 'Mail sent.');
      let type = (res && res.success) ? 'success' : 'info';
      showToast(msg, type);
    }).fail(function(xhr) {
      btn.prop('disabled', false).html('<i class="fas fa-envelope"></i> Mail');
      showToast('Failed to send mail.', 'error');
    });
  });
  // WhatsApp
  $('#whatsappInvoiceModalBtn').on('click', function() {
    if (!currentInvoiceId) { showToast('Invoice ID not found.', 'error'); return; }
    var btn = $(this);
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Sending...');
    $.get('ajax/fetch_customer_contact.php', { invoice_id: currentInvoiceId }, function(res) {
      btn.prop('disabled', false).html('<i class="fab fa-whatsapp"></i> WhatsApp');
      if (res && res.phone) {
        // Add +91 country code if not present
        var phone = res.phone;
        if (!phone.startsWith('91')) {
          phone = '91' + phone.replace(/^0+/, '');
        }
        // Compose a clean WhatsApp message with line breaks
        var invoiceNo = $('#viewInvoiceModalBody').find('b:contains("Invoice No")').parent().text().replace('Invoice No:', '').trim();
        var invoiceDate = $('#viewInvoiceModalBody').find('b:contains("Invoice Date")').parent().text().replace('Invoice Date:', '').trim();
        var customerName = res.name || '';
        var pdfLink = window.location.origin + '/alok-crm/export_invoice_pdf.php?id=' + encodeURIComponent(currentInvoiceId);
        var msg = '';
        msg += 'Courier Invoice\n';
        msg += '\n';
        msg += 'To: ' + customerName + '\n';
        msg += 'Invoice No: ' + invoiceNo + '\n';
        msg += 'Date: ' + invoiceDate + '\n';
        msg += '\n';
        msg += 'You can download your invoice as PDF here:' + '\n' + pdfLink + '\n';
        msg += '\n';
        msg += 'Thank you for your business!';
        var text = encodeURIComponent(msg);
        window.open('https://wa.me/' + phone + '?text=' + text, '_blank');
        showToast('WhatsApp message window opened.', 'success');
      } else {
        showToast('Customer phone not found.', 'error');
      }
    }, 'json').fail(function() {
      btn.prop('disabled', false).html('<i class="fab fa-whatsapp"></i> WhatsApp');
      showToast('Failed to fetch customer phone.', 'error');
    });
  });
});
</script>

<?php include 'inc/footer.php'; ?>