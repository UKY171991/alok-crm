<?php
// order.php
include 'inc/header.php';
include 'inc/sidebar.php';
include 'inc/db.php';

// Fetch all orders for listing
$order_result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
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
                <div class="card-body table-responsive">
                    <table class="table table-hover table-bordered align-middle" id="orderTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Docket</th>
                                <th>Location</th>
                                <th>Destination</th>
                                <th>Mode</th>
                                <th>No of Pcs</th>
                                <th>Pincode</th>
                                <th>Content</th>
                                <th>Sender</th>
                                <th>Receiver</th>
                                <th style="min-width:120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($order_result && $order_result->num_rows > 0):
                            while($row = $order_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                                <td><?php echo htmlspecialchars($row['docket']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td><?php echo htmlspecialchars($row['destination']); ?></td>
                                <td><?php echo htmlspecialchars($row['mode']); ?></td>
                                <td><?php echo htmlspecialchars($row['no_of_pcs']); ?></td>
                                <td><?php echo htmlspecialchars($row['pincode']); ?></td>
                                <td><?php echo htmlspecialchars($row['content']); ?></td>
                                <td><?php echo htmlspecialchars($row['sender_detail']); ?></td>
                                <td><?php echo htmlspecialchars($row['t_receiver_name']); ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm view-order mb-1 w-100" data-id="<?php echo $row['id']; ?>"><i class="fas fa-eye"></i> View</button>
                                    <button class="btn btn-warning btn-sm edit-order mb-1 w-100" data-id="<?php echo $row['id']; ?>"><i class="fas fa-edit"></i> Edit</button>
                                    <button class="btn btn-danger btn-sm delete-order w-100" data-id="<?php echo $row['id']; ?>"><i class="fas fa-trash"></i> Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="12" class="text-center text-muted">No orders found.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Add Order Modal -->
            <div class="modal fade" id="addOrderModal" tabindex="-1" role="dialog" aria-labelledby="addOrderModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addOrderModalLabel">Add Order</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="orderForm">
                        <div class="modal-body">
                            <div class="row">
                                <!-- All fields except 'l' -->
                                <div class="col-md-3 mb-2"><input type="date" name="date" class="form-control" placeholder="Date"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="docket" class="form-control" placeholder="Docket"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="location" class="form-control" placeholder="Location"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="destination" class="form-control" placeholder="Destination"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="mode" class="form-control" placeholder="Mode"></div>
                                <div class="col-md-3 mb-2"><input type="number" name="no_of_pcs" class="form-control" placeholder="No of Pcs"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="pincode" class="form-control" placeholder="Pincode"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="content" class="form-control" placeholder="Content"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="dox_non_dox" class="form-control" placeholder="Dox / Non Dox"></div>
                                <div class="col-md-3 mb-2"><input type="number" step="0.01" name="material_value" class="form-control" placeholder="Material Value"></div>
                                <div class="col-md-3 mb-2"><input type="number" step="0.01" name="fr_weight" class="form-control" placeholder="FR Weight"></div>
                                <div class="col-md-3 mb-2"><input type="number" step="0.01" name="valumatric" class="form-control" placeholder="Valumatric"></div>
                                <div class="col-md-3 mb-2"><input type="number" step="0.01" name="manual_weight" class="form-control" placeholder="Manual Weight"></div>
                                <div class="col-md-3 mb-2"><input type="number" step="0.01" name="invoice_wt" class="form-control" placeholder="Invoice Wt"></div>
                                <div class="col-md-3 mb-2"><input type="number" step="0.01" name="round_off_weight" class="form-control" placeholder="Round Off Weight"></div>
                                <div class="col-md-3 mb-2"><input type="number" step="0.01" name="clinet_billing_value" class="form-control" placeholder="Clinet Billing Value"></div>
                                <div class="col-md-3 mb-2"><input type="number" step="0.01" name="credit_cust_amt" class="form-control" placeholder="Credit Cust.  Amt"></div>
                                <div class="col-md-3 mb-2"><input type="number" step="0.01" name="regular_cust_amt" class="form-control" placeholder="Regular Cust. Amt"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="customer_type" class="form-control" placeholder="Customer Type"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="sender_detail" class="form-control" placeholder="Sender Detail"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="payment_status" class="form-control" placeholder="PAYMENT STATUS"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="sender_contact_no" class="form-control" placeholder="Sender Contact No"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="address" class="form-control" placeholder="Address"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="adhaar_no" class="form-control" placeholder="Adhaar No"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="customer_attend_by" class="form-control" placeholder="Customer Attend By"></div>
                                <div class="col-md-3 mb-2"><input type="date" name="today_date" class="form-control" placeholder="Today Date"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="pending" class="form-control" placeholder="pending"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="td_delivery_status" class="form-control" placeholder="T /D Delivery Status"></div>
                                <div class="col-md-3 mb-2"><input type="date" name="td_delivery_date" class="form-control" placeholder="T / D Delivery Date"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="t_receiver_name" class="form-control" placeholder="T  Receiver Name"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="receiver_contact_no" class="form-control" placeholder="Receiver Contact No"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="receiver_name_as_per_sendor" class="form-control" placeholder="Receiver Name as per Sendor"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="ref" class="form-control" placeholder="Ref"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="complain_no_update" class="form-control" placeholder="Complain No Update"></div>
                                <div class="col-md-3 mb-2"><input type="number" step="0.01" name="shipment_cost_by_other_mode" class="form-control" placeholder="Shipment Cost by other Mode"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="remarks" class="form-control" placeholder="Remarks"></div>
                                <div class="col-md-3 mb-2"><input type="text" name="pod_status" class="form-control" placeholder="POD Status"></div>
                                <div class="col-md-3 mb-2"><input type="number" name="pending_days" class="form-control" placeholder="Pending Days"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Order</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Excel Upload Modal -->
            <div class="modal fade" id="excelModal" tabindex="-1" role="dialog" aria-labelledby="excelModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="excelModalLabel">Bulk Import Orders (Excel)</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="excelUploadForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="file" name="excel_file" accept=".xls,.xlsx" required>
                            <small>Excel columns must match the order fields exactly.</small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
    $('#orderForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: 'ajax/add_order.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $('#addOrderModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message || 'Error adding order.');
                }
            },
            error: function() {
                alert('AJAX error.');
            }
        });
    });

    $('#excelUploadForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'ajax/upload_orders.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $('#excelModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message || 'Error uploading Excel.');
                }
            },
            error: function() {
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
        $.ajax({
            url: 'ajax/edit_order.php',
            type: 'POST',
            data: formData + '&action=update',
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('editOrderModal'));
                    modal.hide();
                    location.reload();
                } else {
                    alert(response.message || 'Error updating order.');
                }
            },
            error: function() {
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
</style>
