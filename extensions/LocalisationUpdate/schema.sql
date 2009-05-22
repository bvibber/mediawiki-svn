-- SQL tables for LocalisationUpdate extension
CREATE TABLE /*$wgDBprefix*/localisation (
  lo_id int unsigned NOT NULL auto_increment,

  lo_key varchar(255) NOT NULL,
  lo_language varchar(10) NOT NULL,
  lo_value mediumblob NOT NULL,

  PRIMARY KEY (lo_id)
) /*$wgDBTableOptions*/;

CREATE TABLE /*$wgDBprefix*/localisation_file_hash (
  lfh_file varchar(250) NOT NULL,
  lfh_hash varchar(50) NOT NULL,

  UNIQUE KEY (lfh_file)
) /*$wgDBTableOptions*/;