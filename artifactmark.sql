CREATE TABLE IF NOT EXISTS `artifactmark` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `picture_id` int(11) NOT NULL,
  `picture_queue` int(11) NOT NULL,
  `experiment_id` int(11) NOT NULL,
  `marked_pixels` mediumtext NOT NULL,
  `remark` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
