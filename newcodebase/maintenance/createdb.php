<?

# Creating a new empty database; either this or the conversion
# script from the old format needs to be run, but not both.

include_once( "Setup.php" );
$wgTitle = Title::newFromText( "Database creation script" );
include_once( "./buildTables.inc" );
set_time_limit(0);

#$wgDBname			= "wikidb";
#$wgDBuser			= "wikiadmin";
#$wgDBpassword		= "adminpass";

print "\n  * * *\nWarning! This script will completely erase the\n" .
  "existing database '" . $wgDBname . "' and all its contents.\n" .
  "Are you sure you want to do this? (yes/no) ";

$fp = fopen( "php://stdin", "r" );
$response = trim( fgets( $fp ) );
fclose( $fp );

if ( 0 == strcasecmp( "yes", $response ) ) {
	$sql = "DROP DATABASE IF EXISTS " . $wgDBname;
	wfQuery( $sql );
	$sql = "CREATE DATABASE " . $wgDBname;
	wfQuery( $sql );

	buildTables();
	initializeTables();
	buildIndexes();
} else {
	print "You did not respond with 'yes'; exiting.\n";
}

print "Done.\n";
exit();

?>
