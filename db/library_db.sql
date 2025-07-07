-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jul 07, 2025 at 05:15 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `reg_no` varchar(20) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `publisher` varchar(100) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `quantity` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`reg_no`, `title`, `author`, `publisher`, `year`, `category`, `quantity`) VALUES
('B012', 'Robinson', 'Robin Andrew', 'sanchitha', '2013', 'novel', '60'),
('B013', 'Madol Doowa', 'martin wikkramsinghe', 'sarasavi publisher', '2009', 'novel', '30'),
('B014', 'Noyena Maga', 'C.lakmal', 'Sarasavi', '2025', 'novel', '20'),
('B015', 'Science With Practicle', 'Dr. Ruchira', 'Sarasavi', '2020', 'Science', '40');

-- --------------------------------------------------------

--
-- Table structure for table `borrowers`
--

CREATE TABLE `borrowers` (
  `nic` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `borrowers`
--

INSERT INTO `borrowers` (`nic`, `name`, `email`, `contact`) VALUES
('200202301144', 'Sanchitha Udana', 'udanasanchitha2@gmail.com', '0710989261'),
('200202301145', 'Kasun', 'kasun@gmail.com', '0724563245'),
('200208934452', 'Dasun', 'dasun@gmail.com', '0710989324'),
('200302342255', 'Kawindi', 'kavindi@gmail.com', '0112342546');

-- --------------------------------------------------------

--
-- Table structure for table `borrowing`
--

CREATE TABLE `borrowing` (
  `borro_id` int(11) NOT NULL,
  `reg_no` varchar(20) DEFAULT NULL,
  `nic` varchar(20) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `issued_day` date DEFAULT NULL,
  `return_day` date DEFAULT NULL,
  `no_of_over_due_date` int(11) DEFAULT '0',
  `due_fine` int(11) DEFAULT '0',
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `borrowing`
--

INSERT INTO `borrowing` (`borro_id`, `reg_no`, `nic`, `title`, `issued_day`, `return_day`, `no_of_over_due_date`, `due_fine`, `status`) VALUES
(22, 'B012', '200202301144', 'Robinson', '2025-06-18', '2025-06-20', 2, 200, 'received'),
(23, 'B013', '200202301145', 'Madol Doowa', '2025-06-14', '2025-06-20', 2, 200, 'missing'),
(24, 'B015', '200302342255', 'Science With Practicle', '2025-06-17', '2025-06-20', 2, 200, 'over due'),
(26, 'B015', '200302342255', 'Science With Practicle', '2025-06-20', '2025-06-23', 0, 0, 'issued');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`reg_no`);

--
-- Indexes for table `borrowers`
--
ALTER TABLE `borrowers`
  ADD PRIMARY KEY (`nic`);

--
-- Indexes for table `borrowing`
--
ALTER TABLE `borrowing`
  ADD PRIMARY KEY (`borro_id`),
  ADD KEY `reg_no` (`reg_no`),
  ADD KEY `nic` (`nic`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `borrowing`
--
ALTER TABLE `borrowing`
  MODIFY `borro_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrowing`
--
ALTER TABLE `borrowing`
  ADD CONSTRAINT `borrowing_ibfk_1` FOREIGN KEY (`reg_no`) REFERENCES `book` (`reg_no`),
  ADD CONSTRAINT `borrowing_ibfk_2` FOREIGN KEY (`nic`) REFERENCES `borrowers` (`nic`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
