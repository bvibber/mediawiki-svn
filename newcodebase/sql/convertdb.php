<?
# Script for converting May 2002 version of wikipedia
# database into the format for the "newwiki" software.
# Intended to be run from the php command line.
#
include_once( "../Namespace.php" );

$DBserver		= "127.0.0.1";
$DBname			= "wikidb";
$DBuser			= "wikiuser";
$DBpassword		= "xxx";
$outfilename	= "newdb.sql";

$conn = mysql_connect( $DBserver, $DBuser, $DBpassword )
  or die( "Can't connect to database server." );
mysql_select_db( $DBname, $conn ) or die( "Can't select database." );
print "Connected to database.\n";

$outf = fopen( $outfilename, "w" )
  or die( "Can't open output file.\n" );

set_time_limit(0);


# USER
#
print "Converting USER table.\n";
$sql = "SELECT * FROM user";
$res = mysql_query( $sql, $conn );
if ( ! $res ) die( "Can't open \"user\" table." );

p_start();
while ( $row = mysql_fetch_object( $res ) ) {

	if ( 0 == ( $progressCount % 100 ) ) {
		if ( 0 != $progressCount ) { fwrite( $outf, ";\n" ) ; }

		fwrite( $outf, "INSERT INTO user (user_id,user_name,user_rights," .
		  "user_password,user_email,user_options,user_watch,user_nickname)" .
		  " VALUES " );
	} else {
		fwrite( $outf, "," );
	}
	# Need to do some tweaking of options here
	#
	$ops = strencode(urldecode($row->user_options));
	$name = strencode($row->user_name);
	$rights = strencode($row->user_rights);
	$email = strencode($row->user_email);
	$pwd = strencode($row->user_password);
	$watch = strencode($row->user_watch);

	fwrite( $outf, "({$row->user_id},'$name','$rights','$pwd','$email'," .
	  "'$ops','$watch','')" );
	progress();
}
mysql_free_result( $res );
fwrite( $outf, ";\n" );
p_end();

# CUR
#

print "Converting CUR table.\n";
$sql = "SELECT * FROM cur";
$res = mysql_query( $sql, $conn );
if ( ! $res ) die( "Can't open \"cur\" table." );

p_start();
while ( $row = mysql_fetch_object( $res ) ) {

	if ( 0 == ( $progressCount % 100 ) ) {
		if ( 0 != $progressCount ) { fwrite( $outf, ";\n" ) ; }

		fwrite( $outf, "INSERT INTO cur (cur_id,cur_namespace," .
		  "cur_title,cur_text,cur_comment,cur_user,cur_user_text," .
		  "cur_old_version,cur_timestamp,cur_minor_edit," .
		  "cur_restrictions,cur_params,cur_counter," .
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
	$namespace = Namespace::getIndex( $ns );
	$title = strencode( $t );
	$text = strencode( $row->cur_text );
	$com = strencode( $row->cur_comment );
	$cut = strencode( $row->cur_user_text );
	$cr = strencode( $row->cur_restrictions );
	$cp = strencode( $row->cur_params );
	$cit = strencode( $row->cur_ind_title );

	fwrite( $outf, "({$row->cur_id},$namespace,'$title','$text'," .
	  "'$com',{$row->cur_user},'$cut',{$row->cur_old_version}," .
	  "'{$row->cur_timestamp}',{$row->cur_minor_edit},'$cr','$cp'," .
	  "{$row->cur_counter},'$cit')" );
	progress();
}
mysql_free_result( $res );
fwrite( $outf, ";\n" );
p_end();

# OLD
#

print "Converting OLD table.\n";
$sql = "SELECT * FROM old";
$res = mysql_query( $sql, $conn );
if ( ! $res ) die( "Can't open \"old\" table." );

p_start();
while ( $row = mysql_fetch_object( $res ) ) {

	if ( 0 == ( $progressCount % 100 ) ) {
		if ( 0 != $progressCount ) { fwrite( $outf, ";\n" ) ; }

		fwrite( $outf, "INSERT INTO old (old_id,old_namespace," .
		  "old_title,old_text,old_comment,old_user,old_user_text," .
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
	$namespace = Namespace::getIndex( $ns );
	$title = strencode( $t );
	$text = strencode( $row->old_text );
	$com = strencode( $row->old_comment );
	$cut = strencode( $row->old_user_text );

	fwrite( $outf, "({$row->old_id},$namespace,'$title','$text'," .
	  "'$com',{$row->old_user},'$cut',{$row->old_old_version}," .
	  "'{$row->old_timestamp}',{$row->old_minor_edit} )" );
	progress();
}
mysql_free_result( $res );
fwrite( $outf, ";\n" );
p_end();



fclose( $outf );

# All done
#
print "Database converted. You should now run the
\"updatedb\" script to fill in the derived tables.
";

function p_start()
{
	global $progressCount;
	$progressCount = 0;
}

function p_end()
{
	global $progressCount;
	print "$progressCount records processed.\n\n";
}

function progress()
{
	global $progressCount;

	++$progressCount;
	if ( 0 == ( $progressCount % 1000 ) ) {
		print "$progressCount records processed.\n";
	}
}

function strencode( $s )
{
	$s = str_replace( "\\", "\\\\", $s );
	$s = str_replace( "\r", "\\r", $s );
	$s = str_replace( "\n", "\\n", $s );
	$s = str_replace( "\"", "\\\"", $s );
	$s = str_replace( "'", "\\'", $s );
	$s = str_replace( "\0", "\\0", $s );
	return $s;
}

?>
