<?

# Rebuild link tracking tables from scratch.  This takes several
# hours, depending on the database size and server configuration.

include_once( "Setup.php" );
include_once( "./rebuildLinks.inc" );
include_once( "./rebuildRecentChanges.inc" );
$wgTitle = Title::newFromText( "Rebuild links script" );
set_time_limit(0);

$wgDBuser			= "wikiadmin";
$wgDBpassword		= $wgDBadminpassword;

rebuildLinkTablesPass1();
rebuildLinkTablesPass2();

rebuildRecentChangesTable();

print "Done.\n";
exit();

?>
