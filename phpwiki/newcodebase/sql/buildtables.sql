# MySQL script for creating wikipedia database

CREATE TABLE user (
  user_id int(5) unsigned NOT NULL auto_increment,
  user_name varchar(40) binary NOT NULL,
  user_rights tinyblob,
  user_password tinyblob,
  user_email tinytext,
  user_options blob,
  user_watch mediumblob,
  UNIQUE KEY user_id (user_id),
  UNIQUE KEY user_name (user_name(20))
) TYPE=MyISAM PACK_KEYS=1;

# "Current" version of each article
#
CREATE TABLE cur (
  cur_id int(8) unsigned NOT NULL auto_increment,
  cur_namespace tinyint(2) unsigned NOT NULL default '0',
  cur_title varchar(255) binary NOT NULL default '',
  cur_text mediumtext,
  cur_comment tinyblob,
  cur_user int(5) unsigned default '0',
  cur_old_version int(5) unsigned default '0',
  cur_timestamp timestamp(14) NOT NULL,
  cur_minor_edit tinyint(1) default '0',
  cur_restrictions tinyblob,
  cur_counter bigint(20) unsigned default '0',
  cur_ind_title varchar(255) default NULL,
  cur_is_redirect tinyint(1) unsigned NOT NULL default '0',
  UNIQUE KEY cur_id (cur_id),
  INDEX cur_namespace (cur_namespace),
  INDEX cur_title (cur_title(30)),
  INDEX cur_timestamp (cur_timestamp),
  FULLTEXT INDEX cur_ind_title (cur_ind_title),
  FULLTEXT INDEX cur_text (cur_text)
) TYPE=MyISAM PACK_KEYS=1;

# Historical versions of articles
#
CREATE TABLE old (
  old_id int(8) unsigned NOT NULL auto_increment,
  old_namespace tinyint(2) unsigned NOT NULL default '0',
  old_title varchar(255) binary NOT NULL default '',
  old_text mediumtext,
  old_comment tinyblob,
  old_user int(5) unsigned default '0',
  old_old_version int(5) unsigned default '0',
  old_timestamp timestamp(14) NOT NULL,
  old_minor_edit tinyint(1) default '0',
  UNIQUE KEY old_id (old_id),
  INDEX old_namespace (old_namespace),
  INDEX old_title (old_title(30)),
  INDEX old_timestamp (old_timestamp)
) TYPE=MyISAM PACK_KEYS=1;

# Internal links
#
CREATE TABLE links (
  l_from varchar(255) binary NOT NULL default '',
  l_to int(8) unsigned NOT NULL default '0',
  INDEX l_from (l_from),
  INDEX l_to (l_to)
) TYPE=MyISAM;

CREATE TABLE brokenlinks (
  bl_from int(8) unsigned NOT NULL default '0',
  bl_to varchar(255) binary NOT NULL default '',
  INDEX bl_from (bl_from),
  INDEX bl_to (bl_to)
) TYPE=MyISAM;

# Site-wide statistics.
#
CREATE TABLE site_stats (
  ss_row_id int(8) unsigned NOT NULL,
  ss_total_views bigint(20) unsigned default '0',
  ss_total_edits bigint(20) unsigned default '0',
  ss_good_articles bigint(20) unsigned default '0',
  UNIQUE KEY ss_row_id (ss_row_id)
) TYPE=MyISAM;

# Users and/or IP addresses blocked from editing
#
CREATE TABLE ipblocks (
  ipb_address varchar(40) binary default '',
  ipb_user int(8) unsigned default '0',
  ipb_by int(8) unsigned default '0',
  ipb_reason blob default '',
  INDEX ipb_address (ipb_address),
  INDEX ipb_user (ipb_user)
) TYPE=MyISAM PACK_KEYS=1;

