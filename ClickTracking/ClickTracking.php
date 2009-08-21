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

// functions should check if this is set before logging clicktrack events
$wgClickTrackEnabled = true;

// set the time window for what we consider 'recent' contributions, in days
$wgClickTrackContribGranularity1 = 60 * 60 * 24 * 365 / 2; // half a year
$wgClickTrackContribGranularity2 =60 * 60 * 24 * 365 / 4;  // 1/4 a year (3 months approx)
$wgClickTrackContribGranularity3 = 60 * 60 * 24 * 30;  //30 days (1 month approx)

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Click Tracking',
	'author' => 'Nimish Gautam',
	'version' => '0.1.1',
	'descriptionmsg' => 'clicktracking-desc',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative'
);

// Includes parent extension
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative.php" );

// Adds Autoload Classes
$dir = dirname( __FILE__ ) . '/';
$wgAutoloadClasses['ClickTrackingHooks'] = $dir . 'ClickTracking.hooks.php';
$wgAutoloadClasses['ApiClickTracking'] = $dir . 'ApiClickTracking.php';

// Hooked functions
$wgHooks['LoadExtensionSchemaUpdates'][] = 'ClickTrackingHooks::schema';
$wgHooks['ArticleSaveComplete'][] = 'ClickTrackingHooks::storeNewContrib';
$wgHooks['EditPage::showEditForm:initial'][] = 'ClickTrackingHooks::addJS';

// Set up the new API module
$wgAPIModules['clicktracking'] = 'ApiClickTracking';