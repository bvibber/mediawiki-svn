DROP TABLE IF EXISTS /*$wgWDprefix*/alt_meaningtexts;

CREATE TABLE /*$wgWDprefix*/alt_meaningtexts (
  `meaning_mid` int(10) default NULL,
  `meaning_text_tcid` int(10) default NULL,
  `source_id` int(11) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

DROP TABLE IF EXISTS /*$wgWDprefix*/bootstrapped_defined_meanings;

CREATE TABLE /*$wgWDprefix*/bootstrapped_defined_meanings (
  `name` varchar(255) NOT NULL,
  `defined_meaning_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO /*$wgWDprefix*/bootstrapped_defined_meanings (name,defined_meaning_id) VALUES 
 ('DefinedMeaning',49),
 ('Definition',52),
 ('SynTrans',55),
 ('Relation',58),
 ('Annotation',61);

DROP TABLE IF EXISTS /*$wgWDprefix*/class_attributes;

CREATE TABLE /*$wgWDprefix*/class_attributes (
  `object_id` int(11) NOT NULL,
  `class_mid` int(11) NOT NULL default '0',
  `level_mid` int(11) NOT NULL,
  `attribute_mid` int(11) NOT NULL default '0',
  `attribute_type` char(4) collate latin1_general_ci NOT NULL default 'TEXT',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

DROP TABLE IF EXISTS /*$wgWDprefix*/class_membership;

CREATE TABLE /*$wgWDprefix*/class_membership (
  `class_membership_id` int(11) NOT NULL,
  `class_mid` int(11) NOT NULL default '0',
  `class_member_mid` int(11) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

DROP TABLE IF EXISTS /*$wgWDprefix*/collection_contents;

CREATE TABLE /*$wgWDprefix*/collection_contents (
  `collection_id` int(10) NOT NULL default '0',
  `member_mid` int(10) NOT NULL default '0',
  `internal_member_id` varchar(255) default NULL,
  `applicable_language_id` int(10) default NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS /*$wgWDprefix*/collection_language;

CREATE TABLE /*$wgWDprefix*/collection_language (
  `collection_id` int(10) NOT NULL default '0',
  `language_id` int(10) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS /*$wgWDprefix*/collection_ns;

CREATE TABLE /*$wgWDprefix*/collection_ns (
  `collection_id` int(10) unsigned NOT NULL,
  `collection_mid` int(10) NOT NULL default '0',
  `collection_type` char(4) default NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS /*$wgWDprefix*/defined_meaning;

CREATE TABLE /*$wgWDprefix*/defined_meaning (
  `defined_meaning_id` int(8) unsigned NOT NULL,
  `expression_id` int(10) NOT NULL,
  `meaning_text_tcid` int(10) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS /*$wgWDprefix*/expression_ns;

CREATE TABLE /*$wgWDprefix*/expression_ns (
  `expression_id` int(10) unsigned NOT NULL,
  `spelling` varchar(255) NOT NULL default '',
  `hyphenation` varchar(255) NOT NULL default '',
  `language_id` int(10) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS /*$wgWDprefix*/meaning_relations;

CREATE TABLE /*$wgWDprefix*/meaning_relations (
  `relation_id` int(11) NOT NULL,
  `meaning1_mid` int(10) NOT NULL default '0',
  `meaning2_mid` int(10) NOT NULL default '0',
  `relationtype_mid` int(10) default NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS /*$wgWDprefix*/objects;

CREATE TABLE /*$wgWDprefix*/objects (
  `object_id` int(11) NOT NULL auto_increment,
  `table` varchar(100) collate latin1_general_ci NOT NULL,
  `original_id` int(11) default NULL,
  `UUID` varchar(36) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`object_id`),
  KEY `table` (`table`),
  KEY `original_id` (`original_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

DROP TABLE IF EXISTS /*$wgWDprefix*/option_attribute_options;

CREATE TABLE /*$wgWDprefix*/option_attribute_options (
  `option_id` int(11) NOT NULL default '0',
  `attribute_id` int(11) NOT NULL default '0',
  `option_mid` int(11) NOT NULL default '0',
  `language_id` int(11) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL default '0',
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS /*$wgWDprefix*/option_attribute_values;

CREATE TABLE /*$wgWDprefix*/option_attribute_values (
  `value_id` int(11) NOT NULL default '0',
  `object_id` int(11) NOT NULL default '0',
  `option_id` int(11) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL default '0',
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS /*$wgWDprefix*/script_log;

CREATE TABLE /*$wgWDprefix*/script_log (
  `script_id` int(11) NOT NULL default '0',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `script_name` varchar(128) character set latin1 collate latin1_general_ci NOT NULL default '',
  `comment` varchar(128) character set latin1 collate latin1_general_ci NOT NULL default ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS /*$wgWDprefix*/syntrans;

CREATE TABLE /*$wgWDprefix*/syntrans (
  `syntrans_sid` int(10) NOT NULL default '0',
  `defined_meaning_id` int(10) NOT NULL default '0',
  `expression_id` int(10) NOT NULL,
  `firstuse` char(14) NOT NULL default '',
  `identical_meaning` tinyint(1) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS /*$wgWDprefix*/syntrans_relations;

CREATE TABLE /*$wgWDprefix*/syntrans_relations (
  `syntrans1_id` int(10) NOT NULL,
  `syntrans2_id` int(10) NOT NULL,
  `relationtype_mid` int(10) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS /*$wgWDprefix*/text;

CREATE TABLE /*$wgWDprefix*/text (
  `text_id` int(8) unsigned NOT NULL auto_increment,
  `text_text` mediumblob NOT NULL,
  `text_flags` tinyblob NOT NULL,
  PRIMARY KEY  (`text_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS /*$wgWDprefix*/text_attribute_values;

CREATE TABLE /*$wgWDprefix*/text_attribute_values (
  `value_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `attribute_mid` int(11) NOT NULL,
  `text` varchar(255) collate latin1_general_ci NOT NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

DROP TABLE IF EXISTS /*$wgWDprefix*/transactions;

CREATE TABLE /*$wgWDprefix*/transactions (
  `transaction_id` int(11) NOT NULL auto_increment,
  `user_id` int(5) NOT NULL,
  `user_ip` varchar(15) collate latin1_general_ci NOT NULL,
  `timestamp` varchar(14) collate latin1_general_ci NOT NULL,
  `comment` tinyblob NOT NULL,
  PRIMARY KEY  (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

DROP TABLE IF EXISTS /*$wgWDprefix*/translated_content;

CREATE TABLE /*$wgWDprefix*/translated_content (
  `translated_content_id` int(11) NOT NULL default '0',
  `language_id` int(10) NOT NULL default '0',
  `shorttext_id` int(10) NOT NULL default '0',
  `text_id` int(10) NOT NULL default '0',
  `original_language_id` int(10) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS /*$wgWDprefix*/translated_content_attribute_values;

CREATE TABLE /*$wgWDprefix*/translated_content_attribute_values (
  `value_id` int(11) NOT NULL default '0',
  `object_id` int(11) NOT NULL,
  `attribute_mid` int(11) NOT NULL,
  `value_tcid` int(11) NOT NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

DROP TABLE IF EXISTS /*$wgWDprefix*/url_attribute_values;

CREATE TABLE /*$wgWDprefix*/url_attribute_values (
  `value_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `attribute_mid` int(11) NOT NULL,
  `url` varchar(255) collate latin1_general_ci NOT NULL,
  `label` varchar(255) collate latin1_general_ci NOT NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
