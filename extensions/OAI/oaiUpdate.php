<?php

if( !class_exists( 'DOMAttr' ) ) {
	echo
		"Requires PHP 5 with the DOM module enabled.\n" .
		"\n" .
		"Although enabled by default in most PHP configurations, this module\n" .
		"is sometimes shipped in a separate package by Linux distributions.\n" .
		"\n" .
		"Fedora 6 users, please try:\n" .
		"    yum install php-xml\n" .
		"\n";
	exit( 1 );
}

$base = dirname( dirname( dirname( __FILE__ ) ) );
require_once( "$base/maintenance/commandLine.inc" );


/**
 * Persistent data:
 * - Source repo URL
 * - Last seen update timestamp
 */
$harvester = new OAIHarvester( $oaiSourceRepository );

if( isset( $options['from'] ) ) {
	$lastUpdate = wfTimestamp( TS_MW, $options['from'] );
} else {
	$dbr = wfGetDB( DB_SLAVE );
	$highest = $dbr->selectField( 'revision', 'MAX(rev_timestamp)' ); // FIXME!
	if( $highest ) {
		$lastUpdate = wfTimestamp( TS_MW, $highest );
	} else {
		# Starting from an empty database!
		$lastUpdate = '19700101000000';
	}
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

