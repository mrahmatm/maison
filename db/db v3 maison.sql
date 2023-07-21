-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 02, 2023 at 08:12 PM
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
-- Database: `maison`
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
('641', 'GEN-2023-3', '2023-06-30 08:30:00'),
('977', NULL, '2023-06-29 16:00:00');

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
(4, 35, 'HIGH', 'F', 11, 999),
(1, 15, 'LOW', 'T', 0, 5),
(2, 25, 'MEDIUM', 'F', 6, 10);

-- --------------------------------------------------------

--
-- Table structure for table `clinic`
--

CREATE TABLE `clinic` (
  `clinic_capacity` int NOT NULL,
  `clinic_LatLng` varchar(1000) NOT NULL,
  `clinic_maxRadius` float NOT NULL,
  `clinic_SLQMaxSize` int NOT NULL,
  `clinic_openTime` time NOT NULL,
  `clinic_closeTime` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clinic`
--

INSERT INTO `clinic` (`clinic_capacity`, `clinic_LatLng`, `clinic_maxRadius`, `clinic_SLQMaxSize`, `clinic_openTime`, `clinic_closeTime`) VALUES
(15, 'LatLng(5.262354, 103.164679)', 401, 5, '00:00:00', '00:00:00');

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
('GEN', 'General Unit', 'Handles general illness or injuries.', 6),
('LRY', 'Laboratory', 'Laboratory whatever desc', 1),
('NA', 'DELETED', 'NULL', 2),
('REC', 'Records', 'Patient Records.', 0);

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
('000101-10-2521', 'MASTER_TEST', 'M', 23, 'frohze@gmail.com', '019663241212'),
('001212-62-1752', 'Jennifer Taylor', 'F', 22, 'jennifer.taylor@example.com', '+6017-8901234'),
('479513-07-2885', 'Olivia Martinez', 'F', 29, 'olivia.martinez@example.com', '+6012-3456789'),
('546218-51-1111', 'Michael Turqoise', 'M', 47, 'michael.brown@example.com', '011-1199111'),
('745129-27-9823', 'Emily Johnson', 'F', 42, 'emily.johnson@example.com', '+6016-7890123'),
('753916-73-3625', 'Christopher Lee', 'M', 31, 'christopher.lee@example.com', '+6018-9012345'),
('821347-37-5593', 'David Williams', 'M', 28, 'david.williams@example.com', '+6019-4567890'),
('900101-95-5371', 'Matthew Wilson', 'M', 33, 'matthew.wilson@example.com', '+6016-7890123'),
('925416-84-8142', 'Jessica Anderson', 'F', 39, 'jessica.anderson@example.com', '+6014-3456789'),
('934826-15-6732', 'John Smith', 'M', 35, 'john.smith@example.com', '+6012-3456789'),
('941217-10-9181', 'ABU', 'M', 28, 'abuuu@gmail.com', '018-712821261'),
('957512-48-2241', 'Sarah Davis', 'F', 61, 'sarah.davis@example.com', '+6011-2345678'),
('980623-10-9186', 'JIYOUNG', 'F', 25, 'baek@gmail.com', '018-99932844'),
('980927-10-2522', 'SEKAI', 'M', 22, 'mrahmatm@gmail.com', '018-6962570');

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
  `dept_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personnel`
--

INSERT INTO `personnel` (`personnel_ID`, `personnel_ICNum`, `personnel_name`, `personnel_gender`, `personnel_age`, `personnel_email`, `personnel_phoneNum`, `personnel_type`, `personnel_attend`, `dept_code`) VALUES
('GEN-2023-3', '950912-10-3432', 'SHINN', 'F', 27, 'yuuma@hotmail.com', '019-2229182', 'DR', 'F', 'GEN'),
('GEN-331', '000816-10-2681', 'FUMINA', 'F', 23, 'winning@gmail.com', '019-3332571', 'DR', 'T', 'GEN'),
('LAB-2023-1', '010606-10-0133', 'SETSUNA', 'M', 22, 'EXIA@yahoo.com', '012-345561212', 'DR', 'F', 'GEN'),
('LAB-2023-2', '010606-10-1411', 'SEIEI', 'M', 22, 'chooo@yahoo.com', '012-3455612121', 'DR', 'F', 'NA'),
('LAB-2023-3', '010606-10-1281', 'YUJU', 'M', 22, 'gf@yahoo.com', '012-3455612121', 'DR', 'F', 'NA'),
('LAB-2023-4', '000117-10-1292', 'SARAM', 'F', 23, 'exia@yahoo.com', '012-345567899', 'DR', 'F', 'LRY'),
('master', '1234', 'temp master', 'M', 25, 'master@gmail.com', '018-69620291', 'DR', 'F', 'GEN'),
('SM123', '001214-10-2681', 'SEKAI', 'M', 24, 'burning@gmail.com', '019-3332571', 'DR', 'F', 'GEN'),
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
('129', '956', '566', 'SLQ', '980623-10-9186', NULL),
('164', NULL, '956', 'SLQ', '745129-27-9823', NULL),
('566', '129', NULL, 'SLQ', '934826-15-6732', NULL),
('641', NULL, NULL, 'APP', '000101-10-2521', NULL),
('956', '164', '129', 'SLQ', '546218-51-1111', NULL),
('977', NULL, NULL, 'APP', '980927-10-2522', 'DCK');

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `svc_code` varchar(10) NOT NULL,
  `svc_desc` varchar(40) NOT NULL,
  `svc_enable` tinyint(1) DEFAULT '1',
  `dept_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`svc_code`, `svc_desc`, `svc_enable`, `dept_code`) VALUES
('BLT', 'Blood Testing', 1, 'GEN'),
('DCK', 'Daily checkups.', 1, 'GEN'),
('DRG', 'drug test', 1, NULL),
('REG', 'Registration', 1, 'REC'),
('URI', 'Urine test(s)', 1, 'LRY'),
('VAC', 'Vaccination', 1, 'GEN');

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
-- Indexes for table `general`
--
ALTER TABLE `general`
  ADD PRIMARY KEY (`q_ID`);

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
  ADD KEY `svc_queue` (`svc_code`),
  ADD KEY `patient_queue` (`patient_ICNum`);

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
-- Constraints for table `general`
--
ALTER TABLE `general`
  ADD CONSTRAINT `q_origin_gen` FOREIGN KEY (`q_ID`) REFERENCES `queue` (`q_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `operation`
--
ALTER TABLE `operation`
  ADD CONSTRAINT `personnel_origin` FOREIGN KEY (`personnel_ID`) REFERENCES `personnel` (`personnel_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `personnel`
--
ALTER TABLE `personnel`
  ADD CONSTRAINT `personnel_dept` FOREIGN KEY (`dept_code`) REFERENCES `department` (`dept_code`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `queue`
--
ALTER TABLE `queue`
  ADD CONSTRAINT `patient_queue` FOREIGN KEY (`patient_ICNum`) REFERENCES `patient` (`patient_ICNum`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `svc_queue` FOREIGN KEY (`svc_code`) REFERENCES `service` (`svc_code`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `svc_dept` FOREIGN KEY (`dept_code`) REFERENCES `department` (`dept_code`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
