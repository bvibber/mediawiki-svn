CREATE TABLE /*$wgDBprefix*/text_sg (
  sg_id int unsigned NOT NULL auto_increment,

  sg_text mediumblob NOT NULL,
  sg_cache mediumblob NOT NULL,

  PRIMARY KEY sg_id (sg_id)

) /*$wgDBTableOptions*/ MAX_ROWS=10000000 AVG_ROW_LENGTH=10240;
-- In case tables are created as MyISAM, use row hints for MySQL <5.0 to avoid 4GB limit
