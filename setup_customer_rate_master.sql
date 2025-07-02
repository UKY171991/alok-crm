-- Updated SQL Script for Customer Rate Master
-- This script creates tables compatible with the current PHP implementation

-- Create cr_modes table if not exists
CREATE TABLE IF NOT EXISTS `cr_modes` (
  `mode_id` int(11) NOT NULL AUTO_INCREMENT,
  `mode_name` varchar(50) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`mode_id`),
  UNIQUE KEY `mode_name` (`mode_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default modes
INSERT IGNORE INTO `cr_modes` (`mode_name`) VALUES
('Air'),
('Surface'),
('Express'),
('Economy'),
('Standard');

-- Create cr_consignment_types table if not exists
CREATE TABLE IF NOT EXISTS `cr_consignment_types` (
  `consignment_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`consignment_type_id`),
  UNIQUE KEY `type_name` (`type_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default consignment types
INSERT IGNORE INTO `cr_consignment_types` (`type_name`) VALUES
('Document'),
('Non-Document'),
('Both'),
('Parcel'),
('Cargo');

-- Check if customers table exists with correct structure
CREATE TABLE IF NOT EXISTS `customers` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample customers if table is empty
INSERT IGNORE INTO `customers` (`customer_name`, `customer_email`, `customer_phone`, `customer_address`) VALUES
('A. N. KAPOOR (JANITORS) PVT. LTD.', 'kapoor@email.com', '9876543210', 'Mumbai, Maharashtra'),
('ABC LOGISTICS PVT. LTD.', 'abc@email.com', '9876543211', 'Delhi, India'),
('XYZ TRADERS', 'xyz@email.com', '9876543212', 'Bangalore, Karnataka'),
('QUICK TRANSPORT CO.', 'quick@email.com', '9876543213', 'Chennai, Tamil Nadu'),
('SPEEDY COURIERS', 'speedy@email.com', '9876543214', 'Pune, Maharashtra');

-- Create customer_rates table with proper structure
CREATE TABLE IF NOT EXISTS `customer_rates` (
  `rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `mode_id` int(11) NOT NULL,
  `consignment_type_id` int(11) NOT NULL,
  `weight_from` decimal(10,3) NOT NULL DEFAULT 0.000,
  `weight_to` decimal(10,3) NOT NULL DEFAULT 0.000,
  `rate_per_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
  `minimum_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`rate_id`),
  KEY `idx_customer_zone_mode_type` (`customer_id`, `zone_id`, `mode_id`, `consignment_type_id`),
  KEY `idx_weight_range` (`weight_from`, `weight_to`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`zone_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`mode_id`) REFERENCES `cr_modes` (`mode_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`consignment_type_id`) REFERENCES `cr_consignment_types` (`consignment_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample rates (only if destinations table exists and has data)
INSERT IGNORE INTO `customer_rates` 
(`customer_id`, `zone_id`, `mode_id`, `consignment_type_id`, `weight_from`, `weight_to`, `rate_per_kg`, `minimum_rate`)
SELECT 
    1 as customer_id,
    d.id as zone_id,
    m.mode_id,
    ct.consignment_type_id,
    0.000 as weight_from,
    1.000 as weight_to,
    25.00 as rate_per_kg,
    50.00 as minimum_rate
FROM destinations d
CROSS JOIN cr_modes m
CROSS JOIN cr_consignment_types ct
WHERE d.status = 'active' 
  AND m.status = 'active' 
  AND ct.status = 'active'
  AND d.id <= 3  -- Limit to first 3 zones
  AND m.mode_id <= 2  -- Limit to first 2 modes
  AND ct.consignment_type_id <= 2  -- Limit to first 2 types
LIMIT 12;

-- Add additional weight ranges for first customer
INSERT IGNORE INTO `customer_rates` 
(`customer_id`, `zone_id`, `mode_id`, `consignment_type_id`, `weight_from`, `weight_to`, `rate_per_kg`, `minimum_rate`)
SELECT 
    1 as customer_id,
    d.id as zone_id,
    m.mode_id,
    ct.consignment_type_id,
    1.001 as weight_from,
    5.000 as weight_to,
    22.00 as rate_per_kg,
    50.00 as minimum_rate
FROM destinations d
CROSS JOIN cr_modes m
CROSS JOIN cr_consignment_types ct
WHERE d.status = 'active' 
  AND m.status = 'active' 
  AND ct.status = 'active'
  AND d.id <= 2  -- Limit to first 2 zones
  AND m.mode_id <= 1  -- Limit to first mode
  AND ct.consignment_type_id <= 1  -- Limit to first type
LIMIT 2;
