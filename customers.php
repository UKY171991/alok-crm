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
        <h2>Customers Management</h2>

        <!-- Add New Customer Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Add New Customer</h3>
            </div>
            <div class="card-body">
                <form action="add_customer.php" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea name="address" id="address" class="form-control" rows="2" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="gst_no">GST No</label>
                                <input type="text" name="gst_no" id="gst_no" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hsn_code">HSN Code</label>
                                <input type="text" name="hsn_code" id="hsn_code" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pan_no">PAN No</label>
                                <input type="text" name="pan_no" id="pan_no" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cin_no">CIN No</label>
                                <input type="text" name="cin_no" id="cin_no" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="aadhaar_no">Aadhaar No</label>
                                <input type="text" name="aadhaar_no" id="aadhaar_no" class="form-control">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Customer</button>
                </form>
            </div>
        </div>

        <!-- Customers List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Customers List</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>GST No</th>
                            <th>HSN Code</th>
                            <th>PAN No</th>
                            <th>CIN No</th>
                            <th>Aadhaar No</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                       
                        // Fetch customers
                        $sql = "SELECT * FROM customers";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['address']}</td>
                                    <td>{$row['phone']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['gst_no']}</td>
                                    <td>{$row['hsn_code']}</td>
                                    <td>{$row['pan_no']}</td>
                                    <td>{$row['cin_no']}</td>
                                    <td>{$row['aadhaar_no']}</td>
                                    <td>{$row['created_at']}</td>
                                    <td>
                                        <a href='edit_customer.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                                        <a href='delete_customer.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='12' class='text-center'>No customers found</td></tr>";
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