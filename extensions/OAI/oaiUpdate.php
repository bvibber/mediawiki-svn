<?php

if( !class_exists( 'DOMAttr' ) ) {
	die( 'Requires PHP 5 with the DOM module enabled...' );
}

require_once( 'commandLine.inc' );
//require_once( "$IP/extensions/OAI/OAIHarvest.php" );


/**
 * Persistent data:
 * - Source repo URL
 * - Last seen update timestamp
 */
$harvester = new OAIHarvester( $oaiSourceRepository );

$dbr =& wfGetDB( DB_SLAVE );
$highest = $dbr->selectField( 'revision', 'MAX(rev_timestamp)' ); // FIXME!
if( $highest ) {
	$lastUpdate = wfTimestamp( TS_MW, $highest );
} else {
	# Starting from an empty database!
	$lastUpdate = '19700101000000';
}


if( isset( $options['debug'] ) ) {
	$callback = 'debugUpdates';
	function debugUpdates( $record ) {
		$record->dump();
		var_dump( $record );
	}
} elseif( isset( $options['dry-run'] ) ) {
	$callback = 'showUpdates';
	function showUpdates( $record ) {
		$record->dump();
	}
} else {
	$callback = 'applyUpdates';
	function applyUpdates( $record ) {
		$record->dump();
		$record->apply();
	}
}


$result = $harvester->listUpdates( $lastUpdate, $callback );

?>