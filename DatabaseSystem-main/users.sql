-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2024 at 03:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `users`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'client',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  `restaurant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `address`, `password`, `role`, `created_at`, `is_admin`, `restaurant_id`) VALUES
(3, 'Admin', 'Account', 'email@gmail.com', '5554446666', 'address', '$2y$10$H0k0IrDVJSVcg8SjJ/F6q.MD0gwsPGPqDx0tZ83LLlD9ocl1EDtU2', 'client', '2024-07-16 02:24:00', 1, NULL),
(4, 'Jane', 'Smith', 'janesmith@email.com', '5555555555', 'Jane Smith Address', '$2y$10$xTdzcAFzbrTljXQzllWTGObwQQEE/SKzbLPimEIpa5G2aYwnEvRhi', 'client', '2024-07-22 06:39:44', 0, NULL),
(5, 'John', 'Smith', 'johnsmith@email.com', '5555555554', 'johnsmith', '$2y$10$7aU36J14go03GZNWl5T.iOLLxmWHZ0HtTAB5qcYylWS.PqVMrAmg6', 'client', '2024-07-22 07:01:55', 0, NULL),
(6, 'Restaurant A', '', 'restaurantA@gmail.com', '5555555555', 'Restaurant A, Atlanta', '$2y$10$62bT7/o8EFyUjlWNFa32GePW1SxA7R/U29wilWfV.IM78lidGEiuC', 'business', '2024-07-22 21:55:07', 0, 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant_info`.`restaurants` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
