-- Stores information about the report cache state
CREATE TABLE /*$wgDBprefix*/reportcache_info (

	-- Report name
	`rci_report` VARCHAR(50) NOT NULL,

	-- Timestamp of last update
	`rci_updated` BINARY(14) NOT NULL,

	PRIMARY KEY( `rci_report` )

) /*$wgDBTableOptions*/;