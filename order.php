<?php
// order.php
include 'inc/header.php';
include 'inc/sidebar.php';
include 'inc/db.php';

// Fetch all orders for listing
$order_result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Order Page</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="mb-3">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addOrderModal">Add Data</button>
                <button class="btn btn-success" data-toggle="modal" data-target="#excelModal">Add by Excel</button>
            </div>
            <!-- Orders List Table -->
            <div class="card">
                <div class="card-header"><b>Order List</b></div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
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
                                <th>Action</th>
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
                                <td><!-- Action buttons can go here --></td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="12">No orders found.</td></tr>
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
                        <form method="post" enctype="multipart/form-data" action="upload_orders.php">
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
        </div>
    </section>
</div>
<?php include 'inc/footer.php'; ?>
<!-- Bootstrap JS for modal support -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
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
});
</script>
