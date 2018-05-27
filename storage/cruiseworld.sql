-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 27, 2018 at 11:52 AM
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
-- Database: `cruiseworld`
--

-- --------------------------------------------------------

--
-- Table structure for table `wp_cruiselines`
--

DROP TABLE IF EXISTS `wp_cruiselines`;
CREATE TABLE `wp_cruiselines` (
  `cruiseline_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wp_cruiselines`
--

INSERT INTO `wp_cruiselines` (`cruiseline_id`, `name`, `details`, `url`, `logo`) VALUES
(1, 'Royal Caribbean International', NULL, NULL, 'https://images.cruisecritic.com/image/5355/image_32x_11.jpg'),
(2, 'P&O Cruises', NULL, NULL, 'https://images.cruisecritic.com/image/5362/image_32x_11.jpg'),
(3, 'Norwegian Cruise Line', NULL, NULL, 'https://images.cruisecritic.com/image/5805/image_32x_11.jpg'),
(4, 'Cunard Line', NULL, NULL, 'https://images.cruisecritic.com/image/5277/image_32x_11.jpg'),
(5, 'MSC Cruises', NULL, NULL, 'https://images.cruisecritic.com/image/5814/image_32x_11.jpg'),
(6, 'Carnival Cruise Line', NULL, NULL, 'https://images.cruisecritic.com/image/5804/image_32x_11.jpg'),
(7, 'Princess Cruises', NULL, NULL, 'https://images.cruisecritic.com/image/5360/image_32x_11.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `wp_cruises`
--

DROP TABLE IF EXISTS `wp_cruises`;
CREATE TABLE `wp_cruises` (
  `cruise_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `cruiseline_id` int(11) DEFAULT NULL,
  `ship_id` int(11) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `source_key` int(11) DEFAULT NULL,
  `source_url` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `itinerary_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wp_keys`
--

DROP TABLE IF EXISTS `wp_keys`;
CREATE TABLE `wp_keys` (
  `key_id` int(11) DEFAULT NULL,
  `domain` enum('cruises','cruiselines','ships','ports') NOT NULL,
  `supplier_key` text NOT NULL,
  `supplier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wp_ports`
--

DROP TABLE IF EXISTS `wp_ports`;
CREATE TABLE `wp_ports` (
  `port_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wp_ports`
--

INSERT INTO `wp_ports` (`port_id`, `name`, `details`, `url`) VALUES
(209, 'Venice', NULL, '/ports/newport.cfm?ID=71'),
(210, 'Dubrovnik', NULL, '/ports/newport.cfm?ID=70'),
(211, 'Kotor', NULL, '/ports/newport.cfm?ID=526'),
(212, 'Santorini', NULL, '/ports/newport.cfm?ID=128'),
(213, 'Katakolon (Olympia)', NULL, '/ports/newport.cfm?ID=124'),
(214, 'Southampton', NULL, '/ports/newport.cfm?ID=61'),
(215, 'Stavanger', NULL, '/ports/newport.cfm?ID=592'),
(216, 'Flam', NULL, '/ports/newport.cfm?ID=206'),
(217, 'Olden', NULL, '/ports/newport.cfm?ID=6206'),
(218, 'Bergen', NULL, '/ports/newport.cfm?ID=114'),
(219, 'Barcelona', NULL, '/ports/newport.cfm?ID=68'),
(220, 'Naples', NULL, '/ports/newport.cfm?ID=72'),
(221, 'Rome (Civitavecchia)', NULL, '/ports/newport.cfm?ID=79'),
(222, 'Florence (Livorno)', NULL, '/ports/newport.cfm?ID=83'),
(223, 'Cannes', NULL, '/ports/newport.cfm?ID=182'),
(224, 'Palma de Mallorca', NULL, '/ports/newport.cfm?ID=69'),
(225, 'San Juan', NULL, '/ports/newport.cfm?ID=8'),
(226, 'St. Maarten', NULL, '/ports/newport.cfm?ID=13'),
(227, 'St. Kitts', NULL, '/ports/newport.cfm?ID=11'),
(228, 'Antigua', NULL, '/ports/newport.cfm?ID=55'),
(229, 'St. Lucia', NULL, '/ports/newport.cfm?ID=12'),
(230, 'Barbados', NULL, '/ports/newport.cfm?ID=5'),
(231, 'Vigo', NULL, '/ports/newport.cfm?ID=185'),
(232, 'Lisbon', NULL, '/ports/newport.cfm?ID=80'),
(233, 'Seville', NULL, '/ports/newport.cfm?ID=81'),
(234, 'Lanzarote', NULL, '/ports/newport.cfm?ID=178'),
(235, 'Tenerife', NULL, '/ports/newport.cfm?ID=184'),
(236, 'La Palma', NULL, '/ports/newport.cfm?ID=186'),
(237, 'Madeira (Funchal)', NULL, '/ports/newport.cfm?ID=170'),
(238, 'La Coruna', NULL, '/ports/newport.cfm?ID=6137'),
(239, 'Corfu', NULL, '/ports/newport.cfm?ID=176'),
(240, 'Athens (Piraeus)', NULL, '/ports/newport.cfm?ID=110'),
(241, 'Mykonos', NULL, '/ports/newport.cfm?ID=210'),
(242, 'Argostoli', NULL, '/ports/newport.cfm?ID=9283'),
(243, 'New York (Manhattan)', NULL, '/ports/newport.cfm?ID=94'),
(244, 'Le Havre', NULL, '/ports/newport.cfm?ID=85'),
(245, 'Bilbao', NULL, '/ports/newport.cfm?ID=564'),
(246, 'Amsterdam', NULL, '/ports/newport.cfm?ID=99'),
(247, 'Hamburg', NULL, '/ports/newport.cfm?ID=503'),
(248, 'St. Thomas', NULL, '/ports/newport.cfm?ID=17'),
(249, 'Split', NULL, '/ports/newport.cfm?ID=205'),
(250, 'Ancona', NULL, '/ports/newport.cfm?ID=6464'),
(251, 'Havana', NULL, '/ports/newport.cfm?ID=474'),
(252, 'Montego Bay', NULL, '/ports/newport.cfm?ID=20'),
(253, 'Grand Cayman', NULL, '/ports/newport.cfm?ID=1'),
(254, 'Cozumel', NULL, '/ports/newport.cfm?ID=6'),
(255, 'Belize City', NULL, '/ports/newport.cfm?ID=107'),
(256, 'Roatan', NULL, '/ports/newport.cfm?ID=156'),
(257, 'Costa Maya', NULL, '/ports/newport.cfm?ID=150'),
(258, 'Oslo', NULL, '/ports/newport.cfm?ID=76'),
(259, 'Copenhagen', NULL, '/ports/newport.cfm?ID=75'),
(260, 'Tallinn', NULL, '/ports/newport.cfm?ID=87'),
(261, 'St. Petersburg', NULL, '/ports/newport.cfm?ID=73'),
(262, 'Helsinki', NULL, '/ports/newport.cfm?ID=86'),
(263, 'Skagen', NULL, '/ports/newport.cfm?ID=6730'),
(264, 'Marseille', NULL, '/ports/newport.cfm?ID=167'),
(265, 'La Spezia (Cinque Terre)', NULL, '/ports/newport.cfm?ID=6632'),
(266, 'Salerno', NULL, '/ports/newport.cfm?ID=6266');

-- --------------------------------------------------------

--
-- Table structure for table `wp_ships`
--

DROP TABLE IF EXISTS `wp_ships`;
CREATE TABLE `wp_ships` (
  `ship_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `cruiseline_id` int(11) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wp_ships`
--

INSERT INTO `wp_ships` (`ship_id`, `name`, `cruiseline_id`, `details`, `url`) VALUES
(1, 'Rhapsody of the Seas', 1, NULL, NULL),
(2, 'Azura', 2, NULL, NULL),
(3, 'Norwegian Epic', 3, NULL, NULL),
(4, 'Freedom of the Seas', 1, NULL, NULL),
(5, 'Independence of the Seas', 1, NULL, NULL),
(6, 'Queen Mary 2 (QM2)', 4, NULL, NULL),
(7, 'MSC Magnifica', 5, NULL, NULL),
(8, 'Norwegian Jade', 3, NULL, NULL),
(9, 'Carnival Fascination', 6, NULL, NULL),
(10, 'MSC Sinfonia', 5, NULL, NULL),
(11, 'MSC Armonia', 5, NULL, NULL),
(12, 'Navigator of the Seas', 1, NULL, NULL),
(13, 'Symphony of the Seas', 1, NULL, NULL),
(14, 'Crown Princess', 7, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wp_suppliers`
--

DROP TABLE IF EXISTS `wp_suppliers`;
CREATE TABLE `wp_suppliers` (
  `supplier_id` int(11) NOT NULL,
  `name` text,
  `details` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wp_cruiselines`
--
ALTER TABLE `wp_cruiselines`
  ADD PRIMARY KEY (`cruiseline_id`);

--
-- Indexes for table `wp_cruises`
--
ALTER TABLE `wp_cruises`
  ADD PRIMARY KEY (`cruise_id`),
  ADD KEY `title` (`title`,`details`,`cruiseline_id`,`ship_id`,`source_id`,`source_key`);

--
-- Indexes for table `wp_ports`
--
ALTER TABLE `wp_ports`
  ADD PRIMARY KEY (`port_id`);

--
-- Indexes for table `wp_ships`
--
ALTER TABLE `wp_ships`
  ADD PRIMARY KEY (`ship_id`);

--
-- Indexes for table `wp_suppliers`
--
ALTER TABLE `wp_suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wp_cruiselines`
--
ALTER TABLE `wp_cruiselines`
  MODIFY `cruiseline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `wp_cruises`
--
ALTER TABLE `wp_cruises`
  MODIFY `cruise_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `wp_ports`
--
ALTER TABLE `wp_ports`
  MODIFY `port_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=267;
--
-- AUTO_INCREMENT for table `wp_ships`
--
ALTER TABLE `wp_ships`
  MODIFY `ship_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `wp_suppliers`
--
ALTER TABLE `wp_suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
