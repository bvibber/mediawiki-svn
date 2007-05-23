DROP TABLE IF EXISTS language;

CREATE TABLE language (
  language_id int(10) NOT NULL auto_increment,
  dialect_of_lid int(10) NOT NULL default '0',
  iso639_2 varchar(10) collate latin1_general_ci NOT NULL default '',
  iso639_3 varchar(10) collate latin1_general_ci NOT NULL default '',
  wikimedia_key varchar(10) collate latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (language_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

INSERT INTO language (language_id,dialect_of_lid,iso639_2,iso639_3,wikimedia_key) VALUES 
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

DROP TABLE IF EXISTS language_names;

CREATE TABLE language_names (
  language_id int(10) NOT NULL default '0',
  name_language_id int(10) NOT NULL default '0',
  language_name varchar(255) NOT NULL default '',
  PRIMARY KEY  (language_id,name_language_id),
  KEY language_id (language_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO language_names (language_id,name_language_id,language_name) VALUES 
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
 
INSERT INTO language_names (language_id,name_language_id,language_name) VALUES 
 (105,85,'Greek'),
 (105,101,'Griechisch');

DROP TABLE IF EXISTS validate;

CREATE TABLE validate (
  val_user int(11) NOT NULL default '0',
  val_page int(11) unsigned NOT NULL default '0',
  val_revision int(11) unsigned NOT NULL default '0',
  val_type int(11) unsigned NOT NULL default '0',
  val_value int(11) default '0',
  val_comment varchar(255) NOT NULL default '',
  val_ip varchar(20) NOT NULL default '',
  KEY val_user (`val_user`,`val_revision`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS wikidata_sets;

CREATE TABLE wikidata_sets (
  set_prefix varchar(20) default NULL,
  set_string varchar(100) default NULL,
  set_dmid int(10) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO wikidata_sets (set_prefix,set_string,set_dmid) VALUES 
 ('uw','OmegaWiki community',0),
 ('umls','Unified Medical Language System',0),
 ('sp','Swiss-Prot',0);
 