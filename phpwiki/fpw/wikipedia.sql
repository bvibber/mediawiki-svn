# MySQL-Front Dump 1.20 beta
#
# Host: localhost Database: wikipedia
#--------------------------------------------------------
# Server version 3.23.39-nt

USE wikipedia;


#
# Table structure for table 'cur'
#

CREATE TABLE /*!32300 IF NOT EXISTS*/ cur (
  cur_id mediumint(8) unsigned NOT NULL auto_increment,
  cur_title tinyblob ,
  cur_text mediumtext ,
  cur_comment tinyblob ,
  cur_user mediumint(8) unsigned DEFAULT '0' ,
  cur_user_text tinyblob ,
  cur_old_version mediumint(8) unsigned DEFAULT '0' ,
  cur_timestamp timestamp(14) ,
  cur_minor_edit tinyint(1) DEFAULT '0' ,
  cur_restrictions tinyblob ,
  cur_params mediumtext ,
  PRIMARY KEY (cur_id),
  UNIQUE cur_id (cur_id),
  INDEX cur_id_2 (cur_id)
);


#
# Table structure for table 'old'
#

CREATE TABLE /*!32300 IF NOT EXISTS*/ old (
  old_id mediumint(8) unsigned NOT NULL auto_increment,
  old_title tinyblob ,
  old_text mediumtext ,
  old_comment tinyblob ,
  old_user mediumint(8) unsigned DEFAULT '0' ,
  old_user_text tinyblob ,
  old_old_version mediumint(8) unsigned DEFAULT '0' ,
  old_timestamp timestamp(14) ,
  old_minor_edit tinyint(1) DEFAULT '0' ,
  PRIMARY KEY (old_id),
  UNIQUE old_id (old_id),
  INDEX old_id_2 (old_id)
);


#
# Table structure for table 'user'
#

CREATE TABLE /*!32300 IF NOT EXISTS*/ user (
  user_id mediumint(8) unsigned NOT NULL auto_increment,
  user_name tinytext ,
  user_rights tinytext ,
  user_password tinytext ,
  user_email tinytext ,
  user_options mediumtext ,
  user_watch mediumtext ,
  PRIMARY KEY (user_id),
  UNIQUE user_id (user_id),
  INDEX user_id_2 (user_id)
);
