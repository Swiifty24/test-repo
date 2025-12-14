-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2025 at 11:02 AM
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
-- Database: `gym`
--

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('male','female','Others') NOT NULL,
  `email` varchar(50) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `valid_id_picture` varchar(255) DEFAULT NULL,
  `phone_no` varchar(55) NOT NULL,
  `password` varchar(15) NOT NULL,
  `role` enum('admin','member','trainer') DEFAULT 'member',
  `created_at` date DEFAULT NULL,
  `status` enum('active','inactive','pending_approval','rejected') NOT NULL DEFAULT 'pending_approval'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`user_id`, `first_name`, `last_name`, `middle_name`, `date_of_birth`, `gender`, `email`, `profile_picture`, `valid_id_picture`, `phone_no`, `password`, `role`, `created_at`, `status`) VALUES
(1, 'Jhon Clein', 'Pagarogan', '', '2005-10-30', 'male', 'pagaroganjhonclein@gmail.com', 'public/uploads/profile_images/user_1_1765698211.png', NULL, '09366600119', 'test1234', 'member', '2025-10-01', 'inactive'),
(4, 'John', 'Smith', 'Toribio', '2021-10-13', 'male', 'test02@gmail.com', NULL, NULL, '09366600119', 'test1234', 'member', '2025-10-05', 'inactive'),
(7, 'Roboute', 'Guilliman', 'Smith', '2015-10-04', 'male', 'admin@gmail.com', NULL, NULL, '09366600119', 'admin123', 'admin', '2025-10-09', 'active'),
(8, 'Jane', 'Doe', 'S', '2025-10-01', 'female', 'test03@gmail.com', NULL, NULL, '', 'test1234', 'member', '2025-10-21', 'inactive'),
(17, 'Jamsik', 'Doe', '', '2010-07-21', 'male', 'test04@gmail.com', NULL, NULL, '', '', 'member', '2025-10-26', 'active'),
(18, 'Kevin', 'Herodias', 'Ramos', '2011-10-30', 'male', 'trainer@gmail.com', NULL, NULL, '09123456789', 'test1234', 'trainer', '2025-10-27', 'active'),
(19, 'John', 'Doe', 'S.', '2005-10-30', 'male', 'demo@gmail.com', NULL, NULL, '', 'test1234', 'member', '2025-11-03', 'active'),
(20, 'Shane', 'Doe', 'S', '2005-10-30', 'female', 'test05@gmail.com', NULL, NULL, '', 'test1234', 'member', '2025-11-16', 'active'),
(21, 'Gray', 'Lockser', 'G', '2004-02-04', 'male', 'ae202401182@wmsu.edu.ph', NULL, NULL, '', 'test1234', 'member', '2025-11-18', 'active'),
(22, 'max', 'verstappen', 'd', '2003-02-02', 'male', 'test06@gmail.com', NULL, NULL, '', 'test1234', 'member', '2025-12-13', 'active'),
(23, 'Miko', 'Mareng', '', '2004-05-23', 'male', 'mikoramos@gmail.com', NULL, NULL, '0969696969', '12345678', 'member', '2025-12-14', 'active'),
(28, 'razel', 'Herodias', '', '2004-05-03', 'male', 'test07@gmail.com', NULL, 'public/uploads/valid_ids/id_1765701563_693e77bb57ace.png', '09366600119', 'test1234', 'member', '2025-12-14', 'pending_approval');

-- --------------------------------------------------------

--
-- Table structure for table `membership_freeze_history`
--

CREATE TABLE `membership_freeze_history` (
  `freeze_id` int(11) NOT NULL,
  `subscription_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `freeze_start` date NOT NULL,
  `freeze_end` date NOT NULL,
  `reason` text DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `membership_plans`
--

CREATE TABLE `membership_plans` (
  `plan_id` int(11) NOT NULL,
  `plan_name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `duration_months` int(11) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `status` enum('active','inactive','removed','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membership_plans`
--

INSERT INTO `membership_plans` (`plan_id`, `plan_name`, `description`, `duration_months`, `price`, `status`) VALUES
(1, 'Basic Plan', 'Get access to gym equipment, locker room and with WIFI Access!', 1, 255.00, 'active'),
(2, 'Premium Plan', 'All basic features plus a personal trainer for 2x a week (with Nutrition Consultation)', 1, 599.00, 'active'),
(3, 'Elite Plan', 'All Premium features with unlimited personal training with Spa & Sauna Access!', 1, 899.00, 'active'),
(4, 'Giga plan', 'Get all elite plan access plus an unli subscription to our gym products', 1, 2899.00, 'inactive'),
(6, 'Mr.Oympia Plan', 'get trained by hardcore body builders to achieve a greek like physique', 2, 1599.00, 'inactive'),
(21, 'Xmas Special', 'Christmas Special Elite plan!', 1, 599.00, 'inactive'),
(22, 'Test Plan', 'Test Description', 1, 500.00, 'removed');

-- --------------------------------------------------------

--
-- Table structure for table `member_address`
--

CREATE TABLE `member_address` (
  `zip` int(10) NOT NULL,
  `street_address` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member_address`
--

INSERT INTO `member_address` (`zip`, `street_address`, `city`) VALUES
(3000, '19 Malvar Road', 'Baguio City'),
(4028, 'J-Block, San Pedro', 'Laguna'),
(4029, '5th St. Zone 2', 'Cavite'),
(5000, '78 Lopez Jaena St', 'Iloilo City'),
(6000, 'A1-B Road Mandaue', 'Cebu City'),
(6002, '30 Rizal Avenue', 'Lapu-Lapu City'),
(7000, 'Sunflower Street', 'Zamboanga City'),
(8000, '45 Aguinaldo St', 'Davao City'),
(9000, '123 street', 'Cagayan de Oro City'),
(9001, 'P12 Barangay Carmen', 'Cagayan de Oro City');

-- --------------------------------------------------------

--
-- Table structure for table `member_address_link`
--

CREATE TABLE `member_address_link` (
  `user_id` int(11) NOT NULL,
  `zip` int(11) NOT NULL,
  `address_type` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member_address_link`
--

INSERT INTO `member_address_link` (`user_id`, `zip`, `address_type`) VALUES
(1, 9000, 'Home'),
(4, 9001, 'Home'),
(7, 8000, 'Home'),
(8, 7000, 'Home'),
(17, 6000, 'Home'),
(18, 6002, 'Home'),
(19, 5000, 'Home'),
(20, 4028, 'Home'),
(21, 4029, 'Home'),
(22, 7000, 'Home'),
(23, 7000, 'Home'),
(28, 9000, 'Home');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `category` enum('membership','payment','schedule','general','booking','trainer') DEFAULT 'general',
  `is_read` tinyint(1) DEFAULT 0,
  `link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `title`, `message`, `type`, `category`, `is_read`, `link`, `created_at`, `read_at`) VALUES
(1, 1, 'Welcome to Gymazing', 'Welcome to gymazing!!', 'info', 'membership', 1, NULL, '2025-11-16 08:27:05', '2025-11-16 09:10:54'),
(2, 1, 'Plan Expiration', 'Your Current Plan has Expired', 'warning', 'membership', 1, NULL, '2025-11-16 14:32:05', '2025-11-16 14:32:13'),
(3, 1, 'Plan Expiration', 'Your Current Plan has Expired', 'warning', 'membership', 1, NULL, '2025-11-16 14:32:41', '2025-11-16 14:32:50'),
(4, 1, 'Membership Expired', 'Your membership has expired. Please renew to continue using the gym.', 'error', 'membership', 1, 'index.php?controller=member&action=renewMembership', '2025-11-16 15:09:14', '2025-11-16 15:09:20'),
(5, 1, 'Plan Expiration', 'Your Current Plan has Expired', 'warning', 'membership', 1, NULL, '2025-11-16 15:09:14', '2025-11-16 15:09:28'),
(6, 1, 'Membership Expired', 'Your membership has expired. Please renew to continue using the gym.', 'error', 'membership', 1, 'index.php?controller=member&action=renewMembership', '2025-11-16 15:09:24', '2025-11-16 15:09:28'),
(7, 1, 'Plan Expiration', 'Your Current Plan has Expired', 'warning', 'membership', 1, NULL, '2025-11-16 15:09:24', '2025-11-16 15:09:28'),
(8, 1, 'Membership Expired', 'Your membership has expired. Please renew to continue using the gym.', 'error', 'membership', 1, 'index.php?controller=member&action=renewMembership', '2025-11-16 15:14:28', '2025-11-16 15:14:31'),
(9, 1, 'Plan Expiration', 'Your Current Plan has Expired', 'warning', 'membership', 1, NULL, '2025-11-16 15:14:28', '2025-11-16 15:14:35'),
(10, 1, 'Membership Expired', 'Your membership has expired. Please renew to continue using the gym.', 'error', 'membership', 1, 'index.php?controller=member&action=renewMembership', '2025-11-16 15:14:32', '2025-11-16 15:14:35'),
(11, 1, 'Plan Expiration', 'Your Current Plan has Expired', 'warning', 'membership', 1, NULL, '2025-11-16 15:14:33', '2025-11-16 15:14:35'),
(12, 7, 'Test Admin Notification', 'This is a test notification for the admin!', 'success', 'general', 1, NULL, '2025-11-17 14:23:46', '2025-11-17 14:33:56'),
(13, 19, 'Payment Received', 'We have received your payment of ₱899.00. Thank you!', 'success', 'payment', 0, 'index.php?controller=member&action=paymentHistory', '2025-11-17 14:50:01', NULL),
(14, 7, 'Payment Received', 'John Doe has made a payment of ₱899.00.', 'success', 'payment', 1, 'index.php?controller=admin&action=payments', '2025-11-17 14:50:01', '2025-11-17 16:39:32'),
(15, 1, 'Payment Received', 'We have received your payment of ₱. Thank you!', 'success', 'payment', 1, 'index.php?controller=member&action=paymentHistory', '2025-11-17 15:59:07', '2025-11-17 16:29:31'),
(16, 7, 'Payment Received', 'Jhon Clein Pagarogan has made a payment of ₱.', 'success', 'payment', 1, 'index.php?controller=admin&action=payments', '2025-11-17 15:59:07', '2025-11-17 16:39:32'),
(17, 1, 'Payment Received', 'We have received your payment of ₱. Thank you!', 'success', 'payment', 1, 'index.php?controller=member&action=paymentHistory', '2025-11-17 15:59:14', '2025-11-17 16:29:31'),
(18, 7, 'Payment Received', 'Jhon Clein Pagarogan has made a payment of ₱.', 'success', 'payment', 1, 'index.php?controller=admin&action=payments', '2025-11-17 15:59:14', '2025-11-17 16:39:32'),
(19, 1, 'Payment Received', 'We have received your payment of ₱899.00. Thank you!', 'success', 'payment', 1, 'index.php?controller=member&action=paymentHistory', '2025-11-17 16:02:00', '2025-11-17 16:29:31'),
(20, 7, 'Payment Received', 'Jhon Clein Pagarogan has made a payment of ₱899.00.', 'success', 'payment', 1, 'index.php?controller=admin&action=payments', '2025-11-17 16:02:00', '2025-11-17 16:39:32'),
(21, 1, 'Payment Received', 'We have received your payment of ₱899.00. Thank you!', 'success', 'payment', 1, 'index.php?controller=member&action=paymentHistory', '2025-11-17 16:02:10', '2025-11-17 16:29:31'),
(22, 7, 'Payment Received', 'Jhon Clein Pagarogan has made a payment of ₱899.00.', 'success', 'payment', 1, 'index.php?controller=admin&action=payments', '2025-11-17 16:02:10', '2025-11-17 16:39:32'),
(23, 1, 'Payment Received', 'We have received your payment of ₱899.00. Thank you!', 'success', 'payment', 1, 'index.php?controller=member&action=paymentHistory', '2025-11-17 16:02:55', '2025-11-17 16:29:31'),
(24, 7, 'Payment Received', 'Jhon Clein Pagarogan has made a payment of ₱899.00.', 'success', 'payment', 1, 'index.php?controller=admin&action=payments', '2025-11-17 16:02:55', '2025-11-17 16:39:32'),
(25, 21, 'Payment Received', 'We have received your payment of ₱899.00. Thank you!', 'success', 'payment', 1, 'index.php?controller=member&action=paymentHistory', '2025-11-17 16:37:53', '2025-11-17 16:39:10'),
(26, 7, 'Payment Received', 'Gray Lockser has made a payment of ₱899.00.', 'success', 'payment', 1, 'index.php?controller=admin&action=payments', '2025-11-17 16:37:53', '2025-11-17 16:39:32'),
(27, 1, 'You\'ve Been Promoted to Trainer!', 'Congratulations! You\'ve been promoted to a trainer role with specialization in Weight Training. Check your new dashboard to explore your trainer features.', 'success', 'trainer', 1, 'index.php?controller=trainer&action=dashboard', '2025-11-23 08:56:32', '2025-11-26 12:47:27'),
(28, 1, 'You\'ve Been Promoted to Trainer!', 'Congratulations! You\'ve been promoted to a trainer role with specialization in Weight Training. Check your new dashboard to explore your trainer features.', 'success', 'trainer', 1, 'index.php?controller=trainer&action=dashboard', '2025-11-23 08:58:56', '2025-11-26 12:47:27'),
(29, 1, 'You\'ve Been Promoted to Trainer!', 'Congratulations! You\'ve been promoted to a trainer role with specialization in Weight Training. Check your new dashboard to explore your trainer features.', 'success', 'trainer', 1, 'index.php?controller=trainer&action=dashboard', '2025-11-23 09:00:42', '2025-11-26 12:47:27'),
(30, 1, 'You\'ve Been Promoted to Trainer!', 'Congratulations! You\'ve been promoted to a trainer role with specialization in Weight Training. Check your new dashboard to explore your trainer features.', 'success', 'trainer', 1, 'index.php?controller=trainer&action=dashboard', '2025-11-23 09:07:51', '2025-11-26 12:47:27'),
(31, 1, 'You\'ve Been Promoted to Trainer!', 'Congratulations! You\'ve been promoted to a trainer role with specialization in Weight Training. Check your new dashboard to explore your trainer features.', 'success', 'trainer', 1, 'index.php?controller=trainer&action=dashboard', '2025-11-23 09:14:40', '2025-11-26 12:47:27'),
(32, 1, 'You\'ve Been Promoted to Trainer!', 'Congratulations! You\'ve been promoted to a trainer role with specialization in Weight Training. Check your new dashboard to explore your trainer features.', 'success', 'trainer', 1, 'index.php?controller=trainer&action=dashboard', '2025-11-23 09:15:08', '2025-11-26 12:47:27'),
(33, 1, 'You\'ve Been Promoted to Trainer!', 'Congratulations! You\'ve been promoted to a trainer role with specialization in Cardio. Check your new dashboard to explore your trainer features.', 'success', 'trainer', 1, 'index.php?controller=trainer&action=dashboard', '2025-11-26 12:37:09', '2025-11-26 12:47:27'),
(34, 1, 'You\'ve Been Promoted to Trainer!', 'Congratulations! You\'ve been promoted to a trainer role with specialization in Cardio. Check your new dashboard to explore your trainer features.', 'success', 'trainer', 1, 'index.php?controller=trainer&action=dashboard', '2025-11-26 12:39:32', '2025-11-26 12:47:27'),
(35, 1, 'Trainer Account Deactivated', 'Your trainer account has been deactivated. Please contact administration if you have any questions.', 'warning', 'trainer', 1, NULL, '2025-11-26 12:41:54', '2025-11-26 12:47:27'),
(36, 1, 'Trainer Account Deactivated', 'Your trainer account has been deactivated. Please contact administration if you have any questions.', 'warning', 'trainer', 1, NULL, '2025-11-26 12:41:54', '2025-11-26 12:47:27'),
(37, 1, 'You\'ve Been Promoted to Trainer!', 'Congratulations! You\'ve been promoted to a trainer role with specialization in Weight Training. Check your new dashboard to explore your trainer features.', 'success', 'trainer', 1, 'index.php?controller=trainer&action=dashboard', '2025-11-26 12:55:54', '2025-12-06 20:26:14'),
(38, 1, 'You\'ve Been Promoted to Trainer!', 'Congratulations! You\'ve been promoted to a trainer role with specialization in Weight Training. Check your new dashboard to explore your trainer features.', 'success', 'trainer', 1, 'index.php?controller=trainer&action=dashboard', '2025-11-26 12:58:18', '2025-12-06 20:26:14'),
(39, 1, 'You\'ve Been Promoted to Trainer!', 'Congratulations! You\'ve been promoted to a trainer role with specialization in Weight Training. Check your new dashboard to explore your trainer features.', 'success', 'trainer', 1, 'index.php?controller=trainer&action=dashboard', '2025-11-26 13:03:29', '2025-12-06 20:26:14'),
(40, 8, 'Membership Expired', 'Your membership has expired. Please renew to continue using the gym.', 'error', 'membership', 0, 'index.php?controller=member&action=renewMembership', '2025-12-09 09:58:53', NULL),
(41, 8, 'Plan Expiration', 'Your Current Plan has Expired', 'warning', 'membership', 0, NULL, '2025-12-09 09:58:53', NULL),
(42, 18, 'Profile Updated', 'Your profile has been updated successfully.', 'success', 'general', 0, NULL, '2025-12-13 07:38:11', NULL),
(43, 18, 'Profile Updated', 'Your profile has been updated successfully.', 'success', 'general', 0, NULL, '2025-12-13 07:38:11', NULL),
(44, 8, 'Payment Received', 'We have received your payment of ₱599.00. Thank you!', 'success', 'payment', 0, 'index.php?controller=member&action=paymentHistory', '2025-12-13 09:28:45', NULL),
(45, 7, 'Payment Received', 'Jane Doe has made a payment of ₱599.00.', 'success', 'payment', 0, 'index.php?controller=admin&action=payments', '2025-12-13 09:28:45', NULL),
(46, 8, 'Payment Received', 'We have received your payment of ₱599.00. Thank you!', 'success', 'payment', 0, 'index.php?controller=member&action=paymentHistory', '2025-12-13 09:29:33', NULL),
(47, 7, 'Payment Received', 'Jane Doe has made a payment of ₱599.00.', 'success', 'payment', 0, 'index.php?controller=admin&action=payments', '2025-12-13 09:29:33', NULL),
(48, 8, 'Payment Received', 'We have received your payment of ₱599.00. Thank you!', 'success', 'payment', 0, 'index.php?controller=member&action=paymentHistory', '2025-12-13 09:30:06', NULL),
(49, 7, 'Payment Received', 'Jane Doe has made a payment of ₱599.00.', 'success', 'payment', 0, 'index.php?controller=admin&action=payments', '2025-12-13 09:30:06', NULL),
(50, 7, 'New Member Registered', 'A new member has registered: max verstappen.', 'info', 'general', 0, 'index.php?controller=admin&action=members', '2025-12-13 14:10:44', NULL),
(51, 22, 'Plan cancellation', 'Your Current Plan has been Cancelled', 'warning', 'membership', 0, NULL, '2025-12-13 14:28:48', NULL),
(52, 22, 'Plan cancellation', 'Your Current Plan has been Cancelled', 'warning', 'membership', 0, NULL, '2025-12-13 15:06:17', NULL),
(53, 22, 'Plan Cancellation', 'Your Current Plan has been Cancelled.', 'warning', 'membership', 0, NULL, '2025-12-13 15:44:14', NULL),
(54, 1, 'Membership Expired', 'Your membership has expired. Please renew to continue using the gym.', 'error', 'membership', 0, 'index.php?controller=member&action=renewMembership', '2025-12-14 05:22:14', NULL),
(55, 1, 'Plan Expiration', 'Your Current Plan has Expired', 'warning', 'membership', 0, NULL, '2025-12-14 05:22:14', NULL),
(56, 7, 'New Member Registered', 'A new member has registered: Miko Zamora.', 'info', 'general', 0, 'index.php?controller=admin&action=members', '2025-12-14 05:43:17', NULL),
(57, 4, 'Membership Expired', 'Your membership has expired. Please renew to continue using the gym.', 'error', 'membership', 0, 'index.php?controller=member&action=renewMembership', '2025-12-14 06:37:21', NULL),
(58, 4, 'Plan Expiration', 'Your Current Plan has Expired', 'warning', 'membership', 0, NULL, '2025-12-14 06:37:21', NULL),
(59, 4, 'Membership Expired', 'Your membership has expired. Please renew to continue using the gym.', 'error', 'membership', 0, 'index.php?controller=member&action=renewMembership', '2025-12-14 06:37:53', NULL),
(60, 4, 'Plan Expiration', 'Your Current Plan has Expired', 'warning', 'membership', 0, NULL, '2025-12-14 06:37:53', NULL),
(61, 7, 'New Member Registered', 'A new member has registered: razel Herodias.', 'info', 'general', 0, 'index.php?controller=admin&action=members', '2025-12-14 08:14:40', NULL),
(62, 7, 'New Member Registered', 'A new member has registered: razel Herodias.', 'info', 'general', 0, 'index.php?controller=admin&action=members', '2025-12-14 08:22:08', NULL),
(63, 7, 'New Member Registered', 'A new member has registered: razel Herodias.', 'info', 'general', 0, 'index.php?controller=admin&action=members', '2025-12-14 08:39:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `token_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`token_id`, `user_id`, `email`, `token`, `expires_at`, `is_used`, `created_at`) VALUES
(1, 1, 'pagaroganjhonclein@gmail.com', 'ca861fea6c1d80f4b65a10b0cace00da2e21894593e87bfc75c5cc25479a1f6a', '2025-12-14 03:20:01', 0, '2025-12-14 09:20:01');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `amount` decimal(8,2) NOT NULL,
  `payment_date` date NOT NULL,
  `status` enum('paid','pending','failed','refunded') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `subscription_id`, `amount`, `payment_date`, `status`) VALUES
(1, 1, 599.00, '2025-11-11', 'paid'),
(11, 17, 899.00, '2025-11-20', 'paid'),
(12, 18, 899.00, '2025-11-21', 'refunded'),
(13, 19, 899.00, '2025-11-21', 'paid'),
(14, 22, 599.00, '2025-11-25', 'paid'),
(15, 23, 899.00, '2025-12-02', 'paid'),
(16, 24, 899.00, '2025-12-02', 'paid'),
(17, 25, 899.00, '2025-12-14', 'paid'),
(18, 26, 899.00, '2025-12-17', 'paid'),
(19, 27, 899.00, '2025-12-17', 'paid'),
(20, 28, 599.00, '2026-01-12', 'refunded'),
(21, 29, 599.00, '2026-01-12', 'pending'),
(22, 30, 599.00, '2025-12-13', 'pending'),
(23, 31, 899.00, '2025-12-14', 'pending'),
(24, 32, 255.00, '2025-12-14', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transaction`
--

CREATE TABLE `payment_transaction` (
  `transaction_id` int(11) NOT NULL,
  `subscription_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_type` enum('new','renewal','upgrade','downgrade','refund') DEFAULT 'new',
  `payment_status` enum('completed','pending','failed','refunded') NOT NULL DEFAULT 'completed',
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_transaction`
--

INSERT INTO `payment_transaction` (`transaction_id`, `subscription_id`, `payment_id`, `payment_method`, `transaction_type`, `payment_status`, `remarks`, `created_at`, `updated_at`) VALUES
(39, 22, 14, 'gcash', 'new', 'completed', '', '2025-10-26 17:12:33', '2025-10-26 23:33:06'),
(40, 18, 12, 'gcash', 'upgrade', 'completed', NULL, '2025-10-27 00:30:23', '2025-10-27 00:34:24'),
(41, 17, 11, 'paymaya', 'renewal', 'completed', NULL, '2025-10-27 00:33:36', '2025-10-27 00:33:57'),
(42, 19, 13, 'paymaya', 'new', 'completed', NULL, '2025-10-27 00:35:28', '2025-10-27 00:51:15'),
(45, 1, 1, 'card', 'new', 'completed', '', '2025-10-27 00:48:01', '2025-10-27 00:52:25'),
(55, 19, 13, 'paymaya', 'new', 'completed', '', '2025-10-27 14:26:49', '2025-10-27 14:26:49'),
(56, 23, 15, 'gcash', 'new', 'completed', '', '2025-11-03 01:11:23', '2025-11-03 01:11:23'),
(57, 24, 16, 'gcash', 'new', 'completed', '', '2025-11-03 01:18:36', '2025-11-03 01:18:36'),
(58, 24, 16, 'gcash', 'new', 'completed', '', '2025-11-10 21:47:25', '2025-11-10 21:47:25'),
(59, 24, 16, 'gcash', 'new', 'completed', '', '2025-11-10 21:48:45', '2025-11-10 21:48:45'),
(60, 25, 17, 'gcash', 'new', 'completed', '', '2025-11-14 22:57:20', '2025-11-14 22:57:20'),
(61, 26, 18, 'gcash', 'new', 'completed', '', '2025-11-17 22:46:10', '2025-11-17 22:46:10'),
(62, 26, 18, 'gcash', 'new', 'completed', '', '2025-11-17 22:46:22', '2025-11-17 22:46:22'),
(63, 26, 18, 'gcash', 'new', 'completed', '', '2025-11-17 22:50:01', '2025-11-17 22:50:01'),
(64, 25, 17, 'gcash', 'new', 'completed', '', '2025-11-17 23:59:07', '2025-11-17 23:59:07'),
(65, 25, 17, 'gcash', 'new', 'completed', '', '2025-11-17 23:59:14', '2025-11-17 23:59:14'),
(66, 25, 17, 'gcash', 'new', 'completed', '', '2025-11-18 00:02:00', '2025-11-18 00:02:00'),
(67, 25, 17, 'gcash', 'new', 'completed', '', '2025-11-18 00:02:10', '2025-11-18 00:02:10'),
(68, 25, 17, 'gcash', 'new', 'completed', '', '2025-11-18 00:02:55', '2025-11-18 00:02:55'),
(69, 27, 19, 'gcash', 'new', 'completed', '', '2025-11-18 00:37:52', '2025-11-18 00:37:52'),
(70, 28, 20, 'gcash', 'new', 'completed', '', '2025-12-13 17:28:43', '2025-12-13 17:28:43'),
(71, 28, 20, 'gcash', 'new', 'completed', '', '2025-12-13 17:29:33', '2025-12-13 17:29:33'),
(72, 28, 20, 'gcash', 'new', 'completed', '', '2025-12-13 17:30:05', '2025-12-13 17:30:05');

-- --------------------------------------------------------

--
-- Table structure for table `plan_features`
--

CREATE TABLE `plan_features` (
  `user_id` int(11) NOT NULL,
  `feature_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `session_date` datetime NOT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `notes` text NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`session_id`, `user_id`, `trainer_id`, `session_date`, `status`, `notes`, `created_at`) VALUES
(3, 1, 1, '2025-11-03 10:10:00', 'cancelled', 'cardio session', '2025-10-27'),
(11, 1, 1, '2025-11-03 22:23:00', 'completed', '', '2025-10-27'),
(13, 4, 1, '2025-11-27 22:26:00', 'scheduled', '', '2025-10-27'),
(14, 8, 1, '2025-12-10 18:24:00', 'scheduled', '', '2025-12-09'),
(15, 8, 1, '2025-12-27 18:27:00', 'scheduled', '', '2025-12-09'),
(16, 8, 1, '2025-12-10 18:30:00', 'scheduled', '', '2025-12-09'),
(17, 4, 1, '2025-12-17 15:00:00', 'completed', '', '2025-12-14');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `subscription_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired','cancelled') NOT NULL DEFAULT 'active',
  `freeze_start_date` date DEFAULT NULL,
  `freeze_end_date` date DEFAULT NULL,
  `freeze_reason` text DEFAULT NULL,
  `is_frozen` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`subscription_id`, `user_id`, `plan_id`, `start_date`, `end_date`, `status`, `freeze_start_date`, `freeze_end_date`, `freeze_reason`, `is_frozen`) VALUES
(1, 1, 2, '2025-10-21', '2025-11-20', 'expired', NULL, NULL, NULL, 0),
(17, 4, 3, '2025-10-21', '2025-11-22', 'expired', NULL, NULL, NULL, 0),
(18, 4, 3, '2025-10-22', '2025-11-21', 'cancelled', NULL, NULL, NULL, 0),
(19, 4, 3, '2025-10-22', '2025-11-21', 'expired', NULL, NULL, NULL, 0),
(22, 17, 2, '2025-10-26', '2025-11-04', 'expired', NULL, NULL, NULL, 0),
(23, 8, 3, '2025-11-02', '2025-12-02', 'expired', NULL, NULL, NULL, 0),
(24, 19, 3, '2025-11-02', '2025-12-02', 'expired', NULL, NULL, NULL, 0),
(25, 1, 3, '2025-11-14', '2025-12-14', 'expired', NULL, NULL, NULL, 0),
(26, 19, 3, '2025-11-17', '2025-12-17', 'active', NULL, NULL, NULL, 0),
(27, 21, 3, '2025-11-17', '2025-12-17', 'active', NULL, NULL, NULL, 0),
(28, 8, 2, '2025-12-13', '2026-01-12', 'active', '2025-12-15', '2025-12-31', 'xmas travel', 1),
(29, 22, 2, '2025-12-13', '2026-01-12', 'cancelled', NULL, NULL, NULL, 0),
(30, 22, 2, '2025-12-13', '2026-01-12', 'cancelled', NULL, NULL, NULL, 0),
(31, 1, 3, '2025-12-14', '2026-01-13', 'active', '2025-12-15', '2025-12-16', 'fever', 1),
(32, 23, 1, '2025-12-14', '2026-01-13', 'active', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `trainer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `join_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`trainer_id`, `user_id`, `specialization`, `experience_years`, `contact_no`, `status`, `join_date`) VALUES
(1, 18, 'Weight Training', 11, '09123456789', 'active', '2025-10-27 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `trainer_members`
--

CREATE TABLE `trainer_members` (
  `assignment_id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_date` datetime DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer_members`
--

INSERT INTO `trainer_members` (`assignment_id`, `trainer_id`, `user_id`, `assigned_date`, `status`) VALUES
(2, 1, 4, '2025-10-27 00:00:00', 'active'),
(3, 1, 1, '2025-10-27 00:00:00', 'active'),
(10, 1, 8, '0000-00-00 00:00:00', ''),
(11, 1, 8, '2025-12-14 15:05:13', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `trainer_requests`
--

CREATE TABLE `trainer_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer_requests`
--

INSERT INTO `trainer_requests` (`request_id`, `user_id`, `trainer_id`, `note`, `status`, `created_at`) VALUES
(1, 17, 1, NULL, 'pending', '2025-10-17 00:00:00'),
(2, 8, 1, '', 'accepted', '2025-12-09 19:59:13');

-- --------------------------------------------------------

--
-- Table structure for table `walk_ins`
--

CREATE TABLE `walk_ins` (
  `walkin_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `session_type` varchar(50) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `payment_amount` int(11) NOT NULL,
  `visit_time` datetime DEFAULT current_timestamp(),
  `end_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `walk_ins`
--

INSERT INTO `walk_ins` (`walkin_id`, `first_name`, `last_name`, `middle_name`, `email`, `contact_no`, `session_type`, `payment_method`, `payment_amount`, `visit_time`, `end_date`) VALUES
(1, 'Jhon Clein', 'Toribio', '', 'test@gmail.com', '09366600119', 'weekend', 'cash', 60, '2025-10-25 17:57:00', '2025-10-26 00:00:00'),
(2, 'Jane', 'Doe', 'S', 'test03@gmail.com', '+639694685197', 'single', 'gcash', 20, '2025-10-26 07:27:49', '2025-10-27 07:27:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `membership_freeze_history`
--
ALTER TABLE `membership_freeze_history`
  ADD PRIMARY KEY (`freeze_id`),
  ADD KEY `subscription_id` (`subscription_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_freeze_status` (`status`),
  ADD KEY `idx_freeze_user` (`user_id`),
  ADD KEY `idx_freeze_dates` (`freeze_start`,`freeze_end`);

--
-- Indexes for table `membership_plans`
--
ALTER TABLE `membership_plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `member_address`
--
ALTER TABLE `member_address`
  ADD PRIMARY KEY (`zip`);

--
-- Indexes for table `member_address_link`
--
ALTER TABLE `member_address_link`
  ADD PRIMARY KEY (`user_id`,`zip`),
  ADD KEY `fk_mal_zip` (`zip`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_subscription` (`subscription_id`);

--
-- Indexes for table `payment_transaction`
--
ALTER TABLE `payment_transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `fk_transaction_user` (`subscription_id`),
  ADD KEY `fk_payment_id` (`payment_id`);

--
-- Indexes for table `plan_features`
--
ALTER TABLE `plan_features`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `fk2_user_id` (`user_id`),
  ADD KEY `fk_trainer_id` (`trainer_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `fk_user` (`user_id`),
  ADD KEY `fk_plan` (`plan_id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`trainer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `trainer_members`
--
ALTER TABLE `trainer_members`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `trainer_members_ibfk_1` (`trainer_id`);

--
-- Indexes for table `trainer_requests`
--
ALTER TABLE `trainer_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `walk_ins`
--
ALTER TABLE `walk_ins`
  ADD PRIMARY KEY (`walkin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `membership_freeze_history`
--
ALTER TABLE `membership_freeze_history`
  MODIFY `freeze_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `membership_plans`
--
ALTER TABLE `membership_plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `payment_transaction`
--
ALTER TABLE `payment_transaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `trainer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `trainer_members`
--
ALTER TABLE `trainer_members`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `trainer_requests`
--
ALTER TABLE `trainer_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `walk_ins`
--
ALTER TABLE `walk_ins`
  MODIFY `walkin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `membership_freeze_history`
--
ALTER TABLE `membership_freeze_history`
  ADD CONSTRAINT `membership_freeze_history_ibfk_1` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`subscription_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `membership_freeze_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `membership_freeze_history_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `members` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `member_address_link`
--
ALTER TABLE `member_address_link`
  ADD CONSTRAINT `fk_mal_user` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mal_zip` FOREIGN KEY (`zip`) REFERENCES `member_address` (`zip`) ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`subscription_id`);

--
-- Constraints for table `payment_transaction`
--
ALTER TABLE `payment_transaction`
  ADD CONSTRAINT `fk_payment_id` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_transaction_user` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`subscription_id`) ON DELETE CASCADE;

--
-- Constraints for table `plan_features`
--
ALTER TABLE `plan_features`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `fk2_user_id` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_trainer_id` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `fk_plan` FOREIGN KEY (`plan_id`) REFERENCES `membership_plans` (`plan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `trainers`
--
ALTER TABLE `trainers`
  ADD CONSTRAINT `trainers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `trainer_members`
--
ALTER TABLE `trainer_members`
  ADD CONSTRAINT `trainer_members_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `trainer_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `trainer_requests`
--
ALTER TABLE `trainer_requests`
  ADD CONSTRAINT `trainer_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trainer_requests_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
