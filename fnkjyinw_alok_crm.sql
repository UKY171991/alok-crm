-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 07, 2025 at 10:06 AM
-- Server version: 10.11.11-MariaDB-cll-lve
-- PHP Version: 8.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fnkjyinw_alok_crm`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `gst_no`, `hsn_code`, `pan_no`, `cin_no`, `aadhaar_no`, `destination`, `parcel_type`, `weight`, `price`, `created_at`, `updated_at`) VALUES
(8, 'Lee Hardin', 'venenugi@mailinator.com', '+1 (534) 691-3996', 'Laudantium vel labo', 'Fugiat ut qui rerum', 'Magnam at quo magni ', 'Aspernatur sed ab vo', 'Aut Nam minus volupt', 'Aperiam omnis ut ill', '[\"Bangalore\",\"Chennai\"]', '[\"Document & Light Parcel\",\"Premium\"]', '[\"1kg\",\"1kg\"]', '[\"54\",\"22\"]', '2025-04-16 08:36:28', '2025-04-16 08:45:48');

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 'Mumbai', 'MUM', '2025-04-12 03:04:48', '2025-04-12 03:04:48'),
(2, 'Delhi', 'DEL', '2025-04-12 03:04:48', '2025-04-12 03:04:48'),
(3, 'Bangalore', 'BLR', '2025-04-12 03:04:48', '2025-04-12 03:04:48'),
(4, 'Chennai', 'CHE', '2025-04-12 03:04:48', '2025-04-12 03:04:48'),
(5, 'Kolkata', 'KOL', '2025-04-12 03:04:48', '2025-04-12 03:04:48'),
(6, 'Nyssa Molina', NULL, '2025-04-16 08:51:37', '2025-04-16 08:51:37'),
(9, 'sdf', NULL, '2025-04-16 09:25:31', '2025-04-16 09:25:31'),
(10, 'Risa Haney', NULL, '2025-04-16 09:54:51', '2025-04-16 09:54:51');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_amount` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_no`, `customer_id`, `destination`, `invoice_date`, `total_amount`, `gst_amount`, `grand_total`, `status`, `created_at`, `updated_at`) VALUES
(6, 'Nostrud iste dolore ', 8, 'Kolkata', '2021-12-20', 47.00, 78.00, 3.00, 'pending', '2025-04-16 09:42:08', '2025-04-16 09:42:08'),
(7, 'Blanditiis eligendi ', 8, 'Nyssa Molina', '1981-09-18', 76.00, 37.00, 1.00, 'pending', '2025-04-16 09:42:21', '2025-04-16 09:42:21'),
(8, 'Dolores quia laborum', 8, 'Chennai', '1991-08-22', 92.00, 93.00, 54.00, 'pending', '2025-04-16 09:43:57', '2025-04-16 09:43:57'),
(9, 'INV-000009', 8, 'Bangalore', '2025-04-16', 44.00, 4.00, 60.00, 'pending', '2025-04-16 09:49:00', '2025-04-16 09:49:00');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `rate` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rates`
--

CREATE TABLE `rates` (
  `id` int(11) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `type` enum('Normal','Premium','Bulk_Surface','Bulk_Air') NOT NULL,
  `weight_category` varchar(50) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rates`
--

INSERT INTO `rates` (`id`, `destination`, `type`, `weight_category`, `rate`, `created_at`, `updated_at`) VALUES
(1, 'Mumbai', 'Normal', '0-500g', 50.00, '2025-04-12 03:04:48', '2025-04-12 03:04:48'),
(2, 'Mumbai', 'Premium', '0-500g', 75.00, '2025-04-12 03:04:48', '2025-04-12 03:04:48'),
(3, 'Delhi', 'Normal', '0-500g', 60.00, '2025-04-12 03:04:48', '2025-04-12 03:04:48'),
(4, 'Delhi', 'Premium', '0-500g', 85.00, '2025-04-12 03:04:48', '2025-04-12 03:04:48'),
(5, 'Bangalore', 'Normal', '0-500g', 55.00, '2025-04-12 03:04:48', '2025-04-12 03:04:48'),
(6, 'Bangalore', 'Premium', '0-500g', 80.00, '2025-04-12 03:04:48', '2025-04-12 03:04:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-04-12 03:01:59', '2025-04-12 03:01:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `rates`
--
ALTER TABLE `rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rates`
--
ALTER TABLE `rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
