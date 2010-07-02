ALTER TABLE /*$wgDBprefix*/code_rev
	CHANGE `cr_status` `cr_status`
	ENUM( 'new', 'fixme', 'reverted', 'resolved', 'ok', 'verified', 'deferred', 'old' )
	NOT NULL DEFAULT 'new';

INSERT INTO /*$wgDBprefix*/updatelog( ul_key ) VALUES( 'add old to code_rev enum' );