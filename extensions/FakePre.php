<?php

/**
 * Simple parser hook that adds a <fakepre> tag, which leaves whitespace in text
 * intact but allows wiki text to be used as normal
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionFunctions[] = 'efFakePre';
	$wgExtensionCredits['parser'][] = array( 'name' => 'FakePre', 'author' => 'Rob Church' );
	
	function efFakePre() {
		global $wgParser;
		$wgParser->setHook( 'fakepre', 'efRenderFakePre' );
	}
	
	function efRenderFakePre( $input, $args, &$parser ) {
		$text = str_replace( "\n", '<br />', $input );
		$output = $parser->parse( $text, $parser->mTitle, $parser->mOptions, false, false );
		return $output->getText();
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( -1 );
}

?>