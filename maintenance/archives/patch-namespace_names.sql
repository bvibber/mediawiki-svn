-- New namespace system

DROP TABLE IF EXISTS /*$wgDBprefix*/namespace_names;
CREATE TABLE /*$wgDBprefix*/namespace_names (
  `ns_id` int(8) NOT NULL default '0',
  `ns_name` varchar(200) NOT NULL default '',
  `ns_default` tinyint(1) NOT NULL default '0',
  `ns_canonical` tinyint(1) default NULL
) TYPE=InnoDB;
