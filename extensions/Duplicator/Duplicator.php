<?php

/**
 * Special page which creates independent copies of articles, retaining
 * separate histories
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionCredits['specialpage'][] = array( 'name' => 'Duplicator', 'author' => 'Rob Church' );
	$wgExtensionFunctions[] = 'efDuplicator';
	
	$wgAutoloadClasses['SpecialDuplicator'] = dirname( __FILE__ ) . '/Duplicator.page.php';
	$wgSpecialPages['Duplicator'] = 'SpecialDuplicator';
	
	/**
	 * Pages with more than this number of revisions can't be duplicated
	 */
	$wgDuplicatorRevisionLimit = 1000;
	
	/**
	 * Extension setup function
	 */
	function efDuplicator() {
		global $wgMessageCache;
		require_once( dirname( __FILE__ ) . '/Duplicator.i18n.php' );
		foreach( efDuplicatorMessages() as $lang => $messages )
			$wgMessageCache->addMessages( $messages, $lang );
	}

} else {
	echo( "This file is an extension to the MediaWiki software, and cannot be used standalone.\n" );
	exit( 1 );
}

?>