# MySQL script for creating wikipedia database
# Non-unique indexes are created separatedly by
# the buildindexes script, so that data loading
# can be done faster.

DROP TABLE IF EXISTS user;
CREATE TABLE user (
  user_id int(5) unsigned NOT NULL auto_increment,
  user_name varchar(255) binary NOT NULL default '',
  user_rights tinyblob NOT NULL default '',
  user_password tinyblob NOT NULL default '',
  user_newpassword tinyblob NOT NULL default '',
  user_email tinytext NOT NULL default '',
  user_options blob NOT NULL default '',
  user_watch mediumblob NOT NULL default '',
  UNIQUE KEY user_id (user_id)
) TYPE=MyISAM PACK_KEYS=1;

# "Current" version of each article
#
DROP TABLE IF EXISTS cur;
CREATE TABLE cur (
  cur_id int(8) unsigned NOT NULL auto_increment,
  cur_namespace tinyint(2) unsigned NOT NULL default '0',
  cur_title varchar(255) binary NOT NULL default '',
  cur_text mediumtext NOT NULL default '',
  cur_comment tinyblob NOT NULL default '',
  cur_user int(5) unsigned NOT NULL default '0',
  cur_user_text varchar(255) binary NOT NULL default '',
  cur_timestamp char(14) binary NOT NULL default '',
  cur_restrictions tinyblob NOT NULL default '',
  cur_counter bigint(20) unsigned NOT NULL default '0',
  cur_ind_title varchar(255) NOT NULL default '',
  cur_ind_text mediumtext NOT NULL default '',
  cur_is_redirect tinyint(1) unsigned NOT NULL default '0',
  cur_minor_edit tinyint(1) unsigned NOT NULL default '0',
  cur_is_new tinyint(1) unsigned NOT NULL default '0',
  UNIQUE KEY cur_id (cur_id)
) TYPE=MyISAM PACK_KEYS=1;

# Historical versions of articles
#
DROP TABLE IF EXISTS old;
CREATE TABLE old (
  old_id int(8) unsigned NOT NULL auto_increment,
  old_namespace tinyint(2) unsigned NOT NULL default '0',
  old_title varchar(255) binary NOT NULL default '',
  old_text mediumtext NOT NULL default '',
  old_comment tinyblob NOT NULL default '',
  old_user int(5) unsigned NOT NULL default '0',
  old_user_text varchar(255) binary NOT NULL,
  old_timestamp char(14) binary NOT NULL default '',
  old_minor_edit tinyint(1) NOT NULL default '0',
  old_flags tinyblob NOT NULL default '',
  UNIQUE KEY old_id (old_id)
) TYPE=MyISAM PACK_KEYS=1;

# Internal links
#
DROP TABLE IF EXISTS links;
CREATE TABLE links (
  l_from varchar(255) binary NOT NULL default '',
  l_to int(8) unsigned NOT NULL default '0'
) TYPE=MyISAM;

DROP TABLE IF EXISTS brokenlinks;
CREATE TABLE brokenlinks (
  bl_from int(8) unsigned NOT NULL default '0',
  bl_to varchar(255) binary NOT NULL default ''
) TYPE=MyISAM;

DROP TABLE IF EXISTS imagelinks;
CREATE TABLE imagelinks (
  il_from varchar(255) binary NOT NULL default '',
  il_to varchar(255) binary NOT NULL default ''
) TYPE=MyISAM;

# Site-wide statistics.
#
DROP TABLE IF EXISTS site_stats;
CREATE TABLE site_stats (
  ss_row_id int(8) unsigned NOT NULL,
  ss_total_views bigint(20) unsigned default '0',
  ss_total_edits bigint(20) unsigned default '0',
  ss_good_articles bigint(20) unsigned default '0',
  UNIQUE KEY ss_row_id (ss_row_id)
) TYPE=MyISAM;

# Users and/or IP addresses blocked from editing
#
DROP TABLE IF EXISTS ipblocks;
CREATE TABLE ipblocks (
  ipb_address varchar(40) binary NOT NULL default '',
  ipb_user int(8) unsigned NOT NULL default '0',
  ipb_by int(8) unsigned NOT NULL default '0',
  ipb_reason tinyblob NOT NULL default '',
  ipb_timestamp char(14) binary NOT NULL default ''
) TYPE=MyISAM PACK_KEYS=1;

# Uploaded images
#
DROP TABLE IF EXISTS image;
CREATE TABLE image (
  img_name varchar(255) binary NOT NULL default '',
  img_size int(8) unsigned NOT NULL default '0',
  img_description tinyblob NOT NULL default '',
  img_user int(5) unsigned NOT NULL default '0',
  img_user_text varchar(255) binary NOT NULL default '',
  img_timestamp char(14) binary NOT NULL default ''
) TYPE=MyISAM PACK_KEYS=1;

# Historical version of uploaded images
#
DROP TABLE IF EXISTS oldimage;
CREATE TABLE oldimage (
  oi_name varchar(255) binary NOT NULL default '',
  oi_archive_name varchar(255) binary NOT NULL default '',
  oi_size int(8) unsigned NOT NULL default 0,
  oi_description tinyblob NOT NULL default '',
  oi_user int(5) unsigned NOT NULL default '0',
  oi_user_text varchar(255) binary NOT NULL default '',
  oi_timestamp char(14) binary NOT NULL default ''
) TYPE=MyISAM PACK_KEYS=1;

