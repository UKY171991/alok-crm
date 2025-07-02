-- Updated Customer Rate Master SQL Script
-- This creates tables that work with the existing customers table structure

-- Create modes table for dropdown options (simplified version)
CREATE TABLE IF NOT EXISTS `cr_modes` (
  `mode_id` int(11) NOT NULL AUTO_INCREMENT,
  `mode_name` varchar(50) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`mode_id`),
  UNIQUE KEY `mode_name` (`mode_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default modes
INSERT IGNORE INTO `cr_modes` (`mode_name`) VALUES
('Air'), ('Surface'), ('Express'), ('Economy'), ('Standard');

-- Create consignment types table
CREATE TABLE IF NOT EXISTS `cr_consignment_types` (
  `consignment_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`consignment_type_id`),
  UNIQUE KEY `type_name` (`type_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default consignment types
INSERT IGNORE INTO `cr_consignment_types` (`type_name`) VALUES
('Document'), ('Non-Document'), ('Both'), ('Parcel'), ('Cargo');

-- Create customer_rates table that matches the existing UI structure
CREATE TABLE IF NOT EXISTS `customer_rates` (
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
  KEY `weight_range` (`from_weight`, `to_weight`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample rates if the destinations table exists and has data
INSERT IGNORE INTO `customer_rates` 
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
WHERE c.id <= 2  -- Limit to first 2 customers
  AND d.id <= 3  -- Limit to first 3 zones
LIMIT 6;

-- Add more weight ranges for variety
INSERT IGNORE INTO `customer_rates` 
(`customer_id`, `zone_wise`, `mode`, `consignment_type`, `from_weight`, `to_weight`, `rate`, `additional_rate`)
SELECT 
    c.id as customer_id,
    d.zone_name as zone_wise,
    'Surface' as mode,
    'Non-Document' as consignment_type,
    1.001 as from_weight,
    5.000 as to_weight,
    20.00 as rate,
    40.00 as additional_rate
FROM customers c
CROSS JOIN destinations d
WHERE c.id <= 1  -- First customer only
  AND d.id <= 2  -- First 2 zones
LIMIT 2;
