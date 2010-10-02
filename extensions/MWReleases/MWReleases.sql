-- Schema for MWReleases

CREATE TABLE /*_*/mwreleases (
 mwr_id int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
 mwr_name varchar(255) NOT NULL,
 mwr_number varchar(32) NOT NULL,
 mwr_reldate varbinary(32) DEFAULT NULL,
 mwr_eoldate varbinary(32) DEFAULT NULL,
 mwr_branch varchar(32) NOT NULL,
 mwr_tag varchar(32) NOT NULL,
 mwr_announcement varchar(255) DEFAULT NULL,
 mwr_supported int(1) NOT NULL
) /*$wgDBTableOptions*/;
