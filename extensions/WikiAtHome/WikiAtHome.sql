
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `mvWiki`
--

-- --------------------------------------------------------

--
-- Table structure for table `wah_jobqueue`
--

CREATE TABLE IF NOT EXISTS `wah_jobqueue` (
  `job_id` int(12) unsigned NOT NULL auto_increment,
  `job_set_id` int(12) unsigned NOT NULL,
  `job_assigned_time` binary(14) default NULL,
  `job_retry_count` int(3) unsigned NOT NULL,
  `job_json` blob NOT NULL,
  PRIMARY KEY  (`job_id`),
  KEY `job_set_id` (`job_set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `wah_jobset`
--

CREATE TABLE IF NOT EXISTS `wah_jobset` (
  `set_id` int(10) unsigned NOT NULL auto_increment,
  `set_namespace` int(11) default NULL,
  `set_title` varchar(255) default NULL,
  `set_jobs_count` int(11) unsigned NOT NULL,
  `set_description` varchar(255) default NULL,
  `set_priority` int(2) NOT NULL default '0',
  `set_done_time` int(14) default NULL,
  PRIMARY KEY  (`set_id`),
  KEY `set_namespace` (`set_namespace`,`set_title`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
