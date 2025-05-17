<?php
// order.php
include 'inc/header.php';
include 'inc/sidebar.php';
include 'inc/db.php';
?>
<div class="content-wrapper bg-light min-vh-100">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1 class="fw-bold text-primary">Order Page</h1>
                </div>
                <div class="col-sm-6 text-end">
                    <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addOrderModal"><i class="fas fa-plus"></i> Add Data</button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#excelModal"><i class="fas fa-file-excel"></i> Add by Excel</button>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0 fw-semibold text-secondary">Order List</h5>
                </div>
                <div class="card-body table-responsive" id="orderTableContainer">
                    <!-- Table will be loaded here by AJAX -->
                </div>
            </div>
            <!-- Add Order Modal -->
            <div class="modal fade" id="addOrderModal" tabindex="-1" role="dialog" aria-labelledby="addOrderModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addOrderModalLabel">Add Order</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">+</span>
                            </button>
                        </div>
                        <form id="orderForm">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Customer Select -->
                                <div class="col-md-3 mb-2">
                                    <label for="customer_id" class="form-label">Customer</label>
                                    <select name="customer_id" id="customer_id" class="form-select form-control" required>
                                        <option value="">Loading...</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" name="date" id="date" class="form-control" placeholder="Date">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="docket" class="form-label">Docket</label>
                                    <input type="text" name="docket" id="docket" class="form-control" placeholder="Docket">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" name="location" id="location" class="form-control" placeholder="Location">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="destination" class="form-label">Destination</label>
                                    <input type="text" name="destination" id="destination" class="form-control" placeholder="Destination">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="mode" class="form-label">Mode</label>
                                    <input type="text" name="mode" id="mode" class="form-control" placeholder="Mode">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="no_of_pcs" class="form-label">No of Pcs</label>
                                    <input type="number" name="no_of_pcs" id="no_of_pcs" class="form-control" placeholder="No of Pcs">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="pincode" class="form-label">Pincode</label>
                                    <input type="text" name="pincode" id="pincode" class="form-control" placeholder="Pincode">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="content" class="form-label">Content</label>
                                    <input type="text" name="content" id="content" class="form-control" placeholder="Content">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="dox_non_dox" class="form-label">Dox / Non Dox</label>
                                    <input type="text" name="dox_non_dox" id="dox_non_dox" class="form-control" placeholder="Dox / Non Dox">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="material_value" class="form-label">Material Value</label>
                                    <input type="number" step="0.01" name="material_value" id="material_value" class="form-control" placeholder="Material Value">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="fr_weight" class="form-label">FR Weight</label>
                                    <input type="number" step="0.01" name="fr_weight" id="fr_weight" class="form-control" placeholder="FR Weight">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="valumatric" class="form-label">Valumatric</label>
                                    <input type="number" step="0.01" name="valumatric" id="valumatric" class="form-control" placeholder="Valumatric">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="manual_weight" class="form-label">Manual Weight</label>
                                    <input type="number" step="0.01" name="manual_weight" id="manual_weight" class="form-control" placeholder="Manual Weight">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="invoice_wt" class="form-label">Invoice Wt</label>
                                    <input type="number" step="0.01" name="invoice_wt" id="invoice_wt" class="form-control" placeholder="Invoice Wt">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="round_off_weight" class="form-label">Round Off Weight</label>
                                    <input type="number" step="0.01" name="round_off_weight" id="round_off_weight" class="form-control" placeholder="Round Off Weight">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="clinet_billing_value" class="form-label">Clinet Billing Value</label>
                                    <input type="number" step="0.01" name="clinet_billing_value" id="clinet_billing_value" class="form-control" placeholder="Clinet Billing Value">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="credit_cust_amt" class="form-label">Credit Cust. Amt</label>
                                    <input type="number" step="0.01" name="credit_cust_amt" id="credit_cust_amt" class="form-control" placeholder="Credit Cust. Amt">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="regular_cust_amt" class="form-label">Regular Cust. Amt</label>
                                    <input type="number" step="0.01" name="regular_cust_amt" id="regular_cust_amt" class="form-control" placeholder="Regular Cust. Amt">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="customer_type" class="form-label">Customer Type</label>
                                    <input type="text" name="customer_type" id="customer_type" class="form-control" placeholder="Customer Type">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="sender_detail" class="form-label">Sender Detail</label>
                                    <input type="text" name="sender_detail" id="sender_detail" class="form-control" placeholder="Sender Detail">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="payment_status" class="form-label">PAYMENT STATUS</label>
                                    <input type="text" name="payment_status" id="payment_status" class="form-control" placeholder="PAYMENT STATUS">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="sender_contact_no" class="form-label">Sender Contact No</label>
                                    <input type="text" name="sender_contact_no" id="sender_contact_no" class="form-control" placeholder="Sender Contact No">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" name="address" id="address" class="form-control" placeholder="Address">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="adhaar_no" class="form-label">Adhaar No</label>
                                    <input type="text" name="adhaar_no" id="adhaar_no" class="form-control" placeholder="Adhaar No">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="customer_attend_by" class="form-label">Customer Attend By</label>
                                    <input type="text" name="customer_attend_by" id="customer_attend_by" class="form-control" placeholder="Customer Attend By">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="today_date" class="form-label">Today Date</label>
                                    <input type="date" name="today_date" id="today_date" class="form-control" placeholder="Today Date">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="pending" class="form-label">Pending</label>
                                    <input type="text" name="pending" id="pending" class="form-control" placeholder="Pending">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="td_delivery_status" class="form-label">T /D Delivery Status</label>
                                    <input type="text" name="td_delivery_status" id="td_delivery_status" class="form-control" placeholder="T /D Delivery Status">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="td_delivery_date" class="form-label">T / D Delivery Date</label>
                                    <input type="date" name="td_delivery_date" id="td_delivery_date" class="form-control" placeholder="T / D Delivery Date">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="t_receiver_name" class="form-label">T Receiver Name</label>
                                    <input type="text" name="t_receiver_name" id="t_receiver_name" class="form-control" placeholder="T Receiver Name">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="receiver_contact_no" class="form-label">Receiver Contact No</label>
                                    <input type="text" name="receiver_contact_no" id="receiver_contact_no" class="form-control" placeholder="Receiver Contact No">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="receiver_name_as_per_sendor" class="form-label">Receiver Name as per Sendor</label>
                                    <input type="text" name="receiver_name_as_per_sendor" id="receiver_name_as_per_sendor" class="form-control" placeholder="Receiver Name as per Sendor">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="ref" class="form-label">Ref</label>
                                    <input type="text" name="ref" id="ref" class="form-control" placeholder="Ref">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="complain_no_update" class="form-label">Complain No Update</label>
                                    <input type="text" name="complain_no_update" id="complain_no_update" class="form-control" placeholder="Complain No Update">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="shipment_cost_by_other_mode" class="form-label">Shipment Cost by other Mode</label>
                                    <input type="number" step="0.01" name="shipment_cost_by_other_mode" id="shipment_cost_by_other_mode" class="form-control" placeholder="Shipment Cost by other Mode">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <input type="text" name="remarks" id="remarks" class="form-control" placeholder="Remarks">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="pod_status" class="form-label">POD Status</label>
                                    <input type="text" name="pod_status" id="pod_status" class="form-control" placeholder="POD Status">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="pending_days" class="form-label">Pending Days</label>
                                    <input type="number" name="pending_days" id="pending_days" class="form-control" placeholder="Pending Days">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Order</button>
                        </div>
                        </form>
                        <script>
                        $(document).ready(function() {
                            // Populate customer select in Add Order Modal
                            $('#addOrderModal').on('show.bs.modal', function() {
                                // Use modal-scoped selector to avoid duplicate ID issues
                                var $select = $(this).find('select[name="customer_id"]');
                                $select.html('<option value="">Loading...</option>');
                                $.ajax({
                                    url: 'ajax/fetch_customers_select.php',
                                    type: 'GET',
                                    success: function(data) {
                                        console.log('Customer select AJAX response:', data);
                                        $select.html(data);
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error loading customer list:', error);
                                        $select.html('<option value="">Error loading customers</option>');
                                    }
                                });
                            });
                        });
                        </script>
                    </div>
                </div>
            </div>
            <!-- Excel Upload Modal -->
            <div class="modal fade" id="excelModal" tabindex="-1" role="dialog" aria-labelledby="excelModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="excelModalLabel">Bulk Import Orders (Excel)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">+</span>
                            </button>
                        </div>
                        <form id="excelUploadForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="file" name="excel_file" accept=".xls,.xlsx" required>
                            <small>Excel columns must match the order fields exactly.</small>
                            <hr>
                            <div class="alert alert-info p-2">
                                <b>Excel Example (first row as header):</b>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Docket</th>
                                                <th>Location</th>
                                                <th>Destination</th>
                                                <th>Mode</th>
                                                <th>No of Pcs</th>
                                                <th>Pincode</th>
                                                <th>Content</th>
                                                <th>Dox / Non Dox</th>
                                                <th>Material Value</th>
                                                <th>FR Weight</th>
                                                <th>Valumatric</th>
                                                <th>Manual Weight</th>
                                                <th>Invoice Wt</th>
                                                <th>Round Off Weight</th>
                                                <th>Clinet Billing Value</th>
                                                <th>Credit Cust. Amt</th>
                                                <th>Regular Cust. Amt</th>
                                                <th>Customer Type</th>
                                                <th>Sender Detail</th>
                                                <th>PAYMENT STATUS</th>
                                                <th>Sender Contact No</th>
                                                <th>Address</th>
                                                <th>Adhaar No</th>
                                                <th>Customer Attend By</th>
                                                <th>Today Date</th>
                                                <th>pending</th>
                                                <th>T /D Delivery Status</th>
                                                <th>T / D Delivery Date</th>
                                                <th>T  Receiver Name</th>
                                                <th>Receiver Contact No</th>
                                                <th>Receiver Name as per Sendor</th>
                                                <th>Ref</th>
                                                <th>Complain No Update</th>
                                                <th>Shipment Cost by other Mode</th>
                                                <th>Remarks</th>
                                                <th>POD Status</th>
                                                <th>Pending Days</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>2024-05-08</td>
                                                <td>DKT123</td>
                                                <td>Delhi</td>
                                                <td>Mumbai</td>
                                                <td>Air</td>
                                                <td>10</td>
                                                <td>110001</td>
                                                <td>Documents</td>
                                                <td>Dox</td>
                                                <td>1000</td>
                                                <td>5.5</td>
                                                <td>6.0</td>
                                                <td>5.8</td>
                                                <td>6.0</td>
                                                <td>6</td>
                                                <td>1200</td>
                                                <td>500</td>
                                                <td>700</td>
                                                <td>Regular</td>
                                                <td>ABC Pvt Ltd</td>
                                                <td>Paid</td>
                                                <td>9876543210</td>
                                                <td>123 Street, Delhi</td>
                                                <td>123456789012</td>
                                                <td>John Doe</td>
                                                <td>2024-05-08</td>
                                                <td>No</td>
                                                <td>Delivered</td>
                                                <td>2024-05-09</td>
                                                <td>Jane Smith</td>
                                                <td>9876543211</td>
                                                <td>Jane Smith</td>
                                                <td>REF001</td>
                                                <td>None</td>
                                                <td>200</td>
                                                <td>OK</td>
                                                <td>Received</td>
                                                <td>0</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Upload Excel</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- View Order Modal -->
            <div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewOrderModalLabel">Order Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">+</span>
                            </button>
                        </div>
                        <div class="modal-body" id="viewOrderBody">
                            <!-- Order details will be loaded here by AJAX -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit Order Modal -->
            <div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editOrderModalLabel">Edit Order</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">+</span>
                            </button>
                        </div>
                        <form id="editOrderForm">
                        <div class="modal-body" id="editOrderBody">
                            <!-- Edit form fields will be loaded here by AJAX -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include 'inc/footer.php'; ?>
<!-- Bootstrap JS for modal support -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    function loadOrders(page = 1) {
        $.ajax({
            url: 'ajax/fetch_orders.php',
            type: 'GET',
            data: {page: page},
            success: function(html) {
                $('#orderTableContainer').html(html);
            },
            error: function() {
                $('#orderTableContainer').html('<div class="alert alert-danger">Failed to load orders.</div>');
            }
        });
    }
    loadOrders();
    $(document).on('click', '.order-pagination .page-link', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        if(page) loadOrders(page);
    });

    $('#orderForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var $btn = $(this).find('button[type="submit"]');
        var $closeBtn = $(this).find('button[data-bs-dismiss="modal"], .btn-close');
        $btn.prop('disabled', true).text('Processing...');
        $closeBtn.prop('disabled', true);
        $.ajax({
            url: 'ajax/add_order.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                $btn.prop('disabled', false).text('Add Order');
                $closeBtn.prop('disabled', false);
                if(response.success) {
                    $('#addOrderModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message || 'Error adding order.');
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('Add Order');
                $closeBtn.prop('disabled', false);
                alert('AJAX error.');
            }
        });
    });

    $('#excelUploadForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var $btn = $(this).find('button[type="submit"]');
        var $closeBtn = $(this).find('button[data-bs-dismiss="modal"], .btn-close');
        $btn.prop('disabled', true).text('Processing...');
        $closeBtn.prop('disabled', true);
        $.ajax({
            url: 'ajax/upload_orders.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                $btn.prop('disabled', false).text('Upload Excel');
                $closeBtn.prop('disabled', false);
                if(response.success) {
                    $('#excelModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message || 'Error uploading Excel.');
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('Upload Excel');
                $closeBtn.prop('disabled', false);
                alert('AJAX error.');
            }
        });
    });

    $(document).on('click', '.view-order', function() {
        var id = $(this).data('id');
        $.ajax({
            url: 'ajax/view_order.php',
            type: 'POST',
            data: {id: id},
            dataType: 'html',
            success: function(response) {
                $('#viewOrderBody').html(response);
                var modal = new bootstrap.Modal(document.getElementById('viewOrderModal'));
                modal.show();
            },
            error: function() {
                alert('Failed to load order details.');
            }
        });
    });

    $(document).on('click', '.edit-order', function() {
        var id = $(this).data('id');
        $.ajax({
            url: 'ajax/edit_order.php',
            type: 'POST',
            data: {id: id, action: 'fetch'},
            dataType: 'html',
            success: function(response) {
                $('#editOrderBody').html(response);
                var modal = new bootstrap.Modal(document.getElementById('editOrderModal'));
                modal.show();
            },
            error: function() {
                alert('Failed to load order for edit.');
            }
        });
    });

    $('#editOrderForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var $btn = $(this).find('button[type="submit"]');
        var $closeBtn = $(this).find('button[data-bs-dismiss="modal"], .btn-close');
        $btn.prop('disabled', true).text('Processing...');
        $closeBtn.prop('disabled', true);
        $.ajax({
            url: 'ajax/edit_order.php',
            type: 'POST',
            data: formData + '&action=update',
            dataType: 'json',
            success: function(response) {
                $btn.prop('disabled', false).text('Save Changes');
                $closeBtn.prop('disabled', false);
                if(response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('editOrderModal'));
                    modal.hide();
                    location.reload();
                } else {
                    alert(response.message || 'Error updating order.');
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('Save Changes');
                $closeBtn.prop('disabled', false);
                alert('AJAX error.');
            }
        });
    });

    $(document).on('click', '.delete-order', function() {
        var id = $(this).data('id');
        if(confirm('Are you sure you want to delete this order?')) {
            $.ajax({
                url: 'ajax/delete_order.php',
                type: 'POST',
                data: {id: id},
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        // Remove the row from the table without reloading
                        $('button.delete-order[data-id="'+id+'"]')
                            .closest('tr').fadeOut(400, function() { $(this).remove(); });
                    } else {
                        alert(response.message || 'Error deleting order.');
                    }
                },
                error: function() {
                    alert('AJAX error.');
                }
            });
        }
    });
});
</script>
<style>
body, .content-wrapper { background: #f8fafc !important; }
.card { border-radius: 12px; }
.table th, .table td { vertical-align: middle !important; }
.btn { font-weight: 500; }
.table thead th { background: #f1f3f6; color: #333; }
.order-pagination .page-item.active .page-link { background: #007bff; color: #fff; border-color: #007bff; }
.order-pagination .page-link { cursor:pointer; }
</style>
