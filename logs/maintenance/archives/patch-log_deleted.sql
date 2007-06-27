-- Introduce `logging`.`log_deleted` column for
-- revision deletion state
ALTER TABLE `/*$wgDBprefix*/logging`
	ADD `log_deleted` TINYINT UNSIGNED NOT NULL DEFAULT '0';