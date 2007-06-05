-- Add page_restrictions.pr_column
-- (Rob Church, June 2007)
ALTER TABLE /*wgDBprefix*/page_restrictions
ADD `pr_timestamp` CHAR(14) BINARY NULL AFTER `pr_user`;