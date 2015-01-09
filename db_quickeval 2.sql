-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 22, 2014 at 07:50 AM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db_quickeval`
--
CREATE DATABASE IF NOT EXISTS `db_quickeval` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `db_quickeval`;

-- --------------------------------------------------------

--
-- Table structure for table `categoryname`
--

CREATE TABLE IF NOT EXISTS `categoryname` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `personId` int(3) DEFAULT NULL,
  `standardFlag` tinyint(1) DEFAULT NULL COMMENT '1=by user/0=standard',
  PRIMARY KEY (`id`),
  KEY `personId` (`personId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=10 ;

--
-- Dumping data for table `categoryname`
--

INSERT INTO `categoryname` (`id`, `name`, `personId`, `standardFlag`) VALUES
(1, 'Best contrast', NULL, 0),
(2, 'Best saturation', NULL, 0),
(3, 'Best Hue', NULL, 0),
(4, 'The best weighted colourbalance', 3, 1),
(5, 'The funniest looking', 3, 1),
(6, 'Fineste bilde', 1, NULL),
(8, 'Styggeste bildet', 1, NULL),
(9, 'Apekatt i et tre', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `experiment`
--

CREATE TABLE IF NOT EXISTS `experiment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `shortDescription` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `longDescription` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isPublic` enum('0 = Hidden','1 = Public','','') COLLATE utf8_bin DEFAULT NULL COMMENT '0=no visible/1=visible. If experiment is visible/may be taken by the general public',
  `allowColourBlind` enum('0','1','','') COLLATE utf8_bin DEFAULT NULL COMMENT '0=Not allowed/1=Allowed',
  `backgroundColour` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT 'RGB Decimal',
  `allowTies` tinyint(1) DEFAULT NULL,
  `showOriginal` tinyint(1) DEFAULT NULL,
  `samePair` tinyint(1) DEFAULT NULL,
  `horizontalFlip` tinyint(1) DEFAULT NULL,
  `monitorDistance` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT 'Measured in mm',
  `lightType` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `naturalLighting` tinyint(1) DEFAULT NULL,
  `screenLuminance` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `whitePoint` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `whitePointRoom` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `ambientllumination` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `person` int(10) NOT NULL,
  `experimentType` int(10) DEFAULT NULL,
  `timer` enum('0','1','','') COLLATE utf8_bin NOT NULL DEFAULT '0' COMMENT '0=No timer/1=Timer',
  PRIMARY KEY (`id`),
  KEY `experimentType` (`experimentType`),
  KEY `person` (`person`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=43 ;

--
-- Dumping data for table `experiment`
--

INSERT INTO `experiment` (`id`, `title`, `shortDescription`, `longDescription`, `date`, `isPublic`, `allowColourBlind`, `backgroundColour`, `allowTies`, `showOriginal`, `samePair`, `horizontalFlip`, `monitorDistance`, `lightType`, `naturalLighting`, `screenLuminance`, `whitePoint`, `whitePointRoom`, `ambientllumination`, `person`, `experimentType`, `timer`) VALUES
(5, 'Whitebalance experiment', 'Whitebalance experiment', 'Experiment', '2014-02-19 17:38:11', '1 = Public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, '0'),
(6, 'Contrast experiment', 'Contrast experiment', 'Experiment2', '2014-02-19 17:38:27', '1 = Public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL, '0'),
(7, 'Årets beste og største test', 'Årets beste og største test', NULL, '2014-02-24 10:17:06', '1 = Public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, 1, '0'),
(32, 'nyeste', 'asdas', 'asdasd', '2014-03-27 18:33:10', '1 = Public', '0', 'backGroundcolor', 0, 1, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 2, '0'),
(33, 'test 1 instruksjon then pictureset', '', 'lDesc', '2014-03-27 19:23:21', '1 = Public', '0', 'backGroundcolor', 0, 1, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 2, '0'),
(34, 'Test 2 bare bilde', '', 'sdfsdf', '2014-03-27 19:25:24', '1 = Public', '0', 'backGroundcolor', 0, 1, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 2, '0'),
(35, 'Test 3 pictureset then instruction', '', 'sdf', '2014-03-28 08:20:02', '1 = Public', '0', 'backGroundcolor', 0, 1, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 2, '0'),
(37, 'dfsgdfgdf', 'gdfgdfg', 'dfgdfg', '2014-03-28 12:28:33', '0 = Hidden', '0', 'backGroundcolor', 0, 1, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 2, '1'),
(38, 'dfgdfg', 'dfgdfgdfg', 'dfgdfg', '2014-03-28 12:32:44', '0 = Hidden', '0', 'backGroundcolor', 0, 0, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 2, '0'),
(39, 'Rangering med bare bilder', 'df', 'Rangering med bare bilder\nRangering med bare bilder', '2014-04-15 04:56:27', '1 = Public', '0', 'backGroundcolor', 1, 1, NULL, 1, '1', NULL, NULL, '', '', '', '', 1, 1, '0'),
(40, 'Kategoribedømmelse uten instruksjon', 'dsfsdf', 'Kategoribedømmelse uten instruksjon\nKategoribedømmelse uten instruksjon', '2014-04-15 07:54:33', '1 = Public', '0', 'backGroundcolor', 1, 1, NULL, 1, '1', NULL, NULL, '', '', '', '', 1, 3, '1'),
(41, 'Rangering med instruksjon', 'sdf', 'Rangering med instruksjon\nRangering med instruksjon', '2014-04-15 09:38:23', '0 = Hidden', '0', 'backGroundcolor', 1, 1, NULL, 1, '1', NULL, NULL, '', '', '', '', 1, 1, '0'),
(42, 'Kategoribedømmelse uten instruksjon med kategorier', 'sdfsdf', 'Kategoribedømmelse uten instruksjon med kategorier\nKategoribedømmelse uten instruksjon med kategorier', '2014-04-15 10:36:52', '0 = Hidden', '0', 'backGroundcolor', 1, 1, NULL, 1, '1', NULL, NULL, '', '', '', '', 1, 3, '0');

-- --------------------------------------------------------

--
-- Table structure for table `experimentcategory`
--

CREATE TABLE IF NOT EXISTS `experimentcategory` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `category` int(10) NOT NULL,
  `experiment` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `categoryId` (`category`,`experiment`),
  KEY `pictureOrder` (`experiment`),
  KEY `categoryId_2` (`category`),
  KEY `pictureOrder_2` (`experiment`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Dumping data for table `experimentcategory`
--

INSERT INTO `experimentcategory` (`id`, `category`, `experiment`) VALUES
(1, 6, 42),
(3, 8, 42),
(4, 9, 42);

-- --------------------------------------------------------

--
-- Table structure for table `experimentinfotype`
--

CREATE TABLE IF NOT EXISTS `experimentinfotype` (
  `experiment` int(10) DEFAULT NULL,
  `infoType` int(3) DEFAULT NULL,
  KEY `experiment` (`experiment`),
  KEY `infoType` (`infoType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `experimentinfotype`
--

INSERT INTO `experimentinfotype` (`experiment`, `infoType`) VALUES
(NULL, 1),
(NULL, 2),
(NULL, 1),
(NULL, 2),
(NULL, 1),
(NULL, 2),
(NULL, 1),
(NULL, 2),
(NULL, 1),
(NULL, 2),
(NULL, 1),
(NULL, 2),
(NULL, 1),
(NULL, 2),
(NULL, 1),
(NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `experimentorder`
--

CREATE TABLE IF NOT EXISTS `experimentorder` (
  `eOrder` int(11) NOT NULL AUTO_INCREMENT,
  `pictureSet` int(10) DEFAULT NULL,
  `experimentQueue` int(10) DEFAULT NULL,
  `pictureQueue` int(10) DEFAULT NULL,
  `instruction` int(10) DEFAULT NULL,
  PRIMARY KEY (`eOrder`),
  KEY `pictureQueue` (`pictureQueue`),
  KEY `experimentInstruction` (`experimentQueue`),
  KEY `pictureQueue_2` (`pictureQueue`),
  KEY `experimentQueue` (`experimentQueue`),
  KEY `instruction` (`instruction`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=35 ;

--
-- Dumping data for table `experimentorder`
--

INSERT INTO `experimentorder` (`eOrder`, `pictureSet`, `experimentQueue`, `pictureQueue`, `instruction`) VALUES
(10, NULL, 25, NULL, 7),
(12, NULL, 25, 8, NULL),
(13, NULL, 25, 9, NULL),
(14, NULL, 26, NULL, 9),
(15, NULL, 26, 21, NULL),
(16, NULL, 27, 22, NULL),
(17, NULL, 28, NULL, 10),
(18, NULL, 28, 23, NULL),
(23, NULL, 30, NULL, 14),
(24, NULL, 30, NULL, 15),
(25, NULL, 30, NULL, 16),
(26, NULL, 30, 25, NULL),
(27, NULL, 31, NULL, 17),
(28, NULL, 31, 26, NULL),
(29, NULL, 31, 27, NULL),
(30, NULL, 32, 28, NULL),
(31, NULL, 33, 29, NULL),
(32, NULL, 34, NULL, 18),
(33, NULL, 34, 30, NULL),
(34, NULL, 35, 31, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `experimentqueue`
--

CREATE TABLE IF NOT EXISTS `experimentqueue` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `experiment` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `experiment` (`experiment`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=36 ;

--
-- Dumping data for table `experimentqueue`
--

INSERT INTO `experimentqueue` (`id`, `experiment`) VALUES
(25, 32),
(26, 33),
(27, 34),
(28, 35),
(30, 37),
(31, 38),
(32, 39),
(33, 40),
(34, 41),
(35, 42);

-- --------------------------------------------------------

--
-- Table structure for table `experimentresult`
--

CREATE TABLE IF NOT EXISTS `experimentresult` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `browser` enum('Chrome','Safari','Opera','Internet Explorer','Mozilla Firefox','other') COLLATE utf8_bin DEFAULT NULL,
  `os` enum('Windows','OSx','Linux','other') COLLATE utf8_bin DEFAULT NULL,
  `xDimension` int(4) DEFAULT NULL,
  `yDimension` int(4) DEFAULT NULL,
  `startTime` datetime DEFAULT NULL,
  `endTime` datetime DEFAULT NULL,
  `experiment` int(10) DEFAULT NULL,
  `person` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `experiment` (`experiment`),
  KEY `person` (`person`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `experimenttype`
--

CREATE TABLE IF NOT EXISTS `experimenttype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` enum('pair','rating','category') COLLATE utf8_bin NOT NULL,
  `description` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `experimenttype`
--

INSERT INTO `experimenttype` (`id`, `name`, `type`, `description`) VALUES
(1, 'Rating', 'rating', 'Rate the shit of the pictures'),
(2, 'Pairing', 'pair', 'Pick one'),
(3, 'Category', 'category', 'Drags pictures into corresponding categories ');

-- --------------------------------------------------------

--
-- Table structure for table `infofield`
--

CREATE TABLE IF NOT EXISTS `infofield` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `text` varchar(1000) COLLATE utf8_bin NOT NULL,
  `experiment` int(10) DEFAULT NULL,
  `person` int(10) DEFAULT NULL,
  `infoType` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `infoType` (`infoType`),
  KEY `person` (`person`),
  KEY `experiment` (`experiment`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Dumping data for table `infofield`
--

INSERT INTO `infofield` (`id`, `text`, `experiment`, `person`, `infoType`) VALUES
(1, 'Big european size 45', NULL, NULL, 3),
(2, 'Red with yellow stripes and superman logo on the front', NULL, NULL, 6),
(3, 'hei', NULL, 1, 5),
(4, 'hei', NULL, 1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `infotype`
--

CREATE TABLE IF NOT EXISTS `infotype` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `standardFlag` tinyint(1) DEFAULT NULL,
  `info` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `person` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `person` (`person`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

--
-- Dumping data for table `infotype`
--

INSERT INTO `infotype` (`id`, `standardFlag`, `info`, `person`) VALUES
(1, 0, 'Firstname', NULL),
(2, 0, 'Lastname', NULL),
(3, 0, 'Age', NULL),
(4, 0, 'Nationality', NULL),
(5, 1, 'Shoe size', 3),
(6, 1, 'Shirt colour', 3);

-- --------------------------------------------------------

--
-- Table structure for table `instruction`
--

CREATE TABLE IF NOT EXISTS `instruction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `standardFlag` tinyint(1) NOT NULL COMMENT '1=by user/0=standard',
  `text` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `personId` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `personId` (`personId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=19 ;

--
-- Dumping data for table `instruction`
--

INSERT INTO `instruction` (`id`, `standardFlag`, `text`, `personId`) VALUES
(1, 1, 'Pick the less pixelated of them', 3),
(2, 0, 'Pick the best looking of the two', NULL),
(3, 0, 'Hei på deg, se på bilde', 1),
(4, 0, 'Sammenlign disse', 1),
(5, 0, 'sdaasdasd', 1),
(6, 0, 'sadasdasd', 1),
(7, 0, 'instruksjon 2', 1),
(8, 0, 'instruksjon 3', 1),
(9, 0, 'Dette er en veeeeeldig lang instruksjon', 1),
(10, 0, 'Instruksjon etter bildesett', 1),
(11, 0, 'hfghfgh', 1),
(12, 0, 'fghfgh', 1),
(13, 0, 'fghfgh', 1),
(14, 0, 'dfgdfg', 1),
(15, 0, 'dfgdfgd', 1),
(16, 0, 'dfgdfgdfg', 1),
(17, 0, 'dfgdfgdfg', 1),
(18, 0, 'sadløkgjdfgølkjdfgdsfgdsfglkjdsfgløkdjsfgølksdfjgølkdsfjgdsfløkgjdsfløkgjdsfølkgjdfsg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE IF NOT EXISTS `person` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `lastName` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `colourBlindFlag` enum('0','1','','') COLLATE utf8_bin DEFAULT '1' COMMENT '0=Test not taken or not normal sight/ 1= Normal sight',
  `age` int(3) DEFAULT NULL,
  `sex` enum('male','female') COLLATE utf8_bin DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `title` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `phoneNumber` int(30) DEFAULT NULL,
  `creationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userType` int(10) NOT NULL,
  `isPublic` enum('0','1','','') COLLATE utf8_bin NOT NULL COMMENT '0=Hidden/1=Public',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `userType` (`userType`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9 ;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`id`, `firstName`, `lastName`, `email`, `password`, `colourBlindFlag`, `age`, `sex`, `nationality`, `title`, `phoneNumber`, `creationDate`, `userType`, `isPublic`) VALUES
(1, 'Lars', 'Hansen', 'lars.hansen@gmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '1', 45, 'male', 'Norway', 'Ph.D', 9111111, '0000-00-00 00:00:00', 0, '1'),
(3, 'Jens', 'Solbakken', 'jens.solbakken@hotmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '0', 27, 'male', 'Denmark', NULL, 234842600, '0000-00-00 00:00:00', 2, '1'),
(4, 'Bernt', 'Ullbakk', 'bernt@gmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '0', NULL, NULL, NULL, NULL, NULL, '2014-02-19 09:57:01', 2, '1'),
(5, 'Admin', 'Adminsen', 'admin@gmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '0', 33, 'male', 'Sweden', 'Ph.D', 45671235, '2014-02-21 14:30:28', 1, '1'),
(6, 'Håkon', 'Ludvigsen', 'scientist@gmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '0', 24, 'male', 'Netherlands', 'Ph.D', 45671455, '2014-02-21 14:31:27', 2, '1'),
(7, 'Observer', 'Observersen', 'observer@gmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '0', 12, 'female', 'Argentina', 'Master', 2147483647, '2014-02-21 14:32:21', 3, '1'),
(8, 'Anonym', 'Anonymsen', 'ano@gmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '0', NULL, NULL, NULL, NULL, NULL, '2014-02-25 17:32:13', 4, '1');

-- --------------------------------------------------------

--
-- Table structure for table `picture`
--

CREATE TABLE IF NOT EXISTS `picture` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `url` varchar(32) COLLATE utf8_bin NOT NULL,
  `isOriginal` tinyint(1) DEFAULT NULL COMMENT '1=Reproduction/0=Original. Picture not reproduction',
  `pictureSet` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pictureSet` (`pictureSet`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=67 ;

--
-- Dumping data for table `picture`
--

INSERT INTO `picture` (`id`, `name`, `url`, `isOriginal`, `pictureSet`) VALUES
(50, 'final_16 - Copy (2).jpeg', 'o_18jv5pu0lb5tt3c7b5hlq1cgci', 1, 9),
(51, 'final_16 - Copy.jpeg', 'o_18jv5pu0l9d4e1a1u39jjgl8jj', 0, 9),
(52, 'final_16.jpeg', 'o_18jv5pu0m1njihib1bbh8ja1845k', 0, 9),
(53, 'final_16_reprod1 - Copy (2).jpeg', 'o_18jv5pu0m1iu1a2vrh6tao1v8dl', 0, 9),
(54, 'final_16_reprod1 - Copy.jpeg', 'o_18jv5pu0m1p84s3i1g3d12o86tfm', 0, 9),
(55, 'final_16_reprod1.jpeg', 'o_18jv5pu0mb8jo7b1skrj10gp6n', 0, 9),
(56, 'final_16_reprod2 - Copy (2).jpeg', 'o_18jv5pu0m4501fqod2ri6l1eido', 0, 9),
(57, 'final_16_reprod2 - Copy.jpeg', 'o_18jv5pu0mjer1e2e13a2jm61qj8p', 0, 9),
(58, 'final_16_reprod2.jpeg', 'o_18jv5pu0m1lv1i5q1rdrs1j1qkrq', 0, 9),
(59, '2012-07-04 00.25.53.jpeg', 'o_18k29sch716f92p8v8hp7doodb', 1, 10),
(60, '2012-07-04 22.42.04.jpeg', 'o_18k29sch7foh1e0e1u2388l189dc', 0, 10),
(61, 'final_05_d5_l1.jpeg', 'o_18k9rplt610f816f51tns1qu4fc1f', 0, 11),
(62, 'final_05_d5_l2.jpeg', 'o_18k9rplt617fv19u4o5hc1pbseg', 0, 11),
(63, 'final_05_d5_l3.jpeg', 'o_18k9rplt61et01j22blj13u71e3ch', 0, 11),
(64, 'final_05_d5_l4.jpeg', 'o_18k9rplt61v8117pc4co1vnp1tt0i', 0, 11),
(65, 'final_05_d5_l5.jpeg', 'o_18k9rplt61ggnu12s31179vt4kj', 0, 11),
(66, 'final_05_original.jpeg', 'o_18k9rplt61aqdotddlh1qurscrk', 1, 11);

-- --------------------------------------------------------

--
-- Table structure for table `pictureorder`
--

CREATE TABLE IF NOT EXISTS `pictureorder` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pOrder` int(10) NOT NULL,
  `picture` int(10) DEFAULT NULL,
  `pictureQueue` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `picture` (`picture`),
  KEY `pictureQueue` (`pictureQueue`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=535 ;

--
-- Dumping data for table `pictureorder`
--

INSERT INTO `pictureorder` (`id`, `pOrder`, `picture`, `pictureQueue`) VALUES
(1, 0, 52, 8),
(2, 0, 56, 8),
(3, 1, 51, 8),
(4, 1, 54, 8),
(5, 2, 53, 8),
(6, 2, 56, 8),
(7, 3, 52, 8),
(8, 3, 53, 8),
(9, 4, 56, 8),
(10, 4, 57, 8),
(11, 5, 56, 8),
(12, 5, 58, 8),
(13, 6, 54, 8),
(14, 6, 57, 8),
(15, 7, 51, 8),
(16, 7, 58, 8),
(17, 8, 53, 8),
(18, 8, 54, 8),
(19, 9, 54, 8),
(20, 9, 56, 8),
(21, 10, 54, 8),
(22, 10, 58, 8),
(23, 11, 54, 8),
(24, 11, 55, 8),
(25, 12, 52, 8),
(26, 12, 54, 8),
(27, 13, 52, 8),
(28, 13, 58, 8),
(29, 14, 55, 8),
(30, 14, 57, 8),
(31, 15, 51, 8),
(32, 15, 52, 8),
(33, 16, 53, 8),
(34, 16, 58, 8),
(35, 17, 53, 8),
(36, 17, 55, 8),
(37, 18, 52, 8),
(38, 18, 55, 8),
(39, 19, 52, 8),
(40, 19, 57, 8),
(41, 20, 51, 8),
(42, 20, 57, 8),
(43, 21, 57, 8),
(44, 21, 58, 8),
(45, 22, 53, 8),
(46, 22, 57, 8),
(47, 23, 51, 8),
(48, 23, 55, 8),
(49, 24, 51, 8),
(50, 24, 56, 8),
(51, 25, 51, 8),
(52, 25, 53, 8),
(53, 26, 55, 8),
(54, 26, 58, 8),
(55, 27, 55, 8),
(56, 27, 56, 8),
(57, 0, 52, 9),
(58, 0, 53, 9),
(59, 1, 51, 9),
(60, 1, 53, 9),
(61, 2, 55, 9),
(62, 2, 56, 9),
(63, 3, 57, 9),
(64, 3, 58, 9),
(65, 4, 56, 9),
(66, 4, 58, 9),
(67, 5, 52, 9),
(68, 5, 58, 9),
(69, 6, 55, 9),
(70, 6, 58, 9),
(71, 7, 53, 9),
(72, 7, 55, 9),
(73, 8, 54, 9),
(74, 8, 57, 9),
(75, 9, 56, 9),
(76, 9, 57, 9),
(77, 10, 52, 9),
(78, 10, 55, 9),
(79, 11, 52, 9),
(80, 11, 56, 9),
(81, 12, 53, 9),
(82, 12, 57, 9),
(83, 13, 51, 9),
(84, 13, 56, 9),
(85, 14, 53, 9),
(86, 14, 58, 9),
(87, 15, 51, 9),
(88, 15, 52, 9),
(89, 16, 53, 9),
(90, 16, 54, 9),
(91, 17, 51, 9),
(92, 17, 55, 9),
(93, 18, 52, 9),
(94, 18, 57, 9),
(95, 19, 54, 9),
(96, 19, 56, 9),
(97, 20, 52, 9),
(98, 20, 54, 9),
(99, 21, 53, 9),
(100, 21, 56, 9),
(101, 22, 54, 9),
(102, 22, 55, 9),
(103, 23, 51, 9),
(104, 23, 57, 9),
(105, 24, 54, 9),
(106, 24, 58, 9),
(107, 25, 51, 9),
(108, 25, 54, 9),
(109, 26, 51, 9),
(110, 26, 58, 9),
(111, 27, 55, 9),
(112, 27, 57, 9),
(113, 0, 59, 13),
(114, 0, 60, 13),
(115, 0, 59, 15),
(116, 0, 60, 15),
(117, 0, 59, 17),
(118, 0, 60, 17),
(119, 0, 54, 21),
(120, 0, 58, 21),
(121, 1, 52, 21),
(122, 1, 57, 21),
(123, 2, 51, 21),
(124, 2, 54, 21),
(125, 3, 54, 21),
(126, 3, 57, 21),
(127, 4, 52, 21),
(128, 4, 55, 21),
(129, 5, 53, 21),
(130, 5, 56, 21),
(131, 6, 51, 21),
(132, 6, 56, 21),
(133, 7, 51, 21),
(134, 7, 57, 21),
(135, 8, 51, 21),
(136, 8, 52, 21),
(137, 9, 51, 21),
(138, 9, 53, 21),
(139, 10, 55, 21),
(140, 10, 56, 21),
(141, 11, 53, 21),
(142, 11, 57, 21),
(143, 12, 54, 21),
(144, 12, 56, 21),
(145, 13, 53, 21),
(146, 13, 55, 21),
(147, 14, 52, 21),
(148, 14, 58, 21),
(149, 15, 52, 21),
(150, 15, 53, 21),
(151, 16, 51, 21),
(152, 16, 58, 21),
(153, 17, 55, 21),
(154, 17, 58, 21),
(155, 18, 57, 21),
(156, 18, 58, 21),
(157, 19, 56, 21),
(158, 19, 57, 21),
(159, 20, 52, 21),
(160, 20, 54, 21),
(161, 21, 54, 21),
(162, 21, 55, 21),
(163, 22, 51, 21),
(164, 22, 55, 21),
(165, 23, 53, 21),
(166, 23, 58, 21),
(167, 24, 53, 21),
(168, 24, 54, 21),
(169, 25, 55, 21),
(170, 25, 57, 21),
(171, 26, 56, 21),
(172, 26, 58, 21),
(173, 27, 52, 21),
(174, 27, 56, 21),
(175, 0, 51, 22),
(176, 0, 52, 22),
(177, 1, 57, 22),
(178, 1, 58, 22),
(179, 2, 54, 22),
(180, 2, 55, 22),
(181, 3, 51, 22),
(182, 3, 55, 22),
(183, 4, 52, 22),
(184, 4, 58, 22),
(185, 5, 55, 22),
(186, 5, 58, 22),
(187, 6, 55, 22),
(188, 6, 56, 22),
(189, 7, 53, 22),
(190, 7, 55, 22),
(191, 8, 53, 22),
(192, 8, 54, 22),
(193, 9, 52, 22),
(194, 9, 57, 22),
(195, 10, 54, 22),
(196, 10, 56, 22),
(197, 11, 51, 22),
(198, 11, 56, 22),
(199, 12, 54, 22),
(200, 12, 58, 22),
(201, 13, 52, 22),
(202, 13, 55, 22),
(203, 14, 51, 22),
(204, 14, 58, 22),
(205, 15, 51, 22),
(206, 15, 57, 22),
(207, 16, 54, 22),
(208, 16, 57, 22),
(209, 17, 55, 22),
(210, 17, 57, 22),
(211, 18, 53, 22),
(212, 18, 57, 22),
(213, 19, 56, 22),
(214, 19, 57, 22),
(215, 20, 53, 22),
(216, 20, 58, 22),
(217, 21, 51, 22),
(218, 21, 53, 22),
(219, 22, 52, 22),
(220, 22, 53, 22),
(221, 23, 53, 22),
(222, 23, 56, 22),
(223, 24, 52, 22),
(224, 24, 56, 22),
(225, 25, 51, 22),
(226, 25, 54, 22),
(227, 26, 56, 22),
(228, 26, 58, 22),
(229, 27, 52, 22),
(230, 27, 54, 22),
(231, 0, 52, 23),
(232, 0, 54, 23),
(233, 1, 53, 23),
(234, 1, 54, 23),
(235, 2, 51, 23),
(236, 2, 58, 23),
(237, 3, 54, 23),
(238, 3, 58, 23),
(239, 4, 53, 23),
(240, 4, 55, 23),
(241, 5, 51, 23),
(242, 5, 56, 23),
(243, 6, 53, 23),
(244, 6, 58, 23),
(245, 7, 52, 23),
(246, 7, 55, 23),
(247, 8, 56, 23),
(248, 8, 57, 23),
(249, 9, 54, 23),
(250, 9, 56, 23),
(251, 10, 55, 23),
(252, 10, 56, 23),
(253, 11, 52, 23),
(254, 11, 57, 23),
(255, 12, 51, 23),
(256, 12, 53, 23),
(257, 13, 53, 23),
(258, 13, 56, 23),
(259, 14, 51, 23),
(260, 14, 57, 23),
(261, 15, 55, 23),
(262, 15, 58, 23),
(263, 16, 52, 23),
(264, 16, 58, 23),
(265, 17, 54, 23),
(266, 17, 55, 23),
(267, 18, 53, 23),
(268, 18, 57, 23),
(269, 19, 54, 23),
(270, 19, 57, 23),
(271, 20, 51, 23),
(272, 20, 52, 23),
(273, 21, 51, 23),
(274, 21, 54, 23),
(275, 22, 52, 23),
(276, 22, 53, 23),
(277, 23, 51, 23),
(278, 23, 55, 23),
(279, 24, 55, 23),
(280, 24, 57, 23),
(281, 25, 57, 23),
(282, 25, 58, 23),
(283, 26, 52, 23),
(284, 26, 56, 23),
(285, 27, 56, 23),
(286, 27, 58, 23),
(287, 0, 52, 24),
(288, 0, 53, 24),
(289, 1, 55, 24),
(290, 1, 58, 24),
(291, 2, 51, 24),
(292, 2, 57, 24),
(293, 3, 51, 24),
(294, 3, 53, 24),
(295, 4, 53, 24),
(296, 4, 54, 24),
(297, 5, 51, 24),
(298, 5, 56, 24),
(299, 6, 55, 24),
(300, 6, 57, 24),
(301, 7, 51, 24),
(302, 7, 58, 24),
(303, 8, 52, 24),
(304, 8, 56, 24),
(305, 9, 52, 24),
(306, 9, 54, 24),
(307, 10, 51, 24),
(308, 10, 55, 24),
(309, 11, 54, 24),
(310, 11, 57, 24),
(311, 12, 53, 24),
(312, 12, 56, 24),
(313, 13, 53, 24),
(314, 13, 55, 24),
(315, 14, 56, 24),
(316, 14, 58, 24),
(317, 15, 53, 24),
(318, 15, 58, 24),
(319, 16, 52, 24),
(320, 16, 58, 24),
(321, 17, 54, 24),
(322, 17, 58, 24),
(323, 18, 57, 24),
(324, 18, 58, 24),
(325, 19, 51, 24),
(326, 19, 54, 24),
(327, 20, 54, 24),
(328, 20, 56, 24),
(329, 21, 51, 24),
(330, 21, 52, 24),
(331, 22, 54, 24),
(332, 22, 55, 24),
(333, 23, 55, 24),
(334, 23, 56, 24),
(335, 24, 53, 24),
(336, 24, 57, 24),
(337, 25, 56, 24),
(338, 25, 57, 24),
(339, 26, 52, 24),
(340, 26, 57, 24),
(341, 27, 52, 24),
(342, 27, 55, 24),
(343, 0, 54, 25),
(344, 0, 55, 25),
(345, 1, 53, 25),
(346, 1, 55, 25),
(347, 2, 52, 25),
(348, 2, 53, 25),
(349, 3, 56, 25),
(350, 3, 57, 25),
(351, 4, 55, 25),
(352, 4, 57, 25),
(353, 5, 55, 25),
(354, 5, 56, 25),
(355, 6, 51, 25),
(356, 6, 53, 25),
(357, 7, 52, 25),
(358, 7, 58, 25),
(359, 8, 51, 25),
(360, 8, 54, 25),
(361, 9, 52, 25),
(362, 9, 56, 25),
(363, 10, 53, 25),
(364, 10, 57, 25),
(365, 11, 53, 25),
(366, 11, 58, 25),
(367, 12, 55, 25),
(368, 12, 58, 25),
(369, 13, 57, 25),
(370, 13, 58, 25),
(371, 14, 51, 25),
(372, 14, 55, 25),
(373, 15, 52, 25),
(374, 15, 55, 25),
(375, 16, 51, 25),
(376, 16, 52, 25),
(377, 17, 51, 25),
(378, 17, 58, 25),
(379, 18, 52, 25),
(380, 18, 57, 25),
(381, 19, 53, 25),
(382, 19, 56, 25),
(383, 20, 56, 25),
(384, 20, 58, 25),
(385, 21, 54, 25),
(386, 21, 56, 25),
(387, 22, 51, 25),
(388, 22, 56, 25),
(389, 23, 54, 25),
(390, 23, 58, 25),
(391, 24, 52, 25),
(392, 24, 54, 25),
(393, 25, 53, 25),
(394, 25, 54, 25),
(395, 26, 54, 25),
(396, 26, 57, 25),
(397, 27, 51, 25),
(398, 27, 57, 25),
(399, 0, 53, 26),
(400, 0, 54, 26),
(401, 1, 51, 26),
(402, 1, 53, 26),
(403, 2, 53, 26),
(404, 2, 57, 26),
(405, 3, 55, 26),
(406, 3, 58, 26),
(407, 4, 52, 26),
(408, 4, 54, 26),
(409, 5, 52, 26),
(410, 5, 53, 26),
(411, 6, 51, 26),
(412, 6, 54, 26),
(413, 7, 55, 26),
(414, 7, 56, 26),
(415, 8, 53, 26),
(416, 8, 56, 26),
(417, 9, 51, 26),
(418, 9, 52, 26),
(419, 10, 54, 26),
(420, 10, 55, 26),
(421, 11, 51, 26),
(422, 11, 58, 26),
(423, 12, 52, 26),
(424, 12, 57, 26),
(425, 13, 55, 26),
(426, 13, 57, 26),
(427, 14, 52, 26),
(428, 14, 55, 26),
(429, 15, 54, 26),
(430, 15, 57, 26),
(431, 16, 53, 26),
(432, 16, 58, 26),
(433, 17, 56, 26),
(434, 17, 58, 26),
(435, 18, 57, 26),
(436, 18, 58, 26),
(437, 19, 54, 26),
(438, 19, 56, 26),
(439, 20, 54, 26),
(440, 20, 58, 26),
(441, 21, 52, 26),
(442, 21, 56, 26),
(443, 22, 51, 26),
(444, 22, 57, 26),
(445, 23, 51, 26),
(446, 23, 55, 26),
(447, 24, 52, 26),
(448, 24, 58, 26),
(449, 25, 53, 26),
(450, 25, 55, 26),
(451, 26, 56, 26),
(452, 26, 57, 26),
(453, 27, 51, 26),
(454, 27, 56, 26),
(455, 0, 51, 27),
(456, 0, 55, 27),
(457, 1, 53, 27),
(458, 1, 56, 27),
(459, 2, 55, 27),
(460, 2, 57, 27),
(461, 3, 54, 27),
(462, 3, 55, 27),
(463, 4, 52, 27),
(464, 4, 53, 27),
(465, 5, 54, 27),
(466, 5, 56, 27),
(467, 6, 52, 27),
(468, 6, 56, 27),
(469, 7, 54, 27),
(470, 7, 58, 27),
(471, 8, 53, 27),
(472, 8, 58, 27),
(473, 9, 55, 27),
(474, 9, 56, 27),
(475, 10, 51, 27),
(476, 10, 53, 27),
(477, 11, 56, 27),
(478, 11, 57, 27),
(479, 12, 52, 27),
(480, 12, 55, 27),
(481, 13, 52, 27),
(482, 13, 58, 27),
(483, 14, 51, 27),
(484, 14, 54, 27),
(485, 15, 51, 27),
(486, 15, 57, 27),
(487, 16, 57, 27),
(488, 16, 58, 27),
(489, 17, 52, 27),
(490, 17, 54, 27),
(491, 18, 53, 27),
(492, 18, 55, 27),
(493, 19, 53, 27),
(494, 19, 57, 27),
(495, 20, 56, 27),
(496, 20, 58, 27),
(497, 21, 52, 27),
(498, 21, 57, 27),
(499, 22, 54, 27),
(500, 22, 57, 27),
(501, 23, 53, 27),
(502, 23, 54, 27),
(503, 24, 55, 27),
(504, 24, 58, 27),
(505, 25, 51, 27),
(506, 25, 52, 27),
(507, 26, 51, 27),
(508, 26, 56, 27),
(509, 27, 51, 27),
(510, 27, 58, 27),
(511, 0, 61, 28),
(512, 0, 62, 28),
(513, 0, 63, 28),
(514, 0, 64, 28),
(515, 0, 65, 28),
(516, 0, 66, 28),
(517, 0, 61, 29),
(518, 0, 62, 29),
(519, 0, 63, 29),
(520, 0, 64, 29),
(521, 0, 65, 29),
(522, 0, 66, 29),
(523, 0, 61, 30),
(524, 0, 62, 30),
(525, 0, 63, 30),
(526, 0, 64, 30),
(527, 0, 65, 30),
(528, 0, 66, 30),
(529, 0, 61, 30),
(530, 0, 62, 31),
(531, 0, 63, 31),
(532, 0, 64, 31),
(533, 0, 65, 31),
(534, 0, 66, 31);

-- --------------------------------------------------------

--
-- Table structure for table `picturequeue`
--

CREATE TABLE IF NOT EXISTS `picturequeue` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT 'Name of queue. Reusability of queue',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=32 ;

--
-- Dumping data for table `picturequeue`
--

INSERT INTO `picturequeue` (`id`, `title`) VALUES
(7, NULL),
(8, NULL),
(9, NULL),
(10, NULL),
(11, NULL),
(12, NULL),
(13, NULL),
(14, NULL),
(15, NULL),
(16, NULL),
(17, NULL),
(18, NULL),
(19, NULL),
(20, NULL),
(21, NULL),
(22, NULL),
(23, NULL),
(24, NULL),
(25, NULL),
(26, NULL),
(27, NULL),
(28, NULL),
(29, NULL),
(30, NULL),
(31, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pictureset`
--

CREATE TABLE IF NOT EXISTS `pictureset` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `text` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `pictureAmount` int(10) NOT NULL COMMENT 'Number of picture in set',
  `person` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `person` (`person`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=12 ;

--
-- Dumping data for table `pictureset`
--

INSERT INTO `pictureset` (`id`, `name`, `text`, `pictureAmount`, `person`) VALUES
(1, 'Bildesett 3', '', 2, 1),
(2, 'Bildesett 1', '', 1, 1),
(3, '', '', 4, 1),
(4, 'Tissefant', '', 4, 5),
(5, 'testbilder', '', 3, 6),
(6, 'Numbered test pictures', 'Numbered pictures of yellow flower for testing', 9, 1),
(7, 'asd', 'asd', 3, 1),
(9, 'Numbered pictures', 'Desc', 9, 1),
(10, 'asd', 'asd', 2, 1),
(11, 'Building at night', 'Building at night, light source from bottom right', 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `result`
--

CREATE TABLE IF NOT EXISTS `result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` enum('category','rating','pair') COLLATE utf8_bin DEFAULT NULL,
  `experimentId` int(10) DEFAULT NULL,
  `pictureOrderId` int(10) DEFAULT NULL,
  `chooseNone` int(10) DEFAULT NULL,
  `personId` int(3) DEFAULT NULL,
  `category` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `experimentId` (`experimentId`),
  KEY `pictureOrder` (`pictureOrderId`),
  KEY `personId` (`personId`),
  KEY `category` (`category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=61 ;

--
-- Dumping data for table `result`
--

INSERT INTO `result` (`id`, `created`, `type`, `experimentId`, `pictureOrderId`, `chooseNone`, `personId`, `category`) VALUES
(1, '2014-04-15 11:03:54', 'category', 42, 529, NULL, 1, 6),
(2, '2014-04-15 11:41:54', 'rating', 41, 523, NULL, 1, NULL),
(3, '2014-04-15 11:41:54', 'rating', 41, 524, NULL, 1, NULL),
(4, '2014-04-15 11:41:54', 'rating', 41, 525, NULL, 1, NULL),
(5, '2014-04-15 11:41:54', 'rating', 41, 526, NULL, 1, NULL),
(6, '2014-04-15 11:41:54', 'rating', 41, 527, NULL, 1, NULL),
(7, '2014-04-15 11:41:54', 'rating', 41, 528, NULL, 1, NULL),
(8, '2014-04-15 14:13:08', 'pair', 34, 176, NULL, 1, NULL),
(9, '2014-04-15 14:13:09', 'pair', 34, 178, NULL, 1, NULL),
(10, '2014-04-15 14:13:11', 'pair', 34, 179, NULL, 1, NULL),
(11, '2014-04-15 14:13:13', 'pair', 34, 181, NULL, 1, NULL),
(12, '2014-04-15 14:13:15', 'pair', 34, 184, NULL, 1, NULL),
(13, '2014-04-15 14:13:16', 'pair', 34, 186, NULL, 1, NULL),
(14, '2014-04-15 14:13:19', 'pair', 34, 187, NULL, 1, NULL),
(15, '2014-04-15 14:13:21', 'pair', 34, 190, NULL, 1, NULL),
(16, '2014-04-15 14:13:22', 'pair', 34, 192, NULL, 1, NULL),
(17, '2014-04-15 14:13:23', 'pair', 34, 193, NULL, 1, NULL),
(18, '2014-04-15 14:13:25', 'pair', 34, 196, NULL, 1, NULL),
(19, '2014-04-15 14:13:27', 'pair', 34, 197, NULL, 1, NULL),
(20, '2014-04-15 14:13:29', 'pair', 34, 199, NULL, 1, NULL),
(21, '2014-04-15 14:13:31', 'pair', 34, 202, NULL, 1, NULL),
(22, '2014-04-15 14:13:33', 'pair', 34, 203, NULL, 1, NULL),
(23, '2014-04-15 14:13:34', 'pair', 34, 206, NULL, 1, NULL),
(24, '2014-04-15 14:13:36', 'pair', 34, 208, NULL, 1, NULL),
(25, '2014-04-15 14:13:38', 'pair', 34, 210, NULL, 1, NULL),
(26, '2014-04-15 14:13:40', 'pair', 34, 212, NULL, 1, NULL),
(27, '2014-04-15 14:13:41', 'pair', 34, 213, NULL, 1, NULL),
(28, '2014-04-15 14:13:44', 'pair', 34, 215, NULL, 1, NULL),
(29, '2014-04-15 14:13:46', 'pair', 34, 217, NULL, 1, NULL),
(30, '2014-04-15 14:13:47', 'pair', 34, 219, NULL, 1, NULL),
(31, '2014-04-15 14:13:49', 'pair', 34, 221, NULL, 1, NULL),
(32, '2014-04-15 14:13:50', 'pair', 34, 223, NULL, 1, NULL),
(33, '2014-04-15 14:13:54', 'pair', 34, 225, NULL, 1, NULL),
(34, '2014-04-15 14:13:56', 'pair', 34, 227, NULL, 1, NULL),
(35, '2014-04-15 14:13:58', 'pair', 34, 230, NULL, 1, NULL),
(36, '2014-04-15 14:14:38', 'rating', 41, 523, NULL, 1, NULL),
(37, '2014-04-15 14:14:38', 'rating', 41, 524, NULL, 1, NULL),
(38, '2014-04-15 14:14:38', 'rating', 41, 525, NULL, 1, NULL),
(39, '2014-04-15 14:14:38', 'rating', 41, 526, NULL, 1, NULL),
(40, '2014-04-15 14:14:38', 'rating', 41, 527, NULL, 1, NULL),
(41, '2014-04-15 14:14:39', 'rating', 41, 528, NULL, 1, NULL),
(42, '2014-04-15 14:17:02', 'category', 42, 529, NULL, 1, 6),
(43, '2014-04-15 14:17:04', 'category', 42, 530, NULL, 1, 8),
(44, '2014-04-15 14:17:05', 'category', 42, 531, NULL, 1, 9),
(45, '2014-04-15 14:17:07', 'category', 42, 532, NULL, 1, 6),
(46, '2014-04-15 14:17:09', 'category', 42, 533, NULL, 1, 8),
(47, '2014-04-15 14:17:11', 'category', 42, 534, NULL, 1, 6),
(48, '2014-04-15 19:46:55', 'category', 42, 529, NULL, 1, 8),
(49, '2014-04-16 12:27:00', 'rating', 41, 523, NULL, 1, NULL),
(50, '2014-04-16 12:27:00', 'rating', 41, 524, NULL, 1, NULL),
(51, '2014-04-16 12:27:01', 'rating', 41, 525, NULL, 1, NULL),
(52, '2014-04-16 12:27:01', 'rating', 41, 526, NULL, 1, NULL),
(53, '2014-04-16 12:27:01', 'rating', 41, 527, NULL, 1, NULL),
(54, '2014-04-16 12:27:01', 'rating', 41, 528, NULL, 1, NULL),
(55, '2014-04-16 12:27:52', 'rating', 41, 523, NULL, 5, NULL),
(56, '2014-04-16 12:27:52', 'rating', 41, 524, NULL, 5, NULL),
(57, '2014-04-16 12:27:52', 'rating', 41, 525, NULL, 5, NULL),
(58, '2014-04-16 12:27:52', 'rating', 41, 526, NULL, 5, NULL),
(59, '2014-04-16 12:27:52', 'rating', 41, 527, NULL, 5, NULL),
(60, '2014-04-16 12:27:52', 'rating', 41, 528, NULL, 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `usertype`
--

CREATE TABLE IF NOT EXISTS `usertype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  `description` varchar(500) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Dumping data for table `usertype`
--

INSERT INTO `usertype` (`id`, `title`, `description`) VALUES
(0, 'Superuser', 'Control of the whole system'),
(1, 'Admin', 'Control of whole system for a particular organization/institute'),
(2, 'Scientist', 'Researcher'),
(3, 'Observer', 'A normal user only allowed to perform experiments'),
(4, 'Anonymous', 'Not registered user');

-- --------------------------------------------------------

--
-- Table structure for table `workplace`
--

CREATE TABLE IF NOT EXISTS `workplace` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL COMMENT '0=Institute/1=organization',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

--
-- Dumping data for table `workplace`
--

INSERT INTO `workplace` (`id`, `name`, `country`, `description`, `type`) VALUES
(2, 'Gjøvik University College (GUC)', 'Norway', 'GUC is a Norwegian university college with an international perspective. Currently, there are 30 countries represented among our staff.\r\n\r\nGUC was established as a result of the reorganisation of Norwegian higher education in 1994 when the Gjøvik College of Engineering and the College of Nursing in Oppland were merged into a single college.', 0),
(3, 'Lillehammer University College', 'Norway', 'Lillehammer University College (LUC) is located in beautiful surroundings in central eastern Norway. The institution has a long tradition as education provider and is part of the Norwegian public higher education system. LUC is situated just outside the city of Lillehammer and has approximately 4200 students and an academic and administrative staff of 330 employees. All on one campus.', 0),
(4, 'The Norwegian Colour and Visual Computing Laboratory', 'Norway', 'A research group within the  Media Technology Laboratory  and  Faculty of Computer Science and Media Technology  at  Gjøvik University College . It was founded in spring 2001 to serve the rising needs for colour management solutions in the graphic arts industry.', 1),
(5, 'Berkley Lab', 'USA', 'In the world of science, Lawrence Berkeley National Laboratory (Berkeley Lab) is synonymous with “excellence.” Thirteen Nobel prizes are associated with Berkeley Lab. Fifty-seven Lab scientists are members of the National Academy of Sciences (NAS), one of the highest honors for a scientist in the United States. ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `workplacebelongs`
--

CREATE TABLE IF NOT EXISTS `workplacebelongs` (
  `personId` int(11) NOT NULL,
  `workPlace` int(10) DEFAULT NULL,
  `id` int(5) DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `workPlace` (`workPlace`),
  KEY `personId` (`personId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `workplacebelongs`
--

INSERT INTO `workplacebelongs` (`personId`, `workPlace`, `id`) VALUES
(3, 2, NULL),
(4, 2, NULL),
(3, 4, NULL),
(4, 4, NULL),
(1, 2, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categoryname`
--
ALTER TABLE `categoryname`
  ADD CONSTRAINT `categoryname_ibfk_1` FOREIGN KEY (`personId`) REFERENCES `person` (`id`);

--
-- Constraints for table `experiment`
--
ALTER TABLE `experiment`
  ADD CONSTRAINT `experiment_ibfk_2` FOREIGN KEY (`experimentType`) REFERENCES `experimenttype` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `experiment_ibfk_3` FOREIGN KEY (`person`) REFERENCES `person` (`id`);

--
-- Constraints for table `experimentcategory`
--
ALTER TABLE `experimentcategory`
  ADD CONSTRAINT `experimentcategory_ibfk_2` FOREIGN KEY (`experiment`) REFERENCES `experiment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `experimentcategory_ibfk_1` FOREIGN KEY (`category`) REFERENCES `categoryname` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `experimentinfotype`
--
ALTER TABLE `experimentinfotype`
  ADD CONSTRAINT `experimentinfotype_ibfk_1` FOREIGN KEY (`experiment`) REFERENCES `experiment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `experimentinfotype_ibfk_2` FOREIGN KEY (`infoType`) REFERENCES `infotype` (`id`);

--
-- Constraints for table `experimentorder`
--
ALTER TABLE `experimentorder`
  ADD CONSTRAINT `experimentorder_ibfk_1` FOREIGN KEY (`pictureQueue`) REFERENCES `picturequeue` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `experimentorder_ibfk_2` FOREIGN KEY (`experimentQueue`) REFERENCES `experimentqueue` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `experimentorder_ibfk_3` FOREIGN KEY (`instruction`) REFERENCES `instruction` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `experimentqueue`
--
ALTER TABLE `experimentqueue`
  ADD CONSTRAINT `experimentqueue_ibfk_1` FOREIGN KEY (`experiment`) REFERENCES `experiment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `experimentresult`
--
ALTER TABLE `experimentresult`
  ADD CONSTRAINT `experimentresult_ibfk_1` FOREIGN KEY (`experiment`) REFERENCES `experiment` (`id`),
  ADD CONSTRAINT `experimentresult_ibfk_2` FOREIGN KEY (`person`) REFERENCES `person` (`id`);

--
-- Constraints for table `infofield`
--
ALTER TABLE `infofield`
  ADD CONSTRAINT `infofield_ibfk_1` FOREIGN KEY (`experiment`) REFERENCES `experiment` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `infofield_ibfk_2` FOREIGN KEY (`person`) REFERENCES `person` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `infofield_ibfk_3` FOREIGN KEY (`infoType`) REFERENCES `infotype` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `infotype`
--
ALTER TABLE `infotype`
  ADD CONSTRAINT `infotype_ibfk_1` FOREIGN KEY (`person`) REFERENCES `person` (`id`);

--
-- Constraints for table `instruction`
--
ALTER TABLE `instruction`
  ADD CONSTRAINT `instruction_ibfk_1` FOREIGN KEY (`personId`) REFERENCES `person` (`id`);

--
-- Constraints for table `person`
--
ALTER TABLE `person`
  ADD CONSTRAINT `person_ibfk_1` FOREIGN KEY (`userType`) REFERENCES `usertype` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `picture`
--
ALTER TABLE `picture`
  ADD CONSTRAINT `picture_ibfk_1` FOREIGN KEY (`pictureSet`) REFERENCES `pictureset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pictureset`
--
ALTER TABLE `pictureset`
  ADD CONSTRAINT `pictureset_ibfk_1` FOREIGN KEY (`person`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `result`
--
ALTER TABLE `result`
  ADD CONSTRAINT `result_ibfk_1` FOREIGN KEY (`experimentId`) REFERENCES `experiment` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `result_ibfk_3` FOREIGN KEY (`personId`) REFERENCES `person` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `result_ibfk_4` FOREIGN KEY (`category`) REFERENCES `categoryname` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `result_ibfk_5` FOREIGN KEY (`pictureOrderId`) REFERENCES `pictureorder` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `workplacebelongs`
--
ALTER TABLE `workplacebelongs`
  ADD CONSTRAINT `workplacebelongs_ibfk_1` FOREIGN KEY (`personId`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `workplacebelongs_ibfk_2` FOREIGN KEY (`workPlace`) REFERENCES `workplace` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
