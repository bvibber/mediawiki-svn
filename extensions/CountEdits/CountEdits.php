<?php

/**
 * Simple edit counter for small wikis
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */

if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionFunctions[] = 'efCountEdits';
	$wgExtensionCredits['specialpage'][] = array(
		'name' => 'Count Edits',
		'version' => '1.1',
		'author' => 'Rob Church',
		'description' => 'Special page that counts user edits and provides a top-ten contributor list',
		'url' => 'http://www.mediawiki.org/wiki/Extension:CountEdits',
	);

	/* This line will have no effect on pre-1.7 wikis */
	$wgAutoloadClasses['SpecialCountEdits'] = dirname( __FILE__ ) . '/CountEdits.page.php';
	/* However, on pre-1.7 wikis, we can't afford to mess this up */
	if( version_compare( $wgVersion, '1.7alpha', '>=' ) )
		$wgSpecialPages['CountEdits'] = 'SpecialCountEdits';

	/**
	 * Should we show the "most active contributors" list?
	 * This could be expensive for larger wikis
	 */
	$wgCountEditsMostActive = true;

	/**
	 * Extension setup function
	 */
	function efCountEdits() {
		global $wgVersion, $wgMessageCache;
		require_once( dirname( __FILE__ ) . '/CountEdits.i18n.php' );
		if( version_compare( $wgVersion, '1.7alpha', '>=' ) ) {
			foreach( efCountEditsMessages() as $lang => $messages )
				$wgMessageCache->addMessages( $messages, $lang );
		} else {
			$wgMessageCache->addMessages( efCountEditsMessages( true ) );
			require_once( 'SpecialPage.php' );
			require_once( dirname( __FILE__ ) . '/CountEdits.page.php' );
			SpecialPage::addPage( new SpecialCountEdits() );
		}
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	exit( 1 );
}
