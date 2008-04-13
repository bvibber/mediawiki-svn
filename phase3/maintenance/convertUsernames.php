<?php

/*
 * Converts usernames in the given rows from the old
 * space form to the new underscore form.
 * 
 */

if( !$dontAutoRun ) {
	$options = array( 'help' );
	require_once( dirname( __FILE__ ) . '/commandLine.inc' );
	
	$db =& wfGetDB( DB_MASTER );
	convert_usernames( $db, array_merge( $optionsWithArgs, $fields ) );
}

function convert_usernames( $db, $fields ) {
	
	foreach( $fields as $field ) {
		$db->update( $field[0],
			array( "{$field[1]} = REPLACE({$field[1]},' ','_')" ),
			array( 1 => 1), __METHOD__ );
		
		$logged = $db->insert( 'updatelog',
			array( 'ul_key' => "schema_change {$field[0]}.{$field[1]}" ),
			__FUNCTION__,
			'IGNORE' );
		$rows = $db->affectedRows();
		if( $logged ) {
			echo "{$field[0]}.{$field[1]} convert complete ... [{$rows} rows changed]\n";
		} else {
			echo "Could not insert {$field[0]}.{$field[1]} convert row.\n";
		}
	}
}