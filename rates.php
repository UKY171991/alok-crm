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
        <h2>Rates Management</h2>

        <!-- Shared Add/Update Form -->
        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title" id="formTitle">Add New Rate</h3></div>
            <div class="card-body">
                <form id="rateForm">
                    <input type="hidden" name="id" id="rate_id" value="">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Destination</label>
                            <input type="text" name="destination" id="destination" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="Normal">Normal</option>
                                <option value="Premium">Premium</option>
                                <option value="Bulk_Surface">Bulk Surface</option>
                                <option value="Bulk_Air">Bulk Air</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Weight Category</label>
                            <input type="text" name="weight_category" id="weight_category" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Rate</label>
                            <input type="number" step="0.01" name="rate" id="rate" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" id="submitBtn" class="btn btn-primary mt-3">Add Rate</button>
                </form>
                <div id="message" class="mt-2"></div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-header"><h3 class="card-title">Rates List</h3></div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Destination</th>
                            <th>Type</th>
                            <th>Weight</th>
                            <th>Rate</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="rateTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include 'inc/footer.php'; ?>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    function loadRates() {
        $.get("fetch_rates.php", function (data) {
            $("#rateTableBody").html(data);
        });
    }

    loadRates();

    $("#rateForm").on("submit", function (e) {
        e.preventDefault();
        $.post("save_rate.php", $(this).serialize(), function (res) {
            $("#message").html(res);
            $("#rateForm")[0].reset();
            $("#formTitle").text("Add New Rate");
            $("#submitBtn").text("Add Rate");
            $("#rate_id").val('');
            loadRates();
        });
    });

    $(document).on("click", ".edit-btn", function () {
        $("#formTitle").text("Update Rate");
        $("#submitBtn").text("Update Rate");

        $("#rate_id").val($(this).data("id"));
        $("#destination").val($(this).data("destination"));
        $("#type").val($(this).data("type"));
        $("#weight_category").val($(this).data("weight"));
        $("#rate").val($(this).data("rate"));
    });

    $(document).on("click", ".delete-btn", function () {
        if (confirm("Are you sure you want to delete this rate?")) {
            $.post("delete_rate.php", { id: $(this).data("id") }, function () {
                loadRates();
            });
        }
    });
});
</script>
