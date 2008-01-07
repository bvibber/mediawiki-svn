<?
/*
*will update tables to new database schema (once we have a more or less stable version)
*
*
*table code:


CREATE TABLE IF NOT EXISTS `mv_mvd_index` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `wiki_title` varchar(100) collate utf8_unicode_ci NOT NULL,
  `mvd_type` varchar(32) collate utf8_unicode_ci NOT NULL,
  `stream_id` int(11) NOT NULL,
  `start_time` int(7) unsigned NOT NULL,
  `end_time` int(7) unsigned default NULL,
  `text` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `wiki_title` (`wiki_title`),
  KEY `mvd_type` (`mvd_type`),
  KEY `stream_id` (`stream_id`),
  KEY `stream_time_start` (`start_time`,`end_time`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='metavid data index' AUTO_INCREMENT=5545 ;

-- --------------------------------------------------------

--
-- Table structure for table `mv_streams`
--

CREATE TABLE IF NOT EXISTS `mv_streams` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(48) collate utf8_unicode_ci NOT NULL,
  `state` enum('available','available_more_otw','live','otw','failed') collate utf8_unicode_ci default NULL,
  `date_start_time` int(10) default NULL,
  `duration` int(7) default NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `adj_start_time` (`date_start_time`),
  KEY `state` (`state`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `mv_stream_files`
--

CREATE TABLE IF NOT EXISTS `mv_stream_files` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `stream_id` int(10) unsigned NOT NULL,
  `base_offset` int(10) default NULL,
  `duration` int(9) default NULL,
  `path_type` enum('mvprime','cap1','ext_cspan','ext_archive_org','ext_url') character set utf8 collate utf8_unicode_ci NOT NULL,
  `file_desc_msg` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `path` text character set utf8 collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `stream_id` (`stream_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='base urls for path types are hard coded' AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `mv_stream_images`
--

CREATE TABLE IF NOT EXISTS `mv_stream_images` (
  `id` int(11) NOT NULL auto_increment,
  `stream_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `stream_id` (`stream_id`,`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='time to images table' AUTO_INCREMENT=1 ;

--
-- Table structure for table `mv_url_cache`
--
 CREATE  TABLE IF NOT EXISTS  `mv_url_cache` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`url` VARCHAR( 255 ) NOT NULL ,
`post_vars` TEXT NULL ,
`req_time` INT NOT NULL ,
`result` TEXT NULL ,
INDEX ( `url` ) ,
UNIQUE (
`id`
)
) ENGINE = MYISAM COMMENT = 'simple url cache (as to not tax external services too much) ' 

*/
?>