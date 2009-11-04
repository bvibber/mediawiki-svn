<?php
/**
 * Usability Initiative SideBySidePreview extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the include file for the SideBySidePreview portion of the
 * UsabilityInitiative extension of MediaWiki.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/UsabilityInitiative/SideBySidePreview/SideBySidePreview.php" );
 *
 * @author Roan Kattouw <roan.kattouw@gmail.com>
 * @license GPL v2 or later
 * @version 0.1.1
 */

/* Configuration */

// Set this to true to simply force SideBySidePreview on everyone
$wgSideBySidePreviewGlobalEnable = false;

// Set this to true to add a preference to the editing section of preferences
// which enables and disables SideBySidePreview (if $wgSideBySidePreviewGlobalEnable, this
// will not do anything)
$wgSideBySidePreviewUserEnable = true;

// Bump the version number every time you change any of the .css/.js files
$wgSideBySidePreviewStyleVersion = 0;

/* Setup */

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'SideBySidePreview',
	'author' => 'Roan Kattouw',
	'version' => '0.1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'sideBysidepreview-desc',
);

// Includes parent extension
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative.php" );

// Adds Autoload Classes
$wgAutoloadClasses['SideBySidePreviewHooks'] =
	dirname( __FILE__ ) . '/SideBySidePreview.hooks.php';

// Adds Internationalized Messages
$wgExtensionMessagesFiles['SideBySidePreview'] =
	dirname( __FILE__ ) . '/SideBySidePreview.i18n.php';

// Registers Hooks
$wgHooks['EditPageBeforeEditToolbar'][] = 'SideBySidePreviewHooks::addPreview';
$wgHooks['GetPreferences'][] = 'SideBySidePreviewHooks::addPreferences';
