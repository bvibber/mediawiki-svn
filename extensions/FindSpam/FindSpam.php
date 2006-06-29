<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( "Not a valid entry point\n" );
}

# This is a simple example of a special page module
# Given a string in UTF-8, it converts it to HTML entities suitable for 
# an ISO 8859-1 web page.

global $wgAvailableRights, $wgGroupPermissions;
$wgAvailableRights[] = 'findspam';
$wgGroupPermissions['sysop']['findspam'] = true;

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/FindSpam_body.php', 'FindSpam', 'FindSpamPage' );
?>
