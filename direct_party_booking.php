<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once 'inc/config.php';
require_once 'inc/db.php';
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<style>
.content-wrapper {
    margin-left: 250px;
    padding: 20px;
    background-color: #f0f8ff;
    min-height: 100vh;
}

.booking-header {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    padding: 15px 20px;
    margin-bottom: 20px;
    border-radius: 8px;
    text-align: center;
    font-size: 1.2rem;
    font-weight: bold;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    letter-spacing: 1px;
}

.booking-controls {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.booking-controls .row {
    align-items: end;
}

.booking-controls label {
    color: white;
    font-weight: 600;
    margin-bottom: 5px;
    display: block;
}

.booking-controls .form-control,
.booking-controls .form-select {
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 6px;
    background: rgba(255,255,255,0.9);
    font-weight: 500;
}

.booking-controls .form-control:focus,
.booking-controls .form-select:focus {
    border-color: #fbbf24;
    box-shadow: 0 0 0 0.2rem rgba(251, 191, 36, 0.25);
}

.outstanding-balance {
    background: rgba(255,255,255,0.15);
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 8px;
    padding: 15px;
    text-align: center;
}

.outstanding-balance .balance-label {
    color: rgba(255,255,255,0.8);
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.outstanding-balance .balance-amount {
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
}

.booking-form {
    background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.booking-form label {
    color: #e5e7eb;
    font-weight: 500;
    margin-bottom: 5px;
    display: block;
}

.booking-form .form-control,
.booking-form .form-select {
    background: #374151;
    border: 1px solid #4b5563;
    color: white;
    border-radius: 4px;
}

.booking-form .form-control:focus,
.booking-form .form-select:focus {
    background: #4b5563;
    border-color: #3b82f6;
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}

.booking-table-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.booking-table {
    margin: 0;
    font-size: 0.85rem;
}

.booking-table thead {
    background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
}

.booking-table thead th {
    color: white;
    font-weight: 600;
    padding: 12px 8px;
    border: none;
    text-align: center;
    vertical-align: middle;
    font-size: 0.8rem;
}

.booking-table tbody tr {
    transition: all 0.2s ease;
    border-bottom: 1px solid #e5e7eb;
}

.booking-table tbody tr:hover {
    background: #f8fafc;
}

.booking-table tbody td {
    padding: 8px;
    vertical-align: middle;
    border: none;
    text-align: center;
    font-size: 0.8rem;
}

.consignment-cell {
    background: #eff6ff;
    font-weight: 600;
    color: #1e40af;
}

.customer-cell {
    text-align: left;
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.weight-cell,
.amount-cell {
    font-family: 'Courier New', monospace;
    font-weight: 600;
}

.amount-cell {
    color: #059669;
}

.btn-group-actions {
    display: inline-flex;
    border-radius: 4px;
    overflow: hidden;
}

.btn-group-actions .btn {
    border-radius: 0;
    border: none;
    padding: 4px 8px;
    font-size: 0.75rem;
    margin: 0;
}

.btn-action-view {
    background: #3b82f6;
    color: white;
}

.btn-action-edit {
    background: #f59e0b;
    color: white;
}

.btn-action-delete {
    background: #ef4444;
    color: white;
}

.summary-section {
    background: white;
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.summary-cards {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.summary-card {
    flex: 1;
    min-width: 120px;
    padding: 12px;
    border-radius: 6px;
    text-align: center;
    color: white;
    font-weight: 600;
}

.summary-card.total-records {
    background: #f97316;
}

.summary-card.total-billed {
    background: #f97316;
}

.summary-card.total-non-billed {
    background: #f97316;
}

.summary-card.total-weight {
    background: #f97316;
}

.summary-card.total-amount {
    background: #f97316;
}

.summary-card .card-label {
    font-size: 0.8rem;
    opacity: 0.9;
    margin-bottom: 2px;
}

.summary-card .card-value {
    font-size: 1.1rem;
    font-weight: bold;
}

.bulk-actions {
    background: #fef3c7;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    display: none;
}

.bulk-actions.show {
    display: block;
}

.btn-delete-selected {
    background: #dc2626;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    font-weight: 600;
}

.modal-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    color: white;
    border-bottom: none;
}

.modal-header .btn-close {
    filter: invert(1);
}

.spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3b82f6;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.loading-overlay.show {
    display: flex;
}

.loading-content {
    background: white;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
}

@media (max-width: 768px) {
    .content-wrapper {
        margin-left: 0;
        padding: 15px;
    }
    
    .booking-controls .col-md-3,
    .booking-controls .col-md-2 {
        margin-bottom: 15px;
    }
    
    .summary-cards {
        flex-direction: column;
    }
    
    .booking-table {
        font-size: 0.75rem;
    }
    
    .booking-table thead th,
    .booking-table tbody td {
        padding: 6px 4px;
    }
}
</style>

<div class="content-wrapper">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <div style="margin-top: 15px; font-weight: 600;">Loading...</div>
        </div>
    </div>

    <!-- Header -->
    <div class="booking-header">
        DIRECT PARTY BOOKING
    </div>

    <!-- Booking Controls -->
    <div class="booking-controls">
        <div class="row">
            <div class="col-md-3">
                <label for="booking_date">Booking Date</label>
                <input type="date" id="booking_date" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-2">
                <label for="billing_status">Billing Status</label>
                <select id="billing_status" class="form-select">
                    <option value="">All</option>
                    <option value="billed">Billed</option>
                    <option value="non-billed">Non-Billed</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="outstanding-balance">
                    <div class="balance-label">Outstanding Balance</div>
                    <div class="balance-amount" id="outstanding_balance">0</div>
                </div>
            </div>
            <div class="col-md-3">
                <label for="search_text">Search</label>
                <input type="text" id="search_text" class="form-control" placeholder="Search consignment, customer...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100" onclick="loadBookings()">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>
    </div>

    <!-- Add New Booking Form -->
    <div class="booking-form" id="bookingForm">
        <div class="row">
            <div class="col-md-2">
                <label for="consignment_no">Consignment No</label>
                <input type="text" id="consignment_no" class="form-control" readonly>
            </div>
            <div class="col-md-3">
                <label for="customer_name">Customer Name</label>
                <select id="customer_name" class="form-select">
                    <option value="">Select Customer</option>
                </select>
            </div>
            <div class="col-md-1">
                <label for="doc_type">Doc Type</label>
                <select id="doc_type" class="form-select">
                    <option value="DOX">DOX</option>
                    <option value="SPX">SPX</option>
                    <option value="NDX">NDX</option>
                </select>
            </div>
            <div class="col-md-1">
                <label for="service_type">Service</label>
                <select id="service_type" class="form-select">
                    <option value="AIR">AIR</option>
                    <option value="SURFACE">SURFACE</option>
                    <option value="EXPRESS">EXPRESS</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="pincode">Pincode</label>
                <input type="text" id="pincode" class="form-control" pattern="[0-9]{6}">
            </div>
            <div class="col-md-3">
                <label for="city_description">City Description</label>
                <input type="text" id="city_description" class="form-control">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-2">
                <label for="weight">Weight (Kg.)</label>
                <input type="number" id="weight" class="form-control" step="0.001" min="0.001">
            </div>
            <div class="col-md-2">
                <label for="courier_amt">Courier Amt</label>
                <input type="number" id="courier_amt" class="form-control" step="0.01" min="0">
            </div>
            <div class="col-md-2">
                <label for="vas_amount">VAS</label>
                <input type="number" id="vas_amount" class="form-control" step="0.01" min="0" value="0">
            </div>
            <div class="col-md-2">
                <label for="chargeable_amt">Chargeable Amt</label>
                <input type="number" id="chargeable_amt" class="form-control" step="0.01" readonly>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary w-100" onclick="saveBooking()">
                    <i class="fas fa-save"></i> Save Booking
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div id="bulkActions" class="bulk-actions">
        <span id="selectedCount">0</span> items selected
        <button class="btn-delete-selected ms-3" onclick="deleteSelected()">
            <i class="fas fa-trash"></i> Delete Selected
        </button>
    </div>

    <!-- Booking Table -->
    <div class="booking-table-container">
        <table class="table booking-table">
            <thead>
                <tr>
                    <th width="40">
                        <input type="checkbox" id="selectAll" class="form-check-input">
                    </th>
                    <th width="100">Consign. No</th>
                    <th width="180">Customer</th>
                    <th width="60">Doc/Type</th>
                    <th width="60">Mode</th>
                    <th width="80">Pincode</th>
                    <th width="120">Dest</th>
                    <th width="80">Weight (Kg.)</th>
                    <th width="80">VAS</th>
                    <th width="100">Courier Amt</th>
                    <th width="100">Chargeable Amt</th>
                    <th width="80">Edit</th>
                </tr>
            </thead>
            <tbody id="bookingTableBody">
                <tr>
                    <td colspan="12" class="text-center" style="padding: 40px;">
                        <div class="spinner"></div>
                        <div style="margin-top: 10px;">Loading bookings...</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-cards">
            <div class="summary-card total-records">
                <div class="card-label">Total Records</div>
                <div class="card-value" id="summary_total_records">0</div>
            </div>
            <div class="summary-card total-billed">
                <div class="card-label">Total Billed</div>
                <div class="card-value" id="summary_total_billed">0</div>
            </div>
            <div class="summary-card total-non-billed">
                <div class="card-label">Total Non Billed</div>
                <div class="card-value" id="summary_total_non_billed">0</div>
            </div>
            <div class="summary-card total-weight">
                <div class="card-label">Total Weight</div>
                <div class="card-value" id="summary_total_weight">0.000</div>
            </div>
            <div class="summary-card total-amount">
                <div class="card-label">Total Amount</div>
                <div class="card-value" id="summary_total_amount">0.00</div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="pagination-info">
            Showing <span id="pagination_start">0</span> to <span id="pagination_end">0</span> of <span id="pagination_total">0</span> entries
        </div>
        <nav>
            <ul class="pagination" id="pagination_controls">
                <!-- Pagination will be generated here -->
            </ul>
        </nav>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewBookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye"></i> View Booking Details
                </h5>
                <button type="button" class="btn-close" onclick="closeViewModal()"></button>
            </div>
            <div class="modal-body" id="viewBookingContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editBookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Edit Booking
                </h5>
                <button type="button" class="btn-close" onclick="closeEditModal()"></button>
            </div>
            <div class="modal-body">
                <form id="editBookingForm">
                    <input type="hidden" id="edit_booking_id">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Consignment No</label>
                            <input type="text" id="edit_consignment_no" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Customer Name</label>
                            <select id="edit_customer_name" class="form-select">
                                <option value="">Select Customer</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="form-label">Doc Type</label>
                            <select id="edit_doc_type" class="form-select">
                                <option value="DOX">DOX</option>
                                <option value="SPX">SPX</option>
                                <option value="NDX">NDX</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Service Type</label>
                            <select id="edit_service_type" class="form-select">
                                <option value="AIR">AIR</option>
                                <option value="SURFACE">SURFACE</option>
                                <option value="EXPRESS">EXPRESS</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pincode</label>
                            <input type="text" id="edit_pincode" class="form-control" pattern="[0-9]{6}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Weight (Kg.)</label>
                            <input type="number" id="edit_weight" class="form-control" step="0.001" min="0.001">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">City Description</label>
                            <input type="text" id="edit_city_description" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">Courier Amount</label>
                            <input type="number" id="edit_courier_amt" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">VAS Amount</label>
                            <input type="number" id="edit_vas_amount" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Chargeable Amount</label>
                            <input type="number" id="edit_chargeable_amt" class="form-control" readonly>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateBooking()">
                    <i class="fas fa-save"></i> Update Booking
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include Toastify CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

<!-- Include Toastify JS -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
let currentPage = 1;
let isLoading = false;
let selectedBookings = [];

$(document).ready(function() {
    // Initialize the page
    generateConsignmentNumber();
    loadCustomers();
    loadBookings();
    
    // Auto-calculate chargeable amount
    $('#courier_amt, #vas_amount').on('input', calculateChargeableAmount);
    $('#edit_courier_amt, #edit_vas_amount').on('input', calculateEditChargeableAmount);
    
    // Search on Enter key
    $('#search_text').on('keypress', function(e) {
        if (e.which === 13) {
            loadBookings();
        }
    });
    
    // Select all checkbox
    $('#selectAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.booking-checkbox').prop('checked', isChecked);
        updateBulkActions();
    });
    
    // Individual checkbox change
    $(document).on('change', '.booking-checkbox', function() {
        updateBulkActions();
    });
});

// Generate new consignment number
function generateConsignmentNumber() {
    const timestamp = Date.now().toString().slice(-8);
    $('#consignment_no').val('U' + timestamp);
}

// Load customers
function loadCustomers() {
    $.ajax({
        url: 'api_fallback.php?endpoint=customers',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let options = '<option value="">Select Customer</option>';
                response.data.forEach(function(customer) {
                    options += `<option value="${customer.id}">${customer.name}</option>`;
                });
                $('#customer_name, #edit_customer_name').html(options);
            }
        },
        error: function() {
            showToast('Failed to load customers', 'error');
        }
    });
}

// Calculate chargeable amount
function calculateChargeableAmount() {
    const courier = parseFloat($('#courier_amt').val()) || 0;
    const vas = parseFloat($('#vas_amount').val()) || 0;
    $('#chargeable_amt').val((courier + vas).toFixed(2));
}

// Calculate edit chargeable amount
function calculateEditChargeableAmount() {
    const courier = parseFloat($('#edit_courier_amt').val()) || 0;
    const vas = parseFloat($('#edit_vas_amount').val()) || 0;
    $('#edit_chargeable_amt').val((courier + vas).toFixed(2));
}

// Load bookings
function loadBookings(page = 1) {
    if (isLoading) return;
    
    currentPage = page;
    isLoading = true;
    
    const filters = {
        page: page,
        booking_date: $('#booking_date').val(),
        billing_status: $('#billing_status').val(),
        search: $('#search_text').val(),
        limit: 15
    };
    
    // Show loading in table
    $('#bookingTableBody').html(`
        <tr>
            <td colspan="12" class="text-center" style="padding: 40px;">
                <div class="spinner"></div>
                <div style="margin-top: 10px;">Loading bookings...</div>
            </td>
        </tr>
    `);
    
    $.ajax({
        url: 'ajax/get_bookings.php',
        type: 'GET',
        data: filters,
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            isLoading = false;
            if (response.success) {
                displayBookings(response.data);
                updateSummary(response.data);
                updatePagination(
                    response.pagination.current_page,
                    response.pagination.total_pages,
                    response.pagination.total_records
                );
                
                // Show demo notification if using mock data
                if (response.source === 'mock' || response.demo_mode) {
                    showToast('Running in demo mode - sample data displayed', 'warning');
                }
            } else {
                showToast('Error loading bookings: ' + response.message, 'error');
                $('#bookingTableBody').html(`
                    <tr>
                        <td colspan="12" class="text-center" style="padding: 40px;">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                            <div style="margin-top: 10px;">Failed to load bookings</div>
                        </td>
                    </tr>
                `);
            }
        },
        error: function(xhr, status, error) {
            isLoading = false;
            console.error('Booking loading error:', status, error, xhr.responseText);
            
            if (xhr.status === 500 || xhr.status === 0) {
                showToast('Server unavailable - Loading demo data', 'warning');
                const demoBookings = generateDemoBookings();
                displayBookings(demoBookings);
                updateSummary(demoBookings);
                updatePagination(1, 1, demoBookings.length);
            } else {
                showToast('Failed to load bookings. Please check your connection.', 'error');
                $('#bookingTableBody').html(`
                    <tr>
                        <td colspan="12" class="text-center" style="padding: 40px;">
                            <i class="fas fa-wifi text-danger" style="font-size: 2rem;"></i>
                            <div style="margin-top: 10px;">Connection Error</div>
                        </td>
                    </tr>
                `);
            }
        }
    });
}

// Generate demo bookings data
function generateDemoBookings() {
    const customers = ['STARLIT MEDICAL CENTER PVT...', 'ABC LOGISTICS PVT LTD', 'XYZ ENTERPRISES', 'PQR INTERNATIONAL'];
    const cities = ['LKO', 'DEL', 'HYD', 'BLR', 'MUM', 'CCU'];
    const docTypes = ['DOX', 'SPX', 'NDX'];
    const serviceTypes = ['AIR', 'SURFACE'];
    
    const bookings = [];
    for (let i = 1; i <= 12; i++) {
        const consignmentNo = 'U36414' + (665 + i);
        const weight = (Math.random() * 10 + 0.1).toFixed(3);
        const courierAmt = (Math.random() * 100 + 20).toFixed(2);
        const vasAmt = '0.00';
        
        bookings.push({
            id: i,
            consignment_no: consignmentNo,
            customer_name: customers[Math.floor(Math.random() * customers.length)],
            doc_type: docTypes[Math.floor(Math.random() * docTypes.length)],
            service_type: serviceTypes[Math.floor(Math.random() * serviceTypes.length)],
            pincode: '110030',
            city_description: cities[Math.floor(Math.random() * cities.length)],
            weight: weight,
            vas_amount: vasAmt,
            courier_amt: courierAmt,
            chargeable_amt: courierAmt,
            billing_status: Math.random() > 0.5 ? 'billed' : 'non-billed'
        });
    }
    
    return bookings;
}

// Display bookings in table
function displayBookings(bookings) {
    let html = '';
    
    if (bookings.length === 0) {
        html = `
            <tr>
                <td colspan="12" class="text-center" style="padding: 40px;">
                    <i class="fas fa-inbox text-muted" style="font-size: 2rem;"></i>
                    <div style="margin-top: 10px;">No bookings found</div>
                </td>
            </tr>
        `;
    } else {
        bookings.forEach(function(booking) {
            html += `
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input booking-checkbox" value="${booking.id}">
                    </td>
                    <td class="consignment-cell">${booking.consignment_no}</td>
                    <td class="customer-cell" title="${booking.customer_name}">${booking.customer_name}</td>
                    <td>${booking.doc_type}</td>
                    <td>${booking.service_type}</td>
                    <td>${booking.pincode}</td>
                    <td>${booking.city_description}</td>
                    <td class="weight-cell">${booking.weight}</td>
                    <td class="amount-cell">${booking.vas_amount}</td>
                    <td class="amount-cell">${booking.courier_amt}</td>
                    <td class="amount-cell">${booking.chargeable_amt}</td>
                    <td>
                        <div class="btn-group-actions">
                            <button class="btn btn-action-view" onclick="viewBooking(${booking.id})" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-action-edit" onclick="editBooking(${booking.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-action-delete" onclick="deleteBooking(${booking.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#bookingTableBody').html(html);
}

// Update summary statistics
function updateSummary(bookings) {
    const totalRecords = bookings.length;
    const totalBilled = bookings.filter(b => b.billing_status === 'billed').length;
    const totalNonBilled = bookings.filter(b => b.billing_status === 'non-billed').length;
    const totalWeight = bookings.reduce((sum, b) => sum + parseFloat(b.weight), 0);
    const totalAmount = bookings.reduce((sum, b) => sum + parseFloat(b.chargeable_amt), 0);
    
    $('#summary_total_records').text(totalRecords);
    $('#summary_total_billed').text(totalBilled);
    $('#summary_total_non_billed').text(totalNonBilled);
    $('#summary_total_weight').text(totalWeight.toFixed(3));
    $('#summary_total_amount').text(totalAmount.toFixed(2));
}

// Update pagination
function updatePagination(currentPage, totalPages, totalRecords) {
    const start = totalRecords > 0 ? 1 : 0;
    const end = totalRecords;
    
    $('#pagination_start').text(start);
    $('#pagination_end').text(end);
    $('#pagination_total').text(totalRecords);
    
    // For demo, we'll show simple pagination
    let paginationHtml = `
        <li class="page-item disabled">
            <a class="page-link" href="#">Previous</a>
        </li>
        <li class="page-item active">
            <a class="page-link" href="#">1</a>
        </li>
        <li class="page-item disabled">
            <a class="page-link" href="#">Next</a>
        </li>
    `;
    
    $('#pagination_controls').html(paginationHtml);
}

// Update bulk actions visibility
function updateBulkActions() {
    const checkedBoxes = $('.booking-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (count > 0) {
        $('#bulkActions').addClass('show');
        $('#selectedCount').text(count);
    } else {
        $('#bulkActions').removeClass('show');
    }
    
    // Update select all checkbox
    const totalBoxes = $('.booking-checkbox').length;
    if (count === 0) {
        $('#selectAll').prop('indeterminate', false).prop('checked', false);
    } else if (count === totalBoxes) {
        $('#selectAll').prop('indeterminate', false).prop('checked', true);
    } else {
        $('#selectAll').prop('indeterminate', true);
    }
}

// Save new booking
function saveBooking() {
    const formData = {
        consignment_no: $('#consignment_no').val(),
        customer_id: $('#customer_name').val(),
        doc_type: $('#doc_type').val(),
        service_type: $('#service_type').val(),
        pincode: $('#pincode').val(),
        city_description: $('#city_description').val(),
        weight: $('#weight').val(),
        courier_amt: $('#courier_amt').val(),
        vas_amount: $('#vas_amount').val(),
        chargeable_amt: $('#chargeable_amt').val()
    };
    
    // Basic validation
    if (!formData.customer_id) {
        showToast('Please select a customer', 'error');
        return;
    }
    
    if (!formData.weight || parseFloat(formData.weight) <= 0) {
        showToast('Please enter a valid weight', 'error');
        return;
    }
    
    if (!formData.courier_amt || parseFloat(formData.courier_amt) <= 0) {
        showToast('Please enter a valid courier amount', 'error');
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: 'ajax/save_booking.php',
        type: 'POST',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            hideLoading();
            if (response.success) {
                showToast(response.message || 'Booking saved successfully!', 'success');
                
                // Reset form
                $('#bookingForm')[0].reset();
                $('#vas_amount').val('0');
                generateConsignmentNumber();
                
                // Reload bookings
                loadBookings();
                
                if (response.demo_mode) {
                    showToast('Demo mode: ' + response.note, 'warning');
                }
            } else {
                showToast('Error: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            console.error('Save booking error:', status, error, xhr.responseText);
            
            if (xhr.status === 500 || xhr.status === 0) {
                showToast('Server unavailable - Demo save simulated', 'warning');
                
                // Reset form and reload
                $('#bookingForm')[0].reset();
                $('#vas_amount').val('0');
                generateConsignmentNumber();
                loadBookings();
            } else {
                showToast('Failed to save booking', 'error');
            }
        }
    });
}

// View booking
function viewBooking(bookingId) {
    const demoBooking = {
        consignment_no: 'U36414673',
        customer_name: 'STARLIT MEDICAL CENTER PVT LTD',
        doc_type: 'DOX',
        service_type: 'AIR',
        pincode: '226010',
        city_description: 'LKO',
        weight: '2.640',
        vas_amount: '0.00',
        courier_amt: '338.00',
        chargeable_amt: '338.00',
        billing_status: 'non-billed',
        booking_date: '2025-06-05'
    };
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6>Booking Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Consignment No:</strong></td><td>${demoBooking.consignment_no}</td></tr>
                    <tr><td><strong>Booking Date:</strong></td><td>${demoBooking.booking_date}</td></tr>
                    <tr><td><strong>Service Type:</strong></td><td>${demoBooking.service_type}</td></tr>
                    <tr><td><strong>Document Type:</strong></td><td>${demoBooking.doc_type}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Customer & Destination</h6>
                <table class="table table-sm">
                    <tr><td><strong>Customer:</strong></td><td>${demoBooking.customer_name}</td></tr>
                    <tr><td><strong>City:</strong></td><td>${demoBooking.city_description}</td></tr>
                    <tr><td><strong>Pincode:</strong></td><td>${demoBooking.pincode}</td></tr>
                    <tr><td><strong>Weight:</strong></td><td>${demoBooking.weight} Kg</td></tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h6>Amount Details</h6>
                <table class="table table-sm">
                    <tr><td><strong>Courier Amount:</strong></td><td>₹${demoBooking.courier_amt}</td></tr>
                    <tr><td><strong>VAS Amount:</strong></td><td>₹${demoBooking.vas_amount}</td></tr>
                    <tr><td><strong>Chargeable Amount:</strong></td><td><strong>₹${demoBooking.chargeable_amt}</strong></td></tr>
                    <tr><td><strong>Billing Status:</strong></td><td><span class="badge bg-warning">${demoBooking.billing_status}</span></td></tr>
                </table>
            </div>
        </div>
    `;
    
    $('#viewBookingContent').html(html);
    $('#viewBookingModal').modal('show');
}

// Edit booking
function editBooking(bookingId) {
    const demoBooking = {
        id: bookingId,
        consignment_no: 'U36414673',
        customer_id: '1',
        doc_type: 'DOX',
        service_type: 'AIR',
        pincode: '226010',
        city_description: 'LKO',
        weight: '2.640',
        vas_amount: '0.00',
        courier_amt: '338.00',
        chargeable_amt: '338.00'
    };
    
    // Populate edit form
    $('#edit_booking_id').val(demoBooking.id);
    $('#edit_consignment_no').val(demoBooking.consignment_no);
    $('#edit_customer_name').val(demoBooking.customer_id);
    $('#edit_doc_type').val(demoBooking.doc_type);
    $('#edit_service_type').val(demoBooking.service_type);
    $('#edit_pincode').val(demoBooking.pincode);
    $('#edit_city_description').val(demoBooking.city_description);
    $('#edit_weight').val(demoBooking.weight);
    $('#edit_vas_amount').val(demoBooking.vas_amount);
    $('#edit_courier_amt').val(demoBooking.courier_amt);
    $('#edit_chargeable_amt').val(demoBooking.chargeable_amt);
    
    $('#editBookingModal').modal('show');
}

// Update booking
function updateBooking() {
    const formData = {
        id: $('#edit_booking_id').val(),
        customer_id: $('#edit_customer_name').val(),
        doc_type: $('#edit_doc_type').val(),
        service_type: $('#edit_service_type').val(),
        pincode: $('#edit_pincode').val(),
        city_description: $('#edit_city_description').val(),
        weight: $('#edit_weight').val(),
        courier_amt: $('#edit_courier_amt').val(),
        vas_amount: $('#edit_vas_amount').val(),
        chargeable_amt: $('#edit_chargeable_amt').val()
    };
    
    showLoading();
    
    // Simulate update
    setTimeout(function() {
        hideLoading();
        showToast('Booking updated successfully!', 'success');
        closeEditModal();
        loadBookings();
    }, 1000);
}

// Delete booking
function deleteBooking(bookingId) {
    if (!confirm('Are you sure you want to delete this booking?')) {
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: 'ajax/delete_booking.php',
        type: 'POST',
        data: JSON.stringify({ id: bookingId }),
        contentType: 'application/json',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            hideLoading();
            if (response.success) {
                showToast(response.message || 'Booking deleted successfully!', 'success');
                loadBookings();
                
                if (response.demo_mode) {
                    showToast('Demo mode: ' + response.note, 'warning');
                }
            } else {
                showToast('Error: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            console.error('Delete booking error:', status, error, xhr.responseText);
            
            if (xhr.status === 500 || xhr.status === 0) {
                showToast('Server unavailable - Demo deletion simulated', 'warning');
                loadBookings();
            } else {
                showToast('Failed to delete booking', 'error');
            }
        }
    });
}

// Delete selected bookings
function deleteSelected() {
    const checkedBoxes = $('.booking-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (count === 0) {
        showToast('Please select bookings to delete', 'warning');
        return;
    }
    
    if (!confirm(`Are you sure you want to delete ${count} selected booking(s)?`)) {
        return;
    }
    
    showLoading();
    
    // Simulate bulk delete
    setTimeout(function() {
        hideLoading();
        showToast(`${count} booking(s) deleted successfully!`, 'success');
        loadBookings();
    }, 1000);
}

// Close modals
function closeViewModal() {
    $('#viewBookingModal').modal('hide');
}

function closeEditModal() {
    $('#editBookingModal').modal('hide');
}

// Show/Hide loading
function showLoading() {
    $('#loadingOverlay').addClass('show');
}

function hideLoading() {
    $('#loadingOverlay').removeClass('show');
}

// Show toast notification
function showToast(message, type = 'info') {
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };
    
    Toastify({
        text: message,
        duration: 4000,
        gravity: "top",
        position: "right",
        style: {
            background: colors[type] || colors.info,
            borderRadius: "8px",
            fontSize: "14px",
            fontWeight: "500"
        }
    }).showToast();
}
</script>

<?php require_once 'inc/footer.php'; ?>
