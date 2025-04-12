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

// Fetch dashboard statistics
$stats = [];

// Total Customers
$result = $conn->query("SELECT COUNT(*) as total FROM customers");
$stats['customers'] = $result->fetch_assoc()['total'];

// Total Invoices
$result = $conn->query("SELECT COUNT(*) as total FROM invoices");
$stats['invoices'] = $result->fetch_assoc()['total'];

// Total Revenue (Sum of paid invoices)
$result = $conn->query("SELECT SUM(grand_total) as total FROM invoices WHERE status = 'paid'");
$stats['revenue'] = $result->fetch_assoc()['total'] ?? 0;

// Pending Invoices
$result = $conn->query("SELECT COUNT(*) as total FROM invoices WHERE status = 'pending'");
$stats['pending_invoices'] = $result->fetch_assoc()['total'];

// Recent Invoices
$recent_invoices = $conn->query("
    SELECT i.*, c.name as customer_name 
    FROM invoices i 
    LEFT JOIN customers c ON i.customer_id = c.id 
    ORDER BY i.created_at DESC 
    LIMIT 5
");

// Recent Customers
$recent_customers = $conn->query("
    SELECT * FROM customers 
    ORDER BY created_at DESC 
    LIMIT 5
");
?>

<main class='content-wrapper'>
    <div class='container-fluid p-3'>
        <h2>Dashboard</h2>
        <p>Welcome to Courier Billing CRM</p>

        <!-- Dashboard Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- Total Customers Card -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo number_format($stats['customers']); ?></h3>
                        <p>Total Customers</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="customers.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- Total Invoices Card -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?php echo number_format($stats['invoices']); ?></h3>
                        <p>Total Invoices</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <a href="invoices.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- Total Revenue Card -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>₹<?php echo number_format($stats['revenue'], 2); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <a href="reports.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- Pending Invoices Card -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?php echo number_format($stats['pending_invoices']); ?></h3>
                        <p>Pending Invoices</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="invoices.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="row">
            <!-- Recent Invoices -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Invoices</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Invoice No</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($invoice = $recent_invoices->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($invoice['invoice_no']); ?></td>
                                        <td><?php echo htmlspecialchars($invoice['customer_name']); ?></td>
                                        <td>₹<?php echo number_format($invoice['grand_total'], 2); ?></td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $invoice['status'] == 'paid' ? 'success' : 
                                                    ($invoice['status'] == 'pending' ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php echo ucfirst($invoice['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Customers -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Customers</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($customer = $recent_customers->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php 
$conn->close();
include 'inc/footer.php'; 
?>

<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sample data for the graph
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [{
                label: 'Revenue (₹)',
                data: [1200, 1900, 3000, 5000, 2300, 3400, 4500],
                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                borderColor: 'rgba(60, 141, 188, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>