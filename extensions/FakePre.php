<?php

/**
 * Simple parser hook that adds a <fakepre> tag, which leaves whitespace in text
 * intact but allows wiki text to be used as normal
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionFunctions[] = 'efFakePre';
	$wgExtensionCredits['parserhook'][] = array( 'name' => 'FakePre', 'author' => 'Rob Church' );
	
	/**
	 * Setup function; register the hook with the parser*
	 */
	function efFakePre() {
		global $wgParser;
		$wgParser->setHook( 'fakepre', 'efRenderFakePre' );
	}
	
	/**
	 * Main rendering function; handle whitespace ourselves, then
	 * pass off the text to the parser to be dealt with
	 *
	 * @param $input Content between the tags
	 * @param $args Attributes of the tag (unused here)
	 * @param $parser Reference to the parser instance
	 * @return XHTML
	 */
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