-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 24, 2023 at 11:40 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `id20957725_maison`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE `administrator` (
  `personnel_ID` varchar(10) NOT NULL,
  `admin_title` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `q_ID` varchar(10) NOT NULL,
  `personnel_ID` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`q_ID`, `personnel_ID`, `app_datetime`) VALUES
('078', 'SM123', '2023-06-24 00:00:00'),
('135', 'SM123', '2023-06-24 00:00:00'),
('184', 'SM123', '2023-06-24 00:00:00'),
('369', NULL, '2023-06-23 10:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `cbq`
--

CREATE TABLE `cbq` (
  `cbq_X` int NOT NULL,
  `cbq_Y` int NOT NULL,
  `PRESET` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cbq_active` varchar(2) NOT NULL DEFAULT 'F',
  `cbq_minSupport` int NOT NULL,
  `cbq_maxSupport` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cbq`
--

INSERT INTO `cbq` (`cbq_X`, `cbq_Y`, `PRESET`, `cbq_active`, `cbq_minSupport`, `cbq_maxSupport`) VALUES
(4, 35, 'HIGH', 'F', 9, 999),
(1, 15, 'LOW', 'F', 0, 4),
(2, 25, 'MEDIUM', 'T', 5, 8);

-- --------------------------------------------------------

--
-- Table structure for table `clinic`
--

CREATE TABLE `clinic` (
  `clinic_capacity` int NOT NULL,
  `clinic_LatLng` varchar(1000) NOT NULL,
  `clinic_maxRadius` float NOT NULL,
  `clinic_SLQMaxSize` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clinic`
--

INSERT INTO `clinic` (`clinic_capacity`, `clinic_LatLng`, `clinic_maxRadius`, `clinic_SLQMaxSize`) VALUES
(10, '5.262512978963726, 103.1650855825122', 35, 5);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_code` varchar(3) NOT NULL,
  `dept_name` varchar(50) NOT NULL,
  `dept_desc` varchar(100) DEFAULT NULL,
  `dept_headCount` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dept_code`, `dept_name`, `dept_desc`, `dept_headCount`) VALUES
('GEN', 'General Unit', 'gen', 5),
('NA', 'DELETED', 'NULL', 0),
('PHA', 'Pharmacy', 'pharmacy meow2', 0);

-- --------------------------------------------------------

--
-- Table structure for table `encounter`
--

CREATE TABLE `encounter` (
  `enc_ID` varchar(10) NOT NULL,
  `q_ID` varchar(10) NOT NULL,
  `personnel_ID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general`
--

CREATE TABLE `general` (
  `q_ID` varchar(10) NOT NULL,
  `q_arriveTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `head_department`
--

CREATE TABLE `head_department` (
  `dept_code` varchar(3) NOT NULL,
  `personnel_ID` varchar(10) NOT NULL,
  `head_title` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `operation`
--

CREATE TABLE `operation` (
  `personnel_ID` varchar(10) NOT NULL,
  `op_position` varchar(30) NOT NULL,
  `op_area` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `operation`
--

INSERT INTO `operation` (`personnel_ID`, `op_position`, `op_area`) VALUES
('GEN-2023-3', 'SURGERY', 'NAORTICS');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `patient_ICNum` varchar(14) NOT NULL,
  `patient_name` varchar(50) NOT NULL,
  `patient_gender` varchar(4) NOT NULL,
  `patient_age` int NOT NULL,
  `patient_email` varchar(30) NOT NULL,
  `patient_phoneNum` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patient_ICNum`, `patient_name`, `patient_gender`, `patient_age`, `patient_email`, `patient_phoneNum`) VALUES
('000927-10-2521', 'SEKAI', 'M', 22, 'mrahmatm@gmail.com', '018-6962570'),
('126834-95-5379', 'Matthew Wilson', 'M', 45, 'matthew.wilson@example.com', '+6016-7890123'),
('367512-48-2241', 'Sarah Davis', 'F', 61, 'sarah.davis@example.com', '+6011-2345678'),
('432819-62-1752', 'Jennifer Taylor', 'F', 53, 'jennifer.taylor@example.com', '+6017-8901234'),
('479513-07-2885', 'Olivia Martinez', 'F', 29, 'olivia.martinez@example.com', '+6012-3456789'),
('546218-51-8937', 'Michael Brown', 'M', 47, 'michael.brown@example.com', '+6013-5678901'),
('745129-27-9823', 'Emily Johnson', 'F', 42, 'emily.johnson@example.com', '+6016-7890123'),
('753916-73-3625', 'Christopher Lee', 'M', 31, 'christopher.lee@example.com', '+6018-9012345'),
('821347-37-5593', 'David Williams', 'M', 28, 'david.williams@example.com', '+6019-4567890'),
('925416-84-8142', 'Jessica Anderson', 'F', 39, 'jessica.anderson@example.com', '+6014-3456789'),
('934826-15-6732', 'John Smith', 'M', 35, 'john.smith@example.com', '+6012-3456789'),
('980623-10-9186', 'JIYOUNG', 'F', 25, 'baek@gmail.com', '018-99932844'),
('990925-10-9856', 'FROHZE', 'M', 22, 'frohze@gmail.com', '01966324');

-- --------------------------------------------------------

--
-- Table structure for table `personnel`
--

CREATE TABLE `personnel` (
  `personnel_ID` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `personnel_ICNum` varchar(14) NOT NULL,
  `personnel_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `personnel_gender` varchar(3) NOT NULL,
  `personnel_age` int NOT NULL,
  `personnel_email` varchar(30) NOT NULL,
  `personnel_phoneNum` varchar(15) NOT NULL,
  `personnel_type` varchar(20) NOT NULL,
  `personnel_attend` varchar(5) DEFAULT 'F',
  `dept_code` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personnel`
--

INSERT INTO `personnel` (`personnel_ID`, `personnel_ICNum`, `personnel_name`, `personnel_gender`, `personnel_age`, `personnel_email`, `personnel_phoneNum`, `personnel_type`, `personnel_attend`, `dept_code`) VALUES
('GEN-2023-3', '990612-10-3433', 'YUUMA', 'M', 24, 'yuuma@hotmail.com', '019-2229182', 'DR', 'F', 'GEN'),
('GEN-331', '000816-10-2681', 'FUMINA', 'F', 23, 'winning@gmail.com', '019-3332571', 'DR', 'T', 'GEN'),
('SM123', '001214-10-2681', 'SEKAI', 'M', 24, 'burning@gmail.com', '019-3332571', 'DR', 'F', 'GEN'),
('test', '000', 'SIYEON', '', 30, 'siyeonmeow@gmail.com', '', '', 'F', 'GEN'),
('test1', '000', 'DAMI', '', 25, 'damiii@gmail.com', '', '', 'F', 'GEN');

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE `queue` (
  `q_ID` varchar(10) NOT NULL,
  `q_before` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q_after` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q_type` varchar(3) NOT NULL,
  `patient_ICNum` varchar(14) NOT NULL,
  `svc_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `queue`
--

INSERT INTO `queue` (`q_ID`, `q_before`, `q_after`, `q_type`, `patient_ICNum`, `svc_code`) VALUES
('169', '483', '789', 'TTT', '934826-15-6732', NULL),
('289', '804', NULL, 'APQ', '367512-48-2241', NULL),
('318', '520', '391', 'GPQ', '432819-62-1752', NULL),
('391', '318', NULL, 'GPQ', '432819-62-1752', NULL),
('483', '587', '169', 'TTT', '934826-15-6732', NULL),
('519', NULL, '520', 'GPQ', '367512-48-2241', NULL),
('520', '519', '318', 'GPQ', '367512-48-2241', NULL),
('587', NULL, '483', 'TTT', '934826-15-6732', NULL),
('789', '169', NULL, 'TTT', '934826-15-6732', NULL),
('804', 'X11', '289', 'APQ', '367512-48-2241', NULL),
('X11', NULL, '804', 'APQ', '546218-51-8937', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `svc_code` varchar(10) NOT NULL,
  `svc_desc` varchar(40) NOT NULL,
  `svc_fee` float DEFAULT NULL,
  `dept_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`svc_code`, `svc_desc`, `svc_fee`, `dept_code`) VALUES
('BLT', 'Blood Test.', NULL, 'GEN'),
('DCK', 'Daily checkups.', 2, 'GEN');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`personnel_ID`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`q_ID`),
  ADD KEY `personnel_origin_app` (`personnel_ID`);

--
-- Indexes for table `cbq`
--
ALTER TABLE `cbq`
  ADD PRIMARY KEY (`PRESET`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dept_code`);

--
-- Indexes for table `encounter`
--
ALTER TABLE `encounter`
  ADD PRIMARY KEY (`enc_ID`,`q_ID`),
  ADD KEY `q_origin_enc` (`q_ID`),
  ADD KEY `personnel_origin_enc` (`personnel_ID`);

--
-- Indexes for table `general`
--
ALTER TABLE `general`
  ADD PRIMARY KEY (`q_ID`);

--
-- Indexes for table `head_department`
--
ALTER TABLE `head_department`
  ADD PRIMARY KEY (`dept_code`),
  ADD KEY `personnel_origin_hd` (`personnel_ID`);

--
-- Indexes for table `operation`
--
ALTER TABLE `operation`
  ADD PRIMARY KEY (`personnel_ID`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patient_ICNum`);

--
-- Indexes for table `personnel`
--
ALTER TABLE `personnel`
  ADD PRIMARY KEY (`personnel_ID`),
  ADD KEY `personnel_dept` (`dept_code`);

--
-- Indexes for table `queue`
--
ALTER TABLE `queue`
  ADD PRIMARY KEY (`q_ID`),
  ADD KEY `patient_queue` (`patient_ICNum`),
  ADD KEY `svc_queue` (`svc_code`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`svc_code`),
  ADD KEY `svc_dept` (`dept_code`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `administrator`
--
ALTER TABLE `administrator`
  ADD CONSTRAINT `personnel_origin_admin` FOREIGN KEY (`personnel_ID`) REFERENCES `personnel` (`personnel_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `personnel_origin_app` FOREIGN KEY (`personnel_ID`) REFERENCES `personnel` (`personnel_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `encounter`
--
ALTER TABLE `encounter`
  ADD CONSTRAINT `personnel_origin_enc` FOREIGN KEY (`personnel_ID`) REFERENCES `personnel` (`personnel_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `q_origin_enc` FOREIGN KEY (`q_ID`) REFERENCES `queue` (`q_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `general`
--
ALTER TABLE `general`
  ADD CONSTRAINT `q_origin_gen` FOREIGN KEY (`q_ID`) REFERENCES `queue` (`q_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `head_department`
--
ALTER TABLE `head_department`
  ADD CONSTRAINT `dept_origin` FOREIGN KEY (`dept_code`) REFERENCES `department` (`dept_code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `personnel_origin_hd` FOREIGN KEY (`personnel_ID`) REFERENCES `personnel` (`personnel_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `operation`
--
ALTER TABLE `operation`
  ADD CONSTRAINT `personnel_origin` FOREIGN KEY (`personnel_ID`) REFERENCES `personnel` (`personnel_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `queue`
--
ALTER TABLE `queue`
  ADD CONSTRAINT `patient_queue` FOREIGN KEY (`patient_ICNum`) REFERENCES `patient` (`patient_ICNum`) ON UPDATE CASCADE,
  ADD CONSTRAINT `svc_queue` FOREIGN KEY (`svc_code`) REFERENCES `service` (`svc_code`) ON UPDATE CASCADE;

--
-- Constraints for table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `svc_dept` FOREIGN KEY (`dept_code`) REFERENCES `department` (`dept_code`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
