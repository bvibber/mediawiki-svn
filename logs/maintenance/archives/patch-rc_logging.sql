-- Introduce additional log-related columns to `recentchanges`
-- to facilitate cleaner log formatting
ALTER TABLE /*$wgDBprefix*/recentchanges
	ADD rc_logid INT UNSIGNED NOT NULL DEFAULT '0',
	ADD rc_log_type VARBINARY(255) NULL,
	ADD rc_log_action VARBINARY(255) NULL,
	ADD rc_params BLOB NULL;