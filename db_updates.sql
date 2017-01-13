CREATE TABLE `artifactmark` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `picture_queue` int(11) NOT NULL,
  `experiment_id` int(11) NOT NULL,
  `marked_pixels` longtext NOT NULL,
  `remark` text NOT NULL,
  `person` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `experimenttype` CHANGE `type` `type` ENUM('pair','rating','category','artifact','triplet') CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;

ALTER TABLE `result` CHANGE `type` `type` ENUM('category','rating','pair','triplet','artifact') CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;

INSERT INTO `experimenttype` (`id`, `name`, `type`, `description`)
VALUES (4, 'Artifact marking', 'artifact', 'Let observers draw marks on interesting spots on images'),
(5, 'Triplet comparison', 'triplet', 'Rate three images based in perceived quality');