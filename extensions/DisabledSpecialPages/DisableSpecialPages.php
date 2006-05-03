<?php

/**
 * Extension allows wiki administrators to make a special page unavailable
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0 or later
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	require_once( 'SpecialPage.php' );
	$wgExtensionFunctions[] = 'efDisableSpecialPages';
	$wgExtensionCredits['other'][] = array( 'name' => 'Disable Special Pages', 'author' => 'Rob Church' );

	# Titles (minus the Special prefix) of special pages to disable
	# Special:Userlogin, Special:Userlogout and Special:Search can never
	# be disabled
	$wgDisabledSpecialPages = array();

	function efDisableSpecialPages() {
		global $wgSpecialPages, $wgDisabledSpecialPages;
		$whitelist = array( 'Search', 'Userlogin', 'Userlogout' );
		foreach( $wgDisabledSpecialPages as $page )
			if( !array_search( $page, $whitelist ) && isset( $wgSpecialPages[ $page ] ) )
				SpecialPage::removePage( $page );
		}
	}
	
} else {
	echo( "This file is an extension to the MediaWiki software, and cannot be used standalone.\n" );
	die( -1 );
}

?>