# MySQL script for creating wikipedia database
#

#
# Table structure for table 'cur'
#

CREATE TABLE cur (
  cur_id mediumint(8) unsigned NOT NULL auto_increment,
  cur_namespace varchar(20) binary NOT NULL default '',
  cur_title varchar(255) binary NOT NULL default '',
  cur_text mediumtext,
  cur_comment tinyblob,
  cur_user mediumint(8) unsigned default '0',
  cur_user_text tinyblob,
  cur_old_version mediumint(8) unsigned default '0',
  cur_timestamp timestamp(14) NOT NULL,
  cur_minor_edit tinyint(1) default '0',
  cur_restrictions tinyblob,
  cur_params mediumtext,
  cur_counter bigint(20) unsigned default '0',
  cur_cache mediumtext,
  cur_ind_title varchar(255) default NULL,
  INDEX cur_title (cur_title),
  UNIQUE KEY cur_id (cur_id),
  INDEX timeind (cur_timestamp),
  FULLTEXT KEY cur_ind_title (cur_ind_title),
  FULLTEXT KEY cur_text (cur_text)
) TYPE=MyISAM PACK_KEYS=1;

#
# Table structure for table 'linked'
#

CREATE TABLE linked (
  linked_to varchar(255) binary NOT NULL default '',
  linked_from varchar(255) binary NOT NULL default '',
  KEY linked_to (linked_to),
  KEY linked_from (linked_from)
) TYPE=MyISAM;

#
# Table structure for table 'old'
#

CREATE TABLE old (
  old_id mediumint(8) unsigned NOT NULL auto_increment,
  old_title varchar(255) binary NOT NULL default '',
  old_text mediumtext,
  old_comment tinyblob,
  old_user mediumint(8) unsigned default '0',
  old_user_text tinyblob,
  old_old_version mediumint(8) unsigned default '0',
  old_timestamp timestamp(14) NOT NULL,
  old_minor_edit tinyint(1) default '0',
  UNIQUE KEY old_id (old_id),
  KEY timeind (old_timestamp),
  KEY old_title (old_title)
) TYPE=MyISAM PACK_KEYS=1;

#
# Table structure for table 'unlinked'
#

CREATE TABLE unlinked (
  unlinked_from varchar(255) binary NOT NULL default '',
  unlinked_to varchar(255) binary NOT NULL default '',
  KEY unlinked_from (unlinked_from),
  KEY unlinked_to (unlinked_to)
) TYPE=MyISAM;

#
# Table structure for table 'user'
#

CREATE TABLE user (
  user_id mediumint(8) unsigned NOT NULL auto_increment,
  user_name varchar(40) binary NOT NULL,
  user_rights tinytext,
  user_password tinytext,
  user_email tinytext,
  user_options mediumtext,
  user_watch mediumtext,
  user_nickname tinytext,
  UNIQUE KEY user_id (user_id),
  UNIQUE KEY user_name (user_name)
) TYPE=MyISAM PACK_KEYS=1;

CREATE TABLE site_stats (
  ss_row_id mediumint(8) unsigned NOT NULL,
  ss_total_views bigint(20) unsigned default '0',
  ss_total_edits bigint(20) unsigned default '0',
  ss_good_articles bigint(20) unsigned default '0',
  UNIQUE KEY ss_row_id (ss_row_id)
) TYPE=MyISAM;

CREATE TABLE ipblocks (
  ipb_address varchar(40) binary default '',
  ipb_user mediumint(8) unsigned default '0',
  ipb_by mediumint(8) unsigned default '0',
  ipb_reason mediumtext default '',
  INDEX ipb_address (ipb_address),
  INDEX ipb_user (ipb_user)
) TYPE=MyISAM;

