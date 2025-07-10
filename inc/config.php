<?php
// Application settings
define('APP_NAME', 'Courier CRM');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/alok-crm');

// Enhanced UI Configuration
define('PAGINATION_LIMIT', 10); // Records per page
define('MAX_SEARCH_RESULTS', 100); // Maximum search results
define('AJAX_TIMEOUT', 10000); // AJAX timeout in milliseconds
define('TOAST_DURATION', 4000); // Toast notification duration in milliseconds

// UI Theme settings
define('PRIMARY_COLOR', '#3b82f6');
define('SUCCESS_COLOR', '#10b981');
define('ERROR_COLOR', '#ef4444');
define('WARNING_COLOR', '#f59e0b');

// Table display settings
define('TABLE_HOVER_ANIMATION', true);
define('SHOW_LOADING_SPINNER', true);
define('AUTO_REFRESH_INTERVAL', 30000); // Auto refresh interval in milliseconds (0 = disabled)

// Modal settings
define('MODAL_ANIMATION_SPEED', 300); // Modal animation speed in milliseconds
define('MODAL_BACKDROP_CLOSE', true); // Allow closing modal by clicking backdrop

// Form validation
define('INVOICE_NUMBER_PREFIX', 'INV-');
define('MIN_INVOICE_AMOUNT', 0.01);
define('MAX_INVOICE_AMOUNT', 999999.99);
define('GST_RATE_DEFAULT', 18); // Default GST rate in percentage

// File upload settings (for future enhancements)
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_FILE_TYPES', ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx']);

// Session settings (only in web context)
if (isset($_SERVER['HTTP_HOST'])) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 1);
}

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Create logs directory if it doesn't exist
if (!file_exists(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}

// Database initialization function
function initialize_database() {
    require_once __DIR__ . '/db.php';
    
    try {
        // Create PDO connection for table creation
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create customers table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `customers` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `email` varchar(100) DEFAULT NULL,
            `phone` varchar(20) DEFAULT NULL,
            `address` text DEFAULT NULL,
            `gst_no` varchar(20) DEFAULT NULL,
            `hsn_code` varchar(20) DEFAULT NULL,
            `pan_no` varchar(20) DEFAULT NULL,
            `cin_no` varchar(20) DEFAULT NULL,
            `aadhaar_no` varchar(20) DEFAULT NULL,
            `destination` text DEFAULT NULL,
            `parcel_type` text DEFAULT NULL,
            `weight` text DEFAULT NULL,
            `price` text DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            `service` text DEFAULT NULL,
            `shipment_type` text DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `name` (`name`),
            KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        
        // Create destinations table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `destinations` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `code` varchar(20) DEFAULT NULL,
            `zone_name` varchar(100) DEFAULT 'General',
            `type` varchar(50) DEFAULT 'Standard',
            `status` enum('active','inactive') NOT NULL DEFAULT 'active',
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `name` (`name`),
            KEY `zone_name` (`zone_name`),
            KEY `status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        
        // Insert sample customers if table is empty
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM customers");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO `customers` (`name`, `email`, `phone`, `address`) VALUES
                ('ABC Logistics', 'contact@abclogistics.com', '9876543210', 'Sector 10, Noida, UP'),
                ('XYZ Enterprises', 'info@xyzenterprises.com', '9876543211', 'MG Road, Bangalore, KA'),
                ('Quick Courier Ltd', 'admin@quickcourier.com', '9876543212', 'Commercial Street, Pune, MH'),
                ('Express Delivery Co', 'support@expressdelivery.com', '9876543213', 'Connaught Place, New Delhi'),
                ('Fast Track Logistics', 'hello@fasttrack.com', '9876543214', 'Park Street, Kolkata, WB')");
        }
        
        // Insert sample destinations if table is empty
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM destinations");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO `destinations` (`name`, `code`, `zone_name`, `type`) VALUES
                ('Local (Lucknow)', 'LKO', 'North Zone', 'Local'),
                ('Delhi NCR', 'DEL', 'North Zone', 'Metro'),
                ('Mumbai', 'BOM', 'West Zone', 'Metro'),
                ('Bangalore', 'BLR', 'South Zone', 'Metro'),
                ('Chennai', 'MAA', 'South Zone', 'Metro'),
                ('Kolkata', 'CCU', 'East Zone', 'Metro'),
                ('Pune', 'PUN', 'West Zone', 'City'),
                ('Hyderabad', 'HYD', 'South Zone', 'City'),
                ('Ahmedabad', 'AMD', 'West Zone', 'City'),
                ('Jaipur', 'JAI', 'North Zone', 'City'),
                ('Surat', 'SRT', 'West Zone', 'City'),
                ('Kanpur', 'KNP', 'North Zone', 'City'),
                ('Agra', 'AGR', 'North Zone', 'City'),
                ('Varanasi', 'VNS', 'North Zone', 'City'),
                ('Goa', 'GOI', 'West Zone', 'Tourist')");
        }
        
        // Create users table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(50) NOT NULL,
            `password` varchar(255) NOT NULL,
            `role` enum('admin','user') NOT NULL DEFAULT 'user',
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `username` (`username`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        
        // Insert default admin user if table is empty
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            // Default password is 'admin123' - should be changed after first login
            $default_password = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->exec("INSERT INTO `users` (`username`, `password`, `role`) VALUES
                ('admin', '$default_password', 'admin')");
        }
        
        // Create invoices table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `invoices` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `invoice_no` varchar(50) NOT NULL,
            `customer_id` int(11) NOT NULL,
            `destination` varchar(255) DEFAULT NULL,
            `invoice_date` date NOT NULL,
            `from_date` date DEFAULT NULL,
            `to_date` date DEFAULT NULL,
            `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
            `gst_amount` decimal(10,2) DEFAULT 0.00,
            `grand_total` decimal(10,2) NOT NULL DEFAULT 0.00,
            `status` enum('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `invoice_no` (`invoice_no`),
            KEY `customer_id` (`customer_id`),
            KEY `invoice_date` (`invoice_date`),
            KEY `status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        
        // Create invoice_items table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `invoice_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `invoice_id` int(11) NOT NULL,
            `booking_date` date DEFAULT NULL,
            `consignment_no` varchar(50) DEFAULT NULL,
            `destination` varchar(100) DEFAULT NULL,
            `service` varchar(50) DEFAULT NULL,
            `mode` varchar(50) DEFAULT NULL,
            `no_of_pcs` int(11) DEFAULT NULL,
            `weight` decimal(10,3) DEFAULT NULL,
            `rate` decimal(10,2) DEFAULT NULL,
            `amount` decimal(10,2) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `invoice_id` (`invoice_id`),
            KEY `consignment_no` (`consignment_no`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        
        // Create orders table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `orders` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `customer_id` int(11) DEFAULT NULL,
            `date` date DEFAULT NULL,
            `docket` varchar(100) DEFAULT NULL,
            `location` varchar(100) DEFAULT NULL,
            `destination` varchar(100) DEFAULT NULL,
            `mode` varchar(50) DEFAULT NULL,
            `no_of_pcs` int(11) DEFAULT NULL,
            `pincode` varchar(20) DEFAULT NULL,
            `content` varchar(255) DEFAULT NULL,
            `dox_non_dox` varchar(50) DEFAULT NULL,
            `material_value` decimal(15,2) DEFAULT NULL,
            `fr_weight` decimal(10,2) DEFAULT NULL,
            `valumatric` decimal(10,2) DEFAULT NULL,
            `manual_weight` decimal(10,2) DEFAULT NULL,
            `invoice_wt` decimal(10,2) DEFAULT NULL,
            `round_off_weight` decimal(10,2) DEFAULT NULL,
            `clinet_billing_value` decimal(15,2) DEFAULT NULL,
            `credit_cust_amt` decimal(15,2) DEFAULT NULL,
            `regular_cust_amt` decimal(15,2) DEFAULT NULL,
            `customer_type` varchar(50) DEFAULT NULL,
            `sender_detail` varchar(255) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `customer_id` (`customer_id`),
            KEY `date` (`date`),
            KEY `docket` (`docket`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        
        // Create rates table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `rates` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `destination` varchar(100) NOT NULL,
            `mode` varchar(50) NOT NULL,
            `weight_from` decimal(10,3) NOT NULL DEFAULT 0.000,
            `weight_to` decimal(10,3) NOT NULL DEFAULT 0.000,
            `rate` decimal(10,2) NOT NULL DEFAULT 0.00,
            `additional_rate` decimal(10,2) DEFAULT 0.00,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `destination` (`destination`),
            KEY `mode` (`mode`),
            KEY `weight_range` (`weight_from`, `weight_to`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        
        // Create cr_modes table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `cr_modes` (
            `mode_id` int(11) NOT NULL AUTO_INCREMENT,
            `mode_name` varchar(50) NOT NULL,
            `status` enum('active','inactive') NOT NULL DEFAULT 'active',
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`mode_id`),
            UNIQUE KEY `mode_name` (`mode_name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        
        // Insert default modes if table is empty
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cr_modes");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO `cr_modes` (`mode_name`) VALUES
                ('Air'), ('Surface'), ('Express'), ('Economy'), ('Standard')");
        }
        
        // Create cr_consignment_types table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `cr_consignment_types` (
            `consignment_type_id` int(11) NOT NULL AUTO_INCREMENT,
            `type_name` varchar(50) NOT NULL,
            `status` enum('active','inactive') NOT NULL DEFAULT 'active',
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`consignment_type_id`),
            UNIQUE KEY `type_name` (`type_name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        
        // Insert default consignment types if table is empty
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cr_consignment_types");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO `cr_consignment_types` (`type_name`) VALUES
                ('Document'), ('Non-Document'), ('Both'), ('Parcel'), ('Cargo')");
        }
        
        // Create customer_rates table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `customer_rates` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `customer_id` int(11) NOT NULL,
            `mode` varchar(50) NOT NULL DEFAULT 'Standard',
            `consignment_type` varchar(50) NOT NULL DEFAULT 'General',
            `zone_wise` varchar(50) NOT NULL DEFAULT 'General',
            `state_wise` varchar(50) NOT NULL DEFAULT 'General',
            `city_wise` varchar(50) NOT NULL DEFAULT 'General',
            `from_weight` decimal(10,3) NOT NULL DEFAULT 0.000,
            `to_weight` decimal(10,3) NOT NULL DEFAULT 0.000,
            `rate` decimal(10,2) NOT NULL DEFAULT 0.00,
            `additional_weight_kg` decimal(10,3) NOT NULL DEFAULT 0.000,
            `additional_weight` decimal(10,3) NOT NULL DEFAULT 0.000,
            `additional_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `customer_id` (`customer_id`),
            KEY `mode` (`mode`),
            KEY `zone_wise` (`zone_wise`),
            KEY `weight_range` (`from_weight`, `to_weight`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        
        // Add foreign key constraint for customer_rates if it doesn't exist
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                              WHERE TABLE_SCHEMA = DATABASE() 
                              AND TABLE_NAME = 'customer_rates' 
                              AND CONSTRAINT_NAME = 'fk_customer_rates_customer'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            try {
                $pdo->exec("ALTER TABLE `customer_rates` 
                           ADD CONSTRAINT `fk_customer_rates_customer` 
                           FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) 
                           ON DELETE CASCADE ON UPDATE CASCADE");
            } catch (PDOException $e) {
                // Ignore if customers table doesn't exist yet or constraint already exists
                error_log("Foreign key constraint creation skipped: " . $e->getMessage());
            }
        }
        
        // Add foreign key constraints for invoices table
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                              WHERE TABLE_SCHEMA = DATABASE() 
                              AND TABLE_NAME = 'invoices' 
                              AND CONSTRAINT_NAME = 'fk_invoices_customer'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            try {
                $pdo->exec("ALTER TABLE `invoices` 
                           ADD CONSTRAINT `fk_invoices_customer` 
                           FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) 
                           ON DELETE CASCADE ON UPDATE CASCADE");
            } catch (PDOException $e) {
                error_log("Invoice foreign key constraint creation skipped: " . $e->getMessage());
            }
        }
        
        // Add foreign key constraints for invoice_items table
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                              WHERE TABLE_SCHEMA = DATABASE() 
                              AND TABLE_NAME = 'invoice_items' 
                              AND CONSTRAINT_NAME = 'fk_invoice_items_invoice'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            try {
                $pdo->exec("ALTER TABLE `invoice_items` 
                           ADD CONSTRAINT `fk_invoice_items_invoice` 
                           FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) 
                           ON DELETE CASCADE ON UPDATE CASCADE");
            } catch (PDOException $e) {
                error_log("Invoice items foreign key constraint creation skipped: " . $e->getMessage());
            }
        }
        
        // Add foreign key constraints for orders table
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
                              WHERE TABLE_SCHEMA = DATABASE() 
                              AND TABLE_NAME = 'orders' 
                              AND CONSTRAINT_NAME = 'fk_orders_customer'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            try {
                $pdo->exec("ALTER TABLE `orders` 
                           ADD CONSTRAINT `fk_orders_customer` 
                           FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) 
                           ON DELETE SET NULL ON UPDATE CASCADE");
            } catch (PDOException $e) {
                error_log("Orders foreign key constraint creation skipped: " . $e->getMessage());
            }
        }
        
        // Check if destinations table has required columns, if not add them
        $stmt = $pdo->prepare("SHOW COLUMNS FROM destinations LIKE 'status'");
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            try {
                $pdo->exec("ALTER TABLE destinations ADD COLUMN status enum('active','inactive') NOT NULL DEFAULT 'active'");
            } catch (PDOException $e) {
                error_log("Destinations status column creation skipped: " . $e->getMessage());
            }
        }
        
        $stmt = $pdo->prepare("SHOW COLUMNS FROM destinations LIKE 'zone_name'");
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            try {
                $pdo->exec("ALTER TABLE destinations ADD COLUMN zone_name varchar(100) DEFAULT 'General'");
            } catch (PDOException $e) {
                error_log("Destinations zone_name column creation skipped: " . $e->getMessage());
            }
        }
        
        $stmt = $pdo->prepare("SHOW COLUMNS FROM destinations LIKE 'type'");
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            try {
                $pdo->exec("ALTER TABLE destinations ADD COLUMN type varchar(50) DEFAULT 'Standard'");
            } catch (PDOException $e) {
                error_log("Destinations type column creation skipped: " . $e->getMessage());
            }
        }
        
        // Insert sample customer rates if table is empty and customers/destinations exist
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM customer_rates");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            try {
                // Check if we have customers and destinations
                $customers_check = $pdo->prepare("SELECT COUNT(*) FROM customers");
                $customers_check->execute();
                $destinations_check = $pdo->prepare("SELECT COUNT(*) FROM destinations");
                $destinations_check->execute();
                
                if ($customers_check->fetchColumn() > 0 && $destinations_check->fetchColumn() > 0) {
                    $pdo->exec("INSERT IGNORE INTO `customer_rates` 
                        (`customer_id`, `zone_wise`, `mode`, `consignment_type`, `from_weight`, `to_weight`, `rate`, `additional_rate`)
                        SELECT 
                            c.id as customer_id,
                            d.zone_name as zone_wise,
                            'Air' as mode,
                            'Document' as consignment_type,
                            0.000 as from_weight,
                            1.000 as to_weight,
                            25.00 as rate,
                            50.00 as additional_rate
                        FROM customers c
                        CROSS JOIN destinations d
                        WHERE c.id <= 2 AND d.id <= 3
                        LIMIT 6");
                }
            } catch (PDOException $e) {
                error_log("Sample data insertion skipped: " . $e->getMessage());
            }
        }
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Database initialization error: " . $e->getMessage());
        return false;
    }
}

// Auto-initialize database on config load (only in web context)
if (isset($_SERVER['HTTP_HOST'])) {
    initialize_database();
}

// Helper functions
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generate_invoice_number() {
    return 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());
}

function format_date($date) {
    return date('d M Y', strtotime($date));
}

function format_currency($amount) {
    return '₹' . number_format($amount, 2);
}

// CSRF Protection
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Function to manually create tables (for CLI usage)
function create_customer_rate_tables() {
    return initialize_database();
}

// Booking/Consignment System Configuration
define('CONSIGNMENT_PREFIX', 'U'); // Consignment number prefix
define('BOOKING_PAGINATION_LIMIT', 15); // Records per page for booking table
define('DEFAULT_SERVICE_TYPE', 'AIR'); // Default service type
define('DEFAULT_DOC_TYPE', 'DOX'); // Default document type
define('ENABLE_BULK_OPERATIONS', true); // Enable bulk select/delete
define('SHOW_SUMMARY_STATS', true); // Show summary statistics

// Service Types
define('SERVICE_TYPES', ['AIR', 'SURFACE', 'EXPRESS']);

// Document Types  
define('DOCUMENT_TYPES', ['DOX', 'SPX', 'NDX']);

// Billing Status Options
define('BILLING_STATUS_OPTIONS', ['All', 'Billed', 'Non-Billed', 'Pending']);

// Weight and Amount Settings
define('MIN_WEIGHT', 0.001);
define('MAX_WEIGHT', 999.999);
define('WEIGHT_DECIMAL_PLACES', 3);
define('AMOUNT_DECIMAL_PLACES', 2);
?>