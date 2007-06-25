-- Updates the page.page_len index so it includes
-- all pertinent columns, speeding up the short pages
-- report execution/generation time
ALTER TABLE /*$wgDBprefix*/page DROP INDEX `page_len`,
ADD INDEX `page_len` ( `page_len` , `page_id` , `page_namespace` , `page_title` , `page_is_redirect` )