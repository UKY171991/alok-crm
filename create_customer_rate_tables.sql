-- SQL Script to create Customer Rate Master tables

-- Create customer_rates table
CREATE TABLE IF NOT EXISTS `customer_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `mode` varchar(50) NOT NULL,
  `consignment_type` varchar(50) NOT NULL,
  `zone_wise` varchar(50) NOT NULL,
  `state_wise` varchar(50) NOT NULL,
  `city_wise` varchar(50) NOT NULL,
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
  KEY `zone_wise` (`zone_wise`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Note: customers table should already exist in the system
-- The existing customers table has this structure:
/*
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `gst_no` varchar(15) DEFAULT NULL,
  `hsn_code` varchar(10) DEFAULT NULL,
  `pan_no` varchar(10) DEFAULT NULL,
  `cin_no` varchar(21) DEFAULT NULL,
  `aadhaar_no` varchar(12) DEFAULT NULL,
  `destination` json DEFAULT NULL,
  `parcel_type` json DEFAULT NULL,
  `weight` json DEFAULT NULL,
  `price` json DEFAULT NULL,
  `service` json DEFAULT NULL,
  `shipment_type` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
*/

-- Insert sample customers if table is empty (using actual structure)
INSERT IGNORE INTO `customers` (`name`, `address`, `phone`, `email`, `gst_no`) VALUES
('A. N. KAPOOR (JANITORS) PVT. LTD.', 'Mumbai, Maharashtra', '9876543210', 'kapoor@email.com', '27ABCDE1234F1Z5'),
('ABC LOGISTICS PVT. LTD.', 'Delhi, India', '9876543211', 'abc@email.com', '07ABCDE1234F1Z5'),
('XYZ TRADERS', 'Bangalore, Karnataka', '9876543212', 'xyz@email.com', '29ABCDE1234F1Z5');

-- Create modes table for dropdown options
CREATE TABLE IF NOT EXISTS `shipping_modes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mode_name` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default shipping modes
INSERT IGNORE INTO `shipping_modes` (`mode_name`) VALUES
('Air'), ('Surface'), ('Express'), ('Economy');

-- Create consignment types table
CREATE TABLE IF NOT EXISTS `consignment_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default consignment types
INSERT IGNORE INTO `consignment_types` (`type_name`) VALUES
('Document'), ('Non-Document'), ('Both');

-- Add foreign key constraints
ALTER TABLE `customer_rates` 
  ADD CONSTRAINT `fk_customer_rates_customer` 
  FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) 
  ON DELETE CASCADE ON UPDATE CASCADE;
