-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2025 at 08:05 AM
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
-- Database: `library_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(10) UNSIGNED NOT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `year_published` int(4) DEFAULT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(6,2) NOT NULL DEFAULT 0.00,
  `copies_total` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `copies_available` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `target_user` enum('student','teacher','all') NOT NULL DEFAULT 'all',
  `status` enum('active','archive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `isbn`, `title`, `author`, `publisher`, `year_published`, `category_id`, `description`, `price`, `copies_total`, `copies_available`, `target_user`, `status`, `created_at`, `updated_at`) VALUES
(1, '978-0131103627', 'Introduction to Algorithms', 'Thomas H. Cormen', 'MIT Press', 2009, 1, 'Comprehensive guide to modern algorithms.', 300.00, 10, 8, 'all', 'active', '2025-11-26 03:51:39', '2025-12-06 07:39:08'),
(2, '978-0321573513', 'Discrete Mathematics and Its Applications', 'Kenneth Rosen', 'McGraw-Hill', 2012, 2, 'Foundational mathematics for computer science.', 500.00, 8, 8, 'all', 'active', '2025-11-26 03:51:39', '2025-12-03 10:14:36'),
(3, '978-0199291151', 'Principles of Economics', 'N. Gregory Mankiw', 'Oxford University Press', 2014, 4, 'Basic principles of micro and macroeconomics.', 340.50, 12, 12, 'all', 'active', '2025-11-26 03:51:39', '2025-11-29 12:36:14'),
(4, '978-1118472100', 'Accounting Principles', 'Jerry Weygandt', 'Wiley', 2015, 5, 'Introduction to financial accounting.', 430.00, 7, 5, 'teacher', 'active', '2025-11-26 03:51:39', '2025-12-02 13:15:41'),
(5, '978-0134093413', 'Physics for Scientists and Engineers', 'Douglas C. Giancoli', 'Pearson', 2016, 6, 'Comprehensive physics textbook for science students.', 450.00, 15, 14, 'all', 'active', '2025-11-26 03:51:39', '2025-12-03 10:14:36'),
(6, '978-0262033848', 'Artificial Intelligence: A Modern Approach', 'Stuart Russell', 'Pearson', 2010, 1, 'The standard textbook on AI.', 320.00, 6, 4, 'all', 'active', '2025-11-26 03:51:39', '2025-12-07 16:17:52'),
(7, '978-0123748560', 'Linear Algebra Done Right', 'Sheldon Axler', 'Springer', 2015, 2, 'Linear algebra theory with a focus on vector spaces.', 300.00, 6, 6, 'all', 'active', '2025-11-26 03:51:39', '2025-11-29 12:37:20'),
(8, '978-0137053460', 'Microeconomics', 'Paul Krugman', 'Worth Publishers', 2013, 4, 'Focuses on supply and demand and market behavior.', 670.00, 9, 9, 'all', 'active', '2025-11-26 03:51:39', '2025-12-03 10:14:36'),
(9, '978-1118094945', 'Business Law', 'Henry Cheeseman', 'Pearson', 2014, 5, 'Understanding legal environment of business.', 640.00, 10, 9, 'all', 'active', '2025-11-26 03:51:39', '2025-12-02 15:59:08'),
(10, '978-0321570892', 'Chemistry: The Central Science', 'Brown, LeMay, Bursten', 'Pearson', 2016, 6, 'Comprehensive chemistry textbook.', 350.00, 8, 8, 'all', 'active', '2025-11-26 03:51:39', '2025-12-03 10:14:36'),
(11, '978-0132350884', 'Clean Code', 'Robert C. Martin', 'Prentice Hall', 2008, 1, 'A Handbook of Agile Software Craftsmanship.', 200.00, 7, 6, 'teacher', 'active', '2025-11-26 03:51:39', '2025-11-29 12:38:21'),
(12, '978-1118713283', 'Calculus: Early Transcendentals', 'James Stewart', 'Cengage', 2015, 2, 'Complete guide to differential and integral calculus.', 510.00, 12, 9, 'all', 'active', '2025-11-26 03:51:39', '2025-12-04 15:52:46');

-- --------------------------------------------------------

--
-- Table structure for table `borrows`
--

CREATE TABLE `borrows` (
  `borrow_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `book_id` int(11) UNSIGNED NOT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` datetime DEFAULT NULL,
  `fine_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fine_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('borrowed','returned','overdue') NOT NULL DEFAULT 'borrowed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrows`
--

INSERT INTO `borrows` (`borrow_id`, `user_id`, `book_id`, `borrow_date`, `due_date`, `return_date`, `fine_amount`, `fine_paid`, `status`) VALUES
(1, 1, 6, '2025-12-07', '2025-12-21', NULL, 0.00, 0.00, 'borrowed'),
(2, 1, 9, '2025-12-07', '2025-12-14', NULL, 0.00, 0.00, 'borrowed');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_requests`
--

CREATE TABLE `borrow_requests` (
  `request_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `book_id` int(10) UNSIGNED NOT NULL,
  `request_type` enum('borrow','reserve') NOT NULL DEFAULT 'borrow',
  `request_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `staff_id` int(10) UNSIGNED DEFAULT NULL,
  `decision_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_requests`
--

INSERT INTO `borrow_requests` (`request_id`, `user_id`, `book_id`, `request_type`, `request_date`, `status`, `staff_id`, `decision_date`) VALUES
(1, 1, 6, 'borrow', '2025-12-08 00:10:17', 'approved', 5, '2025-12-08 00:17:52');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `created_at`, `updated_at`) VALUES
(1, 'Computer Science', '2025-11-25 16:17:07', '2025-11-25 19:04:19'),
(2, 'Mathematics', '2025-11-25 16:44:41', '2025-11-25 19:06:10'),
(4, 'Economics', '2025-11-25 19:04:52', '2025-11-25 19:06:27'),
(5, 'Business', '2025-11-25 19:08:39', '2025-11-25 19:08:39'),
(6, 'Science', '2025-11-25 19:09:04', '2025-11-25 19:09:04');

-- --------------------------------------------------------

--
-- Table structure for table `clearance`
--

CREATE TABLE `clearance` (
  `clearance_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `cleared_by` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('cleared','pending') NOT NULL DEFAULT 'pending',
  `cleared_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penalties`
--

CREATE TABLE `penalties` (
  `penalty_id` int(11) NOT NULL,
  `borrow_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `book_id` int(11) UNSIGNED NOT NULL,
  `reservation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `ready_date` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `user_id`, `book_id`, `reservation_date`, `status`, `ready_date`, `expiry_date`) VALUES
(1, 1, 9, '2025-12-07 17:10:26', 'picked_up', '2025-12-07 17:17:58', '2025-12-10 17:17:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `midint` char(1) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','teacher','librarian','staff') NOT NULL DEFAULT 'student',
  `clearance_status` enum('cleared','barred') NOT NULL DEFAULT 'cleared',
  `status` enum('inactive','active','suspended') DEFAULT 'inactive',
  `remember_token` varchar(100) DEFAULT NULL,
  `password_reset_token` varchar(100) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `midint`, `lastname`, `email`, `username`, `password`, `role`, `clearance_status`, `status`, `remember_token`, `password_reset_token`, `password_reset_expires`, `created_at`, `updated_at`) VALUES
(1, 'Rhazeanneth', 'A', 'Ajoc', 'aguilaranneth@gmail.com', 'LuvRhaz', '$2y$10$DY1.WXTkEO4uWF54APUitOCFe1lPYxMxTO7sNp4qa1q1rzXXna78O', 'student', 'cleared', 'active', 'a35851c42a8652f4cc3824db778075b55bcc8862014812746d73a2f538b0ab07', NULL, NULL, '2025-11-24 17:32:48', '2025-12-08 06:21:41'),
(2, 'Ashley', 'R', 'Anderson', 'sciensalvarastra@gmail.com', 'Librarian_Ash', '$2y$10$A4yEqzAOoXOXETHW09TTFOTbbmi3qjFOWtwfU4T1B3VXsJrTQWx/G', 'librarian', 'cleared', 'active', '05e905525c30ac2619adaef70535fa51cadf74d808b5ba264c23d5c5477b3acf', NULL, NULL, '2025-11-24 17:59:17', '2025-12-08 06:20:35'),
(3, 'Rave', NULL, 'Agoncillo', 'raveagoncillo@gmail.com', 'Student_Rave', '$2y$10$HWtR2Qdc84Hwjpi/2ShTO.cZKhkDkCaxHXHLyu.cceTrMUYf2zEeq', 'student', 'cleared', 'active', NULL, NULL, NULL, '2025-11-26 06:55:18', '2025-11-26 06:55:18'),
(4, 'Loni', 'E', 'Aguilar', 'loni_aguilar@gmail.com', 'Teacher_Loni', '$2y$10$1/TxlNoVmcxouMyJOLypZuf0Ola6EBvw3PLmDxCovjVxSnBOvqTPe', 'teacher', 'cleared', 'active', '30d685fc88fd5ebceecf65539441a82a967e6203cfb2d70b73177696e0462ba8', NULL, NULL, '2025-11-28 10:21:19', '2025-12-08 06:18:13'),
(5, 'Allan', NULL, 'Cayetano', 'allan_cayetano@gmail.com', 'Staff_Allan', '$2y$10$Oe2WZzc.a524QXNEiMsc1.QjlxAT1OcVFOmhWq3MIJIMur0aqVLA.', 'staff', 'cleared', 'active', '2258c587142e1db74a088a89d6d29520eb47dd91bf9370061f11d44bbe16cd98', NULL, NULL, '2025-12-02 00:48:28', '2025-12-08 05:07:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `fk_book_category` (`category_id`);

--
-- Indexes for table `borrows`
--
ALTER TABLE `borrows`
  ADD PRIMARY KEY (`borrow_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `clearance`
--
ALTER TABLE `clearance`
  ADD PRIMARY KEY (`clearance_id`),
  ADD KEY `fk_clearance_user` (`user_id`),
  ADD KEY `fk_clearance_staff` (`cleared_by`);

--
-- Indexes for table `penalties`
--
ALTER TABLE `penalties`
  ADD PRIMARY KEY (`penalty_id`),
  ADD KEY `fk_penalty_user` (`user_id`),
  ADD KEY `fk_penalty_borrow` (`borrow_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `fk_reservation_user` (`user_id`),
  ADD KEY `fk_reservation_book` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `borrows`
--
ALTER TABLE `borrows`
  MODIFY `borrow_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  MODIFY `request_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `clearance`
--
ALTER TABLE `clearance`
  MODIFY `clearance_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penalties`
--
ALTER TABLE `penalties`
  MODIFY `penalty_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `fk_book_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `borrows`
--
ALTER TABLE `borrows`
  ADD CONSTRAINT `borrows_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `borrows_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON UPDATE CASCADE;

--
-- Constraints for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD CONSTRAINT `borrow_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `borrow_requests_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Constraints for table `clearance`
--
ALTER TABLE `clearance`
  ADD CONSTRAINT `fk_clearance_staff` FOREIGN KEY (`cleared_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_clearance_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `penalties`
--
ALTER TABLE `penalties`
  ADD CONSTRAINT `fk_penalty_borrow` FOREIGN KEY (`borrow_id`) REFERENCES `borrows` (`borrow_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_penalty_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_reservation_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reservation_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
