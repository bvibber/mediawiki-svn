<?php
/**
 * Usability Initiative WikiEditorCode extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the include file for the WikiEditorCode portion of the
 * UsabilityInitiative extension of MediaWiki.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/UsabilityInitiative/WikiEditorCode/WikiEditorCode.php" );
 *
 * @author Trevor Parscal <tparscal@wikimedia.org>
 * @license GPL v2 or later
 * @version 0.0.1
 */

/* Configuration */

// Set this to true to simply force WikiEditorCode on everyone
$wgWikiEditorCodeGlobalEnable = false;

// Set this to true to add a preference to the editing section of preferences
// which enables and disables WikiEditorCode (if $wgWikiEditorCodeGlobalEnable, this
// will not do anything)
$wgWikiEditorCodeUserEnable = true;

// Bump the version number every time you change any of the .css/.js files
$wgWikiEditorCodeStyleVersion = 1;

/* Setup */

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'WikiEditorCode',
	'author' => 'Trevor Parscal',
	'version' => '0.0.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'wikieditorcode-desc',
);

// Includes parent extension
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative.php" );

// Adds Autoload Classes
$wgAutoloadClasses['WikiEditorCodeHooks'] =
	dirname( __FILE__ ) . '/WikiEditorCode.hooks.php';

// Adds Internationalized Messages
$wgExtensionMessagesFiles['WikiEditorCode'] =
	dirname( __FILE__ ) . '/WikiEditorCode.i18n.php';

// Registers Hooks
$wgHooks['EditPageBeforeEditToolbar'][] = 'WikiEditorCodeHooks::addCode';
$wgHooks['GetPreferences'][] = 'WikiEditorCodeHooks::addPreferences';
