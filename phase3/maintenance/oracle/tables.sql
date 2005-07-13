-- SQL to create the initial tables for the MediaWiki database.
-- This is read and executed by the install script; you should
-- not have to run it by itself unless doing a manual install.

CREATE SEQUENCE user_user_id_seq;

CREATE TABLE "user" (
  user_id		NUMBER(5) NOT NULL PRIMARY KEY,
  user_name		VARCHAR2(255) DEFAULT '' NOT NULL,
  user_real_name	VARCHAR2(255) DEFAULT '',
  user_password		VARCHAR2(128) DEFAULT '',
  user_newpassword	VARCHAR2(128) default '',
  user_email		VARCHAR2(255) default '',
  user_options		CLOB default '',
  user_touched		TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL,
  user_token		CHAR(32) default '',
  user_email_authenticated TIMESTAMP DEFAULT NULL,
  user_email_token	CHAR(32),
  user_email_token_expires TIMESTAMP DEFAULT NULL
);
CREATE UNIQUE INDEX user_name_idx ON "user" (user_name);
CREATE INDEX user_email_token_idx ON "user" (user_email_token);

CREATE TABLE user_groups (
	ug_user		NUMBER(5) DEFAULT '0' NOT NULL
				REFERENCES "user" (user_id)
				ON DELETE CASCADE,
	ug_group	VARCHAR2(16) NOT NULL,
	CONSTRAINT user_groups_pk PRIMARY KEY (ug_user, ug_group)
);
CREATE INDEX user_groups_group_idx ON user_groups(ug_group);

CREATE TABLE user_newtalk (
	user_id		NUMBER(5) DEFAULT 0 NOT NULL,
	user_ip		VARCHAR2(40) DEFAULT '' NOT NULL
);
CREATE INDEX user_newtalk_id_idx ON user_newtalk(user_id);
CREATE INDEX user_newtalk_ip_idx ON user_newtalk(user_ip);

CREATE SEQUENCE page_page_id_seq;
CREATE TABLE page (
	page_id			NUMBER(8) NOT NULL PRIMARY KEY,
	page_namespace		NUMBER(5) NOT NULL,
	page_title		VARCHAR(255) NOT NULL,
	page_restrictions	CLOB DEFAULT '',
	page_counter 		NUMBER(20) DEFAULT 0 NOT NULL,
	page_is_redirect	NUMBER(1) DEFAULT 0 NOT NULL,
	page_is_new		NUMBER(1) DEFAULT 0 NOT NULL,
	page_random		NUMBER(25, 24) NOT NULL,
	page_touched		TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL,
	page_latest		NUMBER(8) NOT NULL,
	page_len 		NUMBER(8) DEFAULT 0
);
CREATE UNIQUE INDEX page_id_namespace_title_idx ON page(page_namespace, page_title);
CREATE INDEX page_random_idx ON page(page_random);
CREATE INDEX page_len_idx ON page(page_len);

CREATE SEQUENCE rev_rev_id_val;
CREATE TABLE revision (
	rev_id		NUMBER(8) NOT NULL,
	rev_page	NUMBER(8) NOT NULL
				REFERENCES page (page_id)
				ON DELETE CASCADE,
	rev_text_id	NUMBER(8) NOT NULL,
	rev_comment	CLOB,
	rev_user	NUMBER(8) DEFAULT 0 NOT NULL,
	rev_user_text	VARCHAR2(255) DEFAULT '' NOT NULL,
	rev_timestamp	TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL,
	rev_minor_edit	NUMBER(1) DEFAULT 0 NOT NULL,
	rev_deleted	NUMBER(1) DEFAULT 0 NOT NULL,
	CONSTRAINT revision_pk PRIMARY KEY (rev_page, rev_id)
);

CREATE UNIQUE INDEX rev_id_idx ON revision(rev_id);
CREATE INDEX rev_timestamp_idx ON revision(rev_timestamp);
CREATE INDEX rev_page_timestamp_idx ON revision(rev_page, rev_timestamp);
CREATE INDEX rev_user_timestamp_idx ON revision(rev_user, rev_timestamp);
CREATE INDEX rev_usertext_timestamp_idx ON revision(rev_user_text, rev_timestamp);

CREATE SEQUENCE text_old_id_val;

CREATE TABLE text (
	old_id		NUMBER(8) NOT NULL,
	old_text	CLOB NOT NULL,
	old_flags	CLOB NOT NULL,
	CONSTRAINT text_pk PRIMARY KEY (old_id)
);

CREATE TABLE archive (
	ar_namespace	NUMBER(5) NOT NULL,
	ar_title	VARCHAR2(255) NOT NULL,
	ar_text		CLOB,
	ar_comment	CLOB,
	ar_user		NUMBER(8),
	ar_user_text	VARCHAR2(255) NOT NULL,
	ar_timestamp	TIMESTAMP NOT NULL,
	ar_minor_edit	NUMBER(1) DEFAULT 0 NOT NULL,
	ar_flags	CLOB,
	ar_rev_id	NUMBER(8),
	ar_text_id	NUMBER(8)
);
CREATE INDEX archive_name_title_timestamp ON archive(ar_namespace,ar_title,ar_timestamp)

CREATE TABLE pagelinks (
	pl_from	NUMBER(8) NOT NULL
				REFERENCES page(page_id)
				ON DELETE CASCADE,
	pl_namespace	NUMBER(4) DEFAULT 0 NOT NULL,
	pl_title	VARCHAR2(255) NOT NULL
);
CREATE UNIQUE INDEX pl_from ON pagelinks(pl_from, pl_namespace, pl_title);
CREATE INDEX pl_namespace ON pagelinks(pl_namespace, pl_title);

CREATE TABLE imagelinks (
	il_from	 NUMBER(8) NOT NULL REFERENCES page(page_id) ON DELETE CASCADE,
	il_to	 VARCHAR2(255) NOT NULL
);
CREATE UNIQUE INDEX il_from ON imagelinks(il_from, il_to);
CREATE INDEX il_to ON imagelinks(il_to);

CREATE TABLE categorylinks (
  cl_from	NUMBER(8) NOT NULL REFERENCES page(page_id) ON DELETE CASCADE,
  cl_to		VARCHAR2(255) NOT NULL,
  cl_sortkey	VARCHAR2(86) default '',
  cl_timestamp	TIMESTAMP NOT NULL
);
CREATE UNIQUE INDEX cl_from ON categorylinks(cl_from, cl_to);
CREATE INDEX cl_sortkey ON categorylinks(cl_to, cl_sortkey);
CREATE INDEX cl_timestamp ON categorylinks(cl_to, cl_timestamp);

--
-- Contains a single row with some aggregate info
-- on the state of the site.
--
CREATE TABLE site_stats (
  ss_row_id		NUMBER(8) NOT NULL,
  ss_total_views	NUMBER(20) default 0,
  ss_total_edits	NUMBER(20) default 0,
  ss_good_articles	NUMBER(20) default 0,
  ss_total_pages	NUMBER(20) default -1,
  ss_users		NUMBER(20) default -1,
  ss_admins		NUMBER(10) default -1
);
CREATE UNIQUE INDEX ss_row_id ON site_stats(ss_row_id);

--
-- Stores an ID for every time any article is visited;
-- depending on $wgHitcounterUpdateFreq, it is
-- periodically cleared and the page_counter column
-- in the page table updated for the all articles
-- that have been visited.)
--
CREATE TABLE /*$wgDBprefix*/hitcounter (
  hc_id INTEGER UNSIGNED NOT NULL
) TYPE=HEAP MAX_ROWS=25000;


--
-- The internet is full of jerks, alas. Sometimes it's handy
-- to block a vandal or troll account.
--
CREATE SEQUENCE ipblocks_ipb_id_val;
CREATE TABLE ipblocks (
	ipb_id		NUMBER(8) NOT NULL,
	ipb_address	VARCHAR2(40),
	ipb_user	NUMBER(8)
				REFERENCES "user" (user_id)
				ON DELETE CASCADE,
	ipb_by		NUMBER(8) NOT NULL
				REFERENCES "user" (user_id)
				ON DELETE CASCADE,
	ipb_reason	CLOB NOT NULL,
	ipb_timestamp	TIMESTAMP NOT NULL,
	ipb_auto	NUMBER(1) DEFAULT 0 NOT NULL,
	ipb_expiry	TIMESTAMP,
	CONSTRAINT ipblocks_pk PRIMARY KEY (ipb_id)
);
CREATE INDEX ipb_address ON ipblocks(ipb_address);
CREATE INDEX ipb_user ON ipblocks(ipb_user);

CREATE TABLE image (
	img_name	VARCHAR2(255) NOT NULL,
	img_size	NUMBER(8) NOT NULL,
	img_width	NUMBER(5) NOT NULL,
	img_height	NUMBER(5) NOT NULL,
	img_metadata	CLOB,
	img_bits	NUMBER(3),
	img_media_type	VARCHAR2(10),
	img_major_mime	VARCHAR2(12) DEFAULT 'unknown',
	img_minor_mime	VARCHAR2(32) DEFAULT 'unknown',
	img_description	CLOB NOT NULL,
	img_user	NUMBER(8) NOT NULL REFERENCES "user"(user_id) ON DELETE CASCADE,
	img_user_text	VARCHAR2(255) NOT NULL,
	img_timestamp	TIMESTAMP,
	CONSTRAINT image_pk PRIMARY KEY (img_name)
);
CREATE INDEX img_size_idx ON image(img_size);
CREATE INDEX img_timestamp_idx ON image(img_timestamp);

CREATE TABLE oldimage (
	oi_name		VARCHAR2(255) NOT NULL,
	oi_archive_name	VARCHAR2(255) NOT NULL,
	oi_size		NUMBER(8) NOT NULL,
	oi_width	NUMBER(5) NOT NULL,
	oi_height	NUMBER(5) NOT NULL,
	oi_bits		NUMBER(3) NOT NULL,
	oi_description	CLOB,
	oi_user		NUMBER(8) NOT NULL REFERENCES "user"(user_id),
	oi_user_text	VARCHAR2(255) NOT NULL,
	oi_timestamp	TIMESTAMP NOT NULL
);
CREATE INDEX oi_name ON oldimage (oi_name);

CREATE SEQUENCE rc_rc_id_seq;
CREATE TABLE recentchanges (
	rc_id 		NUMBER(8) NOT NULL,
	rc_timestamp	TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL,
	rc_cur_time	TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL,
	rc_user		NUMBER(8) DEFAULT 0 NOT NULL,
	rc_user_text	VARCHAR2(255),
	rc_namespace	NUMBER(4) DEFAULT 0 NOT NULL,
	rc_title	VARCHAR2(255) NOT NULL,
	rc_comment	VARCHAR2(255),
	rc_minor	NUMBER(3) DEFAULT 0 NOT NULL,
	rc_bot		NUMBER(3) DEFAULT 0 NOT NULL,
	rc_new 		NUMBER(3) DEFAULT 0 NOT NULL,
	rc_cur_id	NUMBER(8),
	rc_this_oldid	NUMBER(8) NOT NULL,
	rc_last_oldid	NUMBER(8) NOT NULL,
	rc_type		NUMBER(3) DEFAULT 0 NOT NULL,
	rc_moved_to_ns	NUMBER(3),
	rc_moved_to_title	VARCHAR2(255),
	rc_patrolled	NUMBER(3) DEFAULT 0 NOT NULL,
	rc_ip		VARCHAR2(40),
	CONSTRAINT rc_pk PRIMARY KEY (rc_id)
);
CREATE INDEX rc_timestamp ON recentchanges (rc_timestamp);
CREATE INDEX rc_namespace_title ON recentchanges(rc_namespace, rc_title);
CREATE INDEX rc_cur_id ON recentchanges(rc_cur_id);
CREATE INDEX new_name_timestamp ON recentchanges(rc_new, rc_namespace, rc_timestamp);
CREATE INDEX rc_ip ON recentchanges(rc_ip);

CREATE TABLE watchlist (
	wl_user				NUMBER(8) NOT NULL
						REFERENCES "user"(user_id)
						ON DELETE CASCADE,
	wl_namespace			NUMBER(8) DEFAULT 0 NOT NULL,
	wl_title			VARCHAR2(255) NOT NULL,
	wl_notificationtimestamp	TIMESTAMP DEFAULT NULL
);
CREATE UNIQUE INDEX wl_user_namespace_title ON watchlist
	(wl_user, wl_namespace, wl_title);
CREATE INDEX wl_namespace_title ON watchlist(wl_namespace, wl_title);

--
-- Used by texvc math-rendering extension to keep track
-- of previously-rendered items.
--
CREATE TABLE /*$wgDBprefix*/math (
  -- Binary MD5 hash of the latex fragment, used as an identifier key.
  math_inputhash varchar(16) NOT NULL,

  -- Not sure what this is, exactly...
  math_outputhash varchar(16) NOT NULL,

  -- texvc reports how well it thinks the HTML conversion worked;
  -- if it's a low level the PNG rendering may be preferred.
  math_html_conservativeness tinyint(1) NOT NULL,

  -- HTML output from texvc, if any
  math_html text,

  -- MathML output from texvc, if any
  math_mathml text,

  UNIQUE KEY math_inputhash (math_inputhash)

) TYPE=InnoDB;

--
-- When using the default MySQL search backend, page titles
-- and text are munged to strip markup, do Unicode case folding,
-- and prepare the result for MySQL's fulltext index.
--
-- This table must be MyISAM; InnoDB does not support the needed
-- fulltext index.
--
CREATE TABLE /*$wgDBprefix*/searchindex (
  -- Key to page_id
  si_page int(8) unsigned NOT NULL,

  -- Munged version of title
  si_title varchar(255) NOT NULL default '',

  -- Munged version of body text
  si_text mediumtext NOT NULL default '',

  UNIQUE KEY (si_page),
  FULLTEXT si_title (si_title),
  FULLTEXT si_text (si_text)

) TYPE=MyISAM;

--
-- Recognized interwiki link prefixes
--
CREATE TABLE /*$wgDBprefix*/interwiki (
  -- The interwiki prefix, (e.g. "Meatball", or the language prefix "de")
  iw_prefix char(32) NOT NULL,

  -- The URL of the wiki, with "$1" as a placeholder for an article name.
  -- Any spaces in the name will be transformed to underscores before
  -- insertion.
  iw_url char(127) NOT NULL,

  -- A boolean value indicating whether the wiki is in this project
  -- (used, for example, to detect redirect loops)
  iw_local BOOL NOT NULL,

  -- Boolean value indicating whether interwiki transclusions are allowed.
  iw_trans TINYINT(1) NOT NULL DEFAULT 0,

  UNIQUE KEY iw_prefix (iw_prefix)

) TYPE=InnoDB;

--
-- Used for caching expensive grouped queries
--
CREATE TABLE /*$wgDBprefix*/querycache (
  -- A key name, generally the base name of of the special page.
  qc_type char(32) NOT NULL,

  -- Some sort of stored value. Sizes, counts...
  qc_value int(5) unsigned NOT NULL default '0',

  -- Target namespace+title
  qc_namespace int NOT NULL default '0',
  qc_title char(255) binary NOT NULL default '',

  KEY (qc_type,qc_value)

) TYPE=InnoDB;

--
-- For a few generic cache operations if not using Memcached
--
CREATE TABLE objectcache (
	keyname		CHAR(255) DEFAULT '',
	value		CLOB,
	exptime		TIMESTAMP
);
CREATE UNIQUE INDEX oc_keyname_idx ON objectcache(keyname);
CREATE INDEX oc_exptime_idx ON objectcache(exptime);

-- For article validation
CREATE TABLE /*$wgDBprefix*/validate (
  `val_user` int(11) NOT NULL default '0',
  `val_page` int(11) unsigned NOT NULL default '0',
  `val_revision` int(11) unsigned NOT NULL default '0',
  `val_type` int(11) unsigned NOT NULL default '0',
  `val_value` int(11) default '0',
  `val_comment` varchar(255) NOT NULL default '',
  `val_ip` varchar(20) NOT NULL default '',
  KEY `val_user` (`val_user`,`val_revision`)
) TYPE=InnoDB;


CREATE TABLE logging (
  log_type		VARCHAR2(10) NOT NULL,
  log_action		VARCHAR2(10) NOT NULL,
  log_timestamp		TIMESTAMP NOT NULL,
  log_user		NUMBER(8) REFERENCES "user"(user_id),
  log_namespace		NUMBER(4),
  log_title		VARCHAR2(255) NOT NULL,
  log_comment		VARCHAR2(255),
  log_params		CLOB
);
CREATE INDEX logging_type_name ON logging(log_type, log_timestamp);
CREATE INDEX logging_user_time ON logging(log_user, log_timestamp);
CREATE INDEX logging_page_time ON logging(log_namespace, log_title, log_timestamp);

-- Hold group name and description
--CREATE TABLE /*$wgDBprefix*/groups (
--  gr_id int(5) unsigned NOT NULL auto_increment,
--  gr_name varchar(50) NOT NULL default '',
--  gr_description varchar(255) NOT NULL default '',
--  gr_rights tinyblob,
--  PRIMARY KEY  (gr_id)
--
--) TYPE=InnoDB;
