-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 04, 2021 at 05:39 PM
-- Server version: 5.7.35-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tcl`
--

-- --------------------------------------------------------

--
-- Table structure for table `wp_truck_details`
--

CREATE TABLE `wp_truck_details` (
  `ID` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `updated` datetime DEFAULT CURRENT_TIMESTAMP,
  `listing_id` bigint(20) NOT NULL,
  `Manufacturer` varchar(64) NOT NULL,
  `Model` varchar(64) NOT NULL,
  `Year` int(11) NOT NULL,
  `truckCondition` text NOT NULL,
  `VINSerialNumber` varchar(128) NOT NULL,
  `DisplayOnSite` varchar(12) NOT NULL,
  `data` text NOT NULL,
  `Price` int(11) DEFAULT NULL,
  `StockNumber` varchar(64) NOT NULL,
  `Vendor` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
