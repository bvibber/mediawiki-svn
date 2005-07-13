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
	ug_group	CHAR(16) DEFAULT '' NOT NULL,
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

--
-- Holding area for deleted articles, which may be viewed
-- or restored by admins through the Special:Undelete interface.
-- The fields generally correspond to the page, revision, and text
-- fields, with several caveats.
--
CREATE TABLE /*$wgDBprefix*/archive (
  ar_namespace int NOT NULL default '0',
  ar_title varchar(255) binary NOT NULL default '',

  -- Newly deleted pages will not store text in this table,
  -- but will reference the separately existing text rows.
  -- This field is retained for backwards compatibility,
  -- so old archived pages will remain accessible after
  -- upgrading from 1.4 to 1.5.
  -- Text may be gzipped or otherwise funky.
  ar_text mediumblob NOT NULL default '',

  -- Basic revision stuff...
  ar_comment tinyblob NOT NULL default '',
  ar_user int(5) unsigned NOT NULL default '0',
  ar_user_text varchar(255) binary NOT NULL,
  ar_timestamp char(14) binary NOT NULL default '',
  ar_minor_edit tinyint(1) NOT NULL default '0',

  -- See ar_text note.
  ar_flags tinyblob NOT NULL default '',

  -- When revisions are deleted, their unique rev_id is stored
  -- here so it can be retained after undeletion. This is necessary
  -- to retain permalinks to given revisions after accidental delete
  -- cycles or messy operations like history merges.
  --
  -- Old entries from 1.4 will be NULL here, and a new rev_id will
  -- be created on undeletion for those revisions.
  ar_rev_id int(8) unsigned,

  -- For newly deleted revisions, this is the text.old_id key to the
  -- actual stored text. To avoid breaking the block-compression scheme
  -- and otherwise making storage changes harder, the actual text is
  -- *not* deleted from the text table, merely hidden by removal of the
  -- page and revision entries.
  --
  -- Old entries deleted under 1.2-1.4 will have NULL here, and their
  -- ar_text and ar_flags fields will be used to create a new text
  -- row upon undeletion.
  ar_text_id int(8) unsigned,

  KEY name_title_timestamp (ar_namespace,ar_title,ar_timestamp)

) TYPE=InnoDB;


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

--
-- Uploaded images and other files.
--
CREATE TABLE /*$wgDBprefix*/image (
  -- Filename.
  -- This is also the title of the associated description page,
  -- which will be in namespace 6 (NS_IMAGE).
  img_name varchar(255) binary NOT NULL default '',

  -- File size in bytes.
  img_size int(8) unsigned NOT NULL default '0',

  -- For images, size in pixels.
  img_width int(5)  NOT NULL default '0',
  img_height int(5)  NOT NULL default '0',

  -- Extracted EXIF metadata stored as a serialized PHP array.
  img_metadata mediumblob NOT NULL,

  -- For images, bits per pixel if known.
  img_bits int(3)  NOT NULL default '0',

  -- Media type as defined by the MEDIATYPE_xxx constants
  img_media_type ENUM("UNKNOWN", "BITMAP", "DRAWING", "AUDIO", "VIDEO", "MULTIMEDIA", "OFFICE", "TEXT", "EXECUTABLE", "ARCHIVE") default NULL,

  -- major part of a MIME media type as defined by IANA
  -- see http://www.iana.org/assignments/media-types/
  img_major_mime ENUM("unknown", "application", "audio", "image", "text", "video", "message", "model", "multipart") NOT NULL default "unknown",

  -- minor part of a MIME media type as defined by IANA
  -- the minor parts are not required to adher to any standard
  -- but should be consistent throughout the database
  -- see http://www.iana.org/assignments/media-types/
  img_minor_mime varchar(32) NOT NULL default "unknown",

  -- Description field as entered by the uploader.
  -- This is displayed in image upload history and logs.
  img_description tinyblob NOT NULL default '',

  -- user_id and user_name of uploader.
  img_user int(5) unsigned NOT NULL default '0',
  img_user_text varchar(255) binary NOT NULL default '',

  -- Time of the upload.
  img_timestamp char(14) binary NOT NULL default '',

  PRIMARY KEY img_name (img_name),

  -- Used by Special:Imagelist for sort-by-size
  INDEX img_size (img_size),

  -- Used by Special:Newimages and Special:Imagelist
  INDEX img_timestamp (img_timestamp)

) TYPE=InnoDB;

--
-- Previous revisions of uploaded files.
-- Awkwardly, image rows have to be moved into
-- this table at re-upload time.
--
CREATE TABLE /*$wgDBprefix*/oldimage (
  -- Base filename: key to image.img_name
  oi_name varchar(255) binary NOT NULL default '',

  -- Filename of the archived file.
  -- This is generally a timestamp and '!' prepended to the base name.
  oi_archive_name varchar(255) binary NOT NULL default '',

  -- Other fields as in image...
  oi_size int(8) unsigned NOT NULL default 0,
  oi_width int(5) NOT NULL default 0,
  oi_height int(5) NOT NULL default 0,
  oi_bits int(3) NOT NULL default 0,
  oi_description tinyblob NOT NULL default '',
  oi_user int(5) unsigned NOT NULL default '0',
  oi_user_text varchar(255) binary NOT NULL default '',
  oi_timestamp char(14) binary NOT NULL default '',

  INDEX oi_name (oi_name(10))

) TYPE=InnoDB;

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
	rc_cur_id	NUMBER(8) NOT NULL
				REFERENCES page(page_id)
				ON DELETE CASCADE,
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


CREATE TABLE /*$wgDBprefix*/logging (
  -- Symbolic keys for the general log type and the action type
  -- within the log. The output format will be controlled by the
  -- action field, but only the type controls categorization.
  log_type char(10) NOT NULL default '',
  log_action char(10) NOT NULL default '',

  -- Timestamp. Duh.
  log_timestamp char(14) NOT NULL default '19700101000000',

  -- The user who performed this action; key to user_id
  log_user int unsigned NOT NULL default 0,

  -- Key to the page affected. Where a user is the target,
  -- this will point to the user page.
  log_namespace int NOT NULL default 0,
  log_title varchar(255) binary NOT NULL default '',

  -- Freeform text. Interpreted as edit history comments.
  log_comment varchar(255) NOT NULL default '',

  -- LF separated list of miscellaneous parameters
  log_params blob NOT NULL default '',

  KEY type_time (log_type, log_timestamp),
  KEY user_time (log_user, log_timestamp),
  KEY page_time (log_namespace, log_title, log_timestamp)

) TYPE=InnoDB;





-- Hold group name and description
--CREATE TABLE /*$wgDBprefix*/groups (
--  gr_id int(5) unsigned NOT NULL auto_increment,
--  gr_name varchar(50) NOT NULL default '',
--  gr_description varchar(255) NOT NULL default '',
--  gr_rights tinyblob,
--  PRIMARY KEY  (gr_id)
--
--) TYPE=InnoDB;
