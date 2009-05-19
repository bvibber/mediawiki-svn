SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `localisation` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `identifier` varchar(2048) collate utf8_bin NOT NULL,
  `language` varchar(2048) collate utf8_bin NOT NULL,
  `value` varchar(2048) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=13858 ;

CREATE TABLE IF NOT EXISTS `localisation_file_hash` (
  `file` varchar(250) NOT NULL,
  `hash` varchar(50) NOT NULL,
  UNIQUE KEY `file` (`file`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
