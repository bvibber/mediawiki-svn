<?

# Function to build and initialize a new database.  Must be
# included in a script that also includes Setup.php.  See
# convertdb.php, for example.

function buildTables()
{
	print "Creating new tables.\n";

	$sql = "DROP TABLE IF EXISTS user";
	wfQuery( $sql );

	$sql = "CREATE TABLE user (
  user_id int(5) unsigned NOT NULL auto_increment,
  user_name varchar(255) binary NOT NULL default '',
  user_rights tinyblob NOT NULL default '',
  user_password tinyblob NOT NULL default '',
  user_newpassword tinyblob NOT NULL default '',
  user_email tinytext NOT NULL default '',
  user_options blob NOT NULL default '',
  user_watch mediumblob NOT NULL default '',
  user_newtalk tinyint(1) unsigned NOT NULL default '1',
  UNIQUE KEY user_id (user_id)
) TYPE=MyISAM PACK_KEYS=1";
	wfQuery( $sql );

	$sql = "DROP TABLE IF EXISTS cur";
	wfQuery( $sql );

	$sql = "CREATE TABLE cur (
  cur_id int(8) unsigned NOT NULL auto_increment,
  cur_namespace tinyint(2) unsigned NOT NULL default '0',
  cur_title varchar(255) binary NOT NULL default '',
  cur_text mediumtext NOT NULL default '',
  cur_comment tinyblob NOT NULL default '',
  cur_user int(5) unsigned NOT NULL default '0',
  cur_user_text varchar(255) binary NOT NULL default '',
  cur_timestamp char(14) binary NOT NULL default '',
  cur_restrictions tinyblob NOT NULL default '',
  cur_counter bigint(20) unsigned NOT NULL default '0',
  cur_ind_title varchar(255) NOT NULL default '',
  cur_ind_text mediumtext NOT NULL default '',
  cur_is_redirect tinyint(1) unsigned NOT NULL default '0',
  cur_minor_edit tinyint(1) unsigned NOT NULL default '0',
  cur_is_new tinyint(1) unsigned NOT NULL default '0',
  UNIQUE KEY cur_id (cur_id)
) TYPE=MyISAM PACK_KEYS=1";
	wfQuery( $sql );

	$sql = "DROP TABLE IF EXISTS old";
	wfQuery( $sql );

	$sql = "CREATE TABLE old (
  old_id int(8) unsigned NOT NULL auto_increment,
  old_namespace tinyint(2) unsigned NOT NULL default '0',
  old_title varchar(255) binary NOT NULL default '',
  old_text mediumtext NOT NULL default '',
  old_comment tinyblob NOT NULL default '',
  old_user int(5) unsigned NOT NULL default '0',
  old_user_text varchar(255) binary NOT NULL,
  old_timestamp char(14) binary NOT NULL default '',
  old_minor_edit tinyint(1) NOT NULL default '0',
  old_flags tinyblob NOT NULL default '',
  UNIQUE KEY old_id (old_id)
) TYPE=MyISAM PACK_KEYS=1";
	wfQuery( $sql );

	$sql = "DROP TABLE IF EXISTS archive";
	wfQuery( $sql );

	$sql = "CREATE TABLE archive (
  ar_namespace tinyint(2) unsigned NOT NULL default '0',
  ar_title varchar(255) binary NOT NULL default '',
  ar_text mediumtext NOT NULL default '',
  ar_comment tinyblob NOT NULL default '',
  ar_user int(5) unsigned NOT NULL default '0',
  ar_user_text varchar(255) binary NOT NULL,
  ar_timestamp char(14) binary NOT NULL default '',
  ar_minor_edit tinyint(1) NOT NULL default '0',
  ar_flags tinyblob NOT NULL default ''
) TYPE=MyISAM PACK_KEYS=1";
	wfQuery( $sql );

	$sql = "DROP TABLE IF EXISTS links";
	wfQuery( $sql );

	$sql = "CREATE TABLE links (
  l_from varchar(255) binary NOT NULL default '',
  l_to int(8) unsigned NOT NULL default '0'
) TYPE=MyISAM";
	wfQuery( $sql );

	$sql = "DROP TABLE IF EXISTS brokenlinks";
	wfQuery( $sql );

	$sql = "CREATE TABLE brokenlinks (
  bl_from int(8) unsigned NOT NULL default '0',
  bl_to varchar(255) binary NOT NULL default ''
) TYPE=MyISAM";
	wfQuery( $sql );

	$sql = "DROP TABLE IF EXISTS imagelinks";
	wfQuery( $sql );

	$sql = "CREATE TABLE imagelinks (
  il_from varchar(255) binary NOT NULL default '',
  il_to varchar(255) binary NOT NULL default ''
) TYPE=MyISAM";
	wfQuery( $sql );

	$sql = "DROP TABLE IF EXISTS site_stats";
	wfQuery( $sql );

	$sql = "CREATE TABLE site_stats (
  ss_row_id int(8) unsigned NOT NULL,
  ss_total_views bigint(20) unsigned default '0',
  ss_total_edits bigint(20) unsigned default '0',
  ss_good_articles bigint(20) unsigned default '0',
  UNIQUE KEY ss_row_id (ss_row_id)
) TYPE=MyISAM";
	wfQuery( $sql );

	$sql = "DROP TABLE IF EXISTS ipblocks";
	wfQuery( $sql );

	$sql = "CREATE TABLE ipblocks (
  ipb_address varchar(40) binary NOT NULL default '',
  ipb_user int(8) unsigned NOT NULL default '0',
  ipb_by int(8) unsigned NOT NULL default '0',
  ipb_reason tinyblob NOT NULL default '',
  ipb_timestamp char(14) binary NOT NULL default ''
) TYPE=MyISAM PACK_KEYS=1";
	wfQuery( $sql );

	$sql = "DROP TABLE IF EXISTS image";
	wfQuery( $sql );

	$sql = "CREATE TABLE image (
  img_name varchar(255) binary NOT NULL default '',
  img_size int(8) unsigned NOT NULL default '0',
  img_description tinyblob NOT NULL default '',
  img_user int(5) unsigned NOT NULL default '0',
  img_user_text varchar(255) binary NOT NULL default '',
  img_timestamp char(14) binary NOT NULL default ''
) TYPE=MyISAM PACK_KEYS=1";
	wfQuery( $sql );

	$sql = "DROP TABLE IF EXISTS oldimage";
	wfQuery( $sql );

	$sql = "CREATE TABLE oldimage (
  oi_name varchar(255) binary NOT NULL default '',
  oi_archive_name varchar(255) binary NOT NULL default '',
  oi_size int(8) unsigned NOT NULL default 0,
  oi_description tinyblob NOT NULL default '',
  oi_user int(5) unsigned NOT NULL default '0',
  oi_user_text varchar(255) binary NOT NULL default '',
  oi_timestamp char(14) binary NOT NULL default ''
) TYPE=MyISAM PACK_KEYS=1";
	wfQuery( $sql );

	$sql = "DROP TABLE IF EXISTS random";
	wfQuery( $sql );

	$sql = "CREATE TABLE random (
  ra_current tinyint(1) unsigned not null default 0,
  ra_title varchar(255) binary not null default ''
) TYPE=MyISAM PACK_KEYS=1";
	wfQuery( $sql );

	$sql = "DROP TABLE IF EXISTS recentchanges";
	wfQuery( $sql );

	$sql = "CREATE TABLE recentchanges (
  rc_timestamp varchar(14) binary NOT NULL default '',
  rc_cur_time varchar(14) binary NOT NULL default '',
  rc_user int(10) unsigned NOT NULL default '0',
  rc_user_text varchar(255) binary NOT NULL default '',
  rc_namespace tinyint(3) unsigned NOT NULL default '0',
  rc_title varchar(255) binary NOT NULL default '',
  rc_comment varchar(255) binary NOT NULL default '',
  rc_minor tinyint(3) unsigned NOT NULL default '0',
  rc_new tinyint(3) unsigned NOT NULL default '0',
  rc_cur_id int(10) unsigned NOT NULL default '0',
  rc_this_oldid int(10) unsigned NOT NULL default '0',
  rc_last_oldid int(10) unsigned NOT NULL default '0'
) TYPE=MyISAM PACK_KEYS=1;";
	wfQuery( $sql );
}

function buildIndexes()
{
	print "Building indexes.\n";

	$sql = "ALTER TABLE user
  ADD INDEX user_name (user_name(10))";
	wfQuery( $sql );

	$sql = "ALTER TABLE cur
  ADD INDEX cur_namespace (cur_namespace),
  ADD INDEX cur_title (cur_title(20)),
  ADD INDEX cur_timestamp (cur_timestamp),
  ADD FULLTEXT cur_ind_title (cur_ind_title),
  ADD FULLTEXT cur_ind_text (cur_ind_text)";
	wfQuery( $sql );

	$sql = "ALTER TABLE old
  ADD INDEX old_title (old_title(20)),
  ADD INDEX old_timestamp (old_timestamp)";
	wfQuery( $sql );

	$sql = "ALTER TABLE links
  ADD INDEX l_from (l_from (10)),
  ADD INDEX l_to (l_to)";
	wfQuery( $sql );

	$sql = "ALTER TABLE brokenlinks
  ADD INDEX bl_from (bl_from),
  ADD INDEX bl_to (bl_to(10))";
	wfQuery( $sql );

	$sql = "ALTER TABLE imagelinks
  ADD INDEX il_from (il_from(10)),
  ADD INDEX il_to (il_to(10))";
	wfQuery( $sql );

	$sql = "ALTER TABLE ipblocks
  ADD INDEX ipb_address (ipb_address),
  ADD INDEX ipb_user (ipb_user)";
	wfQuery( $sql );

	$sql = "ALTER TABLE image
  ADD INDEX img_name (img_name(10)),
  ADD INDEX img_size (img_size),
  ADD INDEX img_timestamp (img_timestamp)";
	wfQuery( $sql );

	$sql = "ALTER TABLE oldimage
  ADD INDEX oi_name (oi_name(10))";
	wfQuery( $sql );
}

function initializeTables()
{
	$sql = "INSERT INTO site_stats (ss_row_id,ss_total_views," .
	  "ss_total_edits,ss_good_articles) VALUES (1,0,0,0)";
	wfQuery( $sql );

	$wns = Namespace::getWikipedia();
	$ulp = wfMsg( "uploadlogpage" );
	$dlp = wfMsg( "dellogpage" );

	$sql = "INSERT INTO cur (cur_namespace,cur_title,cur_text," .
	  "cur_restrictions) VALUES ({$wns},'{$ulp}','" .
	  wfStrencode( wfMsg( "uploadlogpagetext" ) ) . "','sysop')";
	wfQuery( $sql );

	$sql = "INSERT INTO cur (cur_namespace,cur_title,cur_text," .
	  "cur_restrictions) VALUES ({$wns},'{$dlp}','" .
	  wfStrencode( wfMsg( "dellogpagetext" ) ) . "','sysop')";
	wfQuery( $sql );

	$mp = wfStrencode( wfMsg( "mainpage" ) );
	$sql = "INSERT INTO cur (cur_namespace,cur_title,cur_text) " .
	  "VALUES (0,'{$mp}','Put main page text here.')";
	wfQuery( $sql );

	$sql = "INSERT INTO random(ra_current,ra_title) SELECT 1, cur_title " .
	  "FROM cur WHERE cur_namespace=0 AND cur_is_redirect=0 " .
	  "ORDER BY RAND() LIMIT 1000";
	wfQuery( $sql );
}

?>
