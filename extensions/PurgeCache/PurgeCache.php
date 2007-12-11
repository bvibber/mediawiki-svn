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

$wgExtensionCredits['specialpage'][] = array(
	'name'        => 'PurgeCache',
	'version'     => '1.1',
	'url'         => 'http://www.mediawiki.org/wiki/Extension:PurgeCache',
	'author'      => 'Rob Church',
	'email'       => 'robchur@gmail.com',
	'description' => '[[Special:PurgeCache|Special page]] used to wipe the OBJECTCACHE table',
);

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/PurgeCache_body.php', 'PurgeCache', 'PurgeCache' );

$wgAvailableRights[] = 'purgecache';
$wgGroupPermissions['developer']['purgecache'] = true;
