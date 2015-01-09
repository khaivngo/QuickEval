-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 28, 2014 at 06:16 PM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db_quickeval`
--
CREATE DATABASE IF NOT EXISTS `db_quickeval` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=24 ;

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
(9, 'Apekatt i et tre', 1, NULL),
(10, 'Select one', 1, NULL),
(11, 'Select two', 1, NULL),
(12, 'Kategori 1', 1, NULL),
(13, 'Kategori 2', 1, NULL),
(14, 'Kategori 3', 1, NULL),
(15, 'Kat1', 1, NULL),
(16, 'Kat2', 1, NULL),
(17, 'Kat3', 1, NULL),
(18, 'Kat4', 1, NULL),
(19, 'Kategori 1', 1, NULL),
(20, 'Kategori 2', 1, NULL),
(21, 'Kategori 3', 1, NULL),
(22, 'dfgdfgdfg', 1, NULL),
(23, 'dfgdfg', 1, NULL);

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
  `backgroundColour` varchar(20) COLLATE utf8_bin DEFAULT '808080' COMMENT 'HEX value',
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
  `ambientIllumination` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `person` int(10) NOT NULL,
  `experimentType` int(10) DEFAULT NULL,
  `timer` enum('0','1','','') COLLATE utf8_bin NOT NULL DEFAULT '0' COMMENT '0=No timer/1=Timer',
  `inviteHash` varchar(100) COLLATE utf8_bin NOT NULL COMMENT 'Hash used for invitation link',
  PRIMARY KEY (`id`),
  KEY `experimentType` (`experimentType`),
  KEY `person` (`person`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=70 ;

--
-- Dumping data for table `experiment`
--

INSERT INTO `experiment` (`id`, `title`, `shortDescription`, `longDescription`, `date`, `isPublic`, `allowColourBlind`, `backgroundColour`, `allowTies`, `showOriginal`, `samePair`, `horizontalFlip`, `monitorDistance`, `lightType`, `naturalLighting`, `screenLuminance`, `whitePoint`, `whitePointRoom`, `ambientIllumination`, `person`, `experimentType`, `timer`, `inviteHash`) VALUES
(50, 'asdsfgdsfg', 'dfgdfgdfgdfgdf', 'gdfgdfgdfgdfdfgdfg', '2014-05-10 19:04:11', '0 = Hidden', '0', 'backGroundcolor', 1, 0, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 3, '0', '28be2f9f3a'),
(51, 'dfhgdfghdfgg', 'dfgdfgdfgdfg', 'dfgdfgdfgdfgdfg', '2014-05-07 19:03:13', '0 = Hidden', '0', 'backGroundcolor', 1, 0, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 1, '0', 'a2d3afa2f3'),
(52, 'Parsammenligning', 'I-B-I-B', 'dfgdfg', '2014-05-11 19:26:53', '1 = Public', '0', 'backGroundcolor', 1, 1, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 2, '0', 'c3f249fce3'),
(59, 'Rangering', 'I-B', 'I-B', '2014-05-11 17:53:37', '1 = Public', '0', 'backGroundcolor', 1, 1, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 1, '1', '2cca25e1dc'),
(61, 'Kategoribedømmelse', 'I-B', 'I-B', '2014-05-11 17:58:02', '1 = Public', '0', 'backGroundcolor', 1, 1, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 3, '1', '2ff5f49c98'),
(62, 'Rangering bare bilder', 'Rangering bare bilder', 'Rangering bare bilder', '2014-05-11 18:05:43', '1 = Public', '0', 'backGroundcolor', 1, 1, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 1, '1', 'ef420b52b7'),
(66, 'Parsammenligning I-B', 'I-B', 'I-B', '2014-05-12 05:13:38', '1 = Public', '0', 'backGroundcolor', 1, 1, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 2, '1', '0bfc3f68ba'),
(67, 'I-B-I-B-I-B numbered pictures', 'I-B-I-B-I-B numbered pictures', 'I-B-I-B-I-B numbered pictures', '2014-05-27 16:45:18', '1 = Public', '0', 'backGroundcolor', 1, 0, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 2, '0', '67802e85be'),
(68, 'ftest', 'dfgdfg', 'dfgdfg', '2014-05-27 17:18:14', '1 = Public', '0', 'backGroundcolor', 1, 0, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 1, '0', 'f2d8ed9abe'),
(69, 'dfgdfgdfg', 'dfgdfgdfgdf', 'gdfgdfg', '2014-05-27 17:19:56', '1 = Public', '0', 'backGroundcolor', 1, 0, 0, 1, '1', NULL, NULL, '', '', '', '', 1, 2, '0', '80bc4287f0');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=19 ;

--
-- Dumping data for table `experimentcategory`
--

INSERT INTO `experimentcategory` (`id`, `category`, `experiment`) VALUES
(1, 6, 42),
(3, 8, 42),
(4, 9, 42),
(5, 10, 54),
(6, 11, 54),
(7, 12, 55),
(8, 13, 55),
(9, 14, 55),
(10, 15, 56),
(11, 16, 56),
(12, 17, 56),
(13, 18, 56),
(14, 19, 61),
(15, 20, 61),
(16, 21, 61),
(17, 22, 64),
(18, 23, 64);

-- --------------------------------------------------------

--
-- Table structure for table `experimentinfotype`
--

CREATE TABLE IF NOT EXISTS `experimentinfotype` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `experiment` int(10) DEFAULT NULL,
  `infoType` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `experiment` (`experiment`),
  KEY `infoType` (`infoType`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=17 ;

--
-- Dumping data for table `experimentinfotype`
--

INSERT INTO `experimentinfotype` (`id`, `experiment`, `infoType`) VALUES
(1, NULL, 1),
(2, NULL, 2),
(3, NULL, 1),
(4, NULL, 2),
(5, NULL, 1),
(6, NULL, 2),
(7, NULL, 1),
(8, NULL, 2),
(9, NULL, 1),
(10, NULL, 2),
(11, NULL, 1),
(12, NULL, 2),
(13, NULL, 1),
(14, NULL, 2),
(15, NULL, 1),
(16, NULL, 2);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=105 ;

--
-- Dumping data for table `experimentorder`
--

INSERT INTO `experimentorder` (`eOrder`, `pictureSet`, `experimentQueue`, `pictureQueue`, `instruction`) VALUES
(47, NULL, NULL, NULL, NULL),
(69, NULL, 52, NULL, 30),
(70, NULL, 52, 53, NULL),
(74, NULL, 54, NULL, 33),
(75, NULL, 54, 55, NULL),
(76, NULL, 55, NULL, 34),
(77, NULL, 55, 56, NULL),
(78, NULL, 56, 57, NULL),
(79, NULL, 57, 58, NULL),
(80, NULL, 58, NULL, 35),
(81, NULL, 58, 59, NULL),
(82, NULL, 58, NULL, 36),
(83, NULL, 58, NULL, 37),
(84, NULL, 58, 60, NULL),
(85, NULL, 59, NULL, 38),
(86, NULL, 59, 61, NULL),
(87, NULL, 60, NULL, 39),
(88, NULL, 60, 62, NULL),
(89, NULL, 60, NULL, 40),
(91, NULL, 60, 63, NULL),
(92, NULL, 60, NULL, 42),
(93, NULL, 60, 64, NULL),
(94, NULL, 61, 65, NULL),
(96, NULL, 61, 66, NULL),
(98, NULL, 61, 67, NULL),
(103, NULL, 62, 68, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `experimentqueue`
--

CREATE TABLE IF NOT EXISTS `experimentqueue` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `experiment` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `experiment` (`experiment`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=63 ;

--
-- Dumping data for table `experimentqueue`
--

INSERT INTO `experimentqueue` (`id`, `experiment`) VALUES
(57, 50),
(56, 51),
(58, 52),
(52, 59),
(54, 61),
(55, 62),
(59, 66),
(60, 67),
(61, 68),
(62, 69);

-- --------------------------------------------------------

--
-- Table structure for table `experimentresult`
--

CREATE TABLE IF NOT EXISTS `experimentresult` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `browser` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `os` varchar(20) COLLATE utf8_bin NOT NULL,
  `xDimension` int(4) DEFAULT NULL,
  `yDimension` int(4) DEFAULT NULL,
  `startTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `endTime` datetime DEFAULT NULL,
  `experiment` int(10) DEFAULT NULL,
  `person` int(10) DEFAULT NULL,
  `complete` enum('0','1','','') COLLATE utf8_bin NOT NULL DEFAULT '0' COMMENT '0 = not complete/1 = complete',
  PRIMARY KEY (`id`),
  KEY `experiment` (`experiment`),
  KEY `person` (`person`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=43 ;

--
-- Dumping data for table `experimentresult`
--

INSERT INTO `experimentresult` (`id`, `browser`, `os`, `xDimension`, `yDimension`, `startTime`, `endTime`, `experiment`, `person`, `complete`) VALUES
(41, NULL, 'Windows', 1920, 955, '2014-05-28 18:09:46', '2014-05-28 20:17:46', 67, 1, '1'),
(42, NULL, '', NULL, NULL, '2014-05-28 18:12:04', '2014-05-28 20:22:00', 51, 1, '0');

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
(1, 'Rank order', 'rating', 'Rate the shit of the pictures'),
(2, 'Paired comparison', 'pair', 'Pick one'),
(3, 'Category judgement', 'category', 'Drags pictures into corresponding categories ');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=31 ;

--
-- Dumping data for table `infofield`
--

INSERT INTO `infofield` (`id`, `text`, `experiment`, `person`, `infoType`) VALUES
(1, 'Big european size 45', NULL, NULL, 3),
(2, 'Red with yellow stripes and superman logo on the front', NULL, NULL, NULL),
(3, 'hei', NULL, 1, NULL),
(4, 'hei', NULL, 1, NULL),
(9, '', NULL, 1, NULL),
(10, '', NULL, 1, NULL),
(11, 'asdasd', NULL, 1, NULL),
(12, 'asdasd', NULL, 1, NULL),
(13, 'nothing special yo but htanks', NULL, 1, 7),
(14, 'nothing special yo but htanks', NULL, 1, 7),
(15, 'ghehehe', NULL, 13, 7),
(16, 'christopher ', NULL, 1, 1),
(17, 'wel thanks', NULL, 1, 8),
(18, 'christopher ', NULL, 1, 1),
(19, 'wel thanks', NULL, 1, 8),
(20, 'christopher', NULL, 1, 1),
(21, 'jyoyo', NULL, 1, 8),
(22, 'jkh', NULL, 1, 1),
(23, 'jhkgghuk', NULL, 1, 8),
(24, 'fghfgh', NULL, 1, 1),
(25, 'fghfgh', NULL, 1, 8),
(26, 'ghj', NULL, 1, 1),
(27, 'ghjghj', NULL, 1, 8),
(28, 'dsf', NULL, 1, 1),
(29, 'dfg', NULL, 1, 8),
(30, 'j', NULL, 1, 7);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9 ;

--
-- Dumping data for table `infotype`
--

INSERT INTO `infotype` (`id`, `standardFlag`, `info`, `person`) VALUES
(1, 1, 'Firstname', NULL),
(2, 1, 'Lastname', NULL),
(3, 1, 'Age', NULL),
(4, 1, 'Nationality', NULL),
(7, 0, 'Age', 1),
(8, 0, 'Nationality', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=50 ;

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
(18, 0, 'sadløkgjdfgølkjdfgdsfgdsfglkjdsfgløkdjsfgølksdfjgølkdsfjgdsfløkgjdsfløkgjdsfølkgjdfsg', 1),
(19, 0, 'Select prettiest', 1),
(20, 0, 'Select the one with the brightest colours', 1),
(21, 0, 'asdsadsad', 1),
(22, 0, 'asdxzsad', 1),
(23, 0, 'asdasda', 1),
(24, 0, 'High category = pretty image', 1),
(25, 0, 'Something something', 1),
(26, 0, 'Select one', 1),
(27, 0, 'asddsadsa', 1),
(28, 0, 'Instruksjon mellom bildesett', 1),
(29, 0, 'Instruksjon', 1),
(30, 0, 'Instruction instruction', 1),
(31, 0, 'Instruction', 1),
(32, 0, NULL, 1),
(33, 0, 'Instruction instruction', 1),
(34, 0, NULL, 1),
(35, 0, 'Instruksjon 1', 1),
(36, 0, 'Instruksjon 2', 1),
(37, 0, NULL, 1),
(38, 0, 'Instruction instruction', 1),
(39, 0, 'Instruksjon 1', 1),
(40, 0, 'Instruksjon 2', 1),
(42, 0, 'Instruksjon 3', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=18 ;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`id`, `firstName`, `lastName`, `email`, `password`, `colourBlindFlag`, `age`, `sex`, `nationality`, `title`, `phoneNumber`, `creationDate`, `userType`, `isPublic`) VALUES
(1, 'Lars', 'Hansen', 'lars.hansen@gmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '1', 45, 'male', 'Norway', 'Ph.D', 9111111, '0000-00-00 00:00:00', 0, '1'),
(3, 'Jens', 'Solbakken', 'jens.solbakken@hotmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '0', 27, 'male', 'Denmark', NULL, 234842600, '0000-00-00 00:00:00', 2, '1'),
(4, 'Bernt', 'Ullbakk', 'bernt@gmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '0', NULL, NULL, NULL, NULL, NULL, '2014-02-19 09:57:01', 2, '1'),
(5, 'Admin', 'Adminsen', 'admin@gmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '0', 33, 'male', 'Sweden', 'Ph.D', 45671235, '2014-02-21 14:30:28', 1, '1'),
(6, 'Håkon', 'Ludvigsen', 'scientist@gmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '0', 24, 'male', 'Netherlands', 'Ph.D', 45671455, '2014-02-21 14:31:27', 2, '1'),
(7, 'Observer', 'Observersen', 'observer@gmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '1', 12, 'female', 'Argentina', 'Master', 2147483647, '2014-02-21 14:32:21', 3, '1'),
(8, 'Anonym', 'Anonymsen', 'ano@gmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '0', NULL, NULL, NULL, NULL, NULL, '2014-02-25 17:32:13', 4, '1'),
(9, 'Anonymous1', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, '2014-05-01 15:33:54', 4, '0'),
(10, 'Anonymous2', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, '2014-05-01 15:58:46', 4, '0'),
(11, 'Anonymous3', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, '2014-05-01 17:54:38', 4, '0'),
(12, 'Anonymous4', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, '2014-05-02 10:29:37', 4, '0'),
(13, 'Anonymous5', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, '2014-05-02 10:45:39', 4, '0'),
(14, 'Anonymous6', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, '2014-05-11 18:36:28', 4, '0'),
(15, 'Anonymous7', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, '2014-05-11 18:45:17', 4, '0'),
(16, 'Anonymous8', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, '2014-05-13 16:20:57', 4, '0'),
(17, 'Anonymous9', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, '2014-05-14 11:50:20', 4, '0');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=71 ;

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
(66, 'final_05_original.jpeg', 'o_18k9rplt61aqdotddlh1qurscrk', 1, 11),
(67, 'number1.jpeg', 'o_18mu8bsgq12rf33b123b1a3189bd', 1, 12),
(68, 'number2.jpeg', 'o_18mu8bsgq1b9u1ntsof9fgb1seoe', 0, 12),
(69, 'number3.jpeg', 'o_18mu8bsgq1os4muj13r31oosg7nf', 0, 12),
(70, 'number4.jpeg', 'o_18mu8bsgqnfa13e6sm7aucsug', 0, 12);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=905 ;

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
(534, 0, 66, 31),
(535, 0, 62, 32),
(536, 0, 65, 32),
(537, 1, 61, 32),
(538, 1, 65, 32),
(539, 2, 62, 32),
(540, 2, 63, 32),
(541, 3, 63, 32),
(542, 3, 64, 32),
(543, 4, 61, 32),
(544, 4, 63, 32),
(545, 5, 61, 32),
(546, 5, 64, 32),
(547, 6, 61, 32),
(548, 6, 62, 32),
(549, 7, 62, 32),
(550, 7, 64, 32),
(551, 8, 63, 32),
(552, 8, 65, 32),
(553, 9, 64, 32),
(554, 9, 65, 32),
(555, 0, 51, 33),
(556, 0, 55, 33),
(557, 1, 56, 33),
(558, 1, 57, 33),
(559, 2, 55, 33),
(560, 2, 58, 33),
(561, 3, 54, 33),
(562, 3, 55, 33),
(563, 4, 53, 33),
(564, 4, 58, 33),
(565, 5, 53, 33),
(566, 5, 56, 33),
(567, 6, 52, 33),
(568, 6, 55, 33),
(569, 7, 52, 33),
(570, 7, 53, 33),
(571, 8, 53, 33),
(572, 8, 57, 33),
(573, 9, 51, 33),
(574, 9, 52, 33),
(575, 10, 52, 33),
(576, 10, 56, 33),
(577, 11, 56, 33),
(578, 11, 58, 33),
(579, 12, 54, 33),
(580, 12, 57, 33),
(581, 13, 51, 33),
(582, 13, 57, 33),
(583, 14, 54, 33),
(584, 14, 58, 33),
(585, 15, 52, 33),
(586, 15, 54, 33),
(587, 16, 51, 33),
(588, 16, 53, 33),
(589, 17, 54, 33),
(590, 17, 56, 33),
(591, 18, 51, 33),
(592, 18, 58, 33),
(593, 19, 53, 33),
(594, 19, 55, 33),
(595, 20, 52, 33),
(596, 20, 58, 33),
(597, 21, 57, 33),
(598, 21, 58, 33),
(599, 22, 55, 33),
(600, 22, 57, 33),
(601, 23, 55, 33),
(602, 23, 56, 33),
(603, 24, 51, 33),
(604, 24, 54, 33),
(605, 25, 52, 33),
(606, 25, 57, 33),
(607, 26, 51, 33),
(608, 26, 56, 33),
(609, 27, 53, 33),
(610, 27, 54, 33),
(611, 0, 62, 34),
(612, 0, 63, 34),
(613, 1, 63, 34),
(614, 1, 65, 34),
(615, 2, 64, 34),
(616, 2, 65, 34),
(617, 3, 61, 34),
(618, 3, 64, 34),
(619, 4, 62, 34),
(620, 4, 65, 34),
(621, 5, 61, 34),
(622, 5, 63, 34),
(623, 6, 61, 34),
(624, 6, 65, 34),
(625, 7, 61, 34),
(626, 7, 62, 34),
(627, 8, 62, 34),
(628, 8, 64, 34),
(629, 9, 63, 34),
(630, 9, 64, 34),
(631, 0, 61, 35),
(632, 0, 65, 35),
(633, 1, 63, 35),
(634, 1, 64, 35),
(635, 2, 61, 35),
(636, 2, 64, 35),
(637, 3, 61, 35),
(638, 3, 63, 35),
(639, 4, 64, 35),
(640, 4, 65, 35),
(641, 5, 62, 35),
(642, 5, 65, 35),
(643, 6, 61, 35),
(644, 6, 62, 35),
(645, 7, 62, 35),
(646, 7, 63, 35),
(647, 8, 62, 35),
(648, 8, 64, 35),
(649, 9, 63, 35),
(650, 9, 65, 35),
(651, 0, 61, 36),
(652, 0, 65, 36),
(653, 1, 61, 36),
(654, 1, 63, 36),
(655, 2, 63, 36),
(656, 2, 65, 36),
(657, 3, 62, 36),
(658, 3, 65, 36),
(659, 4, 61, 36),
(660, 4, 62, 36),
(661, 5, 62, 36),
(662, 5, 64, 36),
(663, 6, 61, 36),
(664, 6, 64, 36),
(665, 7, 63, 36),
(666, 7, 64, 36),
(667, 8, 62, 36),
(668, 8, 63, 36),
(669, 9, 64, 36),
(670, 9, 65, 36),
(671, 0, 64, 37),
(672, 0, 65, 37),
(673, 1, 62, 37),
(674, 1, 63, 37),
(675, 2, 61, 37),
(676, 2, 63, 37),
(677, 3, 62, 37),
(678, 3, 65, 37),
(679, 4, 62, 37),
(680, 4, 64, 37),
(681, 5, 63, 37),
(682, 5, 65, 37),
(683, 6, 61, 37),
(684, 6, 64, 37),
(685, 7, 61, 37),
(686, 7, 62, 37),
(687, 8, 63, 37),
(688, 8, 64, 37),
(689, 9, 61, 37),
(690, 9, 65, 37),
(691, 0, 63, 38),
(692, 0, 64, 38),
(693, 1, 62, 38),
(694, 1, 63, 38),
(695, 2, 61, 38),
(696, 2, 65, 38),
(697, 3, 61, 38),
(698, 3, 62, 38),
(699, 4, 61, 38),
(700, 4, 64, 38),
(701, 5, 63, 38),
(702, 5, 65, 38),
(703, 6, 61, 38),
(704, 6, 63, 38),
(705, 7, 62, 38),
(706, 7, 65, 38),
(707, 8, 64, 38),
(708, 8, 65, 38),
(709, 9, 62, 38),
(710, 9, 64, 38),
(711, 0, 63, 39),
(712, 0, 64, 39),
(713, 1, 61, 39),
(714, 1, 62, 39),
(715, 2, 62, 39),
(716, 2, 63, 39),
(717, 3, 61, 39),
(718, 3, 63, 39),
(719, 4, 64, 39),
(720, 4, 65, 39),
(721, 5, 62, 39),
(722, 5, 65, 39),
(723, 6, 61, 39),
(724, 6, 65, 39),
(725, 7, 62, 39),
(726, 7, 64, 39),
(727, 8, 63, 39),
(728, 8, 65, 39),
(729, 9, 61, 39),
(730, 9, 64, 39),
(731, 0, 61, 40),
(732, 0, 62, 40),
(733, 0, 63, 40),
(734, 0, 64, 40),
(735, 0, 65, 40),
(736, 0, 66, 40),
(737, 0, 69, 41),
(738, 0, 70, 41),
(739, 1, 68, 41),
(740, 1, 70, 41),
(741, 2, 68, 41),
(742, 2, 69, 41),
(743, 0, 68, 42),
(744, 0, 69, 42),
(745, 1, 69, 42),
(746, 1, 70, 42),
(747, 2, 68, 42),
(748, 2, 70, 42),
(749, 0, 68, 43),
(750, 0, 69, 43),
(751, 1, 68, 43),
(752, 1, 70, 43),
(753, 2, 69, 43),
(754, 2, 70, 43),
(755, 0, 67, 44),
(756, 0, 68, 44),
(757, 0, 69, 44),
(758, 0, 70, 44),
(759, 0, 67, 45),
(760, 0, 68, 45),
(761, 0, 69, 45),
(762, 0, 70, 45),
(763, 0, 67, 46),
(764, 0, 68, 46),
(765, 0, 69, 46),
(766, 0, 70, 46),
(767, 0, 67, 47),
(768, 0, 68, 47),
(769, 0, 69, 47),
(770, 0, 70, 47),
(771, 0, 68, 48),
(772, 0, 69, 48),
(773, 0, 70, 48),
(774, 0, 68, 49),
(775, 0, 69, 49),
(776, 0, 70, 49),
(777, 0, 68, 50),
(778, 0, 69, 50),
(779, 0, 70, 50),
(780, 0, 68, 51),
(781, 0, 69, 51),
(782, 0, 70, 51),
(783, 0, 61, 52),
(784, 0, 62, 52),
(785, 0, 63, 52),
(786, 0, 64, 52),
(787, 0, 65, 52),
(788, 0, 61, 53),
(789, 0, 62, 53),
(790, 0, 63, 53),
(791, 0, 64, 53),
(792, 0, 65, 53),
(793, 0, 61, 54),
(794, 0, 64, 54),
(795, 1, 62, 54),
(796, 1, 65, 54),
(797, 2, 63, 54),
(798, 2, 65, 54),
(799, 3, 61, 54),
(800, 3, 65, 54),
(801, 4, 61, 54),
(802, 4, 62, 54),
(803, 5, 62, 54),
(804, 5, 64, 54),
(805, 6, 61, 54),
(806, 6, 63, 54),
(807, 7, 62, 54),
(808, 7, 63, 54),
(809, 8, 64, 54),
(810, 8, 65, 54),
(811, 9, 63, 54),
(812, 9, 64, 54),
(813, 0, 61, 55),
(814, 0, 62, 55),
(815, 0, 63, 55),
(816, 0, 64, 55),
(817, 0, 65, 55),
(818, 0, 61, 56),
(819, 0, 62, 56),
(820, 0, 63, 56),
(821, 0, 64, 56),
(822, 0, 65, 56),
(823, 0, 61, 57),
(824, 0, 62, 57),
(825, 0, 63, 57),
(826, 0, 64, 57),
(827, 0, 65, 57),
(828, 0, 68, 58),
(829, 0, 69, 58),
(830, 0, 70, 58),
(831, 0, 68, 59),
(832, 0, 69, 59),
(833, 1, 69, 59),
(834, 1, 70, 59),
(835, 2, 68, 59),
(836, 2, 70, 59),
(837, 0, 68, 60),
(838, 0, 69, 60),
(839, 1, 69, 60),
(840, 1, 70, 60),
(841, 2, 68, 60),
(842, 2, 70, 60),
(843, 0, 61, 61),
(844, 0, 62, 61),
(845, 1, 61, 61),
(846, 1, 64, 61),
(847, 2, 61, 61),
(848, 2, 65, 61),
(849, 3, 64, 61),
(850, 3, 65, 61),
(851, 4, 63, 61),
(852, 4, 64, 61),
(853, 5, 61, 61),
(854, 5, 63, 61),
(855, 6, 62, 61),
(856, 6, 65, 61),
(857, 7, 62, 61),
(858, 7, 64, 61),
(859, 8, 62, 61),
(860, 8, 63, 61),
(861, 9, 63, 61),
(862, 9, 65, 61),
(863, 0, 69, 62),
(864, 0, 70, 62),
(865, 1, 68, 62),
(866, 1, 70, 62),
(867, 2, 68, 62),
(868, 2, 69, 62),
(869, 0, 68, 63),
(870, 0, 70, 63),
(871, 1, 68, 63),
(872, 1, 69, 63),
(873, 2, 69, 63),
(874, 2, 70, 63),
(875, 0, 68, 64),
(876, 0, 69, 64),
(877, 1, 69, 64),
(878, 1, 70, 64),
(879, 2, 68, 64),
(880, 2, 70, 64),
(881, 0, 51, 65),
(882, 0, 52, 65),
(883, 0, 53, 65),
(884, 0, 54, 65),
(885, 0, 55, 65),
(886, 0, 56, 65),
(887, 0, 57, 65),
(888, 0, 58, 65),
(889, 0, 61, 66),
(890, 0, 62, 66),
(891, 0, 63, 66),
(892, 0, 64, 66),
(893, 0, 65, 66),
(894, 0, 61, 67),
(895, 0, 62, 67),
(896, 0, 63, 67),
(897, 0, 64, 67),
(898, 0, 65, 67),
(899, 0, 68, 68),
(900, 0, 69, 68),
(901, 1, 69, 68),
(902, 1, 70, 68),
(903, 2, 68, 68),
(904, 2, 70, 68);

-- --------------------------------------------------------

--
-- Table structure for table `picturequeue`
--

CREATE TABLE IF NOT EXISTS `picturequeue` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT 'Name of queue. Reusability of queue',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=69 ;

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
(31, NULL),
(32, NULL),
(33, NULL),
(34, NULL),
(35, NULL),
(36, NULL),
(37, NULL),
(38, NULL),
(39, NULL),
(40, NULL),
(41, NULL),
(42, NULL),
(43, NULL),
(44, NULL),
(45, NULL),
(46, NULL),
(47, NULL),
(48, NULL),
(49, NULL),
(50, NULL),
(51, NULL),
(52, NULL),
(53, NULL),
(54, NULL),
(55, NULL),
(56, NULL),
(57, NULL),
(58, NULL),
(59, NULL),
(60, NULL),
(61, NULL),
(62, NULL),
(63, NULL),
(64, NULL),
(65, NULL),
(66, NULL),
(67, NULL),
(68, NULL);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=13 ;

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
(11, 'Building at night', 'Building at night, light source from bottom right', 6, 1),
(12, 'Chris Numbered images with names', 'IMages with names n numberu', 4, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=31 ;

--
-- Dumping data for table `result`
--

INSERT INTO `result` (`id`, `created`, `type`, `experimentId`, `pictureOrderId`, `chooseNone`, `personId`, `category`) VALUES
(14, '2014-05-27 17:37:54', 'pair', 67, 863, 1, 1, NULL),
(15, '2014-05-27 17:37:55', 'pair', 67, 865, 1, 1, NULL),
(16, '2014-05-27 17:37:55', 'pair', 67, 867, 1, 1, NULL),
(17, '2014-05-27 17:38:02', 'pair', 67, 869, 1, 1, NULL),
(18, '2014-05-27 17:38:02', 'pair', 67, 871, 1, 1, NULL),
(19, '2014-05-27 17:38:03', 'pair', 67, 873, 1, 1, NULL),
(20, '2014-05-28 18:09:37', 'pair', 67, 863, 1, 1, NULL),
(21, '2014-05-28 18:09:38', 'pair', 67, 865, 1, 1, NULL),
(22, '2014-05-28 18:09:38', 'pair', 67, 867, 1, 1, NULL),
(23, '2014-05-28 18:09:40', 'pair', 67, 869, 1, 1, NULL),
(24, '2014-05-28 18:09:40', 'pair', 67, 871, 1, 1, NULL),
(25, '2014-05-28 18:09:41', 'pair', 67, 873, 1, 1, NULL),
(26, '2014-05-28 18:09:42', 'pair', 67, 875, 1, 1, NULL),
(27, '2014-05-28 18:09:43', 'pair', 67, 877, 1, 1, NULL),
(28, '2014-05-28 18:09:43', 'pair', 67, 879, 1, 1, NULL),
(29, '2014-05-28 18:09:44', 'pair', 67, 879, 1, 1, NULL),
(30, '2014-05-28 18:09:46', 'pair', 67, 879, 1, 1, NULL);

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
  ADD CONSTRAINT `experimentresult_ibfk_1` FOREIGN KEY (`experiment`) REFERENCES `experiment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `experimentresult_ibfk_2` FOREIGN KEY (`person`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
