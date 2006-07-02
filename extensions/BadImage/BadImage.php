<?php

/**
 * Extension to extend the bad image list capabilities of MediaWiki
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence Copyright holder allows use of the code for any purpose
 */

if( defined( 'MEDIAWIKI' ) ) {

	global $wgExtensionCredits, $wgAutoloadClasses, $wgHooks;
	
	$wgExtensionCredits['other'][] = array( 'name' => 'Bad Image List', 'author' => 'Rob Church' );
	$wgAutoloadClasses['BadImageList'] = dirname( __FILE__ ). '/BadImage.class.php';

	$wgHooks['BadImage'][] = 'efBadImage';
	
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

?>