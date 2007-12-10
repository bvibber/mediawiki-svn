<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( "Not a valid entry point\n" );
}

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'MakeDBError',
	'version'     => '1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:MakeDBError',
	'author' => 'Tim Starling',
	'description' => 'Makes a database error with an invalid query',
);

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/MakeDBError_body.php', 'MakeDBError', 'MakeDBErrorPage' );
