<?php

/**
 * Parser hook which generates a gallery of the last X images
 * uploaded by a particular user
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionCredits['parserhook'][] = array( 'name' => 'User Image Gallery', 'author' => 'Rob Church' );
	$wgAutoloadClasses['UserImagesGallery'] = dirname( __FILE__ ) . '/UserImages.class.php';
	$wgExtensionFunctions[] = 'efUserImages';
	
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
		$uig = new UserImagesGallery( $args, $parser );
		return $uig->render();
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	exit();
}

?>