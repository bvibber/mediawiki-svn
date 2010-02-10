<?php
/**
 * Initialization file for the Storyboard extension.
 * Extension documentation: http://www.mediawiki.org/wiki/Extension:Storyboard
 *
 * @file Storyboard.php
 * @ingroup Storyboard
 *
 * @author Jeroen De Dauw
 */

/**
 * This documenation group collects source code files belonging to Storyboard.
 *
 * Please do not use this group name for other code.
 *
 * @defgroup Storyboard Storyboard
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

define( 'Storyboard_VERSION', '0' );

$egStoryboardScriptPath = $wgScriptPath . '/extensions/Storyboard';
$egStoryboardDir = dirname( __FILE__ ) . '/';

// Include the settings file.
require_once( $egStoryboardDir . 'Storyboard_Settings.php' );

// Register the initialization function of Storyboard.
$wgExtensionFunctions[] = 'efStoryboardSetup';

// Register the initernationalization and aliasing files of Storyboard.
$wgExtensionMessagesFiles['Storyboard'] = $egStoryboardDir . 'Storyboard.i18n.php';
$wgExtensionAliasesFiles['Storyboard'] = $egStoryboardDir . 'Storyboard.alias.php';

// Load and register the StoryReview special page and register it's group.
$wgAutoloadClasses['SpecialStoryReview'] = $egStoryboardDir . 'StoryReview_body.php';
$wgSpecialPages['StoryReview'] = 'SpecialStoryReview';
$wgSpecialPageGroups['StoryReview'] = 'contribution';

/**
 * Initialization function for the Storyboard extension.
 */
function efStoryboardSetup() {
	global $wgExtensionCredits;

	wfLoadExtensionMessages( 'Storyboard' );

	$wgExtensionCredits['parserhook'][] = array(
		'path' => __FILE__,
		'name' => wfMsg( 'storyboard-name' ),
		'version' => Storyboard_VERSION,
		'author' => array( '[http://bn2vs.com Jeroen De Dauw]' ),
		'url' => 'http://www.mediawiki.org/wiki/Extension:Storyboard',
		'description' =>  wfMsg( 'storyboard-desc' ),
		'descriptionmsg' => 'storyboard-desc',
	);
}
