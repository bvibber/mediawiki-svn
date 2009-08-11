<?php
/**
 * Usability Initiative Click Tracking extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the include file for the Click Tracking portion of the
 * UsabilityInitiative extension of MediaWiki.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/UsabilityInitiative/ClickTracking/ClickTracking.php" );
 *
 * @author Nimish Gautam <ngautam@wikimedia.org>
 * @license GPL v2 or later
 * @version 0.1.1
 */

/* Configuration */

$wgClickTrackStyleVersion = 1;

//functions should check if this is set before logging clicktrack events
$wgClickTrackEnabled = true;

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Click Tracking',
	'author' => 'Nimish Gautam',
	'version' => '0.1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'clicktracking-desc',
);

// Includes parent extension
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative.php" );

// Adds Autoload Classes
$wgAutoloadClasses['ClickTrackingHooks'] =
	dirname( __FILE__ ) . '/ClickTracking.hooks.php';

$wgHooks['LoadExtensionSchemaUpdates'][] = 'ClickTrackingHooks::schema';

