<?
# Script for converting May 2002 version of wikipedia
# database into the format for the "newwiki" software.
# Intended to be run from the php command line.  It has
# to be run from the same directory as the code so that
# all the includes work.

# Must find and include utility classes from main code,
# and define a few of its globals.
#
include_once( "LocalSettings.php" );
$wgDebugLogFile = "logfile";
include_once( "GlobalFunctions.php" );
include_once( "Language.php" );
include_once( "Namespace.php" );
include_once( "Skin.php" );
include_once( "OutputPage.php" );
include_once( "User.php" );
include_once( "LinkCache.php" );
include_once( "Title.php" );
include_once( "Article.php" );

global $wgUser, $wgLang, $wgOut, $wgTitle, $wgLinkCache;
$wgLangClass = "Language" . ucfirst( $wgLanguageCode );
$wgLang = new $wgLangClass();
$wgOut = new OutputPage();
$wgLinkCache = new LinkCache();
$wgUser = new User();
set_time_limit(0);

# Name of old databse, SQL file to produce, and global progress counter.
#
$wgDBname		= "wikidb";
$outfilename	= "newdb.sql";
$count			= 0;

# The "convert..." functions all open the old database and write SQL
# commands out to a file, which should then be "sourced" in the new
# database after it is created by buildtables.sql.

# $outf = fopen( $outfilename, "w" ) or die( "Can't open output file.\n" );

# convertUserTable();
# convertCurTable();
# convertOldTable();

# fclose( $outf );

#
# The "rebuild..." functions operate on the new db directly.
#

$wgDBname = "newwiki";

rebuildLinkTables();

#
# All done
#

print "Done.\n";
exit();

#
#
function convertUserTable()
{
	global $count, $outf;
	$count = 0;

	print "Converting USER table.\n";
	$conn = wfGetDB();
	$sql = "SELECT user_id,user_name,user_rights,user_password," .
	  "user_email,user_options,user_watch FROM user";
	$res = mysql_query( $sql, $conn );
	if ( ! $res ) die( "Can't open \"user\" table." );

	while ( $row = mysql_fetch_object( $res ) ) {
		if ( 0 == ( $count % 100 ) ) {
			if ( 0 != $count ) { fwrite( $outf, ";\n" ) ; }

			fwrite( $outf, "INSERT INTO user (user_id,user_name,user_rights," .
			  "user_password,user_email,user_options,user_watch)" .
			  " VALUES " );
		} else {
			fwrite( $outf, "," );
		}
		$ops = fixUserOptions( $row->user_options );
		$name = wfStrencode( $row->user_name );
		$rights = wfStrencode( $row->user_rights );
		$email = wfStrencode( $row->user_email );
		$pwd = wfStrencode( $row->user_password );
		$watch = wfStrencode( $row->user_watch );

		fwrite( $outf, "({$row->user_id},'$name','$rights','$pwd','$email'," .
		  "'$ops','$watch')" );

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count records processed.\n";
		}
	}
	print "$count records processed.\n";
	mysql_free_result( $res );
	fwrite( $outf, ";\n" );
	fflush( $outf );
}


function convertCurTable()
{
	global $count, $outf;
	$count = 0;

	print "Converting CUR table.\n";
	$conn = wfGetDB();
	$sql = "SELECT cur_id,cur_title,cur_text,cur_comment,cur_user," .
	  "cur_old_version,cur_timestamp,cur_minor_edit,cur_restrictions," .
	  "cur_counter,cur_ind_title FROM cur";
	$res = mysql_query( $sql, $conn );
	if ( ! $res ) die( "Can't open \"cur\" table." );

	while ( $row = mysql_fetch_object( $res ) ) {
		if ( 0 == ( $count % 100 ) ) {
			if ( 0 != $count ) { fwrite( $outf, ";\n" ) ; }

			fwrite( $outf, "INSERT INTO cur (cur_id,cur_namespace," .
			  "cur_title,cur_text,cur_comment,cur_user," .
			  "cur_old_version,cur_timestamp,cur_minor_edit," .
			  "cur_restrictions,cur_counter," .
			  "cur_ind_title,cur_is_redirect) VALUES " );
		} else {
			fwrite( $outf, "," );
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
		$namespace = Namespace::getIndex( $ns );
		$title = wfStrencode( $t );
		$text = wfStrencode( $row->cur_text );
		$com = wfStrencode( $row->cur_comment );
		$cr = wfStrencode( $row->cur_restrictions );
		$cp = wfStrencode( $row->cur_params );
		$cit = wfStrencode( $row->cur_ind_title );

		if ( preg_match( "/^#redirect /i", $text ) ) {
			$redir = 1;
			$text = fixRedirect( $text );
		} else { $redir = 0; }

		fwrite( $outf, "({$row->cur_id},$namespace,'$title','$text'," .
		  "'$com',{$row->cur_user},{$row->cur_old_version}," .
		  "'{$row->cur_timestamp}',{$row->cur_minor_edit},'$cr'," .
		  "{$row->cur_counter},'$cit',$redir)" );

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count records processed.\n";
		}
	}
	print "$count records processed.\n";
	mysql_free_result( $res );
	fwrite( $outf, ";\n" );
	fflush( $outf );
}


function convertOldTable()
{
	global $count, $outf;
	$count = 0;

	print "Converting OLD table.\n";
	$conn = wfGetDB();
	$sql = "SELECT old_id,old_title,old_text,old_comment,old_user," .
	  "old_old_version,old_timestamp,old_minor_edit FROM old";
	$res = mysql_query( $sql, $conn );
	if ( ! $res ) die( "Can't open \"old\" table." );

	while ( $row = mysql_fetch_object( $res ) ) {
		if ( 0 == ( $count % 100 ) ) {
			if ( 0 != $count ) { fwrite( $outf, ";\n" ) ; }

			fwrite( $outf, "INSERT INTO old (old_id,old_namespace," .
			  "old_title,old_text,old_comment,old_user," .
			  "old_old_version,old_timestamp,old_minor_edit) VALUES " );
		} else {
			fwrite( $outf, "," );
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
		$namespace = Namespace::getIndex( $ns );
		$title = wfStrencode( $t );
		$text = wfStrencode( $row->old_text );
		$com = wfStrencode( $row->old_comment );

		fwrite( $outf, "({$row->old_id},$namespace,'$title','$text'," .
		  "'$com',{$row->old_user},{$row->old_old_version}," .
		  "'{$row->old_timestamp}',{$row->old_minor_edit} )" );

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count records processed.\n";
		}
	}
	print "$count records processed.\n";
	mysql_free_result( $res );
	fwrite( $outf, ";\n" );
	fflush( $outf );
}

function rebuildLinkTables()
{
	global $count, $outf, $wgLinkCache, $wgOut;
	$count = 0;

	print "Rebuilding link tables.\n";

	$conn = wfGetDB();
	$sql = "DELETE FROM links";
	$res = mysql_query( $sql, $conn );
	if ( ! $res ) die( "Can't delete from \"links\" table." );

	$conn = wfGetDB();
	$sql = "DELETE FROM brokenlinks";
	$res = mysql_query( $sql, $conn );
	if ( ! $res ) die( "Can't delete from \"brokenlinks\" table." );

	$conn = wfGetDB();
	$sql = "SELECT cur_id,cur_namespace,cur_title,cur_text FROM cur";
	$res = mysql_query( $sql, $conn );
	if ( ! $res ) die( "Can't open \"cur\" table." );

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
			$res2 = mysql_query( $sql, $conn );
			if ( ! $res2 ) die( "Can't update \"links\" table." );
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
			$res2 = mysql_query( $sql, $conn );
			if ( ! $res2 ) die( "Can't update \"brokenlinks\" table." );
		}

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count records processed.\n";
		}
	}
	print "$count records processed.\n";
	mysql_free_result( $res );
	fwrite( $outf, "\n" );
}

function getInternalLinks ( $title, $text )
{
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
		  "$1 [[$2]]", $text, 1 );
	} else if ( preg_match( "/^{$re}\\s+{$tc}+/i", $text ) ) {
		$text = preg_replace( "/^({$re})\\s+({$tc}+)/i",
		  "$1 [[$2]]", $text, 1 );
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
