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

//functions should check if this is set before logging clicktrack events
$wgClickTrackEnabled = true;


//set the time window for what we consider 'recent' contributions, in days
$wgClickTrackContribTimeValue = 60 * 60 * 24 * 365 / 2; //half a year

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
$wgAutoloadClasses['ClickTrackingHooks'] =
	dirname( __FILE__ ) . '/ClickTracking.hooks.php';

$wgAutoloadClasses['ApiClickTracking'] = dirname( __FILE__ ) . '/ApiClickTracking.php';

$wgHooks['LoadExtensionSchemaUpdates'][] = 'ClickTrackingHooks::schema';

$wgHooks['ArticleSaveComplete'][] = 'ClickTrackingHooks::storeNewContrib';

$wgAPIModules['clicktracking'] = 'ApiClickTracking';

$wgHooks['EditPage::showEditForm:initial'] [] = 'ClickTrackingHooks::addJS';
