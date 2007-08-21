<?php

/**
 * Parser functions for MediaWiki providing information
 * about various media files
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 * @version 1.1
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgHooks['LanguageGetMagic'][] = 'efMediaFunctionsGetMagic';
	$wgExtensionFunctions[] = 'efMediaFunctionsSetup';
	$wgAutoloadClasses['MediaFunctions'] = dirname( __FILE__ ) . '/MediaFunctions.class.php';
	$wgExtensionCredits['parserhook'][] = array(
		'name' => 'MediaFunctions',
		'author' => 'Rob Church',
		'url' => 'http://www.mediawiki.org/wiki/Extension:MediaFunctions',
		'description' => 'Parser functions for obtaining information about media files',
	);	
	
	/**
	 * Register function callbacks and add error messages to
	 * the message cache
	 */
	function efMediaFunctionsSetup() {
		global $wgParser, $wgMessageCache;
		$wgParser->setFunctionHook( 'mediamime', array( 'MediaFunctions', 'mediamime' ) );
		$wgParser->setFunctionHook( 'mediasize', array( 'MediaFunctions', 'mediasize' ) );
		$wgParser->setFunctionHook( 'mediaheight', array( 'MediaFunctions', 'mediaheight' ) );
		$wgParser->setFunctionHook( 'mediawidth', array( 'MediaFunctions', 'mediawidth' ) );
		$wgParser->setFunctionHook( 'mediadimensions', array( 'MediaFunctions', 'mediadimensions' ) );
		$wgParser->setFunctionHook( 'mediaexif', array( 'MediaFunctions', 'mediaexif' ) );
		require_once( dirname( __FILE__ ) . '/MediaFunctions.i18n.php' );
		foreach( efMediaFunctionsMessages() as $lang => $messages )
			$wgMessageCache->addMessages( $messages, $lang );
	}
	
	/**
	 * Associate magic words with synonyms
	 *
	 * @param array $words Magic words
	 * @param string $lang Language code
	 * @return bool
	 */
	function efMediaFunctionsGetMagic( &$words, $lang ) {
		require_once( dirname( __FILE__ ) . '/MediaFunctions.i18n.php' );
		foreach( efMediaFunctionsWords( $lang ) as $word => $trans )
			$words[$word] = $trans;
		return true;
	}

} else {
	echo( "This file is an extension to the MediaWiki software, and cannot be used standalone.\n" );
	exit( 1 );
}