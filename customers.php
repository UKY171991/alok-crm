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
        <h2>Customer Management</h2>

        <!-- Shared Form -->
        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title" id="formTitle">Add New Customer</h3></div>
            <div class="card-body">
                <form id="customerForm">
                    <input type="hidden" name="id" id="customer_id">
                    <div class="row">
                        <div class="col-md-6"><label>Name</label><input type="text" name="name" id="name" class="form-control" required></div>
                        <div class="col-md-6"><label>Address</label><textarea name="address" id="address" class="form-control" rows="2" required></textarea></div>
                        <div class="col-md-4"><label>Phone</label><input type="text" name="phone" id="phone" class="form-control" required></div>
                        <div class="col-md-4"><label>Email</label><input type="email" name="email" id="email" class="form-control"></div>
                        <div class="col-md-4"><label>GST No</label><input type="text" name="gst_no" id="gst_no" class="form-control"></div>
                        <div class="col-md-4"><label>HSN Code</label><input type="text" name="hsn_code" id="hsn_code" class="form-control"></div>
                        <div class="col-md-4"><label>PAN No</label><input type="text" name="pan_no" id="pan_no" class="form-control"></div>
                        <div class="col-md-4"><label>CIN No</label><input type="text" name="cin_no" id="cin_no" class="form-control"></div>
                        <div class="col-md-4"><label>Aadhaar No</label><input type="text" name="aadhaar_no" id="aadhaar_no" class="form-control"></div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3" id="submitBtn">Add Customer</button>
                </form>
                <div id="message" class="mt-2"></div>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="card">
            <div class="card-header"><h3 class="card-title">Customers List</h3></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th><th>Name</th><th>Address</th><th>Phone</th><th>Email</th>
                                <th>GST</th><th>HSN</th><th>PAN</th><th>CIN</th><th>Aadhaar</th><th>Created</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="customerTableBody"></tbody>
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
    function loadCustomers() {
        $.get("fetch_customers.php", function (data) {
            $("#customerTableBody").html(data);
        });
    }

    loadCustomers();

    $("#customerForm").on("submit", function (e) {
        e.preventDefault();
        $.post("save_customer.php", $(this).serialize(), function (res) {
            $("#message").html(res);
            $("#customerForm")[0].reset();
            $("#formTitle").text("Add New Customer");
            $("#submitBtn").text("Add Customer");
            $("#customer_id").val('');
            loadCustomers();
        });
    });

    $(document).on("click", ".edit-btn", function () {
        $("#formTitle").text("Update Customer");
        $("#submitBtn").text("Update Customer");

        $("#customer_id").val($(this).data("id"));
        $("#name").val($(this).data("name"));
        $("#address").val($(this).data("address"));
        $("#phone").val($(this).data("phone"));
        $("#email").val($(this).data("email"));
        $("#gst_no").val($(this).data("gst"));
        $("#hsn_code").val($(this).data("hsn"));
        $("#pan_no").val($(this).data("pan"));
        $("#cin_no").val($(this).data("cin"));
        $("#aadhaar_no").val($(this).data("aadhaar"));
    });

    $(document).on("click", ".delete-btn", function () {
        if (confirm("Are you sure you want to delete this customer?")) {
            $.post("delete_customer.php", { id: $(this).data("id") }, function () {
                loadCustomers();
            });
        }
    });
});
</script>
