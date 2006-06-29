<?php

/**
 * Extension adds improved patrolling interface
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	require_once( 'Patroller.i18n.php' );

	$wgSpecialPages['Patrol'] = 'Patroller';
	$wgAutoloadClasses['Patroller'] = dirname( __FILE__ ) . '/Patroller.class.php';

	$wgExtensionFunctions[] = 'efPatroller';
	$wgExtensionCredits['specialpage'][] = array( 'name' => 'Patroller', 'author' => 'Rob Church', 'url' => 'http://www.mediawiki.org/wiki/Patroller' );
	
	$wgAvailableRights[] = 'patroller';
	$wgGroupPermissions['sysop']['patroller'] = true;
	
	function efPatroller() {
		global $wgMessageCache;
		efPatrollerAddMessages( $wgMessageCache );
	}
	
} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

?>