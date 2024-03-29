-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 07, 2019 at 10:05 PM
-- Server version: 5.6.34-log
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ddwt18_finalproject`
--
CREATE DATABASE IF NOT EXISTS `ddwt18_finalproject` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `ddwt18_finalproject`;

-- --------------------------------------------------------

--
-- Table structure for table `optins`
--

CREATE TABLE `optins` (
  `roomid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `date` varchar(255) COLLATE utf8_bin NOT NULL,
  `message` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `optins`
--

INSERT INTO `optins` (`roomid`, `userid`, `date`, `message`) VALUES
(1, 2, 'Wed Dec 2018', 'I want this room.'),
(1, 3, 'Tue Dec 2018', 'This room seems really nice to have'),
(1, 4, 'Tue Dec 2018', 'May i have this room?'),
(4, 2, 'Wed Dec 2018', 'May i have this room?'),
(8, 7, 'Mon Jan 2019', 'Yes pls');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `address` varchar(255) COLLATE utf8_bin NOT NULL,
  `city` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` varchar(255) COLLATE utf8_bin NOT NULL,
  `price` int(3) NOT NULL,
  `size` int(3) NOT NULL,
  `owner` int(11) NOT NULL,
  `thumbnail` varchar(150) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `address`, `city`, `type`, `price`, `size`, `owner`, `thumbnail`) VALUES
(1, 'De Tuinen 26', 'Groningen', 'Room in high house', 300, 12, 1, NULL),
(2, 'Plutolaan 401', 'Utrecht', 'Room in appartment', 365, 10, 1, NULL),
(3, 'Tolhuis 1', 'Groningen', 'Room in appartment', 200, 10, 1, NULL),
(4, 'Haverlanden 105', 'Groningen', 'Room in house', 150, 6, 5, NULL),
(5, 'Herestraat 10', 'Utrecht', 'Room in appartment', 345, 8, 1, NULL),
(6, 'Marnelaan 15', '', 'Room', 4373, 205212, 6, NULL),
(7, 'Kerkstraat 15b', '', 'room', 2131, 30, 6, NULL),
(8, 'testroom', '', 'test', 32, 11, 6, '86.jpg'),
(9, 'testlaan 1', '', 'ads', 22, 23, 6, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `full_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `birth_date` date NOT NULL,
  `role` int(11) NOT NULL,
  `biography` varchar(255) COLLATE utf8_bin NOT NULL,
  `profession` varchar(255) COLLATE utf8_bin NOT NULL,
  `language` varchar(255) COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `phonenumber` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `birth_date`, `role`, `biography`, `profession`, `language`, `email`, `phonenumber`) VALUES
(1, 'huppelputje', '$2y$10$hn9Iuj.uqsjvqYWz5ot8SOtijBCZgEwGVazIrBJTgT.A6K1HE04uC', 'Jan Hooghe', '1998-12-19', 1, 'Someone', 'Teacher English', 'Dutch', 'janhooghe@example.com', 639283712),
(2, 'melodyman', '$2y$10$56cXE5/nuzOk6qeXat04YOM7JIdKSCXfjjc8IB2g22jDJqdNOFMmy', 'Jannick Akkermans', '1998-10-22', 2, 'A normal student.', 'Information Science', 'Dutch', 'jakkermans@example.com', 619283729),
(3, 'hubbahubba', '$2y$10$kGRSyi52RN4W9GMtKnz9jewFrGUdtw6zjFeJq45mfLdVIkwf6pRmi', 'Huub Hartje', '2010-09-03', 2, 'Someone looking for a room.', 'Physics', 'Dutch', 'hubbahubba@hubba.com', 693827492),
(4, 'huppeldepup', '$2y$10$.srkimlId3NchBB5hbzGyenH8dc2GzFuIfJ.JEpwIKEh5PoRwJQ6m', 'Jantje Iets', '1987-08-18', 2, 'Im a normal person', 'Psychology', 'Dutch', 'huppeldepup@example.com', 638192831),
(5, 'hockeyman', '$2y$10$06htVsuSqGj0Y59RxRTm8OhDxI1d5fYEluUawPz13Adu7tKTzuWo.', 'Hockey Man', '1999-10-30', 1, 'Owner', 'Chef', 'Dutch', 'hockeyman@hockey.com', 693871093),
(6, 'Gekke Geert', '$2y$10$Fvd64mIv7IRyUgvUbtUdDe4OC1viKrVBQlE6KO/BSH7.P/CzyTff6', 'Geert', '1212-12-12', 1, 'test', 'test', 'test', 'test@hotmail.com', 666666666),
(7, 'YeetusFeetus', '$2y$10$23lny76SEXZ9ZVa5bZN7WOj5pEoFLlUMExkKQDLw596l/ShVzGAXS', 'Yeetus', '1212-12-12', 2, 'test', 'test', 'Dutch', 'test@test.com', 645464566);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `optins`
--
ALTER TABLE `optins`
  ADD PRIMARY KEY (`roomid`,`userid`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owns` (`owner`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `owns` FOREIGN KEY (`owner`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
