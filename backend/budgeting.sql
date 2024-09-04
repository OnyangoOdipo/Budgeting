-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2024 at 09:06 AM
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
-- Database: `budgeting`
--

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int(11) NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `approved_by`, `date_modified`, `date_created`, `department_id`, `currency_id`, `item_id`, `total_amount`) VALUES
(6, NULL, '2024-08-30 19:29:31', '2024-08-30 19:29:31', 4, 1, 8, 449980.00),
(7, NULL, '2024-08-30 19:33:38', '2024-08-30 19:33:38', 4, 1, 9, 625000.00),
(9, NULL, '2024-09-04 05:27:18', '2024-09-04 05:27:18', 4, 1, 11, 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `budget_items`
--

CREATE TABLE `budget_items` (
  `budget_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budget_items`
--

INSERT INTO `budget_items` (`budget_id`, `item_id`) VALUES
(6, 8),
(7, 9),
(9, 11);

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

CREATE TABLE `currency` (
  `id` int(11) NOT NULL,
  `currency_symbol` varchar(10) NOT NULL,
  `currency_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `currency`
--

INSERT INTO `currency` (`id`, `currency_symbol`, `currency_name`) VALUES
(1, 'Kshs', 'Kenyan Shilling'),
(2, 'USD', 'United States Dollar');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `head_of_department` int(11) DEFAULT NULL,
  `department` enum('Admin&Finance','Human Resource','Sales&Marketing','Technical','Operation') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `head_of_department`, `department`) VALUES
(1, NULL, 'Admin&Finance'),
(2, NULL, 'Human Resource'),
(3, NULL, 'Sales&Marketing'),
(4, 13, 'Technical'),
(5, 15, 'Operation');

-- --------------------------------------------------------

--
-- Table structure for table `fund_allocations`
--

CREATE TABLE `fund_allocations` (
  `id` int(11) NOT NULL,
  `budget_id` int(11) NOT NULL,
  `allocated_amount` decimal(10,2) NOT NULL,
  `allocation_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fund_allocations`
--

INSERT INTO `fund_allocations` (`id`, `budget_id`, `allocated_amount`, `allocation_date`) VALUES
(8, 6, 345456.00, '2024-09-03 19:25:35'),
(9, 6, 4564.00, '2024-09-04 08:11:51'),
(10, 6, 100000.00, '2024-09-04 08:12:05');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `colour` varchar(50) DEFAULT NULL,
  `total_cost` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `unit_price`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `description`, `quantity`, `unit_price`, `brand`, `colour`) VALUES
(8, 'HP Laptops', 20, 45000.00, 'HP', 'Black'),
(9, 'Phones', 25, 25000.00, 'Samsung', 'Gold'),
(10, 'Tablets', 31, 27000.00, 'Samsung', 'Blue'),
(11, 'Lanyards', 5, 200.00, 'New', 'White');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `message` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `department_id`, `message`, `created_date`) VALUES
(1, 4, 'A new budget has been created for your department.', '2024-09-04 05:27:18'),
(2, 4, 'Allocated Kshs 4,564.00 to budget ID 6', '2024-09-04 08:11:51'),
(3, 4, 'Allocated Kshs 100,000.00 to budget ID 6', '2024-09-04 08:12:05'),
(4, 4, 'Request ID 6 has been updated to processing.', '2024-09-04 08:21:21'),
(5, NULL, 'Budget ID Has been approved please wait for the allocation of funds.', '2024-09-04 08:59:27'),
(6, NULL, 'Budget ID Has been rejected please review it and submit again.', '2024-09-04 08:59:31'),
(7, 4, 'Your budget request ID 6 has been rejected.', '2024-09-04 09:21:29'),
(8, 4, 'Your budget request ID 2 has been approved.', '2024-09-04 09:21:36'),
(9, 4, 'Your budget request ID 3 has been rejected.', '2024-09-04 09:21:39');

-- --------------------------------------------------------

--
-- Table structure for table `process_history`
--

CREATE TABLE `process_history` (
  `id` int(11) NOT NULL,
  `budget_id` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `requested_by` varchar(50) DEFAULT NULL,
  `review_status` enum('requested','processing','approved','rejected') NOT NULL,
  `item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `department_id`, `date_created`, `requested_by`, `review_status`, `item_id`) VALUES
(2, 4, '2024-09-03 10:11:47', 'EDT1363', 'approved', 8),
(3, 4, '2024-09-03 10:11:47', 'EDT1363', 'rejected', 8),
(4, 4, '2024-09-03 10:19:24', 'EDT1363', 'processing', 8),
(5, 4, '2024-09-03 10:19:24', 'EDT1363', 'processing', 8),
(6, 4, '2024-09-03 10:23:45', 'EDT1363', 'rejected', 8),
(7, 4, '2024-09-03 10:29:21', 'EDT1363', 'requested', 8),
(8, 4, '2024-09-03 10:29:34', 'EDT1363', 'requested', 8),
(9, 4, '2024-09-03 10:29:37', 'EDT1363', 'requested', 8),
(10, 4, '2024-09-03 10:29:38', 'EDT1363', 'requested', 8),
(11, 4, '2024-09-03 11:07:50', 'EDT1363', 'requested', 9);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `types_of_role` enum('admin','viewer','finance_manager','editor','budget_controller') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `types_of_role`) VALUES
(1, 'admin'),
(2, 'viewer'),
(3, 'editor'),
(4, 'finance_manager'),
(5, 'budget_controller');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `username`, `phone_number`, `role_id`, `image`, `email`, `password`, `first_name`, `last_name`, `department_id`) VALUES
(1, 'ADM1502', 'shaddy12', '0757963318', NULL, 'uploads/While do Structure.png', 'odpsha@gmail.com', '', 'Shadrack', 'Odipo', NULL),
(3, 'ADM3446', 'shaddy12', '0757963318', 1, 'uploads/While do Structure.png', 'odpsha@gmail.com', '', 'Shadrack', 'Odipo', 1),
(4, 'ADM2578', 'shaddy12', '0757963318', 1, '../uploads/While do Structure.png', 'odpsha@gmail.com', '', 'Shadrack', 'Odipo', 1),
(5, '2620', 'shaddy12', '0757963318', NULL, NULL, 'odpsha@gmail.com', '', 'Shadrack', 'Odipo', NULL),
(6, 'ADM8796', '21Pilot', '567434567', 1, '../uploads/Figure_1.png', 'pontious@pilate.com', '$2y$10$rfJMnGMkoE4MddrEspQHtOmSiJSG3S.mXawxo2X.Ib.knXPtHNX6S', 'Pontious', 'Pilate', 1),
(7, 'ADM6949', 'admin101', '346674467', 1, '../uploads/Figure_1.png', 'admin@admin.com', '$2y$10$cnfUJjE3/E.WuOo1LcBKr.n3T7BpCeI4oBHDZYdaYF2FO7tWX/6Q6', 'Admin', 'Admino', 1),
(8, 'EDT9644', 'Editor101', '56784567568', 3, '../uploads/if the else structure.png', 'editor@editor.com', '$2y$10$k3/x/QxYbyJJ3Wc/q/1aI.UwOA9BGmcXlXozMVFnAnNJUmh3pOA8C', 'Editorial', 'Editor', 2),
(9, 'VWR1084', 'Viewer101', '34567883', 2, '../uploads/if the else structure.png', 'viewer@gmail.com', '$2y$10$D7uPGK6LSAK7ZeskbMY1feHUG2uk144HrMt6z.c.6Tm15a/mwcWOi', 'View', 'Viewer', 2),
(13, 'EDT1363', 'Editor102', '86567', 3, '../uploads/Gradient.png', 'editort@gmail.com', '$2y$10$LDavfWzPXRtJE1i3HCTQ0uUujk.FCOsQ5Xs.xtxgPRRUiC4WzepJC', 'Editor', 'Technical', 4),
(14, 'VWR4753', 'ViewerT', '654446534', 2, '../uploads/Figure_1.png', 'viewert@gmail.com', '$2y$10$WrqGuXATdF.kD5CyDjFj4Om5OgIKIaMR5MddZ9ze5wKie24Quww3y', 'Veiwert', 'Tech', 4),
(15, 'EDT3940', 'editor103', '456753456', 3, '../uploads/Gradient.png', 'editoro@gmail.com', '$2y$10$Q6.1rG0Eq6yBRiEmNfNCZ.cCxocpnwztkJ5PKWhD28dJefnWTpJfK', 'Edith', 'Mary', 5),
(16, 'BGC1033', 'Budget101', '34567436', 5, '../uploads/Figure_1.png', 'bc@gmail.com', '$2y$10$KKVbjnIAdotm3vrthFsp7OJ.wG8OwY4XAzZ4ARCd.ouBMFwJYQWju', 'Bridgit', 'Bright', NULL),
(17, 'FMG3647', 'Financem', '45674567', 4, '../uploads/if the else structure.png', 'finan@gmail.com', '$2y$10$8el7AfdVkvhR2aXD6i9Yq.l0bKziROexQMW5GCBeP1MPD/AZWUhoy', 'Manager', 'Finance', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `budget_items`
--
ALTER TABLE `budget_items`
  ADD PRIMARY KEY (`budget_id`,`item_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `currency`
--
ALTER TABLE `currency`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `head_of_department` (`head_of_department`);

--
-- Indexes for table `fund_allocations`
--
ALTER TABLE `fund_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budget_id` (`budget_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `process_history`
--
ALTER TABLE `process_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budget_id` (`budget_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `requests_ibfk_2` (`requested_by`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `users_ibfk_2` (`department_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `fund_allocations`
--
ALTER TABLE `fund_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `process_history`
--
ALTER TABLE `process_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `budgets_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `budgets_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`),
  ADD CONSTRAINT `budgets_ibfk_4` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `budget_items`
--
ALTER TABLE `budget_items`
  ADD CONSTRAINT `budget_items_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budget_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`head_of_department`) REFERENCES `users` (`id`);

--
-- Constraints for table `fund_allocations`
--
ALTER TABLE `fund_allocations`
  ADD CONSTRAINT `fund_allocations_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `process_history`
--
ALTER TABLE `process_history`
  ADD CONSTRAINT `process_history_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`),
  ADD CONSTRAINT `process_history_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`requested_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `requests_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
