-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 25, 2025 at 03:12 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e-coomerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_logs`
--

DROP TABLE IF EXISTS `access_logs`;
CREATE TABLE IF NOT EXISTS `access_logs` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `access_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `action` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `customer_id` int NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT NULL,
  `update_by` int DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `email` (`email`),
  KEY `created_by` (`created_by`),
  KEY `update_by` (`update_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_orders`
--

DROP TABLE IF EXISTS `customer_orders`;
CREATE TABLE IF NOT EXISTS `customer_orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  PRIMARY KEY (`order_id`),
  KEY `customer_id` (`user_id`),
  KEY `product_id` (`product_id`),
  KEY `created_by` (`created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customer_orders`
--

INSERT INTO `customer_orders` (`order_id`, `user_id`, `product_id`, `quantity`, `total_amount`, `created_at`, `created_by`, `status`) VALUES
(1, 1, 1, 2, '20.00', '2025-03-20 11:38:31', 1, 'pending'),
(2, 1, 2, 1, '20.00', '2025-03-20 11:38:31', 1, 'pending'),
(3, 1, 5, 1, '12.00', '2025-03-20 12:54:32', 1, 'pending'),
(4, 1, 3, 1, '2.00', '2025-03-20 12:55:48', 1, 'pending'),
(5, 1, 4, 1, '111.00', '2025-03-20 12:56:42', 1, 'pending'),
(6, 1, 8, 1, '12345.00', '2025-03-22 14:36:48', 1, 'pending'),
(7, 1, 3, 1, '11.00', '2025-03-24 10:33:51', 1, 'pending'),
(10, 3, 3, 1, '11.00', '2025-03-24 18:08:47', 3, 'completed'),
(11, 3, 4, 1, '111.00', '2025-03-24 18:08:47', 3, 'completed'),
(12, 3, 5, 1, '1233.00', '2025-03-24 18:08:47', 3, 'completed'),
(13, 3, 14, 1, '3000.00', '2025-03-25 07:30:30', 3, 'completed'),
(14, 3, 15, 1, '45000.00', '2025-03-25 07:30:30', 3, 'pending'),
(15, 5, 13, 1, '10.00', '2025-03-25 07:59:17', 5, 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

DROP TABLE IF EXISTS `delivery`;
CREATE TABLE IF NOT EXISTS `delivery` (
  `delivery_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `status` varchar(20) DEFAULT 'in progress',
  PRIMARY KEY (`delivery_id`),
  KEY `order_id` (`order_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `in_stock`
--

DROP TABLE IF EXISTS `in_stock`;
CREATE TABLE IF NOT EXISTS `in_stock` (
  `in_stock_id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`in_stock_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `in_stock`
--

INSERT INTO `in_stock` (`in_stock_id`, `product_id`, `quantity`, `unit_price`, `created_at`) VALUES
(14, 15, 100, '45000.00', '2025-03-25 07:29:24'),
(13, 14, 9, '3000.00', '2025-03-25 07:28:31'),
(12, 13, 9, '10.00', '2025-03-25 07:27:45');

-- --------------------------------------------------------

--
-- Table structure for table `out_stock`
--

DROP TABLE IF EXISTS `out_stock`;
CREATE TABLE IF NOT EXISTS `out_stock` (
  `out_stock_id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`out_stock_id`),
  KEY `product_id` (`product_id`),
  KEY `customer_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `out_stock`
--

INSERT INTO `out_stock` (`out_stock_id`, `product_id`, `quantity`, `unit_price`, `created_at`, `user_id`) VALUES
(1, 8, 1, '12345.00', '2025-03-24 19:26:37', 3),
(2, 5, 1, '1233.00', '2025-03-24 19:27:23', 3),
(3, 8, 1, '12345.00', '2025-03-24 19:41:04', 3),
(4, 5, 1, '1233.00', '2025-03-24 19:42:27', 3),
(5, 5, 1, '1233.00', '2025-03-24 19:44:32', 3),
(6, 8, 1, '0.00', '2025-03-24 19:55:32', 3),
(7, 8, 1, '0.00', '2025-03-24 19:55:38', 3),
(8, 8, 1, '0.00', '2025-03-24 19:59:47', 3),
(9, 3, 1, '0.00', '2025-03-24 20:03:40', 3),
(10, 4, 1, '0.00', '2025-03-24 20:03:48', 3),
(11, 13, 1, '0.00', '2025-03-25 07:59:43', 5),
(12, 14, 1, '0.00', '2025-03-25 15:07:07', 3);

-- --------------------------------------------------------

--
-- Table structure for table `passwords`
--

DROP TABLE IF EXISTS `passwords`;
CREATE TABLE IF NOT EXISTS `passwords` (
  `password_id` int NOT NULL AUTO_INCREMENT,
  `password` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`password_id`),
  KEY `created_by` (`created_by`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `passwords`
--

INSERT INTO `passwords` (`password_id`, `password`, `created_at`, `created_by`, `user_id`) VALUES
(1, '1234', '2025-03-17 10:37:57', NULL, 1),
(2, '1234', '2025-03-18 07:57:25', NULL, 2),
(3, '1234', '2025-03-18 21:09:21', NULL, 3),
(4, 'gahogo@gmail.com', '2025-03-20 13:07:01', NULL, 4),
(5, '123@gmail.com', '2025-03-25 07:34:28', NULL, 5);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  PRIMARY KEY (`payment_id`),
  KEY `order_id` (`order_id`),
  KEY `created_by` (`created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `amount`, `create_at`, `created_by`, `status`) VALUES
(1, 8, '12345.00', '2025-03-24 19:26:36', 3, 'completed'),
(2, 12, '1233.00', '2025-03-24 19:27:23', 3, 'completed'),
(3, 8, '12345.00', '2025-03-24 19:41:04', 3, 'completed'),
(4, 12, '1233.00', '2025-03-24 19:42:27', 3, 'completed'),
(5, 12, '1233.00', '2025-03-24 19:44:32', 3, 'completed'),
(9, 10, '11.00', '2025-03-24 20:03:40', 3, 'completed'),
(8, 8, '12345.00', '2025-03-24 19:59:47', 3, 'completed'),
(10, 11, '111.00', '2025-03-24 20:03:48', 3, 'completed'),
(11, 15, '10.00', '2025-03-25 07:59:43', 5, 'completed'),
(12, 13, '3000.00', '2025-03-25 15:07:07', 3, 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) NOT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(20) DEFAULT 'available',
  `description` text,
  `image_path` longblob,
  PRIMARY KEY (`product_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `user_id`, `created_at`, `updated_at`, `status`, `description`, `image_path`) VALUES
(13, 'Head phone', NULL, '2025-03-25 07:27:45', '2025-03-25 07:27:45', 'active', 'wwwwwww', 0x75706c6f6164732f70726f64756374732f70726f647563745f363765323561663165316337612e6a7067),
(14, 'Tablet', NULL, '2025-03-25 07:28:31', '2025-03-25 07:28:31', 'active', 'Smart Tablet', 0x75706c6f6164732f70726f64756374732f70726f647563745f363765323562316634363630352e6a7067),
(15, 'Smart watch', NULL, '2025-03-25 07:29:24', '2025-03-25 07:29:24', 'active', 'Smart Watch', 0x75706c6f6164732f70726f64756374732f70726f647563745f363765323562353433346439642e6a7067);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'admin'),
(2, 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `update_by` int DEFAULT NULL,
  `role_id` int DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `email_2` (`email`),
  KEY `role_id` (`role_id`),
  KEY `created_by` (`created_by`),
  KEY `update_by` (`update_by`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `phone`, `create_at`, `created_by`, `updated_at`, `update_by`, `role_id`, `status`) VALUES
(1, 'Fred', 'fredndabikunze01@gmail.com', NULL, '2025-03-17 10:37:57', NULL, NULL, NULL, 1, 'active'),
(2, 'Administrator@gmail.com', 'Administrator@gmail.com', NULL, '2025-03-18 07:57:25', NULL, NULL, NULL, 1, 'active'),
(3, 'customer@gmail.com', 'customer@gmail.com', NULL, '2025-03-18 21:09:21', NULL, NULL, NULL, 2, 'active'),
(4, 'gahogo@gmail.com', 'gahogo@gmail.com', NULL, '2025-03-20 13:07:01', NULL, NULL, NULL, 2, 'active'),
(5, '123@gmail.com', '123@gmail.com', NULL, '2025-03-25 07:34:27', NULL, NULL, NULL, 2, 'active');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
