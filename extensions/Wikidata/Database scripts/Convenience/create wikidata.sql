--
-- Add the wikidata specific namespaces
--

INSERT INTO `namespace` (`ns_id`,`ns_system`,`ns_subpages`,`ns_search_default`,`ns_target`,`ns_parent`,`ns_hidden`,`ns_count`,`ns_class`) VALUES 
 (16,NULL,0,0,'',NULL,0,'OmegaWiki',1);
 (17,NULL,1,0,'',16,0,NULL,NULL),
 (24,NULL,0,0,'',NULL,0,'DefinedMeaning',1);
 (25,NULL,1,0,'',24,0,NULL,NULL);

INSERT INTO `namespace_names` (`ns_id`,`ns_name`,`ns_default`,`ns_canonical`) VALUES 
 (16,'Expression',1,0),
 (17,'Expression_talk',1,0),
 (24,'DefinedMeaning',1,0),
 (25,'DefinedMeaning_talk',1,0);

--
-- Definition of table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `language_id` int(10) NOT NULL auto_increment,
  `dialect_of_lid` int(10) NOT NULL default '0',
  `iso639_2` varchar(10) collate latin1_general_ci NOT NULL default '',
  `iso639_3` varchar(10) collate latin1_general_ci NOT NULL default '',
  `wikimedia_key` varchar(10) collate latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

INSERT INTO `language` (`language_id`,`dialect_of_lid`,`iso639_2`,`iso639_3`,`wikimedia_key`) VALUES 
 (84,0,'','','bg'),
 (85,0,'eng','','en'),
 (86,0,'fre','','fr'),
 (87,0,'spa','','es'),
 (88,0,'','','ru'),
 (89,0,'dut','','nl'),
 (90,0,'','','cs'),
 (91,0,'swe','','sv'),
 (92,0,'','','sl'),
 (93,0,'','','pl'),
 (94,0,'por','','pt'),
 (95,0,'nor','','no'),
 (96,0,'baq','','eu'),
 (97,0,'','','sk'),
 (98,0,'','','et'),
 (99,0,'fin','','fi'),
 (100,0,'ita','','it'),
 (101,0,'ger','','de'),
 (102,0,'hun','','hu'),
 (103,0,'dan','','da'),
 (104,0,'','','en-US'),
 (105,0,'','','el'),
 (106,0,'heb','','he');


--
-- Definition of table `language_names`
--

DROP TABLE IF EXISTS `language_names`;
CREATE TABLE `language_names` (
  `language_id` int(10) NOT NULL default '0',
  `name_language_id` int(10) NOT NULL default '0',
  `language_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`language_id`,`name_language_id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `language_names`
--

INSERT INTO `language_names` (`language_id`,`name_language_id`,`language_name`) VALUES 
 (84,85,'Bulgarian'),
 (84,101,'Bulgarisch'),
 (85,85,'English'),
 (85,101,'Englisch'),
 (86,85,'French'),
 (86,101,'FranzÃ¶sisch'),
 (87,85,'Spanish'),
 (87,101,'Spanisch'),
 (88,85,'Russian'),
 (88,101,'Russisch'),
 (89,85,'Dutch'),
 (89,101,'NiederlÃ¤ndisch'),
 (90,85,'Czech'),
 (90,101,'Tschechisch'),
 (91,85,'Swedish'),
 (91,101,'Schwedisch'),
 (92,85,'Slovenian'),
 (92,101,'Slowenisch'),
 (93,85,'Polish'),
 (93,101,'Polnisch'),
 (94,85,'Portuguese'),
 (94,101,'Portugiesisch'),
 (95,85,'Norwegian'),
 (95,101,'Norwegisch'),
 (96,85,'Basque'),
 (96,101,'Baskisch'),
 (97,85,'Slovak'),
 (97,101,'Slowakische Sprache'),
 (98,85,'Estonian'),
 (98,101,'Estnisch'),
 (99,85,'Finnish'),
 (99,101,'Finnisch'),
 (100,85,'Italian'),
 (100,101,'Italienisch'),
 (101,85,'German'),
 (101,101,'Deutsch'),
 (102,85,'Hungarian'),
 (102,101,'Ungarisch'),
 (103,85,'Dansk'),
 (103,101,'DÃ¤nisch'),
 (104,85,'English (United States)'),
 (104,101,'Englisch (USA)');
INSERT INTO `language_names` (`language_id`,`name_language_id`,`language_name`) VALUES 
 (105,85,'Greek'),
 (105,101,'Griechisch');


--
-- Definition of table `uw_alt_meaningtexts`
--

DROP TABLE IF EXISTS `uw_alt_meaningtexts`;
CREATE TABLE `uw_alt_meaningtexts` (
  `meaning_mid` int(10) default NULL,
  `meaning_text_tcid` int(10) default NULL,
  `source_id` int(11) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Definition of table `uw_bootstrapped_defined_meanings`
--

DROP TABLE IF EXISTS `uw_bootstrapped_defined_meanings`;
CREATE TABLE `uw_bootstrapped_defined_meanings` (
  `name` varchar(255) NOT NULL,
  `defined_meaning_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `uw_bootstrapped_defined_meanings` (`name`,`defined_meaning_id`) VALUES 
 ('DefinedMeaning',49),
 ('Definition',52),
 ('SynTrans',55),
 ('Relation',58),
 ('Annotation',61);


--
-- Definition of table `uw_class_attributes`
--

DROP TABLE IF EXISTS `uw_class_attributes`;
CREATE TABLE `uw_class_attributes` (
  `object_id` int(11) NOT NULL,
  `class_mid` int(11) NOT NULL default '0',
  `level_mid` int(11) NOT NULL,
  `attribute_mid` int(11) NOT NULL default '0',
  `attribute_type` char(4) collate latin1_general_ci NOT NULL default 'TEXT',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Definition of table `uw_class_membership`
--

DROP TABLE IF EXISTS `uw_class_membership`;
CREATE TABLE `uw_class_membership` (
  `class_membership_id` int(11) NOT NULL,
  `class_mid` int(11) NOT NULL default '0',
  `class_member_mid` int(11) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Definition of table `uw_collection_contents`
--

DROP TABLE IF EXISTS `uw_collection_contents`;
CREATE TABLE `uw_collection_contents` (
  `collection_id` int(10) NOT NULL default '0',
  `member_mid` int(10) NOT NULL default '0',
  `internal_member_id` varchar(255) default NULL,
  `applicable_language_id` int(10) default NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `uw_collection_language`
--

DROP TABLE IF EXISTS `uw_collection_language`;
CREATE TABLE `uw_collection_language` (
  `collection_id` int(10) NOT NULL default '0',
  `language_id` int(10) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `uw_collection_ns`
--

DROP TABLE IF EXISTS `uw_collection_ns`;
CREATE TABLE `uw_collection_ns` (
  `collection_id` int(10) unsigned NOT NULL,
  `collection_mid` int(10) NOT NULL default '0',
  `collection_type` char(4) default NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `uw_defined_meaning`
--

DROP TABLE IF EXISTS `uw_defined_meaning`;
CREATE TABLE `uw_defined_meaning` (
  `defined_meaning_id` int(8) unsigned NOT NULL,
  `expression_id` int(10) NOT NULL,
  `meaning_text_tcid` int(10) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `uw_expression_ns`
--

DROP TABLE IF EXISTS `uw_expression_ns`;
CREATE TABLE `uw_expression_ns` (
  `expression_id` int(10) unsigned NOT NULL,
  `spelling` varchar(255) NOT NULL default '',
  `hyphenation` varchar(255) NOT NULL default '',
  `language_id` int(10) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `uw_meaning_relations`
--

DROP TABLE IF EXISTS `uw_meaning_relations`;
CREATE TABLE `uw_meaning_relations` (
  `relation_id` int(11) NOT NULL,
  `meaning1_mid` int(10) NOT NULL default '0',
  `meaning2_mid` int(10) NOT NULL default '0',
  `relationtype_mid` int(10) default NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `uw_objects`
--

DROP TABLE IF EXISTS `uw_objects`;
CREATE TABLE `uw_objects` (
  `object_id` int(11) NOT NULL auto_increment,
  `table` varchar(100) collate latin1_general_ci NOT NULL,
  `original_id` int(11) default NULL,
  `UUID` varchar(36) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`object_id`),
  KEY `table` (`table`),
  KEY `original_id` (`original_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Definition of table `uw_option_attribute_options`
--

DROP TABLE IF EXISTS `uw_option_attribute_options`;
CREATE TABLE `uw_option_attribute_options` (
  `option_id` int(11) NOT NULL default '0',
  `attribute_id` int(11) NOT NULL default '0',
  `option_mid` int(11) NOT NULL default '0',
  `language_id` int(11) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL default '0',
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `uw_option_attribute_values`
--

DROP TABLE IF EXISTS `uw_option_attribute_values`;
CREATE TABLE `uw_option_attribute_values` (
  `value_id` int(11) NOT NULL default '0',
  `object_id` int(11) NOT NULL default '0',
  `option_id` int(11) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL default '0',
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `uw_script_log`
--

DROP TABLE IF EXISTS `uw_script_log`;
CREATE TABLE `uw_script_log` (
  `script_id` int(11) NOT NULL default '0',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `script_name` varchar(128) character set latin1 collate latin1_general_ci NOT NULL default '',
  `comment` varchar(128) character set latin1 collate latin1_general_ci NOT NULL default ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `uw_syntrans`
--

DROP TABLE IF EXISTS `uw_syntrans`;
CREATE TABLE `uw_syntrans` (
  `syntrans_sid` int(10) NOT NULL default '0',
  `defined_meaning_id` int(10) NOT NULL default '0',
  `expression_id` int(10) NOT NULL,
  `firstuse` char(14) NOT NULL default '',
  `identical_meaning` tinyint(1) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `uw_syntrans_relations`
--

DROP TABLE IF EXISTS `uw_syntrans_relations`;
CREATE TABLE `uw_syntrans_relations` (
  `syntrans1_id` int(10) NOT NULL,
  `syntrans2_id` int(10) NOT NULL,
  `relationtype_mid` int(10) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `uw_text`
--

DROP TABLE IF EXISTS `uw_text`;
CREATE TABLE `uw_text` (
  `text_id` int(8) unsigned NOT NULL auto_increment,
  `text_text` mediumblob NOT NULL,
  `text_flags` tinyblob NOT NULL,
  PRIMARY KEY  (`text_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `uw_text_attribute_values`
--

DROP TABLE IF EXISTS `uw_text_attribute_values`;
CREATE TABLE `uw_text_attribute_values` (
  `value_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `attribute_mid` int(11) NOT NULL,
  `text` varchar(255) collate latin1_general_ci NOT NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Definition of table `uw_transactions`
--

DROP TABLE IF EXISTS `uw_transactions`;
CREATE TABLE `uw_transactions` (
  `transaction_id` int(11) NOT NULL auto_increment,
  `user_id` int(5) NOT NULL,
  `user_ip` varchar(15) collate latin1_general_ci NOT NULL,
  `timestamp` varchar(14) collate latin1_general_ci NOT NULL,
  `comment` tinyblob NOT NULL,
  PRIMARY KEY  (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Definition of table `uw_translated_content`
--

DROP TABLE IF EXISTS `uw_translated_content`;
CREATE TABLE `uw_translated_content` (
  `translated_content_id` int(11) NOT NULL default '0',
  `language_id` int(10) NOT NULL default '0',
  `shorttext_id` int(10) NOT NULL default '0',
  `text_id` int(10) NOT NULL default '0',
  `original_language_id` int(10) NOT NULL default '0',
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `uw_translated_content_attribute_values`
--

DROP TABLE IF EXISTS `uw_translated_content_attribute_values`;
CREATE TABLE `uw_translated_content_attribute_values` (
  `value_id` int(11) NOT NULL default '0',
  `object_id` int(11) NOT NULL,
  `attribute_mid` int(11) NOT NULL,
  `value_tcid` int(11) NOT NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Definition of table `uw_url_attribute_values`
--

DROP TABLE IF EXISTS `uw_url_attribute_values`;
CREATE TABLE `uw_url_attribute_values` (
  `value_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `attribute_mid` int(11) NOT NULL,
  `url` varchar(255) collate latin1_general_ci NOT NULL,
  `label` varchar(255) collate latin1_general_ci NOT NULL,
  `add_transaction_id` int(11) NOT NULL,
  `remove_transaction_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Definition of table `validate`
--

DROP TABLE IF EXISTS `validate`;
CREATE TABLE `validate` (
  `val_user` int(11) NOT NULL default '0',
  `val_page` int(11) unsigned NOT NULL default '0',
  `val_revision` int(11) unsigned NOT NULL default '0',
  `val_type` int(11) unsigned NOT NULL default '0',
  `val_value` int(11) default '0',
  `val_comment` varchar(255) NOT NULL default '',
  `val_ip` varchar(20) NOT NULL default '',
  KEY `val_user` (`val_user`,`val_revision`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `wikidata_sets`
--

DROP TABLE IF EXISTS `wikidata_sets`;
CREATE TABLE `wikidata_sets` (
  `set_prefix` varchar(20) default NULL,
  `set_string` varchar(100) default NULL,
  `set_dmid` int(10) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `wikidata_sets` (`set_prefix`,`set_string`,`set_dmid`) VALUES 
 ('uw','OmegaWiki community',0),
 ('umls','Unified Medical Language System',0),
 ('sp','Swiss-Prot',0);
