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
        <h2>Rates Management (AJAX)</h2>

        <!-- Add Rate Form -->
        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title">Add New Rate</h3></div>
            <div class="card-body">
                <form id="rateForm">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Destination</label>
                            <input type="text" name="destination" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Type</label>
                            <select name="type" class="form-control" required>
                                <option value="Normal">Normal</option>
                                <option value="Premium">Premium</option>
                                <option value="Bulk_Surface">Bulk Surface</option>
                                <option value="Bulk_Air">Bulk Air</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Weight Category</label>
                            <input type="text" name="weight_category" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Rate</label>
                            <input type="number" step="0.01" name="rate" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Add Rate</button>
                </form>
                <div id="message" class="mt-2"></div>
            </div>
        </div>

        <!-- Rates List -->
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

<!-- ✅ Edit Modal -->
<div class="modal fade" id="editRateModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editRateForm">
        <div class="modal-header">
          <h5 class="modal-title">Edit Rate</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row">
            <input type="hidden" name="id" id="edit_id">
            <div class="col-md-6">
                <label>Destination</label>
                <input type="text" name="destination" id="edit_destination" class="form-control">
            </div>
            <div class="col-md-6">
                <label>Type</label>
                <select name="type" id="edit_type" class="form-control">
                    <option value="Normal">Normal</option>
                    <option value="Premium">Premium</option>
                    <option value="Bulk_Surface">Bulk Surface</option>
                    <option value="Bulk_Air">Bulk Air</option>
                </select>
            </div>
            <div class="col-md-6 mt-2">
                <label>Weight Category</label>
                <input type="text" name="weight_category" id="edit_weight" class="form-control">
            </div>
            <div class="col-md-6 mt-2">
                <label>Rate</label>
                <input type="number" step="0.01" name="rate" id="edit_rate" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Update</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'inc/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    function loadRates() {
        $.get("fetch_rates.php", function(data) {
            $("#rateTableBody").html(data);
        });
    }
    loadRates();

    $("#rateForm").on("submit", function (e) {
        e.preventDefault();
        $.post("add_rate_ajax.php", $(this).serialize(), function (res) {
            $("#message").html(res);
            $("#rateForm")[0].reset();
            loadRates();
        });
    });

    // ✅ Edit button click
    $(document).on("click", ".edit-btn", function () {
        let row = $(this).data();
        $("#edit_id").val(row.id);
        $("#edit_destination").val(row.destination);
        $("#edit_type").val(row.type);
        $("#edit_weight").val(row.weight);
        $("#edit_rate").val(row.rate);
        $("#editRateModal").modal("show");
    });

    // ✅ Update form submit
    $("#editRateForm").on("submit", function (e) {
        e.preventDefault();
        $.post("update_rate.php", $(this).serialize(), function (res) {
            $("#editRateModal").modal("hide");
            loadRates();
        });
    });

    // ✅ Delete
    $(document).on("click", ".delete-btn", function () {
        if (confirm("Are you sure to delete this?")) {
            let id = $(this).data("id");
            $.post("delete_rate.php", { id }, function () {
                loadRates();
            });
        }
    });
});
</script>
