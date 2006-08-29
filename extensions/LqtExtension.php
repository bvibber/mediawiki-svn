<?php

/**
* @package MediaWiki
* @subpackage Extensions
* @author David McCabe <davemccabe@gmail.com>
* @licence GPL2
*/

// This would be replaced by an actual dispatching system.

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( -1 );
}
else {

	require_once( 'LqtController.php' );
	require_once( 'LqtStandardViews.php' );

	$wgExtensionFunctions[] = 'lqtInitialize';

	function lqtSpecialCaseHook( &$title, &$output, $request ) {

		$c = new LqtDispatch(null);
		return $c->execute($title);

	}

	function lqtInitialize() {
		global $wgMessageCache, $wgHooks;
		$wgMessageCache->addMessage( 'lq', 'LiquidThreads' );
		$wgHooks['SpecialCase'][] = 'lqtSpecialCaseHook';
	}
}

