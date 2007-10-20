--
-- patch-langtags.sql
-- Language tag support
-- 2007-06-02
--

CREATE TABLE /*$wgDBprefix*/langtags (
    language_id integer unsigned,
    prefix_id integer unsigned,
    preferred_id integer unsigned,

    tag_name varchar(255),
    display_name varchar(255),

    native_name varchar(255),
    english_name varchar(255),

    rfc4646 varchar(42),
    rfc4646_suppress varchar(4),
    rfc4646_added date,
    rfc4646_deprecated date,

    iso639 char(3),
    iso639_3 char(3),
    iso639_3_revision date,

    wikimedia_key varchar(15),

    is_rtl smallint,
    is_collection smallint,
    is_enabled smallint,
    is_private smallint,
    is_searchable smallint,
    tag_touched char(14) binary default ''
) /*$wgDBTableOptions*/;

CREATE UNIQUE INDEX langtags_tag_enabled
 ON /*$wgDBprefix*/langtags (is_enabled,tag_name);
CREATE UNIQUE INDEX langtags_tag_name
 ON /*$wgDBprefix*/langtags (tag_name);
CREATE INDEX langtags_iso639_key
 ON /*$wgDBprefix*/langtags (iso639);
CREATE INDEX langtags_rfc4646_key
 ON /*$wgDBprefix*/langtags (rfc4646);

CREATE TABLE /*$wgDBprefix*/langsets (
    language_id integer unsigned,
    group_name varchar(255)
) /*$wgDBTableOptions*/;
CREATE UNIQUE INDEX language_id
  ON /*$wgDBprefix*/langsets (language_id,group_name);
CREATE INDEX group_name 
  ON /*$wgDBprefix*/langsets (group_name);

ALTER TABLE /*$wgDBprefix*/archive
 ADD COLUMN /*$wgDBprefix*/ar_language integer;
DROP INDEX name_title_timestamp
  ON /*$wgDBprefix*/archive;
CREATE INDEX name_title_timestamp
  ON /*$wgDBprefix*/archive (ar_language, ar_namespace, ar_title, ar_timestamp)

ALTER TABLE /*$wgDBprefix*/page
  ADD COLUMN /*$wgDBprefix*/page_language integer unsigned;
DROP INDEX name_title
  ON /*$wgDBprefix*/page;
CREATE UNIQUE INDEX name_title
  ON /*$wgDBprefix*/page (page_language, page_namespace, page_title);

ALTER TABLE /*$wgDBprefix*/pagelinks
  ADD COLUMN pl_language integer unsigned;
DROP INDEX pl_from
  ON /*$wgDBprefix*/pagelinks;
DROP INDEX pl_namespace
  ON /*$wgDBprefix*/pagelinks;
CREATE UNIQUE INDEX pl_from
  ON /*$wgDBprefix*/pagelinks (pl_from, pl_namespace, pl_language, pl_title);
CREATE INDEX pl_namespace
  ON /*$wgDBprefix*/pagelinks (pl_namespace, pl_language, pl_title, pl_from);

ALTER TABLE /*$wgDBprefix*/recentchanges
  ADD COLUMN rc_language integer unsigned;
DROP INDEX rc_namespace_title
  ON /*$wgDBprefix*/recentchanges;
CREATE INDEX rc_namespace_title
  ON /*$wgDBprefix*/recentchanges (rc_namespace, rc_language, rc_title);

ALTER TABLE /*$wgDBprefix*/watchlist
  ADD COLUMN wl_language integer unsigned;
DROP INDEX wl_user
  ON /*$wgDBprefix*/watchlist;
DROP INDEX namespace_title
  ON /*$wgDBprefix*/watchlist;
CREATE UNIQUE INDEX wl_user
  ON /*$wgDBprefix*/watchlist (wl_user, wl_namespace, wl_language, wl_title); 
CREATE INDEX namespace_title
  ON /*$wgDBprefix*/watchlist (wl_namespace, wl_language, wl_title);

ALTER TABLE /*$wgDBprefix*/categorylinks
  ADD COLUMN cl_language integer unsigned;
DROP INDEX cl_from
  ON /*$wgDBprefix*/categorylinks;
DROP INDEX cl_sortkey 
  ON /*$wgDBprefix*/categorylinks;
DROP INDEX cl_timestamp 
  ON /*$wgDBprefix*/categorylinks;
CREATE UNIQUE INDEX cl_from
  ON /*$wgDBprefix*/categorylinks (cl_from, cl_language, cl_to);
CREATE INDEX cl_sortkey
  ON /*$wgDBprefix*/categorylinks (cl_language, cl_to, cl_sortkey);
CREATE INDEX cl_timestamp
  ON /*$wgDBprefix*/categorylinks (cl_language, cl_to, cl_timestamp);

ALTER TABLE /*$wgDBprefix*/templatelinks
  ADD COLUMN tl_language integer unsigned;
DROP INDEX tl_from
  ON /*$wgDBprefix*/templatelinks;
DROP INDEX tl_namespace
  ON /*$wgDBprefix*/templatelinks;
CREATE UNIQUE INDEX tl_from
  ON /*$wgDBprefix*/templatelinks (tl_from, tl_namespace, tl_language, tl_title);
CREATE INDEX tl_from
  ON /*$wgDBprefix*/templatelinks (tl_namespace, tl_language, tl_title, tl_from);

ALTER TABLE /*$wgDBprefix*/querycache
  ADD COLUMN qc_language integer unsigned;
