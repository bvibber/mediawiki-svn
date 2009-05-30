<?php
/**
 * Usability Initiative extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the main include file for the UsabilityInitiative
 * extension of MediaWiki.
 *
 * Usage: Add the following line in LocalSettings.php:
 * require_once( "$IP/extensions/UsabilityInitiative/UsabilityInitiative.php" );
 *
 * @author Trevor Parscal <tparscal@wikimedia.org>
 * @license GPL v2
 * @version 0.1.0
 */
/* Configuration */

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'UsabilityInitiative',
	'author' => 'Trevor Parscal',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'usabilityinitiative-desc',
);

// Shortcut to this extension directory
$dir = dirname( __FILE__ );

// Autoload Classes
$wgAutoloadClasses['UsabilityInitiativeHooks'] =
	"$dir/UsabilityInitiative.hooks.php";

$wgExtensionMessagesFiles['UsabilityInitiative'] =
	"$dir/UsabilityInitiative.i18n.php";

// Register ajax add script hook
$wgHooks['AjaxAddScript'][] = 'UsabilityInitiativeHooks::addJS';

/* Components */

require_once( "$dir/Toolbar/Toolbar.php" );