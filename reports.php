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
        <h2>Reports</h2>
        <p>Search and download reports here.</p>

        <!-- Search Reports Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Search Reports</h3>
            </div>
            <div class="card-body">
                <form action="reports.php" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="report_type">Report Type</label>
                                <select name="report_type" id="report_type" class="form-control">
                                    <option value="invoices">Invoices</option>
                                    <option value="customers">Customers</option>
                                    <option value="destinations">Destinations</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
        </div>

        <!-- Reports List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Report Results</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Details</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Example: Fetch filtered reports based on GET parameters
                        $conn = new mysqli('localhost', 'root', '', 'alok_crm');
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $where = [];
                        if (!empty($_GET['start_date'])) {
                            $where[] = "created_at >= '" . $conn->real_escape_string($_GET['start_date']) . "'";
                        }
                        if (!empty($_GET['end_date'])) {
                            $where[] = "created_at <= '" . $conn->real_escape_string($_GET['end_date']) . "'";
                        }
                        if (!empty($_GET['report_type'])) {
                            $where[] = "type = '" . $conn->real_escape_string($_GET['report_type']) . "'";
                        }

                        $sql = "SELECT * FROM reports";
                        if (!empty($where)) {
                            $sql .= " WHERE " . implode(' AND ', $where);
                        }

                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['type']}</td>
                                    <td>{$row['created_at']}</td>
                                    <td>{$row['details']}</td>
                                    <td>
                                        <a href='download_report.php?id={$row['id']}' class='btn btn-sm btn-success'>Download</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>No reports found</td></tr>";
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