--
-- patch-pagesets.sql
--
-- Support new Set: namespace
-- Intended for user with the langtags table
--

CREATE TABLE /*$wgDBprefix*/pagesets (
  set_id integer NOT NULL,
  page_id integer NOT NULL
);
CREATE UNIQUE INDEX set_id
  ON /*$wgDBprefix*/pagesets (set_id,page_id);
CREATE INDEX page_id
  ON /*$wgDBprefix*/pagesets (page_id);
