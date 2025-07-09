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

.legacy-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    overflow: hidden;
    margin: 20px auto;
    max-width: 1400px;
}

.legacy-header {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    padding: 15px 30px;
    text-align: center;
    position: relative;
    font-weight: bold;
    font-size: 18px;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.legacy-close {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: #fff;
    color: #dc2626;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.legacy-close:hover {
    background: #f3f4f6;
    transform: translateY(-50%) scale(1.1);
}

.legacy-body {
    background: linear-gradient(135deg, #a8caba 0%, #5d4e75 100%);
    padding: 30px;
}

.legacy-form-row {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    gap: 20px;
    flex-wrap: wrap;
}

.legacy-field {
    display: flex;
    align-items: center;
    gap: 10px;
}

.legacy-label {
    font-weight: 600;
    color: #374151;
    min-width: 120px;
    font-size: 14px;
}

.legacy-input, .legacy-select {
    padding: 8px 12px;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    background: white;
    min-width: 150px;
    transition: all 0.3s ease;
}

.legacy-input:focus, .legacy-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.legacy-checkbox-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.legacy-checkbox {
    width: 18px;
    height: 18px;
    accent-color: #3b82f6;
}

.legacy-table-container {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 10px;
    overflow: hidden;
    margin: 20px 0;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.legacy-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.legacy-table thead {
    background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
    color: white;
}

.legacy-table th, .legacy-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.legacy-table tbody tr:hover {
    background-color: #f8fafc;
}

.legacy-table tbody tr:nth-child(even) {
    background-color: #f9fafb;
}

.legacy-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.legacy-btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.legacy-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

.legacy-btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.legacy-btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.legacy-btn-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
}

.legacy-btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
}

.legacy-btn-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

.legacy-btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
}

.btn-group {
    display: flex;
    gap: 5px;
}

.btn-group .legacy-btn {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 4px;
}

.pagination-container {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 20px;
    background: rgba(255, 255, 255, 0.9);
    margin-top: 10px;
    border-radius: 8px;
}

.pagination {
    display: flex;
    gap: 5px;
    margin: 0;
}

.pagination button {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    background: white;
    color: #374151;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.pagination button:hover:not(:disabled) {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.pagination button.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.pagination button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-info {
    color: #6b7280;
    font-size: 14px;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    backdrop-filter: blur(5px);
}

.modal-content {
    background: white;
    margin: 2% auto;
    padding: 0;
    border-radius: 15px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
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
    padding: 20px 30px;
    border-radius: 15px 15px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.modal-close {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    font-size: 24px;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
}

.modal-close:hover {
    background: rgba(255,255,255,0.3);
    transform: scale(1.1);
}

.modal-body {
    padding: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-row {
    display: flex;
    gap: 20px;
}

.form-row .form-group {
    flex: 1;
}

.modal-footer {
    padding: 20px 30px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    border-radius: 0 0 15px 15px;
    background: #f9fafb;
}

/* Loading Spinner */
.loading {
    display: none;
    text-align: center;
    padding: 20px;
}

.spinner {
    border: 4px solid #f3f4f6;
    border-top: 4px solid #3b82f6;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .content-wrapper {
        margin-left: 0;
        padding: 10px;
    }
    
    .legacy-form-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .legacy-field {
        flex-direction: column;
        align-items: stretch;
    }
    
    .legacy-label {
        min-width: auto;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .form-row {
        flex-direction: column;
    }
}

/* Floating Action Button */
.floating-action-btn {
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
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 999;
}

.floating-action-btn:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 12px 35px rgba(16, 185, 129, 0.6);
}

.floating-action-btn:active {
    transform: translateY(-1px) scale(1.05);
}

/* Badge styles for status */
.badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-danger {
    background: #fecaca;
    color: #991b1b;
}
</style>

<main class="content-wrapper">
    <div class="legacy-container">
        <div class="legacy-header">
            <span>GENERATE INVOICE</span>
            <button class="legacy-close" onclick="window.location.href='index.php'">&times;</button>
        </div>
        
        <div class="legacy-body">
            <!-- Filter Section -->
            <div class="legacy-form-row">
                <div class="legacy-field">
                    <label class="legacy-label">Invoice Date:</label>
                    <input type="date" id="invoice_date" class="legacy-input" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="legacy-field">
                    <label class="legacy-label">From Booking Date:</label>
                    <input type="date" id="from_date" class="legacy-input">
                </div>
                <div class="legacy-field">
                    <label class="legacy-label">To Booking Date:</label>
                    <input type="date" id="to_date" class="legacy-input">
                </div>
                <button class="legacy-btn legacy-btn-primary" onclick="generateInvoice()">Generate Invoice</button>
            </div>
            
            <!-- Customer Selection Section -->
            <div class="legacy-checkbox-row">
                <input type="checkbox" id="select_all" class="legacy-checkbox">
                <label for="select_all" class="legacy-label">Select All</label>
                <span class="legacy-label" style="margin-left: 20px;">Customer Code / Customer Name</span>
            </div>
            
            <div class="legacy-form-row">
                <div class="legacy-field">
                    <label class="legacy-label">Select:</label>
                    <select id="customer_filter" class="legacy-select">
                        <option value="">All Customers</option>
                    </select>
                </div>
                <div class="legacy-field">
                    <label class="legacy-label">Customer:</label>
                    <input type="text" id="customer_search" class="legacy-input" placeholder="Search customer...">
                </div>
                <button class="legacy-btn legacy-btn-success" onclick="loadInvoices()">View</button>
            </div>
            
            <!-- Data Table -->
            <div class="legacy-table-container">
                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <div>Loading invoices...</div>
                </div>
                
                <table class="legacy-table" id="invoicesTable" style="display: none;">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="select_all_table" class="legacy-checkbox">
                            </th>
                            <th>Invoice No</th>
                            <th>Customer</th>
                            <th>Invoice Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="invoicesTableBody">
                        <!-- Data will be loaded here -->
                    </tbody>
                </table>
                
                <div class="pagination-container" id="paginationContainer" style="display: none;">
                    <div class="pagination-info" id="paginationInfo"></div>
                    <div class="pagination" id="pagination"></div>
                </div>
            </div>
            
            <!-- Floating Action Button -->
            <button class="floating-action-btn" onclick="addInvoice()" title="Add New Invoice">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
</main>

<!-- Add/Edit Invoice Modal -->
<div id="invoiceModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Invoice</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="invoiceForm">
            <div class="modal-body">
                <input type="hidden" id="invoice_id" name="invoice_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_id">Customer *</label>
                        <select id="customer_id" name="customer_id" required>
                            <option value="">Select Customer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="modal_invoice_date">Invoice Date *</label>
                        <input type="date" id="modal_invoice_date" name="invoice_date" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="invoice_no">Invoice Number</label>
                        <input type="text" id="invoice_no" name="invoice_no" readonly>
                    </div>
                    <div class="form-group">
                        <label for="destination">Destination</label>
                        <input type="text" id="destination" name="destination" placeholder="Enter destination">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="from_booking_date">From Booking Date</label>
                        <input type="date" id="from_booking_date" name="from_date">
                    </div>
                    <div class="form-group">
                        <label for="to_booking_date">To Booking Date</label>
                        <input type="date" id="to_booking_date" name="to_date">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="total_amount">Total Amount</label>
                        <input type="number" id="total_amount" name="total_amount" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label for="gst_amount">GST Amount</label>
                        <input type="number" id="gst_amount" name="gst_amount" step="0.01" min="0">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="grand_total">Grand Total</label>
                        <input type="number" id="grand_total" name="grand_total" step="0.01" min="0" readonly>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="legacy-btn legacy-btn-danger" onclick="closeModal()">Cancel</button>
                <button type="submit" class="legacy-btn legacy-btn-primary" id="submitBtn">Save Invoice</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
let currentPage = 1;
let totalPages = 1;
let currentFilters = {};

$(document).ready(function() {
    loadCustomers();
    loadInvoices();
    setupEventListeners();
});

function setupEventListeners() {
    // Select all functionality
    $('#select_all').change(function() {
        $('.invoice-checkbox').prop('checked', this.checked);
    });
    
    $('#select_all_table').change(function() {
        $('.invoice-checkbox').prop('checked', this.checked);
    });
    
    // Form validation
    $('#total_amount, #gst_amount').on('input', function() {
        calculateGrandTotal();
    });
    
    // Customer filter
    $('#customer_filter').change(function() {
        loadInvoices();
    });
    
    // Search functionality
    $('#customer_search').on('keyup', function() {
        loadInvoices();
    });
    
    // Form submission
    $('#invoiceForm').submit(function(e) {
        e.preventDefault();
        saveInvoice();
    });
}

function loadCustomers() {
    $.ajax({
        url: 'api_fallback.php?endpoint=customers',
        type: 'GET',
        dataType: 'json',
        timeout: 10000, // 10 second timeout
        success: function(response) {
            if (response.success) {
                let options = '<option value="">Select Customer</option>';
                let filterOptions = '<option value="">All Customers</option>';
                
                response.data.forEach(function(customer) {
                    options += `<option value="${customer.id}">${customer.name}</option>`;
                    filterOptions += `<option value="${customer.id}">${customer.id} - ${customer.name}</option>`;
                });
                
                $('#customer_id').html(options);
                $('#customer_filter').html(filterOptions);
                
                // Show message if using mock data
                if (response.source === 'mock') {
                    showToast('Using demo data - Please start your database server for live data', 'info');
                }
            } else {
                showToast('Error loading customers: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Customer loading error:', status, error, xhr.responseText);
            let errorMsg = 'Error loading customers';
            if (status === 'timeout') {
                errorMsg = 'Request timeout - please check your connection';
            } else if (xhr.status === 500) {
                errorMsg = 'Server error - please check database connection';
            } else if (xhr.status === 404) {
                errorMsg = 'Customer endpoint not found';
            }
            showToast(errorMsg, 'error');
        }
    });
}

function loadInvoices(page = 1) {
    currentPage = page;
    
    const filters = {
        page: page,
        customer_id: $('#customer_filter').val(),
        search: $('#customer_search').val(),
        invoice_date: $('#invoice_date').val(),
        from_date: $('#from_date').val(),
        to_date: $('#to_date').val()
    };
    
    currentFilters = filters;
    
    $('#loading').show();
    $('#invoicesTable').hide();
    $('#paginationContainer').hide();
    
    $.ajax({
        url: 'api_fallback.php?endpoint=invoices',
        timeout: 10000, // 10 second timeout
        type: 'GET',
        data: filters,
        dataType: 'json',
        success: function(response) {
            $('#loading').hide();
            
            if (response.success) {
                displayInvoices(response.data);
                updatePagination(response.pagination);
                $('#invoicesTable').show();
                $('#paginationContainer').show();
                
                // Show message if using mock data
                if (response.source === 'mock') {
                    showToast('Using demo data - Please start your database server for live data', 'info');
                }
            } else {
                showToast('Error loading invoices: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            $('#loading').hide();
            console.error('Invoice loading error:', status, error, xhr.responseText);
            let errorMsg = 'Error loading invoices';
            if (status === 'timeout') {
                errorMsg = 'Request timeout - please check your connection';
            } else if (xhr.status === 500) {
                errorMsg = 'Server error - please check database connection';
            } else if (xhr.status === 404) {
                errorMsg = 'Invoice endpoint not found';
            }
            showToast(errorMsg, 'error');
        }
    });
}

function displayInvoices(invoices) {
    let html = '';
    
    if (invoices.length === 0) {
        html = '<tr><td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">No invoices found</td></tr>';
    } else {
        invoices.forEach(function(invoice) {
            const statusClass = invoice.status === 'paid' ? 'success' : invoice.status === 'cancelled' ? 'danger' : 'warning';
            
            html += `
                <tr>
                    <td><input type="checkbox" class="invoice-checkbox legacy-checkbox" value="${invoice.id}"></td>
                    <td>${invoice.invoice_no}</td>
                    <td>${invoice.customer_name}</td>
                    <td>${formatDate(invoice.invoice_date)}</td>
                    <td>₹${parseFloat(invoice.grand_total || invoice.total_amount).toFixed(2)}</td>
                    <td><span class="badge badge-${statusClass}">${invoice.status}</span></td>
                    <td>
                        <div class="btn-group">
                            <button class="legacy-btn legacy-btn-primary" onclick="viewInvoice(${invoice.id})" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="legacy-btn legacy-btn-warning" onclick="editInvoice(${invoice.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="legacy-btn legacy-btn-danger" onclick="deleteInvoice(${invoice.id})" title="Delete">
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

function updatePagination(pagination) {
    totalPages = pagination.total_pages;
    currentPage = pagination.current_page;
    
    // Update pagination info
    const start = ((currentPage - 1) * pagination.per_page) + 1;
    const end = Math.min(currentPage * pagination.per_page, pagination.total_records);
    
    $('#paginationInfo').text(`Showing ${start}-${end} of ${pagination.total_records} entries`);
    
    // Update pagination buttons
    let paginationHtml = '';
    
    // Previous button
    paginationHtml += `<button onclick="loadInvoices(${currentPage - 1})" ${currentPage <= 1 ? 'disabled' : ''}>Previous</button>`;
    
    // Page numbers
    for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
        paginationHtml += `<button onclick="loadInvoices(${i})" ${i === currentPage ? 'class="active"' : ''}>${i}</button>`;
    }
    
    // Next button
    paginationHtml += `<button onclick="loadInvoices(${currentPage + 1})" ${currentPage >= totalPages ? 'disabled' : ''}>Next</button>`;
    
    $('#pagination').html(paginationHtml);
}

function generateInvoice() {
    const invoiceDate = $('#invoice_date').val();
    const fromDate = $('#from_date').val();
    const toDate = $('#to_date').val();
    
    if (!invoiceDate) {
        showToast('Please select invoice date', 'error');
        return;
    }
    
    if (!fromDate || !toDate) {
        showToast('Please select booking date range', 'error');
        return;
    }
    
    if (new Date(fromDate) > new Date(toDate)) {
        showToast('From date cannot be greater than to date', 'error');
        return;
    }
    
    // Generate new invoice with selected date range
    $.ajax({
        url: 'ajax/generate_invoice.php',
        type: 'POST',
        data: {
            invoice_date: invoiceDate,
            from_date: fromDate,
            to_date: toDate
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast('Invoice generated successfully!', 'success');
                loadInvoices();
            } else {
                showToast('Error generating invoice: ' + response.message, 'error');
            }
        },
        error: function() {
            showToast('Error generating invoice', 'error');
        }
    });
}

function addInvoice() {
    resetForm();
    $('#modalTitle').text('Add New Invoice');
    $('#submitBtn').text('Save Invoice');
    
    // Generate new invoice number
    $.ajax({
        url: 'generate_invoice_number.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#invoice_no').val(response.invoice_no);
            }
        }
    });
    
    $('#modal_invoice_date').val($('#invoice_date').val());
    $('#invoiceModal').show();
}

function editInvoice(id) {
    resetForm();
    $('#modalTitle').text('Edit Invoice');
    $('#submitBtn').text('Update Invoice');
    
    $.ajax({
        url: 'get_invoice.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const invoice = response.data;
                $('#invoice_id').val(invoice.id);
                $('#customer_id').val(invoice.customer_id);
                $('#modal_invoice_date').val(invoice.invoice_date);
                $('#invoice_no').val(invoice.invoice_no);
                $('#destination').val(invoice.destination);
                $('#from_booking_date').val(invoice.from_date);
                $('#to_booking_date').val(invoice.to_date);
                $('#total_amount').val(invoice.total_amount);
                $('#gst_amount').val(invoice.gst_amount);
                $('#grand_total').val(invoice.grand_total);
                $('#status').val(invoice.status);
                
                $('#invoiceModal').show();
            } else {
                showToast('Error loading invoice: ' + response.message, 'error');
            }
        },
        error: function() {
            showToast('Error loading invoice', 'error');
        }
    });
}

function viewInvoice(id) {
    window.open(`view_invoice.php?id=${id}`, '_blank');
}

function deleteInvoice(id) {
    if (confirm('Are you sure you want to delete this invoice?')) {
        $.ajax({
            url: 'delete_invoice.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast('Invoice deleted successfully!', 'success');
                    loadInvoices(currentPage);
                } else {
                    showToast('Error deleting invoice: ' + response.message, 'error');
                }
            },
            error: function() {
                showToast('Error deleting invoice', 'error');
            }
        });
    }
}

function saveInvoice() {
    const formData = new FormData($('#invoiceForm')[0]);
    const url = $('#invoice_id').val() ? 'update_invoice.php' : 'save_invoice.php';
    
    $('#submitBtn').prop('disabled', true).text('Saving...');
    
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast('Invoice saved successfully!', 'success');
                closeModal();
                loadInvoices(currentPage);
            } else {
                showToast('Error saving invoice: ' + response.message, 'error');
            }
        },
        error: function() {
            showToast('Error saving invoice', 'error');
        },
        complete: function() {
            $('#submitBtn').prop('disabled', false).text($('#invoice_id').val() ? 'Update Invoice' : 'Save Invoice');
        }
    });
}

function calculateGrandTotal() {
    const totalAmount = parseFloat($('#total_amount').val()) || 0;
    const gstAmount = parseFloat($('#gst_amount').val()) || 0;
    const grandTotal = totalAmount + gstAmount;
    $('#grand_total').val(grandTotal.toFixed(2));
}

function resetForm() {
    $('#invoiceForm')[0].reset();
    $('#invoice_id').val('');
    $('#modal_invoice_date').val(new Date().toISOString().split('T')[0]);
    calculateGrandTotal();
}

function closeModal() {
    $('#invoiceModal').hide();
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN');
}

function showToast(message, type = 'info') {
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };
    
    Toastify({
        text: message,
        duration: 3000,
        gravity: "top",
        position: "right",
        style: {
            background: colors[type] || colors.info,
        }
    }).showToast();
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('invoiceModal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>

<?php require_once 'inc/footer.php'; ?>
