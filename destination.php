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
        <!-- Zone Master Interface -->
        <div class="zone-master-container">
            <div class="zone-master-header">
                <h2 class="zone-master-title">ZONE MASTER</h2>
            </div>
            
            <div class="zone-input-section">
                <div class="input-group">
                    <label for="zoneName">Zone</label>
                    <input type="text" id="zoneName" class="zone-input" placeholder="">
                    <label for="zoneType">Type</label>
                    <select id="zoneType" class="zone-select">
                        <option value="ZONE">ZONE</option>
                        <option value="CENTRAL">CENTRAL</option>
                        <option value="EAST">EAST</option>
                        <option value="NORTH">NORTH</option>
                        <option value="SOUTH">SOUTH</option>
                        <option value="WEST">WEST</option>
                    </select>
                    <button type="button" id="addZoneBtn" class="add-btn">Add</button>
                </div>
            </div>

            <div class="zone-table-container">
                <table class="zone-table" id="zoneTable">
                    <thead>
                        <tr>
                            <th>Zone</th>
                            <th>Type</th>
                            <th>Active</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody id="zoneTableBody">
                        <!-- Zone data will be loaded here -->
                    </tbody>
                </table>
            </div>

            <div class="zone-search-section">
                <div class="search-group">
                    <label for="findZone">Find Zone</label>
                    <input type="text" id="findZone" class="zone-search-input" placeholder="">
                    <button type="button" id="findZoneBtn" class="find-btn">Find</button>
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
        // Initialize destination management loader
        if (typeof showLoader !== 'undefined') {
            showLoader('Loading Destination Management', 'Setting up zones and destinations...');
            setTimeout(() => hideLoader(), 1000);
        }
        
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

        // Load zones on page load
        function loadZones() {
            $.ajax({
                url: 'fetch_destinations.php',
                method: 'GET',
                success: function (data) {
                    $('#zoneTableBody').html(data);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    let errorMsg = 'Error loading zones.';
                    if (xhr.status === 403) {
                        errorMsg = 'Access denied. Please check your login status.';
                    } else if (xhr.status === 500) {
                        errorMsg = 'Server error. Please check the database connection.';
                    } else if (xhr.status === 404) {
                        errorMsg = 'fetch_destinations.php file not found.';
                    }
                    showAdminLTEAlert(errorMsg, 'danger');
                    $('#zoneTableBody').html('<tr><td colspan="4" style="text-align:center;">' + errorMsg + '</td></tr>');
                }
            });
        }

        // Load zones initially
        loadZones();

        // Handle Add Zone button click
        $('#addZoneBtn').on('click', function () {
            const zoneName = $('#zoneName').val().trim();
            const zoneType = $('#zoneType').val();
            
            if (!zoneName) {
                showAdminLTEAlert('Please enter a zone name.', 'warning');
                return;
            }

            const $btn = $(this);
            $btn.prop('disabled', true).text('Adding...');
            
            $.ajax({
                url: 'add_destination.php',
                method: 'POST',
                data: { name: zoneName, type: zoneType },
                success: function (response) {
                    $('#zoneName').val('');
                    $('#zoneType').val('ZONE'); // Reset to default
                    $btn.prop('disabled', false).text('Add');
                    loadZones();
                    showAdminLTEAlert(response, 'success');
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).text('Add');
                    console.error('Add Zone Error:', xhr.responseText);
                    showAdminLTEAlert('Error adding zone: ' + (xhr.responseText || 'Unknown error'), 'danger');
                }
            });
        });

        // Handle edit action
        $(document).on('click', '.edit-zone-btn', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const type = $(this).data('type');
            
            // Create a more sophisticated edit dialog
            const newName = prompt('Edit Zone Name:', name);
            if (newName === null || newName.trim() === '') return;
            
            const newType = prompt('Edit Zone Type (ZONE, CENTRAL, EAST, NORTH, SOUTH, WEST):', type);
            if (newType === null || newType.trim() === '') return;
            
            // Validate type
            const validTypes = ['ZONE', 'CENTRAL', 'EAST', 'NORTH', 'SOUTH', 'WEST'];
            if (!validTypes.includes(newType.toUpperCase())) {
                showAdminLTEAlert('Invalid type. Valid types: ' + validTypes.join(', '), 'warning');
                return;
            }
            
            if (newName.trim() !== name || newType.toUpperCase() !== type) {
                $.ajax({
                    url: 'edit_destination.php',
                    method: 'POST',
                    data: { id: id, name: newName.trim(), type: newType.toUpperCase() },
                    success: function (response) {
                        loadZones();
                        showAdminLTEAlert(response, 'success');
                    },
                    error: function(xhr, status, error) {
                        console.error('Edit Zone Error:', xhr.responseText);
                        showAdminLTEAlert('Error updating zone: ' + (xhr.responseText || 'Unknown error'), 'danger');
                    }
                });
            }
        });

        // Handle status toggle
        $(document).on('click', '.status-toggle', function () {
            const id = $(this).data('id');
            const currentStatus = $(this).data('status');
            
            $.ajax({
                url: 'update_zone_status.php',
                method: 'POST',
                data: { id: id, status: currentStatus },
                success: function (response) {
                    loadZones(); // Reload zones to show updated status
                    showAdminLTEAlert(response, 'success');
                },
                error: function(xhr, status, error) {
                    console.error('Status Toggle Error:', xhr.responseText);
                    showAdminLTEAlert('Error updating zone status: ' + (xhr.responseText || 'Unknown error'), 'danger');
                }
            });
        });

        // Handle delete action
        $(document).on('click', '.delete-zone-btn', function () {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this zone?')) {
                $.ajax({
                    url: 'delete_destination.php',
                    method: 'POST',
                    data: { id: id },
                    success: function (response) {
                        loadZones();
                        showAdminLTEAlert(response, 'success');
                    },
                    error: function(xhr, status, error) {
                        console.error('Delete Zone Error:', xhr.responseText);
                        showAdminLTEAlert('Error deleting zone: ' + (xhr.responseText || 'Unknown error'), 'danger');
                    }
                });
            }
        });

        // Handle Find Zone button click
        $('#findZoneBtn').on('click', function() {
            const searchValue = $('#findZone').val().toLowerCase();
            if (searchValue.trim() === '') {
                loadZones(); // Show all zones if search is empty
                return;
            }
            
            $('#zoneTableBody tr').each(function() {
                const rowText = $(this).text().toLowerCase();
                if (rowText.indexOf(searchValue) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Allow Enter key to trigger find
        $('#findZone').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                $('#findZoneBtn').click();
            }
        });

        // Allow Enter key to trigger add
        $('#zoneName').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                $('#addZoneBtn').click();
            }
        });
    });
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    /* Zone Master Styling */
    .zone-master-container {
        max-width: 600px;
        margin: 20px auto;
        background: #87CEEB;
        border: 2px solid #4169E1;
        border-radius: 8px;
        padding: 0;
        font-family: Arial, sans-serif;
    }
    
    .zone-master-header {
        background: #B22222;
        color: white;
        text-align: center;
        padding: 8px;
        border-bottom: 2px solid #4169E1;
    }
    
    .zone-master-title {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
        letter-spacing: 1px;
    }
    
    .zone-input-section {
        padding: 15px;
        background: #87CEEB;
    }
    
    .input-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .input-group label {
        font-weight: bold;
        color: #000;
        min-width: 50px;
    }
    
    .zone-input {
        flex: 1;
        padding: 4px 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 14px;
    }
    
    .zone-select {
        padding: 4px 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 14px;
        background: white;
        min-width: 80px;
    }
    
    .add-btn {
        background: #4169E1;
        color: white;
        border: 1px solid #000;
        padding: 4px 12px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 14px;
    }
    
    .add-btn:hover {
        background: #1E90FF;
    }
    
    .zone-table-container {
        background: white;
        margin: 0 15px;
    }
    
    .zone-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    
    .zone-table th {
        background: #E6E6FA;
        border: 1px solid #000;
        padding: 6px 8px;
        text-align: left;
        font-weight: bold;
    }
    
    .zone-table td {
        border: 1px solid #000;
        padding: 4px 8px;
        background: white;
    }
    
    .zone-table tr:nth-child(even) td {
        background: #F0F8FF;
    }
    
    .edit-zone-btn {
        background: #FFA500;
        color: white;
        border: 1px solid #000;
        padding: 2px 8px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
        margin-right: 5px;
    }
    
    .edit-zone-btn:hover {
        background: #FF8C00;
    }
    
    .delete-zone-btn {
        background: #DC143C;
        color: white;
        border: 1px solid #000;
        padding: 2px 8px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
    }
    
    .delete-zone-btn:hover {
        background: #B22222;
    }
    
    .zone-search-section {
        padding: 15px;
        background: #87CEEB;
    }
    
    .search-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .search-group label {
        font-weight: bold;
        color: #000;
        min-width: 80px;
    }
    
    .zone-search-input {
        flex: 1;
        padding: 4px 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 14px;
    }
    
    .find-btn {
        background: #4169E1;
        color: white;
        border: 1px solid #000;
        padding: 4px 12px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 14px;
    }
    
    .find-btn:hover {
        background: #1E90FF;
    }
    
    .active-indicator {
        text-align: center;
    }
    
    .active-checkmark {
        color: #008000;
        font-weight: bold;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 3px;
        transition: background-color 0.2s;
    }
    
    .active-checkmark:hover {
        background-color: #E8F5E8;
    }
    
    .inactive-cross {
        color: #DC143C;
        font-weight: bold;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 3px;
        transition: background-color 0.2s;
    }
    
    .inactive-cross:hover {
        background-color: #FFE8E8;
    }
    
    .status-toggle {
        user-select: none;
    }
</style>