<?

# Rebuild link tracking tables from scratch.  This takes several
# hours, depending on the database size and server configuration.

if ( ! is_readable( "../LocalSettings.php" ) ) {
	print "A copy of your installation's LocalSettings.php\n" .
	  "must exist in the source directory.\n";
	exit();
}

$wgCommandLineMode = true;
$DP = "../includes";
include_once( "../LocalSettings.php" );
include_once( "../AdminSettings.php" );

$include_path = ini_get( "include_path" );
if( strchr( $include_path, ";" ) ) $sep = ";"; else $sep = ":";
ini_set( "include_path", $IP . $sep . ini_get( "include_path" ) );

include_once( "Setup.php" );
include_once( "./rebuildlinks.inc" );
$wgTitle = Title::newFromText( "Rebuild links script" );
set_time_limit(0);

$wgDBuser			= $wgDBadminuser;
$wgDBpassword		= $wgDBadminpassword;

rebuildLinkTablesPass1();
rebuildLinkTablesPass2();

print "Done.\n";
exit();

?>
