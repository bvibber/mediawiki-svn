# MySQL script for creating wikipedia database
# Non-unique indexes are created separatedly by
# the buildindexes script, so that data loading
# can be done faster.

CREATE TABLE user (
  user_id int(5) unsigned NOT NULL auto_increment,
  user_name varchar(64) binary NOT NULL,
  user_rights tinyblob,
  user_password tinyblob,
  user_newpassword tinyblob,
  user_email tinytext,
  user_options blob,
  user_watch mediumblob,
  UNIQUE KEY user_id (user_id)
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
  cur_user_text varchar(40) binary NOT NULL,
  cur_timestamp char(14) binary NOT NULL default '',
  cur_minor_edit tinyint(1) default '0',
  cur_restrictions tinyblob,
  cur_counter bigint(20) unsigned default '0',
  cur_ind_title varchar(255) default NULL,
  cur_ind_text mediumtext,
  cur_is_redirect tinyint(1) unsigned NOT NULL default '0',
  UNIQUE KEY cur_id (cur_id)
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
  old_user_text varchar(40) binary NOT NULL,
  old_timestamp char(14) binary NOT NULL default '',
  old_minor_edit tinyint(1) default '0',
  old_flags tinyblob,
  UNIQUE KEY old_id (old_id)
) TYPE=MyISAM PACK_KEYS=1;

# Internal links
#
CREATE TABLE links (
  l_from varchar(255) binary NOT NULL default '',
  l_to int(8) unsigned NOT NULL default '0'
) TYPE=MyISAM;

CREATE TABLE brokenlinks (
  bl_from int(8) unsigned NOT NULL default '0',
  bl_to varchar(255) binary NOT NULL default ''
) TYPE=MyISAM;

CREATE TABLE imagelinks (
  il_from varchar(255) binary NOT NULL default '',
  il_to varchar(255) binary NOT NULL default ''
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
  ipb_timestamp char(14) binary NOT NULL default ''
) TYPE=MyISAM PACK_KEYS=1;

# Uploaded images
#
CREATE TABLE image (
  img_name varchar(255) default NULL,
  img_size int(8) unsigned default '0',
  img_description tinyblob,
  img_user int(5) unsigned default '0',
  img_user_text varchar(40) binary NOT NULL,
  img_timestamp char(14) binary NOT NULL default ''
) TYPE=MyISAM PACK_KEYS=1;

# Historical version of uploaded images
#
CREATE TABLE oldimage (
  oi_name varchar(255) binary NOT NULL,
  oi_archive_name varchar(255) binary NOT NULL,
  oi_size int(8) unsigned default 0,
  oi_description tinyblob,
  oi_user int(5) unsigned default '0',
  oi_user_text varchar(64) binary NOT NULL,
  oi_timestamp char(14) binary NOT NULL default ''
) TYPE=MyISAM PACK_KEYS=1;

