<?php
/**
 * Usability Initiative Toolbar extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the include file for the Toolbar portion of the
 * UsabilityInitiative extension of MediaWiki.
 *
 * Usage: This file is included automatically by ../UsabilityInitiative.php
 *
 * @author Trevor Parscal <tparscal@wikimedia.org>
 * @license GPL v2
 * @version 0.1.0
 */

// Shortcut to this extension directory
$dir = dirname( __FILE__ ) . '/';

// Bump the version number every time you change any of the .css/.js files
$wgToolbarStyleVersion = 0;

// Autoload Classes
$wgAutoloadClasses['ToolbarHooks'] = $dir . 'Toolbar.hooks.php';

// Internationalization
$wgExtensionMessagesFiles['Toolbar'] = $dir . 'Toolbar.i18n.php';

// Register toolbar interception
$wgHooks['EditPageBeforeEditToolbar'][] = 'ToolbarHooks::interceptToolbar';

// Register preferences customization
$wgHooks['GetPreferences'][] = 'ToolbarHooks::addPreferences';

// Register ajax add script hook
$wgHooks['AjaxAddScript'][] = 'ToolbarHooks::addJS';

// Register css add script hook
$wgHooks['BeforePageDisplay'][] = 'ToolbarHooks::addCSS';
