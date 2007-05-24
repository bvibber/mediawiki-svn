--
-- Definition of table `archive`
--

DROP TABLE IF EXISTS `archive`;
CREATE TABLE `archive` (
  `ar_namespace` int(11) NOT NULL default '0',
  `ar_title` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `ar_text` mediumblob NOT NULL,
  `ar_comment` tinyblob NOT NULL,
  `ar_user` int(5) unsigned NOT NULL default '0',
  `ar_user_text` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `ar_timestamp` varchar(14) character set latin1 collate latin1_bin NOT NULL default '',
  `ar_minor_edit` tinyint(1) NOT NULL default '0',
  `ar_flags` tinyblob NOT NULL,
  `ar_rev_id` int(8) unsigned default NULL,
  `ar_text_id` int(8) unsigned default NULL,
  KEY `name_title_timestamp` (`ar_namespace`,`ar_title`,`ar_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `categorylinks`
--

DROP TABLE IF EXISTS `categorylinks`;
CREATE TABLE `categorylinks` (
  `cl_from` int(8) unsigned NOT NULL default '0',
  `cl_to` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `cl_sortkey` varchar(86) character set latin1 collate latin1_bin NOT NULL default '',
  `cl_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  UNIQUE KEY `cl_from` (`cl_from`,`cl_to`),
  KEY `cl_sortkey` (`cl_to`,`cl_sortkey`),
  KEY `cl_timestamp` (`cl_to`,`cl_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `externallinks`
--

DROP TABLE IF EXISTS `externallinks`;
CREATE TABLE `externallinks` (
  `el_from` int(8) unsigned NOT NULL default '0',
  `el_to` blob NOT NULL,
  `el_index` blob NOT NULL,
  KEY `el_from` (`el_from`,`el_to`(40)),
  KEY `el_to` (`el_to`(60),`el_from`),
  KEY `el_index` (`el_index`(60))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `filearchive`
--

DROP TABLE IF EXISTS `filearchive`;
CREATE TABLE `filearchive` (
  `fa_id` int(11) NOT NULL auto_increment,
  `fa_name` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `fa_archive_name` varchar(255) character set latin1 collate latin1_bin default '',
  `fa_storage_group` varchar(16) default NULL,
  `fa_storage_key` varchar(64) character set latin1 collate latin1_bin default '',
  `fa_deleted_user` int(11) default NULL,
  `fa_deleted_timestamp` char(14) character set latin1 collate latin1_bin default '',
  `fa_deleted_reason` text,
  `fa_size` int(8) unsigned default '0',
  `fa_width` int(5) default '0',
  `fa_height` int(5) default '0',
  `fa_metadata` mediumblob,
  `fa_bits` int(3) default '0',
  `fa_media_type` enum('UNKNOWN','BITMAP','DRAWING','AUDIO','VIDEO','MULTIMEDIA','OFFICE','TEXT','EXECUTABLE','ARCHIVE') default NULL,
  `fa_major_mime` enum('unknown','application','audio','image','text','video','message','model','multipart') default 'unknown',
  `fa_minor_mime` varchar(32) default 'unknown',
  `fa_description` tinyblob,
  `fa_user` int(5) unsigned default '0',
  `fa_user_text` varchar(255) character set latin1 collate latin1_bin default '',
  `fa_timestamp` char(14) character set latin1 collate latin1_bin default '',
  PRIMARY KEY  (`fa_id`),
  KEY `fa_name` (`fa_name`,`fa_timestamp`),
  KEY `fa_storage_group` (`fa_storage_group`,`fa_storage_key`),
  KEY `fa_deleted_timestamp` (`fa_deleted_timestamp`),
  KEY `fa_deleted_user` (`fa_deleted_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `hitcounter`
--

DROP TABLE IF EXISTS `hitcounter`;
CREATE TABLE `hitcounter` (
  `hc_id` int(10) unsigned NOT NULL default '0'
) ENGINE=MEMORY DEFAULT CHARSET=utf8 MAX_ROWS=25000;

--
-- Definition of table `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE `image` (
  `img_name` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `img_size` int(8) unsigned NOT NULL default '0',
  `img_width` int(5) NOT NULL default '0',
  `img_height` int(5) NOT NULL default '0',
  `img_metadata` mediumblob NOT NULL,
  `img_bits` int(3) NOT NULL default '0',
  `img_media_type` enum('UNKNOWN','BITMAP','DRAWING','AUDIO','VIDEO','MULTIMEDIA','OFFICE','TEXT','EXECUTABLE','ARCHIVE') default NULL,
  `img_major_mime` enum('unknown','application','audio','image','text','video','message','model','multipart') NOT NULL default 'unknown',
  `img_minor_mime` varchar(32) NOT NULL default 'unknown',
  `img_description` tinyblob NOT NULL,
  `img_user` int(5) unsigned NOT NULL default '0',
  `img_user_text` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `img_timestamp` varchar(14) character set latin1 collate latin1_bin NOT NULL default '',
  PRIMARY KEY  (`img_name`),
  KEY `img_size` (`img_size`),
  KEY `img_timestamp` (`img_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `imagelinks`
--

DROP TABLE IF EXISTS `imagelinks`;
CREATE TABLE `imagelinks` (
  `il_from` int(8) unsigned NOT NULL default '0',
  `il_to` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  UNIQUE KEY `il_from` (`il_from`,`il_to`),
  KEY `il_to` (`il_to`,`il_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `interwiki`
--

DROP TABLE IF EXISTS `interwiki`;
CREATE TABLE `interwiki` (
  `iw_prefix` char(32) NOT NULL default '',
  `iw_url` char(127) NOT NULL default '',
  `iw_local` tinyint(1) NOT NULL default '0',
  `iw_trans` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `iw_prefix` (`iw_prefix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `interwiki`
--

/*!40000 ALTER TABLE `interwiki` DISABLE KEYS */;
INSERT INTO `interwiki` (`iw_prefix`,`iw_url`,`iw_local`,`iw_trans`) VALUES 
 ('abbenormal','http://www.ourpla.net/cgi-bin/pikie.cgi?$1',0,0),
 ('acadwiki','http://xarch.tu-graz.ac.at/autocad/wiki/$1',0,0),
 ('acronym','http://www.acronymfinder.com/af-query.asp?String=exact&Acronym=$1',0,0),
 ('advogato','http://www.advogato.org/$1',0,0),
 ('aiwiki','http://www.ifi.unizh.ch/ailab/aiwiki/aiw.cgi?$1',0,0),
 ('alife','http://news.alife.org/wiki/index.php?$1',0,0),
 ('annotation','http://bayle.stanford.edu/crit/nph-med.cgi/$1',0,0),
 ('annotationwiki','http://www.seedwiki.com/page.cfm?wikiid=368&doc=$1',0,0),
 ('arxiv','http://www.arxiv.org/abs/$1',0,0),
 ('aspienetwiki','http://aspie.mela.de/Wiki/index.php?title=$1',0,0),
 ('bemi','http://bemi.free.fr/vikio/index.php?$1',0,0),
 ('benefitswiki','http://www.benefitslink.com/cgi-bin/wiki.cgi?$1',0,0),
 ('brasilwiki','http://rio.ifi.unizh.ch/brasilienwiki/index.php/$1',0,0),
 ('bridgeswiki','http://c2.com/w2/bridges/$1',0,0),
 ('c2find','http://c2.com/cgi/wiki?FindPage&value=$1',0,0),
 ('cache','http://www.google.com/search?q=cache:$1',0,0);
INSERT INTO `interwiki` (`iw_prefix`,`iw_url`,`iw_local`,`iw_trans`) VALUES 
 ('ciscavate','http://ciscavate.org/index.php/$1',0,0),
 ('cliki','http://ww.telent.net/cliki/$1',0,0),
 ('cmwiki','http://www.ourpla.net/cgi-bin/wiki.pl?$1',0,0),
 ('codersbase','http://www.codersbase.com/$1',0,0),
 ('commons','http://commons.wikimedia.org/wiki/$1',0,0),
 ('consciousness','http://teadvus.inspiral.org/',0,0),
 ('corpknowpedia','http://corpknowpedia.org/wiki/index.php/$1',0,0),
 ('creationmatters','http://www.ourpla.net/cgi-bin/wiki.pl?$1',0,0),
 ('dejanews','http://www.deja.com/=dnc/getdoc.xp?AN=$1',0,0),
 ('demokraatia','http://wiki.demokraatia.ee/',0,0),
 ('dictionary','http://www.dict.org/bin/Dict?Database=*&Form=Dict1&Strategy=*&Query=$1',0,0),
 ('disinfopedia','http://www.disinfopedia.org/wiki.phtml?title=$1',0,0),
 ('diveintoosx','http://diveintoosx.org/$1',0,0),
 ('docbook','http://docbook.org/wiki/moin.cgi/$1',0,0),
 ('dolphinwiki','http://www.object-arts.com/wiki/html/Dolphin/$1',0,0),
 ('drumcorpswiki','http://www.drumcorpswiki.com/index.php/$1',0,0);
INSERT INTO `interwiki` (`iw_prefix`,`iw_url`,`iw_local`,`iw_trans`) VALUES 
 ('dwjwiki','http://www.suberic.net/cgi-bin/dwj/wiki.cgi?$1',0,0),
 ('echei','http://www.ikso.net/cgi-bin/wiki.pl?$1',0,0),
 ('ecxei','http://www.ikso.net/cgi-bin/wiki.pl?$1',0,0),
 ('efnetceewiki','http://purl.net/wiki/c/$1',0,0),
 ('efnetcppwiki','http://purl.net/wiki/cpp/$1',0,0),
 ('efnetpythonwiki','http://purl.net/wiki/python/$1',0,0),
 ('efnetxmlwiki','http://purl.net/wiki/xml/$1',0,0),
 ('elibre','http://enciclopedia.us.es/index.php/$1',0,0),
 ('eljwiki','http://elj.sourceforge.net/phpwiki/index.php/$1',0,0),
 ('emacswiki','http://www.emacswiki.org/cgi-bin/wiki.pl?$1',0,0),
 ('eokulturcentro','http://esperanto.toulouse.free.fr/wakka.php?wiki=$1',0,0),
 ('evowiki','http://www.evowiki.org/index.php/$1',0,0),
 ('eÄ‰ei','http://www.ikso.net/cgi-bin/wiki.pl?$1',0,0),
 ('finalempire','http://final-empire.sourceforge.net/cgi-bin/wiki.pl?$1',0,0),
 ('firstwiki','http://firstwiki.org/index.php/$1',0,0),
 ('foldoc','http://www.foldoc.org/foldoc/foldoc.cgi?$1',0,0),
 ('foxwiki','http://fox.wikis.com/wc.dll?Wiki~$1',0,0);
INSERT INTO `interwiki` (`iw_prefix`,`iw_url`,`iw_local`,`iw_trans`) VALUES 
 ('fr.be','http://fr.wikinations.be/$1',0,0),
 ('fr.ca','http://fr.ca.wikinations.org/$1',0,0),
 ('fr.fr','http://fr.fr.wikinations.org/$1',0,0),
 ('fr.org','http://fr.wikinations.org/$1',0,0),
 ('freebsdman','http://www.FreeBSD.org/cgi/man.cgi?apropos=1&query=$1',0,0),
 ('gamewiki','http://gamewiki.org/wiki/index.php/$1',0,0),
 ('gej','http://www.esperanto.de/cgi-bin/aktivikio/wiki.pl?$1',0,0),
 ('gentoo-wiki','http://gentoo-wiki.com/$1',0,0),
 ('globalvoices','http://cyber.law.harvard.edu/dyn/globalvoices/wiki/$1',0,0),
 ('gmailwiki','http://www.gmailwiki.com/index.php/$1',0,0),
 ('google','http://www.google.com/search?q=$1',0,0),
 ('googlegroups','http://groups.google.com/groups?q=$1',0,0),
 ('gotamac','http://www.got-a-mac.org/$1',0,0),
 ('greencheese','http://www.greencheese.org/$1',0,0),
 ('hammondwiki','http://www.dairiki.org/HammondWiki/index.php3?$1',0,0),
 ('haribeau','http://wiki.haribeau.de/cgi-bin/wiki.pl?$1',0,0),
 ('herzkinderwiki','http://www.herzkinderinfo.de/Mediawiki/index.php/$1',0,0);
INSERT INTO `interwiki` (`iw_prefix`,`iw_url`,`iw_local`,`iw_trans`) VALUES 
 ('hewikisource','http://he.wikisource.org/wiki/$1',1,0),
 ('hrwiki','http://www.hrwiki.org/index.php/$1',0,0),
 ('iawiki','http://www.IAwiki.net/$1',0,0),
 ('imdb','http://us.imdb.com/Title?$1',0,0),
 ('infosecpedia','http://www.infosecpedia.org/pedia/index.php/$1',0,0),
 ('jargonfile','http://sunir.org/apps/meta.pl?wiki=JargonFile&redirect=$1',0,0),
 ('jefo','http://www.esperanto-jeunes.org/vikio/index.php?$1',0,0),
 ('jiniwiki','http://www.cdegroot.com/cgi-bin/jini?$1',0,0),
 ('jspwiki','http://www.ecyrd.com/JSPWiki/Wiki.jsp?page=$1',0,0),
 ('kerimwiki','http://wiki.oxus.net/$1',0,0),
 ('kmwiki','http://www.voght.com/cgi-bin/pywiki?$1',0,0),
 ('knowhow','http://www2.iro.umontreal.ca/~paquetse/cgi-bin/wiki.cgi?$1',0,0),
 ('lanifexwiki','http://opt.lanifex.com/cgi-bin/wiki.pl?$1',0,0),
 ('lasvegaswiki','http://wiki.gmnow.com/index.php/$1',0,0),
 ('linuxwiki','http://www.linuxwiki.de/$1',0,0),
 ('lojban','http://www.lojban.org/tiki/tiki-index.php?page=$1',0,0),
 ('lqwiki','http://wiki.linuxquestions.org/wiki/$1',0,0);
INSERT INTO `interwiki` (`iw_prefix`,`iw_url`,`iw_local`,`iw_trans`) VALUES 
 ('lugkr','http://lug-kr.sourceforge.net/cgi-bin/lugwiki.pl?$1',0,0),
 ('lutherwiki','http://www.lutheranarchives.com/mw/index.php/$1',0,0),
 ('mathsongswiki','http://SeedWiki.com/page.cfm?wikiid=237&doc=$1',0,0),
 ('mbtest','http://www.usemod.com/cgi-bin/mbtest.pl?$1',0,0),
 ('meatball','http://www.usemod.com/cgi-bin/mb.pl?$1',0,0),
 ('mediazilla','http://bugzilla.wikipedia.org/$1',1,0),
 ('memoryalpha','http://www.memory-alpha.org/en/index.php/$1',0,0),
 ('metaweb','http://www.metaweb.com/wiki/wiki.phtml?title=$1',0,0),
 ('metawiki','http://sunir.org/apps/meta.pl?$1',0,0),
 ('metawikipedia','http://meta.wikimedia.org/wiki/$1',0,0),
 ('moinmoin','http://purl.net/wiki/moin/$1',0,0),
 ('mozillawiki','http://wiki.mozilla.org/index.php/$1',0,0),
 ('muweb','http://www.dunstable.com/scripts/MuWebWeb?$1',0,0),
 ('netvillage','http://www.netbros.com/?$1',0,0),
 ('oeis','http://www.research.att.com/cgi-bin/access.cgi/as/njas/sequences/eisA.cgi?Anum=$1',0,0),
 ('openfacts','http://openfacts.berlios.de/index.phtml?title=$1',0,0);
INSERT INTO `interwiki` (`iw_prefix`,`iw_url`,`iw_local`,`iw_trans`) VALUES 
 ('openwiki','http://openwiki.com/?$1',0,0),
 ('opera7wiki','http://nontroppo.org/wiki/$1',0,0),
 ('orgpatterns','http://www.bell-labs.com/cgi-user/OrgPatterns/OrgPatterns?$1',0,0),
 ('osi reference model','http://wiki.tigma.ee/',0,0),
 ('pangalacticorg','http://www.pangalactic.org/Wiki/$1',0,0),
 ('patwiki','http://gauss.ffii.org/$1',0,0),
 ('personaltelco','http://www.personaltelco.net/index.cgi/$1',0,0),
 ('phpwiki','http://phpwiki.sourceforge.net/phpwiki/index.php?$1',0,0),
 ('pikie','http://pikie.darktech.org/cgi/pikie?$1',0,0),
 ('pmeg','http://www.bertilow.com/pmeg/$1.php',0,0),
 ('ppr','http://c2.com/cgi/wiki?$1',0,0),
 ('purlnet','http://purl.oclc.org/NET/$1',0,0),
 ('pythoninfo','http://www.python.org/cgi-bin/moinmoin/$1',0,0),
 ('pythonwiki','http://www.pythonwiki.de/$1',0,0),
 ('pywiki','http://www.voght.com/cgi-bin/pywiki?$1',0,0),
 ('raec','http://www.raec.clacso.edu.ar:8080/raec/Members/raecpedia/$1',0,0),
 ('revo','http://purl.org/NET/voko/revo/art/$1.html',0,0);
INSERT INTO `interwiki` (`iw_prefix`,`iw_url`,`iw_local`,`iw_trans`) VALUES 
 ('rfc','http://www.rfc-editor.org/rfc/rfc$1.txt',0,0),
 ('s23wiki','http://is-root.de/wiki/index.php/$1',0,0),
 ('scoutpedia','http://www.scoutpedia.info/index.php/$1',0,0),
 ('seapig','http://www.seapig.org/$1',0,0),
 ('seattlewiki','http://seattlewiki.org/wiki/$1',0,0),
 ('seattlewireless','http://seattlewireless.net/?$1',0,0),
 ('seeds','http://www.IslandSeeds.org/wiki/$1',0,0),
 ('senseislibrary','http://senseis.xmp.net/?$1',0,0),
 ('shakti','http://cgi.algonet.se/htbin/cgiwrap/pgd/ShaktiWiki/$1',0,0),
 ('slashdot','http://slashdot.org/article.pl?sid=$1',0,0),
 ('smikipedia','http://www.smikipedia.org/$1',0,0),
 ('sockwiki','http://wiki.socklabs.com/$1',0,0),
 ('sourceforge','http://sourceforge.net/$1',0,0),
 ('squeak','http://minnow.cc.gatech.edu/squeak/$1',0,0),
 ('strikiwiki','http://ch.twi.tudelft.nl/~mostert/striki/teststriki.pl?$1',0,0),
 ('susning','http://www.susning.nu/$1',0,0),
 ('svgwiki','http://www.protocol7.com/svg-wiki/default.asp?$1',0,0),
 ('tavi','http://tavi.sourceforge.net/$1',0,0);
INSERT INTO `interwiki` (`iw_prefix`,`iw_url`,`iw_local`,`iw_trans`) VALUES 
 ('tejo','http://www.tejo.org/vikio/$1',0,0),
 ('terrorwiki','http://www.liberalsagainstterrorism.com/wiki/index.php/$1',0,0),
 ('theopedia','http://www.theopedia.com/$1',0,0),
 ('tmbw','http://www.tmbw.net/wiki/index.php/$1',0,0),
 ('tmnet','http://www.technomanifestos.net/?$1',0,0),
 ('tmwiki','http://www.EasyTopicMaps.com/?page=$1',0,0),
 ('turismo','http://www.tejo.org/turismo/$1',0,0),
 ('twiki','http://twiki.org/cgi-bin/view/$1',0,0),
 ('twistedwiki','http://purl.net/wiki/twisted/$1',0,0),
 ('uea','http://www.tejo.org/uea/$1',0,0),
 ('unreal','http://wiki.beyondunreal.com/wiki/$1',0,0),
 ('ursine','http://wiki.ursine.ca/$1',0,0),
 ('usej','http://www.tejo.org/usej/$1',0,0),
 ('usemod','http://www.usemod.com/cgi-bin/wiki.pl?$1',0,0),
 ('visualworks','http://wiki.cs.uiuc.edu/VisualWorks/$1',0,0),
 ('warpedview','http://www.warpedview.com/index.php/$1',0,0),
 ('webdevwikinl','http://www.promo-it.nl/WebDevWiki/index.php?page=$1',0,0),
 ('webisodes','http://www.webisodes.org/$1',0,0);
INSERT INTO `interwiki` (`iw_prefix`,`iw_url`,`iw_local`,`iw_trans`) VALUES 
 ('webseitzwiki','http://webseitz.fluxent.com/wiki/$1',0,0),
 ('why','http://clublet.com/c/c/why?$1',0,0),
 ('wiki','http://c2.com/cgi/wiki?$1',0,0),
 ('wikia','http://www.wikia.com/wiki/$1',0,0),
 ('wikibooks','http://en.wikibooks.org/wiki/$1',1,0),
 ('wikicities','http://www.wikicities.com/index.php/$1',0,0),
 ('wikif1','http://www.wikif1.org/$1',0,0),
 ('wikihow','http://www.wikihow.com/$1',0,0),
 ('wikimedia','http://wikimediafoundation.org/wiki/$1',0,0),
 ('wikinews','http://en.wikinews.org/wiki/$1',0,0),
 ('wikinfo','http://www.wikinfo.org/wiki.php?title=$1',0,0),
 ('wikipedia','http://en.wikipedia.org/wiki/$1',1,0),
 ('wikiquote','http://en.wikiquote.org/wiki/$1',1,0),
 ('wikisource','http://sources.wikipedia.org/wiki/$1',1,0),
 ('wikispecies','http://species.wikipedia.org/wiki/$1',1,0),
 ('wikitravel','http://wikitravel.org/en/$1',0,0),
 ('wikiworld','http://WikiWorld.com/wiki/index.php/$1',0,0),
 ('wikt','http://en.wiktionary.org/wiki/$1',1,0),
 ('wiktionary','http://en.wiktionary.org/wiki/$1',1,0);
INSERT INTO `interwiki` (`iw_prefix`,`iw_url`,`iw_local`,`iw_trans`) VALUES 
 ('wlug','http://www.wlug.org.nz/$1',0,0),
 ('wlwiki','http://winslowslair.supremepixels.net/wiki/index.php/$1',0,0),
 ('ypsieyeball','http://sknkwrks.dyndns.org:1957/writewiki/wiki.pl?$1',0,0),
 ('zwiki','http://www.zwiki.org/$1',0,0),
 ('zzz wiki','http://wiki.zzz.ee/',0,0);
/*!40000 ALTER TABLE `interwiki` ENABLE KEYS */;


--
-- Definition of table `ipblocks`
--

DROP TABLE IF EXISTS `ipblocks`;
CREATE TABLE `ipblocks` (
  `ipb_id` int(8) NOT NULL auto_increment,
  `ipb_address` tinyblob NOT NULL,
  `ipb_user` int(8) unsigned NOT NULL default '0',
  `ipb_by` int(8) unsigned NOT NULL default '0',
  `ipb_reason` tinyblob NOT NULL,
  `ipb_timestamp` char(14) character set latin1 collate latin1_bin NOT NULL default '',
  `ipb_auto` tinyint(1) NOT NULL default '0',
  `ipb_anon_only` tinyint(1) NOT NULL default '0',
  `ipb_create_account` tinyint(1) NOT NULL default '1',
  `ipb_expiry` char(14) character set latin1 collate latin1_bin NOT NULL default '',
  `ipb_range_start` tinyblob NOT NULL,
  `ipb_range_end` tinyblob NOT NULL,
  `ipb_enable_autoblock` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`ipb_id`),
  UNIQUE KEY `ipb_address_unique` (`ipb_address`(255),`ipb_user`,`ipb_auto`),
  KEY `ipb_user` (`ipb_user`),
  KEY `ipb_range` (`ipb_range_start`(8),`ipb_range_end`(8)),
  KEY `ipb_timestamp` (`ipb_timestamp`),
  KEY `ipb_expiry` (`ipb_expiry`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `job`
--

DROP TABLE IF EXISTS `job`;
CREATE TABLE `job` (
  `job_id` int(9) unsigned NOT NULL auto_increment,
  `job_cmd` varchar(255) NOT NULL default '',
  `job_namespace` int(11) NOT NULL,
  `job_title` varchar(255) character set latin1 collate latin1_bin NOT NULL,
  `job_params` blob NOT NULL,
  PRIMARY KEY  (`job_id`),
  KEY `job_cmd` (`job_cmd`,`job_namespace`,`job_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `langlinks`
--

DROP TABLE IF EXISTS `langlinks`;
CREATE TABLE `langlinks` (
  `ll_from` int(8) unsigned NOT NULL default '0',
  `ll_lang` varchar(10) character set latin1 collate latin1_bin NOT NULL default '',
  `ll_title` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  UNIQUE KEY `ll_from` (`ll_from`,`ll_lang`),
  KEY `ll_lang` (`ll_lang`,`ll_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `logging`
--

DROP TABLE IF EXISTS `logging`;
CREATE TABLE `logging` (
  `log_type` varchar(10) NOT NULL default '',
  `log_action` varchar(10) NOT NULL default '',
  `log_timestamp` varchar(14) NOT NULL default '19700101000000',
  `log_user` int(10) unsigned NOT NULL default '0',
  `log_namespace` int(11) NOT NULL default '0',
  `log_title` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `log_comment` varchar(255) NOT NULL default '',
  `log_params` blob NOT NULL,
  `log_id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`log_id`),
  KEY `type_time` (`log_type`,`log_timestamp`),
  KEY `user_time` (`log_user`,`log_timestamp`),
  KEY `page_time` (`log_namespace`,`log_title`,`log_timestamp`),
  KEY `times` (`log_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `math`
--

DROP TABLE IF EXISTS `math`;
CREATE TABLE `math` (
  `math_inputhash` varchar(16) NOT NULL default '',
  `math_outputhash` varchar(16) NOT NULL default '',
  `math_html_conservativeness` tinyint(1) NOT NULL default '0',
  `math_html` text,
  `math_mathml` text,
  UNIQUE KEY `math_inputhash` (`math_inputhash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `namespace`
--

DROP TABLE IF EXISTS `namespace`;
CREATE TABLE `namespace` (
  `ns_id` int(8) NOT NULL default '0',
  `ns_system` varchar(80) default '',
  `ns_subpages` tinyint(1) NOT NULL default '0',
  `ns_search_default` tinyint(1) NOT NULL default '0',
  `ns_target` varchar(200) default NULL,
  `ns_parent` int(8) default NULL,
  `ns_hidden` tinyint(1) default NULL,
  `ns_class` varchar(100) default NULL,
  `ns_count` tinyint(1) default NULL,
  PRIMARY KEY  (`ns_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `namespace`
--

/*!40000 ALTER TABLE `namespace` DISABLE KEYS */;
INSERT INTO `namespace` (`ns_id`,`ns_system`,`ns_subpages`,`ns_search_default`,`ns_target`,`ns_parent`,`ns_hidden`,`ns_count`,`ns_class`) VALUES 
 (-2,'NS_MEDIA',0,0,NULL,NULL,NULL,0,NULL),
 (-1,'NS_SPECIAL',0,0,NULL,NULL,NULL,0,NULL),
 (0,'NS_MAIN',0,1,NULL,NULL,NULL,1,NULL),
 (1,'NS_TALK',1,0,NULL,0,NULL,0,NULL),
 (2,'NS_USER',1,0,NULL,NULL,NULL,0,NULL),
 (3,'NS_USER_TALK',1,0,NULL,2,NULL,0,NULL),
 (4,'NS_PROJECT',0,0,NULL,NULL,NULL,0,NULL),
 (5,'NS_PROJECT_TALK',1,0,NULL,4,NULL,0,NULL),
 (6,'NS_FILE',0,0,NULL,NULL,NULL,0,NULL),
 (7,'NS_FILE_TALK',1,0,NULL,6,NULL,0,NULL),
 (8,'NS_MEDIAWIKI',0,0,NULL,NULL,NULL,0,NULL),
 (9,'NS_MEDIAWIKI_TALK',1,0,NULL,8,NULL,0,NULL),
 (10,'NS_TEMPLATE',0,0,NULL,NULL,NULL,0,NULL),
 (11,'NS_TEMPLATE_TALK',1,0,NULL,10,NULL,0,NULL),
 (12,'NS_HELP',0,0,NULL,NULL,NULL,0,NULL),
 (13,'NS_HELP_TALK',1,0,NULL,12,NULL,0,NULL),
 (14,'NS_CATEGORY',0,0,NULL,NULL,NULL,0,NULL),
 (15,'NS_CATEGORY_TALK',1,0,NULL,14,NULL,0,NULL);
/*!40000 ALTER TABLE `namespace` ENABLE KEYS */;


--
-- Definition of table `namespace_names`
--

DROP TABLE IF EXISTS `namespace_names`;
CREATE TABLE `namespace_names` (
  `ns_id` int(8) NOT NULL default '0',
  `ns_name` varchar(200) NOT NULL default '',
  `ns_default` tinyint(1) NOT NULL default '0',
  `ns_canonical` tinyint(1) default NULL,
  UNIQUE KEY `ns_name` (`ns_name`),
  KEY `ns_id` (`ns_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `namespace_names`
--

/*!40000 ALTER TABLE `namespace_names` DISABLE KEYS */;
INSERT INTO `namespace_names` (`ns_id`,`ns_name`,`ns_default`,`ns_canonical`) VALUES 
 (14,'Category',1,0),
 (15,'Category_talk',1,0),
 (6,'File',1,0),
 (7,'File_talk',1,0),
 (12,'Help',1,0),
 (13,'Help_talk',1,0),
 (6,'Image',0,0),
 (7,'Image_talk',0,0),
 (-2,'Media',1,0),
 (8,'MediaWiki',1,0),
 (9,'MediaWiki_talk',1,0),
 (4,'Project',0,1),
 (5,'Project_talk',0,1),
 (6,'Sound',0,0),
 (7,'Sound_talk',0,0),
 (-1,'Special',1,0),
 (1,'Talk',1,0),
 (10,'Template',1,0),
 (11,'Template_talk',1,0),
 (2,'User',1,0),
 (3,'User_talk',1,0),
 (6,'Video',0,0),
 (7,'Video_talk',0,0),
 (4,'Wikdevelop',1,0),
 (5,'Wikdevelop_talk',1,0);
/*!40000 ALTER TABLE `namespace_names` ENABLE KEYS */;


--
-- Definition of table `objectcache`
--

DROP TABLE IF EXISTS `objectcache`;
CREATE TABLE `objectcache` (
  `keyname` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `value` mediumblob,
  `exptime` datetime default NULL,
  UNIQUE KEY `keyname` (`keyname`),
  KEY `exptime` (`exptime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `objects`
--

DROP TABLE IF EXISTS `objects`;
CREATE TABLE `objects` (
  `object_id` int(11) NOT NULL auto_increment,
  `table` varchar(100) collate latin1_general_ci NOT NULL,
  `original_id` int(11) default NULL,
  `UUID` varchar(36) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`object_id`),
  KEY `table` (`table`),
  KEY `original_id` (`original_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `oldimage`
--

DROP TABLE IF EXISTS `oldimage`;
CREATE TABLE `oldimage` (
  `oi_name` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `oi_archive_name` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `oi_size` int(8) unsigned NOT NULL default '0',
  `oi_width` int(5) NOT NULL default '0',
  `oi_height` int(5) NOT NULL default '0',
  `oi_bits` int(3) NOT NULL default '0',
  `oi_description` tinyblob NOT NULL,
  `oi_user` int(5) unsigned NOT NULL default '0',
  `oi_user_text` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `oi_timestamp` varchar(14) character set latin1 collate latin1_bin NOT NULL default '',
  KEY `oi_name` (`oi_name`(10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `page`
--

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `page_id` int(10) unsigned NOT NULL auto_increment,
  `page_namespace` int(11) NOT NULL default '0',
  `page_title` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `page_restrictions` tinyblob,
  `page_counter` bigint(20) unsigned NOT NULL default '0',
  `page_is_redirect` tinyint(1) unsigned NOT NULL default '0',
  `page_is_new` tinyint(1) unsigned NOT NULL default '0',
  `page_random` double unsigned NOT NULL default '0',
  `page_touched` varchar(14) character set latin1 collate latin1_bin NOT NULL default '',
  `page_latest` int(8) unsigned NOT NULL default '0',
  `page_len` int(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`page_id`),
  KEY `page_random` (`page_random`),
  KEY `page_len` (`page_len`),
  KEY `name_title` (`page_namespace`,`page_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `page`
--

/*!40000 ALTER TABLE `page` DISABLE KEYS */;
INSERT INTO `page` (`page_id`,`page_namespace`,`page_title`,`page_restrictions`,`page_counter`,`page_is_redirect`,`page_is_new`,`page_random`,`page_touched`,`page_latest`,`page_len`) VALUES 
 (1,0,0x4D61696E5F50616765,'',0,0,0,0.680286290438,0x3230303730353232313233373130,1,444);
/*!40000 ALTER TABLE `page` ENABLE KEYS */;


--
-- Definition of table `page_restrictions`
--

DROP TABLE IF EXISTS `page_restrictions`;
CREATE TABLE `page_restrictions` (
  `pr_page` int(8) NOT NULL,
  `pr_type` varchar(255) NOT NULL,
  `pr_level` varchar(255) NOT NULL,
  `pr_cascade` tinyint(4) NOT NULL,
  `pr_user` int(8) default NULL,
  `pr_expiry` char(14) character set latin1 collate latin1_bin default NULL,
  PRIMARY KEY  (`pr_page`,`pr_type`),
  KEY `pr_page` (`pr_page`),
  KEY `pr_typelevel` (`pr_type`,`pr_level`),
  KEY `pr_level` (`pr_level`),
  KEY `pr_cascade` (`pr_cascade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `pagelinks`
--

DROP TABLE IF EXISTS `pagelinks`;
CREATE TABLE `pagelinks` (
  `pl_from` int(8) unsigned NOT NULL default '0',
  `pl_namespace` int(11) NOT NULL default '0',
  `pl_title` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  UNIQUE KEY `pl_from` (`pl_from`,`pl_namespace`,`pl_title`),
  KEY `pl_namespace` (`pl_namespace`,`pl_title`,`pl_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `querycache`
--

DROP TABLE IF EXISTS `querycache`;
CREATE TABLE `querycache` (
  `qc_type` char(32) NOT NULL default '',
  `qc_value` int(5) unsigned NOT NULL default '0',
  `qc_namespace` int(11) NOT NULL default '0',
  `qc_title` char(255) character set latin1 collate latin1_bin NOT NULL default '',
  KEY `qc_type` (`qc_type`,`qc_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `querycache_info`
--

DROP TABLE IF EXISTS `querycache_info`;
CREATE TABLE `querycache_info` (
  `qci_type` varchar(32) NOT NULL default '',
  `qci_timestamp` char(14) NOT NULL default '19700101000000',
  UNIQUE KEY `qci_type` (`qci_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `querycachetwo`
--

DROP TABLE IF EXISTS `querycachetwo`;
CREATE TABLE `querycachetwo` (
  `qcc_type` char(32) NOT NULL,
  `qcc_value` int(5) unsigned NOT NULL default '0',
  `qcc_namespace` int(11) NOT NULL default '0',
  `qcc_title` char(255) character set latin1 collate latin1_bin NOT NULL default '',
  `qcc_namespacetwo` int(11) NOT NULL default '0',
  `qcc_titletwo` char(255) character set latin1 collate latin1_bin NOT NULL default '',
  KEY `qcc_type` (`qcc_type`,`qcc_value`),
  KEY `qcc_title` (`qcc_type`,`qcc_namespace`,`qcc_title`),
  KEY `qcc_titletwo` (`qcc_type`,`qcc_namespacetwo`,`qcc_titletwo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `recentchanges`
--

DROP TABLE IF EXISTS `recentchanges`;
CREATE TABLE `recentchanges` (
  `rc_id` int(8) unsigned NOT NULL auto_increment,
  `rc_timestamp` varchar(14) character set latin1 collate latin1_bin NOT NULL default '',
  `rc_cur_time` varchar(14) character set latin1 collate latin1_bin NOT NULL default '',
  `rc_user` int(10) unsigned NOT NULL default '0',
  `rc_user_text` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `rc_namespace` int(11) NOT NULL default '0',
  `rc_title` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `rc_comment` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `rc_minor` tinyint(3) unsigned NOT NULL default '0',
  `rc_bot` tinyint(3) unsigned NOT NULL default '0',
  `rc_new` tinyint(3) unsigned NOT NULL default '0',
  `rc_cur_id` int(10) unsigned NOT NULL default '0',
  `rc_this_oldid` int(10) unsigned NOT NULL default '0',
  `rc_last_oldid` int(10) unsigned NOT NULL default '0',
  `rc_type` tinyint(3) unsigned NOT NULL default '0',
  `rc_moved_to_ns` tinyint(3) unsigned NOT NULL default '0',
  `rc_moved_to_title` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `rc_patrolled` tinyint(3) unsigned NOT NULL default '0',
  `rc_ip` varchar(15) NOT NULL default '',
  `rc_old_len` int(10) default NULL,
  `rc_new_len` int(10) default NULL,
  PRIMARY KEY  (`rc_id`),
  KEY `rc_timestamp` (`rc_timestamp`),
  KEY `rc_namespace_title` (`rc_namespace`,`rc_title`),
  KEY `rc_cur_id` (`rc_cur_id`),
  KEY `new_name_timestamp` (`rc_new`,`rc_namespace`,`rc_timestamp`),
  KEY `rc_ip` (`rc_ip`),
  KEY `rc_ns_usertext` (`rc_namespace`,`rc_user_text`),
  KEY `rc_user_text` (`rc_user_text`,`rc_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `redirect`
--

DROP TABLE IF EXISTS `redirect`;
CREATE TABLE `redirect` (
  `rd_from` int(8) unsigned NOT NULL default '0',
  `rd_namespace` int(11) NOT NULL default '0',
  `rd_title` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  PRIMARY KEY  (`rd_from`),
  KEY `rd_ns_title` (`rd_namespace`,`rd_title`,`rd_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Definition of table `revision`
--

DROP TABLE IF EXISTS `revision`;
CREATE TABLE `revision` (
  `rev_id` int(8) unsigned NOT NULL auto_increment,
  `rev_page` int(8) unsigned NOT NULL default '0',
  `rev_text_id` int(8) unsigned NOT NULL default '0',
  `rev_comment` tinyblob NOT NULL,
  `rev_user` int(5) unsigned NOT NULL default '0',
  `rev_user_text` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `rev_timestamp` varchar(14) character set latin1 collate latin1_bin NOT NULL default '',
  `rev_minor_edit` tinyint(1) unsigned NOT NULL default '0',
  `rev_deleted` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`rev_page`,`rev_id`),
  UNIQUE KEY `rev_id` (`rev_id`),
  KEY `rev_timestamp` (`rev_timestamp`),
  KEY `page_timestamp` (`rev_page`,`rev_timestamp`),
  KEY `user_timestamp` (`rev_user`,`rev_timestamp`),
  KEY `usertext_timestamp` (`rev_user_text`,`rev_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `revision`
--

/*!40000 ALTER TABLE `revision` DISABLE KEYS */;
INSERT INTO `revision` (`rev_id`,`rev_page`,`rev_text_id`,`rev_comment`,`rev_user`,`rev_user_text`,`rev_timestamp`,`rev_minor_edit`,`rev_deleted`) VALUES 
 (1,1,1,'',0,0x4D6564696157696B692064656661756C74,0x3230303730353232313233373130,0,0);
/*!40000 ALTER TABLE `revision` ENABLE KEYS */;


--
-- Definition of table `searchindex`
--

DROP TABLE IF EXISTS `searchindex`;
CREATE TABLE `searchindex` (
  `si_page` int(8) unsigned NOT NULL default '0',
  `si_title` varchar(255) NOT NULL default '',
  `si_text` mediumtext NOT NULL,
  UNIQUE KEY `si_page` (`si_page`),
  FULLTEXT KEY `si_title` (`si_title`),
  FULLTEXT KEY `si_text` (`si_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Definition of table `site_stats`
--

DROP TABLE IF EXISTS `site_stats`;
CREATE TABLE `site_stats` (
  `ss_row_id` int(8) unsigned NOT NULL default '0',
  `ss_total_views` bigint(20) unsigned default '0',
  `ss_total_edits` bigint(20) unsigned default '0',
  `ss_good_articles` bigint(20) unsigned default '0',
  `ss_total_pages` bigint(20) default '-1',
  `ss_users` bigint(20) default '-1',
  `ss_admins` int(10) default '-1',
  `ss_images` int(10) default '0',
  UNIQUE KEY `ss_row_id` (`ss_row_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `site_stats`
--

/*!40000 ALTER TABLE `site_stats` DISABLE KEYS */;
INSERT INTO `site_stats` (`ss_row_id`,`ss_total_views`,`ss_total_edits`,`ss_good_articles`,`ss_total_pages`,`ss_users`,`ss_admins`,`ss_images`) VALUES 
 (1,0,0,0,-1,-1,-1,0);
/*!40000 ALTER TABLE `site_stats` ENABLE KEYS */;


--
-- Definition of table `templatelinks`
--

DROP TABLE IF EXISTS `templatelinks`;
CREATE TABLE `templatelinks` (
  `tl_from` int(8) unsigned NOT NULL default '0',
  `tl_namespace` int(11) NOT NULL default '0',
  `tl_title` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  UNIQUE KEY `tl_from` (`tl_from`,`tl_namespace`,`tl_title`),
  KEY `tl_namespace` (`tl_namespace`,`tl_title`,`tl_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `text`
--

DROP TABLE IF EXISTS `text`;
CREATE TABLE `text` (
  `old_id` int(8) unsigned NOT NULL auto_increment,
  `old_text` mediumblob NOT NULL,
  `old_flags` tinyblob,
  PRIMARY KEY  (`old_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `text`
--

/*!40000 ALTER TABLE `text` DISABLE KEYS */;
INSERT INTO `text` (`old_id`,`old_text`,`old_flags`) VALUES 
 (1,0x3C6269673E2727274D6564696157696B6920686173206265656E207375636365737366756C6C7920696E7374616C6C65642E2727273C2F6269673E0A0A436F6E73756C7420746865205B687474703A2F2F6D6574612E77696B696D656469612E6F72672F77696B692F48656C703A436F6E74656E7473205573657227732047756964655D20666F7220696E666F726D6174696F6E206F6E207573696E67207468652077696B6920736F6674776172652E0A0A3D3D2047657474696E672073746172746564203D3D0A0A2A205B687474703A2F2F7777772E6D6564696177696B692E6F72672F77696B692F48656C703A436F6E66696775726174696F6E5F73657474696E677320436F6E66696775726174696F6E2073657474696E6773206C6973745D0A2A205B687474703A2F2F7777772E6D6564696177696B692E6F72672F77696B692F48656C703A464151204D6564696157696B69204641515D0A2A205B687474703A2F2F6D61696C2E77696B696D656469612E6F72672F6D61696C6D616E2F6C697374696E666F2F6D6564696177696B692D616E6E6F756E6365204D6564696157696B692072656C65617365206D61696C696E67206C6973745D,0x7574662D38);
/*!40000 ALTER TABLE `text` ENABLE KEYS */;


--
-- Definition of table `trackbacks`
--

DROP TABLE IF EXISTS `trackbacks`;
CREATE TABLE `trackbacks` (
  `tb_id` int(11) NOT NULL default '0',
  `tb_page` int(11) default NULL,
  `tb_title` varchar(255) NOT NULL default '',
  `tb_url` varchar(255) NOT NULL default '',
  `tb_ex` text,
  `tb_name` varchar(255) default NULL,
  PRIMARY KEY  (`tb_id`),
  KEY `tb_page` (`tb_page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `transcache`
--

DROP TABLE IF EXISTS `transcache`;
CREATE TABLE `transcache` (
  `tc_url` varchar(255) NOT NULL default '',
  `tc_contents` text,
  `tc_time` int(11) NOT NULL default '0',
  UNIQUE KEY `tc_url_idx` (`tc_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(8) unsigned NOT NULL auto_increment,
  `user_name` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `user_real_name` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `user_password` tinyblob NOT NULL,
  `user_newpassword` tinyblob NOT NULL,
  `user_email` tinytext NOT NULL,
  `user_options` blob NOT NULL,
  `user_touched` varchar(14) character set latin1 collate latin1_bin NOT NULL default '',
  `user_token` varchar(32) character set latin1 collate latin1_bin NOT NULL default '',
  `user_email_authenticated` varchar(14) character set latin1 collate latin1_bin default NULL,
  `user_email_token` varchar(32) character set latin1 collate latin1_bin default NULL,
  `user_email_token_expires` varchar(14) character set latin1 collate latin1_bin default NULL,
  `user_registration` char(14) character set latin1 collate latin1_bin default NULL,
  `user_newpass_time` char(14) character set latin1 collate latin1_bin default NULL,
  `user_editcount` int(11) default NULL,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  KEY `user_email_token` (`user_email_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`user_id`,`user_name`,`user_real_name`,`user_password`,`user_newpassword`,`user_newpass_time`,`user_email`,`user_options`,`user_touched`,`user_token`,`user_email_authenticated`,`user_email_token`,`user_email_token_expires`,`user_registration`,`user_editcount`) VALUES 
 (1,0x526F6F74,'',0x3935363061356465306437653065356130303661656438326163356466303266,'',NULL,'',0x717569636B6261723D310A756E6465726C696E653D320A636F6C733D38300A726F77733D32350A7365617263686C696D69743D32300A636F6E746578746C696E65733D350A636F6E7465787463686172733D35300A736B696E3D0A6D6174683D310A7263646179733D370A72636C696D69743D35300A776C6C696D69743D3235300A686967686C6967687462726F6B656E3D310A737475627468726573686F6C643D300A707265766965776F6E746F703D310A6564697473656374696F6E3D310A6564697473656374696F6E6F6E7269676874636C69636B3D300A73686F77746F633D310A73686F77746F6F6C6261723D310A646174653D64656661756C740A696D61676573697A653D320A7468756D6273697A653D320A72656D656D62657270617373776F72643D300A656E6F74696677617463686C69737470616765733D300A656E6F7469667573657274616C6B70616765733D310A656E6F7469666D696E6F7265646974733D300A656E6F74696672657665616C616464723D300A73686F776E756D626572737761746368696E673D310A66616E63797369673D300A65787465726E616C656469746F723D300A65787465726E616C646966663D300A73686F776A756D706C696E6B733D310A6E756D62657268656164696E67733D300A7573656C697665707265766965773D300A77617463686C697374646179733D330A76617269616E743D656E0A6C616E67756167653D656E0A7365617263684E73303D31,0x3230303730353232313233373134,0x3230663162623563626335336561633831653831633639313135623236383462,NULL,NULL,NULL,0x3230303730353232313233373039,0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;


--
-- Definition of table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE `user_groups` (
  `ug_user` int(5) unsigned NOT NULL default '0',
  `ug_group` char(16) NOT NULL default '',
  PRIMARY KEY  (`ug_user`,`ug_group`),
  KEY `ug_group` (`ug_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_groups`
--

/*!40000 ALTER TABLE `user_groups` DISABLE KEYS */;
INSERT INTO `user_groups` (`ug_user`,`ug_group`) VALUES 
 (1,'bureaucrat'),
 (1,'sysop');
/*!40000 ALTER TABLE `user_groups` ENABLE KEYS */;


--
-- Definition of table `user_newtalk`
--

DROP TABLE IF EXISTS `user_newtalk`;
CREATE TABLE `user_newtalk` (
  `user_id` int(5) NOT NULL default '0',
  `user_ip` varchar(40) NOT NULL default '',
  KEY `user_id` (`user_id`),
  KEY `user_ip` (`user_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `watchlist`
--

DROP TABLE IF EXISTS `watchlist`;
CREATE TABLE `watchlist` (
  `wl_user` int(5) unsigned NOT NULL default '0',
  `wl_namespace` int(11) NOT NULL default '0',
  `wl_title` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `wl_notificationtimestamp` varchar(14) character set latin1 collate latin1_bin default NULL,
  UNIQUE KEY `wl_user` (`wl_user`,`wl_namespace`,`wl_title`),
  KEY `namespace_title` (`wl_namespace`,`wl_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
