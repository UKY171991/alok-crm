<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
        <!-- <img src="img/logo.png" alt="CRM Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
        <span class="brand-text font-weight-light">Courier CRM</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <!-- <div class="image">
                <img src="img/user.png" class="img-circle elevation-2" alt="User Image">
            </div> -->
            <div class="info">
                <a href="#" class="d-block">Admin</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="index.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'index.php') echo ' active'; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!-- Destinations -->
                <li class="nav-item">
                    <a href="destination.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'destination.php') echo ' active'; ?>">
                        <i class="nav-icon fas fa-map-marker-alt"></i>
                        <p>Destinations</p>
                    </a>
                </li>
                <!-- Customers -->
                <li class="nav-item">
                    <a href="customers.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'customers.php') echo ' active'; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Customers</p>
                    </a>
                </li>
                <!-- Direct Party Booking -->
                <li class="nav-item">
                    <a href="direct_party_booking.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'direct_party_booking.php') echo ' active'; ?>">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Direct Party Booking</p>
                    </a>
                </li>
                <!-- Order -->
                <li class="nav-item">
                    <a href="order.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'order.php') echo ' active'; ?>">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Order</p>
                    </a>
                </li>
                <!-- Customer Rate Master -->
                <li class="nav-item">
                    <a href="customer_rate_master.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'customer_rate_master.php') echo ' active'; ?>">
                        <i class="nav-icon fas fa-calculator"></i>
                        <p>Customer Rate Master</p>
                    </a>
                </li>
                <!-- Invoices -->
                <li class="nav-item">
                    <a href="invoices.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'invoices.php') echo ' active'; ?>">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>Invoices</p>
                    </a>
                </li>
                
                <!-- Generate Invoice -->
                <li class="nav-item">
                    <a href="generate_invoice.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'generate_invoice.php') echo ' active'; ?>">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Generate Invoice</p>
                    </a>
                </li>
                
                <!-- Reports -->
                <li class="nav-item">
                    <a href="reports.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'reports.php') echo ' active'; ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reports</p>
                    </a>
                </li>
                
                <!-- Loader Demo -->
                <li class="nav-item">
                    <a href="loader_demo.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'loader_demo.php') echo ' active'; ?>">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Loader Demo</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>