<?php
// order.php
include 'inc/header.php';
include 'inc/sidebar.php';
include 'inc/db.php';

// Handle manual form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manual_submit'])) {
    $fields = [
        'date','docket','location','destination','mode','no_of_pcs','pincode','content','dox_non_dox','material_value','fr_weight','valumatric','manual_weight','invoice_wt','round_off_weight','clinet_billing_value','credit_cust_amt','regular_cust_amt','customer_type','sender_detail','payment_status','sender_contact_no','address','adhaar_no','customer_attend_by','today_date','pending','td_delivery_status','td_delivery_date','t_receiver_name','receiver_contact_no','receiver_name_as_per_sendor','ref','complain_no_update','shipment_cost_by_other_mode','remarks','pod_status','pending_days'
    ];
    $values = [];
    foreach ($fields as $f) {
        $values[$f] = isset($_POST[$f]) ? $_POST[$f] : null;
    }
    $stmt = $conn->prepare("INSERT INTO orders (".implode(",", $fields).") VALUES (".str_repeat('?,', count($fields)-1)."?)");
    $stmt->bind_param(
        str_repeat('s', count($fields)),
        ...array_values($values)
    );
    if ($stmt->execute()) {
        $msg = "Order added successfully.";
    } else {
        $msg = "Error: " . $stmt->error;
    }
    $stmt->close();
}
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
            <?php if (!empty($msg)) { echo '<div class="alert alert-info">'.$msg.'</div>'; } ?>
            <div class="card mb-4">
                <div class="card-header"><b>Manual Order Entry</b></div>
                <div class="card-body">
                    <form method="post">
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
                        <button type="submit" name="manual_submit" class="btn btn-primary mt-3">Add Order</button>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><b>Bulk Import Orders (Excel)</b></div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data" action="upload_orders.php">
                        <input type="file" name="excel_file" accept=".xls,.xlsx" required>
                        <button type="submit" class="btn btn-success">Upload Excel</button>
                    </form>
                    <small>Excel columns must match the order fields exactly.</small>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include 'inc/footer.php'; ?>
