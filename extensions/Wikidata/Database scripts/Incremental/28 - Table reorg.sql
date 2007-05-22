-- These tables can exist within each dataset.
RENAME TABLE translated_content to uw_translated_content;
RENAME TABLE transactions to uw_transactions;

-- We used to share this with MediaWiki, but it makes more sense to
-- have our own within each data set.
CREATE TABLE `uw_text` (
  `text_id` int(8) unsigned NOT NULL auto_increment,
  `text_text` mediumblob NOT NULL,
  `text_flags` tinyblob NOT NULL,
  PRIMARY KEY  (`text_id`)
) DEFAULT CHARSET=utf8;
INSERT INTO `script_log` (`time`, `script_name`, `comment`) VALUES (NOW(), '28 - Table reorg.sql', 'reorganize tables for authoritative view');