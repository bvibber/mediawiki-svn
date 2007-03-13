<?php
/**
 * News extension - shows recent changes on a wiki page.
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Daniel Kinzler, brightbyte.de
 * @copyright © 2007 Daniel Kinzler
 * @licence GNU General Public Licence 2.0 or later
 */


if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

$wgExtensionCredits['other'][] = array( 
	'name' => 'News', 
	'author' => 'Daniel Kinzler, brightbyte.de', 
	'url' => 'http://mediawiki.org/wiki/Extension:News',
	'description' => 'shows recent changes on a wiki page',
);

$wgExtensionFunctions[] = "wfNewsExtension";

$wgAutoloadClasses['NewsRenderer'] = dirname( __FILE__ ) . '/NewsRenderer.php';

function wfNewsExtension() {
    global $wgParser;
    $wgParser->setHook( "news", "newsxRenderNews" );
}


function newsxRenderNews( $templatetext, $argv, &$parser ) {
    $renderer = new NewsRenderer($templatetext, $argv, $parser);
    return $renderer->renderNews();
}

?>