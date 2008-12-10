CREATE TABLE IF NOT EXISTS `alerts` (
  `id` int(11) NOT NULL auto_increment,
  `time` timestamp NOT NULL,
  `reporter` varchar(10) NOT NULL,
  `project` varchar(20) NOT NULL,
  `affected` varchar(100) NOT NULL,
  `problem` text NOT NULL,
  `state` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);
