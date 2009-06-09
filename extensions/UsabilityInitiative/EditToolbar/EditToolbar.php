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
 * Usage: This file is included automatically by ../UsabilityInitiative.php
 *
 * @author Trevor Parscal <tparscal@wikimedia.org>
 * Allow "or a later version" here?
 * @license GPL v2
 * @version 0.1.1
 */

// Shortcut to this extension directory
$dir = dirname( __FILE__ ) . '/';

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'EditToolbar',
	'author' => 'Trevor Parscal',
	'version' => '0.1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'toolbar-desc',
);

// Bump the version number every time you change any of the .css/.js files
$wgEditToolbarStyleVersion = 1;

// Set this to true to simply override the stock toolbar for everyone
$wgEditToolbarGlobalEnable = false;

// Set this to true to add a preference to the editing section of preferences
// which enables and disables this toolbar (if $wgEditToolbarGlobalEnable, this
// will not do anything)
$wgEditToolbarUserEnable = true;

// Autoload Classes
$wgAutoloadClasses['EditToolbarHooks'] = $dir . 'EditToolbar.hooks.php';

// Internationalization
$wgExtensionMessagesFiles['EditToolbar'] = $dir . 'EditToolbar.i18n.php';

// Register toolbar interception
$wgHooks['EditPageBeforeEditToolbar'][] = 'EditToolbarHooks::intercept';

// Register preferences customization
$wgHooks['GetPreferences'][] = 'EditToolbarHooks::addPreferences';

// Register ajax add script hook
$wgHooks['AjaxAddScript'][] = 'EditToolbarHooks::addJS';

// Register css add script hook
$wgHooks['BeforePageDisplay'][] = 'EditToolbarHooks::addCSS';
