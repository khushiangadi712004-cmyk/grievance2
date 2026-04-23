-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 21, 2026 at 08:34 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `grievance`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `admin_id` varchar(50) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `mypswd` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_id` (`admin_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `admin_id`, `admin_name`, `email`, `mypswd`, `created_at`) VALUES
(1, 'ADMIN001', 'System Admin', 'admin@gms.com', '$2y$10$J1VQmyhoFNVxMoVGEnlumehqK.s6GKTR99kQd09OwHMid8AOR6tGa', '2026-04-15 09:58:45');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int NOT NULL,
  `category_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`category_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaint`
--

DROP TABLE IF EXISTS `complaint`;
CREATE TABLE IF NOT EXISTS `complaint` (
  `complaint_id` int NOT NULL AUTO_INCREMENT,
  `register_no` int NOT NULL,
  `staff_id` int DEFAULT NULL,
  `category_id` int NOT NULL,
  `department_no` int NOT NULL,
  `description` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file_upload` blob NOT NULL,
  `status` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `remarks` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_submitted` datetime NOT NULL,
  `date_resolved` datetime NOT NULL,
  `escalated_to` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `escalation_reason` text COLLATE utf8mb4_general_ci,
  `escalated_at` datetime DEFAULT NULL,
  `handled_by_role` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`complaint_id`),
  KEY `fk_cat_id` (`category_id`),
  KEY `fk_stud_id` (`register_no`) USING BTREE,
  KEY `fk_dept_no` (`department_no`) USING BTREE,
  KEY `fk_staff_id` (`staff_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaint`
--

INSERT INTO `complaint` (`complaint_id`, `register_no`, `staff_id`, `category_id`, `department_no`, `description`, `file_upload`, `status`, `remarks`, `date_submitted`, `date_resolved`, `escalated_to`, `escalation_reason`, `escalated_at`, `handled_by_role`) VALUES
(1, 0, NULL, 1, 1, 'kjfoieqfyqwif', 0x3133343130313935343032333630353031382e6a7067, 'Pending', '', '2026-03-16 15:21:36', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL),
(2, 0, NULL, 1, 1, 'wefjhfouh', 0x3133343130363836353730363036303637392e6a7067, 'Pending', '', '2026-03-16 15:26:47', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL),
(3, 0, NULL, 1, 1, 'wefjhfouh', 0x3133343130363836353730363036303637392e6a7067, 'Pending', '', '2026-03-16 15:31:27', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL),
(4, 0, NULL, 1, 1, 'xyz', 0x3133343130313935343032333630353031382e6a7067, 'Pending', '', '2026-03-16 15:32:19', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL),
(5, 0, NULL, 1, 1, 'wefjhfouh', 0x3133343130363836353730363036303637392e6a7067, 'Pending', '', '2026-03-16 16:05:11', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL),
(6, 0, NULL, 1, 1, 'light problem', 0x6f70656e746865617472652e6a70672e6a706567, 'Pending', '', '2026-03-24 23:12:18', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL),
(7, 0, NULL, 2, 1, 'Two tube lights in Classroom BCA-204 are not working properly, making evening lectures difficult to attend.', 0x636c617373726f6f6d2d6c696768742d69737375652e6a7067, 'Pending', '', '2026-03-18 09:20:00', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL),
(8, 0, NULL, 2, 2, 'Water leakage is visible near the science block corridor after rainfall, and the floor becomes slippery during class hours.', 0x636f727269646f722d77617465722d6c65616b2e6a7067, 'In Progress', '', '2026-03-20 11:10:00', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL),
(9, 24, NULL, 3, 3, 'light is not working', 0x6c696768742e6a666966, 'Pending', '', '2026-04-21 13:49:22', '0000-00-00 00:00:00', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `complaint_history`
--

DROP TABLE IF EXISTS `complaint_history`;
CREATE TABLE IF NOT EXISTS `complaint_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `complaint_id` int DEFAULT NULL,
  `source_type` varchar(20) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `remarks` text,
  `handled_by_role` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deparment`
--

DROP TABLE IF EXISTS `deparment`;
CREATE TABLE IF NOT EXISTS `deparment` (
  `department_no` int NOT NULL,
  `department_name` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hod_id` int NOT NULL,
  PRIMARY KEY (`department_no`),
  KEY `fk_hod_id` (`hod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

DROP TABLE IF EXISTS `designations`;
CREATE TABLE IF NOT EXISTS `designations` (
  `design` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`design`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hod`
--

DROP TABLE IF EXISTS `hod`;
CREATE TABLE IF NOT EXISTS `hod` (
  `hod_id` varchar(50) NOT NULL,
  `hod_name` varchar(100) NOT NULL,
  `department_no` int NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`hod_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hod`
--

INSERT INTO `hod` (`hod_id`, `hod_name`, `department_no`, `email`, `password`) VALUES
('HOD001', 'Department HOD', 1, 'hod@gms.com', '$2y$10$J1VQmyhoFNVxMoVGEnlumehqK.s6GKTR99kQd09OwHMid8AOR6tGa');

-- --------------------------------------------------------

--
-- Table structure for table `management`
--

DROP TABLE IF EXISTS `management`;
CREATE TABLE IF NOT EXISTS `management` (
  `management_id` int NOT NULL AUTO_INCREMENT,
  `mname` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`management_id`),
  KEY `fk_mang_id` (`management_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `management`
--

INSERT INTO `management` (`management_id`, `mname`, `password`) VALUES
(201, 'Management One', '$2y$12$xUIhKtwrqHiTUI0/s5BbZePzv925x1v9SQLZIkPQ4MptsNn55xY4e'),
(202, 'Management Two', '$2y$12$WPbtPYGSM9y61vHZ73tgouakdlt/CfSho5RGbP6Gx41VHb0pZRQCC');

-- --------------------------------------------------------

--
-- Table structure for table `notiification`
--

DROP TABLE IF EXISTS `notiification`;
CREATE TABLE IF NOT EXISTS `notiification` (
  `notification_id` int NOT NULL,
  `complaint_id` int NOT NULL,
  `user_type` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_sent` datetime NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `fk_comp_id` (`complaint_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `principal`
--

DROP TABLE IF EXISTS `principal`;
CREATE TABLE IF NOT EXISTS `principal` (
  `principal_id` varchar(50) NOT NULL,
  `principal_name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`principal_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `principal`
--

INSERT INTO `principal` (`principal_id`, `principal_name`, `email`, `password`) VALUES
('PRINCIPAL001', 'College Principal', 'principal@gms.com', '$2y$10$J1VQmyhoFNVxMoVGEnlumehqK.s6GKTR99kQd09OwHMid8AOR6tGa');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
CREATE TABLE IF NOT EXISTS `staff` (
  `staff_id` int NOT NULL,
  `stname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_no` int DEFAULT NULL,
  `email` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `phone_no` int DEFAULT NULL,
  `design` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`staff_id`),
  KEY `designation` (`design`),
  KEY `fk_dept_no` (`department_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `stname`, `department_no`, `email`, `password`, `created_at`, `phone_no`, `design`) VALUES
(101, 'Support Staff', 1, 'staff@gms.com', '$2y$10$J1VQmyhoFNVxMoVGEnlumehqK.s6GKTR99kQd09OwHMid8AOR6tGa', '2026-04-15 16:24:22', 987654321, NULL),
(201, 'Management One', 1, 'management1@gms.com', '$2y$12$xUIhKtwrqHiTUI0/s5BbZePzv925x1v9SQLZIkPQ4MptsNn55xY4e', '2026-04-23 10:00:00', 987650001, NULL),
(202, 'Management Two', 1, 'management2@gms.com', '$2y$12$WPbtPYGSM9y61vHZ73tgouakdlt/CfSho5RGbP6Gx41VHb0pZRQCC', '2026-04-23 10:05:00', 987650002, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff_complaint`
--

DROP TABLE IF EXISTS `staff_complaint`;
CREATE TABLE IF NOT EXISTS `staff_complaint` (
  `complaint_id` int NOT NULL AUTO_INCREMENT,
  `staff_id` int NOT NULL,
  `category_id` int NOT NULL,
  `department_no` int NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `file_upload` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `date_submitted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `escalated_to` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `escalation_reason` text COLLATE utf8mb4_general_ci,
  `escalated_at` datetime DEFAULT NULL,
  `handled_by_role` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`complaint_id`),
  KEY `fk_staff_complaint_staff` (`staff_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_complaint`
--

INSERT INTO `staff_complaint` (`complaint_id`, `staff_id`, `category_id`, `department_no`, `description`, `file_upload`, `status`, `date_submitted`, `escalated_to`, `escalation_reason`, `escalated_at`, `handled_by_role`) VALUES
(1, 101, 1, 2, 'Classroom Fan is not working', 'classroom_image.png', 'Pending', '2026-04-15 16:45:27', 'HOD', 'not able to solve', '2026-04-16 17:39:30', 'Admin'),
(2, 101, 2, 2, 'qwertyuiop', 'WhatsApp Image 2026-04-15 at 3.58.25 PM.jpeg', 'Pending', '2026-04-16 14:05:35', 'HOD', 'categorized to hod so', '2026-04-21 13:42:07', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `register_no` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mypswd` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `department_no` int DEFAULT NULL,
  `semester` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`register_no`),
  KEY `idxdno` (`department_no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`register_no`, `sname`, `email`, `mypswd`, `phone`, `created_at`, `department_no`, `semester`) VALUES
('24BBA104', 'Priya Nair', 'priya.nair@gms.com', '$2y$10$wwGy6pDlYsvrcAggmRketuRIJkgj4dl0460hnzgB3hbXkVrs1kSKK', 2147483647, '2026-04-15 17:26:17', 4, ''),
('24BCA101', 'Aarav Sharma', 'aarav.sharma@gms.com', '$2y$10$wwGy6pDlYsvrcAggmRketuRIJkgj4dl0460hnzgB3hbXkVrs1kSKK', 2147483647, '2026-04-15 17:26:17', 1, ''),
('24BCA105', 'Kiran Patel', 'kiran.patel@gms.com', '$2y$10$cbC5SevZsJvOgYFg/j/qg.DsCpQfX4HvSD7IomruwWcXuWyPV/eUi', 2147483647, '2026-04-15 17:26:17', 1, ''),
('24BCOM103', 'Rohit Mehta', 'rohit.mehta@gms.com', '$2y$10$RE9pSTuP2X3u6kojmBg0m.DtjucUTg2em30volhoZbDA1WwEoksMW', 2147483647, '2026-04-15 17:26:17', 3, ''),
('24BSC102', 'Nisha Verma', 'nisha.verma@gms.com', '$2y$10$cbC5SevZsJvOgYFg/j/qg.DsCpQfX4HvSD7IomruwWcXuWyPV/eUi', 2147483647, '2026-04-15 17:26:17', 2, ''),
('U02BF23S0167', 'nakshatra', 'nakshatra123@gmail.com', '$2y$10$bARYtUFEMm2UAQ7q9KaHl.f3fJzAJK9xdEF0wZF9rBB4stW2zFIBS', 2147483647, '2026-03-24 23:17:39', NULL, ''),
('U02BF23S0347', 'Khushi Angadi', 'khushiangadi712004@gmail.com', '$2y$10$fbFqWPV6C6YZtuA7p3I82e/WphBGhwHcurrfxLI83w8Z3od1Mhu6.', 2147483647, NULL, NULL, '');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `management`
--
ALTER TABLE `management`
  ADD CONSTRAINT `management_ibfk_1` FOREIGN KEY (`management_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff-fkdesign` FOREIGN KEY (`design`) REFERENCES `designations` (`design`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
