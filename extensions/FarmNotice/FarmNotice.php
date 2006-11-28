<?php

/**
 * Extension allows adding a global site notice, editable on one wiki
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionCredits['other'][] = array( 'name' => 'Farm Notice', 'author' => 'Rob Church' );
	$wgAutoloadClasses['FarmNotice'] = dirname( __FILE__ ) . '/FarmNotice.class.php';

	/**
	 * Is this wiki the source wiki? If so, updates to farmnotice messages here will
	 * cause invalidation of the farm notice cache
	 */
	$wgFarmNoticeIsSource = false;
	
	/**
	 * URL to the source wiki
	 * This should end in index.php without going via URL rewrites
	 */
	$wgFarmNoticeSourceUrl = '';

	new FarmNotice();
	
} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	exit( 1 );
}