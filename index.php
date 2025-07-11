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

// Total Destinations
$result = $conn->query("SELECT COUNT(*) as total FROM destinations");
$stats['destinations'] = $result->fetch_assoc()['total'];

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

// Fetch revenue for the last 6 months for the chart
$revenue_by_month = [];
$months = [];
$revenue_query = $conn->query("SELECT DATE_FORMAT(invoice_date, '%b %Y') as month, SUM(grand_total) as revenue FROM invoices WHERE status = 'paid' GROUP BY month ORDER BY MIN(invoice_date) DESC LIMIT 6");
while ($row = $revenue_query->fetch_assoc()) {
    $months[] = $row['month'];
    $revenue_by_month[] = $row['revenue'] ?? 0;
}
$months = array_reverse($months);
$revenue_by_month = array_reverse($revenue_by_month);
?>

<main class='content-wrapper'>
    <div class='container-fluid p-3'>
        <h2>Dashboard</h2>
        <p>Welcome to Courier Billing CRM</p>

        <!-- Quick Actions -->
        <div class="mb-4 d-flex flex-wrap gap-2">
            <a href="customers.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add Customer</a>
            <a href="invoices.php" class="btn btn-success"><i class="fas fa-file-invoice"></i> Create Invoice</a>
            <a href="order.php" class="btn btn-warning"><i class="fas fa-shopping-cart"></i> New Order</a>
            <a href="reports.php" class="btn btn-info"><i class="fas fa-chart-bar"></i> View Reports</a>
        </div>

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
                <!-- Total Destinations Card -->
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3><?php echo number_format($stats['destinations']); ?></h3>
                        <p>Total Destinations</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <a href="destination.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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

        <!-- Revenue Trend Chart -->
        <div class="card mb-4">
            <div class="card-header bg-gradient-primary text-white">
                <h3 class="card-title mb-0"><i class="fas fa-chart-line"></i> Revenue Trend (Last 6 Months)</h3>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="80"></canvas>
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
        
        <!-- Loader Test Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cog"></i> Loader System Test
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Test the different loader types:</p>
                        <div class="btn-group" role="group">
                            <button class="btn btn-primary" onclick="testPageLoader()">
                                <i class="fas fa-spinner"></i> Test Page Loader
                            </button>
                            <button class="btn btn-success" onclick="testActionLoader()">
                                <i class="fas fa-tasks"></i> Test Action Loader
                            </button>
                            <button class="btn btn-warning" onclick="testDashboardLoader()">
                                <i class="fas fa-tachometer-alt"></i> Test Dashboard Loader
                            </button>
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
<!-- Loader Debug Script (temporary) -->
<script src="js/loader-debug.js"></script>
<script>
    // Enhanced dashboard loader with comprehensive loading steps
    document.addEventListener('DOMContentLoaded', function() {
        initDashboardLoader();
    });

    function initDashboardLoader() {
        // Show initial page loader
        if (typeof showLoader !== 'undefined') {
            showLoader('Loading Dashboard', 'Initializing CRM Dashboard...');
            
            // Simulate multi-step loading process
            setTimeout(() => {
                if (typeof showActionLoader !== 'undefined') {
                    showActionLoader('Loading Statistics...', 'Fetching customer and invoice data');
                }
                
                setTimeout(() => {
                    if (typeof showActionLoader !== 'undefined') {
                        showActionLoader('Preparing Charts...', 'Setting up revenue analytics');
                    }
                    
                    setTimeout(() => {
                        if (typeof showActionLoader !== 'undefined') {
                            showActionLoader('Finalizing Dashboard...', 'Almost ready');
                        }
                        
                        setTimeout(() => {
                            if (typeof hideActionLoader !== 'undefined') {
                                hideActionLoader();
                            }
                            if (typeof hideLoader !== 'undefined') {
                                hideLoader();
                            }
                            
                            // Show success message if available
                            if (typeof showToast !== 'undefined') {
                                showToast('Dashboard loaded successfully!', 'success');
                            }
                        }, 800);
                    }, 600);
                }, 700);
            }, 1000);
        } else {
            // Fallback for when loader system is not available
            console.log('Loading dashboard...');
        }
    }

    // Test functions for loader system
    function testPageLoader() {
        if (typeof showLoader !== 'undefined') {
            showLoader('Testing Page Loader', 'This is a test of the page loader system...');
            setTimeout(() => {
                if (typeof hideLoader !== 'undefined') {
                    hideLoader();
                }
            }, 3000);
        } else {
            alert('Loader system not available. Please check if loader.js is loaded.');
        }
    }

    function testActionLoader() {
        if (typeof showActionLoader !== 'undefined') {
            showActionLoader('Testing Action Loader...', 'This demonstrates the action loader');
            setTimeout(() => {
                if (typeof hideActionLoader !== 'undefined') {
                    hideActionLoader();
                }
            }, 2500);
        } else {
            alert('Action loader not available. Please check if loader.js is loaded.');
        }
    }

    function testDashboardLoader() {
        initDashboardLoader();
    }

    // Fallback loader function
    function showFallbackLoader(title = 'Loading...', subtitle = 'Please wait') {
        // Remove existing fallback loader
        const existing = document.getElementById('fallbackLoader');
        if (existing) existing.remove();
        
        // Create fallback loader
        const loader = document.createElement('div');
        loader.id = 'fallbackLoader';
        loader.className = 'fallback-loader';
        loader.innerHTML = `
            <div class="fallback-loader-content">
                <div class="fallback-spinner"></div>
                <div class="fallback-text">${title}</div>
                <div class="fallback-subtext">${subtitle}</div>
            </div>
        `;
        
        document.body.appendChild(loader);
        return loader;
    }

    function hideFallbackLoader() {
        const loader = document.getElementById('fallbackLoader');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => loader.remove(), 300);
        }
    }

    // Enhanced initialization with fallback support
    document.addEventListener('DOMContentLoaded', function() {
        // Try main loader system first
        if (typeof showLoader !== 'undefined') {
            initDashboardLoader();
        } else {
            // Use fallback loader
            console.log('Main loader system not available, using fallback');
            const fallback = showFallbackLoader('Loading Dashboard', 'Preparing your workspace...');
            
            setTimeout(() => {
                hideFallbackLoader();
            }, 2000);
        }
    });

    // Revenue chart with real data from PHP
    const revenueLabels = <?php echo json_encode($months); ?>;
    const revenueData = <?php echo json_encode($revenue_by_month); ?>;
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Revenue (₹)',
                data: revenueData,
                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                borderColor: 'rgba(60, 141, 188, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(60, 141, 188, 1)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>