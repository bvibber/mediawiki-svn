<?php

/**
 * Special page used to wipe the OBJECTCACHE table
 * I use it on test wikis when I'm fiddling about with things en masse that could be cached
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 * @licence Public domain
 */
 
if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	exit( 1 );
}

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/PurgeCache_body.php', 'PurgeCache', 'PurgeCache' );

$wgAvailableRights[] = 'purgecache';
$wgGroupPermissions['developer']['purgecache'] = true;


