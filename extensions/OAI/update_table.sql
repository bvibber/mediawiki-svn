--
-- Summary table of last-edit timestamp of every page in the wiki.
-- This includes entries for pages which are deleted, so they can
-- be cleanly kept track of.
--
CREATE TABLE /*$wgDBprefix*/updates (
  up_page int(10) unsigned NOT NULL,
  up_action enum('modify','create','delete') NOT NULL default 'modify',
  up_timestamp char(14) NOT NULL default '',
  
  -- Exactly one entry per page
  PRIMARY KEY up_page(up_page),
  
  -- We routinely pull things based on timestamp.
  KEY up_timestamp(up_timestamp)
);

--
-- Initialize the table from the current state.
-- This will not list any _prior_ deletions, unfortunately.
-- New deletions can be kept track of as the happen through
-- updates from the extension hooks.
--
INSERT INTO /*$wgDBprefix*/updates (up_page, up_action, up_timestamp)
SELECT cur_id, IF(cur_is_new, 'create', 'modify'), cur_timestamp
FROM /*$wgDBprefix*/cur;

