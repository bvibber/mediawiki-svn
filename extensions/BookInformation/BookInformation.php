<?php

/**
 * Extension allows Special:Booksource to obtain basic details
 * about a book from available web services
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */

if( defined( 'MEDIAWIKI' ) ) {

	$wgAutoloadClasses['BookInformation'] = dirname( __FILE__ ) . '/drivers/Worker.php';
	$wgAutoloadClasses['BookInformationCache'] = dirname( __FILE__ ) . '/drivers/Cache.php';
	$wgAutoloadClasses['BookInformationDriver'] = dirname( __FILE__ ) . '/drivers/Driver.php';
	$wgAutoloadClasses['BookInformationResult'] = dirname( __FILE__ ) . '/drivers/Result.php';

	$wgAutoloadClasses['BookInformationAmazon'] = dirname( __FILE__ ) . '/drivers/Amazon.php';
	$wgAutoloadClasses['BookInformationIsbnDb'] = dirname( __FILE__ ) . '/drivers/IsbnDb.php';

	$wgExtensionFunctions[] = 'efBookInformationSetup';
	$wgHooks['BookInformation'][] = 'efBookInformation';
	$wgHooks['SkinTemplateSetupPageCss'][] = 'efBookInformationCss';

	$wgExtensionCredits['other'][] = array(
		'name' => 'Book Information',
		'version' => '1.1',
		'url' => 'http://www.mediawiki.org/wiki/Extension:BookInformation',
		'author' => 'Rob Church',
		'description' => 'Expands [[Special:Booksources]] with information from a web service',
	);

	/**
	 * Enables caching of results when the "bookinfo" table is available
	 */
	$wgBookInformationCache = false;

	/**
	 * The book information driver in use
	 * (Please see docs/drivers.htm for more information)
	 */
	$wgBookInformationDriver = 'Amazon';

	/**
	 * Service identification/authentication information
	 * (Consult driver documentation for specifics)
	 */
	$wgBookInformationService = array();

	/**
	 * Extension setup function
	 */
	function efBookInformationSetup() {
		global $wgMessageCache;
		require_once( dirname( __FILE__ ) . '/BookInformation.i18n.php' );
		foreach( efBookInformationMessages() as $lang => $messages )
			$wgMessageCache->addMessages( $messages, $lang );
	}

	/**
	 * Hook handling function
	 *
	 * @param string $isbn ISBN to be queried
	 * @param OutputPage $output OutputPage to use
	 */
	function efBookInformation( $isbn, $output ) {
		BookInformation::show( $isbn, $output );
		return true;
	}

	/**
	 * Add extra CSS to the skin
	 *
	 * @param string $css Additional CSS
	 * @return mixed
	 */
	function efBookInformationCss( &$css ) {
		global $wgTitle;
		if( $wgTitle->isSpecial( 'Booksources' ) ) {
			$file = dirname( __FILE__ ) . '/BookInformation.css';
			$css .= "/*<![CDATA[*/\n" . htmlspecialchars( file_get_contents( $file ) ) . "\n/*]]>*/";
		}
		return true;
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	exit( 1 );
}
