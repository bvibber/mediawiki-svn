<?php
/**
 * Usability Initiative Click Tracking extension
 * 
 * @file
 * @ingroup Extensions
 * 
 * @author Nimish Gautam <ngautam@wikimedia.org>
 * @author Trevor Parscal <tparscal@wikimedia.org>
 * @license GPL v2 or later
 * @version 0.1.1
 */

/* Configuration */

// Click tracking throttle, should be seen as "1 out of every $wgClickTrackThrottle users will have it enabled"
// Setting this to 1 means all users will have it enabled, setting to a negative number will disable it for all users
$wgClickTrackThrottle = -1;

// Set the time window for what we consider 'recent' contributions, in days
$wgClickTrackContribGranularity1 = 60 * 60 * 24 * 365 / 2; // 6 months
$wgClickTrackContribGranularity2 = 60 * 60 * 24 * 365 / 4; // 3 months
$wgClickTrackContribGranularity3 = 60 * 60 * 24 * 30; // 1 month

/* Setup */

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Click Tracking',
	'author' => array( 'Nimish Gautam', 'Trevor Parscal' ),
	'version' => '0.1.1',
	'descriptionmsg' => 'clicktracking-desc',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative'
);
$wgAutoloadClasses['ClickTrackingHooks'] = dirname( __FILE__ ) . '/ClickTracking.hooks.php';
$wgAutoloadClasses['ApiClickTracking'] = dirname( __FILE__ ) . '/ApiClickTracking.php';
$wgAutoloadClasses['SpecialClickTracking'] = dirname( __FILE__ ) . '/SpecialClickTracking.php';
$wgAutoloadClasses['ApiSpecialClickTracking'] = dirname( __FILE__ ) . '/ApiSpecialClickTracking.php';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'ClickTrackingHooks::loadExtensionSchemaUpdates';
$wgHooks['BeforePageDisplay'][] = 'ClickTrackingHooks::beforePageDisplay';
$wgHooks['MakeGlobalVariablesScript'][] = 'ClickTrackingHooks::makeGlobalVariablesScript';
$wgHooks['ResourceLoaderRegisterModules'][] = 'ClickTrackingHooks::resourceLoaderRegisterModules';
$wgHooks['ParserTestTables'][] = 'ClickTrackingHooks::parserTestTables';
$wgAPIModules['clicktracking'] = 'ApiClickTracking';
$wgAPIModules['specialclicktracking'] = 'ApiSpecialClickTracking';
$wgSpecialPages['ClickTracking'] = 'SpecialClickTracking';
$wgGroupPermissions['sysop']['clicktrack'] = true;
$wgExtensionMessagesFiles['ClickTracking'] = dirname( __FILE__ ) . '/ClickTracking.i18n.php';
$wgExtensionAliasesFiles['ClickTracking'] = dirname( __FILE__ ) . '/ClickTracking.alias.php';
