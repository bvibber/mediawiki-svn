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

buildTables();
initializeTables();
buildIndexes();

print "Done.\n";
exit();

?>
