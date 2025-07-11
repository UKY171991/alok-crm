<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include 'inc/header.php';
include 'inc/sidebar.php';
?>

<main class="content-wrapper">
    <div class="container-fluid p-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="mb-3"><i class="fas fa-cog text-primary"></i> Loader System Demo</h1>
                <p class="lead">Comprehensive demonstration of all loader types and features in the CRM system.</p>
            </div>
        </div>

        <!-- Page Loaders Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-spinner"></i> Page Loaders</h5>
                    </div>
                    <div class="card-body">
                        <p>Full-screen overlay loaders for page transitions and initial loading.</p>
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-primary" onclick="demoPageLoader()">
                                <i class="fas fa-play"></i> Show Page Loader
                            </button>
                            <button class="btn btn-outline-primary" onclick="demoCustomPageLoader()">
                                <i class="fas fa-magic"></i> Custom Page Loader
                            </button>
                            <button class="btn btn-outline-secondary" onclick="hideLoader()">
                                <i class="fas fa-stop"></i> Hide Loader
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Loaders Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-tasks"></i> Action Loaders</h5>
                    </div>
                    <div class="card-body">
                        <p>Modal-style loaders for actions like form submissions and AJAX requests.</p>
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-success" onclick="demoActionLoader()">
                                <i class="fas fa-play"></i> Show Action Loader
                            </button>
                            <button class="btn btn-outline-success" onclick="demoCustomActionLoader()">
                                <i class="fas fa-cogs"></i> Processing Action
                            </button>
                            <button class="btn btn-outline-secondary" onclick="hideActionLoader()">
                                <i class="fas fa-stop"></i> Hide Action Loader
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Button Loaders Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-mouse-pointer"></i> Button Loaders</h5>
                    </div>
                    <div class="card-body">
                        <p>Loading states for individual buttons during actions.</p>
                        <div class="d-grid gap-2">
                            <button id="demoBtn1" class="btn btn-warning" onclick="demoButtonLoader(this)">
                                <i class="fas fa-save"></i> Save Data
                            </button>
                            <button id="demoBtn2" class="btn btn-outline-warning" onclick="demoButtonLoader(this)">
                                <i class="fas fa-upload"></i> Upload File
                            </button>
                            <button id="demoBtn3" class="btn btn-outline-warning" onclick="demoButtonLoader(this)">
                                <i class="fas fa-sync"></i> Refresh Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Loaders Section -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-edit"></i> Form Loaders</h5>
                    </div>
                    <div class="card-body">
                        <p>Loading states for entire forms during submission.</p>
                        <form id="demoForm" class="loader-form">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" placeholder="Enter name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="Enter email">
                            </div>
                            <button type="button" class="btn btn-info" onclick="demoFormLoader()">
                                <i class="fas fa-paper-plane"></i> Submit Form
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Loaders Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-table"></i> Table Loaders</h5>
                    </div>
                    <div class="card-body">
                        <p>Loading overlays for table data refreshing.</p>
                        <div class="mb-3">
                            <button class="btn btn-dark" onclick="demoTableLoader()">
                                <i class="fas fa-refresh"></i> Load Table Data
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="demoTable" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>John Doe</td>
                                        <td>john@example.com</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Edit</button>
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Jane Smith</td>
                                        <td>jane@example.com</td>
                                        <td><span class="badge bg-warning">Pending</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Edit</button>
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Bob Johnson</td>
                                        <td>bob@example.com</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Edit</button>
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AJAX Loaders Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-purple text-white" style="background-color: #6f42c1 !important;">
                        <h5 class="mb-0"><i class="fas fa-cloud"></i> AJAX with Loaders</h5>
                    </div>
                    <div class="card-body">
                        <p>Demonstration of AJAX requests with automatic loader handling.</p>
                        <div class="row">
                            <div class="col-md-4">
                                <button class="btn btn-outline-purple w-100 mb-2" onclick="demoAjaxLoader('GET')" style="border-color: #6f42c1; color: #6f42c1;">
                                    <i class="fas fa-download"></i> GET Request
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-outline-purple w-100 mb-2" onclick="demoAjaxLoader('POST')" style="border-color: #6f42c1; color: #6f42c1;">
                                    <i class="fas fa-upload"></i> POST Request
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-outline-purple w-100 mb-2" onclick="demoAjaxError()" style="border-color: #6f42c1; color: #6f42c1;">
                                    <i class="fas fa-exclamation-triangle"></i> Error Simulation
                                </button>
                            </div>
                        </div>
                        <div id="ajaxResult" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Loaders Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-link"></i> Navigation Loaders</h5>
                    </div>
                    <div class="card-body">
                        <p>Page transition loaders for navigation links.</p>
                        <div class="row">
                            <div class="col-md-3">
                                <a href="customers.php" class="btn btn-outline-secondary w-100 loader-nav">
                                    <i class="fas fa-users"></i> Customers
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="invoices.php" class="btn btn-outline-secondary w-100 loader-nav">
                                    <i class="fas fa-file-invoice"></i> Invoices
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="reports.php" class="btn btn-outline-secondary w-100 loader-nav">
                                    <i class="fas fa-chart-bar"></i> Reports
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="index.php" class="btn btn-outline-secondary w-100 loader-nav">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance and Features Section -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-tachometer-alt text-primary"></i> Performance Features</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Minimum Display Time
                                <span class="badge bg-primary rounded-pill">500ms</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Auto Cleanup
                                <span class="badge bg-success rounded-pill">Yes</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Memory Leaks
                                <span class="badge bg-success rounded-pill">None</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Browser Support
                                <span class="badge bg-info rounded-pill">All Modern</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-universal-access text-success"></i> Accessibility Features</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Screen Reader Support
                                <span class="badge bg-success rounded-pill">Full</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Reduced Motion
                                <span class="badge bg-success rounded-pill">Respected</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                High Contrast
                                <span class="badge bg-success rounded-pill">Supported</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Keyboard Navigation
                                <span class="badge bg-success rounded-pill">Friendly</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'inc/footer.php'; ?>

<script>
// Demo functions for loader system
function demoPageLoader() {
    showLoader('Loading Demo Page', 'Demonstrating page loader functionality...');
    setTimeout(() => hideLoader(), 3000);
}

function demoCustomPageLoader() {
    showLoader('Custom Loading Process', 'This is a custom message with different content...');
    setTimeout(() => hideLoader(), 4000);
}

function demoActionLoader() {
    showActionLoader('Processing demo action...', 'This may take a few moments');
    setTimeout(() => hideActionLoader(), 2500);
}

function demoCustomActionLoader() {
    showActionLoader('Analyzing data...', 'Step 1 of 3: Validation');
    setTimeout(() => {
        showActionLoader('Processing results...', 'Step 2 of 3: Computation');
        setTimeout(() => {
            showActionLoader('Finalizing...', 'Step 3 of 3: Cleanup');
            setTimeout(() => hideActionLoader(), 1000);
        }, 1500);
    }, 1500);
}

function demoButtonLoader(button) {
    setButtonLoading(button, true);
    setTimeout(() => {
        setButtonLoading(button, false);
        if (typeof showToast !== 'undefined') {
            showToast('Button action completed!', 'success');
        }
    }, 3000);
}

function demoFormLoader() {
    const form = document.getElementById('demoForm');
    setFormLoading(form, true);
    showActionLoader('Submitting form...', 'Validating and processing data');
    
    setTimeout(() => {
        setFormLoading(form, false);
        hideActionLoader();
        if (typeof showToast !== 'undefined') {
            showToast('Form submitted successfully!', 'success');
        }
    }, 3000);
}

function demoTableLoader() {
    const table = document.getElementById('demoTable');
    const overlay = window.CRMLoader.showTableLoader(table);
    
    setTimeout(() => {
        window.CRMLoader.hideTableLoader(overlay);
        if (typeof showToast !== 'undefined') {
            showToast('Table data refreshed!', 'info');
        }
    }, 2500);
}

function demoAjaxLoader(method) {
    if (typeof ajaxWithLoader === 'undefined') {
        showActionLoader('Simulating ' + method + ' request...', 'Demo mode - no actual request');
        setTimeout(() => {
            hideActionLoader();
            document.getElementById('ajaxResult').innerHTML = 
                '<div class="alert alert-success">✅ Simulated ' + method + ' request completed successfully!</div>';
        }, 2000);
        return;
    }
    
    ajaxWithLoader({
        url: 'demo-endpoint.php',
        method: method,
        data: { demo: true },
        success: function(data) {
            document.getElementById('ajaxResult').innerHTML = 
                '<div class="alert alert-success">✅ ' + method + ' request completed successfully!</div>';
        },
        error: function() {
            document.getElementById('ajaxResult').innerHTML = 
                '<div class="alert alert-danger">❌ ' + method + ' request failed!</div>';
        }
    });
}

function demoAjaxError() {
    showActionLoader('Simulating error...', 'This will demonstrate error handling');
    setTimeout(() => {
        hideActionLoader();
        document.getElementById('ajaxResult').innerHTML = 
            '<div class="alert alert-danger">❌ Simulated error occurred! Error handling works correctly.</div>';
    }, 2000);
}

// Initialize demo page
document.addEventListener('DOMContentLoaded', function() {
    showLoader('Loading Loader Demo', 'Preparing demonstration environment...');
    setTimeout(() => hideLoader(), 2000);
});
</script>

<style>
.btn-outline-purple:hover {
    background-color: #6f42c1 !important;
    border-color: #6f42c1 !important;
    color: white !important;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    font-size: 0.8em;
}

.list-group-item {
    border-color: rgba(0,0,0,0.1);
}
</style>
