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
        <div id="adminlte-alert" class="alert alert-success alert-dismissible fade show d-none shadow-sm rounded-3 animate__animated animate__fadeInDown" role="alert" style="z-index:1055;position:fixed;top:20px;right:20px;min-width:300px;">
            <span id="adminlte-alert-message"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 fw-bold"><i class="fas fa-map-marker-alt text-primary me-2"></i>Destinations Management</h2>
            <button class="btn btn-primary btn-lg rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addDestinationModal">
                <i class="fas fa-plus me-1"></i> Add Destination
            </button>
        </div>
        <div class="card shadow rounded-4 mb-4">
            <div class="card-body pb-2">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="searchDestination" class="form-control form-control-lg rounded-pill" placeholder="Search destinations...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle table-bordered border-light mb-0" id="destinationTable">
                        <thead class="table-primary">
                            <tr>
                                <th style="width:60px;">ID</th>
                                <th>Name</th>
                                <th style="width:180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="destinationTableBody">
                            <!-- Data will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Pagination Controls -->
        <nav aria-label="Destinations pagination" class="d-flex justify-content-center mb-4">
            <ul class="pagination pagination-lg" id="destinationPagination"></ul>
        </nav>
        <!-- Add Destination Modal -->
        <div class="modal fade animate__animated animate__fadeInDown" id="addDestinationModal" tabindex="-1" aria-labelledby="addDestinationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-4 shadow-lg">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title" id="addDestinationModalLabel"><i class="fas fa-plus me-2"></i>Add New Destination</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="addDestinationForm">
                        <div class="modal-body">
                            <div class="form-floating mb-3">
                                <input type="text" name="name" id="name" class="form-control" placeholder="Destination Name" required>
                                <label for="name">Destination Name</label>
                            </div>
                        </div>
                        <div class="modal-footer bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Add Destination</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Edit Destination Modal -->
        <div class="modal fade animate__animated animate__fadeInDown" id="editDestinationModal" tabindex="-1" aria-labelledby="editDestinationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-4 shadow-lg">
                    <div class="modal-header bg-warning text-dark rounded-top-4">
                        <h5 class="modal-title" id="editDestinationModalLabel"><i class="fas fa-edit me-2"></i>Edit Destination</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editDestinationForm">
                            <input type="hidden" id="editId">
                            <div class="form-floating mb-3">
                                <input type="text" id="editName" class="form-control" placeholder="Destination Name" required>
                                <label for="editName">Destination Name</label>
                            </div>
                            <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i> Update Destination</button>
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
<script src="https://kit.fontawesome.com/2c36e9b7b1.js" crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
        // Function to render pagination controls
        function renderPagination(total, page, per_page) {
            var totalPages = Math.ceil(total / per_page);
            var $pagination = $('#destinationPagination');
            $pagination.empty();
            if (totalPages <= 1) return;
            var prevDisabled = (page <= 1) ? 'disabled' : '';
            var nextDisabled = (page >= totalPages) ? 'disabled' : '';
            $pagination.append('<li class="page-item ' + prevDisabled + '"><a class="page-link" href="#" data-page="' + (page - 1) + '">Previous</a></li>');
            for (var i = 1; i <= totalPages; i++) {
                var active = (i === page) ? 'active' : '';
                $pagination.append('<li class="page-item ' + active + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>');
            }
            $pagination.append('<li class="page-item ' + nextDisabled + '"><a class="page-link" href="#" data-page="' + (page + 1) + '">Next</a></li>');
        }
        // Modified loadDestinations to accept page param
        function loadDestinations(page = 1, per_page = 10) {
            $.ajax({
                url: 'fetch_destinations.php',
                method: 'GET',
                data: { page: page, per_page: per_page },
                success: function (data) {
                    $('#destinationTableBody').html(data);
                    // Read pagination info from hidden row
                    var $info = $('#pagination-info');
                    if ($info.length) {
                        var total = parseInt($info.data('total'));
                        var page = parseInt($info.data('page'));
                        var per_page = parseInt($info.data('per_page'));
                        renderPagination(total, page, per_page);
                    } else {
                        $('#destinationPagination').empty();
                    }
                }
            });
        }
        // Handle pagination click
        $(document).on('click', '#destinationPagination .page-link', function(e) {
            e.preventDefault();
            var page = parseInt($(this).data('page'));
            if (!isNaN(page) && page > 0) {
                loadDestinations(page);
            }
        });

        // Function to show AdminLTE alert
        function showAdminLTEAlert(message, type = 'success') {
            const alertDiv = $('#adminlte-alert');
            alertDiv.removeClass('alert-success alert-danger alert-warning alert-info d-none')
                .addClass('alert-' + type)
                .addClass('show');
            $('#adminlte-alert-message').text(message);
            alertDiv.removeClass('d-none');
            setTimeout(() => {
                alertDiv.alert('close');
            }, 3000);
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
                    showAdminLTEAlert(response, 'success');
                },
                error: function() {
                    $btn.prop('disabled', false).text('Add Destination');
                    showAdminLTEAlert('AJAX error.', 'danger');
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
                        showAdminLTEAlert(response, 'success'); // Show success message
                    },
                    error: function() {
                        showAdminLTEAlert('AJAX error.', 'danger');
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
                    showAdminLTEAlert(response, 'success'); // Show success message
                },
                error: function () {
                    showAdminLTEAlert('AJAX error.', 'danger');
                }
            });
        });

        // Search/filter destinations
        $('#searchDestination').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#destinationTableBody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    #destinationTable thead th { font-size: 1.1rem; }
    #destinationTable tbody td { font-size: 1.05rem; }
    .modal-content { border-radius: 1.2rem; }
    .form-floating > label { left: 1.2rem; }
    .btn { letter-spacing: 0.03em; }
    .table-primary th { background: #e9f5ff !important; }
    .modal-header.bg-primary { background: #2563eb !important; }
    .modal-header.bg-warning { background: #ffe066 !important; color: #333 !important; }
    .modal-header i { font-size: 1.2em; }
    .shadow-lg { box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.12)!important; }
    .rounded-4 { border-radius: 1.2rem!important; }
    .rounded-top-4 { border-top-left-radius: 1.2rem!important; border-top-right-radius: 1.2rem!important; }
    .rounded-bottom-4 { border-bottom-left-radius: 1.2rem!important; border-bottom-right-radius: 1.2rem!important; }
</style>