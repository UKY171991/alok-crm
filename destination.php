<?php
session_start();

// Redirect to login.php if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

include 'inc/db.php'; 
include 'inc/header.php';
include 'inc/sidebar.php';
?>
<main class='content-wrapper'>
    <div class='container-fluid p-3'>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Destinations Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDestinationModal">
                Add Destination
            </button>
        </div>

        <!-- Add Destination Modal -->
        <div class="modal fade" id="addDestinationModal" tabindex="-1" aria-labelledby="addDestinationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDestinationModalLabel">Add New Destination</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="addDestinationForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Destination Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Destination</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Destinations List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Destinations List</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="destinationTableBody">
                        <!-- Data will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Destination Modal -->
        <div class="modal fade" id="editDestinationModal" tabindex="-1" aria-labelledby="editDestinationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDestinationModalLabel">Edit Destination</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editDestinationForm">
                            <input type="hidden" id="editId">
                            <div class="form-group">
                                <label for="editName">Destination Name</label>
                                <input type="text" id="editName" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Update Destination</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include 'inc/footer.php'; ?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Function to load destinations
        function loadDestinations() {
            $.ajax({
                url: 'fetch_destinations.php',
                method: 'GET',
                success: function (data) {
                    $('#destinationTableBody').html(data);
                }
            });
        }

        // Load destinations on page load
        loadDestinations();

        // Handle form submission for adding a destination
        $('#addDestinationForm').on('submit', function (e) {
            e.preventDefault();
            const name = $('#name').val();
            const $btn = $(this).find('button[type="submit"]');
            $btn.prop('disabled', true).text('Processing...');
            $.ajax({
                url: 'add_destination.php',
                method: 'POST',
                data: { name: name },
                success: function (response) {
                    $('#name').val('');
                    $('#addDestinationModal').modal('hide');
                    $btn.prop('disabled', false).text('Add Destination');
                    loadDestinations();
                    alert(response);
                },
                error: function() {
                    $btn.prop('disabled', false).text('Add Destination');
                    alert('AJAX error.');
                }
            });
        });

        // Handle delete action
        $(document).on('click', '.delete-btn', function () {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this destination?')) {
                $.ajax({
                    url: 'delete_destination.php',
                    method: 'POST',
                    data: { id: id },
                    success: function (response) {
                        loadDestinations(); // Reload the destinations
                        alert(response); // Show success message
                    }
                });
            }
        });

        // Handle edit button click
        $(document).on('click', '.edit-btn', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');

            console.log("Editing Destination ID:", id, "Name:", name); // Debugging

            $('#editId').val(id);
            $('#editName').val(name);

            $('#editDestinationModal').modal('show');
        });

        // Handle form submission for editing a destination
        $('#editDestinationForm').on('submit', function (e) {
            e.preventDefault();
            const id = $('#editId').val();
            const name = $('#editName').val();

            console.log("Editing Destination:", { id, name }); // Debugging

            $.ajax({
                url: 'edit_destination.php',
                method: 'POST',
                data: { id: id, name: name },
                success: function (response) {
                    console.log("Response:", response); // Debugging
                    $('#editDestinationModal').modal('hide'); // Hide the modal
                    loadDestinations(); // Reload the destinations
                    alert(response); // Show success message
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", error); // Debugging
                }
            });
        });
    });
</script>