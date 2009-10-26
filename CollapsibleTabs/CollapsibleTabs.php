<?php
/**
 * Usability Initiative CollapsibleTabs extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the include file for the CollapsibleTabs portion of the
 * UsabilityInitiative extension of MediaWiki.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/UsabilityInitiative/CollapsibleTabs/CollapsibleTabs.php" );
 *
 * @author Adam Miller <amiller@wikimedia.org>
 * @license GPL v2 or later
 * @version 0.0.7
 */

/* Configuration */
// Bump the version number every time you change any of the .css/.js files
$wgCollapsibleTabsStyleVersion = 7;

/* Setup */

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'CollapsibleTabs',
	'author' => 'Adam Miller',
	'version' => '0.0.7',
	'url' => 'http://www.mediawiki.org/wiki/Extension:CollapsibleTabs',
	'descriptionmsg' => 'collapsibletabs-desc',
);
// Includes parent extension
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative.php" );

// Adds Autoload Classes
$wgAutoloadClasses['CollapsibleTabsHooks'] =
	dirname( __FILE__ ) . '/CollapsibleTabs.hooks.php';

$wgHooks['AjaxAddScript'][] = 'CollapsibleTabsHooks::initialize';
