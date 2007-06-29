<?php

/**
 * Extension allows users to export their watchlist into
 * list format and import from a list into their watchlist
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */
if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionCredits['specialpage'][] = array(
		'name' => 'Export Watchlist',
		'author' => 'Rob Church',
		'url' => 'http://www.mediawiki.org/wiki/Extension:Export_Watchlist',
		'description' => 'Allows users to export/import watchlists as XML files',
	);
	
	$wgAutoloadClasses['SpecialExportWatchlist'] = dirname( __FILE__ ) . '/SpecialExportWatchlist.php';
	$wgAutoloadClasses['SpecialImportWatchlist'] = dirname( __FILE__ ) . '/SpecialImportWatchlist.php';
	$wgSpecialPages['ExportWatchlist'] = 'SpecialExportWatchlist';
	$wgSpecialPages['ImportWatchlist'] = 'SpecialImportWatchlist';
	$wgExtensionFunctions[] = 'efExportWatchlist';

	/**
	 * Register messages with the message cache
	 */
	function efExportWatchlist() {
		global $wgMessageCache;
		require_once( dirname( __FILE__ ) . '/ExportWatchlist.i18n.php' );
		foreach( efExportWatchlistMessages() as $lang => $messages )
			$wgMessageCache->addMessages( $messages, $lang );
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standlone.\n" );
	exit( 1 );
}

?>