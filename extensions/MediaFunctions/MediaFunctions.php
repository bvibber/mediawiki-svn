<?php
if ( !defined( 'MEDIAWIKI' ) ) die();
/**
 * Parser functions for MediaWiki providing information
 * about various media files
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 * @version 1.2
 */

$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'MediaFunctions',
	'version' => '1.2',
	'author' => 'Rob Church',
	'url' => 'http://www.mediawiki.org/wiki/Extension:MediaFunctions',
	'description' => 'Parser functions for obtaining information about media files',
	'descriptionmsg' => 'mediafunctions-desc',
);

$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['MediaFunctions'] = $dir . 'MediaFunctions.i18n.php';
$wgAutoloadClasses['MediaFunctions'] = $dir . 'MediaFunctions.class.php';
$wgExtensionFunctions[] = 'efMediaFunctionsSetup';

/**
 * Register function callbacks and add error messages to
 * the message cache
 */
function efMediaFunctionsSetup() {
	global $wgParser;
	$wgParser->setFunctionHook( 'mediamime', array( 'MediaFunctions', 'mediamime' ) );
	$wgParser->setFunctionHook( 'mediasize', array( 'MediaFunctions', 'mediasize' ) );
	$wgParser->setFunctionHook( 'mediaheight', array( 'MediaFunctions', 'mediaheight' ) );
	$wgParser->setFunctionHook( 'mediawidth', array( 'MediaFunctions', 'mediawidth' ) );
	$wgParser->setFunctionHook( 'mediadimensions', array( 'MediaFunctions', 'mediadimensions' ) );
	$wgParser->setFunctionHook( 'mediaexif', array( 'MediaFunctions', 'mediaexif' ) );
}
