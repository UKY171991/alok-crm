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
    background-color: #f4f4f4;
    min-height: 100vh;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.page-header h1 {
    margin: 0;
    font-size: 2rem;
    font-weight: 600;
}

.page-header .breadcrumb {
    margin: 0;
    background: transparent;
    padding: 0;
    font-size: 0.9rem;
    margin-top: 5px;
}

.card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
    border: none;
    margin-bottom: 20px;
}

.card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
    padding: 20px;
    border-radius: 10px 10px 0 0;
}

.card-body {
    padding: 20px;
}

.filters-section {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.btn-group-custom {
    display: inline-flex;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-group-custom .btn {
    border-radius: 0;
    border-right: 1px solid rgba(255,255,255,0.2);
    margin: 0;
}

.btn-group-custom .btn:last-child {
    border-right: none;
}

.btn-view {
    background: #3b82f6;
    color: white;
    border: none;
}

.btn-edit {
    background: #f59e0b;
    color: white;
    border: none;
}

.btn-delete {
    background: #ef4444;
    color: white;
    border: none;
}

.table-responsive {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
}

.table {
    margin-bottom: 0;
}

.table thead th {
    background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
    color: white;
    border: none;
    padding: 15px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.table tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid #f1f5f9;
}

.table tbody tr:hover {
    background: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.table tbody td {
    padding: 15px;
    vertical-align: middle;
    border: none;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.badge-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.badge-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.pagination-container {
    background: white;
    padding: 20px;
    border-radius: 0 0 10px 10px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.pagination-info {
    color: #64748b;
    font-size: 0.9rem;
}

.pagination {
    margin: 0;
}

.page-link {
    color: #3b82f6;
    border: 1px solid #e2e8f0;
    padding: 8px 12px;
    margin: 0 2px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.page-link:hover {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.page-item.active .page-link {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.floating-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 24px;
    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.4);
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 1000;
}

.floating-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(16, 185, 129, 0.6);
}

.modal {
    display: none;
    position: fixed;
    z-index: 1050;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
}

.modal-dialog {
    position: relative;
    margin: 30px auto;
    max-width: 800px;
    width: 90%;
}

.modal-content {
    background: white;
    border-radius: 15px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    overflow: hidden;
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 20px 25px;
    border-bottom: none;
}

.modal-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.3s;
}

.modal-close:hover {
    opacity: 1;
}

.modal-body {
    padding: 25px;
}

.modal-footer {
    background: #f8fafc;
    padding: 20px 25px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
    font-size: 0.9rem;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.btn-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.btn-secondary {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.demo-banner {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
    animation: slideDown 0.5s ease-out;
}

@keyframes slideDown {
    from { transform: translateY(-100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e2e8f0;
    border-top: 4px solid #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.row {
    display: flex;
    flex-wrap: wrap;
    margin: -10px;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding: 10px;
}

.col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
    padding: 10px;
}

.col-md-3 {
    flex: 0 0 25%;
    max-width: 25%;
    padding: 10px;
}

@media (max-width: 768px) {
    .col-md-6, .col-md-4, .col-md-3 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .content-wrapper {
        margin-left: 0;
        padding: 10px;
    }
    
    .pagination-container {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<main class="content-wrapper">
    <!-- Demo Mode Notification Banner -->
    <div id="demoNotification" class="demo-banner" style="display: none;">
        <i class="fas fa-info-circle"></i>
        <span>Demo Mode: Using sample data. Please start your database server for live data.</span>
        <button onclick="hideDemoNotification()" style="background: none; border: none; color: inherit; font-size: 18px; cursor: pointer; margin-left: auto;">&times;</button>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-file-invoice"></i> Invoice Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" style="color: rgba(255,255,255,0.8);">Dashboard</a></li>
                <li class="breadcrumb-item active" style="color: white;">Generate Invoice</li>
            </ol>
        </nav>
    </div>

    <!-- Filters Section -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filters & Search</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Invoice Date</label>
                        <input type="date" id="invoice_date" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">From Date</label>
                        <input type="date" id="from_date" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">To Date</label>
                        <input type="date" id="to_date" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Customer</label>
                        <select id="customer_filter" class="form-control">
                            <option value="">All Customers</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Search</label>
                        <input type="text" id="customer_search" class="form-control" placeholder="Search by invoice no, customer name...">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button class="btn btn-primary" onclick="loadInvoices()">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <button class="btn btn-secondary" onclick="resetFilters()">
                                <i class="fas fa-times"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table"></i> Invoice List</h5>
        </div>
        <div class="table-responsive">
            <table class="table" id="invoicesTable">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="select_all_table" class="form-check-input">
                        </th>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody id="invoicesTableBody">
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 40px;">
                            <div class="spinner"></div>
                            <div style="margin-top: 10px;">Loading invoices...</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="pagination-container" id="paginationContainer">
            <div class="pagination-info" id="paginationInfo">
                Loading...
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination" id="pagination">
                    <!-- Pagination will be generated here -->
                </ul>
            </nav>
        </div>
    </div>

    <!-- Floating Add Button -->
    <button class="floating-btn" onclick="openInvoiceModal()" title="Add New Invoice">
        <i class="fas fa-plus"></i>
    </button>
</main>

<!-- Invoice Modal -->
<div class="modal" id="invoiceModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-file-invoice"></i> <span id="modalTitleText">Add New Invoice</span>
                </h5>
                <button type="button" class="modal-close" onclick="closeInvoiceModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="invoiceForm">
                    <input type="hidden" id="invoice_id" name="invoice_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Invoice Number</label>
                                <input type="text" id="invoice_no" name="invoice_no" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Invoice Date</label>
                                <input type="date" id="modal_invoice_date" name="invoice_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Customer</label>
                                <select id="modal_customer_id" name="customer_id" class="form-control" required>
                                    <option value="">Select Customer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Destination</label>
                                <input type="text" id="destination" name="destination" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">From Date</label>
                                <input type="date" id="modal_from_date" name="from_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">To Date</label>
                                <input type="date" id="modal_to_date" name="to_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Total Amount</label>
                                <input type="number" id="total_amount" name="total_amount" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">GST Amount</label>
                                <input type="number" id="gst_amount" name="gst_amount" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Grand Total</label>
                                <input type="number" id="grand_total" name="grand_total" class="form-control" step="0.01" min="0" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeInvoiceModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="saveInvoice()">
                    <i class="fas fa-save"></i> <span id="saveButtonText">Save Invoice</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Invoice Modal -->
<div class="modal" id="viewInvoiceModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye"></i> Invoice Details
                </h5>
                <button type="button" class="modal-close" onclick="closeViewModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="viewInvoiceContent">
                <!-- Invoice details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeViewModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="printInvoice()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="spinner"></div>
</div>

<!-- Include Toastify CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.css">
<!-- Include Toastify JS -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.js"></script>
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
// Global variables
let currentPage = 1;
let currentFilters = {};
let isLoading = false;

// Initialize page
$(document).ready(function() {
    // Initialize page loader
    initInvoiceGenerationLoader();
    
    loadCustomers();
    loadInvoices();
    
    // Auto-calculate grand total
    $('#total_amount, #gst_amount').on('input', calculateGrandTotal);
    
    // Search on Enter key
    $('#customer_search').on('keypress', function(e) {
        if (e.which === 13) {
            loadInvoices();
        }
    });
    
    // Filter change handlers
    $('#customer_filter, #invoice_date, #from_date, #to_date').on('change', function() {
        loadInvoices();
    });
});

// Show loading overlay
function showLoading() {
    $('#loadingOverlay').show();
    isLoading = true;
}

// Hide loading overlay
function hideLoading() {
    $('#loadingOverlay').hide();
    isLoading = false;
}

// Show demo notification
function showDemoNotification() {
    $('#demoNotification').slideDown(300);
}

// Hide demo notification
function hideDemoNotification() {
    $('#demoNotification').slideUp(300);
}

// Load customers for dropdowns
function loadCustomers() {
    const timestamp = new Date().getTime();
    
    $.ajax({
        url: 'api_fallback.php?endpoint=customers&_t=' + timestamp,
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            if (response.success) {
                let options = '<option value="">All Customers</option>';
                let modalOptions = '<option value="">Select Customer</option>';
                
                response.data.forEach(function(customer) {
                    options += `<option value="${customer.id}">${customer.id} - ${customer.name}</option>`;
                    modalOptions += `<option value="${customer.id}">${customer.name}</option>`;
                });
                
                $('#customer_filter').html(options);
                $('#modal_customer_id').html(modalOptions);
                
                // Show demo notification if using mock data
                if (response.source === 'mock') {
                    showDemoNotification();
                }
            } else {
                showToast('Error loading customers: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Customer loading error:', status, error, xhr.responseText);
            
            // Try to load demo customers if server error
            if (xhr.status === 500 || xhr.status === 0) {
                showToast('Server unavailable - Loading demo customers', 'warning');
                loadDemoCustomers();
            } else {
                showToast('Failed to load customers. Please check your connection.', 'error');
            }
        }
    });
}

// Load invoices with pagination and filters
function loadInvoices(page = 1) {
    if (isLoading) return;
    
    currentPage = page;
    
    const filters = {
        page: page,
        customer_id: $('#customer_filter').val(),
        search: $('#customer_search').val(),
        invoice_date: $('#invoice_date').val(),
        from_date: $('#from_date').val(),
        to_date: $('#to_date').val(),
        _t: new Date().getTime()
    };
    
    currentFilters = filters;
    
    // Show loading in table
    $('#invoicesTableBody').html(`
        <tr>
            <td colspan="7" class="text-center" style="padding: 40px;">
                <div class="spinner"></div>
                <div style="margin-top: 10px;">Loading invoices...</div>
            </td>
        </tr>
    `);
    
    $.ajax({
        url: 'api_fallback.php?endpoint=invoices',
        timeout: 10000,
        type: 'GET',
        data: filters,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayInvoices(response.data);
                updatePagination(response.pagination);
                
                // Show demo notification if using mock data
                if (response.source === 'mock') {
                    showDemoNotification();
                }
            } else {
                showToast('Error loading invoices: ' + response.message, 'error');
                $('#invoicesTableBody').html(`
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 40px;">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                            <div style="margin-top: 10px;">Failed to load invoices</div>
                        </td>
                    </tr>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Invoice loading error:', status, error, xhr.responseText);
            
            // Try to fall back to demo mode if server error
            if (xhr.status === 500 || xhr.status === 0) {
                showToast('Server unavailable - Loading demo data', 'warning');
                loadDemoInvoices();
            } else {
                showToast('Failed to load invoices. Please check your connection.', 'error');
                $('#invoicesTableBody').html(`
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 40px;">
                            <i class="fas fa-wifi text-danger" style="font-size: 2rem;"></i>
                            <div style="margin-top: 10px;">Connection Error</div>
                        </td>
                    </tr>
                `);
            }
        }
    });
}

// Load demo customers when server is unavailable
function loadDemoCustomers() {
    const demoCustomers = [
        { id: 1, name: 'ABC Corporation' },
        { id: 2, name: 'XYZ Ltd' },
        { id: 3, name: 'PQR Enterprises' },
        { id: 4, name: 'Express Delivery Co' },
        { id: 5, name: 'Fast Track Logistics' }
    ];
    
    let options = '<option value="">All Customers</option>';
    let modalOptions = '<option value="">Select Customer</option>';
    
    demoCustomers.forEach(function(customer) {
        options += `<option value="${customer.id}">${customer.id} - ${customer.name}</option>`;
        modalOptions += `<option value="${customer.id}">${customer.name}</option>`;
    });
    
    $('#customer_filter').html(options);
    $('#modal_customer_id').html(modalOptions);
    
    showDemoNotification();
}

// Load demo invoices when server is unavailable
function loadDemoInvoices() {
    const demoInvoices = [
        {
            id: 1,
            invoice_no: 'INV-2024-001',
            customer_name: 'ABC Corporation',
            customer_city: 'Mumbai',
            invoice_date: '2024-01-12',
            date_range: '1/11/2024 - 30/11/2024',
            total_amount: '1000.00',
            grand_total: '1180.00',
            status: 'pending'
        },
        {
            id: 2,
            invoice_no: 'INV-2024-002',
            customer_name: 'XYZ Ltd',
            customer_city: 'Delhi',
            invoice_date: '2024-01-12',
            date_range: '1/11/2024 - 30/11/2024',
            total_amount: '1500.00',
            grand_total: '1770.00',
            status: 'paid'
        },
        {
            id: 3,
            invoice_no: 'INV-2024-003',
            customer_name: 'PQR Enterprises',
            customer_city: 'Bangalore',
            invoice_date: '2024-01-12',
            date_range: '1/11/2024 - 30/11/2024',
            total_amount: '800.00',
            grand_total: '944.00',
            status: 'pending'
        }
    ];
    
    displayInvoices(demoInvoices);
    
    // Show demo pagination
    updatePagination({
        current_page: 1,
        total_pages: 1,
        total_records: 3,
        per_page: 10
    });
    
    // Show demo notification
    showDemoNotification();
}

// Display invoices in table
function displayInvoices(invoices) {
    let html = '';
    
    if (invoices.length === 0) {
        html = `
            <tr>
                <td colspan="7" class="text-center" style="padding: 40px;">
                    <i class="fas fa-inbox text-muted" style="font-size: 2rem;"></i>
                    <div style="margin-top: 10px;">No invoices found</div>
                </td>
            </tr>
        `;
    } else {
        invoices.forEach(function(invoice) {
            const statusBadge = getStatusBadge(invoice.status);
            html += `
                <tr data-invoice-id="${invoice.id}">
                    <td>
                        <input type="checkbox" class="form-check-input invoice-checkbox" value="${invoice.id}">
                    </td>
                    <td>
                        <strong>${invoice.invoice_no}</strong>
                    </td>
                    <td>
                        <div style="font-weight: 600;">${invoice.customer_name || 'Unknown'}</div>
                        <small class="text-muted">${invoice.destination || ''}</small>
                    </td>
                    <td>
                        <div>${formatDate(invoice.invoice_date)}</div>
                        <small class="text-muted">${formatDate(invoice.from_date)} - ${formatDate(invoice.to_date)}</small>
                    </td>
                    <td>
                        <div style="font-weight: 600;">₹${parseFloat(invoice.grand_total || 0).toFixed(2)}</div>
                        <small class="text-muted">Base: ₹${parseFloat(invoice.total_amount || 0).toFixed(2)}</small>
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-group-custom">
                            <button class="btn btn-view btn-sm" onclick="viewInvoice(${invoice.id})" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-edit btn-sm" onclick="editInvoice(${invoice.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-delete btn-sm" onclick="deleteInvoice(${invoice.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#invoicesTableBody').html(html);
}

// Get status badge HTML
function getStatusBadge(status) {
    const badges = {
        'paid': '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Paid</span>',
        'pending': '<span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>',
        'cancelled': '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Cancelled</span>'
    };
    return badges[status] || '<span class="badge badge-secondary">Unknown</span>';
}

// Update pagination
function updatePagination(pagination) {
    if (!pagination) {
        $('#paginationContainer').hide();
        return;
    }
    
    const { current_page, total_pages, total_records, per_page } = pagination;
    
    // Update info
    const start = ((current_page - 1) * per_page) + 1;
    const end = Math.min(current_page * per_page, total_records);
    $('#paginationInfo').html(`Showing ${start}-${end} of ${total_records} entries`);
    
    // Generate pagination HTML
    let paginationHtml = '';
    
    // Previous button
    if (current_page > 1) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadInvoices(${current_page - 1})"><i class="fas fa-chevron-left"></i></a></li>`;
    }
    
    // Page numbers
    let startPage = Math.max(1, current_page - 2);
    let endPage = Math.min(total_pages, current_page + 2);
    
    if (startPage > 1) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadInvoices(1)">1</a></li>`;
        if (startPage > 2) {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const activeClass = (i === current_page) ? 'active' : '';
        paginationHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="loadInvoices(${i})">${i}</a></li>`;
    }
    
    if (endPage < total_pages) {
        if (endPage < total_pages - 1) {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadInvoices(${total_pages})">${total_pages}</a></li>`;
    }
    
    // Next button
    if (current_page < total_pages) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadInvoices(${current_page + 1})"><i class="fas fa-chevron-right"></i></a></li>`;
    }
    
    $('#pagination').html(paginationHtml);
    $('#paginationContainer').show();
}

// Open invoice modal for adding/editing
function openInvoiceModal(invoiceId = null) {
    if (invoiceId) {
        // Edit mode
        $('#modalTitleText').text('Edit Invoice');
        $('#saveButtonText').text('Update Invoice');
        loadInvoiceData(invoiceId);
    } else {
        // Add mode
        $('#modalTitleText').text('Add New Invoice');
        $('#saveButtonText').text('Save Invoice');
        resetInvoiceForm();
        generateInvoiceNumber();
    }
    
    $('#invoiceModal').show();
}

// Close invoice modal
function closeInvoiceModal() {
    $('#invoiceModal').hide();
}

// Reset invoice form
function resetInvoiceForm() {
    $('#invoiceForm')[0].reset();
    $('#invoice_id').val('');
    $('#modal_invoice_date').val(new Date().toISOString().split('T')[0]);
    calculateGrandTotal();
}

// Generate new invoice number
function generateInvoiceNumber() {
    $.ajax({
        url: 'ajax/generate_invoice_number.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#invoice_no').val(response.invoice_no);
            } else {
                $('#invoice_no').val('INV-' + Date.now());
            }
        },
        error: function(xhr, status, error) {
            console.error('Generate invoice number error:', status, error, xhr.responseText);
            
            if (xhr.status === 500 || xhr.status === 0) {
                // Generate demo invoice number
                const timestamp = Date.now().toString().slice(-6);
                $('#invoice_no').val('INV-DEMO-' + timestamp);
                showToast('Server unavailable - Demo invoice number generated', 'warning');
            } else {
                $('#invoice_no').val('INV-' + Date.now());
            }
        }
    });
}

// Load invoice data for editing
function loadInvoiceData(invoiceId) {
    showLoading();
    
    $.ajax({
        url: 'ajax/get_invoice.php',
        type: 'GET',
        data: { id: invoiceId },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                const invoice = response.data;
                $('#invoice_id').val(invoice.id);
                $('#invoice_no').val(invoice.invoice_no);
                $('#modal_invoice_date').val(invoice.invoice_date);
                $('#modal_customer_id').val(invoice.customer_id);
                $('#destination').val(invoice.destination);
                $('#modal_from_date').val(invoice.from_date);
                $('#modal_to_date').val(invoice.to_date);
                $('#total_amount').val(invoice.total_amount);
                $('#gst_amount').val(invoice.gst_amount);
                $('#grand_total').val(invoice.grand_total);
                $('#status').val(invoice.status);
            } else {
                showToast('Error loading invoice: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            console.error('Load invoice error:', status, error, xhr.responseText);
            
            if (xhr.status === 500 || xhr.status === 0) {
                showToast('Server unavailable - Using demo data', 'warning');
                // Load demo invoice data
                loadDemoInvoiceData(invoiceId);
            } else {
                showToast('Failed to load invoice data', 'error');
            }
        }
    });
}

// Save invoice (add or update)
function saveInvoice() {
    if (isLoading) return;
    
    const formData = {
        id: $('#invoice_id').val(),
        invoice_no: $('#invoice_no').val(),
        invoice_date: $('#modal_invoice_date').val(),
        customer_id: $('#modal_customer_id').val(),
        destination: $('#destination').val(),
        from_date: $('#modal_from_date').val(),
        to_date: $('#modal_to_date').val(),
        total_amount: $('#total_amount').val(),
        gst_amount: $('#gst_amount').val(),
        grand_total: $('#grand_total').val(),
        status: $('#status').val()
    };
    
    // Basic validation
    if (!formData.customer_id) {
        showToast('Please select a customer', 'error');
        return;
    }
    
    if (!formData.invoice_date) {
        showToast('Please select invoice date', 'error');
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: 'ajax/save_invoice.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                showToast(response.message || 'Invoice saved successfully!', 'success');
                closeInvoiceModal();
                loadInvoices(currentPage);
            } else {
                showToast('Error: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            console.error('Save error:', status, error, xhr.responseText);
            
            if (xhr.status === 500 || xhr.status === 0) {
                showToast('Server unavailable - Demo save simulated', 'warning');
                closeInvoiceModal();
                loadDemoInvoices(); // Refresh with demo data
            } else {
                showToast('Failed to save invoice', 'error');
            }
        }
    });
}

// View invoice details
function viewInvoice(invoiceId) {
    showLoading();
    
    $.ajax({
        url: 'ajax/get_invoice.php',
        type: 'GET',
        data: { id: invoiceId },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                displayInvoiceDetails(response.data);
                $('#viewInvoiceModal').show();
            } else {
                showToast('Error loading invoice: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            console.error('View invoice error:', status, error, xhr.responseText);
            
            if (xhr.status === 500 || xhr.status === 0) {
                showToast('Server unavailable - Using demo data', 'warning');
                // Show demo invoice data in view modal
                const demoInvoice = getDemoInvoiceForView(invoiceId);
                if (demoInvoice) {
                    $('#viewInvoiceModal').show();
                    displayInvoiceDetails(demoInvoice);
                    showDemoNotification();
                } else {
                    showToast('Demo invoice not found', 'error');
                }
            } else {
                showToast('Failed to load invoice details', 'error');
            }
        }
    });
}

// Get demo invoice data for view modal
function getDemoInvoiceForView(invoiceId) {
    const demoInvoices = {
        1: {
            id: 1,
            invoice_no: 'INV-2024-001',
            invoice_date: '2024-01-12',
            customer_name: 'ABC Corporation',
            destination: 'Mumbai',
            from_date: '2024-01-01',
            to_date: '2024-01-31',
            total_amount: '1000.00',
            gst_amount: '180.00',
            grand_total: '1180.00',
            status: 'pending'
        },
        2: {
            id: 2,
            invoice_no: 'INV-2024-002',
            invoice_date: '2024-01-12',
            customer_name: 'XYZ Ltd',
            destination: 'Delhi',
            from_date: '2024-01-01',
            to_date: '2024-01-31',
            total_amount: '1500.00',
            gst_amount: '270.00',
            grand_total: '1770.00',
            status: 'paid'
        },
        3: {
            id: 3,
            invoice_no: 'INV-2024-003',
            invoice_date: '2024-01-12',
            customer_name: 'PQR Enterprises',
            destination: 'Bangalore',
            from_date: '2024-01-01',
            to_date: '2024-01-31',
            total_amount: '800.00',
            gst_amount: '144.00',
            grand_total: '944.00',
            status: 'pending'
        }
    };
    
    return demoInvoices[invoiceId] || null;
}

// Display invoice details in view modal
function displayInvoiceDetails(invoice) {
    const statusBadge = getStatusBadge(invoice.status);
    
    const html = `
        <div class="invoice-details">
            <div class="row">
                <div class="col-md-6">
                    <h6>Invoice Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Invoice No:</strong></td><td>${invoice.invoice_no}</td></tr>
                        <tr><td><strong>Date:</strong></td><td>${formatDate(invoice.invoice_date)}</td></tr>
                        <tr><td><strong>Status:</strong></td><td>${statusBadge}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Customer Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Customer:</strong></td><td>${invoice.customer_name || 'N/A'}</td></tr>
                        <tr><td><strong>Destination:</strong></td><td>${invoice.destination || 'N/A'}</td></tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h6>Date Range</h6>
                    <table class="table table-sm">
                        <tr><td><strong>From:</strong></td><td>${formatDate(invoice.from_date)}</td></tr>
                        <tr><td><strong>To:</strong></td><td>${formatDate(invoice.to_date)}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Amount Details</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Total Amount:</strong></td><td>₹${parseFloat(invoice.total_amount || 0).toFixed(2)}</td></tr>
                        <tr><td><strong>GST Amount:</strong></td><td>₹${parseFloat(invoice.gst_amount || 0).toFixed(2)}</td></tr>
                        <tr><td><strong>Grand Total:</strong></td><td><strong>₹${parseFloat(invoice.grand_total || 0).toFixed(2)}</strong></td></tr>
                    </table>
                </div>
            </div>
        </div>
    `;
    
    $('#viewInvoiceContent').html(html);
}

// Close view modal
function closeViewModal() {
    $('#viewInvoiceModal').hide();
}

// Edit invoice
function editInvoice(invoiceId) {
    openInvoiceModal(invoiceId);
}

// Delete invoice
function deleteInvoice(invoiceId) {
    if (!confirm('Are you sure you want to delete this invoice? This action cannot be undone.')) {
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: 'ajax/delete_invoice.php',
        type: 'POST',
        data: { id: invoiceId },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                showToast('Invoice deleted successfully!', 'success');
                loadInvoices(currentPage);
            } else {
                showToast('Error: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            console.error('Delete error:', status, error, xhr.responseText);
            
            if (xhr.status === 500 || xhr.status === 0) {
                showToast('Server unavailable - Demo deletion simulated', 'warning');
                // Remove the row from table in demo mode
                $(`tr[data-invoice-id="${invoiceId}"]`).fadeOut(300, function() {
                    $(this).remove();
                });
            } else {
                showToast('Failed to delete invoice', 'error');
            }
        }
    });
}

// Load demo invoice data for editing when server is unavailable
function loadDemoInvoiceData(invoiceId) {
    const demoInvoiceData = {
        1: {
            id: 1,
            invoice_no: 'INV-2024-001',
            invoice_date: '2024-01-12',
            customer_id: 1,
            destination: 'Mumbai',
            from_date: '2024-01-01',
            to_date: '2024-01-31',
            total_amount: '1000.00',
            gst_amount: '180.00',
            grand_total: '1180.00',
            status: 'pending'
        },
        2: {
            id: 2,
            invoice_no: 'INV-2024-002',
            invoice_date: '2024-01-12',
            customer_id: 2,
            destination: 'Delhi',
            from_date: '2024-01-01',
            to_date: '2024-01-31',
            total_amount: '1500.00',
            gst_amount: '270.00',
            grand_total: '1770.00',
            status: 'paid'
        },
        3: {
            id: 3,
            invoice_no: 'INV-2024-003',
            invoice_date: '2024-01-12',
            customer_id: 3,
            destination: 'Bangalore',
            from_date: '2024-01-01',
            to_date: '2024-01-31',
            total_amount: '800.00',
            gst_amount: '144.00',
            grand_total: '944.00',
            status: 'pending'
        }
    };
    
    const invoice = demoInvoiceData[invoiceId];
    if (invoice) {
        $('#invoice_id').val(invoice.id);
        $('#invoice_no').val(invoice.invoice_no);
        $('#modal_invoice_date').val(invoice.invoice_date);
        $('#modal_customer_id').val(invoice.customer_id);
        $('#destination').val(invoice.destination);
        $('#modal_from_date').val(invoice.from_date);
        $('#modal_to_date').val(invoice.to_date);
        $('#total_amount').val(invoice.total_amount);
        $('#gst_amount').val(invoice.gst_amount);
        $('#grand_total').val(invoice.grand_total);
        $('#status').val(invoice.status);
        
        showDemoNotification();
    } else {
        showToast('Demo invoice not found', 'error');
    }
}

// Calculate grand total
function calculateGrandTotal() {
    const total = parseFloat($('#total_amount').val()) || 0;
    const gst = parseFloat($('#gst_amount').val()) || 0;
    const grandTotal = total + gst;
    $('#grand_total').val(grandTotal.toFixed(2));
}

// Reset filters
function resetFilters() {
    $('#customer_filter').val('');
    $('#customer_search').val('');
    $('#invoice_date').val('<?= date('Y-m-d') ?>');
    $('#from_date').val('');
    $('#to_date').val('');
    loadInvoices(1);
}

// Print invoice
function printInvoice() {
    window.print();
}

// Format date
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN');
}

// Show toast notification
function showToast(message, type = 'info') {
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };
    
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    
    Toastify({
        text: `${icons[type]} ${message}`,
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

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>

<?php require_once 'inc/footer.php'; ?>
