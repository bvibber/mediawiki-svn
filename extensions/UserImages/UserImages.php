<?php

/**
 * Parser hook which generates a gallery of the last X images
 * uploaded by a particular user
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgAutoloadClasses['UserImagesGallery'] = dirname( __FILE__ ) . '/UserImages.class.php';
	$wgExtensionFunctions[] = 'efUserImages';

	$wgExtensionCredits['parserhook'][] = array(
		'name' => 'User Image Gallery',
		'author' => 'Rob Church',
		'description' => 'Generate galleries of user-uploaded images with <code><nowiki><userimage /></nowiki></code>',
	);
	
	/**
	 * Set this to true to disable the parser cache for pages which
	 * contain a <userimages> tag; this keeps the galleries up to date
	 * at the cost of a performance overhead on page views
	 */
	$wgUserImagesNoCache = false;
	
	/**
	 * Extension initialisation function
	 */
	function efUserImages() {
		global $wgMessageCache, $wgParser;
		require_once( dirname( __FILE__ ) . '/UserImages.i18n.php' );
		foreach( efUserImagesMessages() as $lang => $messages )
			$wgMessageCache->addMessages( $messages, $lang );
		$wgParser->setHook( 'userimages', 'efUserImagesRender' );
	}
	
	/**
	 * Extension rendering function
	 *
	 * @param $text Text inside <userimages> tags
	 * @param $args Tag arguments
	 * @param $parser Parent parser
	 * @return string
	 */
	function efUserImagesRender( $text, $args, &$parser ) {
		global $wgUserImagesNoCache;
		if( $wgUserImagesNoCache )
			$parser->disableCache();
		$uig = new UserImagesGallery( $args, $parser );
		return $uig->render();
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	exit();
}

