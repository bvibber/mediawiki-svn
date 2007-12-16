<?php

/**
 * Extension adds improved patrolling interface
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0
 */

if( defined( 'MEDIAWIKI' ) ) {

	$wgSpecialPages['Patrol'] = 'Patroller';
	$wgAutoloadClasses['Patroller'] = dirname( __FILE__ ) . '/Patroller.class.php';
	$wgHooks['LoadAllMessages'][] = 'Patroller::initialiseMessages';

	$wgExtensionCredits['specialpage'][] = array(
		'name' => 'Patroller',
		'author' => 'Rob Church',
		'description' => 'Enhanced patrolling interface with workload sharing',
		'url' => 'http://www.mediawiki.org/wiki/Extension:Patroller',
	);

	$wgAvailableRights[] = 'patroller';
	$wgGroupPermissions['sysop']['patroller'] = true;
	$wgGroupPermissions['patroller']['patroller'] = true;

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	exit( 1 );
}


