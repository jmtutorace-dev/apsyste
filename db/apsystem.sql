-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2026 at 09:19 AM
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
-- Database: `payroll_advnce`
--

-- --------------------------------------------------------

--
-- Table structure for table `ace_employee_deductions`
--

CREATE TABLE `ace_employee_deductions` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `deduction_type_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `deduction_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(60) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `firstname`, `lastname`, `photo`, `created_on`) VALUES
(1, 'admin', '$2y$10$fCOiMky4n5hCJx3cpsG20Od4wHtlkCLKmO6VLobJNRIg9ooHTkgjK', 'HR', 'ADMIN', 'ACEMC.png', '2018-04-30');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` time NOT NULL,
  `status` int(1) NOT NULL,
  `time_out` time NOT NULL,
  `num_hr` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `date`, `time_in`, `status`, `time_out`, `num_hr`) VALUES
(93, 5, '2026-05-25', '07:45:00', 1, '17:00:00', 8),
(94, 5, '2026-05-24', '07:45:00', 1, '16:45:00', 7.75),
(96, 4, '2026-05-30', '06:11:00', 1, '19:00:00', 8),
(97, 5, '2026-05-26', '08:00:00', 1, '17:00:00', 8),
(98, 5, '2026-05-19', '08:00:00', 1, '17:00:00', 8),
(100, 5, '2026-05-27', '08:15:00', 0, '17:00:00', 7.75),
(101, 4, '2026-05-27', '05:15:00', 1, '17:15:00', 7.25),
(102, 4, '2026-05-26', '05:15:00', 1, '16:15:00', 6.25),
(104, 5, '2026-05-01', '07:45:00', 1, '17:00:00', 8),
(105, 5, '2026-06-01', '08:30:00', 0, '18:00:00', 7.5),
(106, 5, '2025-06-01', '07:30:00', 1, '17:30:00', 8),
(107, 9, '2026-05-30', '07:00:00', 1, '19:00:00', 11),
(108, 9, '2026-04-01', '07:30:00', 0, '18:30:00', 10);

-- --------------------------------------------------------

--
-- Table structure for table `cashadvance`
--

CREATE TABLE `cashadvance` (
  `id` int(11) NOT NULL,
  `date_advance` date NOT NULL,
  `employee_id` varchar(15) NOT NULL,
  `amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cashadvance`
--

INSERT INTO `cashadvance` (`id`, `date_advance`, `employee_id`, `amount`) VALUES
(2, '2018-05-02', '1', 1000),
(4, '2026-05-22', '5', 50);

-- --------------------------------------------------------

--
-- Table structure for table `deductions`
--

CREATE TABLE `deductions` (
  `id` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  `amount` double NOT NULL,
  `is_government` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT 'fixed'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `deductions`
--

INSERT INTO `deductions` (`id`, `description`, `amount`, `is_government`, `type`) VALUES
(5, 'SSS', 4.5, 1, 'percent'),
(6, 'PhilHealth', 2.5, 1, 'percent'),
(7, 'PagIBIG', 200, 1, 'fixed'),
(14, 'GSIS', 9, 0, 'percent');

-- --------------------------------------------------------

--
-- Table structure for table `deduction_types`
--

CREATE TABLE `deduction_types` (
  `id` int(11) NOT NULL,
  `deduction_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deduction_types`
--

INSERT INTO `deduction_types` (`id`, `deduction_name`, `description`) VALUES
(1, 'Cafeteria', 'Cafeteria Deductions'),
(2, 'Uniform', NULL),
(3, 'Employee Loan', NULL),
(4, 'Damaged Equipment', NULL),
(5, 'Other', '');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(2, 'Accounting'),
(3, 'Admitting'),
(4, 'Billing'),
(5, 'Biomed'),
(6, 'Cafeteria'),
(7, 'Cashiering'),
(8, 'Cardiovascular'),
(9, 'Central Supply Room'),
(10, 'Credit And Collection'),
(11, 'Customer Service'),
(12, 'Dietary'),
(13, 'Facilities Management'),
(14, 'Finance'),
(15, 'HESU'),
(16, 'HMO'),
(17, 'Housekeeping / Linen And Laundry'),
(18, 'Human Resource'),
(19, 'Imaging'),
(20, 'Information And Communication'),
(21, 'Marketing'),
(22, 'Medical Records'),
(23, 'Neuroscience'),
(24, 'Nursing Services'),
(25, 'Office Of The Corporate Secretary'),
(26, 'Office Of The Hospital Administrator'),
(27, 'Office Of The Medical Director'),
(28, 'Office Of The President'),
(29, 'Orthopedics'),
(30, 'Pathology'),
(31, 'Pharmacy'),
(32, 'Philhealth'),
(33, 'Property Management'),
(34, 'Pulmonary'),
(35, 'Purchasing'),
(36, 'Quality Assurance'),
(37, 'Security'),
(38, 'Sleep Laboratory'),
(39, 'Social Services'),
(40, 'Warehousing'),
(41, 'Woundcare');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(15) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `birthdate` date NOT NULL,
  `contact_info` varchar(100) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `position_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `created_on` date NOT NULL,
  `department` varchar(50) DEFAULT NULL,
  `salary` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_id`, `firstname`, `lastname`, `address`, `birthdate`, `contact_info`, `gender`, `position_id`, `schedule_id`, `photo`, `created_on`, `department`, `salary`) VALUES
(1, 'ABC123456789', 'Neovic', 'Devierte', 'Brgy. Mambulac, Silay City', '2018-04-02', '09092735719', 'Male', 1, 2, 'desktop.jpg', '2018-04-28', 'Housekeeping / Linen And Laundry', 0.00),
(3, 'DYE473869250', 'Julyn', 'Divinagracia', 'E.B. Magalona', '1992-05-02', '09123456789', 'Female', 2, 2, '', '2018-04-30', 'Credit And Collection', 0.00),
(4, 'JIE625973480', 'Gemalyn', 'Cepe', 'Carmen, Bohol', '1995-10-02', '09468029840', 'Female', 4, 3, '', '2018-04-30', 'Information And Communication', 0.00),
(5, 'LET025174983', 'John mhico', 'Tutor', 'ac', '1999-09-22', '09789789', 'Male', 1, 2, 'facebook-profile-image.png', '2026-05-22', 'Information And Communication', 0.00),
(6, 'FVR305276189', 'wer', 'rew', 'wer', '2026-05-26', '09468029840', 'Female', 3, 1, '', '2026-05-23', 'Central Supply Room', 0.00),
(7, 'UHR703849561', 'tes', 'ts', 'sawd', '2026-05-26', '09092735719', 'Male', 3, 2, '', '2026-05-28', 'Human Resource', 0.00),
(9, 'BUW721489350', 'Test New', 'Test New', 'Test New', '1992-05-30', '09123456789', 'Male', 1, 5, 'ACEMC LOGO.png', '2026-05-30', 'Information And Communication', 0.00),
(10, 'TJO705412893', 'test', 'test', 'test', '2009-07-01', 'test', 'Male', 14, 3, '', '2026-06-01', 'Human Resource', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `employee_deductions`
--

CREATE TABLE `employee_deductions` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `deduction_id` int(11) DEFAULT NULL,
  `description` varchar(100) NOT NULL,
  `amount` double NOT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `employee_deductions`
--

INSERT INTO `employee_deductions` (`id`, `employee_id`, `deduction_id`, `description`, `amount`, `created_on`) VALUES
(7, 1, 14, '', 0, '0000-00-00'),
(8, 1, 7, '', 0, '0000-00-00'),
(10, 7, 6, '', 0, '2026-05-28'),
(35, 3, 7, '', 0, '0000-00-00'),
(41, 6, 14, '', 0, '0000-00-00'),
(42, 6, 7, '', 0, '0000-00-00'),
(43, 6, 6, '', 0, '0000-00-00'),
(52, 5, 7, '', 0, '0000-00-00'),
(53, 5, 6, '', 0, '0000-00-00'),
(63, 5, 1, 'Cafeteria', 2000, '2026-05-22'),
(70, 9, 14, '', 0, '0000-00-00'),
(71, 9, 7, '', 0, '0000-00-00'),
(72, 9, 6, '', 0, '0000-00-00'),
(73, 9, 5, '', 0, '0000-00-00'),
(74, 4, 7, '', 0, '0000-00-00'),
(75, 4, 6, '', 0, '0000-00-00'),
(76, 10, 14, '', 0, '2026-06-01'),
(77, 10, 7, '', 0, '2026-06-01'),
(78, 10, 6, '', 0, '2026-06-01'),
(79, 10, 5, '', 0, '2026-06-01');

-- --------------------------------------------------------

--
-- Table structure for table `employee_other_deductions`
--

CREATE TABLE `employee_other_deductions` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `deduction_type_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `deduction_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` int(11) NOT NULL,
  `holiday_date` date NOT NULL,
  `description` varchar(150) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `holidays`
--

INSERT INTO `holidays` (`id`, `holiday_date`, `description`, `type`) VALUES
(2, '2026-01-01', 'New Year\'s Day', 'Regular'),
(3, '2026-02-25', 'EDSA People Power Revolution', 'Special'),
(4, '2026-04-09', 'Araw ng Kagitingan', 'Regular'),
(5, '2026-04-02', 'Maundy Thursday', 'Regular'),
(6, '2026-04-03', 'Good Friday', 'Regular'),
(7, '2026-04-04', 'Black Saturday', 'Special'),
(8, '2026-05-01', 'Labor Day', 'Regular'),
(9, '2026-06-12', 'Independence Day', 'Regular'),
(10, '2026-08-21', 'Ninoy Aquino Day', 'Special'),
(11, '2026-08-31', 'National Heroes Day', 'Regular'),
(12, '2026-11-01', 'All Saints\' Day', 'Special'),
(13, '2026-11-30', 'Bonifacio Day', 'Regular'),
(14, '2026-12-08', 'Feast of the Immaculate Conception', 'Special'),
(15, '2026-12-24', 'Christmas Eve', 'Special'),
(16, '2026-12-25', 'Christmas Day', 'Regular'),
(17, '2026-12-30', 'Rizal Day', 'Regular'),
(18, '2026-12-31', 'New Year\'s Eve', 'Special');

-- --------------------------------------------------------

--
-- Table structure for table `overtime`
--

CREATE TABLE `overtime` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(15) NOT NULL,
  `hours` double NOT NULL,
  `rate` double NOT NULL,
  `date_overtime` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `id` int(11) NOT NULL,
  `description` varchar(150) NOT NULL,
  `rate` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `position`
--

INSERT INTO `position` (`id`, `description`, `rate`) VALUES
(1, 'Programmer', 12982),
(2, 'Writer', 26500),
(4, 'Programmer', 15300),
(6, 'TEST', 234234),
(7, 'TEST', 2342),
(9, 'MSS', 12800),
(13, 'MSS', 8500),
(14, 'Writer', 35221);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `time_in`, `time_out`) VALUES
(1, '07:00:00', '16:00:00'),
(2, '08:00:00', '17:00:00'),
(3, '09:00:00', '18:00:00'),
(4, '10:00:00', '19:00:00'),
(5, '07:00:00', '19:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ace_employee_deductions`
--
ALTER TABLE `ace_employee_deductions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cashadvance`
--
ALTER TABLE `cashadvance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deductions`
--
ALTER TABLE `deductions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deduction_types`
--
ALTER TABLE `deduction_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_other_deductions`
--
ALTER TABLE `employee_other_deductions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `overtime`
--
ALTER TABLE `overtime`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ace_employee_deductions`
--
ALTER TABLE `ace_employee_deductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `cashadvance`
--
ALTER TABLE `cashadvance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `deductions`
--
ALTER TABLE `deductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `deduction_types`
--
ALTER TABLE `deduction_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `employee_other_deductions`
--
ALTER TABLE `employee_other_deductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `overtime`
--
ALTER TABLE `overtime`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `position`
--
ALTER TABLE `position`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
