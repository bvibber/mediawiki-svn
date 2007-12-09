<?php

/**
 * Extension adds "next" and "previous" alphabetic paging links to
 * the top of articles
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */
if( defined( 'MEDIAWIKI' ) ) {

	$wgAutoloadClasses['BackAndForth'] = dirname( __FILE__ ) . '/BackAndForth.class.php';
	$wgExtensionFunctions[] = 'efBackAndForth';
	$wgExtensionCredits['other'][] = array(
		'name' => 'Back and Forth',
		'version' => '1.1',
		'author' => 'Rob Church',
		'url' => 'http://www.mediawiki.org/wiki/Extension:Back-and-Forth',
		'description' => 'Adds \'next\' and \'previous\' alphabetic paging links to the top of articles',
	);

	/**
	 * Extension setup function
	 */
	function efBackAndForth() {
		global $wgMessageCache, $wgHooks;
		require_once( dirname( __FILE__ ) . '/BackAndForth.i18n.php' );
		foreach( efBackAndForthMessages() as $lang => $messages )
			$wgMessageCache->addMessages( $messages, $lang );
		$wgHooks['ArticleViewHeader'][] = 'BackAndForth::viewHook';
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	exit( 1 );
}
