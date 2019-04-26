-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 04. Apr, 2019 17:54 PM
-- Tjener-versjon: 10.1.38-MariaDB
-- PHP Version: 7.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quickeval`
--

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `artifactmark`
--

CREATE TABLE `artifactmark` (
  `id` int(11) NOT NULL,
  `picture_queue` int(11) NOT NULL,
  `experiment_id` int(11) NOT NULL,
  `marked_pixels` longtext COLLATE utf8_bin NOT NULL,
  `remark` text COLLATE utf8_bin NOT NULL,
  `person` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `categoryname`
--

CREATE TABLE `categoryname` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `personId` int(3) DEFAULT NULL,
  `standardFlag` tinyint(1) DEFAULT NULL COMMENT '1=by user/0=standard'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `experiment`
--

CREATE TABLE `experiment` (
  `id` int(10) NOT NULL,
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
  `viewingDistance` varchar(30) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `experimentcategory`
--

CREATE TABLE `experimentcategory` (
  `id` int(10) NOT NULL,
  `category` int(10) NOT NULL,
  `experiment` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `experimentinfotype`
--

CREATE TABLE `experimentinfotype` (
  `id` int(10) NOT NULL,
  `experiment` int(10) DEFAULT NULL,
  `infoType` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `experimentorder`
--

CREATE TABLE `experimentorder` (
  `eOrder` int(11) NOT NULL,
  `pictureSet` int(10) DEFAULT NULL,
  `experimentQueue` int(10) DEFAULT NULL,
  `pictureQueue` int(10) DEFAULT NULL,
  `instruction` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `experimentqueue`
--

CREATE TABLE `experimentqueue` (
  `id` int(10) NOT NULL,
  `experiment` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `experimentresult`
--

CREATE TABLE `experimentresult` (
  `id` int(10) NOT NULL,
  `browser` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `os` varchar(20) COLLATE utf8_bin NOT NULL,
  `xDimension` int(4) DEFAULT NULL,
  `yDimension` int(4) DEFAULT NULL,
  `startTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `endTime` datetime DEFAULT NULL,
  `experiment` int(10) DEFAULT NULL,
  `person` int(10) DEFAULT NULL,
  `complete` enum('0','1','','') COLLATE utf8_bin NOT NULL DEFAULT '0' COMMENT '0 = not complete/1 = complete'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `experimenttype`
--

CREATE TABLE `experimenttype` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` enum('pair','rating','category','artifact','triplet') COLLATE utf8_bin NOT NULL,
  `description` varchar(500) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dataark for tabell `experimenttype`
--

INSERT INTO `experimenttype` (`id`, `name`, `type`, `description`) VALUES
(1, 'Rank order', 'rating', 'Rate the pictures'),
(2, 'Paired comparison', 'pair', 'Pick one'),
(3, 'Category judgement', 'category', 'Place images into categories'),
(4, 'Artifact marking', 'artifact', 'Let observers draw marks on interesting spots on images'),
(5, 'Triplet comparison', 'triplet', 'Rate three images based in perceived quality');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `infofield`
--

CREATE TABLE `infofield` (
  `id` int(10) NOT NULL,
  `text` varchar(1000) COLLATE utf8_bin NOT NULL,
  `experiment` int(10) DEFAULT NULL,
  `person` int(10) DEFAULT NULL,
  `infoType` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `infotype`
--

CREATE TABLE `infotype` (
  `id` int(10) NOT NULL,
  `standardFlag` tinyint(1) DEFAULT NULL,
  `info` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `person` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `instruction`
--

CREATE TABLE `instruction` (
  `id` int(11) NOT NULL,
  `standardFlag` tinyint(1) NOT NULL COMMENT '1=by user/0=standard',
  `text` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `personId` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `person`
--

CREATE TABLE `person` (
  `id` int(10) NOT NULL,
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
  `isPublic` enum('0','1','','') COLLATE utf8_bin NOT NULL COMMENT '0=Hidden/1=Public'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `picture`
--

CREATE TABLE `picture` (
  `id` int(10) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `url` varchar(32) COLLATE utf8_bin NOT NULL,
  `isOriginal` tinyint(1) DEFAULT NULL COMMENT '1=Reproduction/0=Original. Picture not reproduction',
  `pictureSet` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `pictureorder`
--

CREATE TABLE `pictureorder` (
  `id` int(10) NOT NULL,
  `pOrder` int(10) NOT NULL,
  `picture` int(10) DEFAULT NULL,
  `pictureQueue` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `picturequeue`
--

CREATE TABLE `picturequeue` (
  `id` int(10) NOT NULL,
  `title` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT 'Name of queue. Reusability of queue'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `pictureset`
--

CREATE TABLE `pictureset` (
  `id` int(10) NOT NULL,
  `name` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `text` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `pictureAmount` int(10) NOT NULL COMMENT 'Number of picture in set',
  `person` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `result`
--

CREATE TABLE `result` (
  `id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` enum('category','rating','pair','triplet','artifact') COLLATE utf8_bin DEFAULT NULL,
  `experimentId` int(10) DEFAULT NULL,
  `pictureOrderId` int(10) DEFAULT NULL,
  `chooseNone` int(10) DEFAULT NULL,
  `personId` int(3) DEFAULT NULL,
  `category` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `usertype`
--

CREATE TABLE `usertype` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  `description` varchar(500) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dataark for tabell `usertype`
--

INSERT INTO `usertype` (`id`, `title`, `description`) VALUES
(0, 'Superuser', 'Control of the whole system'),
(1, 'Admin', 'Control of whole system for a particular organization/institute'),
(2, 'Scientist', 'Researcher'),
(3, 'Observer', 'A normal user only allowed to perform experiments'),
(4, 'Anonymous', 'Not registered user');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `workplace`
--

CREATE TABLE `workplace` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL COMMENT '0=Institute/1=organization'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dataark for tabell `workplace`
--

INSERT INTO `workplace` (`id`, `name`, `country`, `description`, `type`) VALUES
(2, 'Gjøvik University College (GUC)', 'Norway', 'GUC is a Norwegian university college with an international perspective. Currently, there are 30 countries represented among our staff.\r\n\r\nGUC was established as a result of the reorganisation of Norwegian higher education in 1994 when the Gjøvik College of Engineering and the College of Nursing in Oppland were merged into a single college.', 0),
(3, 'Lillehammer University College', 'Norway', 'Lillehammer University College (LUC) is located in beautiful surroundings in central eastern Norway. The institution has a long tradition as education provider and is part of the Norwegian public higher education system. LUC is situated just outside the city of Lillehammer and has approximately 4200 students and an academic and administrative staff of 330 employees. All on one campus.', 0),
(4, 'The Norwegian Colour and Visual Computing Laboratory', 'Norway', 'A research group within the  Media Technology Laboratory  and  Faculty of Computer Science and Media Technology  at  Gjøvik University College . It was founded in spring 2001 to serve the rising needs for colour management solutions in the graphic arts industry.', 1),
(5, 'Other', 'Other', '', 1);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `workplacebelongs`
--

CREATE TABLE `workplacebelongs` (
  `personId` int(11) NOT NULL,
  `workPlace` int(10) DEFAULT NULL,
  `id` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artifactmark`
--
ALTER TABLE `artifactmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categoryname`
--
ALTER TABLE `categoryname`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personId` (`personId`);

--
-- Indexes for table `experiment`
--
ALTER TABLE `experiment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experimentType` (`experimentType`),
  ADD KEY `person` (`person`);

--
-- Indexes for table `experimentcategory`
--
ALTER TABLE `experimentcategory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoryId` (`category`,`experiment`),
  ADD KEY `pictureOrder` (`experiment`),
  ADD KEY `categoryId_2` (`category`),
  ADD KEY `pictureOrder_2` (`experiment`);

--
-- Indexes for table `experimentinfotype`
--
ALTER TABLE `experimentinfotype`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiment` (`experiment`),
  ADD KEY `infoType` (`infoType`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `experimentorder`
--
ALTER TABLE `experimentorder`
  ADD PRIMARY KEY (`eOrder`),
  ADD KEY `pictureQueue` (`pictureQueue`),
  ADD KEY `experimentInstruction` (`experimentQueue`),
  ADD KEY `pictureQueue_2` (`pictureQueue`),
  ADD KEY `experimentQueue` (`experimentQueue`),
  ADD KEY `instruction` (`instruction`);

--
-- Indexes for table `experimentqueue`
--
ALTER TABLE `experimentqueue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiment` (`experiment`);

--
-- Indexes for table `experimentresult`
--
ALTER TABLE `experimentresult`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiment` (`experiment`),
  ADD KEY `person` (`person`);

--
-- Indexes for table `experimenttype`
--
ALTER TABLE `experimenttype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `infofield`
--
ALTER TABLE `infofield`
  ADD PRIMARY KEY (`id`),
  ADD KEY `infoType` (`infoType`),
  ADD KEY `person` (`person`),
  ADD KEY `experiment` (`experiment`);

--
-- Indexes for table `infotype`
--
ALTER TABLE `infotype`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person` (`person`);

--
-- Indexes for table `instruction`
--
ALTER TABLE `instruction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personId` (`personId`);

--
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `userType` (`userType`);

--
-- Indexes for table `picture`
--
ALTER TABLE `picture`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pictureSet` (`pictureSet`);

--
-- Indexes for table `pictureorder`
--
ALTER TABLE `pictureorder`
  ADD PRIMARY KEY (`id`),
  ADD KEY `picture` (`picture`),
  ADD KEY `pictureQueue` (`pictureQueue`);

--
-- Indexes for table `picturequeue`
--
ALTER TABLE `picturequeue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pictureset`
--
ALTER TABLE `pictureset`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person` (`person`);

--
-- Indexes for table `result`
--
ALTER TABLE `result`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experimentId` (`experimentId`),
  ADD KEY `pictureOrder` (`pictureOrderId`),
  ADD KEY `personId` (`personId`),
  ADD KEY `category` (`category`);

--
-- Indexes for table `usertype`
--
ALTER TABLE `usertype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workplace`
--
ALTER TABLE `workplace`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workplacebelongs`
--
ALTER TABLE `workplacebelongs`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `workPlace` (`workPlace`),
  ADD KEY `personId` (`personId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artifactmark`
--
ALTER TABLE `artifactmark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categoryname`
--
ALTER TABLE `categoryname`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `experiment`
--
ALTER TABLE `experiment`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `experimentcategory`
--
ALTER TABLE `experimentcategory`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `experimentinfotype`
--
ALTER TABLE `experimentinfotype`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `experimentorder`
--
ALTER TABLE `experimentorder`
  MODIFY `eOrder` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `experimentqueue`
--
ALTER TABLE `experimentqueue`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `experimentresult`
--
ALTER TABLE `experimentresult`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `experimenttype`
--
ALTER TABLE `experimenttype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `infofield`
--
ALTER TABLE `infofield`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `infotype`
--
ALTER TABLE `infotype`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `instruction`
--
ALTER TABLE `instruction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `person`
--
ALTER TABLE `person`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `picture`
--
ALTER TABLE `picture`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pictureorder`
--
ALTER TABLE `pictureorder`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `picturequeue`
--
ALTER TABLE `picturequeue`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pictureset`
--
ALTER TABLE `pictureset`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `result`
--
ALTER TABLE `result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usertype`
--
ALTER TABLE `usertype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `workplace`
--
ALTER TABLE `workplace`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
