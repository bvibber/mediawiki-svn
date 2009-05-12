-- (c) Aaron Schulz, 2007-2009, GPL
-- Table structure for table `Flagged Revisions`
-- Replace /*$wgDBprefix*/ with the proper prefix
-- Replace /*$wgDBTableOptions*/ with the correct options

CREATE TABLE /*$wgDBprefix*/poll (
`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`question` VARCHAR( 255 ) NOT NULL ,
`alternative_1` VARCHAR( 255 ) NOT NULL ,
`alternative_2` VARCHAR( 255 ) NOT NULL ,
`alternative_3` VARCHAR( 255 ) NOT NULL ,
`alternative_4` VARCHAR( 255 ) NOT NULL ,
`alternative_5` VARCHAR( 255 ) NOT NULL ,
`alternative_6` VARCHAR( 255 ) NOT NULL
) /*$wgDBTableOptions*/;