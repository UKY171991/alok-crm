-- Create database (comment out these lines when using on live server)
-- CREATE DATABASE IF NOT EXISTS alok_crm;
-- USE alok_crm;

-- For live server, uncomment these lines and comment out the above ones
-- CREATE DATABASE IF NOT EXISTS fnkjyinw_alok_crm;
-- USE fnkjyinw_alok_crm;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    gst_no VARCHAR(20),
    hsn_code VARCHAR(20),
    pan_no VARCHAR(20),
    cin_no VARCHAR(20),
    aadhaar_no VARCHAR(20),
    destination TEXT NULL,
    parcel_type TEXT NULL,
    weight TEXT NULL,
    price TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample customers
INSERT INTO customers (name, email, phone, address, gst_no, hsn_code, pan_no, cin_no, aadhaar_no) VALUES
('ABC Traders', 'abc@example.com', '9876543210', '123 Main St, Mumbai', 'GST123456789', 'HSN001', 'ABCPN1234M', 'CIN123456', '123456789012'),
('XYZ Enterprises', 'xyz@example.com', '8765432109', '456 Park Road, Delhi', 'GST987654321', 'HSN002', 'XYZPN5678N', 'CIN654321', '987654321098'),
('Sharma Courier', 'sharma@example.com', '7654321098', '789 Lake View, Bangalore', 'GST456789123', 'HSN003', 'SHRPN9012P', 'CIN789123', '456789123045');

-- Destinations table
CREATE TABLE IF NOT EXISTS destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample destinations
INSERT INTO destinations (name, code) VALUES
('Mumbai', 'MUM'),
('Delhi', 'DEL'),
('Bangalore', 'BLR'),
('Chennai', 'CHE'),
('Kolkata', 'KOL');

-- Rates table
CREATE TABLE IF NOT EXISTS rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destination VARCHAR(100) NOT NULL,
    type ENUM('Normal', 'Premium', 'Bulk_Surface', 'Bulk_Air') NOT NULL,
    weight_category VARCHAR(50) NOT NULL,
    rate DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample rates
INSERT INTO rates (destination, type, weight_category, rate) VALUES
('Mumbai', 'Normal', '0-500g', 50.00),
('Mumbai', 'Premium', '0-500g', 75.00),
('Delhi', 'Normal', '0-500g', 60.00),
('Delhi', 'Premium', '0-500g', 85.00),
('Bangalore', 'Normal', '0-500g', 55.00),
('Bangalore', 'Premium', '0-500g', 80.00);

-- Invoices table
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_no VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    invoice_date DATE NOT NULL,
    destination VARCHAR(100),
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    gst_amount DECIMAL(10,2) DEFAULT 0.00,
    grand_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('pending', 'paid', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

-- Insert sample invoices
INSERT INTO invoices (invoice_no, customer_id, invoice_date, total_amount, gst_amount, grand_total, status) VALUES
('INV001', 1, '2025-04-01', 80.00, 14.40, 94.40, 'paid'),
('INV002', 2, '2025-04-05', 200.00, 36.00, 236.00, 'pending'),
('INV003', 3, '2025-04-07', 135.00, 24.30, 159.30, 'pending');

-- Invoice items table
CREATE TABLE IF NOT EXISTS invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    description TEXT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    rate DECIMAL(10,2) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);

-- Insert sample invoice items
INSERT INTO invoice_items (invoice_id, description, quantity, rate, amount) VALUES
(1, 'Courier delivery to Mumbai - Normal 0-500g', 1, 80.00, 80.00),
(2, 'Courier delivery to Delhi - Premium 0-500g', 2, 100.00, 200.00),
(3, 'Courier delivery to Bangalore - Normal 0-500g', 3, 45.00, 135.00);