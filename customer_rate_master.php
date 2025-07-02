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
        
        <!-- Customer Rate Master Interface -->
        <div class="rate-master-container">
            <div class="rate-master-header">
                <h2 class="rate-master-title">CUSTOMER RATE MASTER</h2>
            </div>
            
            <!-- Filter Section -->
            <div class="rate-filter-section">
                <div class="filter-row">
                    <!-- Rate Option -->
                    <div class="filter-group">
                        <label>Rate Option</label>
                        <div class="filter-subgroup">
                            <label for="customerSelect">Customer</label>
                            <select id="customerSelect" class="rate-select">
                                <option value="">Select Customer</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Mode / Consignment Type -->
                    <div class="filter-group">
                        <label>Mode / Consignment Type</label>
                        <div class="filter-subgroup">
                            <label for="modeSelect">Mode</label>
                            <select id="modeSelect" class="rate-select">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div class="filter-subgroup">
                            <label for="consignmentSelect">Consignment Type</label>
                            <select id="consignmentSelect" class="rate-select">
                                <option value="">All</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Rate Destination -->
                    <div class="filter-group">
                        <label>Rate Destination</label>
                        <div class="filter-subgroup">
                            <label for="zoneSelect">Zone Wise</label>
                            <select id="zoneSelect" class="rate-select">
                                <option value="">CENTRAL</option>
                            </select>
                        </div>
                        <div class="filter-subgroup">
                            <label for="stateSelect">State Wise</label>
                            <select id="stateSelect" class="rate-select">
                                <option value="">MADHYA PRADESH</option>
                            </select>
                        </div>
                        <div class="filter-subgroup">
                            <label for="citySelect">City Wise</label>
                            <select id="citySelect" class="rate-select">
                                <option value="">All</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Rate Section -->
            <div class="rate-input-section">
                <div class="input-row">
                    <div class="input-group-rate">
                        <label for="fromWeight">From Weight</label>
                        <input type="number" id="fromWeight" class="rate-input" step="0.001" value="0.000">
                    </div>
                    <div class="input-group-rate">
                        <label for="toWeight">To Weight</label>
                        <input type="number" id="toWeight" class="rate-input" step="0.001" value="0.000">
                    </div>
                    <div class="input-group-rate">
                        <label for="rate">Rate</label>
                        <input type="number" id="rate" class="rate-input" step="0.01" value="0.00">
                    </div>
                    <div class="input-group-rate">
                        <label for="additionalWeightKg">Addnl Weight(KG)</label>
                        <input type="number" id="additionalWeightKg" class="rate-input" step="0.001" value="0.000">
                    </div>
                    <div class="input-group-rate">
                        <label for="additionalWeight">Addnl Weight</label>
                        <input type="number" id="additionalWeight" class="rate-input" step="0.001" value="0.000">
                    </div>
                    <div class="input-group-rate">
                        <label for="additionalRate">Addnl Rate</label>
                        <input type="number" id="additionalRate" class="rate-input" step="0.01" value="0.00">
                    </div>
                    <div class="input-group-rate">
                        <button type="button" id="addRateBtn" class="add-rate-btn">Add</button>
                    </div>
                </div>
            </div>

            <!-- Rate Master Entry Table -->
            <div class="rate-table-container">
                <div class="table-header">Rate Master Entry</div>
                <table class="rate-table" id="rateTable">
                    <thead>
                        <tr>
                            <th>Mode</th>
                            <th>Con Type</th>
                            <th>Zone</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Wt. From</th>
                            <th>Wt To</th>
                            <th>Rate</th>
                            <th>Addnl</th>
                            <th>ADDNL Wt</th>
                            <th>ADDNL Rate</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody id="rateTableBody">
                        <!-- Rate data will be loaded here -->
                    </tbody>
                </table>
            </div>

            <!-- Print Rate Chart Button -->
            <div class="rate-actions">
                <button type="button" id="printRateChart" class="print-btn">Print Rate Chart</button>
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
        // Function to show toaster alert
        function showToasterAlert(message, type = 'success') {
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

        // Load dropdown options
        function loadDropdownOptions() {
            // Load customers
            $.ajax({
                url: 'fetch_customers_rate.php',
                method: 'GET',
                data: { type: 'customers' },
                success: function (data) {
                    $('#customerSelect').html('<option value="">Select Customer</option>' + data);
                },
                error: function() {
                    showToasterAlert('Error loading customers.', 'danger');
                }
            });

            // Load modes
            $.ajax({
                url: 'fetch_customers_rate.php',
                method: 'GET',
                data: { type: 'modes' },
                success: function (data) {
                    $('#modeSelect').html('<option value="">All</option>' + data);
                },
                error: function() {
                    showToasterAlert('Error loading modes.', 'danger');
                }
            });

            // Load consignment types
            $.ajax({
                url: 'fetch_customers_rate.php',
                method: 'GET',
                data: { type: 'consignment_types' },
                success: function (data) {
                    $('#consignmentSelect').html('<option value="">All</option>' + data);
                },
                error: function() {
                    showToasterAlert('Error loading consignment types.', 'danger');
                }
            });

            // Load zones
            $.ajax({
                url: 'fetch_customers_rate.php',
                method: 'GET',
                data: { type: 'zones' },
                success: function (data) {
                    $('#zoneSelect').html('<option value="">Select Zone</option>' + data);
                },
                error: function() {
                    showToasterAlert('Error loading zones.', 'danger');
                }
            });
        }

        // Load customer rates
        function loadCustomerRates() {
            const customerId = $('#customerSelect').val();
            if (!customerId) {
                $('#rateTableBody').html('<tr><td colspan="13" style="text-align:center;">Please select a customer first.</td></tr>');
                return;
            }

            const filters = {
                customer_id: customerId,
                mode: $('#modeSelect').val(),
                consignment_type: $('#consignmentSelect').val(),
                zone_wise: $('#zoneSelect').val(),
                state_wise: $('#stateSelect').val(),
                city_wise: $('#citySelect').val()
            };

            $.ajax({
                url: 'fetch_customer_rates.php',
                method: 'GET',
                data: filters,
                success: function (data) {
                    $('#rateTableBody').html(data);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    showToasterAlert('Error loading customer rates.', 'danger');
                    $('#rateTableBody').html('<tr><td colspan="13" style="text-align:center;">Error loading rates.</td></tr>');
                }
            });
        }

        // Initialize page
        loadDropdownOptions();

        // Handle customer selection change
        $('#customerSelect').on('change', function() {
            loadCustomerRates();
        });

        // Handle filter changes
        $('#modeSelect, #consignmentSelect, #zoneSelect, #stateSelect, #citySelect').on('change', function() {
            loadCustomerRates();
        });

        // Handle Add Rate button click
        $('#addRateBtn').on('click', function () {
            const customerId = $('#customerSelect').val();
            if (!customerId) {
                showToasterAlert('Please select a customer first.', 'warning');
                return;
            }

            const rateData = {
                customer_id: customerId,
                mode: $('#modeSelect').val() || 'General',
                consignment_type: $('#consignmentSelect').val() || 'General',
                zone_wise: $('#zoneSelect').val() || 'General',
                state_wise: $('#stateSelect').val() || 'General',
                city_wise: $('#citySelect').val() || 'General',
                from_weight: $('#fromWeight').val(),
                to_weight: $('#toWeight').val(),
                rate: $('#rate').val(),
                additional_weight_kg: $('#additionalWeightKg').val(),
                additional_weight: $('#additionalWeight').val(),
                additional_rate: $('#additionalRate').val()
            };

            const $btn = $(this);
            $btn.prop('disabled', true).text('Adding...');
            
            $.ajax({
                url: 'add_customer_rate.php',
                method: 'POST',
                data: rateData,
                success: function (response) {
                    // Reset form
                    $('#fromWeight, #toWeight, #rate, #additionalWeightKg, #additionalWeight, #additionalRate').val('0.000');
                    $('#rate, #additionalRate').val('0.00');
                    
                    $btn.prop('disabled', false).text('Add');
                    loadCustomerRates();
                    showToasterAlert(response, 'success');
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).text('Add');
                    console.error('Add Rate Error:', xhr.responseText);
                    showToasterAlert('Error adding rate: ' + (xhr.responseText || 'Unknown error'), 'danger');
                }
            });
        });

        // Handle edit rate
        $(document).on('click', '.edit-rate-btn', function () {
            const id = $(this).data('id');
            const rate = $(this).data('rate');
            
            const newRate = prompt('Edit Rate:', rate);
            if (newRate !== null && newRate.trim() !== '' && newRate !== rate) {
                $.ajax({
                    url: 'edit_customer_rate.php',
                    method: 'POST',
                    data: { id: id, rate: newRate.trim() },
                    success: function (response) {
                        loadCustomerRates();
                        showToasterAlert(response, 'success');
                    },
                    error: function(xhr, status, error) {
                        console.error('Edit Rate Error:', xhr.responseText);
                        showToasterAlert('Error updating rate: ' + (xhr.responseText || 'Unknown error'), 'danger');
                    }
                });
            }
        });

        // Handle delete rate
        $(document).on('click', '.delete-rate-btn', function () {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this rate?')) {
                $.ajax({
                    url: 'delete_customer_rate.php',
                    method: 'POST',
                    data: { id: id },
                    success: function (response) {
                        loadCustomerRates();
                        showToasterAlert(response, 'success');
                    },
                    error: function(xhr, status, error) {
                        console.error('Delete Rate Error:', xhr.responseText);
                        showToasterAlert('Error deleting rate: ' + (xhr.responseText || 'Unknown error'), 'danger');
                    }
                });
            }
        });

        // Handle Print Rate Chart
        $('#printRateChart').on('click', function() {
            const customerId = $('#customerSelect').val();
            if (!customerId) {
                showToasterAlert('Please select a customer first.', 'warning');
                return;
            }
            
            window.open('print_rate_chart.php?customer_id=' + customerId, '_blank');
            showToasterAlert('Opening print preview...', 'info');
        });
    });
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    /* Customer Rate Master Styling */
    .rate-master-container {
        max-width: 100%;
        margin: 20px auto;
        background: #87CEEB;
        border: 2px solid #4169E1;
        border-radius: 8px;
        padding: 0;
        font-family: Arial, sans-serif;
    }
    
    .rate-master-header {
        background: #B22222;
        color: white;
        text-align: center;
        padding: 8px;
        border-bottom: 2px solid #4169E1;
    }
    
    .rate-master-title {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
        letter-spacing: 1px;
    }
    
    .rate-filter-section {
        padding: 15px;
        background: #87CEEB;
        border-bottom: 1px solid #4169E1;
    }
    
    .filter-row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .filter-group {
        flex: 1;
        min-width: 200px;
    }
    
    .filter-group > label {
        display: block;
        font-weight: bold;
        color: #000;
        margin-bottom: 5px;
        font-size: 12px;
    }
    
    .filter-subgroup {
        margin-bottom: 8px;
    }
    
    .filter-subgroup label {
        display: block;
        font-weight: normal;
        color: #000;
        margin-bottom: 2px;
        font-size: 11px;
    }
    
    .rate-select {
        width: 100%;
        padding: 3px 6px;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 12px;
        background: white;
    }
    
    .rate-input-section {
        padding: 10px 15px;
        background: #87CEEB;
        border-bottom: 1px solid #4169E1;
    }
    
    .input-row {
        display: flex;
        gap: 10px;
        align-items: end;
        flex-wrap: wrap;
    }
    
    .input-group-rate {
        flex: 1;
        min-width: 80px;
    }
    
    .input-group-rate label {
        display: block;
        font-weight: bold;
        color: #000;
        margin-bottom: 2px;
        font-size: 10px;
    }
    
    .rate-input {
        width: 100%;
        padding: 3px 6px;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 12px;
        text-align: center;
    }
    
    .add-rate-btn {
        background: #4169E1;
        color: white;
        border: 1px solid #000;
        padding: 6px 12px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
        margin-top: 15px;
    }
    
    .add-rate-btn:hover {
        background: #1E90FF;
    }
    
    .rate-table-container {
        background: white;
        margin: 0 15px 15px 15px;
        border: 1px solid #000;
    }
    
    .table-header {
        background: #E6E6FA;
        padding: 5px 10px;
        font-weight: bold;
        border-bottom: 1px solid #000;
        font-size: 12px;
    }
    
    .rate-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }
    
    .rate-table th {
        background: #E6E6FA;
        border: 1px solid #000;
        padding: 4px 6px;
        text-align: center;
        font-weight: bold;
        font-size: 10px;
    }
    
    .rate-table td {
        border: 1px solid #000;
        padding: 3px 6px;
        background: white;
        text-align: center;
        font-size: 10px;
    }
    
    .rate-table tr:nth-child(even) td {
        background: #F0F8FF;
    }
    
    .edit-rate-btn {
        background: #FFA500;
        color: white;
        border: 1px solid #000;
        padding: 1px 6px;
        border-radius: 2px;
        cursor: pointer;
        font-size: 9px;
    }
    
    .edit-rate-btn:hover {
        background: #FF8C00;
    }
    
    .delete-rate-btn {
        background: #DC143C;
        color: white;
        border: 1px solid #000;
        padding: 1px 6px;
        border-radius: 2px;
        cursor: pointer;
        font-size: 9px;
    }
    
    .delete-rate-btn:hover {
        background: #B22222;
    }
    
    .rate-actions {
        padding: 10px 15px;
        background: #87CEEB;
        text-align: center;
    }
    
    .print-btn {
        background: #4169E1;
        color: white;
        border: 1px solid #000;
        padding: 6px 20px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
    }
    
    .print-btn:hover {
        background: #1E90FF;
    }
</style>
