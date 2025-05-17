<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include 'inc/db.php';
include 'inc/header.php';
include 'inc/sidebar.php';

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
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="invoiceModalLabel">Add New Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="invoiceForm">
                        <div class="modal-body">
                            <input type="hidden" name="id" id="invoice_id">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Invoice No</label>
                                    <input type="text" name="invoice_no" id="invoice_no" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Customer</label>
                                    <select name="customer_id" id="customer_id" class="form-control" required>
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
                                <div class="col-md-6">
                                    <label>Invoice Date</label>
                                    <input type="date" name="invoice_date" id="invoice_date" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Destination</label>
                                    <select name="destination" id="destination" class="form-control" required>
                                        <option value="">Select Destination</option>
                                        <?php
                                        $query = "SELECT name FROM destinations ORDER BY name ASC";
                                        $result = $conn->query($query);
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Total Amount</label>
                                    <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>GST Amount</label>
                                    <input type="number" step="0.01" name="gst_amount" id="gst_amount" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>Grand Total</label>
                                    <input type="number" step="0.01" name="grand_total" id="grand_total" class="form-control" required>
                                </div>
                            </div>
                            <div id="message" class="mt-2"></div>
                        </div>
                        <div class="modal-footer">
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
                                <th>Destination</th><th>Total Amount</th><th>GST Amount</th><th>Grand Total</th><th>Created</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

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
        $("#invoice_no").val($(this).data("invoice_no"));
        $("#customer_id").val($(this).data("customer_id"));
        $("#invoice_date").val($(this).data("invoice_date"));
        $("#destination").val($(this).data("destination"));
        $("#total_amount").val($(this).data("total_amount"));
        $("#gst_amount").val($(this).data("gst_amount"));
        $("#grand_total").val($(this).data("grand_total"));
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
        $("#invoice_no").val("<?php echo $invoice_no; ?>");
    });

});
</script>