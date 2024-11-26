-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2024 at 06:15 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tiffin_service`
--

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `monthly_price` decimal(10,2) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `specialtys` mediumtext NOT NULL,
  `delivery_time` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `user_id`, `monthly_price`, `item_name`, `quantity`, `price`, `created_at`, `specialtys`, `delivery_time`) VALUES
(1, 3, '1234.00', 'Roti', 1, '2.00', '2024-11-04 16:56:14', '', ''),
(2, 3, '1234.00', 'Egg', 2, '12.00', '2024-11-04 16:56:14', '', ''),
(3, 3, '1234.00', 'Dal', 12, '10.00', '2024-11-04 18:23:02', '', ''),
(4, 4, '1500.00', '0', 5, '10.00', '2024-11-04 19:36:35', 'Homemade Indian Cuisine', '30-45 minutes'),
(5, 4, '1500.00', '0', 2, '12.00', '2024-11-04 19:36:35', 'Homemade Indian Cuisine', '30-45 minutes');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `monthly_price` decimal(10,2) NOT NULL,
  `extra_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `payment_date` datetime NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `vendor_id`, `monthly_price`, `extra_amount`, `total_amount`, `transaction_id`, `payment_date`, `status`, `user_id`, `email`, `firstName`, `mobile`) VALUES
(1, 3, '1234.00', '0.00', '1234.00', 'TXN1731195174478193', '2024-11-09 23:32:54', 'pendong', 0, '', '', ''),
(2, 3, '1234.00', '24.00', '1258.00', 'TXN1731195439985338', '2024-11-09 23:37:19', 'pendong', 0, '', '', ''),
(3, 3, '1234.00', '0.00', '1234.00', 'TXN173123400647278', '2024-11-10 10:20:06', 'pendong', 0, '', '', ''),
(14, 3, '1234.00', '0.00', '1234.00', 'TXN1731235915477889', '2024-11-10 10:51:55', 'complete', 0, 'kunal@gmail.com', 'Kunal11', '9630258741');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL,
  `address` varchar(100) NOT NULL,
  `city` varchar(50) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `state` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstName`, `lastName`, `email`, `mobile`, `password`, `role`, `address`, `city`, `pincode`, `state`, `created_at`) VALUES
(1, 'Kunal1', 'Pardhi1', 'kunalpardhi444@gmail.com', '9284677667', '$2y$10$U2Zj9p1THmiBwRsuUfWvxeTVkUninHEZgXqyItoL6maXUeB9/YJ8C', 'customer', 'Lokmanya Nagar', 'Nagpur', '440016', 'Maharashtra ', '2024-11-02 10:20:18'),
(2, 'k', 'p', 'kp@gmail.com', '8520147963', '$2y$10$Yxu8z8Becr/C36XTpzWA6ujuR9yvfuVof03yq6R8kRMLCgJnuvqlS', 'customer', 'Lokmanya Nagar', 'Nagpur', '440016', 'Maharashtra ', '2024-11-02 10:24:17'),
(3, 'Kunal11', 'Pardhi', 'kunal@gmail.com', '9630258741', '$2y$10$7ON1FEDiPKACDUe5VpWzf./w1dvkI0fC2IlWxYYk7F7mWNE3fYwCm', 'vendor', 'Lokmanya Nagar', 'Nagpur', '440016', 'Maharashtra ', '2024-11-04 07:45:39'),
(4, 'Raja', 'Ram', 'rajaram@gmail.com', '9630258741', '$2y$10$hzfmimxHcgpakx5Ubby1tOkjGG5oHR/DsQa4u7toUE8PHQ6me22Ma', 'vendor', 'Lokmanya Nagar', 'Gondia', '445522', 'Maharashtra ', '2024-11-04 19:33:10'),
(5, 'Vijay', 'Rathor', 'vijay@gmail.com', '8522265412', '$2y$10$ujFzvU9idCR82hMFZC3kiO15X0z5iSs.615mgYvb3FxlfR9Qw8YPG', 'customer', 'Lokmanya Nagar', 'Nagpur', '440016', 'Maharashtra ', '2024-11-09 19:05:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`);

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
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
