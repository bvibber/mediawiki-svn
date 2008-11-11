-- Table to store configuration data set on-wiki.

CREATE TABLE /*$wgDBprefix*/configuration (
	conf_setting varbinary(255) NOT NULL,
	conf_value BLOB NOT NULL,
	PRIMARY KEY (conf_setting)
) /*$wgDBoptions*/;