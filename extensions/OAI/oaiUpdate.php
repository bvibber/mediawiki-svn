<?php

if( !function_exists( 'domxml_open_mem' ) ) {
	die( 'Install the domxml module...' );
	#dl( 'domxml.so' );
	#dl( '/usr/lib/php/extensions/no-debug-non-zts-20020429/domxml.so' );
}

require_once( 'commandLine.inc' );
#require_once( 'extensions/OAI/OAIHarvest.php' );


/**
 * Persistent data:
 * - Source repo URL
 * - Last seen update timestamp
 */
$harvester = new OAIHarvester( $oaiSourceRepository );

$dbr =& wfGetDB( DB_SLAVE );
$lastUpdate = wfTimestamp( TS_MW, $dbr->selectField( 'cur', 'MAX(cur_timestamp)' ) );

$callback = 'showUpdates';
function showUpdates( $record ) {
	$record->dump();
	$record->apply();
}

$result = $harvester->listUpdates( $lastUpdate, $callback );
if( OAIError::isError( $result ) ) {
	die( $result->toString() );
}

?>