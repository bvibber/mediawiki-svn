<?
# Script for updating tables with derived information.
# You should put your wiki into read-only mode while
# running this.
#

$DBserver	= "127.0.0.1";
$DBname		= "newwiki";
$DBuser		= "wikiuser";
$DBpassword	= "xxx";

$conn = mysql_connect( $DBserver, $DBuser, $DBpassword )
  or die( "Can't connect to new database" );
mysql_select_db( $DBname, $conn )
  or die( "Can't select database" );

print "Connected to database.\n";


print "Completed successfully.\n";

?>
