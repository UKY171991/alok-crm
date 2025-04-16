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
        <h2>Invoices Management</h2>

        <!-- Add/Edit Invoice Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title" id="formTitle">Add New Invoice</h3>
            </div>
            <div class="card-body">
                <form id="invoiceForm">
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
                    <button type="submit" class="btn btn-primary mt-3" id="submitBtn">Add Invoice</button>
                </form>
                <div id="message" class="mt-2"></div>
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

<style>
    table th, table td {
        white-space: nowrap;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    function loadInvoices() {
        $.get("fetch_invoices.php", function (data) {
            $("#invoiceTableBody").html(data);
        });
    }

    loadInvoices();

    $("#invoiceForm").on("submit", function (e) {
        e.preventDefault();
        $.post("save_invoice.php", $(this).serialize(), function (res) {
            $("#message").html(res);
            $("#invoiceForm")[0].reset();
            $("#formTitle").text("Add New Invoice");
            $("#submitBtn").text("Add Invoice");
            $("#invoice_id").val('');
            loadInvoices();
        });
    });

    $(document).on("click", ".edit-btn", function () {
        $("#formTitle").text("Update Invoice");
        $("#submitBtn").text("Update Invoice");
        $("#invoice_id").val($(this).data("id"));
        $("#invoice_no").val($(this).data("invoice_no"));
        $("#customer_id").val($(this).data("customer_id"));
        $("#invoice_date").val($(this).data("invoice_date"));
        $("#destination").val($(this).data("destination"));
        $("#total_amount").val($(this).data("total_amount"));
        $("#gst_amount").val($(this).data("gst_amount"));
        $("#grand_total").val($(this).data("grand_total"));
    });

    $(document).on("click", ".delete-btn", function () {
        const id = $(this).data("id");

        if (confirm("Are you sure you want to delete this invoice?")) {
            $.post("delete_invoice.php", { id: id }, function (response) {
                // Optional: alert(response);
                loadInvoices(); // refresh table
            });
        }
    });

    $(document).ready(function () {
        $("#invoice_no").val("<?php echo $invoice_no; ?>");
    });

});
</script>