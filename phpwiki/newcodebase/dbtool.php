<?
# Script for doing various database maintenance tasks.
# Intended to be run from the php command line.  It has
# to be run from the same directory as the code so that
# all the includes work.

include_once( "Setup.php" );
$wgDebugLogFile = "logfile";
set_time_limit(0);

# Need to have separate connection to "old" database.
#
$wgOldDBname	= "wikidb";
$wgOldDBuser	= "wikiadmin";
$wgOldImageDir	= "/rfs/backups/lee/wikiimages";

# Convert old (May 2002) database format.
#
# convertUserTable();
# convertCurTable();
# convertOldTable();

# convertImageDirectories();

# Maintenance tasks.
#

# rebuildLinkTables();

rebuildIndText();

print "Done.\n";
exit();

########## End of script, beginning of functions.

# Convert May 2002 version of database into new format.
#
function convertUserTable()
{
	$count = 0;
	print "Converting USER table.\n";

	$oldconn = getOldDB();
	$sql = "SELECT user_id,user_name,user_rights,user_password," .
	  "user_email,user_options,user_watch FROM user";
	$oldres = dbQuery( $sql, $oldconn );
	if ( ! $oldres ) $oldres = dbErr( $sql, "old" );

	$newconn = getNewDB();
	$sql = "DELETE FROM user";
	$newres = dbQuery( $sql, $newconn );
	if ( ! $newres ) $newres = dbErr( $sql );

	$sql = "LOCK TABLES user WRITE";
	$newres = dbQuery( $sql, $newconn );
	if ( ! $newres ) $newres = dbErr( $sql );

	$sql = "";
	while ( $row = mysql_fetch_object( $oldres ) ) {
		if ( 0 == ( $count % 10 ) ) {
			if ( 0 != $count ) {
				$newconn = getNewDB();
				$newres = dbQuery( $sql, $newconn );
				if ( ! $newres ) $newres = dbErr( $sql );
			}
			$sql = "INSERT INTO user (user_id,user_name,user_rights," .
			  "user_password,user_oldpassword,user_email,user_options," .
			  "user_watch) VALUES ";
		} else {
			$sql .= ",";
		}
		$ops = fixUserOptions( $row->user_options );
		$name = wfStrencode( $row->user_name );
		$rights = wfStrencode( $row->user_rights );
		$email = wfStrencode( $row->user_email );
		$pwd = wfStrencode( User::encryptPassword( $row->user_password ) );
		$watch = wfStrencode( $row->user_watch );

		$sql .= "({$row->user_id},'$name','$rights','$pwd','','$email'," .
		  "'$ops','$watch')";

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count records processed.\n";
		}
	}
	if ( $sql ) {
		$newconn = getNewDB();
		$newres = dbQuery( $sql, $newconn );
		if ( ! $newres ) $newres = dbErr( $sql );
	}
	print "$count records processed.\n";
	mysql_free_result( $oldres );

	$newconn = getNewDB();
	$sql = "UNLOCK TABLES";
	$newres = dbQuery( $sql, $newconn );
	if ( ! $newres ) $newres = dbErr( $sql );
}

# Convert May 2002 version of database into new format.
#
function convertCurTable()
{
	$count = 0;
	print "Converting CUR table.\n";

	$oldconn = getOldDB();
	$sql = "SELECT cur_id,cur_title,cur_text,cur_comment,cur_user," .
	  "cur_timestamp,cur_minor_edit,cur_restrictions," .
	  "cur_counter,cur_ind_title,cur_user_text FROM cur";
	$oldres = dbQuery( $sql, $oldconn );
	if ( ! $oldres ) $oldres = dbErr( $sql, "old" );

	$newconn = getNewDB();
	$sql = "DELETE FROM cur";
	$newres = dbQuery( $sql, $newconn );
	if ( ! $newres ) $newres = dbErr( $sql );

	$sql = "LOCK TABLES cur WRITE";
	$newres = dbQuery( $sql, $newconn );
	if ( ! $newres ) $newres = dbErr( $sql );

	$sql = "";
	while ( $row = mysql_fetch_object( $oldres ) ) {
		if ( 0 == ( $count % 10 ) ) {
			if ( 0 != $count ) {
				$newconn = getNewDB();
				$newres = dbQuery( $sql, $newconn );
				if ( ! $newres ) $newres = dbErr( $sql );
			}
			$sql = "INSERT INTO cur (cur_id,cur_namespace," .
			  "cur_title,cur_text,cur_comment,cur_user," .
			  "cur_timestamp,cur_minor_edit," .
			  "cur_restrictions,cur_counter," .
			  "cur_ind_title,cur_is_redirect,cur_user_text) VALUES ";
		} else {
			$sql .= ",";
		}
		if ( preg_match( "/^([A-Za-z][A-Za-z0-9 _]*):(.*)$/",
		  $row->cur_title, $m ) ) {
			$ns = $m[1];
			$t = $m[2];
		} else {
			$ns = "";
			$t = $row->cur_title;
		}
		if ( 0 == strcasecmp( "Log", $ns ) ) {
			$ns = "Wikipedia";
			$t .= " log";
		}
		$text = convertImageLinks( $row->cur_text );

		$namespace = Namespace::getIndex( $ns );
		$title = wfStrencode( $t );
		$text = wfStrencode( $text );
		$com = wfStrencode( $row->cur_comment );
		$cr = wfStrencode( $row->cur_restrictions );
		$cit = wfStrencode( $row->cur_ind_title );
		$cut = wfStrencode( $row->cur_user_text );
		if ( "" == $cut ) { $cut = "Unknown"; }

		$counter = $row->cur_counter;
		if ( ! $counter ) { $counter = 0; }

		if ( preg_match( "/^#redirect /i", $text ) ) {
			$redir = 1;
			$text = fixRedirect( $text );
		} else { $redir = 0; }

		$sql .= "({$row->cur_id},$namespace,'$title','$text'," .
		  "'$com',{$row->cur_user}," .
		  "'{$row->cur_timestamp}',{$row->cur_minor_edit},'$cr'," .
		  "{$counter},'$cit',$redir,'$cut')";

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count records processed.\n";
		}
	}
	if ( $sql ) {
		$newconn = getNewDB();
		$newres = dbQuery( $sql, $newconn );
		if ( ! $newres ) $newres = dbErr( $sql );
	}
	print "$count records processed.\n";
	mysql_free_result( $oldres );

	$newconn = getNewDB();
	$sql = "UNLOCK TABLES";
	$newres = dbQuery( $sql, $newconn );
	if ( ! $newres ) $newres = dbErr( $sql );
}

# Convert May 2002 version of database into new format.
#
function convertOldTable()
{
	$count = 0;
	print "Converting OLD table.\n";

	$oldconn = getOldDB();
	$sql = "SELECT old_id,old_title,old_text,old_comment,old_user," .
	  "old_timestamp,old_minor_edit,old_user_text FROM old";
	$oldres = dbQuery( $sql, $oldconn );
	if ( ! $oldres ) $oldres = dbErr( $sql, "old" );

	$newconn = getNewDB();
	$sql = "DELETE FROM old";
	$newres = dbQuery( $sql, $newconn );
	if ( ! $newres ) $newres = dbErr( $sql );

	$sql = "LOCK TABLES old WRITE";
	$newres = dbQuery( $sql, $newconn );
	if ( ! $newres ) $newres = dbErr( $sql );

	$sql = "";
	while ( $row = mysql_fetch_object( $oldres ) ) {
		if ( 0 == ( $count % 10 ) ) {
			if ( 0 != $count ) {
				$newconn = getNewDB();
				$newres = dbQuery( $sql, $newconn );
				if ( ! $newres ) $newres = dbErr( $sql );
			}
			$sql = "INSERT INTO old (old_id,old_namespace,old_title," .
			  "old_text,old_comment,old_user," .
			  "old_timestamp,old_minor_edit,old_user_text) VALUES ";
		} else {
			$sql .= ",";
		}
		if ( preg_match( "/^([A-Za-z][A-Za-z0-9 _]*):(.*)$/",
		  $row->old_title, $m ) ) {
			$ns = $m[1];
			$t = $m[2];
		} else {
			$ns = "";
			$t = $row->old_title;
		}
		if ( 0 == strcasecmp( "Log", $ns ) ) {
			continue;
		}
		$text = convertImageLinks( $row->old_text );

		$namespace = Namespace::getIndex( $ns );
		$title = wfStrencode( $t );
		$text = wfStrencode( $text );
		$com = wfStrencode( $row->old_comment );
		$ot = wfStrencode( $row->old_user_text );
		if ( "" == $ot ) { $ot = "Unknown"; }

		$sql .= "({$row->old_id},$namespace,'$title','$text'," .
		  "'$com',{$row->old_user}," .
		  "'{$row->old_timestamp}',{$row->old_minor_edit},'$ot')";

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count records processed.\n";
		}
	}
	if ( $sql ) {
		$newconn = getNewDB();
		$newres = dbQuery( $sql, $newconn );
		if ( ! $newres ) $newres = dbErr( $sql );
	}
	print "$count records processed.\n";
	mysql_free_result( $oldres );

	$newconn = getNewDB();
	$sql = "UNLOCK TABLES";
	$newres = dbQuery( $sql, $newconn );
	if ( ! $newres ) $newres = dbErr( $sql );
}


function convertImageDirectories()
{
	global $wgOldImageDir, $wgUploadDirectory;
	$count = 0;

	print "Moving image files.\n";

	$conn = getNewDB();
	$sql = "SELECT DISTINCT il_to FROM imagelinks";
	$res = dbQuery( $sql, $conn );
	if ( ! $res ) $res = dbErr( $sql );
/*
	while ( $row = mysql_fetch_object( $res ) ) {
		$oname = $row->il_to;

		$nt = Title::newFromText( $oname );
		$nname = $nt->getDBkey();
	
		$oldumask = umask(0);
		$dest = $wgUploadDirectory . "/" . $nname{0};
		if ( ! is_dir( $dest ) ) { mkdir( $dest, 0777 ); }
		$dest .= "/" . substr( $nname, 0, 2 );
		if ( ! is_dir( $dest ) ) { mkdir( $dest, 0777 ); }
		umask( $oldumask );

		print "{$wgOldImageDir}/{$oname} => {$dest}/{$nname}\n";

		if ( copy( "{$wgOldImageDir}/{$oname}", "{$dest}/{$nname}" ) ) {
			++$count;

			$conn = getNewDB();
			$sql = "UPDATE imagelinks SET il_to='{$nname}' " .
			  "WHERE il_to='{$oname}'";
			$res2 = dbQuery( $sql, $conn );
			if ( ! $res2 ) $res2 = dbErr( $sql );

			$conn = getNewDB();
			$sql = "INSERT INTO image (img_name,img_timestamp,img_user," .
			  "img_user_text,img_size,img_description) VALUES ('{$nname}','" .
			  date( "YmdHis" ) . "',0,'(Automated conversion)','" .
			  filesize( "{$dest}/{$nname}" ) . "','')";
			$res2 = dbQuery( $sql, $conn );
			if ( ! $res2 ) $res2 = dbErr( $sql );
		}
	}
	mysql_free_result( $res );
	print "{$count} images moved.\n";
*/
	$count = 0;
	$dir = opendir( $wgOldImageDir );
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
		$dest = $wgUploadDirectory . "/" . $nname{0};
		if ( ! is_dir( $dest ) ) { mkdir( $dest, 0777 ); }
		$dest .= "/" . substr( $nname, 0, 2 );
		if ( ! is_dir( $dest ) ) { mkdir( $dest, 0777 ); }
		umask( $oldumask );

		print "{$wgOldImageDir}/{$oname} => {$dest}/{$nname}\n";

		if ( copy( "{$wgOldImageDir}/{$oname}", "{$dest}/{$nname}" ) ) {
			++$count;

			$conn = getNewDB();
			$sql = "DELETE FROM image WHERE img_name='" .
			  wfStrencode( $nname ) . "'";
			$res2 = dbQuery( $sql, $conn );

			$conn = getNewDB();
			$sql = "INSERT INTO image (img_name,img_timestamp,img_user," .
			  "img_user_text,img_size,img_description) VALUES ('" .
			  wfStrencode( $nname ) . "','" .
			  date( "YmdHis" ) . "',0,'(Automated conversion)','" .
			  filesize( "{$dest}/{$nname}" ) . "','')";
			$res2 = dbQuery( $sql, $conn );
			if ( ! $res2 ) $res2 = dbErr( $sql );
		}
	}
	print "{$count} images moved.\n";
}


# Empty and rebuild the "links" and "brokenlinks" tables.
# This can be done at any time for the new database, and
# probably should be done periodically (you should lock
# the wiki while it is running as well).
#
function rebuildLinkTables()
{
	global $wgLinkCache;
	$count = 0;

	print "Rebuilding link tables.\n";

	$conn = getNewDB();
	$sql = "DELETE FROM links";
	$res = dbQuery( $sql, $conn );
	if ( ! $res ) $res = dbErr( $sql );

	$conn = getNewDB();
	$sql = "DELETE FROM brokenlinks";
	$res = dbQuery( $sql, $conn );
	if ( ! $res ) $res = dbErr( $sql );

	$conn = getNewDB();
	$sql = "DELETE FROM imagelinks";
	$res = dbQuery( $sql, $conn );
	if ( ! $res ) $res = dbErr( $sql );

	$conn = getNewDB();
	$sql = "SELECT cur_id,cur_namespace,cur_title,cur_text FROM cur";
	$res = dbQuery( $sql, $conn );
	if ( ! $res ) $res = dbErr( $sql );

	while ( $row = mysql_fetch_object( $res ) ) {
		$id = $row->cur_id;
		$ns = Namespace::getName( $row->cur_namespace );
		if ( "" == $ns ) {
			$title = $row->cur_title;
		} else {
			$title = "$ns:{$row->cur_title}";
		}
		$text = $row->cur_text;
		$wgLinkCache = new LinkCache();
		getInternalLinks( $title, $text );

		$sql = "";
		$a = $wgLinkCache->getImageLinks();
		if ( 0 != count ( $a ) ) {
			$sql = "INSERT INTO imagelinks (il_from,il_to) VALUES ";
			$first = true;
			foreach( $a as $iname => $val ) {
				if ( ! $first ) { $sql .= ","; }
				$first = false;

				$sql .= "('" . wfStrencode( $title ) . "','" .
				  wfStrencode( $iname ) . "')";
			}
		}
		if ( "" != $sql ) {
			$conn = getNewDB();
			$res2 = dbQuery( $sql, $conn );
			if ( ! $res2 ) $res = dbErr( $sql );
		}

		$sql = "";
		$a = $wgLinkCache->getGoodLinks();
		if ( 0 != count( $a ) ) {
			$sql = "INSERT INTO links (l_from,l_to) VALUES ";
			$first = true;
			foreach( $a as $lt => $lid ) {
				if ( ! $first ) { $sql .= ","; }
				$first = false;

				$sql .= "('" . wfStrencode( $title ) . "',$lid)";
			}
		}
		if ( "" != $sql ) {
			$conn = getNewDB();
			$res2 = dbQuery( $sql, $conn );
			if ( ! $res2 ) $res = dbErr( $sql );
		}

		$sql = "";
		$a = $wgLinkCache->getBadLinks();
		if ( 0 != count ( $a ) ) {
			$sql = "INSERT INTO brokenlinks (bl_from,bl_to) VALUES ";
			$first = true;
			foreach( $a as $blt ) {
				if ( ! $first ) { $sql .= ","; }
				$first = false;

				$sql .= "($id,'" . wfStrencode( $blt ) . "')";
			}
		}
		if ( "" != $sql ) {
			$conn = getNewDB();
			$res2 = dbQuery( $sql, $conn );
			if ( ! $res2 ) $res = dbErr( $sql );
		}

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count records processed.\n";
		}
	}
	print "$count records processed.\n";
	mysql_free_result( $res );
}

# This is a handy function to modify for doing any big transform
# on every page.
#

function rebuildIndText()
{
	$count = 0;
	print "Rebuilding fulltext index fields.\n";

	$conn = getNewDB();
	$sql = "LOCK TABLES cur WRITE";
	$res = dbQuery( $sql, $conn );
	if ( ! $res ) $res = dbErr( $sql );

	$sql = "SELECT cur_id,cur_title,cur_text FROM cur";
	$res = dbQuery( $sql, $conn );
	if ( ! $res ) $res = dbErr( $sql );

	$sql = "";
	while ( $row = mysql_fetch_object( $res ) ) {
		$u = new SearchUpdate( $row->cur_id, $row->cur_title,
		  $row->cur_text );
		$u->doUpdate();

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count records processed.\n";
		}
	}
	print "$count records processed.\n";
	mysql_free_result( $res );

	$conn = getNewDB();
	$sql = "UNLOCK TABLES";
	$res = dbQuery( $sql, $conn );
	if ( ! $res ) $res = dbErr( $sql );
}
# Utility functions for the above.
#

function convertImageLinks( $text )
{
	$re = "/(^|[^[])http:\/\/(www.|)wikipedia.com\/upload\/" .
	  "([a-zA-Z0-9_:.~\%\-]+)\.(png|PNG|jpg|JPG|jpeg|JPEG|gif|GIF)/";

	while ( preg_match( $re, $text, $m ) ) {
		$nt = Title::newFromText( $m[3] . "." . $m[4] );
		$nname = $nt->getDBkey();

		print "{$m[3]}.{$m[4]} => {$nname}\n";
		preg_replace( $re, "\\1[[image:{$nname}]]", $text, 1 );
	}
	$re = "/(^|[^[])http:\/\/(www.|)wikipedia.com\/images\/uploads\/" .
	  "([a-zA-Z0-9_:.~\%\-]+)\.(png|PNG|jpg|JPG|jpeg|JPEG|gif|GIF)/";

	while ( preg_match( $re, $text, $m ) ) {
		$nt = Title::newFromText( $m[3] . "." . $m[4] );
		$nname = $nt->getDBkey();

		print "{$m[3]}.{$m[4]} => {$nname}\n";
		preg_replace( $re, "\\1[[image:{$nname}]]", $text, 1 );
	}
	return $text;
}

function getOldDB()
{
	global $wgDBserver, $wgDBpassword;
	global $wgOldDBuser, $wgOldDBname;
	global $wgOldDBconnection;

	if ( ! $wgOldDBconnection ) {
		$wgOldDBconnection = mysql_connect( $wgDBserver, $wgOldDBuser,
		  $wgDBpassword ) or die( "Can't connect to old database." );
		mysql_select_db( $wgOldDBname, $wgOldDBconnection ) or die(
		  "Can't select old database." );
	}
	return $wgOldDBconnection;
}

function getNewDB()
{
	global $wgDBserver, $wgDBpassword;
	global $wgDBuser, $wgDBname;
	global $wgDBconnection;

	if ( ! $wgDBconnection ) {
		$wgDBconnection = mysql_connect( $wgDBserver, $wgDBuser,
		  $wgDBpassword ) or die( "Can't connect to new database." );
		mysql_select_db( $wgDBname, $wgDBconnection ) or die(
		  "Can't select new database." );
	}
	return $wgDBconnection;
}

function dbQuery( $sql, $conn )
{
	# error_log( "{$sql}\n", 3, "logfile" );
	return mysql_query( $sql, $conn );
}

function dbErr( $query, $db = "" )
{
	global $wgDBconnection, $wgOldDBconnection;

	$e = mysql_errno();
	$em = mysql_error();

	if ( 2006 == $e ) {
		$retries = 5;
		while ( $retries > 0 ) {
			print "Lost connection...retrying.\n";
			sleep( rand( 1, 5 ) );

			if ( "old" == $db ) {
				unset( $wgOldDBconnection );
				$c = getOldDB();
			} else {
				unset( $wgDBconnection );
				$c = getNewDB();
			}
			$r = dbQuery( $query, $c );
			if ( $r ) { return $r; }

			if ( 2006 != mysql_errno() ) break;
			--$retries;
		}
	}
	if ( strlen( $query ) > 1000 ) {
		$query = substr( $query, 0, 1000 ) . "...";
	}
	print "Query: $query\n";
	print "Error {$e}: {$em}\n";
	die();
}

function getInternalLinks ( $title, $text )
{
	global $wgLinkCache, $wgLang;

	$text = preg_replace( "/<\\s*nowiki\\s*>.*<\\/\\s*nowiki\\s*>/i",
	  "", $text );

	$a = explode( "[[", " " . $text );
	$s = array_shift( $a );
	$s = substr( $s, 1 );

	$tc = "[&;%\\-,.\\(\\)' _0-9A-Za-z\\/:\\x80-\\xff]";
	foreach ( $a as $line ) {
		$e1 = "/^({$tc}+)\\|([^]]+)]]/sD";
		$e2 = "/^({$tc}+)]]/sD";

		if ( preg_match( $e1, $line, $m )
		  || preg_match( $e2, $line, $m ) ) {
			$link = $m[1];
		} else {
			continue;
		}
		if ( preg_match( "/^([a-z]+):(.*)$$/", $link,  $m ) ) {
			$pre = strtolower( $m[1] );
			$suf = $m[2];
			if ( "image" == $pre ) {
				$wgLinkCache->addImageLink( $suf );
				continue;
			} else {
				$l = $wgLang->getLanguageName( $pre );
				if ( "" != $l ) {
					continue; # Language link
				}
			}
		}
		$nt = Title::newFromText( $link );
		$id = $nt->getArticleID(); # To force caching
	}
}

function fixRedirect( $text )
{
	$tc = "[&;%\\-,.\\(\\)' _0-9A-Za-z\\/:\\x80-\\xff]";
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

	if ( "" != $ops["viewFrames"] ) { $nops["frames"] = 1; }
	if ( "" != $ops["autowikify"] ) { $nops["advanced"] = 1; }

	$q = strtolower( $ops["quickBar"] );
	if ( $q == "none" ) { $q = 0; }
	else if ( $q == "left" ) { $q = 1; }
	else { $q = 2; }
	$nops["quickbar"] = $q;

	if ( $ops["markupNewTopics"] == "inverse" ) {
		$nops["highlightbroken"] = 1;
	}
	$sk = substr( strtolower( $ops["skin"] ), 0, 4 );
	if ( "star" == $sk ) { $sk = 1; }
	else if ( "nost" == $sk ) { $sk = 2; }
	else if ( "colo" == $sk ) { $sk = 3; }
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
	if ( $c < 20 || c > 200 ) { $nops["cols"] = 60; }
	else { $nops["cols"] = $c; }
	$r = $ops["rows"];
	if ( $r < 5 || $r > 100 ) { $nops["rows"] = 20; }
	else { $nops["rows"] = $r; }
	$r = $ops["resultsPerPage"];
	if ( $r < 5 || $r > 500 ) { $nops["searchlimit"] = 20; }
	else { $nops["searchlimit"] = $r; }
	$r = $ops["viewRecentChanges"];
	if ( $r < 5 || $r > 500 ) { $nops["rclimit"] = 20; }
	else { $nops["rclimit"] = $r; }
	$nops["rcdays"] = 3;

	$a = array();
	foreach ( $nops as $oname => $oval ) {
		array_push( $a, "$oname=$oval" );
	}
	$s = implode( "\n", $a );
	return urlencode( $s );
}

?>
