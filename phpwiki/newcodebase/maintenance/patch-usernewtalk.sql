--- This table stores all the IDs of users whose talk
--- page has been changed (the respective row is deleted
--- when the user looks at the page).
--- The respective column in the user table is no longer
--- required and therefore dropped.

CREATE TABLE user_newtalk (
  user_id int(5) NOT NULL default '0',
  user_ip varchar(40) NOT NULL default '',
  PRIMARY KEY  (user_id),
  KEY user_ip (user_ip)
) TYPE=MyISAM;

ALTER TABLE user DROP COLUMN user_newtalk;
