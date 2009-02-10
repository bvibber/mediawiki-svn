/*
 * Minified copy from tables.sql from MediaWiki
 * Useful for setting up a revision table compatible with a MediaWiki dump
*/
CREATE TABLE revision (
  rev_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  rev_page int unsigned NOT NULL,
  rev_text_id int unsigned NOT NULL,
  rev_comment tinyblob NOT NULL,
  rev_user int unsigned NOT NULL default 0,
  rev_user_text varchar(255) binary NOT NULL default '',
  rev_timestamp binary(14) NOT NULL default '',
  rev_minor_edit tinyint unsigned NOT NULL default 0,
  rev_deleted tinyint unsigned NOT NULL default 0,
  rev_len int unsigned,
  rev_parent_id int unsigned default NULL
) MAX_ROWS=10000000 AVG_ROW_LENGTH=1024;
CREATE UNIQUE INDEX rev_page_id ON revision (rev_page, rev_id);
CREATE INDEX rev_timestamp ON revision (rev_timestamp);
CREATE INDEX page_timestamp ON revision (rev_page,rev_timestamp);
CREATE INDEX user_timestamp ON revision (rev_user,rev_timestamp);
CREATE INDEX usertext_timestamp ON revision (rev_user_text,rev_timestamp);
/*
 *  Extra indexes for faster lookups in this particular instance
*/
CREATE INDEX timestamp_usertext ON revision (rev_timestamp,rev_user_text);
