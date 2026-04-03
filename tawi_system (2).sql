-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2026 at 11:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tawi system`
--

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `id` int(11) NOT NULL,
  `region_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `location_coords` varchar(100) DEFAULT NULL,
  `tree_capacity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `region_name`, `description`, `location_coords`, `tree_capacity`, `created_at`, `status`) VALUES
(5, 'Mberee North', 'Black cotton soil', NULL, NULL, '2026-03-17 14:12:23', 'Active'),
(6, 'Kericho', 'loam soil', NULL, NULL, '2026-03-18 05:29:01', 'Active'),
(7, 'kirinyaga', 'red soil', NULL, NULL, '2026-03-18 05:29:20', 'Active'),
(8, 'nyeri', 'clay soil', NULL, NULL, '2026-03-18 05:29:55', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `seedling_id` int(11) DEFAULT NULL,
  `officer_id` int(11) NOT NULL,
  `issue_type` varchar(100) DEFAULT NULL,
  `tree_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','reviewed','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `seedling_id`, `officer_id`, `issue_type`, `tree_id`, `title`, `description`, `status`, `created_at`) VALUES
(5, 3, 15, 'Drought/Dehydration', NULL, '', 'The tree should be sprinkles with water', 'pending', '2026-03-17 14:15:59'),
(6, 8, 18, 'Drought/Dehydration', NULL, '', 'The tree needs to be sprinkled with water', 'pending', '2026-03-18 06:21:21'),
(7, 5, 18, 'Pest Infestation', NULL, '', 'it should be sprayed', 'pending', '2026-03-18 08:00:24');

-- --------------------------------------------------------

--
-- Table structure for table `seedlings`
--

CREATE TABLE `seedlings` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `tree_name` varchar(100) NOT NULL,
  `region_id` int(11) DEFAULT NULL,
  `officer_id` int(11) NOT NULL,
  `tree_type` varchar(100) DEFAULT NULL,
  `beneficiary_id` int(11) DEFAULT NULL,
  `species` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `date_planted` timestamp NOT NULL DEFAULT current_timestamp(),
  `price` decimal(10,2) DEFAULT 500.00,
  `payment_status` varchar(50) DEFAULT 'Pending',
  `quantity` int(11) DEFAULT 0,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seedlings`
--

INSERT INTO `seedlings` (`id`, `owner_id`, `tree_name`, `region_id`, `officer_id`, `tree_type`, `beneficiary_id`, `species`, `status`, `date_planted`, `price`, `payment_status`, `quantity`, `description`) VALUES
(1, 0, '', NULL, 0, NULL, NULL, 'Acacia', NULL, '2026-03-18 06:56:58', 150.00, 'Pending', 45, 'Fast-growing, drought-resistant tree.'),
(2, 0, '', NULL, 0, NULL, NULL, 'Mvule', NULL, '2026-03-18 06:56:58', 300.00, 'Pending', 20, 'High-value timber tree.'),
(3, 0, '', NULL, 0, NULL, NULL, 'Bamboo', NULL, '2026-03-18 06:56:58', 100.00, 'Pending', 100, 'Great for CO2 offset.'),
(4, 0, '', NULL, 0, NULL, NULL, 'Grevillea', NULL, '2026-03-18 06:56:58', 120.00, 'Pending', 60, 'Rapid growth.'),
(5, 0, '', 5, 18, 'mahogany', NULL, NULL, NULL, '2026-03-18 07:59:13', 500.00, 'Pending', 0, NULL),
(6, 0, '', 6, 18, 'graviela', NULL, NULL, NULL, '2026-03-21 11:17:09', 500.00, 'Pending', 0, NULL),
(7, 16, 'Bamboo', NULL, 0, NULL, NULL, NULL, '10', '2026-03-24 15:09:13', 500.00, 'Pending', 0, NULL),
(8, 16, 'Bamboo', NULL, 0, NULL, NULL, NULL, '10', '2026-03-24 15:14:29', 500.00, 'Pending', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `trees`
--

CREATE TABLE `trees` (
  `id` int(11) NOT NULL,
  `beneficiary_id` int(11) NOT NULL,
  `region` varchar(255) DEFAULT NULL,
  `species` varchar(100) NOT NULL,
  `location_lat` decimal(10,8) DEFAULT NULL,
  `location_lng` decimal(11,8) DEFAULT NULL,
  `status` enum('Healthy','Needs Attention','Critical') DEFAULT 'Healthy',
  `height_m` decimal(5,2) DEFAULT 0.00,
  `date_planted` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trees`
--

INSERT INTO `trees` (`id`, `beneficiary_id`, `region`, `species`, `location_lat`, `location_lng`, `status`, `height_m`, `date_planted`, `created_at`) VALUES
(1, 1, NULL, 'Acacia', NULL, NULL, 'Healthy', 0.10, '2026-03-17 21:00:00', '2026-03-18 04:59:59'),
(2, 1, NULL, 'Bamboo', NULL, NULL, 'Healthy', 0.10, '2026-03-17 21:00:00', '2026-03-18 05:14:40'),
(3, 1, NULL, 'Gravillea', NULL, NULL, 'Healthy', 0.10, '2026-03-17 21:00:00', '2026-03-18 05:14:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','officer','beneficiary') NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `wallet_balance` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `full_name`, `username`, `email`, `role`, `password`, `password_hash`, `created_at`, `reset_token`, `token_expiry`, `wallet_balance`) VALUES
(14, NULL, 'Livingstonebundi Karani', 'karani', 'livingstonebundikarani@gmail.com', 'admin', '$2y$10$BKJBl6d8rzVcUebIuXdQVuChch7Z1ovQGy7qJhgjedeqEXQvEg6Lm', '$2y$10$rOC030cS/xtGLckoy5W5NuP4w0OtMc/VJFVYjX7v64LFwZNYsVNUq', '2026-03-17 13:30:59', '4b95fc4652cbfbf1ecff62efe7b96eca42aa8ee64aaa2dad6b642f19f54863a12a4b89740a12e1ffff636009834b4a8d06e5', '2026-03-24 10:16:40', 0.00),
(16, NULL, 'peris muthoni', 'peris', 'perismuthoni@gmail.com', 'beneficiary', '', '$2y$10$T6ueFpAlA95F6q3Z5YDeie/G2Y9xcf1X63IUaMi5VYNOmeRBhtlQu', '2026-03-17 14:50:14', NULL, NULL, 1000.00),
(18, NULL, 'james karani', 'james', 'jameskarani@gmail.com', 'officer', '', '$2y$10$x5KcrRVNuaS2JD1/YYHaruwFkmx.t2jhZGGDyONye6JmWyopJi0UO', '2026-03-18 06:17:35', NULL, NULL, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `user_trees`
--

CREATE TABLE `user_trees` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `seedling_id` int(11) NOT NULL,
  `species` varchar(255) NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Planted',
  `planted_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_trees`
--

INSERT INTO `user_trees` (`id`, `user_id`, `seedling_id`, `species`, `purchase_price`, `status`, `planted_date`) VALUES
(1, 1, 1, 'Acacia', 150.00, 'Planted', '2026-03-18 06:40:28'),
(2, 1, 2, 'Mvule', 300.00, 'Planted', '2026-03-18 06:40:28'),
(3, 1, 4, 'Grevillea', 120.00, 'Planted', '2026-03-18 06:40:28'),
(4, 16, 11, 'Acacia', 150.00, 'Planted', '2026-03-18 06:41:23'),
(5, 16, 12, 'Mvule', 300.00, 'Planted', '2026-03-18 06:43:14'),
(6, 16, 13, 'Bamboo', 100.00, 'Planted', '2026-03-18 06:43:38'),
(7, 16, 11, 'Acacia', 150.00, 'Planted', '2026-03-18 06:44:24');

-- --------------------------------------------------------

--
-- Table structure for table `wallet`
--

CREATE TABLE `wallet` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallet`
--

INSERT INTO `wallet` (`id`, `user_id`, `balance`, `last_updated`) VALUES
(1, 1, 1385.00, '2026-03-18 05:14:55'),
(2, 1, 1385.00, '2026-03-18 06:45:55'),
(3, 1, 1000.00, '2026-03-18 07:03:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seedlings`
--
ALTER TABLE `seedlings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trees`
--
ALTER TABLE `trees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_trees`
--
ALTER TABLE `user_trees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallet`
--
ALTER TABLE `wallet`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `seedlings`
--
ALTER TABLE `seedlings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `trees`
--
ALTER TABLE `trees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `user_trees`
--
ALTER TABLE `user_trees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `wallet`
--
ALTER TABLE `wallet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
