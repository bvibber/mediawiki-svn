<?php

$maintenance = '/home/wikipedia/common/php/maintenance';
$scriptDir = dirname( __FILE__ );

require_once( "$maintenance/commandLine.inc" );
require_once( "$maintenance/backup.inc" );

if ( !isset( $args[1] ) ) {
	print "Usage: php -n incubator-transfer.php incubatorwiki <prefix> <destination_db>\n";
	exit( 1 );
}
list( $prefix, $destDB ) = $args;

# Remove trailing slash, we'll handle that special case ourselves
if ( substr( $prefix, -1 ) == '/' ) {
	$prefix = substr( $prefix, 0, -1 );
}
$prefix = ucfirst( $prefix );
print "Prefix: $prefix, destination: $destDB\n";

# Initialise Dumper object
$dumper = new BackupDumper( array( '--output=file:transfer.xml' ) );

# Get page list
$dbr =& wfGetDB( DB_SLAVE );
$res = $dbr->select( 'page', array( 'page_namespace', 'page_title' ), 
	array( "page_title LIKE '" . $dbr->escapeLike( $prefix ) . "/%'" ),
	__METHOD__ );

# Start the page list with a main page
$title = Title::newFromText( $prefix );
$dumper->pages = array( $title->getPrefixedDBkey() );

# Add the rest
while ( $row = $dbr->fetchObject( $res ) ) {
	$title = Title::makeTitle( $row->page_namespace, $row->page_title );
	$dumper->pages[] = $title->getPrefixedDBkey();
}
$dbr->freeResult( $res );

# Save a list file
file_put_contents( 'transfer.lst', implode( "\n", $dumper->pages ) );

# Export the XML
$dumper->dump( MW_EXPORT_FULL );

# Import it into the destination wiki
passthru( "php -n " . wfEscapeShellArg( "$scriptDir/_import.php", $destDB, $prefix ) );

?>
