-- Add page_restrictions.pr_timestamp column
-- (Rob Church, June 2007)
ALTER TABLE /*wgDBprefix*/page_restrictions
ADD `pr_timestamp` VARCHAR(14) BINARY NULL AFTER `pr_user`;