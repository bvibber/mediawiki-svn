<?

# Database conversion (from May 2002 format).  Assumes that
# the "buildtables.sql" script has been run to create the new
# empty tables, and that the old tables have been read in from
# a database dump, renamed "old_*".

include_once( "Setup.php" );
include_once( "./rebuildlinksfunction.php" );
$wgTitle = Title::newFromText( "Rebuild links script" );
set_time_limit(0);

$wgDBname			= "wikidb";
$wgDBuser			= "wikiadmin";
$wgDBpassword		= "adminpass";
$wgUploadDirectory	= "/usr/local/apache/htdocs/upload";

rebuildLinkTablesPass1();
rebuildLinkTablesPass2();

print "Done.\n";
exit();

?>
