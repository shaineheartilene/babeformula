-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2024 at 05:51 AM
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
-- Database: `dbbabes`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin') NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `role`) VALUES
(1, 'adminbabe', 'adminbabef', 'babeformula1@gmail.com', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `username`, `product_id`, `quantity`, `created_at`) VALUES
(15, 'shai', 29, 1, '2024-11-07 11:50:40'),
(16, 'stephittupp', 34, 1, '2024-11-07 11:56:05');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `username`, `total_amount`, `order_date`) VALUES
(4, 'stephittupp', 1136.00, '2024-11-03 16:34:30'),
(5, 'stephittupp', 284.00, '2024-11-04 06:22:35'),
(6, 'stephittupp', 284.00, '2024-11-04 06:24:21');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(3, 4, 29, 3, 0.00),
(4, 4, 32, 1, 0.00),
(5, 5, 29, 1, 0.00),
(6, 6, 29, 1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `stocks` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `category`, `price`, `size`, `image_path`, `stocks`, `description`, `date_added`) VALUES
(29, 'Bonbon Shampoo', 'shampoo', 284.00, '250', 'products/1730644966_bonbon-shampoo.jpg', 295, '', '2024-11-10 13:15:00'),
(30, 'Chiffon Hair Spray', 'Hair Spray', 180.00, '60', 'products/1730645022_chiffon-spray.jpg', 240, '', '2024-11-10 13:15:00'),
(31, 'Chiffon Shampoo', 'shampoo', 284.00, '250', 'products/1730645194_chiffon-shampoo.png', 300, '', '2024-11-10 13:15:00'),
(32, 'Chiffon Conditioner', 'shampoo', 284.00, '250', 'products/1730646717_chiffon-conditioner.png', 299, '', '2024-11-10 13:15:00'),
(33, 'Chiffon Set - Shampoo and Conditioner', 'set', 567.00, '250', 'products/1730646918_chiffon-set.png', 100, '', '2024-11-10 13:15:00'),
(34, 'whimsicle conditioner', 'Conditioner', 284.00, '250', 'products/1730975021_400g-whimsicle-conditioner-refill.png', 300, '', '2024-11-10 13:15:00'),
(37, 'Avo Hair Mask', 'Hair Mask', 284.00, '250', 'products/1730980308_avo-hairmasque.jpg', 300, 'Hair Mask', '2024-11-10 13:15:00'),
(40, 'Chiffon Conditioner', 'Conditioner', 284.00, '250', 'products/1731244450_1730646717_chiffon-conditioner.png', 300, '', '2024-11-10 13:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `tblcustomer_login`
--

CREATE TABLE `tblcustomer_login` (
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `birthday` date DEFAULT NULL,
  `gender` enum('male','female','prefer_not_to_say') NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `verify_token` varchar(191) NOT NULL,
  `verif_status` int(2) NOT NULL DEFAULT 0,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fname`, `lname`, `birthday`, `gender`, `contact`, `email`, `verify_token`, `verif_status`, `role`) VALUES
(16, 'shai', '$2y$10$Y1SJ81yaD6/4XBej0VRStu93XsDyqAT7kX6an1moDeuRKEuAvDPtu', 'shaine heartilene', 'amargo', '1999-01-28', 'female', '09304368310', 'shaineheartileneamargo@gmail.com', 'd643d5698501a3bbf78074cd6d7c1218', 1, 'user'),
(26, 'adminbabe', '$2y$10$4AV8auxbr08BCe92OkPY3OfbKIlFmjiChE4o3Lg6VXLuxO4KvOYF2', 'Admin', 'Babe', '1999-01-01', 'male', '09266167021', 'adminbabe@babeformula.com', '', 1, 'admin'),
(27, 'stephittupp', '$2y$10$be0pNtFt3BZovraUvi5QluhU5dr5vwqwwL9OoBXrGg08huPCMvSRm', 'Estephanie', 'De Torres', '2004-11-18', 'female', '09533304095', 'estephaniedetorres15@gmail.com', 'd880d70b7b64090e48da8f5b52320136', 1, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `province` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `address_details` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `username`, `name`, `contact`, `email`, `address`, `is_default`, `province`, `city`, `barangay`, `address_details`) VALUES
(1, 'shai', 'SHAINE HEARTILENE AMARGO', '0912345677', 'shaineheartileneamargo@gmail.com', 'UPA', 0, 'BATANGAS', 'MATAAS NA KAHOY', 'UPA', 'PRK.3'),
(2, 'shai', 'SHAINE HEARTILENE AMARGO', '0912345677', 'shaineheartileneamargo@gmail.com', '', 0, 'BATANGAS', 'MATAAS NA KAHOY', 'UPA', 'PRK.3'),
(3, 'stephittupp', 'DE TORRES, ESTEPHANIE ANNE', '09567220918', 'estephaniedetorres15@gmail.com', '', 0, 'BATANGAS', 'MATAASNAKAHOY', 'CALINGATAN', '143');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

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
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
