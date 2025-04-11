<?php
session_start();

// Redirect to login.php if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

include 'inc/header.php';
include 'inc/sidebar.php';
?>
<main class='content-wrapper'>
    <div class='container-fluid p-3'>
        <h2>Rates Management</h2>

        <!-- Add New Rate Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Add New Rate</h3>
            </div>
            <div class="card-body">
                <form action="add_rate.php" method="POST">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="destination">Destination</label>
                                <input type="text" name="destination" id="destination" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="Normal">Normal</option>
                                    <option value="Premium">Premium</option>
                                    <option value="Bulk_Surface">Bulk Surface</option>
                                    <option value="Bulk_Air">Bulk Air</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="weight_category">Weight Category</label>
                                <input type="text" name="weight_category" id="weight_category" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="rate">Rate</label>
                                <input type="number" step="0.01" name="rate" id="rate" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Rate</button>
                </form>
            </div>
        </div>

        <!-- Rates List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Rates List</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Destination</th>
                            <th>Type</th>
                            <th>Weight Category</th>
                            <th>Rate</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Database connection
                        $conn = new mysqli('localhost', 'root', '', 'alok_crm');
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Fetch rates
                        $sql = "SELECT * FROM rates";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['destination']}</td>
                                    <td>{$row['type']}</td>
                                    <td>{$row['weight_category']}</td>
                                    <td>{$row['rate']}</td>
                                    <td>{$row['created_at']}</td>
                                    <td>
                                        <a href='edit_rate.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                                        <a href='delete_rate.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No rates found</td></tr>";
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