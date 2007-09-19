CREATE TABLE /*$wgWDprefix*/alt_meaningtexts (
  `meaning_mid` int(10) default NULL,
  `meaning_text_tcid` int(10) default NULL,
  `source_id` int(11) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_meaning` (`remove_transaction_id`,`meaning_mid`,`meaning_text_tcid`,`source_id`),
  KEY `versioned_end_text` (`remove_transaction_id`,`meaning_text_tcid`,`meaning_mid`,`source_id`),
  KEY `versioned_end_source` (`remove_transaction_id`,`source_id`,`meaning_mid`,`meaning_text_tcid`),
  KEY `versioned_start_meaning` (`add_transaction_id`,`meaning_mid`,`meaning_text_tcid`,`source_id`),
  KEY `versioned_start_text` (`add_transaction_id`,`meaning_text_tcid`,`meaning_mid`,`source_id`),
  KEY `versioned_start_source` (`add_transaction_id`,`source_id`,`meaning_mid`,`meaning_text_tcid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE /*$wgWDprefix*/alt_meaningtexts 
	ADD INDEX /*$wgWDprefix*/versioned_end_meaning (`remove_transaction_id`, `meaning_mid`, `meaning_text_tcid`, `source_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_text (`remove_transaction_id`, `meaning_text_tcid`, `meaning_mid`, `source_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_source (`remove_transaction_id`, `source_id`, `meaning_mid`, `meaning_text_tcid`),
	ADD INDEX /*$wgWDprefix*/versioned_start_meaning (`add_transaction_id`, `meaning_mid`, `meaning_text_tcid`, `source_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_text (`add_transaction_id`, `meaning_text_tcid`, `meaning_mid`, `source_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_source (`add_transaction_id`, `source_id`, `meaning_mid`, `meaning_text_tcid`);

CREATE TABLE /*$wgWDprefix*/bootstrapped_defined_meanings (
  `name` varchar(255) NOT NULL,
  `defined_meaning_id` int(11) NOT NULL,
  KEY `unversioned_meaning` (`defined_meaning_id`),
  KEY `unversioned_name` (`name`,`defined_meaning_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE /*$wgWDprefix*/bootstrapped_defined_meanings 
	ADD INDEX /*$wgWDprefix*/unversioned_meaning (`defined_meaning_id`),
	ADD INDEX /*$wgWDprefix*/unversioned_name (`name` (255), `defined_meaning_id`);



-- object_id - key for the attribute, used elsewhere as a foreign key
-- class_mid - which class (identified by DMID) has this attribute?
-- level_mid - on which level can we annotate: Annotation, DefinedMeaning, Definition, Relation, SynTrans; these are also cached in *_bootstrapped_defined_meanings
-- attribute_mid - which attribute are we describing?
-- attribute_type - what kind of information are we talking about? can be 'DM', 'TRNS' (translatable text), 'TEXT', 'URL', 'OPTN' (multiple DMs to choose from)a
attribute_id - refers to the object_id from xx_class_attributes
CREATE TABLE /*$wgWDprefix*/class_attributes (
  `object_id` int(11) NOT NULL,
  `class_mid` int(11) NOT NULL default '0',
  `level_mid` int(11) NOT NULL,
  `attribute_mid` int(11) NOT NULL default '0',
  `attribute_type` char(4) collate utf8_bin NOT NULL default 'TEXT',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_class` (`remove_transaction_id`,`class_mid`,`attribute_mid`,`object_id`),
  KEY `versioned_end_attribute` (`remove_transaction_id`,`attribute_mid`,`class_mid`,`object_id`),
  KEY `versioned_end_object` (`remove_transaction_id`,`object_id`),
  KEY `versioned_start_class` (`add_transaction_id`,`class_mid`,`attribute_mid`,`object_id`),
  KEY `versioned_start_attribute` (`add_transaction_id`,`attribute_mid`,`class_mid`,`object_id`),
  KEY `versioned_start_object` (`add_transaction_id`,`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE /*$wgWDprefix*/class_attributes 
	ADD INDEX /*$wgWDprefix*/versioned_end_class (`remove_transaction_id`, `class_mid`, `attribute_mid`, `object_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_attribute (`remove_transaction_id`, `attribute_mid`, `class_mid`, `object_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_object (`remove_transaction_id`, `object_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_class (`add_transaction_id`, `class_mid`, `attribute_mid`, `object_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_attribute (`add_transaction_id`, `attribute_mid`, `class_mid`, `object_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_object (`add_transaction_id`, `object_id`);

CREATE TABLE /*$wgWDprefix*/class_membership (
  `class_membership_id` int(11) NOT NULL,
  `class_mid` int(11) NOT NULL default '0',
  `class_member_mid` int(11) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_class` (`remove_transaction_id`,`class_mid`,`class_member_mid`),
  KEY `versioned_end_class_member` (`remove_transaction_id`,`class_member_mid`,`class_mid`),
  KEY `versioned_end_class_membership` (`remove_transaction_id`,`class_membership_id`),
  KEY `versioned_start_class` (`add_transaction_id`,`class_mid`,`class_member_mid`),
  KEY `versioned_start_class_member` (`add_transaction_id`,`class_member_mid`,`class_mid`),
  KEY `versioned_start_class_membership` (`add_transaction_id`,`class_membership_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE /*$wgWDprefix*/class_membership 
	ADD INDEX /*$wgWDprefix*/versioned_end_class (`remove_transaction_id`, `class_mid`, `class_member_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_end_class_member (`remove_transaction_id`, `class_member_mid`, `class_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_end_class_membership (`remove_transaction_id`, `class_membership_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_class (`add_transaction_id`, `class_mid`, `class_member_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_start_class_member (`add_transaction_id`, `class_member_mid`, `class_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_start_class_membership (`add_transaction_id`, `class_membership_id`);

CREATE TABLE /*$wgWDprefix*/collection_contents (
  `collection_id` int(10) NOT NULL default '0',
  `member_mid` int(10) NOT NULL default '0',
  `internal_member_id` varchar(255) default NULL,
  `applicable_language_id` int(10) default NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_collection` (`remove_transaction_id`,`collection_id`,`member_mid`),
  KEY `versioned_end_collection_member` (`remove_transaction_id`,`member_mid`,`collection_id`),
  KEY `versioned_end_internal_id` (`remove_transaction_id`,`internal_member_id`,`collection_id`,`member_mid`),
  KEY `versioned_start_collection` (`add_transaction_id`,`collection_id`,`member_mid`),
  KEY `versioned_start_collection_member` (`add_transaction_id`,`member_mid`,`collection_id`),
  KEY `versioned_start_internal_id` (`add_transaction_id`,`internal_member_id`,`collection_id`,`member_mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE /*$wgWDprefix*/collection_contents 
	ADD INDEX /*$wgWDprefix*/versioned_end_collection (`remove_transaction_id`, `collection_id`, `member_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_end_collection_member (`remove_transaction_id`, `member_mid`, `collection_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_internal_id (`remove_transaction_id`, `internal_member_id` (255), `collection_id`, `member_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_start_collection (`add_transaction_id`, `collection_id`, `member_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_start_collection_member (`add_transaction_id`, `member_mid`, `collection_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_internal_id (`add_transaction_id`, `internal_member_id` (255), `collection_id`, `member_mid`),	
	ADD INDEX /*$wgWDprefix*/collection_id_idx (`collection_id`),
	ADD INDEX /*$wgWDprefix*/member_mid_idx (`member_mid`);

CREATE TABLE /*$wgWDprefix*/collection_language (
  `collection_id` int(10) NOT NULL default '0',
  `language_id` int(10) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE /*$wgWDprefix*/collection (
  `collection_id` int(10) unsigned NOT NULL,
  `collection_mid` int(10) NOT NULL default '0',
  `collection_type` char(4) default NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_collection` (`remove_transaction_id`,`collection_id`,`collection_mid`),
  KEY `versioned_end_collection_meaning` (`remove_transaction_id`,`collection_mid`,`collection_id`),
  KEY `versioned_end_collection_type` (`remove_transaction_id`,`collection_type`,`collection_id`,`collection_mid`),
  KEY `versioned_start_collection` (`add_transaction_id`,`collection_id`,`collection_mid`),
  KEY `versioned_start_collection_meaning` (`add_transaction_id`,`collection_mid`,`collection_id`),
  KEY `versioned_start_collection_type` (`add_transaction_id`,`collection_type`,`collection_id`,`collection_mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE /*$wgWDprefix*/collection 
	ADD INDEX /*$wgWDprefix*/versioned_end_collection (`remove_transaction_id`, `collection_id`, `collection_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_end_collection_meaning (`remove_transaction_id`, `collection_mid`, `collection_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_collection_type (`remove_transaction_id`, `collection_type` (4), `collection_id`, `collection_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_start_collection (`add_transaction_id`, `collection_id`, `collection_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_start_collection_meaning (`add_transaction_id`, `collection_mid`, `collection_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_collection_type (`add_transaction_id`, `collection_type` (4), `collection_id`, `collection_mid`);

CREATE TABLE /*$wgWDprefix*/defined_meaning (
  `defined_meaning_id` int(8) unsigned NOT NULL,
  `expression_id` int(10) NOT NULL,
  `meaning_text_tcid` int(10) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_meaning` (`remove_transaction_id`,`defined_meaning_id`,`expression_id`),
  KEY `versioned_end_expression` (`remove_transaction_id`,`expression_id`,`defined_meaning_id`),
  KEY `versioned_end_meaning_text` (`remove_transaction_id`,`meaning_text_tcid`,`defined_meaning_id`),
  KEY `versioned_start_meaning` (`add_transaction_id`,`defined_meaning_id`,`expression_id`),
  KEY `versioned_start_expression` (`add_transaction_id`,`expression_id`,`defined_meaning_id`),
  KEY `versioned_start_meaning_text` (`add_transaction_id`,`meaning_text_tcid`,`defined_meaning_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE /*$wgWDprefix*/defined_meaning 
	ADD INDEX /*$wgWDprefix*/versioned_end_meaning (`remove_transaction_id`, `defined_meaning_id`, `expression_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_expression (`remove_transaction_id`, `expression_id`, `defined_meaning_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_meaning_text (`remove_transaction_id`, `meaning_text_tcid`, `defined_meaning_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_meaning (`add_transaction_id`, `defined_meaning_id`, `expression_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_expression (`add_transaction_id`, `expression_id`, `defined_meaning_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_meaning_text (`add_transaction_id`, `meaning_text_tcid`, `defined_meaning_id`),
	ADD INDEX /*$wgWDprefix*/defined_meaning_idx (`defined_meaning_id`);

CREATE TABLE /*$wgWDprefix*/expression (
  `expression_id` int(10) unsigned NOT NULL,
  `spelling` varchar(255) NOT NULL default '',
  `language_id` int(10) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_expression` (`remove_transaction_id`,`expression_id`,`language_id`),
  KEY `versioned_end_language` (`remove_transaction_id`,`language_id`,`expression_id`),
  KEY `versioned_end_spelling` (`remove_transaction_id`,`spelling`,`expression_id`,`language_id`),
  KEY `versioned_start_expression` (`add_transaction_id`,`expression_id`,`language_id`),
  KEY `versioned_start_language` (`add_transaction_id`,`language_id`,`expression_id`),
  KEY `versioned_start_spelling` (`add_transaction_id`,`spelling`,`expression_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE /*$wgWDprefix*/expression 
	ADD INDEX /*$wgWDprefix*/versioned_end_expression (`remove_transaction_id`, `expression_id`, `language_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_language (`remove_transaction_id`, `language_id`, `expression_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_spelling (`remove_transaction_id`, `spelling` (255), `expression_id`, `language_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_expression (`add_transaction_id`, `expression_id`, `language_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_language (`add_transaction_id`, `language_id`, `expression_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_spelling (`add_transaction_id`, `spelling` (255), `expression_id`, `language_id`),
	ADD INDEX /*$wgWDprefix*/expressions_unique_idx (`expression_id`,`language_id`),
	ADD INDEX /*$wgWDprefix*/expressions_idx	(`expression_id`),
	ADD INDEX /*$wgWDprefix*/language_idx	(`language_id`);

CREATE TABLE /*$wgWDprefix*/meaning_relations (
  `relation_id` int(11) NOT NULL,
  `meaning1_mid` int(10) NOT NULL default '0',
  `meaning2_mid` int(10) NOT NULL default '0',
  `relationtype_mid` int(10) default NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_outgoing` (`remove_transaction_id`,`meaning1_mid`,`relationtype_mid`,`meaning2_mid`),
  KEY `versioned_end_incoming` (`remove_transaction_id`,`meaning2_mid`,`relationtype_mid`,`meaning1_mid`),
  KEY `versioned_end_relation` (`remove_transaction_id`,`relation_id`),
  KEY `versioned_start_outgoing` (`add_transaction_id`,`meaning1_mid`,`relationtype_mid`,`meaning2_mid`),
  KEY `versioned_start_incoming` (`add_transaction_id`,`meaning2_mid`,`relationtype_mid`,`meaning1_mid`),
  KEY `versioned_start_relation` (`add_transaction_id`,`relation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE /*$wgWDprefix*/meaning_relations 
	ADD INDEX /*$wgWDprefix*/versioned_end_outgoing (`remove_transaction_id`, `meaning1_mid`, `relationtype_mid`, `meaning2_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_end_incoming (`remove_transaction_id`, `meaning2_mid`, `relationtype_mid`, `meaning1_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_end_relation (`remove_transaction_id`, `relation_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_outgoing (`add_transaction_id`, `meaning1_mid`, `relationtype_mid`, `meaning2_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_start_incoming (`add_transaction_id`, `meaning2_mid`, `relationtype_mid`, `meaning1_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_start_relation (`add_transaction_id`, `relation_id`);

CREATE TABLE /*$wgWDprefix*/objects (
  `object_id` int(11) NOT NULL auto_increment,
  `table` varchar(100) collate utf8_bin NOT NULL,
  `original_id` int(11) default NULL,
  `UUID` varchar(36) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`object_id`),
  KEY `table` (`table`),
  KEY `original_id` (`original_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- attribute_id - refers to the object_id from xx_class_attributes
CREATE TABLE /*$wgWDprefix*/option_attribute_options (
  `option_id` int(11) NOT NULL default '0',
  `attribute_id` int(11) NOT NULL default '0',
  `option_mid` int(11) NOT NULL default '0',
  `language_id` int(11) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL default '0',
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_option` (`remove_transaction_id`,`option_mid`,`attribute_id`,`option_id`),
  KEY `versioned_end_attribute` (`remove_transaction_id`,`attribute_id`,`option_id`,`option_mid`),
  KEY `versioned_end_id` (`remove_transaction_id`,`option_id`),
  KEY `versioned_start_option` (`add_transaction_id`,`option_mid`,`attribute_id`,`option_id`),
  KEY `versioned_start_attribute` (`add_transaction_id`,`attribute_id`,`option_id`,`option_mid`),
  KEY `versioned_start_id` (`add_transaction_id`,`option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE /*$wgWDprefix*/option_attribute_options 
	ADD INDEX /*$wgWDprefix*/versioned_end_option (`remove_transaction_id`, `option_mid`, `attribute_id`, `option_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_attribute (`remove_transaction_id`, `attribute_id`, `option_id`, `option_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_end_id (`remove_transaction_id`, `option_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_option (`add_transaction_id`, `option_mid`, `attribute_id`, `option_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_attribute (`add_transaction_id`, `attribute_id`, `option_id`, `option_mid`),
	ADD INDEX /*$wgWDprefix*/versioned_start_id (`add_transaction_id`, `option_id`);


CREATE TABLE /*$wgWDprefix*/option_attribute_values (
  `value_id` int(11) NOT NULL default '0',
  `object_id` int(11) NOT NULL default '0',
  `option_id` int(11) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL default '0',
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_object` (`remove_transaction_id`,`object_id`,`option_id`,`value_id`),
  KEY `versioned_end_option` (`remove_transaction_id`,`option_id`,`object_id`,`value_id`),
  KEY `versioned_end_value` (`remove_transaction_id`,`value_id`),
  KEY `versioned_start_object` (`add_transaction_id`,`object_id`,`option_id`,`value_id`),
  KEY `versioned_start_option` (`add_transaction_id`,`option_id`,`object_id`,`value_id`),
  KEY `versioned_start_value` (`add_transaction_id`,`value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE /*$wgWDprefix*/option_attribute_values 
	ADD INDEX /*$wgWDprefix*/versioned_end_object (`remove_transaction_id`, `object_id`, `option_id`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_option (`remove_transaction_id`, `option_id`, `object_id`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_value (`remove_transaction_id`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_object (`add_transaction_id`, `object_id`, `option_id`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_option (`add_transaction_id`, `option_id`, `object_id`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_value (`add_transaction_id`, `value_id`);

CREATE TABLE /*$wgWDprefix*/script_log (
  `script_id` int(11) NOT NULL default '0',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `script_name` varchar(128) collate utf8_bin NOT NULL default '',
  `comment` varchar(128) collate utf8_bin NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE /*$wgWDprefix*/syntrans (
  `syntrans_sid` int(10) NOT NULL default '0',
  `defined_meaning_id` int(10) NOT NULL default '0',
  `expression_id` int(10) NOT NULL,
  `identical_meaning` tinyint(1) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_syntrans` (`remove_transaction_id`,`syntrans_sid`),
  KEY `versioned_end_expression` (`remove_transaction_id`,`expression_id`,`identical_meaning`,`defined_meaning_id`),
  KEY `versioned_end_defined_meaning` (`remove_transaction_id`,`defined_meaning_id`,`identical_meaning`,`expression_id`),
  KEY `versioned_start_syntrans` (`add_transaction_id`,`syntrans_sid`),
  KEY `versioned_start_expression` (`add_transaction_id`,`expression_id`,`identical_meaning`,`defined_meaning_id`),
  KEY `versioned_start_defined_meaning` (`add_transaction_id`,`defined_meaning_id`,`identical_meaning`,`expression_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE /*$wgWDprefix*/syntrans 
	ADD INDEX /*$wgWDprefix*/versioned_end_syntrans (`remove_transaction_id`, `syntrans_sid`),
	ADD INDEX /*$wgWDprefix*/versioned_end_expression (`remove_transaction_id`, `expression_id`, `identical_meaning`, `defined_meaning_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_defined_meaning (`remove_transaction_id`, `defined_meaning_id`, `identical_meaning`, `expression_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_syntrans (`add_transaction_id`, `syntrans_sid`),
	ADD INDEX /*$wgWDprefix*/versioned_start_expression (`add_transaction_id`, `expression_id`, `identical_meaning`, `defined_meaning_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_defined_meaning (`add_transaction_id`, `defined_meaning_id`, `identical_meaning`, `expression_id`),
	ADD INDEX /*$wgWDprefix*/syntrans_defined_meaning_idx	(`defined_meaning_id`),
	ADD INDEX /*$wgWDprefix*/syntrans_expression_id_idx	(`expression_id`),
	ADD INDEX /*$wgWDprefix*/syntrans_remove_transaction_idx	(`remove_transaction_id`);

CREATE TABLE /*$wgWDprefix*/text (
  `text_id` int(8) unsigned NOT NULL auto_increment,
  `text_text` mediumblob NOT NULL,
  `text_flags` tinyblob default NULL,
  PRIMARY KEY  (`text_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE /*$wgWDprefix*/text_attribute_values (
  `value_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `attribute_mid` int(11) NOT NULL,
  `text` mediumblob NOT NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_object` (`remove_transaction_id`,`object_id`,`attribute_mid`,`value_id`),
  KEY `versioned_end_attribute` (`remove_transaction_id`,`attribute_mid`,`object_id`,`value_id`),
  KEY `versioned_end_value` (`remove_transaction_id`,`value_id`),
  KEY `versioned_start_object` (`add_transaction_id`,`object_id`,`attribute_mid`,`value_id`),
  KEY `versioned_start_attribute` (`add_transaction_id`,`attribute_mid`,`object_id`,`value_id`),
  KEY `versioned_start_value` (`add_transaction_id`,`value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE /*$wgWDprefix*/text_attribute_values 
	ADD INDEX /*$wgWDprefix*/versioned_end_object (`remove_transaction_id`, `object_id`, `attribute_mid`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_attribute (`remove_transaction_id`, `attribute_mid`, `object_id`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_value (`remove_transaction_id`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_object (`add_transaction_id`, `object_id`, `attribute_mid`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_attribute (`add_transaction_id`, `attribute_mid`, `object_id`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_value (`add_transaction_id`, `value_id`);

CREATE TABLE /*$wgWDprefix*/transactions (
  `transaction_id` int(11) NOT NULL auto_increment,
  `user_id` int(5) NOT NULL,
  `user_ip` varchar(15) collate utf8_bin NOT NULL,
  `timestamp` varchar(14) collate utf8_bin NOT NULL,
  `comment` tinyblob NOT NULL,
  PRIMARY KEY  (`transaction_id`),
  KEY `user` (`user_id`,`transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE /*$wgWDprefix*/transactions 
	ADD INDEX /*$wgWDprefix*/user (`user_id`, `transaction_id`);

CREATE TABLE /*$wgWDprefix*/translated_content (
  `translated_content_id` int(11) NOT NULL default '0',
  `language_id` int(10) NOT NULL default '0',
  `text_id` int(10) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_translated_content` (`remove_transaction_id`,`translated_content_id`,`language_id`,`text_id`),
  KEY `versioned_end_text` (`remove_transaction_id`,`text_id`,`translated_content_id`,`language_id`),
  KEY `versioned_start_translated_content` (`add_transaction_id`,`translated_content_id`,`language_id`,`text_id`),
  KEY `versioned_start_text` (`add_transaction_id`,`text_id`,`translated_content_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE /*$wgWDprefix*/translated_content 
	ADD INDEX /*$wgWDprefix*/versioned_end_translated_content (`remove_transaction_id`, `translated_content_id`, `language_id`, `text_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_text (`remove_transaction_id`, `text_id`, `translated_content_id`, `language_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_translated_content (`add_transaction_id`, `translated_content_id`, `language_id`, `text_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_text (`add_transaction_id`, `text_id`, `translated_content_id`, `language_id`);

CREATE TABLE /*$wgWDprefix*/translated_content_attribute_values (
  `value_id` int(11) NOT NULL default '0',
  `object_id` int(11) NOT NULL,
  `attribute_mid` int(11) NOT NULL,
  `value_tcid` int(11) NOT NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_object` (`remove_transaction_id`,`object_id`,`attribute_mid`,`value_tcid`),
  KEY `versioned_end_attribute` (`remove_transaction_id`,`attribute_mid`,`object_id`,`value_tcid`),
  KEY `versioned_end_translated_content` (`remove_transaction_id`,`value_tcid`,`value_id`),
  KEY `versioned_end_value` (`remove_transaction_id`,`value_id`),
  KEY `versioned_start_object` (`add_transaction_id`,`object_id`,`attribute_mid`,`value_tcid`),
  KEY `versioned_start_attribute` (`add_transaction_id`,`attribute_mid`,`object_id`,`value_tcid`),
  KEY `versioned_start_translated_content` (`add_transaction_id`,`value_tcid`,`value_id`),
  KEY `versioned_start_value` (`add_transaction_id`,`value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE /*$wgWDprefix*/translated_content_attribute_values 
	ADD INDEX /*$wgWDprefix*/versioned_end_object (`remove_transaction_id`, `object_id`, `attribute_mid`, `value_tcid`),
	ADD INDEX /*$wgWDprefix*/versioned_end_attribute (`remove_transaction_id`, `attribute_mid`, `object_id`, `value_tcid`),
	ADD INDEX /*$wgWDprefix*/versioned_end_translated_content (`remove_transaction_id`, `value_tcid`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_value (`remove_transaction_id`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_object (`add_transaction_id`, `object_id`, `attribute_mid`, `value_tcid`),
	ADD INDEX /*$wgWDprefix*/versioned_start_attribute (`add_transaction_id`, `attribute_mid`, `object_id`, `value_tcid`),
	ADD INDEX /*$wgWDprefix*/versioned_start_translated_content (`add_transaction_id`, `value_tcid`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_value (`add_transaction_id`, `value_id`);

CREATE TABLE /*$wgWDprefix*/url_attribute_values (
  `value_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `attribute_mid` int(11) NOT NULL,
  `url` varchar(255) collate utf8_bin NOT NULL,
  `label` varchar(255) collate utf8_bin NOT NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL,
  KEY `versioned_end_object` (`remove_transaction_id`,`object_id`,`attribute_mid`,`value_id`),
  KEY `versioned_end_attribute` (`remove_transaction_id`,`attribute_mid`,`object_id`,`value_id`),
  KEY `versioned_end_value` (`remove_transaction_id`,`value_id`),
  KEY `versioned_start_object` (`add_transaction_id`,`object_id`,`attribute_mid`,`value_id`),
  KEY `versioned_start_attribute` (`add_transaction_id`,`attribute_mid`,`object_id`,`value_id`),
  KEY `versioned_start_value` (`add_transaction_id`,`value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE /*$wgWDprefix*/url_attribute_values 
	ADD INDEX /*$wgWDprefix*/versioned_end_object (`remove_transaction_id`, `object_id`, `attribute_mid`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_attribute (`remove_transaction_id`, `attribute_mid`, `object_id`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_end_value (`remove_transaction_id`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_object (`add_transaction_id`, `object_id`, `attribute_mid`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_attribute (`add_transaction_id`, `attribute_mid`, `object_id`, `value_id`),
	ADD INDEX /*$wgWDprefix*/versioned_start_value (`add_transaction_id`, `value_id`);
	

