# MySQL dump 8.16
#
# Host: localhost    Database: wikipedia
#--------------------------------------------------------
# Server version	3.23.47

#
# Table structure for table 'cur'
#

CREATE TABLE cur (
  cur_id mediumint(8) unsigned NOT NULL auto_increment,
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
  cur_linked_links mediumtext,
  cur_unlinked_links mediumtext,
  cur_counter bigint(20) unsigned default '0',
  cur_cache mediumtext,
  cur_ind_title varchar(255) default NULL,
  UNIQUE KEY cur_title (cur_title),
  UNIQUE KEY cur_id (cur_id),
  KEY timeind (cur_timestamp),
  FULLTEXT KEY cur_ind_title (cur_ind_title),
  FULLTEXT KEY cur_text (cur_text)
) TYPE=MyISAM PACK_KEYS=1;

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
# Table structure for table 'user'
#

CREATE TABLE user (
  user_id mediumint(8) unsigned NOT NULL auto_increment,
  user_name tinytext,
  user_rights tinytext,
  user_password tinytext,
  user_email tinytext,
  user_options mediumtext,
  user_watch mediumtext,
  UNIQUE KEY user_id (user_id)
) TYPE=MyISAM PACK_KEYS=1;

