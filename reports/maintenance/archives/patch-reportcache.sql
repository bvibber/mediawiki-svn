-- Caches information from expensive reports
CREATE TABLE /*$wgDBprefix*/reportcache (

	-- Sequence value for paging, etc.
	`rp_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	
	-- Report name
	`rp_report` VARCHAR(50) NOT NULL,
	
	-- Result title
	`rp_namespace` INT UNSIGNED NOT NULL,
	`rp_title` VARCHAR(255) BINARY NOT NULL,
	
	-- Is the result a redirect?
	`rp_redirect` TINYINT NOT NULL,
	
	-- Additional result parameters
	`rp_params` BLOB NULL,
	
	PRIMARY KEY ( `rp_id` ),
	KEY ( `rp_report`, `rp_namespace`, `rp_redirect` )

) /*$wgDBTableOptions*/;