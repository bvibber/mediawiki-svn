CREATE TABLE IF NOT EXISTS `localisation` (
  lo_id unsigned NOT NULL auto_increment,
  lo_key varchar(255) NOT NULL,
  lo_language varchar(10) NOT NULL,
  lo_value mediumblob NOT NULL,
  PRIMARY KEY (lo_id)
) ENGINE=MyISAM DEFAULT CHARSET=binary;

CREATE TABLE IF NOT EXISTS `localisation_file_hash` (
  lfh_file varchar(250) NOT NULL,
  lfh_hash varchar(50) NOT NULL,
  UNIQUE KEY `file` (lfh_file)
) ENGINE=MyISAM DEFAULT CHARSET=binary;
