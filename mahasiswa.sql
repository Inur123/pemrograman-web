-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jun 10, 2024 at 04:23 PM
-- Server version: 5.7.39
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mahasiswa`
--

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int(11) NOT NULL,
  `nim` varchar(50) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `foto` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('admin','mahasiswa') NOT NULL,
  `approved` enum('1','0') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `nim`, `nama`, `foto`, `password`, `role`, `approved`) VALUES
(31, '21533458', 'zainur', 'images/default-image.webp', '$2y$10$RZhTveBLCcPnlVRvi3.s9u2ImGWsbtn3rJDbenU55WhGL2hLk/2Oy', 'admin', '1'),
(33, '223', 'Muhammad Zainur', 'uploads/ANIS DAWIM 4.png', '$2y$10$Y/Zwsfr4ho8X40V6Bl6Zfe3IGlTPR1Rk5HIffmR3GH72bEsDFXqy6', 'mahasiswa', '1'),
(34, '21533452', 'zainur', 'mahasiswa', '$2y$10$Mck3OStnj08Zv437ZHX7zeUWKCVW72Z6UDJ6f9Uq81i.Ff8zFnfRe', 'mahasiswa', '0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
