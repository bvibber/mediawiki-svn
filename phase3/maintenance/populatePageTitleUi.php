<?php

/*
 * Makes the required database updates for page_title_ui
 * 
 * TODO: Also give it the ability to override ui titles,
 * and extract data from a DISPLAYTITLE.
 */

if( !$dontAutoRun ) {
	$options = array( 'help', 'override' );
	$optionsWithArgs = array(  );
	require_once( dirname( __FILE__ ) . '/commandLine.inc' );
	
	$db =& wfGetDB( DB_MASTER );
	if( !$db->fieldExists( 'page', 'page_title_ui' ) ) {
		echo "page.page_title_ui field does not exist\n";
		exit( 1 );
	}
	populate_page_title_ui( $db, array_merge( $optionsWithArgs, $options ) );
}

function populate_page_title_ui( $db, $options ) {
	
	$conditions = array();
	if( !$options['override'] ) $conditions['page_title_ui'] = '';
	
	$db->update( 'page',
		array( "page_title_ui = REPLACE(page_title,'_',' ')" ),
		$conditions, __METHOD__ );
	
	$logged = $db->insert( 'updatelog',
		array( 'ul_key' => 'populate page_title_ui' ),
		__FUNCTION__,
		'IGNORE' );
	$rows = $db->affectedRows();
	if( $logged ) {
		echo "page_title_ui population complete ... [{$rows} rows changed]\n";
		return true;
	} else {
		echo "Could not insert page_title_ui population row.\n";
		return false;
	}
	
}