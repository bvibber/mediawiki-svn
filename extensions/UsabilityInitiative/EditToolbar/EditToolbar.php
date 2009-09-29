<?php
/**
 * Usability Initiative EditToolbar extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the include file for the EditToolbar portion of the
 * UsabilityInitiative extension of MediaWiki.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/UsabilityInitiative/EditToolbar/EditToolbar.php" );
 *
 * @author Trevor Parscal <tparscal@wikimedia.org>, Roan Kattouw <roan.kattouw@gmail.com>
 * @license GPL v2 or later
 * @version 0.1.1
 */

/* Configuration */

// Bump the version number every time you change any of the .css/.js files
$wgEditToolbarStyleVersion = 45;

// Set this to true to simply override the stock toolbar for everyone
$wgEditToolbarGlobalEnable = false;

// Set this to true to add a preference to the editing section of preferences
// which enables and disables this toolbar (if $wgEditToolbarGlobalEnable, this
// will not do anything)
$wgEditToolbarUserEnable = true;

// Set this to true to enable content generation dialogs for everyone who gets
// the toolbar
$wgEditToolbarCGDGlobalEnable = false;

// Set this to true to add a preference to the editing section of preferences
// which enabled and disables content generation dialogs
// If $wgEditToolbatCGDGlobalEnable is true, this will be ignored
$wgEditToolbarCGDUserEnable = true;

// Set if we should include a client-side testing script
$wgEditToolbarRunTests = false;

/* Setup */

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'EditToolbar',
	'author' => 'Trevor Parscal',
	'version' => '0.1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'edittoolbar-desc',
);

// Includes parent extension
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative.php" );

// Adds Autoload Classes
$wgAutoloadClasses['EditToolbarHooks'] =
	dirname( __FILE__ ) . '/EditToolbar.hooks.php';

// Adds Internationalized Messages
$wgExtensionMessagesFiles['EditToolbar'] =
	dirname( __FILE__ ) . '/EditToolbar.i18n.php';

// Registers Hooks
$wgHooks['EditPageBeforeEditToolbar'][] = 'EditToolbarHooks::addToolbar';
$wgHooks['GetPreferences'][] = 'EditToolbarHooks::addPreferences';
