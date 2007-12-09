<?php

/**
 * Extension to extend the bad image list capabilities of MediaWiki
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence Copyright holder allows use of the code for any purpose
 */

if( defined( 'MEDIAWIKI' ) ) {

	global $wgAutoloadClasses, $wgSpecialPages;

	$wgAutoloadClasses['BadImageList'] = dirname( __FILE__ ). '/BadImage.class.php';
	$wgAutoloadClasses['BadImageManipulator'] = dirname( __FILE__ ) . '/BadImage.page.php';

	$wgSpecialPages['Badimages'] = 'BadImageManipulator';
	$wgExtensionCredits['other'][] = array(
		'name' => 'Bad Image List',
		'version' => '1.1',
		'author' => 'Rob Church',
		'url' => 'http://www.mediawiki.org/wiki/Extension:Bad_Image_List',
		'description' => 'Extends the bad image list capabilities',
	);
	$wgExtensionFunctions[] = 'efBadImageSetup';

	$wgAvailableRights[] = 'badimages';
	$wgGroupPermissions['sysop']['badimages'] = true;

	/** Set this to false to disable caching results with shared memory caching */
	$wgBadImageCache = true;

	function efBadImageSetup() {
		global $wgMessageCache;
		require_once( dirname( __FILE__ ) . '/BadImage.i18n.php' );
		foreach( efBadImageMessages() as $lang => $messages )
			$wgMessageCache->addMessages( $messages, $lang );
		global $wgHooks, $wgLogTypes, $wgLogNames, $wgLogHeaders, $wgLogActions;
		$wgHooks['BadImage'][] = 'efBadImage';
		$wgLogTypes[] = 'badimage';
		$wgLogNames['badimage'] = 'badimages-log-name';
		$wgLogHeaders['badimage'] = 'badimages-log-header';
		$wgLogActions['badimage/add']  = 'badimages-log-add';
		$wgLogActions['badimage/remove'] = 'badimages-log-remove';
	}

	function efBadImage( $image, &$bad ) {
		if( BadImageList::check( $image ) ) {
			$bad = true;
			return false;
		} else {
			return true;
		}
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}
