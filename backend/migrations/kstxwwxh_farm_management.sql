-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 29, 2026 at 03:27 PM
-- Server version: 8.0.41
-- PHP Version: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kstxwwxh_farm_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `collections`
--

CREATE TABLE `collections` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `farm_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'Other',
  `reference_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `collections`
--

INSERT INTO `collections` (`id`, `user_id`, `farm_id`, `customer_id`, `customer_name`, `amount`, `payment_method`, `reference_number`, `payment_date`, `created_at`) VALUES
(1, 6, 6, 4, 'NAOMI', 700.00, 'Other', NULL, '2026-07-23', '2026-07-24 12:54:23'),
(2, 6, 6, 7, 'CAROL', 1000.00, 'Other', NULL, '2026-07-25', '2026-07-25 19:25:09'),
(3, 6, 6, 6, 'FBIL', 5200.00, 'Other', NULL, '2026-07-20', '2026-07-26 10:59:09'),
(4, 6, 6, 4, 'NAOMI', 550.00, 'Other', NULL, '2026-07-17', '2026-07-26 11:04:31'),
(5, 6, 6, 5, 'JOAN', 2555.00, 'Other', NULL, '2026-07-26', '2026-07-26 11:10:24'),
(6, 6, 6, 11, 'X', 525.00, 'Other', NULL, '2026-07-26', '2026-07-26 11:11:08'),
(7, 6, 6, 7, 'CAROL', 800.00, 'Other', NULL, '2026-07-26', '2026-07-26 11:11:48'),
(8, 6, 6, 10, 'MARY', 3360.00, 'Other', NULL, '2026-07-27', '2026-07-27 10:59:24');

-- --------------------------------------------------------

--
-- Table structure for table `cows`
--

CREATE TABLE `cows` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `farm_id` int NOT NULL,
  `cow_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `breed` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `ear_tag` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gender` enum('Male','Female') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Female',
  `status` enum('Active','Dry','Pregnant','Sick','Sold','Deceased') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Active',
  `acquisition_date` date DEFAULT NULL,
  `acquisition_cost` decimal(10,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `weight_kg` decimal(10,2) DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cows`
--

INSERT INTO `cows` (`id`, `user_id`, `farm_id`, `cow_name`, `breed`, `date_of_birth`, `ear_tag`, `gender`, `status`, `acquisition_date`, `acquisition_cost`, `notes`, `created_at`, `updated_at`, `weight_kg`, `image_path`) VALUES
(4, 6, 6, 'Max', 'Friesian', '2025-11-01', '001', 'Female', 'Active', NULL, NULL, '', '2026-06-07 14:54:19', '2026-06-07 14:54:19', 115.00, NULL),
(5, 6, 6, 'Kairetu', 'Holstein', '2023-06-07', '002', 'Female', 'Active', NULL, NULL, '', '2026-06-07 14:55:33', '2026-06-07 14:55:33', 290.00, NULL),
(6, 5, 5, 'Mueni', 'Guernsey', '2025-03-04', 'T001', 'Female', 'Active', NULL, NULL, '', '2026-06-07 15:10:05', '2026-06-07 15:10:05', 125.00, NULL),
(7, 6, 6, 'Giant', 'Holstein', '2021-06-14', '003', 'Female', 'Active', NULL, NULL, '', '2026-06-14 07:58:01', '2026-08-29 15:16:33', 300.00, '/public/uploads/cows/cow_1788016593_e067811711677797.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `farm_id` int NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `price_per_litre` decimal(10,2) NOT NULL DEFAULT '0.00',
  `contact_info` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `user_id`, `farm_id`, `customer_name`, `price_per_litre`, `contact_info`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 5, 'Fort Aqua', 80.00, 'fortaqua@gmail.com', 'Active', '2026-06-17 15:55:20', '2026-06-17 15:55:20'),
(2, 5, 5, 'Kairu', 70.00, '0780909870', 'Active', '2026-06-17 15:59:24', '2026-06-17 15:59:24'),
(3, 6, 6, 'Kairu', 70.00, '0723405355', 'Active', '2026-06-18 09:54:06', '2026-06-18 09:54:06'),
(4, 6, 6, 'NAOMI', 70.00, '0713808997', 'Active', '2026-06-18 09:55:14', '2026-06-18 09:55:14'),
(5, 6, 6, 'JOAN', 70.00, '0726246236', 'Active', '2026-06-18 09:56:30', '2026-06-18 09:56:30'),
(6, 6, 6, 'FBIL', 80.00, '', 'Active', '2026-06-18 09:56:59', '2026-06-18 09:56:59'),
(7, 6, 6, 'CAROL', 70.00, '0720651524', 'Active', '2026-06-18 09:58:25', '2026-06-18 09:58:25'),
(8, 6, 6, 'WAHOME', 70.00, '0724882089', 'Active', '2026-06-18 09:59:33', '2026-06-18 09:59:33'),
(9, 6, 6, 'REBECCA', 70.00, '0110932834', 'Active', '2026-06-18 10:00:27', '2026-06-18 10:00:27'),
(10, 6, 6, 'MARY', 70.00, '0115771986', 'Active', '2026-06-18 10:01:26', '2026-06-18 10:01:26'),
(11, 6, 6, 'X', 70.00, '', 'Active', '2026-06-23 05:17:08', '2026-06-23 05:17:08'),
(12, 6, 6, 'Brookside Dairy', 49.00, '', 'Active', '2026-07-03 14:20:58', '2026-07-03 14:20:58'),
(13, 6, 6, 'Wa Annette', 70.00, '', 'Active', '2026-08-03 19:06:54', '2026-08-03 19:06:54'),
(14, 6, 6, 'Halima', 70.00, '+254717210705', 'Active', '2026-08-23 05:55:50', '2026-08-23 05:55:50'),
(15, 6, 6, 'Maina', 50.00, 'Me', 'Active', '2026-08-23 05:58:01', '2026-08-23 05:58:01');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `farm_id` int NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `feed_id` int DEFAULT NULL,
  `source_type` enum('manual','feed') COLLATE utf8mb4_general_ci DEFAULT 'manual'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `user_id`, `farm_id`, `category`, `description`, `amount`, `expense_date`, `created_at`, `feed_id`, `source_type`) VALUES
(6, 7, 7, 'Feed', 'Monthly supply', 5000.00, '2026-06-13', '2026-06-13 09:59:43', NULL, 'manual'),
(7, 6, 6, 'Transport', 'napier transport', 400.00, '2026-06-01', '2026-06-14 09:06:00', NULL, 'manual'),
(8, 6, 6, 'Vet', 'fly repellant', 1300.00, '2026-06-01', '2026-06-14 09:08:52', NULL, 'manual'),
(9, 6, 6, 'Labour', 'napier cutting', 200.00, '2026-06-01', '2026-06-14 09:09:53', NULL, 'manual'),
(10, 6, 6, 'Labour', 'napier cutting', 200.00, '2026-06-06', '2026-06-14 09:10:57', NULL, 'manual'),
(11, 6, 6, 'Equipment', 'Delivery bottles', 800.00, '2026-06-09', '2026-06-14 09:14:28', NULL, 'manual'),
(12, 6, 6, 'Transport', 'napier transport', 350.00, '2026-06-09', '2026-06-14 09:15:33', NULL, 'manual'),
(13, 6, 6, 'Vet', 'Egocin Wound spray', 500.00, '2026-06-14', '2026-06-14 09:18:07', NULL, 'manual'),
(14, 6, 6, 'Transport', 'milk delivery', 150.00, '2026-06-01', '2026-06-14 09:37:12', NULL, 'manual'),
(15, 6, 6, 'Transport', 'milk delivery', 80.00, '2026-06-02', '2026-06-14 09:38:27', NULL, 'manual'),
(16, 6, 6, 'Transport', 'milk delivery', 100.00, '2026-06-03', '2026-06-14 09:39:31', NULL, 'manual'),
(17, 6, 6, 'Transport', 'milk delivery', 100.00, '2026-06-04', '2026-06-14 09:41:49', NULL, 'manual'),
(18, 6, 6, 'Transport', 'milk delivery', 150.00, '2026-06-05', '2026-06-14 09:42:34', NULL, 'manual'),
(19, 6, 6, 'Transport', 'milk delivery', 150.00, '2026-06-06', '2026-06-14 09:44:31', NULL, 'manual'),
(20, 6, 6, 'Transport', 'milk delivery', 180.00, '2026-06-07', '2026-06-14 09:45:43', NULL, 'manual'),
(21, 6, 6, 'Transport', 'milk delivery', 300.00, '2026-06-08', '2026-06-14 09:46:34', NULL, 'manual'),
(22, 6, 6, 'Transport', 'milk delivery', 300.00, '2026-06-09', '2026-06-14 09:49:48', NULL, 'manual'),
(23, 6, 6, 'Transport', 'milk delivery', 100.00, '2026-06-10', '2026-06-14 09:53:13', NULL, 'manual'),
(24, 6, 6, 'Transport', 'milk delivery', 100.00, '2026-06-10', '2026-06-14 09:54:51', NULL, 'manual'),
(25, 6, 6, 'Transport', 'milk delivery', 200.00, '2026-06-11', '2026-06-14 09:55:47', NULL, 'manual'),
(26, 6, 6, 'Transport', 'milk delivery', 150.00, '2026-06-12', '2026-06-14 09:56:46', NULL, 'manual'),
(27, 6, 6, 'Transport', 'milk delivery', 150.00, '2026-06-13', '2026-06-14 09:57:44', NULL, 'manual'),
(28, 5, 5, 'Feed', 'Dairy meal', 2300.00, '2026-06-18', '2026-06-18 04:01:16', 12, 'feed'),
(29, 6, 6, 'Feed', 'Dairy Meal', 3300.00, '2026-06-19', '2026-06-19 06:08:10', 13, 'feed'),
(30, 6, 6, 'Transport', 'milk delivery', 120.00, '2026-06-14', '2026-06-19 06:21:46', NULL, 'manual'),
(31, 6, 6, 'Transport', 'milk delivery', 150.00, '2026-06-15', '2026-06-19 06:23:02', NULL, 'manual'),
(32, 6, 6, 'Transport', 'milk delivery', 150.00, '2026-06-16', '2026-06-19 06:24:08', NULL, 'manual'),
(33, 6, 6, 'Transport', 'milk delivery', 300.00, '2026-06-17', '2026-06-19 06:25:38', NULL, 'manual'),
(34, 6, 6, 'Transport', 'milk delivery', 300.00, '2026-06-18', '2026-06-19 06:26:36', NULL, 'manual'),
(35, 6, 6, 'Vet', 'Dehorning', 1700.00, '2026-06-10', '2026-06-19 08:25:24', NULL, 'manual'),
(36, 6, 6, 'Transport', 'milk delivery', 120.00, '2026-06-19', '2026-06-23 05:35:19', NULL, 'manual'),
(37, 6, 6, 'Transport', 'milk delivery', 150.00, '2026-06-22', '2026-06-23 05:37:03', NULL, 'manual'),
(38, 6, 6, 'Transport', 'milk delivery', 200.00, '2026-06-22', '2026-06-23 05:37:39', NULL, 'manual'),
(39, 6, 6, 'Transport', 'milk delivery', 200.00, '2026-06-23', '2026-06-23 05:38:43', NULL, 'manual'),
(40, 6, 6, 'Vet', 'Limping foot ', 800.00, '2026-06-26', '2026-06-30 06:09:59', NULL, 'manual'),
(41, 6, 6, 'Feed', 'Macklick Super', 2700.00, '2026-07-02', '2026-07-05 08:55:32', 14, 'feed'),
(42, 6, 6, 'Feed', 'Macklick Plus', 1000.00, '2026-07-02', '2026-07-05 08:56:25', 15, 'feed'),
(43, 6, 6, 'Feed', 'Maize Germ', 2100.00, '2026-07-02', '2026-07-05 09:00:23', 16, 'feed'),
(44, 6, 6, 'Feed', 'Nappier Grass', 4500.00, '2026-07-02', '2026-07-05 09:05:53', 17, 'feed'),
(45, 6, 6, 'Equipment', 'Delivery can', 6500.00, '2026-07-05', '2026-07-05 09:07:40', NULL, 'manual'),
(46, 6, 6, 'Vet', 'Milking Salve', 280.00, '2026-07-02', '2026-07-05 09:08:36', NULL, 'manual'),
(47, 6, 6, 'Transport', 'milk delivery', 200.00, '2026-07-05', '2026-07-05 09:11:40', NULL, 'manual'),
(48, 6, 6, 'Transport', 'milk delivery', 200.00, '2026-07-06', '2026-07-06 07:31:35', NULL, 'manual'),
(49, 6, 6, 'Feed', 'JOY MAX Dairy Meal', 3300.00, '2026-07-08', '2026-07-08 09:00:42', 18, 'feed'),
(50, 6, 6, 'Vet', 'Oxytocin hormone injection ', 2200.00, '2026-07-10', '2026-07-13 14:03:14', NULL, 'manual'),
(51, 6, 6, 'Feed', 'Maize germ', 2300.00, '2026-07-20', '2026-07-20 18:43:52', 19, 'feed'),
(52, 6, 6, 'Feed', 'Macklick Super', 1350.00, '2026-07-20', '2026-07-20 18:44:53', 20, 'feed'),
(53, 6, 6, 'Vet', 'Nilzan Deworming medicine ', 300.00, '2026-07-20', '2026-07-20 18:46:38', NULL, 'manual'),
(54, 6, 6, 'Feed', 'Afri Dairy meal', 800.00, '2026-07-23', '2026-07-23 19:02:10', 21, 'feed'),
(55, 6, 6, 'Vet', 'Insemination', 3500.00, '2026-08-03', '2026-08-16 13:58:52', NULL, 'manual'),
(56, 6, 6, 'Feed', 'Wheat Pollard', 2000.00, '2026-06-10', '2026-08-29 13:22:28', 5, 'feed'),
(57, 6, 6, 'Feed', 'Macklick Super', 1350.00, '2026-06-10', '2026-08-29 13:22:28', 6, 'feed'),
(58, 6, 6, 'Feed', 'Maize Germ', 2500.00, '2026-06-10', '2026-08-29 13:22:28', 7, 'feed'),
(59, 6, 6, 'Feed', 'Nappier Grass', 2500.00, '2026-06-01', '2026-08-29 13:22:28', 8, 'feed'),
(60, 6, 6, 'Feed', 'Macklick Super', 2700.00, '2026-06-01', '2026-08-29 13:22:28', 9, 'feed'),
(61, 6, 6, 'Feed', 'Macklick Plus', 1000.00, '2026-06-01', '2026-08-29 13:22:28', 10, 'feed'),
(62, 6, 6, 'Feed', 'Dairy Meal', 3300.00, '2026-06-01', '2026-08-29 13:22:28', 11, 'feed'),
(63, 6, 6, 'Feed', 'Joymax maize germ', 2300.00, '2026-08-17', '2026-08-29 13:33:49', 22, 'feed'),
(64, 6, 6, 'Feed', 'Protein supplements', 550.00, '2026-08-17', '2026-08-29 13:50:55', 23, 'feed');

-- --------------------------------------------------------

--
-- Table structure for table `farms`
--

CREATE TABLE `farms` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `farm_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `registration_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `total_cows` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farms`
--

INSERT INTO `farms` (`id`, `user_id`, `farm_name`, `location`, `registration_number`, `total_cows`, `created_at`) VALUES
(5, 5, 'DairySync', 'Rwathia', 'FM-36958', 0, '2026-06-07 13:23:53'),
(6, 6, 'Maish Dairy farm', 'Murang&#039;a', 'FM-36742', 0, '2026-06-07 14:53:04'),
(7, 7, 'Loo[', 'WANG&#039;CHIENG', 'FM-64679', 0, '2026-06-13 09:44:54');

-- --------------------------------------------------------

--
-- Table structure for table `farm_settings`
--

CREATE TABLE `farm_settings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_general_ci,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feed_management`
--

CREATE TABLE `feed_management` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `farm_id` int NOT NULL,
  `feed_name` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `feed_type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `quantity_kg` decimal(10,2) DEFAULT '0.00',
  `cost` decimal(10,2) DEFAULT '0.00',
  `supplier` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feed_management`
--

INSERT INTO `feed_management` (`id`, `user_id`, `farm_id`, `feed_name`, `feed_type`, `quantity_kg`, `cost`, `supplier`, `purchase_date`, `notes`, `created_at`) VALUES
(4, 7, 7, 'Loopys', 'Starter', 15.00, 566.00, 'John Kamau', '2026-06-13', '', '2026-06-13 09:57:46'),
(5, 6, 6, 'Wheat Pollard', 'concentrates', 50.00, 2000.00, 'FARMERS GUIDE', '2026-06-10', '', '2026-06-14 08:37:43'),
(6, 6, 6, 'Macklick Super', 'Mineral salts', 5.00, 1350.00, 'FARMERS GUIDE', '2026-06-10', '', '2026-06-14 08:38:57'),
(7, 6, 6, 'Maize Germ', 'concentrates', 50.00, 2500.00, 'FARMERS GUIDE', '2026-06-10', '', '2026-06-14 08:42:11'),
(8, 6, 6, 'Nappier Grass', 'Fodder', 0.00, 2500.00, 'Gathima', '2026-06-01', '', '2026-06-14 08:44:53'),
(9, 6, 6, 'Macklick Super', 'Mineral salts', 10.00, 2700.00, 'FARMERS GUIDE', '2026-06-01', '', '2026-06-14 08:45:52'),
(10, 6, 6, 'Macklick Plus', 'Mineral salts', 5.00, 1000.00, 'FARMERS GUIDE', '2026-06-01', '', '2026-06-14 08:46:38'),
(11, 6, 6, 'Dairy Meal', 'concentrates', 70.00, 3300.00, 'FARMERS GUIDE', '2026-06-01', '', '2026-06-14 08:49:08'),
(12, 5, 5, 'Dairy meal', 'Suppliment', 30.00, 2300.00, 'KImani and JOhnson', '2026-06-18', '//', '2026-06-18 04:01:16'),
(13, 6, 6, 'Dairy Meal', 'concentrates', 70.00, 3300.00, 'FARMERS GUIDE', '2026-06-19', '', '2026-06-19 06:08:10'),
(14, 6, 6, 'Macklick Super', 'Mineral salts', 10.00, 2700.00, 'FARMERS GUIDE', '2026-07-02', '', '2026-07-05 08:55:32'),
(15, 6, 6, 'Macklick Plus', 'Mineral salts', 5.00, 1000.00, 'FARMERS GUIDE', '2026-07-02', '', '2026-07-05 08:56:25'),
(16, 6, 6, 'Maize Germ', 'concentrates', 50.00, 2100.00, 'FARMERS GUIDE', '2026-07-02', '', '2026-07-05 09:00:23'),
(17, 6, 6, 'Nappier Grass', 'Fodder', 1000.00, 4500.00, 'Waweru', '2026-07-02', 'inclusive of maize stalks 2500', '2026-07-05 09:05:52'),
(18, 6, 6, 'JOY MAX Dairy Meal', 'concentrates', 70.00, 3300.00, 'Rwathia Farrmcare', '2026-07-08', '', '2026-07-08 09:00:42'),
(19, 6, 6, 'Maize germ', 'Concentrates', 50.00, 2300.00, 'Farmers Guide', '2026-07-20', '', '2026-07-20 18:43:52'),
(20, 6, 6, 'Macklick Super', 'Salt', 5.00, 1350.00, 'Farmers Guide', '2026-07-20', '', '2026-07-20 18:44:53'),
(21, 6, 6, 'Afri Dairy meal', 'Concentrates', 20.00, 800.00, 'Rwathia', '2026-07-23', '', '2026-07-23 19:02:10'),
(22, 6, 6, 'Joymax maize germ', 'Maize germ', 50.00, 2300.00, 'Rwathia  agrovet', '2026-08-17', '', '2026-08-29 13:33:49'),
(23, 6, 6, 'Protein supplements', 'Concentrates', 7.00, 550.00, 'Farmers Guide', '2026-08-17', 'Cotton cake, fish meal, Bone meal, sunflower,', '2026-08-29 13:50:55');

-- --------------------------------------------------------

--
-- Table structure for table `health_records`
--

CREATE TABLE `health_records` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `farm_id` int NOT NULL,
  `cow_id` int NOT NULL,
  `condition_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `treatment` text COLLATE utf8mb4_general_ci,
  `status` enum('Healthy','Under Treatment','Recovered','Critical') COLLATE utf8mb4_general_ci DEFAULT 'Healthy',
  `notes` text COLLATE utf8mb4_general_ci,
  `record_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `health_records`
--

INSERT INTO `health_records` (`id`, `user_id`, `farm_id`, `cow_id`, `condition_name`, `treatment`, `status`, `notes`, `record_date`, `created_at`, `updated_at`) VALUES
(5, 5, 1, 6, 'Mastitis', 'Mastitis', 'Under Treatment', 'Under treatment to be observed', '2026-06-17', '2026-06-17 15:45:46', '2026-06-17 15:45:46'),
(6, 6, 1, 7, 'Dehorning', 'Dehorned', 'Healthy', '', '2026-06-10', '2026-06-19 08:19:25', '2026-06-19 08:19:25'),
(7, 6, 1, 4, 'Dehorning', 'Dehorned', 'Healthy', '', '2026-06-10', '2026-06-19 08:19:53', '2026-06-19 08:19:53'),
(8, 6, 1, 7, 'Weight recording', '204cm \r\n690kgs', 'Healthy', '', '2026-06-23', '2026-06-23 07:10:36', '2026-06-23 07:10:36'),
(9, 6, 1, 5, 'Weight recording', '156cm\r\n308kgs', 'Healthy', '', '2026-06-23', '2026-06-23 07:11:19', '2026-06-23 07:11:19'),
(10, 6, 1, 4, 'Weight recording', '123cm\r\n162kgs', 'Healthy', '', '2026-06-23', '2026-06-23 07:11:54', '2026-06-23 07:11:54'),
(11, 6, 1, 7, 'Limping foot', 'Treated', 'Healthy', 'Under observation', '2026-06-26', '2026-06-29 07:28:58', '2026-06-29 07:28:58'),
(12, 6, 1, 7, 'Oxytocin hormone injection', 'Injected', 'Healthy', '', '2026-07-10', '2026-07-13 14:02:21', '2026-07-13 14:02:21'),
(13, 6, 1, 5, 'Oxytocin hormone injection', 'Injected', 'Healthy', '', '2026-07-10', '2026-07-13 14:02:43', '2026-07-13 14:02:43');

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `farm_id` int NOT NULL,
  `source` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'Milk Sales',
  `customer_id` int DEFAULT NULL,
  `litres` decimal(10,2) NOT NULL,
  `rate_per_litre` decimal(10,2) DEFAULT '70.00',
  `total_amount` decimal(10,2) NOT NULL,
  `nrm_value` decimal(10,2) DEFAULT '0.00',
  `income_date` date NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cow_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `income`
--

INSERT INTO `income` (`id`, `user_id`, `farm_id`, `source`, `customer_id`, `litres`, `rate_per_litre`, `total_amount`, `nrm_value`, `income_date`, `customer_name`, `cow_id`, `created_at`) VALUES
(27, 6, 6, 'Milk Sales', NULL, 3.00, 70.00, 210.00, 16.80, '2026-06-14', 'joan', NULL, '2026-06-14 08:02:12'),
(28, 6, 6, 'Milk Sales', NULL, 1.50, 70.00, 105.00, 15.30, '2026-06-14', 'Mary', NULL, '2026-06-14 08:06:50'),
(29, 5, 5, 'Milk Sales', 2, 3.00, 70.00, 210.00, 9.00, '2026-06-17', 'Kairu', NULL, '2026-06-17 15:59:46'),
(30, 5, 5, 'Milk Sales', 1, 4.00, 80.00, 320.00, 5.00, '2026-06-17', 'Fort Aqua', NULL, '2026-06-17 16:03:06'),
(31, 5, 5, 'Milk Sales', 1, 4.00, 80.00, 320.00, 6.00, '2026-06-18', 'Fort Aqua', NULL, '2026-06-18 03:56:16'),
(32, 5, 5, 'Milk Sales', 2, 3.00, 70.00, 210.00, 3.00, '2026-06-18', 'Kairu', NULL, '2026-06-18 04:00:37'),
(33, 6, 6, 'Milk Sales', 4, 3.50, 70.00, 245.00, 4.00, '2026-06-01', 'NAOMI', NULL, '2026-06-18 10:17:47'),
(34, 6, 6, 'Milk Sales', 7, 4.00, 70.00, 280.00, 0.00, '2026-06-01', 'CAROL', NULL, '2026-06-18 10:18:31'),
(35, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 13.30, '2026-06-18', 'FBIL', NULL, '2026-06-18 10:18:50'),
(36, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 4.90, '2026-06-02', 'FBIL', NULL, '2026-06-18 10:19:23'),
(37, 6, 6, 'Milk Sales', 5, 4.00, 70.00, 280.00, 0.90, '2026-06-02', 'JOAN', NULL, '2026-06-18 10:19:50'),
(38, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 4.40, '2026-06-03', 'FBIL', NULL, '2026-06-18 10:20:18'),
(39, 6, 6, 'Milk Sales', 3, 3.00, 70.00, 210.00, 1.40, '2026-06-03', 'Kairu', NULL, '2026-06-18 10:21:00'),
(40, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 4.60, '2026-06-04', 'FBIL', NULL, '2026-06-18 10:23:29'),
(41, 6, 6, 'Milk Sales', 3, 2.00, 70.00, 140.00, 2.60, '2026-06-04', 'Kairu', NULL, '2026-06-18 10:25:31'),
(42, 6, 6, 'Milk Sales', 8, 2.00, 70.00, 140.00, 0.60, '2026-06-04', 'WAHOME', NULL, '2026-06-18 10:26:47'),
(43, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 3.80, '2026-06-05', 'FBIL', NULL, '2026-06-18 10:27:27'),
(44, 6, 6, 'Milk Sales', 4, 3.50, 70.00, 245.00, 0.30, '2026-06-05', 'NAOMI', NULL, '2026-06-18 10:28:24'),
(45, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 4.00, '2026-06-06', 'FBIL', NULL, '2026-06-18 10:28:55'),
(46, 6, 6, 'Milk Sales', 9, 3.50, 70.00, 245.00, 0.50, '2026-06-06', 'REBECCA', NULL, '2026-06-18 10:29:43'),
(47, 6, 6, 'Milk Sales', 5, 3.50, 70.00, 245.00, 3.70, '2026-06-07', 'JOAN', NULL, '2026-06-18 10:30:19'),
(48, 6, 6, 'Milk Sales', 7, 3.50, 70.00, 245.00, 0.20, '2026-06-07', 'CAROL', NULL, '2026-06-18 10:30:50'),
(49, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 4.60, '2026-06-08', 'FBIL', NULL, '2026-06-18 10:31:32'),
(51, 6, 6, 'Milk Sales', 5, 4.00, 70.00, 280.00, 14.30, '2026-06-09', 'JOAN', NULL, '2026-06-18 10:34:22'),
(52, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 11.30, '2026-06-09', 'FBIL', NULL, '2026-06-18 10:34:45'),
(53, 6, 6, 'Milk Sales', 9, 3.00, 70.00, 210.00, 8.30, '2026-06-09', 'REBECCA', NULL, '2026-06-18 10:39:21'),
(54, 6, 6, 'Milk Sales', 7, 4.00, 70.00, 280.00, 13.70, '2026-06-10', 'CAROL', NULL, '2026-06-18 10:40:59'),
(55, 6, 6, 'Milk Sales', 4, 4.00, 70.00, 280.00, 0.60, '2026-06-08', 'NAOMI', NULL, '2026-06-18 10:42:42'),
(56, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 10.70, '2026-06-10', 'FBIL', NULL, '2026-06-18 10:43:33'),
(57, 6, 6, 'Milk Sales', 3, 3.00, 70.00, 210.00, 7.70, '2026-06-10', 'Kairu', NULL, '2026-06-18 10:44:09'),
(58, 6, 6, 'Milk Sales', 8, 5.00, 70.00, 350.00, 2.70, '2026-06-10', 'WAHOME', NULL, '2026-06-18 10:44:32'),
(59, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 1.20, '2026-06-10', 'MARY', NULL, '2026-06-18 10:45:01'),
(60, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 15.70, '2026-06-11', 'FBIL', NULL, '2026-06-18 10:45:40'),
(61, 6, 6, 'Milk Sales', 3, 2.00, 70.00, 140.00, 13.70, '2026-06-11', 'Kairu', NULL, '2026-06-18 10:47:44'),
(62, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 12.20, '2026-06-11', 'MARY', NULL, '2026-06-18 10:48:13'),
(63, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 17.70, '2026-06-12', 'FBIL', NULL, '2026-06-18 10:49:01'),
(64, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 16.20, '2026-06-12', 'MARY', NULL, '2026-06-18 10:49:27'),
(67, 6, 6, 'Milk Sales', 4, 5.00, 70.00, 350.00, 13.20, '2026-06-13', 'NAOMI', NULL, '2026-06-18 10:51:55'),
(68, 6, 6, 'Milk Sales', 9, 3.00, 70.00, 210.00, 10.20, '2026-06-13', 'REBECCA', NULL, '2026-06-18 10:52:38'),
(69, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 8.70, '2026-06-13', 'MARY', NULL, '2026-06-18 10:53:13'),
(70, 6, 6, 'Milk Sales', 7, 5.00, 70.00, 350.00, 10.30, '2026-06-14', 'CAROL', NULL, '2026-06-18 10:55:57'),
(71, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 16.60, '2026-06-15', 'FBIL', NULL, '2026-06-18 10:57:05'),
(72, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 15.40, '2026-06-16', 'FBIL', NULL, '2026-06-18 10:57:27'),
(73, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 13.90, '2026-06-16', 'MARY', NULL, '2026-06-18 10:58:22'),
(74, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 13.90, '2026-06-17', 'FBIL', NULL, '2026-06-18 10:59:06'),
(75, 6, 6, 'Milk Sales', 3, 4.00, 70.00, 280.00, 9.90, '2026-06-17', 'Kairu', NULL, '2026-06-18 10:59:42'),
(76, 6, 6, 'Milk Sales', 8, 5.00, 70.00, 350.00, 4.90, '2026-06-17', 'WAHOME', NULL, '2026-06-18 11:00:33'),
(78, 6, 6, 'Milk Sales', 4, 5.00, 70.00, 350.00, 8.30, '2026-06-18', 'NAOMI', NULL, '2026-06-19 06:04:19'),
(79, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 13.30, '2026-06-19', 'FBIL', NULL, '2026-06-19 06:06:01'),
(80, 6, 6, 'Milk Sales', 7, 5.00, 70.00, 350.00, 8.30, '2026-06-19', 'CAROL', NULL, '2026-06-20 06:49:51'),
(81, 6, 6, 'Milk Sales', 5, 4.00, 70.00, 280.00, 10.30, '2026-06-20', 'JOAN', NULL, '2026-06-20 06:50:19'),
(82, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 6.80, '2026-06-19', 'MARY', NULL, '2026-06-20 06:50:48'),
(83, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 10.30, '2026-06-22', 'FBIL', NULL, '2026-06-23 04:01:57'),
(84, 6, 6, 'Milk Sales', 9, 2.00, 70.00, 140.00, 8.30, '2026-06-22', 'REBECCA', NULL, '2026-06-23 04:02:19'),
(85, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 12.70, '2026-06-23', 'FBIL', NULL, '2026-06-23 04:02:55'),
(86, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 6.80, '2026-06-22', 'MARY', NULL, '2026-06-23 05:16:44'),
(87, 6, 6, 'Milk Sales', 11, 0.50, 70.00, 35.00, 6.30, '2026-06-22', 'X', NULL, '2026-06-23 05:17:40'),
(88, 6, 6, 'Milk Sales', 4, 5.00, 70.00, 350.00, 7.70, '2026-06-23', 'NAOMI', NULL, '2026-06-24 05:46:42'),
(89, 6, 6, 'Milk Sales', 11, 0.50, 70.00, 35.00, 7.20, '2026-06-23', 'X', NULL, '2026-06-24 05:47:00'),
(90, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 5.70, '2026-06-23', 'MARY', NULL, '2026-06-24 05:47:28'),
(91, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 13.10, '2026-06-24', 'FBIL', NULL, '2026-06-24 05:47:56'),
(92, 6, 6, 'Milk Sales', 3, 5.00, 70.00, 350.00, 8.10, '2026-06-24', 'Kairu', NULL, '2026-06-24 05:48:11'),
(93, 6, 6, 'Milk Sales', 8, 5.00, 70.00, 350.00, 3.10, '2026-06-24', 'WAHOME', NULL, '2026-06-24 18:23:40'),
(94, 6, 6, 'Milk Sales', 11, 0.50, 70.00, 35.00, 2.60, '2026-06-24', 'X', NULL, '2026-06-24 18:24:03'),
(95, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 1.10, '2026-06-24', 'MARY', NULL, '2026-06-24 18:24:26'),
(96, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 13.60, '2026-06-25', 'FBIL', NULL, '2026-06-25 04:07:36'),
(97, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 12.10, '2026-06-25', 'MARY', NULL, '2026-06-25 18:37:45'),
(98, 6, 6, 'Milk Sales', 11, 0.50, 70.00, 35.00, 11.60, '2026-06-25', 'X', NULL, '2026-06-25 18:37:59'),
(99, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 13.00, '2026-06-26', 'FBIL', NULL, '2026-06-26 05:54:18'),
(100, 6, 6, 'Milk Sales', 9, 4.00, 70.00, 280.00, 12.00, '2026-06-27', 'REBECCA', NULL, '2026-06-27 17:59:13'),
(101, 6, 6, 'Milk Sales', 5, 4.00, 70.00, 280.00, 8.00, '2026-06-27', 'JOAN', NULL, '2026-06-27 17:59:26'),
(102, 6, 6, 'Milk Sales', 11, 1.00, 70.00, 70.00, 7.00, '2026-06-27', 'X', NULL, '2026-06-27 17:59:48'),
(103, 6, 6, 'Milk Sales', 7, 5.00, 70.00, 350.00, 10.10, '2026-06-28', 'CAROL', NULL, '2026-06-28 16:50:06'),
(104, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 8.60, '2026-06-28', 'MARY', NULL, '2026-06-28 16:50:22'),
(105, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 9.60, '2026-06-29', 'FBIL', NULL, '2026-06-29 07:15:24'),
(106, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 10.60, '2026-06-30', 'FBIL', NULL, '2026-06-30 06:07:36'),
(107, 6, 6, 'Milk Sales', 4, 5.00, 70.00, 350.00, 4.60, '2026-06-29', 'NAOMI', NULL, '2026-06-30 06:08:04'),
(108, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 3.10, '2026-06-29', 'MARY', NULL, '2026-06-30 06:08:30'),
(109, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 9.10, '2026-06-30', 'MARY', NULL, '2026-07-01 05:11:26'),
(110, 6, 6, 'Milk Sales', 11, 0.50, 70.00, 35.00, 8.60, '2026-06-30', 'X', NULL, '2026-07-01 05:11:50'),
(111, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 11.50, '2026-07-01', 'FBIL', NULL, '2026-07-01 05:18:56'),
(112, 6, 6, 'Milk Sales', 3, 4.00, 70.00, 280.00, 7.50, '2026-07-01', 'Kairu', NULL, '2026-07-01 05:19:13'),
(113, 6, 6, 'Milk Sales', 8, 5.00, 70.00, 350.00, 2.50, '2026-07-01', 'WAHOME', NULL, '2026-07-01 18:58:30'),
(114, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 1.00, '2026-07-01', 'MARY', NULL, '2026-07-01 18:58:49'),
(115, 6, 6, 'Milk Sales', 11, 0.50, 70.00, 35.00, 0.50, '2026-07-01', 'X', NULL, '2026-07-01 18:59:03'),
(116, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 10.10, '2026-07-02', 'FBIL', NULL, '2026-07-02 04:53:12'),
(117, 6, 6, 'Milk Sales', 3, 1.00, 70.00, 70.00, 9.10, '2026-07-02', 'Kairu', NULL, '2026-07-02 04:54:17'),
(118, 6, 6, 'Milk Sales', 12, 7.90, 49.00, 387.10, 5.80, '2026-07-03', 'Brookside Dairy', NULL, '2026-07-03 19:10:55'),
(119, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 2.80, '2026-07-03', 'FBIL', NULL, '2026-07-03 19:11:44'),
(120, 6, 6, 'Milk Sales', 7, 2.50, 70.00, 175.00, 0.30, '2026-07-03', 'CAROL', NULL, '2026-07-03 19:12:17'),
(121, 6, 6, 'Milk Sales', 12, 6.00, 49.00, 294.00, 9.40, '2026-07-04', 'Brookside Dairy', NULL, '2026-07-04 13:15:54'),
(122, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 7.90, '2026-07-04', 'MARY', NULL, '2026-07-04 18:27:16'),
(123, 6, 6, 'Milk Sales', 4, 5.00, 70.00, 350.00, 2.90, '2026-07-04', 'NAOMI', NULL, '2026-07-04 18:27:38'),
(124, 6, 6, 'Milk Sales', 11, 0.50, 70.00, 35.00, 2.40, '2026-07-04', 'X', NULL, '2026-07-04 18:27:55'),
(125, 6, 6, 'Milk Sales', 12, 6.90, 49.00, 338.10, 8.90, '2026-07-05', 'Brookside Dairy', NULL, '2026-07-05 07:57:42'),
(126, 6, 6, 'Milk Sales', 5, 4.00, 70.00, 280.00, 4.90, '2026-07-05', 'JOAN', NULL, '2026-07-05 18:26:06'),
(127, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 3.40, '2026-07-05', 'MARY', NULL, '2026-07-05 18:26:19'),
(128, 6, 6, 'Milk Sales', 12, 6.30, 49.00, 308.70, 10.00, '2026-07-06', 'Brookside Dairy', NULL, '2026-07-06 04:59:52'),
(129, 6, 6, 'Milk Sales', 6, 2.00, 80.00, 160.00, 8.00, '2026-07-06', 'FBIL', NULL, '2026-07-06 05:01:02'),
(130, 6, 6, 'Milk Sales', 6, 1.00, 80.00, 80.00, 2.40, '2026-07-05', 'FBIL', NULL, '2026-07-06 05:01:27'),
(131, 6, 6, 'Milk Sales', 12, 5.50, 49.00, 269.50, 10.80, '2026-07-07', 'Brookside Dairy', NULL, '2026-07-07 04:51:44'),
(132, 6, 6, 'Milk Sales', 6, 2.50, 80.00, 200.00, 8.30, '2026-07-07', 'FBIL', NULL, '2026-07-07 04:52:45'),
(133, 6, 6, 'Milk Sales', 12, 4.80, 49.00, 235.20, 3.50, '2026-07-07', 'Brookside Dairy', NULL, '2026-07-08 02:58:04'),
(134, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 2.00, '2026-07-07', 'MARY', NULL, '2026-07-08 02:58:32'),
(135, 6, 6, 'Milk Sales', 11, 1.00, 70.00, 70.00, 1.00, '2026-07-07', 'X', NULL, '2026-07-08 02:58:48'),
(136, 6, 6, 'Milk Sales', 12, 0.70, 49.00, 34.30, 15.00, '2026-07-08', 'Brookside Dairy', NULL, '2026-07-08 05:38:26'),
(137, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 12.00, '2026-07-08', 'FBIL', NULL, '2026-07-08 05:38:39'),
(138, 6, 6, 'Milk Sales', 3, 3.80, 70.00, 266.00, 8.20, '2026-07-08', 'Kairu', NULL, '2026-07-08 05:39:31'),
(139, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 12.70, '2026-07-09', 'FBIL', NULL, '2026-07-09 05:56:46'),
(140, 6, 6, 'Milk Sales', 3, 1.00, 70.00, 70.00, 11.70, '2026-07-09', 'Kairu', NULL, '2026-07-09 05:57:14'),
(141, 6, 6, 'Milk Sales', 9, 3.40, 70.00, 238.00, 8.30, '2026-07-09', 'REBECCA', NULL, '2026-07-09 05:57:29'),
(142, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 6.70, '2026-07-08', 'MARY', NULL, '2026-07-09 06:38:02'),
(143, 6, 6, 'Milk Sales', 8, 5.00, 70.00, 350.00, 1.70, '2026-07-08', 'WAHOME', NULL, '2026-07-09 06:38:22'),
(144, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 0.20, '2026-07-08', 'MARY', NULL, '2026-07-10 05:43:01'),
(145, 6, 6, 'Milk Sales', 4, 5.00, 70.00, 350.00, 3.30, '2026-07-09', 'NAOMI', NULL, '2026-07-10 05:43:21'),
(146, 6, 6, 'Milk Sales', 12, 0.70, 49.00, 34.30, 2.60, '2026-07-09', 'Brookside Dairy', NULL, '2026-07-10 05:43:48'),
(147, 6, 6, 'Milk Sales', 12, 5.00, 49.00, 245.00, 10.90, '2026-07-10', 'Brookside Dairy', NULL, '2026-07-10 05:44:16'),
(148, 6, 6, 'Milk Sales', 6, 2.70, 80.00, 216.00, 8.20, '2026-07-10', 'FBIL', NULL, '2026-07-10 05:44:44'),
(149, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 6.70, '2026-07-10', 'MARY', NULL, '2026-07-10 18:38:33'),
(150, 6, 6, 'Milk Sales', 12, 4.80, 49.00, 235.20, 1.90, '2026-07-10', 'Brookside Dairy', NULL, '2026-07-11 03:21:58'),
(151, 6, 6, 'Milk Sales', 12, 2.50, 49.00, 122.50, 15.80, '2026-07-11', 'Brookside Dairy', NULL, '2026-07-11 05:13:51'),
(152, 6, 6, 'Milk Sales', 5, 5.00, 70.00, 350.00, 10.80, '2026-07-11', 'JOAN', NULL, '2026-07-11 05:14:03'),
(153, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 9.30, '2026-07-11', 'MARY', NULL, '2026-07-11 17:51:14'),
(155, 6, 6, 'Milk Sales', 12, 7.00, 49.00, 343.00, 2.30, '2026-07-11', 'Brookside Dairy', NULL, '2026-07-12 03:53:57'),
(156, 6, 6, 'Milk Sales', 12, 5.30, 49.00, 259.70, 12.00, '2026-07-12', 'Brookside Dairy', NULL, '2026-07-12 05:14:23'),
(157, 6, 6, 'Milk Sales', 12, 5.70, 49.00, 279.30, 6.30, '2026-07-12', 'Brookside Dairy', NULL, '2026-07-13 05:26:57'),
(158, 6, 6, 'Milk Sales', 12, 1.00, 49.00, 49.00, 17.30, '2026-07-13', 'Brookside Dairy', NULL, '2026-07-13 05:27:07'),
(159, 6, 6, 'Milk Sales', 6, 1.50, 80.00, 120.00, 0.80, '2026-07-11', 'FBIL', NULL, '2026-07-13 11:55:55'),
(160, 6, 6, 'Milk Sales', 7, 5.00, 70.00, 350.00, 12.30, '2026-07-13', 'CAROL', NULL, '2026-07-13 11:56:44'),
(161, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 4.80, '2026-07-12', 'MARY', NULL, '2026-07-13 11:59:05'),
(162, 6, 6, 'Milk Sales', 11, 2.00, 70.00, 140.00, 2.80, '2026-07-12', 'X', NULL, '2026-07-13 12:00:37'),
(163, 6, 6, 'Milk Sales', 4, 5.00, 70.00, 350.00, 7.30, '2026-07-13', 'NAOMI', NULL, '2026-07-13 18:27:20'),
(164, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 5.80, '2026-07-13', 'MARY', NULL, '2026-07-13 18:27:43'),
(165, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 2.80, '2026-07-13', 'FBIL', NULL, '2026-07-13 18:28:49'),
(167, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 14.60, '2026-07-14', 'FBIL', NULL, '2026-07-14 07:20:31'),
(168, 6, 6, 'Milk Sales', 12, 5.30, 49.00, 259.70, 9.30, '2026-07-14', 'Brookside Dairy', NULL, '2026-07-14 08:39:28'),
(169, 6, 6, 'Milk Sales', 12, 1.60, 49.00, 78.40, 1.20, '2026-07-13', 'Brookside Dairy', NULL, '2026-07-14 08:40:46'),
(170, 6, 6, 'Milk Sales', 9, 5.00, 70.00, 350.00, 4.30, '2026-07-14', 'REBECCA', NULL, '2026-07-14 17:55:53'),
(171, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 2.80, '2026-07-14', 'MARY', NULL, '2026-07-14 17:56:07'),
(172, 6, 6, 'Milk Sales', 3, 5.00, 70.00, 350.00, 11.50, '2026-07-15', 'Kairu', NULL, '2026-07-15 03:59:44'),
(173, 6, 6, 'Milk Sales', 6, 2.50, 80.00, 200.00, 9.00, '2026-07-15', 'FBIL', NULL, '2026-07-15 04:00:17'),
(174, 6, 6, 'Milk Sales', 6, 0.50, 80.00, 40.00, 2.30, '2026-07-14', 'FBIL', NULL, '2026-07-15 04:00:36'),
(175, 6, 6, 'Milk Sales', 8, 5.00, 70.00, 350.00, 4.00, '2026-07-15', 'WAHOME', NULL, '2026-07-16 04:22:24'),
(176, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 2.50, '2026-07-15', 'MARY', NULL, '2026-07-16 04:22:38'),
(177, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 13.40, '2026-07-16', 'FBIL', NULL, '2026-07-16 04:26:28'),
(178, 6, 6, 'Milk Sales', 12, 5.00, 49.00, 245.00, 8.40, '2026-07-16', 'Brookside Dairy', NULL, '2026-07-16 07:37:46'),
(179, 6, 6, 'Milk Sales', 12, 1.60, 49.00, 78.40, 0.90, '2026-07-15', 'Brookside Dairy', NULL, '2026-07-16 07:38:34'),
(180, 6, 6, 'Milk Sales', 5, 4.00, 70.00, 280.00, 4.40, '2026-07-16', 'JOAN', NULL, '2026-07-17 06:26:16'),
(181, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 2.90, '2026-07-16', 'MARY', NULL, '2026-07-17 06:26:39'),
(182, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 10.90, '2026-07-17', 'FBIL', NULL, '2026-07-17 06:27:04'),
(183, 6, 6, 'Milk Sales', 12, 4.30, 49.00, 210.70, 6.60, '2026-07-17', 'Brookside Dairy', NULL, '2026-07-17 07:38:09'),
(184, 6, 6, 'Milk Sales', 12, 1.70, 49.00, 83.30, 1.20, '2026-07-16', 'Brookside Dairy', NULL, '2026-07-17 07:39:29'),
(185, 6, 6, 'Milk Sales', 7, 4.00, 70.00, 280.00, 2.60, '2026-07-17', 'CAROL', NULL, '2026-07-17 08:47:22'),
(186, 6, 6, 'Milk Sales', 12, 5.20, 49.00, 254.80, 8.60, '2026-07-18', 'Brookside Dairy', NULL, '2026-07-18 07:41:11'),
(187, 6, 6, 'Milk Sales', 7, 5.00, 70.00, 350.00, 3.60, '2026-07-18', 'CAROL', NULL, '2026-07-18 19:11:05'),
(188, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 2.10, '2026-07-18', 'MARY', NULL, '2026-07-18 19:11:25'),
(189, 6, 6, 'Milk Sales', 12, 5.00, 49.00, 245.00, 9.00, '2026-07-19', 'Brookside Dairy', NULL, '2026-07-19 14:41:49'),
(190, 6, 6, 'Milk Sales', 5, 4.00, 70.00, 280.00, 5.00, '2026-07-19', 'JOAN', NULL, '2026-07-19 17:08:41'),
(191, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 11.40, '2026-07-20', 'FBIL', NULL, '2026-07-20 05:47:39'),
(192, 6, 6, 'Milk Sales', 12, 3.70, 49.00, 181.30, 7.70, '2026-07-20', 'Brookside Dairy', NULL, '2026-07-20 05:48:06'),
(193, 6, 6, 'Milk Sales', 12, 2.70, 49.00, 132.30, 2.30, '2026-07-19', 'Brookside Dairy', NULL, '2026-07-20 05:49:07'),
(194, 6, 6, 'Milk Sales', 4, 5.00, 70.00, 350.00, 2.70, '2026-07-20', 'NAOMI', NULL, '2026-07-20 17:30:07'),
(195, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 10.20, '2026-07-21', 'FBIL', NULL, '2026-07-21 04:13:55'),
(196, 6, 6, 'Milk Sales', 12, 2.50, 49.00, 122.50, 7.70, '2026-07-21', 'Brookside Dairy', NULL, '2026-07-21 04:14:44'),
(197, 6, 6, 'Milk Sales', 12, 1.60, 49.00, 78.40, 1.10, '2026-07-20', 'Brookside Dairy', NULL, '2026-07-21 04:15:24'),
(198, 6, 6, 'Milk Sales', 9, 5.00, 70.00, 350.00, 2.70, '2026-07-21', 'REBECCA', NULL, '2026-07-21 18:14:19'),
(199, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 11.60, '2026-07-22', 'FBIL', NULL, '2026-07-22 04:07:10'),
(200, 6, 6, 'Milk Sales', 12, 3.90, 49.00, 191.10, 7.70, '2026-07-22', 'Brookside Dairy', NULL, '2026-07-22 04:07:24'),
(201, 6, 6, 'Milk Sales', 12, 1.30, 49.00, 63.70, 1.40, '2026-07-21', 'Brookside Dairy', NULL, '2026-07-22 04:08:03'),
(202, 6, 6, 'Milk Sales', 3, 5.00, 70.00, 350.00, 2.70, '2026-07-22', 'Kairu', NULL, '2026-07-22 19:02:22'),
(203, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 1.20, '2026-07-22', 'MARY', NULL, '2026-07-22 19:02:42'),
(204, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 12.00, '2026-07-23', 'FBIL', NULL, '2026-07-23 06:35:36'),
(205, 6, 6, 'Milk Sales', 12, 3.00, 49.00, 147.00, 9.00, '2026-07-23', 'Brookside Dairy', NULL, '2026-07-23 06:36:03'),
(206, 6, 6, 'Milk Sales', 8, 5.00, 70.00, 350.00, 4.00, '2026-07-23', 'WAHOME', NULL, '2026-07-23 18:59:14'),
(207, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 2.50, '2026-07-23', 'MARY', NULL, '2026-07-23 18:59:37'),
(208, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 12.00, '2026-07-24', 'FBIL', NULL, '2026-07-24 03:35:29'),
(209, 6, 6, 'Milk Sales', 12, 3.30, 49.00, 161.70, 8.70, '2026-07-24', 'Brookside Dairy', NULL, '2026-07-24 03:35:46'),
(210, 6, 6, 'Milk Sales', 12, 0.90, 49.00, 44.10, 1.60, '2026-07-23', 'Brookside Dairy', NULL, '2026-07-24 03:36:17'),
(212, 6, 6, 'Milk Sales', 4, 5.00, 70.00, 350.00, 3.70, '2026-07-24', 'NAOMI', NULL, '2026-07-24 18:32:01'),
(213, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 2.20, '2026-07-24', 'MARY', NULL, '2026-07-24 18:32:14'),
(214, 6, 6, 'Milk Sales', 12, 6.40, 49.00, 313.60, 7.80, '2026-07-25', 'Brookside Dairy', NULL, '2026-07-25 16:31:59'),
(215, 6, 6, 'Milk Sales', 12, 1.40, 49.00, 68.60, 0.80, '2026-07-24', 'Brookside Dairy', NULL, '2026-07-25 16:32:48'),
(216, 6, 6, 'Milk Sales', 7, 5.00, 70.00, 350.00, 2.80, '2026-07-25', 'CAROL', NULL, '2026-07-25 16:33:03'),
(217, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 1.30, '2026-07-25', 'MARY', NULL, '2026-07-25 16:33:16'),
(219, 6, 6, 'Milk Sales', 3, 5.00, 70.00, 350.00, 9.60, '2026-07-26', 'Kairu', NULL, '2026-07-26 04:32:56'),
(220, 6, 6, 'Milk Sales', 5, 2.00, 70.00, 140.00, 7.60, '2026-07-26', 'JOAN', NULL, '2026-07-26 04:33:05'),
(227, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 11.30, '2026-07-27', 'FBIL', NULL, '2026-07-27 10:42:14'),
(228, 6, 6, 'Milk Sales', 12, 3.80, 49.00, 186.20, 7.50, '2026-07-27', 'Brookside Dairy', NULL, '2026-07-27 10:42:49'),
(229, 6, 6, 'Milk Sales', 12, 4.20, 49.00, 205.80, 3.40, '2026-07-26', 'Brookside Dairy', NULL, '2026-07-27 10:47:19'),
(230, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 1.90, '2026-07-26', 'MARY', NULL, '2026-07-27 10:54:35'),
(232, 6, 6, 'Milk Sales', 9, 5.00, 70.00, 350.00, 2.50, '2026-07-27', 'REBECCA', NULL, '2026-07-27 18:04:36'),
(233, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 1.00, '2026-07-27', 'MARY', NULL, '2026-07-27 18:04:53'),
(234, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 10.40, '2026-07-28', 'FBIL', NULL, '2026-07-28 03:18:47'),
(235, 6, 6, 'Milk Sales', 12, 3.00, 49.00, 147.00, 7.40, '2026-07-28', 'Brookside Dairy', NULL, '2026-07-28 03:18:56'),
(236, 6, 6, 'Milk Sales', 4, 5.00, 70.00, 350.00, 2.40, '2026-07-28', 'NAOMI', NULL, '2026-07-28 18:32:14'),
(237, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 0.90, '2026-07-28', 'MARY', NULL, '2026-07-28 18:32:31'),
(238, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 10.20, '2026-07-29', 'FBIL', NULL, '2026-07-29 18:18:55'),
(239, 6, 6, 'Milk Sales', 12, 3.00, 49.00, 147.00, 7.20, '2026-07-29', 'Brookside Dairy', NULL, '2026-07-29 18:19:13'),
(240, 6, 6, 'Milk Sales', 3, 5.00, 70.00, 350.00, 2.20, '2026-07-29', 'Kairu', NULL, '2026-07-29 18:20:11'),
(241, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 0.70, '2026-07-29', 'MARY', NULL, '2026-07-29 18:20:32'),
(242, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 9.70, '2026-07-30', 'FBIL', NULL, '2026-07-30 04:23:09'),
(243, 6, 6, 'Milk Sales', 12, 3.00, 49.00, 147.00, 6.70, '2026-07-30', 'Brookside Dairy', NULL, '2026-07-30 04:23:23'),
(244, 6, 6, 'Milk Sales', 8, 5.00, 70.00, 350.00, 1.70, '2026-07-30', 'WAHOME', NULL, '2026-07-31 04:01:23'),
(246, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 0.20, '2026-07-30', 'MARY', NULL, '2026-07-31 04:02:14'),
(247, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 7.50, '2026-07-31', 'FBIL', NULL, '2026-07-31 04:02:28'),
(248, 6, 6, 'Milk Sales', 5, 4.00, 70.00, 280.00, 3.50, '2026-07-31', 'JOAN', NULL, '2026-07-31 17:24:56'),
(249, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 2.00, '2026-07-31', 'MARY', NULL, '2026-07-31 17:25:11'),
(250, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 8.00, '2026-08-01', 'MARY', NULL, '2026-08-01 15:34:07'),
(251, 6, 6, 'Milk Sales', 7, 3.00, 70.00, 210.00, 5.00, '2026-08-01', 'CAROL', NULL, '2026-08-01 15:34:29'),
(252, 6, 6, 'Milk Sales', 4, 3.00, 70.00, 210.00, 2.00, '2026-08-01', 'NAOMI', NULL, '2026-08-01 15:36:37'),
(253, 6, 6, 'Milk Sales', 3, 4.00, 70.00, 280.00, 4.80, '2026-08-02', 'Kairu', NULL, '2026-08-02 05:03:18'),
(254, 6, 6, 'Milk Sales', 9, 2.50, 70.00, 175.00, 2.30, '2026-08-02', 'REBECCA', NULL, '2026-08-02 16:34:42'),
(255, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 0.80, '2026-08-02', 'MARY', NULL, '2026-08-02 16:34:55'),
(256, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 6.60, '2026-08-03', 'FBIL', NULL, '2026-08-03 09:20:51'),
(257, 6, 6, 'Milk Sales', 13, 3.00, 70.00, 210.00, 3.60, '2026-08-03', 'Wa Annette', NULL, '2026-08-03 19:07:08'),
(258, 6, 6, 'Milk Sales', 4, 2.00, 70.00, 140.00, 1.60, '2026-08-03', 'NAOMI', NULL, '2026-08-03 19:07:26'),
(259, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 8.00, '2026-08-04', 'FBIL', NULL, '2026-08-04 18:43:20'),
(260, 6, 6, 'Milk Sales', 13, 3.00, 70.00, 210.00, 5.00, '2026-08-04', 'Wa Annette', NULL, '2026-08-04 18:43:38'),
(261, 6, 6, 'Milk Sales', 9, 2.00, 70.00, 140.00, 3.00, '2026-08-04', 'REBECCA', NULL, '2026-08-04 18:43:53'),
(262, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 1.50, '2026-08-04', 'MARY', NULL, '2026-08-04 18:44:32'),
(263, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 9.00, '2026-08-05', 'FBIL', NULL, '2026-08-05 06:04:28'),
(264, 6, 6, 'Milk Sales', 7, 1.50, 70.00, 105.00, 7.50, '2026-08-05', 'CAROL', NULL, '2026-08-05 06:04:56'),
(265, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 8.90, '2026-08-06', 'FBIL', NULL, '2026-08-06 05:15:58'),
(266, 6, 6, 'Milk Sales', 4, 2.00, 70.00, 140.00, 6.90, '2026-08-06', 'NAOMI', NULL, '2026-08-06 05:16:08'),
(267, 6, 6, 'Milk Sales', 8, 3.50, 70.00, 245.00, 3.40, '2026-08-06', 'WAHOME', NULL, '2026-08-06 19:39:56'),
(268, 6, 6, 'Milk Sales', 13, 3.00, 70.00, 210.00, 0.40, '2026-08-06', 'Wa Annette', NULL, '2026-08-06 19:40:12'),
(269, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 9.00, '2026-08-07', 'FBIL', NULL, '2026-08-08 03:49:19'),
(270, 6, 6, 'Milk Sales', 3, 2.00, 70.00, 140.00, 7.00, '2026-08-07', 'Kairu', NULL, '2026-08-08 03:49:32'),
(273, 6, 6, 'Milk Sales', 9, 2.00, 70.00, 140.00, 5.00, '2026-08-07', 'REBECCA', NULL, '2026-08-08 03:50:30'),
(274, 6, 6, 'Milk Sales', 13, 3.00, 70.00, 210.00, 2.00, '2026-08-07', 'Wa Annette', NULL, '2026-08-08 03:51:14'),
(275, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 0.50, '2026-08-07', 'MARY', NULL, '2026-08-08 03:51:44'),
(276, 6, 6, 'Milk Sales', 5, 5.00, 70.00, 350.00, 9.40, '2026-08-08', 'JOAN', NULL, '2026-08-08 03:52:11'),
(277, 6, 6, 'Milk Sales', 7, 5.00, 70.00, 350.00, 4.40, '2026-08-08', 'CAROL', NULL, '2026-08-08 17:15:03'),
(278, 6, 6, 'Milk Sales', 3, 5.00, 70.00, 350.00, 8.60, '2026-08-09', 'Kairu', NULL, '2026-08-09 04:31:19'),
(279, 6, 6, 'Milk Sales', 4, 5.00, 70.00, 350.00, 3.60, '2026-08-09', 'NAOMI', NULL, '2026-08-10 09:12:59'),
(280, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 2.10, '2026-08-09', 'MARY', NULL, '2026-08-10 09:13:24'),
(281, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 12.10, '2026-08-10', 'FBIL', NULL, '2026-08-10 09:13:41'),
(282, 6, 6, 'Milk Sales', 13, 3.00, 70.00, 210.00, 9.10, '2026-08-10', 'Wa Annette', NULL, '2026-08-10 18:36:25'),
(283, 6, 6, 'Milk Sales', 9, 4.00, 70.00, 280.00, 5.10, '2026-08-10', 'REBECCA', NULL, '2026-08-10 18:36:37'),
(284, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 3.60, '2026-08-10', 'MARY', NULL, '2026-08-10 18:36:52'),
(285, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 12.20, '2026-08-11', 'FBIL', NULL, '2026-08-11 05:40:17'),
(286, 6, 6, 'Milk Sales', 13, 3.00, 70.00, 210.00, 9.20, '2026-08-11', 'Wa Annette', NULL, '2026-08-11 17:34:55'),
(287, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 7.70, '2026-08-11', 'MARY', NULL, '2026-08-11 17:35:14'),
(288, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 12.00, '2026-08-12', 'FBIL', NULL, '2026-08-12 18:22:20'),
(289, 6, 6, 'Milk Sales', 13, 3.00, 70.00, 210.00, 9.00, '2026-08-12', 'Wa Annette', NULL, '2026-08-12 18:22:37'),
(290, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 7.50, '2026-08-12', 'MARY', NULL, '2026-08-12 18:22:55'),
(291, 6, 6, 'Milk Sales', 3, 4.00, 70.00, 280.00, 3.50, '2026-08-12', 'Kairu', NULL, '2026-08-12 18:23:15'),
(292, 6, 6, 'Milk Sales', 3, 1.00, 70.00, 70.00, 13.00, '2026-08-13', 'Kairu', NULL, '2026-08-13 08:01:23'),
(293, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 10.00, '2026-08-13', 'FBIL', NULL, '2026-08-13 08:01:44'),
(294, 6, 6, 'Milk Sales', 5, 5.00, 70.00, 350.00, 9.50, '2026-08-15', 'JOAN', NULL, '2026-08-15 19:04:45'),
(295, 6, 6, 'Milk Sales', 7, 5.00, 70.00, 350.00, 4.50, '2026-08-15', 'CAROL', NULL, '2026-08-15 19:04:57'),
(296, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 12.10, '2026-08-14', 'FBIL', NULL, '2026-08-15 19:05:44'),
(297, 6, 6, 'Milk Sales', 8, 4.50, 70.00, 315.00, 7.60, '2026-08-14', 'WAHOME', NULL, '2026-08-15 19:07:13'),
(298, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 6.10, '2026-08-14', 'MARY', NULL, '2026-08-15 19:08:09'),
(299, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 8.50, '2026-08-13', 'MARY', NULL, '2026-08-15 19:08:54'),
(300, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 3.00, '2026-08-15', 'MARY', NULL, '2026-08-15 19:09:39'),
(301, 6, 6, 'Milk Sales', 3, 5.00, 70.00, 350.00, 9.80, '2026-08-16', 'Kairu', NULL, '2026-08-16 13:29:31'),
(302, 6, 6, 'Milk Sales', 13, 3.00, 70.00, 210.00, 5.50, '2026-08-13', 'Wa Annette', NULL, '2026-08-16 13:31:51'),
(303, 6, 6, 'Milk Sales', 13, 3.00, 70.00, 210.00, 3.10, '2026-08-14', 'Wa Annette', NULL, '2026-08-16 13:32:20'),
(305, 6, 6, 'Milk Sales', 13, 3.00, 70.00, 210.00, 4.50, '2026-08-05', 'Wa Annette', NULL, '2026-08-16 13:50:35'),
(306, 6, 6, 'Milk Sales', 4, 3.00, 70.00, 210.00, 0.10, '2026-08-14', 'NAOMI', NULL, '2026-08-16 13:56:27'),
(307, 6, 6, 'Milk Sales', 4, 1.00, 70.00, 70.00, 2.00, '2026-08-15', 'NAOMI', NULL, '2026-08-16 13:56:58'),
(308, 6, 6, 'Milk Sales', 9, 5.00, 70.00, 350.00, 4.80, '2026-08-16', 'REBECCA', NULL, '2026-08-16 17:48:22'),
(309, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 3.30, '2026-08-16', 'MARY', NULL, '2026-08-16 17:48:38'),
(310, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 10.70, '2026-08-17', 'FBIL', NULL, '2026-08-17 07:09:34'),
(311, 6, 6, 'Milk Sales', 13, 3.00, 70.00, 210.00, 7.70, '2026-08-17', 'Wa Annette', NULL, '2026-08-17 18:56:42'),
(312, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 6.20, '2026-08-17', 'MARY', NULL, '2026-08-17 18:57:03'),
(313, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 11.30, '2026-08-18', 'FBIL', NULL, '2026-08-18 04:17:46'),
(314, 6, 6, 'Milk Sales', 13, 5.00, 70.00, 350.00, 6.30, '2026-08-18', 'Wa Annette', NULL, '2026-08-18 19:07:09'),
(315, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 4.80, '2026-08-18', 'MARY', NULL, '2026-08-18 19:07:46'),
(316, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 13.80, '2026-08-19', 'FBIL', NULL, '2026-08-19 05:12:51'),
(317, 6, 6, 'Milk Sales', 4, 4.50, 70.00, 315.00, 9.30, '2026-08-19', 'NAOMI', NULL, '2026-08-19 05:13:35'),
(318, 6, 6, 'Milk Sales', 4, 0.50, 70.00, 35.00, 4.30, '2026-08-18', 'NAOMI', NULL, '2026-08-19 05:13:56'),
(319, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 7.80, '2026-08-19', 'MARY', NULL, '2026-08-19 16:16:58'),
(320, 6, 6, 'Milk Sales', 3, 2.00, 70.00, 140.00, 5.80, '2026-08-19', 'Kairu', NULL, '2026-08-19 16:17:10'),
(321, 6, 6, 'Milk Sales', 13, 5.00, 70.00, 350.00, 0.80, '2026-08-19', 'Wa Annette', NULL, '2026-08-19 16:17:30'),
(322, 6, 6, 'Milk Sales', 3, 3.00, 70.00, 210.00, 12.90, '2026-08-20', 'Kairu', NULL, '2026-08-20 04:16:42'),
(323, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 9.90, '2026-08-20', 'FBIL', NULL, '2026-08-20 04:16:52'),
(324, 6, 6, 'Milk Sales', 13, 5.00, 70.00, 350.00, 4.90, '2026-08-20', 'Wa Annette', NULL, '2026-08-20 18:48:33'),
(325, 6, 6, 'Milk Sales', 8, 2.00, 70.00, 140.00, 2.90, '2026-08-20', 'WAHOME', NULL, '2026-08-20 18:49:11'),
(326, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 1.40, '2026-08-20', 'MARY', NULL, '2026-08-20 18:49:39'),
(327, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 12.50, '2026-08-21', 'FBIL', NULL, '2026-08-21 04:12:57'),
(328, 6, 6, 'Milk Sales', 8, 3.00, 70.00, 210.00, 9.50, '2026-08-21', 'WAHOME', NULL, '2026-08-21 04:13:12'),
(329, 6, 6, 'Milk Sales', 13, 5.00, 70.00, 350.00, 4.50, '2026-08-21', 'Wa Annette', NULL, '2026-08-21 18:32:57'),
(330, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 3.00, '2026-08-21', 'MARY', NULL, '2026-08-21 18:33:21'),
(331, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 14.70, '2026-08-22', 'MARY', NULL, '2026-08-22 18:30:20'),
(333, 6, 6, 'Milk Sales', 7, 6.00, 70.00, 420.00, 8.70, '2026-08-22', 'CAROL', NULL, '2026-08-22 18:31:20'),
(334, 6, 6, 'Milk Sales', 5, 5.00, 70.00, 350.00, 3.70, '2026-08-22', 'JOAN', NULL, '2026-08-22 18:31:31'),
(335, 6, 6, 'Milk Sales', 14, 3.00, 70.00, 210.00, 13.20, '2026-08-23', 'Halima', NULL, '2026-08-23 05:56:06'),
(336, 6, 6, 'Milk Sales', 3, 4.00, 70.00, 280.00, 9.20, '2026-08-23', 'Kairu', NULL, '2026-08-23 05:56:24'),
(337, 6, 6, 'Milk Sales', 15, 1.40, 50.00, 70.00, 7.80, '2026-08-23', 'Maina', NULL, '2026-08-23 05:58:26'),
(338, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 6.30, '2026-08-23', 'MARY', NULL, '2026-08-23 18:29:34'),
(339, 6, 6, 'Milk Sales', 3, 6.00, 70.00, 420.00, 0.30, '2026-08-23', 'Kairu', NULL, '2026-08-23 18:29:54'),
(340, 6, 6, 'Milk Sales', 15, 0.29, 50.00, 14.50, 0.01, '2026-08-23', 'Maina', NULL, '2026-08-23 18:31:06'),
(341, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 14.90, '2026-08-24', 'FBIL', NULL, '2026-08-24 03:25:32'),
(342, 6, 6, 'Milk Sales', 15, 5.80, 50.00, 290.00, 9.10, '2026-08-24', 'Maina', NULL, '2026-08-24 03:26:19'),
(343, 6, 6, 'Milk Sales', 13, 5.00, 70.00, 350.00, 4.10, '2026-08-24', 'Wa Annette', NULL, '2026-08-24 18:35:39'),
(344, 6, 6, 'Milk Sales', 4, 2.00, 70.00, 140.00, 2.10, '2026-08-24', 'NAOMI', NULL, '2026-08-24 18:35:56'),
(345, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 0.60, '2026-08-24', 'MARY', NULL, '2026-08-24 18:36:19'),
(346, 6, 6, 'Milk Sales', 4, 3.00, 70.00, 210.00, 12.70, '2026-08-25', 'NAOMI', NULL, '2026-08-25 04:52:05'),
(347, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 9.70, '2026-08-25', 'FBIL', NULL, '2026-08-25 04:52:26'),
(348, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 10.60, '2026-08-26', 'FBIL', NULL, '2026-08-26 04:12:31'),
(349, 6, 6, 'Milk Sales', 3, 3.00, 70.00, 210.00, 7.60, '2026-08-26', 'Kairu', NULL, '2026-08-26 04:12:49'),
(350, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 10.50, '2026-08-27', 'FBIL', NULL, '2026-08-27 03:55:23'),
(351, 6, 6, 'Milk Sales', 3, 2.00, 70.00, 140.00, 8.50, '2026-08-27', 'Kairu', NULL, '2026-08-27 03:55:41'),
(352, 6, 6, 'Milk Sales', 13, 4.00, 70.00, 280.00, 4.50, '2026-08-27', 'Wa Annette', NULL, '2026-08-28 05:24:56'),
(353, 6, 6, 'Milk Sales', 8, 2.00, 70.00, 140.00, 2.50, '2026-08-27', 'WAHOME', NULL, '2026-08-28 05:29:00'),
(354, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 1.00, '2026-08-27', 'MARY', NULL, '2026-08-28 05:29:56'),
(355, 6, 6, 'Milk Sales', 6, 3.00, 80.00, 240.00, 11.20, '2026-08-28', 'FBIL', NULL, '2026-08-28 05:45:24'),
(356, 6, 6, 'Milk Sales', 8, 3.00, 70.00, 210.00, 8.20, '2026-08-28', 'WAHOME', NULL, '2026-08-28 05:45:49'),
(357, 6, 6, 'Milk Sales', 13, 5.00, 70.00, 350.00, 3.20, '2026-08-28', 'Wa Annette', NULL, '2026-08-28 19:19:58'),
(358, 6, 6, 'Milk Sales', 10, 1.50, 70.00, 105.00, 1.70, '2026-08-28', 'MARY', NULL, '2026-08-28 19:20:33'),
(359, 6, 6, 'Milk Sales', 7, 1.00, 70.00, 70.00, 0.70, '2026-08-28', 'CAROL', NULL, '2026-08-28 19:21:50'),
(360, 6, 6, 'Milk Sales', 9, 0.60, 70.00, 42.00, 0.10, '2026-08-28', 'REBECCA', NULL, '2026-08-28 19:22:36'),
(361, 6, 6, 'Milk Sales', 7, 2.00, 70.00, 140.00, 3.70, '2026-08-29', 'CAROL', NULL, '2026-08-29 13:51:57'),
(362, 6, 6, 'Milk Sales', 9, 2.50, 70.00, 175.00, 1.20, '2026-08-29', 'REBECCA', NULL, '2026-08-29 13:52:38'),
(363, 6, 6, 'Milk Sales', 15, 1.20, 50.00, 60.00, 0.00, '2026-08-29', 'Maina', NULL, '2026-08-29 13:52:58');

-- --------------------------------------------------------

--
-- Table structure for table `milk_production`
--

CREATE TABLE `milk_production` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `farm_id` int NOT NULL,
  `cow_id` int NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `session` enum('Morning','Afternoon','Evening') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Morning',
  `quality` enum('Excellent','Good','Average','Poor') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Good',
  `production_date` date NOT NULL,
  `morning_litres` decimal(10,2) DEFAULT '0.00',
  `evening_litres` decimal(10,2) DEFAULT '0.00',
  `total_litres` decimal(10,2) GENERATED ALWAYS AS ((`morning_litres` + `evening_litres`)) STORED,
  `litres_sold` decimal(10,2) DEFAULT '0.00',
  `customer_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nrm` decimal(10,2) GENERATED ALWAYS AS ((`total_litres` - `litres_sold`)) STORED,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `milk_production`
--

INSERT INTO `milk_production` (`id`, `user_id`, `farm_id`, `cow_id`, `quantity`, `session`, `quality`, `production_date`, `morning_litres`, `evening_litres`, `litres_sold`, `customer_name`, `notes`, `created_at`, `updated_at`) VALUES
(21, 5, 5, 6, 0.00, 'Morning', 'Good', '2026-06-07', 11.00, 0.00, 0.00, NULL, '', '2026-06-07 15:10:20', '2026-06-07 15:12:05'),
(23, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-13', 3.00, 4.20, 0.00, NULL, '', '2026-06-14 07:59:51', '2026-06-14 07:59:51'),
(24, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-14', 4.00, 3.80, 0.00, NULL, '', '2026-06-14 08:01:29', '2026-06-19 07:08:20'),
(25, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-14', 6.00, 6.00, 0.00, NULL, '', '2026-06-14 08:05:11', '2026-06-19 07:06:34'),
(26, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-13', 5.00, 6.00, 0.00, NULL, '', '2026-06-14 08:08:13', '2026-06-14 08:08:13'),
(27, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-12', 5.70, 7.00, 0.00, NULL, '', '2026-06-14 08:08:56', '2026-06-14 08:08:56'),
(28, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-11', 4.70, 7.00, 0.00, NULL, '', '2026-06-14 08:09:54', '2026-06-14 08:09:54'),
(29, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-10', 5.00, 5.50, 0.00, NULL, '', '2026-06-14 08:10:48', '2026-06-14 08:10:48'),
(30, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-09', 5.50, 6.30, 0.00, NULL, '', '2026-06-14 08:11:58', '2026-06-14 08:11:58'),
(31, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-12', 4.00, 4.00, 0.00, NULL, '', '2026-06-14 08:23:08', '2026-06-14 08:23:08'),
(32, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-11', 3.00, 4.00, 0.00, NULL, '', '2026-06-14 08:24:02', '2026-06-14 08:24:02'),
(33, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-10', 3.50, 3.70, 0.00, NULL, '', '2026-06-14 08:25:45', '2026-06-14 08:25:45'),
(34, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-09', 3.00, 3.50, 0.00, NULL, '', '2026-06-14 08:28:26', '2026-06-14 08:28:26'),
(35, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-01', 4.00, 3.50, 0.00, NULL, '', '2026-06-14 08:51:49', '2026-06-14 08:51:49'),
(36, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-02', 3.60, 4.30, 0.00, NULL, '', '2026-06-14 08:52:24', '2026-06-14 08:52:24'),
(37, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-03', 3.70, 3.70, 0.00, NULL, '', '2026-06-14 08:53:24', '2026-06-14 08:53:24'),
(38, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-04', 3.60, 4.00, 0.00, NULL, '', '2026-06-14 08:54:31', '2026-06-14 08:54:31'),
(39, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-05', 3.10, 3.70, 0.00, NULL, '', '2026-06-14 08:55:46', '2026-06-14 08:55:46'),
(40, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-06', 3.50, 3.50, 0.00, NULL, '', '2026-06-14 08:56:49', '2026-06-14 08:56:49'),
(41, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-07', 3.50, 3.70, 0.00, NULL, '', '2026-06-14 08:58:10', '2026-06-14 08:58:10'),
(42, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-08', 3.60, 4.00, 0.00, NULL, '', '2026-06-14 08:58:34', '2026-06-14 08:58:34'),
(43, 5, 5, 6, 0.00, 'Morning', 'Good', '2026-06-17', 5.00, 7.00, 0.00, NULL, '', '2026-06-17 14:08:41', '2026-06-17 14:08:41'),
(44, 5, 5, 6, 0.00, 'Morning', 'Good', '2026-06-18', 10.00, 0.00, 0.00, NULL, '', '2026-06-18 03:56:05', '2026-06-18 03:56:05'),
(45, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-15', 3.60, 4.00, 0.00, NULL, '', '2026-06-18 10:08:15', '2026-06-18 10:08:15'),
(46, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-16', 3.20, 3.80, 0.00, NULL, '', '2026-06-18 10:08:54', '2026-06-18 10:08:54'),
(47, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-17', 3.30, 3.60, 0.00, NULL, '', '2026-06-18 10:09:30', '2026-06-18 10:09:30'),
(48, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-18', 3.40, 3.20, 0.00, NULL, '', '2026-06-18 10:09:47', '2026-06-19 05:57:49'),
(49, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-15', 6.00, 6.00, 0.00, NULL, '', '2026-06-18 10:11:02', '2026-06-18 10:11:02'),
(50, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-16', 5.40, 6.00, 0.00, NULL, '', '2026-06-18 10:11:54', '2026-06-18 10:11:54'),
(51, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-17', 5.00, 5.00, 0.00, NULL, '', '2026-06-18 10:12:29', '2026-06-18 10:12:29'),
(52, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-18', 4.50, 5.20, 0.00, NULL, '', '2026-06-18 10:12:54', '2026-06-19 05:59:27'),
(53, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-19', 5.00, 5.00, 0.00, NULL, '', '2026-06-19 04:11:41', '2026-06-20 06:49:15'),
(54, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-19', 3.10, 3.20, 0.00, NULL, '', '2026-06-19 04:11:59', '2026-06-20 06:48:41'),
(55, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-20', 4.50, 4.00, 0.00, NULL, '', '2026-06-20 06:48:05', '2026-06-20 16:18:39'),
(56, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-20', 3.00, 2.80, 0.00, NULL, '', '2026-06-20 06:48:17', '2026-06-20 16:18:54'),
(57, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-21', 4.90, 5.70, 0.00, NULL, '', '2026-06-21 04:08:41', '2026-06-21 18:50:49'),
(58, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-21', 3.20, 3.50, 0.00, NULL, '', '2026-06-21 04:08:56', '2026-06-21 18:51:28'),
(59, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-22', 3.10, 5.00, 0.00, NULL, '', '2026-06-22 04:58:43', '2026-06-22 16:28:04'),
(60, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-22', 2.10, 3.10, 0.00, NULL, '', '2026-06-22 04:58:59', '2026-06-22 16:27:51'),
(61, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-23', 4.60, 5.00, 0.00, NULL, '', '2026-06-23 03:59:57', '2026-06-23 16:16:53'),
(62, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-23', 3.00, 3.10, 0.00, NULL, '', '2026-06-23 04:01:33', '2026-06-23 16:16:42'),
(63, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-24', 5.00, 5.00, 0.00, NULL, '', '2026-06-24 05:45:48', '2026-06-24 18:22:30'),
(64, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-24', 3.10, 3.00, 0.00, NULL, '', '2026-06-24 05:46:00', '2026-06-24 18:22:42'),
(65, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-25', 5.60, 4.60, 0.00, NULL, '', '2026-06-25 04:06:33', '2026-06-25 18:36:55'),
(66, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-25', 3.30, 3.10, 0.00, NULL, '', '2026-06-25 04:06:56', '2026-06-25 18:37:06'),
(67, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-26', 5.00, 4.80, 0.00, NULL, '', '2026-06-26 05:53:43', '2026-06-26 16:31:00'),
(68, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-26', 3.10, 3.10, 0.00, NULL, '', '2026-06-26 05:53:52', '2026-06-26 16:30:50'),
(69, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-27', 5.00, 5.00, 0.00, NULL, '', '2026-06-27 16:39:23', '2026-06-27 16:42:07'),
(70, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-27', 3.00, 3.00, 0.00, NULL, '', '2026-06-27 16:39:32', '2026-06-27 16:39:57'),
(71, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-28', 4.50, 4.80, 0.00, NULL, '', '2026-06-28 05:42:32', '2026-06-28 16:49:34'),
(72, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-28', 2.80, 3.00, 0.00, NULL, '', '2026-06-28 05:42:46', '2026-06-28 16:49:24'),
(73, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-29', 3.60, 3.70, 0.00, NULL, '', '2026-06-29 05:45:06', '2026-06-29 15:51:20'),
(74, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-29', 2.50, 2.80, 0.00, NULL, '', '2026-06-29 05:45:16', '2026-06-29 15:54:14'),
(75, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-06-30', 3.80, 4.40, 0.00, NULL, '', '2026-06-30 06:06:18', '2026-06-30 16:06:25'),
(76, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-06-30', 2.60, 2.80, 0.00, NULL, '', '2026-06-30 06:06:31', '2026-06-30 16:08:38'),
(77, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-01', 4.50, 4.70, 0.00, NULL, '', '2026-07-01 03:41:34', '2026-07-01 15:55:28'),
(78, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-01', 2.70, 2.60, 0.00, NULL, '', '2026-07-01 03:41:48', '2026-07-01 18:58:05'),
(79, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-02', 4.30, 4.00, 0.00, NULL, '', '2026-07-02 04:52:13', '2026-07-02 16:33:52'),
(80, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-02', 2.50, 2.30, 0.00, NULL, '', '2026-07-02 04:52:34', '2026-07-02 16:33:42'),
(81, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-03', 3.90, 4.60, 0.00, NULL, '', '2026-07-03 04:53:02', '2026-07-03 16:25:14'),
(82, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-03', 2.40, 2.80, 0.00, NULL, '', '2026-07-03 04:53:25', '2026-07-03 16:25:29'),
(83, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-04', 4.30, 5.30, 0.00, NULL, '', '2026-07-04 03:50:54', '2026-07-04 16:57:16'),
(84, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-04', 2.60, 3.20, 0.00, NULL, '', '2026-07-04 03:51:08', '2026-07-04 16:50:51'),
(85, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-05', 2.80, 3.10, 0.00, NULL, '', '2026-07-05 05:23:00', '2026-07-05 16:12:40'),
(86, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-05', 4.90, 5.00, 0.00, NULL, '', '2026-07-05 05:23:09', '2026-07-05 16:26:26'),
(87, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-06', 3.30, 3.20, 0.00, NULL, '', '2026-07-06 03:50:29', '2026-07-06 15:50:07'),
(88, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-06', 5.00, 4.80, 0.00, NULL, '', '2026-07-06 03:53:21', '2026-07-06 15:56:40'),
(89, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-07', 4.90, 5.00, 0.00, NULL, '', '2026-07-07 04:51:00', '2026-07-07 16:24:10'),
(90, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-07', 3.20, 3.20, 0.00, NULL, '', '2026-07-07 04:51:12', '2026-07-07 16:20:31'),
(91, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-08', 3.00, 3.20, 0.00, NULL, '', '2026-07-08 04:24:36', '2026-07-08 16:27:20'),
(92, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-08', 4.50, 5.00, 0.00, NULL, '', '2026-07-08 04:24:56', '2026-07-08 16:31:31'),
(93, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-09', 4.40, 5.00, 0.00, NULL, '', '2026-07-09 03:55:17', '2026-07-09 16:42:27'),
(94, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-09', 3.00, 3.30, 0.00, NULL, '', '2026-07-09 03:59:07', '2026-07-09 16:37:08'),
(95, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-10', 3.10, 3.20, 0.00, NULL, '', '2026-07-10 04:08:17', '2026-07-10 16:22:28'),
(96, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-10', 4.60, 5.00, 0.00, NULL, '', '2026-07-10 04:11:41', '2026-07-10 16:25:30'),
(97, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-11', 5.00, 6.20, 0.00, NULL, '', '2026-07-11 03:27:15', '2026-07-11 17:48:42'),
(98, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-11', 3.30, 3.80, 0.00, NULL, '', '2026-07-11 03:29:39', '2026-07-11 17:44:52'),
(99, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-12', 2.80, 3.80, 0.00, NULL, '', '2026-07-12 03:42:30', '2026-07-12 16:01:47'),
(100, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-12', 4.90, 5.80, 0.00, NULL, '', '2026-07-12 03:47:24', '2026-07-12 16:07:04'),
(101, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-13', 3.20, 3.70, 0.00, NULL, '', '2026-07-13 03:35:29', '2026-07-13 16:10:55'),
(102, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-13', 5.50, 5.90, 0.00, NULL, '', '2026-07-13 03:41:59', '2026-07-13 16:17:08'),
(103, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-14', 3.20, 3.70, 0.00, NULL, '', '2026-07-14 04:29:17', '2026-07-14 17:00:03'),
(104, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-14', 5.10, 5.60, 0.00, NULL, '', '2026-07-14 04:32:43', '2026-07-14 17:00:21'),
(105, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-15', 3.00, 3.60, 0.00, NULL, '', '2026-07-15 03:59:03', '2026-07-15 16:17:45'),
(106, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-15', 4.50, 5.40, 0.00, NULL, '', '2026-07-15 03:59:18', '2026-07-15 16:21:35'),
(107, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-16', 3.30, 3.40, 0.00, NULL, '', '2026-07-16 03:16:18', '2026-07-16 16:09:42'),
(108, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-16', 4.70, 5.00, 0.00, NULL, '', '2026-07-16 03:19:29', '2026-07-16 16:06:14'),
(109, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-17', 3.30, 3.10, 0.00, NULL, '', '2026-07-17 03:22:28', '2026-07-17 16:24:34'),
(110, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-17', 4.00, 3.50, 0.00, NULL, '', '2026-07-17 03:26:13', '2026-07-17 16:28:48'),
(111, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-18', 3.30, 4.00, 0.00, NULL, '', '2026-07-18 03:04:59', '2026-07-18 16:39:31'),
(112, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-18', 3.00, 3.50, 0.00, NULL, '', '2026-07-18 03:05:07', '2026-07-18 16:47:55'),
(113, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-19', 3.20, 4.40, 0.00, NULL, '', '2026-07-19 03:33:33', '2026-07-19 16:39:34'),
(114, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-19', 2.80, 3.60, 0.00, NULL, '', '2026-07-19 03:35:37', '2026-07-19 16:39:23'),
(115, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-20', 3.70, 4.30, 0.00, NULL, '', '2026-07-20 03:05:57', '2026-07-20 17:02:47'),
(116, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-20', 3.00, 3.40, 0.00, NULL, '', '2026-07-20 03:06:08', '2026-07-20 16:59:59'),
(117, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-21', 3.10, 4.40, 0.00, NULL, '', '2026-07-21 03:29:14', '2026-07-21 16:42:27'),
(118, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-21', 2.50, 3.20, 0.00, NULL, '', '2026-07-21 03:29:33', '2026-07-21 16:39:11'),
(119, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-22', 4.00, 4.50, 0.00, NULL, '', '2026-07-22 03:22:17', '2026-07-22 16:28:23'),
(120, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-22', 2.90, 3.20, 0.00, NULL, '', '2026-07-22 03:22:28', '2026-07-22 16:24:57'),
(121, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-23', 3.70, 5.00, 0.00, NULL, '', '2026-07-23 03:25:40', '2026-07-23 17:06:14'),
(122, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-23', 2.70, 3.60, 0.00, NULL, '', '2026-07-23 03:25:48', '2026-07-23 17:02:58'),
(123, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-24', 2.80, 3.70, 0.00, NULL, '', '2026-07-24 03:31:51', '2026-07-24 16:59:42'),
(124, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-24', 3.50, 5.00, 0.00, NULL, '', '2026-07-24 03:33:43', '2026-07-24 17:04:34'),
(125, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-25', 2.90, 3.40, 0.00, NULL, '', '2026-07-25 03:18:28', '2026-07-25 16:29:59'),
(126, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-25', 3.50, 4.40, 0.00, NULL, '', '2026-07-25 03:20:15', '2026-07-25 16:29:50'),
(127, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-26', 3.80, 4.40, 0.00, NULL, '', '2026-07-26 04:32:24', '2026-07-26 16:09:10'),
(128, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-26', 3.20, 3.20, 0.00, NULL, '', '2026-07-26 04:32:32', '2026-07-26 16:09:00'),
(129, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-27', 3.50, 3.50, 0.00, NULL, '', '2026-07-27 03:49:15', '2026-07-27 16:38:17'),
(130, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-27', 3.30, 4.00, 0.00, NULL, '', '2026-07-27 03:49:32', '2026-07-27 16:38:07'),
(131, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-28', 3.00, 3.90, 0.00, NULL, '', '2026-07-28 03:18:15', '2026-07-28 18:31:55'),
(132, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-28', 3.00, 3.50, 0.00, NULL, '', '2026-07-28 03:18:23', '2026-07-28 18:31:44'),
(135, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-29', 3.00, 4.00, 0.00, NULL, '', '2026-07-29 18:17:18', '2026-07-29 18:18:25'),
(136, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-29', 3.00, 3.20, 0.00, NULL, '', '2026-07-29 18:17:36', '2026-07-29 18:18:12'),
(137, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-30', 3.20, 3.50, 0.00, NULL, '', '2026-07-30 03:31:13', '2026-07-31 04:00:33'),
(138, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-30', 2.80, 3.20, 0.00, NULL, '', '2026-07-30 03:31:27', '2026-07-31 03:59:41'),
(139, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-07-31', 2.50, 2.90, 0.00, NULL, '', '2026-07-31 03:58:04', '2026-07-31 16:13:43'),
(140, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-07-31', 2.50, 2.60, 0.00, NULL, '', '2026-07-31 03:58:15', '2026-07-31 16:13:29'),
(141, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-01', 2.50, 2.00, 0.00, NULL, '', '2026-08-01 15:31:13', '2026-08-01 15:36:15'),
(143, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-01', 2.50, 2.50, 0.00, NULL, '', '2026-08-01 15:35:38', '2026-08-01 15:36:07'),
(144, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-02', 2.50, 2.10, 0.00, NULL, '', '2026-08-02 05:01:36', '2026-08-02 16:34:23'),
(145, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-02', 2.30, 1.90, 0.00, NULL, '', '2026-08-02 05:02:56', '2026-08-02 16:34:13'),
(146, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-03', 2.50, 2.50, 0.00, NULL, '', '2026-08-03 09:20:17', '2026-08-03 17:13:50'),
(147, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-03', 2.10, 2.50, 0.00, NULL, '', '2026-08-03 09:20:34', '2026-08-03 17:13:41'),
(148, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-04', 2.50, 3.50, 0.00, NULL, '', '2026-08-04 03:28:49', '2026-08-04 17:01:00'),
(149, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-04', 2.00, 3.00, 0.00, NULL, '', '2026-08-04 03:29:05', '2026-08-04 17:00:44'),
(150, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-05', 2.50, 3.70, 0.00, NULL, '', '2026-08-05 02:52:11', '2026-08-05 16:47:59'),
(151, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-05', 2.50, 3.30, 0.00, NULL, '', '2026-08-05 02:52:21', '2026-08-05 16:48:34'),
(152, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-06', 2.70, 3.50, 0.00, NULL, '', '2026-08-06 05:14:07', '2026-08-06 16:42:59'),
(153, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-06', 2.50, 3.20, 0.00, NULL, '', '2026-08-06 05:15:18', '2026-08-06 16:42:46'),
(154, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-08', 3.30, 4.30, 0.00, NULL, '', '2026-08-08 03:43:52', '2026-08-08 17:13:29'),
(155, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-08', 3.50, 3.30, 0.00, NULL, '', '2026-08-08 03:44:07', '2026-08-08 17:09:34'),
(156, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-07', 3.00, 4.00, 0.00, NULL, '', '2026-08-08 03:47:10', '2026-08-08 03:47:10'),
(157, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-07', 2.50, 2.50, 0.00, NULL, '', '2026-08-08 03:48:45', '2026-08-08 03:48:45'),
(158, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-09', 3.40, 4.00, 0.00, NULL, '', '2026-08-09 04:19:44', '2026-08-10 09:12:02'),
(159, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-09', 2.70, 3.50, 0.00, NULL, '', '2026-08-09 04:19:58', '2026-08-10 09:12:16'),
(160, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-10', 3.60, 4.70, 0.00, NULL, '', '2026-08-10 09:10:42', '2026-08-10 18:33:45'),
(161, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-10', 3.10, 3.70, 0.00, NULL, '', '2026-08-10 09:10:51', '2026-08-10 18:33:58'),
(162, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-11', 3.80, 4.70, 0.00, NULL, '', '2026-08-11 05:39:46', '2026-08-11 17:34:18'),
(163, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-11', 3.00, 3.70, 0.00, NULL, '', '2026-08-11 05:40:03', '2026-08-11 17:34:29'),
(164, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-12', 3.50, 4.80, 0.00, NULL, '', '2026-08-12 17:53:30', '2026-08-12 18:21:44'),
(165, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-12', 2.90, 3.80, 0.00, NULL, '', '2026-08-12 17:53:42', '2026-08-12 18:21:56'),
(166, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-13', 4.00, 4.00, 0.00, NULL, '', '2026-08-13 08:00:34', '2026-08-15 18:48:46'),
(167, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-13', 3.00, 3.00, 0.00, NULL, '', '2026-08-13 08:00:43', '2026-08-15 18:48:29'),
(168, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-14', 3.40, 4.50, 0.00, NULL, '', '2026-08-14 08:05:36', '2026-08-14 16:25:57'),
(169, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-14', 3.20, 4.00, 0.00, NULL, '', '2026-08-14 08:05:58', '2026-08-14 16:26:06'),
(170, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-15', 3.70, 3.60, 0.00, NULL, '', '2026-08-15 05:05:22', '2026-08-15 16:03:24'),
(171, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-15', 3.60, 3.60, 0.00, NULL, '', '2026-08-15 05:06:55', '2026-08-15 16:03:14'),
(172, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-16', 3.70, 3.30, 0.00, NULL, '', '2026-08-16 13:28:24', '2026-08-16 15:37:44'),
(173, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-16', 4.00, 3.80, 0.00, NULL, '', '2026-08-16 13:28:49', '2026-08-16 15:37:32'),
(174, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-17', 3.20, 3.20, 0.00, NULL, '', '2026-08-17 03:29:09', '2026-08-17 17:03:18'),
(175, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-17', 3.60, 3.70, 0.00, NULL, '', '2026-08-17 03:29:20', '2026-08-17 17:04:05'),
(176, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-18', 3.00, 4.20, 0.00, NULL, '', '2026-08-18 04:00:25', '2026-08-18 16:41:18'),
(177, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-18', 3.00, 4.10, 0.00, NULL, '', '2026-08-18 04:00:34', '2026-08-18 16:46:02'),
(178, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-19', 3.80, 4.80, 0.00, NULL, '', '2026-08-19 04:29:04', '2026-08-19 16:16:10'),
(179, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-19', 3.80, 4.40, 0.00, NULL, '', '2026-08-19 04:29:20', '2026-08-19 16:16:01'),
(180, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-20', 3.80, 4.20, 0.00, NULL, '', '2026-08-20 03:37:09', '2026-08-20 16:41:35'),
(181, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-20', 3.60, 4.30, 0.00, NULL, '', '2026-08-20 03:37:23', '2026-08-20 16:45:28'),
(182, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-21', 3.50, 4.60, 0.00, NULL, '', '2026-08-21 04:12:33', '2026-08-21 18:32:23'),
(183, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-21', 3.00, 4.40, 0.00, NULL, '', '2026-08-21 04:12:43', '2026-08-21 18:32:10'),
(184, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-22', 3.40, 4.80, 0.00, NULL, '', '2026-08-22 03:19:39', '2026-08-22 16:17:13'),
(185, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-22', 3.60, 4.40, 0.00, NULL, '', '2026-08-22 03:19:48', '2026-08-22 16:08:13'),
(186, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-23', 4.40, 4.10, 0.00, NULL, '', '2026-08-23 05:54:23', '2026-08-23 15:18:15'),
(187, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-23', 4.00, 3.70, 0.00, NULL, '', '2026-08-23 05:54:35', '2026-08-23 15:17:58'),
(188, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-24', 4.80, 5.00, 0.00, NULL, '', '2026-08-24 03:24:37', '2026-08-24 16:43:34'),
(189, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-24', 4.00, 4.10, 0.00, NULL, '', '2026-08-24 03:24:54', '2026-08-24 16:43:44'),
(190, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-25', 3.50, 4.60, 0.00, NULL, '', '2026-08-25 04:51:32', '2026-08-25 17:09:32'),
(191, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-25', 3.50, 4.10, 0.00, NULL, '', '2026-08-25 04:51:45', '2026-08-25 17:10:20'),
(192, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-26', 3.00, 3.60, 0.00, NULL, '', '2026-08-26 04:10:54', '2026-08-26 16:39:27'),
(193, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-26', 3.00, 4.00, 0.00, NULL, '', '2026-08-26 04:11:03', '2026-08-26 16:55:25'),
(194, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-27', 2.90, 3.80, 0.00, NULL, '', '2026-08-27 03:09:43', '2026-08-27 19:26:41'),
(195, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-27', 3.00, 3.80, 0.00, NULL, '', '2026-08-27 03:10:00', '2026-08-27 19:26:19'),
(196, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-28', 3.00, 4.20, 0.00, NULL, '', '2026-08-28 05:21:51', '2026-08-28 17:25:31'),
(197, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-28', 3.00, 4.00, 0.00, NULL, '', '2026-08-28 05:23:16', '2026-08-28 17:30:02'),
(198, 6, 6, 5, 0.00, 'Morning', 'Good', '2026-08-29', 2.90, 0.00, 0.00, NULL, '', '2026-08-29 03:28:02', '2026-08-29 03:28:02'),
(199, 6, 6, 7, 0.00, 'Morning', 'Good', '2026-08-29', 2.80, 0.00, 0.00, NULL, '', '2026-08-29 03:28:12', '2026-08-29 03:28:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `phone`, `created_at`) VALUES
(5, 'karenju', 'karenjuduncan750@gmail.com', '$2y$10$QdaiGd8cR3uBPgnC59YjC.2KauNSnecUN9VhvBtEyzaf2mPRwMPZe', '0112554479', '2026-06-07 13:23:53'),
(6, 'Maish', 'mainapetermwangi2017@gmail.com', '$2y$10$5ar3Thm1f2.RTHka2netoOsai2yx5q3pxES/3PBwob9Z6c6x.aJk6', '0707454717', '2026-06-07 14:53:04'),
(7, 'Rhymes', 'dokeyo390@gmail.com', '$2y$10$C3tkvID.5cwqR10XsbpuyOocF0GKtIIvmyFZPgwmrjjKe7OMJM.mC', '', '2026-06-13 09:44:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_collections_user` (`user_id`),
  ADD KEY `idx_collections_customer` (`customer_id`),
  ADD KEY `idx_collections_date` (`payment_date`);

--
-- Indexes for table `cows`
--
ALTER TABLE `cows`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ear_tag` (`ear_tag`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `farm_id` (`farm_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_customer` (`user_id`,`customer_name`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `farm_id` (`farm_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `farm_id` (`farm_id`),
  ADD KEY `expense_date` (`expense_date`);

--
-- Indexes for table `farms`
--
ALTER TABLE `farms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_number` (`registration_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `farm_settings`
--
ALTER TABLE `farm_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_key` (`user_id`,`setting_key`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `feed_management`
--
ALTER TABLE `feed_management`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `health_records`
--
ALTER TABLE `health_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cow_id` (`cow_id`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `farm_id` (`farm_id`),
  ADD KEY `cow_id` (`cow_id`),
  ADD KEY `income_customer_id` (`customer_id`);

--
-- Indexes for table `milk_production`
--
ALTER TABLE `milk_production`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `collections`
--
ALTER TABLE `collections`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `cows`
--
ALTER TABLE `cows`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `farms`
--
ALTER TABLE `farms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `farm_settings`
--
ALTER TABLE `farm_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `feed_management`
--
ALTER TABLE `feed_management`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `health_records`
--
ALTER TABLE `health_records`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=364;

--
-- AUTO_INCREMENT for table `milk_production`
--
ALTER TABLE `milk_production`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cows`
--
ALTER TABLE `cows`
  ADD CONSTRAINT `cows_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cows_ibfk_2` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customers_ibfk_2` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `farms`
--
ALTER TABLE `farms`
  ADD CONSTRAINT `farms_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `health_records`
--
ALTER TABLE `health_records`
  ADD CONSTRAINT `health_records_ibfk_1` FOREIGN KEY (`cow_id`) REFERENCES `cows` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `income_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `income_ibfk_2` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`),
  ADD CONSTRAINT `income_ibfk_3` FOREIGN KEY (`cow_id`) REFERENCES `cows` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `income_ibfk_4` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
