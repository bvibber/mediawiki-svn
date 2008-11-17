ALTER TABLE /*$wgDBprefix*/code_rev
	CHANGE `cr_status` `cr_status` ENUM( 'new', 'fixme', 'reverted', 'resolved', 'ok' ) NOT NULL DEFAULT 'new';
