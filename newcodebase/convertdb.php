<?

# Database conversion (from May 2002 format).  Assumes that
# the "buildtables.sql" script has been run to create the new
# empty tables, and that the old tables have been read in from
# a database dump, renamed "old_*".

include_once( "Setup.php" );
include_once( "./rebuildlinksfunction.php" );
set_time_limit(0);

$wgDBname			= "wikidb";
$wgDBuser			= "wikiadmin";
$wgDBpassword		= "adminpasswd";
$wgImageDirectory	= "/usr/local/apache/htdocs/wikiimages";

renameOldTables();
buildtables();

convertUserTable();
convertOldTable();
convertCurTable();
# convertImageDirectories();

buildindexes();
rebuildLinkTablesPass1();
rebuildLinkTablesPass2();
removeOldTables();

print "Done.\n";
exit();

########## End of script, beginning of functions.

function convertUserTable()
{
	$count = 0;
	print "Converting USER table.\n";

	$sql = "LOCK TABLES old_user READ, user WRITE";
	$newres = wfQuery( $sql );

	$sql = "SELECT user_id,user_name,user_rights,user_password," .
	  "user_email,user_options,user_watch FROM old_user";
	$oldres = wfQuery( $sql );

	$sql = "DELETE FROM user";
	$newres = wfQuery( $sql );

	$sql = "";
	while ( $row = mysql_fetch_object( $oldres ) ) {
		if ( 0 == ( $count % 10 ) ) {
			if ( 0 != $count ) { $newres = wfQuery( $sql ); }

			$sql = "INSERT INTO user (user_id,user_name,user_rights," .
			  "user_password,user_newpassword,user_email,user_options," .
			  "user_watch) VALUES ";
		} else {
			$sql .= ",";
		}
		$ops = addslashes( fixUserOptions( $row->user_options ) );
		$name = addslashes( fixUserName( $row->user_name ) );
		$rights = addslashes( fixUserRights( $row->user_rights ) );
		$email = addslashes( $row->user_email );
		$pwd = addslashes( md5( $row->user_password ) );
		$watch = addslashes( $row->user_watch );

		if ( "" == $name ) continue; # Don't convert illegal names

		$sql .= "({$row->user_id},'{$name}','{$rights}','{$pwd}',''," .
		  "'{$email}','{$ops}','{$watch}')";

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count user records processed.\n";
		}
	}
	if ( $sql ) { $newres = wfQuery( $sql ); }

	print "$count user records processed.\n";
	mysql_free_result( $oldres );

	$sql = "UNLOCK TABLES";
	$newres = wfQuery( $sql );
}

# Convert May 2002 version of database into new format.
#
function convertCurTable()
{
	$count = 0;
    $countables = 0;
	print "Converting CUR table.\n";

	$sql = "LOCK TABLES old_cur READ, cur WRITE";
	$newres = wfQuery( $sql );

	$sql = "SELECT cur_id,cur_title,cur_text,cur_comment,cur_user," .
	  "cur_timestamp,cur_minor_edit,cur_restrictions," .
	  "cur_counter,cur_ind_title,cur_user_text FROM old_cur";
	$oldres = wfQuery( $sql );

	$sql = "DELETE FROM cur";
	$newres = wfQuery( $sql );

	$sql = "";
	while ( $row = mysql_fetch_object( $oldres ) ) {
		$nt = Title::newFromDBkey( $row->cur_title );
		$title = addslashes( $nt->getDBkey() );
		$ns = $nt->getNamespace();
		$text = addslashes( convertMediaLinks( $row->cur_text ) );

		$ititle = addslashes( indexTitle( $nt->getText() ) );
		$itext = addslashes( indexText( $text, $ititle ) );

		$com = addslashes( $row->cur_comment );
		$cr = addslashes( $row->cur_restrictions );
		$cut = addslashes( $row->cur_user_text );
		if ( "" == $cut ) { $cut = "Unknown"; }

		if ( 2 == $row->cur_minor_edit ) { $isnew = 1; }
		else { $isnew = 0; }
		if ( 0 != $row->cur_minor_edit ) { $isme = 1; }
		else { $isme = 0; }

		# $counter = $row->cur_counter;
		# if ( ! $counter ) { $counter = 0; }

		if ( preg_match( "/^#redirect /i", $text ) ) {
			$redir = 1;
			$text = fixRedirect( $text );
		} else { $redir = 0; }

		$sql = "INSERT INTO cur (cur_id,cur_namespace," .
		  "cur_title,cur_text,cur_comment,cur_user," .
		  "cur_timestamp,cur_minor_edit,cur_is_new," .
		  "cur_restrictions,cur_counter,cur_ind_title," .
		  "cur_ind_text,cur_is_redirect,cur_user_text) VALUES ";
		$sql .= "({$row->cur_id},{$ns},'{$title}','{$text}'," .
		  "'{$com}',{$row->cur_user},'{$row->cur_timestamp}'," .
		  "{$isme},{$isnew},'{$cr}',0,'{$ititle}','{$itext}'," .
		  "{$redir},'{$cut}')";
		wfQuery( $sql );

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count article records processed.\n";
		}
		if ( 0 != $ns ) { continue; }
		if ( 0 != $redir ) { continue; }
		if ( false === strstr( $text, "," ) ) { continue; }
		++$countables;
	}
	print "$count article records processed.\n";
	mysql_free_result( $oldres );

	$sql = "UNLOCK TABLES";
	$newres = wfQuery( $sql );

	$sql = "UPDATE site_stats SET ss_good_articles={$countables} " .
	  "WHERE ss_row_id=1";
	wfQuery( $sql );
}

# Convert May 2002 version of database into new format.
#
function convertOldTable()
{
	$count = 0;
	print "Converting OLD table.\n";

	$sql = "LOCK TABLES old_old READ, old WRITE";
	$newres = wfQuery( $sql );

	$sql = "SELECT old_id,old_title,old_text,old_comment,old_user," .
	  "old_timestamp,old_minor_edit,old_user_text FROM old_old";
	$oldres = wfQuery( $sql );

	$sql = "DELETE FROM old";
	$newres = wfQuery( $sql );

	while ( $row = mysql_fetch_object( $oldres ) ) {
		$nt = Title::newFromDBkey( $row->old_title );
		$title = addslashes( $nt->getDBkey() );
		$ns = $nt->getNamespace();
		$text = addslashes( convertMediaLinks( $row->old_text ) );

		$com = addslashes( $row->old_comment );
		$cut = addslashes( $row->old_user_text );
		if ( "" == $cut ) { $cut = "Unknown"; }

		if ( 0 != $row->old_minor_edit ) { $isme = 1; }
		else { $isme = 0; }

		if ( preg_match( "/^#redirect /i", $text ) ) {
			$redir = 1;
			$text = fixRedirect( $text );
		} else { $redir = 0; }

		$sql = "INSERT INTO old (old_id,old_namespace,old_title," .
		  "old_text,old_comment,old_user," .
		  "old_timestamp,old_minor_edit,old_user_text) VALUES ";
		$sql .= "({$row->old_id},{$ns},'{$title}','{$text}'," .
		  "'{$com}',{$row->old_user},'{$row->old_timestamp}'," .
		  "{$isme},'{$cut}')";
		wfQuery( $sql );

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count history records processed.\n";
		}
	}
	print "$count history records processed.\n";
	mysql_free_result( $oldres );

	$sql = "UNLOCK TABLES";
	$newres = wfQuery( $sql );
}

function convertImageDirectories()
{
	global $wgImageDirectory, $wgUploadDirectory;
	$count = 0;

	print "Moving image files.\n";
	$dir = opendir( $wgImageDirectory );
	while ( false !== ( $oname = readdir( $dir ) ) ) {
		if ( "." == $oname{0} ) continue;

		$nt = Title::newFromText( $oname );
		$nname = $nt->getDBkey();

		$exts = array( "png", "gif", "jpg", "jpeg" );
		$ext = strrchr( $nname, "." );
		if ( false === $ext ) { $ext = ""; }
		else { $ext = strtolower( substr( $ext, 1 ) ); }
		if ( ! in_array( $ext, $exts ) ) {
			print "Skipping \"{$oname}\"\n";
			continue;
		}
		$oldumask = umask(0);
		$hash = md5( $nname );
		$dest = $wgUploadDirectory . "/" . $hash{0};
		if ( ! is_dir( $dest ) ) { mkdir( $dest, 0777 ); }
		$dest .= "/" . substr( $hash, 0, 2 );
		if ( ! is_dir( $dest ) ) { mkdir( $dest, 0777 ); }
		umask( $oldumask );

		print "{$wgImageDirectory}/{$oname} => {$dest}/{$nname}\n";

		if ( copy( "{$wgImageDirectory}/{$oname}", "{$dest}/{$nname}" ) ) {
			++$count;

			$sql = "DELETE FROM image WHERE img_name='" .
			  addslashes( $nname ) . "'";
			$res = wfQuery( $sql );

			$sql = "INSERT INTO image (img_name,img_timestamp,img_user," .
			  "img_user_text,img_size,img_description) VALUES ('" .
			  addslashes( $nname ) . "','" .
			  date( "YmdHis" ) . "',0,'(Automated conversion)','" .
			  filesize( "{$dest}/{$nname}" ) . "','')";
			$res = wfQuery( $sql );
		}
	}
	print "{$count} images moved.\n";
}

# Utility functions for the above.
#
function convertMediaLinks( $text )
{
	$text = preg_replace(
	  "/(^|[^[])http:\/\/(www.|)wikipedia.com\/upload\/" .
	  "([a-zA-Z0-9_:.~\%\-]+)\.(png|PNG|jpg|JPG|jpeg|JPEG|gif|GIF)/",
	  "\\1[[image:\\3.\\4]]", $text );
	$text = preg_replace(
	  "/(^|[^[])http:\/\/(www.|)wikipedia.com\/images\/uploads\/" .
	  "([a-zA-Z0-9_:.~\%\-]+)\.(png|PNG|jpg|JPG|jpeg|JPEG|gif|GIF)/",
	  "\\1[[image:\\3.\\4]]", $text );

	$text = preg_replace(
	  "/(^|[^[])http:\/\/(www.|)wikipedia.com\/upload\/" .
	  "([a-zA-Z0-9_:.~\%\-]+)/", "\\1[[media:\\3]]", $text );
	$text = preg_replace(
	  "/(^|[^[])http:\/\/(www.|)wikipedia.com\/images\/uploads\/" .
	  "([a-zA-Z0-9_:.~\%\-]+)/", "\\1[[media:\\3]]", $text );

	return $text;
}

function fixRedirect( $text )
{
	$tc = "[&;%\\-,.\\(\\)' _0-9A-Za-z\\/:\\xA0-\\xff]";
	$re = "#redirect";
	if ( preg_match( "/^{$re}\\s*\\[{$tc}+\\]/i", $text ) ) {
		$text = preg_replace( "/^({$re})\\s*\\[\\s*({$tc}+)\\]/i",
		  "\\1 [[\\2]]", $text, 1 );
	} else if ( preg_match( "/^{$re}\\s+{$tc}+/i", $text ) ) {
		$text = preg_replace( "/^({$re})\\s+({$tc}+)/i",
		  "\\1 [[\\2]]", $text, 1 );
	}
	return $text;
}

function fixUserOptions( $in )
{
	$s = urldecode( $in );
	$a = explode( "\n", $s );

	foreach ( $a as $l ) {
		if ( preg_match( "/^([A-Za-z0-9_]+)=(.*)/", $l, $m ) ) {
			$ops[$m[1]] = $m[2];
		}
	}
	$nops = array();

	$q = strtolower( $ops["quickBar"] );
	if ( $q == "none" ) { $q = 0; }
	else if ( $q == "left" ) { $q = 1; }
	else { $q = 2; }
	$nops["quickbar"] = $q;

	if ( $ops["markupNewTopics"] == "inverse" ) {
		$nops["highlightbroken"] = 1;
	}
	$sk = substr( strtolower( $ops["skin"] ), 0, 4 );
	if ( "star" == $sk ) { $sk = 0; }
	else if ( "nost" == $sk ) { $sk = 1; }
	else if ( "colo" == $sk ) { $sk = 2; }
	else { $sk = 0; }
	$nops["skin"] = $sk;

	$u = strtolower( $ops["underlineLinks"] );
	if ( "yes" == $u || "on" == $u ) { $nops["underline"] = 1; }
	else { $nops["underline"] = 0; }

	$j = strtolower( $ops["justify"] );
	if ( "yes" == $j || "on" == $j ) { $nops["justify"] = 1; }
	$n = strtolower( $ops["numberHeadings"] );
	if ( "yes" == $n || "on" == $n ) { $nops["numberheadings"] = 1; }
	$h = strtolower( $ops["hideMinor"] );
	if ( "yes" == $h || "on" == $h ) { $nops["hideminor"] = 1; }
	$r = strtolower( $ops["rememberPassword"] );
	if ( "yes" == $r || "on" == $r ) { $nops["rememberpassword"] = 1; }
	$s = strtolower( $ops["showHover"] );
	if ( "yes" == $s || "on" == $s ) { $nops["hover"] = 1; }

	$c = $ops["cols"];
	if ( $c < 20 || c > 200 ) { $nops["cols"] = 80; }
	else { $nops["cols"] = $c; }
	$r = $ops["rows"];
	if ( $r < 5 || $r > 100 ) { $nops["rows"] = 20; }
	else { $nops["rows"] = $r; }
	$r = $ops["resultsPerPage"];
	if ( $r < 3 || $r > 500 ) { $nops["searchlimit"] = 20; }
	else { $nops["searchlimit"] = $r; }
	$r = $ops["viewRecentChanges"];
	if ( $r < 10 || $r > 1000 ) { $nops["rclimit"] = 50; }
	else { $nops["rclimit"] = $r; }
	$nops["rcdays"] = 3;

	$a = array();
	foreach ( $nops as $oname => $oval ) {
		array_push( $a, "$oname=$oval" );
	}
	$s = implode( "\n", $a );
	return $s;
}

function fixUserRights( $in )
{
	$a = explode( ",", $in );
	$b = array();
	foreach ( $a as $r ) {
		if ( "is_developer" == strtolower( trim( $r ) ) ) {
			array_push( $b, "developer" );
		} else if ( "is_sysop" == strtolower( trim( $r ) ) ) {
			array_push( $b, "sysop" );
		}
	}
	$out = implode( ",", $b );
	return $out;
}

function fixUserName( $in )
{
	$lc = "-,.()' _0-9A-Za-z\\/:\\xA0-\\xFF";
	$out = preg_replace( "/[^{$lc}]/", "", $in );
	$out = ucfirst( trim( str_replace( "_", " ", $out ) ) );
	return $out;
}

function indexTitle( $in )
{
	$lc = "A-Za-z_'0-9&#;\\x90-\\xFF\\-";
	$t = preg_replace( "/[^{$lc}]+/", " ", $in );
	$t = preg_replace( "/\\b[{$lc}][{$lc}]\\b/", " ", $t );
	$t = preg_replace( "/\\b[{$lc}]\\b/", " ", $t );
	$t = preg_replace( "/\\s+/", " ", $t );
	return $t;
}

function indexText( $text, $ititle )
{
	$lc = "A-Za-z_'0-9&#;\\x90-\\xFF\\-";
	$titlewords = array();
	$words = explode( " ", strtolower( trim( $ititle ) ) );
	foreach ( $words as $w ) { $titlewords[$w] = 1; }

	$text = preg_replace( "/<\\/?\\s*[A-Za-z][A-Za-z0-9]*\\s*([^>]*?)>/",
	  " ", strtolower( $this->mText ) ); # Strip HTML markup
	$text = preg_replace( "/(^|\\n)\\s*==\\s+([^\\n]+)\\s+==\\s/sD",
	  "\\2 \\2 \\2 ", $text ); # Emphasize headings

	# Strip external URLs
	$uc = "A-Za-z0-9_\\/:.,~%\\-+&;#?!=()@\\xA0-\\xFF";
	$protos = "http|https|ftp|mailto|news|gopher";
	$pat = "/(^|[^\\[])({$protos}):[{$uc}]+([^{$uc}]|$)/";
	$text = preg_replace( $pat, "\\1 \\3", $text );

	$p1 = "/(^|[^\\[])\\[({$protos}):[{$uc}]+]/";
	$p2 = "/(^|[^\\[])\\[({$protos}):[{$uc}]+\\s+([^\\]]+)]/";
	$text = preg_replace( $p1, "\\1 ", $text );
	$text = preg_replace( $p2, "\\1 \\3 ", $text );

	# Internal image links
	$pat2 = "/\\[\\[image:([{$uc}]+)\\.(png|jpg|jpeg)([^{$uc}])/i";
	$text = preg_replace( $pat2, " \\1 \\3", $text );

	$text = preg_replace( "/([^{$lc}])([{$lc}]+)]]([a-z]+)/",
	  "\\1\\2 \\2\\3", $text ); # Handle [[game]]s

	# Strip all remaining non-search characters
	$text = preg_replace( "/[^{$lc}]+/", " ", $text );

	# Handle 's, s'
	$text = preg_replace( "/([{$lc}]+)'s /", "\\1 \\1's ", $text );
	$text = preg_replace( "/([{$lc}]+)s' /", "\\1s ", $text );

	# Strip 1- and 2-letter words
	$text = preg_replace( "/(^|[^{$lc}])[{$lc}][{$lc}]([^{$lc}]|$)/",
	  "\\1 \\2", $text );
	$text = preg_replace( "/(^|[^{$lc}])[{$lc}]([^{$lc}]|$)/",
	  "\\1 \\2", $text );

	# Strip wiki '' and '''
	$text = preg_replace( "/''[']*/", " ", $text );

	# Remove title words: those have already been found
	foreach ( $titlewords as $w => $val ) {
		$text = str_replace( $w, " ", $text );
	}
	$text = preg_replace( "/\\s+/", " ", $text );
	return $text;
}

function renameOldTables()
{
	$sql = "ALTER TABLE user RENAME TO old_user";
	wfQuery( $sql );
	$sql = "ALTER TABLE cur RENAME TO old_cur";
	wfQuery( $sql );
	$sql = "ALTER TABLE old RENAME TO old_old";
	wfQuery( $sql );
	$sql = "DROP TABLE IF EXISTS linked";
	wfQuery( $sql );
	#sql = "DROP TABLE IF EXISTS unlinked";
	wfQuery( $sql );
}

function buildtables()
{
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
}

function buildindexes()
{
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

function removeOldTables()
{
	wfQuery( "DROP TABLE IF EXISTS old_user" );
	wfQuery( "DROP TABLE IF EXISTS old_linked" );
	wfQuery( "DROP TABLE IF EXISTS old_unlinked" );
	wfQuery( "DROP TABLE IF EXISTS old_cur" );
	wfQuery( "DROP TABLE IF EXISTS old_old" );
}

?>
