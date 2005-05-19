<?php

if( !function_exists( 'domxml_open_mem' ) ) {
	die( 'Install the domxml module...' );
	#dl( 'domxml.so' );
	#dl( '/usr/lib/php/extensions/no-debug-non-zts-20020429/domxml.so' );
}

$options = array( 'dry-run' );
require_once( 'commandLine.inc' );
#require_once( 'extensions/OAI/OAIHarvest.php' );


/**
 * Persistent data:
 * - Source repo URL
 * - Last seen update timestamp
 */
$harvester = new OAIHarvester( $oaiSourceRepository );

$dbr =& wfGetDB( DB_SLAVE );
$highest = $dbr->selectField( 'cur', 'MAX(cur_timestamp)' );
if( $highest ) {
	$lastUpdate = wfTimestamp( TS_MW, $highest );
} else {
	# Starting from an empty database!
	$lastUpdate = '19700101000000';
}

$callback = 'showUpdates';
function showUpdates( $record ) {
	global $options;
	$record->dump();
	if( !isset( $options['dry-run'] ) ) {
		$record->apply();
	}
}

$result = $harvester->listUpdates( $lastUpdate, $callback );
if( OAIError::isError( $result ) ) {
	die( $result->toString() );
}

?>