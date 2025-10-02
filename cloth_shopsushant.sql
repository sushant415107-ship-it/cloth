-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 26, 2025 at 09:19 PM
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
-- Database: `cloth_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Men', 'men', '2025-09-25 15:36:36'),
(2, 'Women', 'women', '2025-09-25 15:36:36'),
(3, 'Kids', 'kids', '2025-09-25 15:36:36'),
(4, 'Accessories', 'accessories', '2025-09-25 15:36:36');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `submission_date`) VALUES
(1, 'djcdij', 'atharvamondkar24@gmail.com', 'djovdcj', '2025-09-25 18:28:57'),
(2, 'HFB', 'atharvamondkar24@gmail.com', 'GNBN', '2025-09-25 19:37:06');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `delivery_address` varchar(255) NOT NULL,
  `delivery_city` varchar(100) NOT NULL,
  `delivery_zip` varchar(20) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `transaction_status` varchar(50) NOT NULL DEFAULT 'Pending Payment',
  `razorpay_order_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `delivery_address`, `delivery_city`, `delivery_zip`, `payment_method`, `order_status`, `transaction_status`, `razorpay_order_id`, `created_at`) VALUES
(1, 1, 100.00, '304, panchasheel arcade, 3rd floor, sector-5', 'Airoli', '400708', 'Cash on Delivery', 'Pending', 'Pending Payment', NULL, '2025-09-26 17:04:23'),
(2, 7, 75.00, '304, panchasheel arcade, 3rd floor, sector-5', 'Airoli', '400708', 'Cash on Delivery', 'Pending', 'Pending Payment', NULL, '2025-09-26 17:47:11'),
(3, 7, 80.00, '304, panchasheel arcade, 3rd floor, sector-5', 'Airoli', '400708', 'Razorpay', 'Pending', 'Completed', NULL, '2025-09-26 17:52:35'),
(4, 7, 145.00, '304, panchasheel arcade, 3rd floor, sector-5', 'Airoli', '400708', 'Razorpay', 'Pending', 'Completed', NULL, '2025-09-26 19:16:07');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'men'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `category`) VALUES
(1, 'Men\'s T-Shirt', 'A comfortable and stylish cotton t-shirt.', 25.00, 'images/men_tshirt.jpg', 'men'),
(2, 'Men\'s Jeans', 'Classic blue denim jeans, perfect for any occasion.', 55.00, 'images/men_jeans.jpg', 'men'),
(3, 'Men\'s Jacket', 'A lightweight jacket for cooler weather.', 75.00, 'images/men_jacket.jpg', 'men'),
(4, 'Men\'s Formal Shirt', 'A crisp, professional formal shirt.', 45.00, 'images/men_formal_shirt.jpg', 'men'),
(5, 'Men\'s Chinos', 'Stylish and versatile chinos for a smart-casual look.', 60.00, 'images/men_chinos.jpg', 'men'),
(6, 'Men\'s Hoodie', 'A warm and comfortable fleece hoodie.', 50.00, 'images/men_hoodie.jpg', 'men'),
(7, 'Men\'s Polo Shirt', 'Classic polo shirt with a striped collar.', 30.00, 'images/men_polo.jpg', 'men'),
(8, 'Men\'s Denim Jacket', 'A classic denim jacket with a rugged finish.', 85.00, 'images/men_denim_jacket.jpg', 'men');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `created_at`) VALUES
(1, 'atharva', 'atharvamondkar24@gmail.com', '$2y$10$cy7xorTDr53wPUGphG3.YexDUoHNuYW0BbQOpLoubRrycFbURMf/y', '2025-09-25 15:54:47'),
(2, 'ath', 'ath123@gmail.com', '$2y$10$ybJjnB2GbTK0YDvtUw31H.QufgU.znWZrBRO5Tkn8BkUt4uTdwuhq', '2025-09-25 16:11:18'),
(3, 'mihir kanade', 'ath@gmail.com', '$2y$10$nm6L1RLMzebV0s8DgeuNtuFI.pgtlF9VA2GUrHieE5Sjd/H0wNJne', '2025-09-26 17:04:03'),
(4, 'pranali', 'at@gmail.com', '$2y$10$8BnVPoQ671CAZ9Rwu3uhkeouZcZuL9Z5ONXgZHc6rSqtW5EK7W7Li', '2025-09-26 17:05:37'),
(5, 'ATHARVA MONDKAR', 'atharvapankaj@gmail.com', '$2y$10$ds022VipfHx/6hvM6x7wlu0U/TUKM3TINDHJu0x5X6SRX6VIio62i', '2025-09-26 17:19:29'),
(6, 'atharva', 'atharvamondkar25@gmail.com', '$2y$10$qz9YF/EK1faB8XexPj6KWew0y2BDN1CeABToE0ZJpwuPbDB8yMEKa', '2025-09-26 17:37:18'),
(7, 'atha', 'atharvamondkar26@gmail.com', '$2y$10$1kqO7x2ifVZtRVh08bFFUuO7rzhvd7QEFtst3KRGWdkx8t23aSrAW', '2025-09-26 17:41:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
