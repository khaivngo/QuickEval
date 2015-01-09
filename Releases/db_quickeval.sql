-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 25, 2014 at 06:35 PM
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

--
-- Dumping data for table `categoryname`
--

INSERT INTO `categoryname` (`id`, `name`, `personId`, `standardFlag`) VALUES
(1, 'Best contrast', NULL, 0),
(2, 'Best saturation', NULL, 0),
(3, 'Best Hue', NULL, 0),
(4, 'The best weighted colourbalance', 3, 1),
(5, 'The funniest looking', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `experiment`
--

CREATE TABLE IF NOT EXISTS `experiment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `info` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `text` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `title` varchar(100) COLLATE utf8_bin DEFAULT NULL,
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
  PRIMARY KEY (`id`),
  KEY `experimentType` (`experimentType`),
  KEY `person` (`person`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;

--
-- Dumping data for table `experiment`
--

INSERT INTO `experiment` (`id`, `info`, `text`, `title`, `date`, `isPublic`, `allowColourBlind`, `backgroundColour`, `allowTies`, `showOriginal`, `samePair`, `horizontalFlip`, `monitorDistance`, `lightType`, `naturalLighting`, `screenLuminance`, `whitePoint`, `whitePointRoom`, `ambientllumination`, `person`, `experimentType`) VALUES
(5, 'Whitebalance experiment', 'Experiment', 'Whitebalance experiment', '2014-02-19 17:38:11', '0 = Hidden', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL),
(6, 'Contrast experiment', 'Experiment2', 'Contrast experiment', '2014-02-19 17:38:27', '0 = Hidden', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(7, 'Årets beste og største test', NULL, 'Årets beste og største test', '2014-02-24 10:17:06', '0 = Hidden', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, 1);

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

-- --------------------------------------------------------

--
-- Table structure for table `experimentinstruction`
--

CREATE TABLE IF NOT EXISTS `experimentinstruction` (
  `instruction` int(3) DEFAULT NULL,
  `experimentOrder` int(10) DEFAULT NULL,
  `id` int(5) NOT NULL AUTO_INCREMENT,
  KEY `instruction` (`instruction`),
  KEY `experimentOrder` (`experimentOrder`),
  KEY `experimentOrder_2` (`experimentOrder`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `experimentorder`
--

CREATE TABLE IF NOT EXISTS `experimentorder` (
  `eOrder` int(11) NOT NULL AUTO_INCREMENT,
  `pictureSet` int(10) DEFAULT NULL,
  `experimentQueue` int(10) DEFAULT NULL,
  `pictureQueue` int(10) DEFAULT NULL,
  PRIMARY KEY (`eOrder`),
  KEY `pictureQueue` (`pictureQueue`),
  KEY `experimentInstruction` (`experimentQueue`),
  KEY `pictureQueue_2` (`pictureQueue`),
  KEY `experimentQueue` (`experimentQueue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `experimentqueue`
--

CREATE TABLE IF NOT EXISTS `experimentqueue` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `experiment` int(10) DEFAULT NULL,
  `experimentOrder` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `experiment` (`experiment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

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
(2, 'Pair', 'pair', 'Pick one'),
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `infofield`
--

INSERT INTO `infofield` (`id`, `text`, `experiment`, `person`, `infoType`) VALUES
(1, 'Big european size 45', NULL, NULL, 3),
(2, 'Red with yellow stripes and superman logo on the front', NULL, NULL, 6);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `instruction`
--

INSERT INTO `instruction` (`id`, `standardFlag`, `text`, `personId`) VALUES
(1, 1, 'Pick the less pixelated of them', 3),
(2, 0, 'Pick the best looking of the two', NULL);

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
  `colourBlindFlag` enum('0','1','','') COLLATE utf8_bin DEFAULT NULL COMMENT '0=normal/1=colourblind',
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=12 ;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`id`, `firstName`, `lastName`, `email`, `password`, `colourBlindFlag`, `age`, `sex`, `nationality`, `title`, `phoneNumber`, `creationDate`, `userType`, `isPublic`) VALUES
(1, 'Lars', 'Hansen', 'lars.hansen@gmail.com', '74bdb1e2736b3238a91dde1b02809f745b36ec82820017d3e5074b16a5701315d67e470f7cc4c4c0105f9c76c5169795f25b9477a516e78be231d66473341a97', '0', 45, 'male', 'Norway', 'Ph.D', 9111111, '0000-00-00 00:00:00', 1, '1'),
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=28 ;

--
-- Dumping data for table `picture`
--

INSERT INTO `picture` (`id`, `name`, `url`, `isOriginal`, `pictureSet`) VALUES
(15, '2014-02-15 12.45.25.jpeg', 'o_18hiqitpdkfc13crnj1akeig0b', 1, NULL),
(16, 'Messefolk 2014 HiG.jpeg', 'o_18hiqitpe107ee1h1c41glfjmoc', 0, NULL),
(17, '2014-02-15 12.45.25.jpeg', 'o_18hirblp3lnr1e8f19qf1i781rfeb', 1, 1),
(18, 'Messefolk 2014 HiG.jpeg', 'o_18hirblp31a4j1po9vu91cfk113lc', 0, 1),
(19, '2014-02-15 12.47.30.jpeg', 'o_18hirfh4f1r6v67f1vcq6te1d4a', 1, 2),
(20, '2014-02-15 12.45.25.jpeg', 'o_18hirgd3i1p581e7bvecser10et', 0, 3),
(21, '2014-02-15 12.47.30.jpeg', 'o_18hirgd3i12ocj0d1mgi1ur63uku', 1, 3),
(22, 'Dlink DWA-131.png', 'o_18hirgd3i4bd1mdmh1i1sd51b78v', 0, 3),
(23, 'Messefolk 2014 HiG.jpeg', 'o_18hirgd3ipl91t9u10ju1a0us9110', 0, 3),
(24, '2014-02-15 12.45.25.jpeg', 'o_18hkatgco9ien3gq3v1qn41876d', 1, 4),
(25, '2014-02-15 12.47.30.jpeg', 'o_18hkatgcoig416u11pp5rib55e', 0, 4),
(26, 'USB Dongle', 'o_18hkatgco1bjqhgdlkj19hrsbuf', 0, 4),
(27, 'Messefolk 2014 HiG.jpeg', 'o_18hkatgco88n2isuglnpo1d2g', 0, 4);

-- --------------------------------------------------------

--
-- Table structure for table `pictureorder`
--

CREATE TABLE IF NOT EXISTS `pictureorder` (
  `pOrder` int(10) NOT NULL,
  `picture` int(10) DEFAULT NULL,
  `pictureQueue` int(10) DEFAULT NULL,
  PRIMARY KEY (`pOrder`),
  KEY `picture` (`picture`),
  KEY `pictureQueue` (`pictureQueue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `picturequeue`
--

CREATE TABLE IF NOT EXISTS `picturequeue` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pOrder` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pOrder` (`pOrder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Dumping data for table `pictureset`
--

INSERT INTO `pictureset` (`id`, `name`, `text`, `pictureAmount`, `person`) VALUES
(1, '', '', 2, 1),
(2, '', '', 1, 1),
(3, '', '', 4, 1),
(4, 'Tissefant', '', 4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `result`
--

CREATE TABLE IF NOT EXISTS `result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `type` enum('category','rating','pair') COLLATE utf8_bin DEFAULT NULL,
  `experimentId` int(10) DEFAULT NULL,
  `pictureOrder` int(10) DEFAULT NULL,
  `personId` int(3) DEFAULT NULL,
  `category` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `experimentId` (`experimentId`),
  KEY `pictureOrder` (`pictureOrder`),
  KEY `personId` (`personId`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

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
(2, 'Gjøvik University College(GUC)', 'Norway', 'GUC is a Norwegian university college with an international perspective. Currently, there are 30 countries represented among our staff.\r\n\r\nGUC was established as a result of the reorganisation of Norwegian higher education in 1994 when the Gjøvik College of Engineering and the College of Nursing in Oppland were merged into a single college.', 0),
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
(4, 4, NULL);

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
-- Constraints for table `experimentinstruction`
--
ALTER TABLE `experimentinstruction`
  ADD CONSTRAINT `experimentinstruction_ibfk_1` FOREIGN KEY (`instruction`) REFERENCES `instruction` (`id`),
  ADD CONSTRAINT `experimentinstruction_ibfk_2` FOREIGN KEY (`experimentOrder`) REFERENCES `experimentorder` (`eOrder`);

--
-- Constraints for table `experimentorder`
--
ALTER TABLE `experimentorder`
  ADD CONSTRAINT `experimentorder_ibfk_1` FOREIGN KEY (`pictureQueue`) REFERENCES `picturequeue` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `experimentorder_ibfk_2` FOREIGN KEY (`experimentQueue`) REFERENCES `experimentqueue` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `pictureorder`
--
ALTER TABLE `pictureorder`
  ADD CONSTRAINT `pictureorder_ibfk_1` FOREIGN KEY (`picture`) REFERENCES `picture` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pictureorder_ibfk_2` FOREIGN KEY (`pictureQueue`) REFERENCES `picturequeue` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `picturequeue`
--
ALTER TABLE `picturequeue`
  ADD CONSTRAINT `picturequeue_ibfk_1` FOREIGN KEY (`pOrder`) REFERENCES `pictureorder` (`pOrder`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pictureset`
--
ALTER TABLE `pictureset`
  ADD CONSTRAINT `pictureset_ibfk_1` FOREIGN KEY (`person`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `result`
--
ALTER TABLE `result`
  ADD CONSTRAINT `result_ibfk_1` FOREIGN KEY (`experimentId`) REFERENCES `experiment` (`id`),
  ADD CONSTRAINT `result_ibfk_2` FOREIGN KEY (`pictureOrder`) REFERENCES `pictureorder` (`pOrder`),
  ADD CONSTRAINT `result_ibfk_3` FOREIGN KEY (`personId`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `result_ibfk_4` FOREIGN KEY (`category`) REFERENCES `categoryname` (`id`);

--
-- Constraints for table `workplacebelongs`
--
ALTER TABLE `workplacebelongs`
  ADD CONSTRAINT `workplacebelongs_ibfk_1` FOREIGN KEY (`personId`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `workplacebelongs_ibfk_2` FOREIGN KEY (`workPlace`) REFERENCES `workplace` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
