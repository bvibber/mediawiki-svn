-- Page title ui.
-- Storing both a page_title and page_title_ui now allows us to make
-- characters like _ and titles with extra padding or different
-- starting letter case, valid for creation and moved they will
-- also keep those extra characters instead of being normalized.
--
-- Daniel Friesen (Dantman), March 2008

ALTER TABLE /*$wgDBprefix*/page
	ADD COLUMN page_title_ui varchar(255) binary NOT NULL AFTER page_title;
