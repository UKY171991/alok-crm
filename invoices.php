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
        <h2>Invoices Management</h2>

        <!-- Add New Invoice Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Add New Invoice</h3>
            </div>
            <div class="card-body">
                <form action="add_invoice.php" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="invoice_no">Invoice No</label>
                                <input type="text" name="invoice_no" id="invoice_no" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_id">Customer ID</label>
                                <input type="number" name="customer_id" id="customer_id" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="invoice_date">Invoice Date</label>
                                <input type="date" name="invoice_date" id="invoice_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="total_amount">Total Amount</label>
                                <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gst_amount">GST Amount</label>
                                <input type="number" step="0.01" name="gst_amount" id="gst_amount" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="grand_total">Grand Total</label>
                                <input type="number" step="0.01" name="grand_total" id="grand_total" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Invoice</button>
                </form>
            </div>
        </div>

        <!-- Invoices List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Invoices List</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Invoice No</th>
                            <th>Customer ID</th>
                            <th>Invoice Date</th>
                            <th>Total Amount</th>
                            <th>GST Amount</th>
                            <th>Grand Total</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                  
                        // Fetch invoices
                        $sql = "SELECT * FROM invoices";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['invoice_no']}</td>
                                    <td>{$row['customer_id']}</td>
                                    <td>{$row['invoice_date']}</td>
                                    <td>{$row['total_amount']}</td>
                                    <td>{$row['gst_amount']}</td>
                                    <td>{$row['grand_total']}</td>
                                    <td>{$row['created_at']}</td>
                                    <td>
                                        <a href='edit_invoice.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                                        <a href='delete_invoice.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9' class='text-center'>No invoices found</td></tr>";
                        }

                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php include 'inc/footer.php'; ?>