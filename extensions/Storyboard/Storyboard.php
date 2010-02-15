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
$wgAutoloadClasses['SpecialStoryReview'] = $egStoryboardDir . 'specials/StoryReview_body.php';
$wgSpecialPages['StoryReview'] = 'SpecialStoryReview';
$wgSpecialPageGroups['StoryReview'] = 'contribution';

// Load the tag extension classes.
$wgAutoloadClasses['TagStoryboard'] = $egStoryboardDir . 'tags/Storyboard_body.php';
$wgAutoloadClasses['TagStorysubmission'] = $egStoryboardDir . 'tags/Storysubmission_body.php';

// Register the tag extensions.
// Avoid unstubbing $wgParser on setHook() too early on modern (1.12+) MW versions, as per r35980.
if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
	$wgHooks['ParserFirstCallInit'][] = 'efStoryboardStoryboardSetup';
	$wgHooks['ParserFirstCallInit'][] = 'efStoryboardStorysubmissionSetup';
} else { // Otherwise do things the old fashioned way.
	$wgExtensionFunctions[] = 'efStoryboardStoryboardSetup';
	$wgExtensionFunctions[] = 'efStoryboardStorysubmissionSetup';
}

// Hook for db updates.
$wgHooks['LoadExtensionSchemaUpdates'][] = 'efStoryboardSchemaUpdate';

/**
 * The 'storyboard' permission key can be given out to users
 * to enable them to review, edit, publish, and hide stories.
 *
 * By default, only sysops will be able to do this.
 */
$wgGroupPermissions['*'            ]['storyboard'] = false;
$wgGroupPermissions['user'         ]['storyboard'] = false;
$wgGroupPermissions['autoconfirmed']['storyboard'] = false;
$wgGroupPermissions['bot'          ]['storyboard'] = false;
$wgGroupPermissions['sysop'        ]['storyboard'] = true;
$wgAvailableRights[] = 'storyboard';

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

function efStoryboardSchemaUpdate() {
	global $wgExtNewTables, $egStoryboardDir;
	
	$wgExtNewTables[] = array(
		'storyboard',
		$egStoryboardDir . 'Storyboard.sql'
	);
}

function efStoryboardStoryboardSetup() {
	global $wgParser;
	$wgParser->setHook( 'storyboard', array('TagStoryboard', 'render') );
    return true;
}

function efStoryboardStorysubmissionSetup() {
	global $wgParser;
	$wgParser->setHook( 'storysubmission', array('TagStorysubmission', 'render') );
    return true;
}
