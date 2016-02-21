-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 30, 2011 at 10:38 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dasbot`
--

-- --------------------------------------------------------

--
-- Table structure for table `drinks`
--

DROP TABLE IF EXISTS `drinks`;
CREATE TABLE IF NOT EXISTS `drinks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `volume` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `drinks`
--

INSERT INTO `drinks` (`id`, `timestamp`, `userid`, `volume`) VALUES
(1, 0, 1, 700),
(2, 0, 1, 200),
(3, 0, 2, 800),
(4, 0, 3, 100),
(5, 0, 3, 1250),
(6, 0, 1, 400),
(7, 0, 4, 300),
(8, 0, 4, 300),
(10, 1317435095, 1, 100),
(11, 1317435100, 1, 100),
(12, 1317435102, 1, 100),
(13, 1317435103, 1, 100),
(14, 1317435566, 1, 100),
(15, 1317435580, 1, 100),
(16, 1317435652, 1, 100),
(17, 1317435705, 1, 100),
(18, 1317435717, 1, 100),
(19, 1317435718, 1, 100),
(20, 1317435719, 1, 100),
(21, 1317435720, 1, 100),
(22, 1317435721, 1, 100),
(23, 1317435721, 1, 100),
(24, 1317435722, 1, 100),
(25, 1317435723, 1, 100),
(26, 1317435723, 1, 100),
(27, 1317435725, 1, 100),
(28, 1317435958, 1, 100),
(29, 1317435970, 1, 100),
(30, 1317435971, 1, 100),
(31, 1317435972, 1, 100),
(32, 1317436341, 14, 1000),
(33, 1317436378, 14, 1000),
(34, 1317436382, 14, 1000),
(35, 1317436383, 14, 1000),
(36, 1317436385, 14, 1000),
(37, 1317436386, 14, 1000),
(38, 1317436387, 14, 1000);

-- --------------------------------------------------------

--
-- Table structure for table `keg`
--

DROP TABLE IF EXISTS `keg`;
CREATE TABLE IF NOT EXISTS `keg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(11) NOT NULL,
  `ticks` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `keg`
--

INSERT INTO `keg` (`id`, `timestamp`, `ticks`) VALUES
(1, 1317151961, 0),
(2, 1317431524, 400),
(3, 1317433524, 1200);

-- --------------------------------------------------------

--
-- Table structure for table `ref_data`
--

DROP TABLE IF EXISTS `ref_data`;
CREATE TABLE IF NOT EXISTS `ref_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `ref_data`
--

INSERT INTO `ref_data` (`id`, `name`, `value`) VALUES
(1, 'Total_Estimated_Ticks', 88011),
(2, 'ticks_per_liter', 1500);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rfid` varchar(12) NOT NULL,
  `username` varchar(24) NOT NULL DEFAULT 'orphan',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rfid` (`rfid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `rfid`, `username`) VALUES
(1, '12345678', 'matt'),
(2, '12345', 'orphan'),
(3, '123456', 'orphan'),
(4, '111', 'orphan'),
(5, '888888', 'orphan'),
(6, 'abc123', 'orphan'),
(7, 'abc12364zx', 'orphan'),
(8, '112233abc', 'orphan'),
(9, '1122', 'orphan'),
(10, '112233', 'test1'),
(11, '112234', 'orphan'),
(12, '123456789', 'orphan'),
(13, 'abcabc', 'orphan'),
(14, 'adam123', 'adam');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
