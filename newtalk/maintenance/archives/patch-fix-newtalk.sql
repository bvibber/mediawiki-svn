-- Alters columns in `user_newtalk` to be nullable and
-- drops existing indices in favour of a single UNIQUE
-- index
ALTER TABLE /*$wgDBprefix*/user_newtalk DROP INDEX `user_id`,
DROP INDEX `user_ip`, CHANGE `user_id` `user_id` INT(5) NULL,
CHANGE `user_ip` `user_ip` VARCHAR(40) NULL,
ADD UNIQUE( `user_id`, `user_ip` );