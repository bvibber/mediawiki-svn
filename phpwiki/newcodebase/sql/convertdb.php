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
include_once( "GlobalFunctions.php" );
include_once( "Language.php" );
include_once( "Namespace.php" );
include_once( "User.php" );
include_once( "Title.php" );
include_once( "Article.php" );

global $wgUser, $wgLang, $wgOut, $wgTitle;
$wgLangClass = "Language" . ucfirst( $wgLanguageCode );
$wgLang = new $wgLangClass();

# Name of old databse, SQL file to produce, and global progress counter.
#
$wgDBname		= "wikidb";
$outfilename	= "newdb.sql";
$count			= 0;

# Actual code begins here.  Some code may be commented out.
#

set_time_limit(0);
$outf = fopen( $outfilename, "w" ) or die( "Can't open output file.\n" );


convertUserTable();
convertCurTable();
convertOldTable();


# All done
#
fclose( $outf );
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
			  "user_password,user_email,user_options,user_watch,user_nickname)" .
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
		  "'$ops','$watch','')" );

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count records processed.\r";
		}
	}
	print "$count records processed.\n";
	mysql_free_result( $res );
	fwrite( $outf, ";\n" );
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
			  "cur_revision,cur_timestamp,cur_minor_edit," .
			  "cur_restrictions,cur_counter," .
			  "cur_ind_title) VALUES " );
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

		fwrite( $outf, "({$row->cur_id},$namespace,'$title','$text'," .
		  "'$com',{$row->cur_user},{$row->cur_old_version}," .
		  "'{$row->cur_timestamp}',{$row->cur_minor_edit},'$cr'," .
		  "{$row->cur_counter},'$cit')" );

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count records processed.\r";
		}
	}
	print "$count records processed.\n";
	mysql_free_result( $res );
	fwrite( $outf, ";\n" );
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
			  "old_revision,old_timestamp,old_minor_edit) VALUES " );
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
			print "$count records processed.\r";
		}
	}
	print "$count records processed.\n";
	mysql_free_result( $res );
	fwrite( $outf, ";\n" );
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
	unset( $ops["showStructure"] );
	unset( $ops["autowikify"] );
	unset( $ops["viewFrames"] );
	unset( $ops["textTableBackground"] );
	unset( $ops["text"] );
	unset( $ops["background"] );
	unset( $ops["forceQuickbar"] );
	unset( $ops["tabLine0"] );
	unset( $ops["tabLine1"] );
	unset( $ops["tabLine2"] );

	if ( $ops["changesLayout"] == "classic" ) {
		$ops["changesLayout"] = 1;
	} else {
		unset( $ops["changesLayout"] );
	}
	$q = strtolower( $ops["quickBar"] );
	if ( $q == "none" ) { $q = 0; }
	else if ( $q == "left" ) { $q = 1; }
	else { $q = 2; }
	$ops["quickBar"] = $q;

	if ( $ops["markupNewTopics"] == "inverse" ) {
		$ops["markupNewTopics"] = 1;
	} else {
		unset( $ops["markupNewTopics"] );
	}
	$sk = substr( strtolower( $ops["skin"] ), 0, 4 );
	if ( "star" == $sk ) { $sk = 1; }
	else if ( "nost" == $sk ) { $sk = 2; }
	else if ( "colo" == $sk ) { $sk = 3; }
	else { $sk = 0; }
	$ops["skin"] = $sk;

	$toggles = array( "underlineLinks", "justify",
		"numberHeadings", "hideMinor", "rememberPassword",
		"showHover" );

	foreach ( $toggles as $op ) {
		$lop = strtolower( $op );
		if ( ( "yes" == $lop ) || ( "on" == $lop ) ) {
			$ops[$op] = 1;
		} else {
			unset( $ops[$op] );
		}
	}
	$a = array();

	foreach ( $ops as $oname => $oval ) {
		array_push( $a, "$oname=$oval" );
	}
	$s = implode( "\n", $a );
	return urlencode( $s );
}

?>
