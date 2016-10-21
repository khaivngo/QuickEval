-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 21. Okt, 2016 04:48 
-- Server-versjon: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mariusp`
--

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `artifactmark`
--

CREATE TABLE IF NOT EXISTS `artifactmark` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `picture_queue` int(11) NOT NULL,
  `experiment_id` int(11) NOT NULL,
  `marked_pixels` longtext NOT NULL,
  `remark` text NOT NULL,
  `person` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `categoryname`
--

CREATE TABLE IF NOT EXISTS `categoryname` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `personId` int(3) DEFAULT NULL,
  `standardFlag` tinyint(1) DEFAULT NULL COMMENT '1=by user/0=standard',
  PRIMARY KEY (`id`),
  KEY `personId` (`personId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `experiment`
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
  `viewingDistance` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `experimentType` (`experimentType`),
  KEY `person` (`person`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=380 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `experimentcategory`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `experimentinfotype`
--

CREATE TABLE IF NOT EXISTS `experimentinfotype` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `experiment` int(10) DEFAULT NULL,
  `infoType` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `experiment` (`experiment`),
  KEY `infoType` (`infoType`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `experimentorder`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=48 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `experimentqueue`
--

CREATE TABLE IF NOT EXISTS `experimentqueue` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `experiment` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `experiment` (`experiment`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=370 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `experimentresult`
--

CREATE TABLE IF NOT EXISTS `experimentresult` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `browser` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `os` varchar(20) COLLATE utf8_bin NOT NULL,
  `xDimension` int(4) DEFAULT NULL,
  `yDimension` int(4) DEFAULT NULL,
  `startTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `endTime` datetime DEFAULT NULL,
  `experiment` int(10) DEFAULT NULL,
  `person` int(10) DEFAULT NULL,
  `complete` enum('0','1','','') COLLATE utf8_bin NOT NULL DEFAULT '0' COMMENT '0 = not complete/1 = complete',
  PRIMARY KEY (`id`),
  KEY `experiment` (`experiment`),
  KEY `person` (`person`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=404 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `experimenttype`
--

CREATE TABLE IF NOT EXISTS `experimenttype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` enum('pair','rating','category','artifact','triplet') COLLATE utf8_bin NOT NULL,
  `description` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `infofield`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `infotype`
--

CREATE TABLE IF NOT EXISTS `infotype` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `standardFlag` tinyint(1) DEFAULT NULL,
  `info` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `person` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `person` (`person`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `instruction`
--

CREATE TABLE IF NOT EXISTS `instruction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `standardFlag` tinyint(1) NOT NULL COMMENT '1=by user/0=standard',
  `text` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `personId` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `personId` (`personId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `person`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=389 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `picture`
--

CREATE TABLE IF NOT EXISTS `picture` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `url` varchar(32) COLLATE utf8_bin NOT NULL,
  `isOriginal` tinyint(1) DEFAULT NULL COMMENT '1=Reproduction/0=Original. Picture not reproduction',
  `pictureSet` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pictureSet` (`pictureSet`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `pictureorder`
--

CREATE TABLE IF NOT EXISTS `pictureorder` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pOrder` int(10) NOT NULL,
  `picture` int(10) DEFAULT NULL,
  `pictureQueue` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `picture` (`picture`),
  KEY `pictureQueue` (`pictureQueue`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=25602 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `picturequeue`
--

CREATE TABLE IF NOT EXISTS `picturequeue` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT 'Name of queue. Reusability of queue',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=884 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `pictureset`
--

CREATE TABLE IF NOT EXISTS `pictureset` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `text` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `pictureAmount` int(10) NOT NULL COMMENT 'Number of picture in set',
  `person` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `person` (`person`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=356 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `result`
--

CREATE TABLE IF NOT EXISTS `result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` enum('category','rating','pair','triplet','artifact') COLLATE utf8_bin DEFAULT NULL,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=85 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `usertype`
--

CREATE TABLE IF NOT EXISTS `usertype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  `description` varchar(500) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `workplace`
--

CREATE TABLE IF NOT EXISTS `workplace` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL COMMENT '0=Institute/1=organization',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `workplacebelongs`
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
-- Begrensninger for dumpede tabeller
--

--
-- Begrensninger for tabell `categoryname`
--
ALTER TABLE `categoryname`
  ADD CONSTRAINT `categoryname_ibfk_1` FOREIGN KEY (`personId`) REFERENCES `person` (`id`);

--
-- Begrensninger for tabell `experiment`
--
ALTER TABLE `experiment`
  ADD CONSTRAINT `experiment_ibfk_2` FOREIGN KEY (`experimentType`) REFERENCES `experimenttype` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `experiment_ibfk_3` FOREIGN KEY (`person`) REFERENCES `person` (`id`);

--
-- Begrensninger for tabell `experimentinfotype`
--
ALTER TABLE `experimentinfotype`
  ADD CONSTRAINT `experimentinfotype_ibfk_1` FOREIGN KEY (`experiment`) REFERENCES `experiment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `experimentinfotype_ibfk_2` FOREIGN KEY (`infoType`) REFERENCES `infotype` (`id`);

--
-- Begrensninger for tabell `experimentorder`
--
ALTER TABLE `experimentorder`
  ADD CONSTRAINT `experimentorder_ibfk_1` FOREIGN KEY (`pictureQueue`) REFERENCES `picturequeue` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `experimentorder_ibfk_2` FOREIGN KEY (`experimentQueue`) REFERENCES `experimentqueue` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `experimentorder_ibfk_3` FOREIGN KEY (`instruction`) REFERENCES `instruction` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrensninger for tabell `experimentqueue`
--
ALTER TABLE `experimentqueue`
  ADD CONSTRAINT `experimentqueue_ibfk_1` FOREIGN KEY (`experiment`) REFERENCES `experiment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrensninger for tabell `experimentresult`
--
ALTER TABLE `experimentresult`
  ADD CONSTRAINT `experimentresult_ibfk_1` FOREIGN KEY (`experiment`) REFERENCES `experiment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `experimentresult_ibfk_2` FOREIGN KEY (`person`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrensninger for tabell `infofield`
--
ALTER TABLE `infofield`
  ADD CONSTRAINT `infofield_ibfk_1` FOREIGN KEY (`experiment`) REFERENCES `experiment` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `infofield_ibfk_2` FOREIGN KEY (`person`) REFERENCES `person` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `infofield_ibfk_3` FOREIGN KEY (`infoType`) REFERENCES `infotype` (`id`) ON DELETE SET NULL;

--
-- Begrensninger for tabell `infotype`
--
ALTER TABLE `infotype`
  ADD CONSTRAINT `infotype_ibfk_1` FOREIGN KEY (`person`) REFERENCES `person` (`id`);

--
-- Begrensninger for tabell `instruction`
--
ALTER TABLE `instruction`
  ADD CONSTRAINT `instruction_ibfk_1` FOREIGN KEY (`personId`) REFERENCES `person` (`id`);

--
-- Begrensninger for tabell `person`
--
ALTER TABLE `person`
  ADD CONSTRAINT `person_ibfk_1` FOREIGN KEY (`userType`) REFERENCES `usertype` (`id`) ON UPDATE CASCADE;

--
-- Begrensninger for tabell `picture`
--
ALTER TABLE `picture`
  ADD CONSTRAINT `picture_ibfk_1` FOREIGN KEY (`pictureSet`) REFERENCES `pictureset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrensninger for tabell `pictureset`
--
ALTER TABLE `pictureset`
  ADD CONSTRAINT `pictureset_ibfk_1` FOREIGN KEY (`person`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrensninger for tabell `result`
--
ALTER TABLE `result`
  ADD CONSTRAINT `result_ibfk_1` FOREIGN KEY (`experimentId`) REFERENCES `experiment` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `result_ibfk_3` FOREIGN KEY (`personId`) REFERENCES `person` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `result_ibfk_4` FOREIGN KEY (`category`) REFERENCES `categoryname` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `result_ibfk_5` FOREIGN KEY (`pictureOrderId`) REFERENCES `pictureorder` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Begrensninger for tabell `workplacebelongs`
--
ALTER TABLE `workplacebelongs`
  ADD CONSTRAINT `workplacebelongs_ibfk_1` FOREIGN KEY (`personId`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `workplacebelongs_ibfk_2` FOREIGN KEY (`workPlace`) REFERENCES `workplace` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
