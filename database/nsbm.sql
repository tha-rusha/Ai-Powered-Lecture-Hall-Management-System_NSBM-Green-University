-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2025 at 12:11 PM
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
-- Database: `nsbm`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `lecturer_name` varchar(120) NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,
  `time_slot` varchar(20) NOT NULL,
  `expected_size` int(11) NOT NULL,
  `hall_id` varchar(50) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `title` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `code`, `title`) VALUES
(1, 'FOC-DS', 'BSc (Hons) in Data Science'),
(2, 'FOC-CN', 'BSc (Hons) in Computer Networks'),
(3, 'FOC-CS', 'BSc (Hons) in Computer Science'),
(4, 'FOC-SE', 'BSc (Hons) in Software Engineering'),
(5, 'FOC-MIS', 'BSc in Management Information Systems (Special)'),
(6, 'FOC-FOUND', 'Foundation Programme for Bachelor’s Degree'),
(7, 'VU-BIT-CSEC', 'Bachelor of Information Technology (Major in Cyber Security)'),
(8, 'PU-TM', 'BSc (Hons) Technology Management'),
(9, 'PU-CS', 'BSc (Hons) Computer Science'),
(10, 'PU-CN', 'BSc (Hons) Computer Networks'),
(11, 'PU-CSEC', 'BSc (Hons) Computer Security'),
(12, 'PU-SE', 'BSc (Hons) Software Engineering'),
(13, 'PU-DS', 'BSc (Hons) Data Science'),
(14, 'PU-AI', 'BSc (Hons) Artificial Intelligence');

-- --------------------------------------------------------

--
-- Table structure for table `halls`
--

CREATE TABLE `halls` (
  `id` varchar(50) NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(120) NOT NULL,
  `capacity` int(11) NOT NULL,
  `equipment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `halls`
--

INSERT INTO `halls` (`id`, `code`, `name`, `capacity`, `equipment`) VALUES
('1', 'C2-009', 'C2-009 (Lecture Hall)', 350, 'Projector,Smart Board,Wi-Fi,Sound System'),
('10', 'C2-L110', 'C2-L110 (Lecture Hall)', 150, 'Projector,Smart Board,Wi-Fi'),
('11', 'C2-L109', 'C2-L109 (Network Lab)', 100, 'Lab PCs,Projector,Wi-Fi,Networking Gear'),
('12', 'C2-L202', 'C2-L202 (Lecture Hall)', 50, 'Projector,Wi-Fi'),
('13', 'C2-L203', 'C2-L203 (Lecture Hall)', 50, 'Projector,Wi-Fi'),
('14', 'C2-105', 'C2-105 (Lecture Hall)', 200, 'Projector,Smart Board,Wi-Fi'),
('15', 'C2-106', 'C2-106 (Lecture Hall)', 100, 'Projector,Wi-Fi'),
('16', 'C2-007', 'C2-007 (Lab)', 50, 'Lab PCs,Projector,Wi-Fi'),
('17', 'C2-008', 'C2-008 (Lab)', 50, 'Lab PCs,Projector,Wi-Fi'),
('18', 'C2-L106', 'C2-L106 (Lab)', 50, 'Lab PCs,Projector,Wi-Fi'),
('19', 'C2-L107', 'C2-L107 (Lab)', 50, 'Lab PCs,Projector,Wi-Fi'),
('2', 'C2-002', 'C2-002 (Lecture Hall)', 250, 'Projector,Smart Board,Wi-Fi'),
('20', 'C2-L204', 'C2-L204 (Lab)', 50, 'Lab PCs,Projector,Wi-Fi'),
('21', 'C2-L205', 'C2-L205 (Lab)', 50, 'Lab PCs,Projector,Wi-Fi'),
('22', 'C2-103', 'C2-103 (Lab)', 70, 'Lab PCs,Projector,Wi-Fi'),
('3', 'C2-003', 'C2-003 (Lecture Hall)', 120, 'Projector,Wi-Fi'),
('4', 'C2-005', 'C2-005 (Lecture Hall)', 50, 'Projector,Wi-Fi'),
('5', 'C2-006', 'C2-006 (Lecture Hall)', 50, 'Projector,Wi-Fi'),
('6', 'C2-L101', 'C2-L101 (Lecture Hall)', 175, 'Projector,Smart Board,Wi-Fi'),
('7', 'C2-L102', 'C2-L102 (Lecture Hall)', 175, 'Projector,Smart Board,Wi-Fi'),
('8', 'C2-L104', 'C2-L104 (Lecture Hall)', 50, 'Projector,Wi-Fi'),
('9', 'C2-L105', 'C2-L105 (Lecture Hall)', 50, 'Projector,Wi-Fi');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','lecturer','student') NOT NULL DEFAULT 'lecturer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@nsbm.ac.lk', '$2y$10$TgJ9QFypGBuI2xH3C8EBMe0Y02vCKD/1q4E8rfPGpt.yD8cnXQASq', 'admin', '2025-09-13 01:01:57'),
(2, 'Dr. Silva', 'silva@nsbm.ac.lk', '$2y$10$RxH0fC4oQAJtuZu8RQfxtuYJYlw5YhRxMljWxI4xUDFQv1lQhLO7q', 'lecturer', '2025-09-13 01:01:57'),
(3, 'Ms. Perera', 'perera@nsbm.ac.lk', '$2y$10$nnfQiMbEbnN.jgU6cT.CAeZAgxKjzS9CAAK1tGQAo7AX9blhOGpBu', 'lecturer', '2025-09-13 01:01:57'),
(4, 'Mr. Fernando', 'fernando@nsbm.ac.lk', '$2y$10$XMHni8l3o95VTIJ8SkLU8ek14O9I/mRwcQbSUT6U1YpSc9zPQW1Ia', 'lecturer', '2025-09-13 01:01:57'),
(5, 'Ms. Jayasinghe', 'jayasinghe@nsbm.ac.lk', '$2y$10$Jq7pKh.W2D/uD6OjXZeDzOiI8NChc8nBPJdc94UVxYwYzNH9QloSu', 'lecturer', '2025-09-13 01:01:57'),
(6, 'Prof. Rathnayake', 'rathnayake@nsbm.ac.lk', '$2y$10$XbH0HZ3pIUGQeeQhP6tA2u4hXRYfPG1sY4rXwBPYW8FQb0H4y.GD.', 'lecturer', '2025-09-13 01:01:57'),
(7, 'Dr. Kumara', 'kumara@nsbm.ac.lk', '$2y$10$V9Xy3zkh0snEkmnsOqZwguYkD5F9fnmqS8H4HVFt23keOxqRfGSua', 'lecturer', '2025-09-13 01:01:57'),
(8, 'Ms. Gunasekara', 'gunasekara@nsbm.ac.lk', '$2y$10$Q6eZy0ujSHJd0Am9IsD7Re85n3YgeVxsy5sOBR47vnA0xAmkDGlPq', 'lecturer', '2025-09-13 01:01:57'),
(9, 'Mr. Abeysinghe', 'abeysinghe@nsbm.ac.lk', '$2y$10$YoF2n8cqBZ/0oUMzjrsye.3RB8k4c4oFAlP2GeaNOe7v6D7ShD5Qa', 'lecturer', '2025-09-13 01:01:57'),
(10, 'Dr. Wijesinghe', 'wijesinghe@nsbm.ac.lk', '$2y$10$0CkWoyN4Pj/RR.cPjqOnluqWzLzSozkFXtsw5OfVjxl4vHJJGqM9y', 'lecturer', '2025-09-13 01:01:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_course` (`course_code`),
  ADD KEY `fk_hall` (`hall_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `halls`
--
ALTER TABLE `halls`
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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_course` FOREIGN KEY (`course_code`) REFERENCES `courses` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hall` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
