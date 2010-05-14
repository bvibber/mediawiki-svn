<?php
/**
 * "Update my preferences" extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the include for the "update my preferences" extension
 * 
 * This allows users to set a flag ('on' by default) that will expressly allow them to 
 * state whether they would like their preferences to be updated with the 'latest and greatest' that we come out
 * with or not
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/UsabilityInitiative/UpdateMyPrefs/UpdateMyPrefs.php" );
 *
 * @author Nimish Gautam <ngautam@wikimedia.org>
 * @license GPL v2 or later
 * @version 0.1.1
 */

/* Configuration */
 
// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Update My Prefs',
	'author' => 'Nimish Gautam',
	'version' => '0.1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'updatemyprefs-desc',
);

// Includes parent extension
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative.php" );

// Adds Autoload Classes
$dir = dirname( __FILE__ ) . '/';
$wgAutoloadClasses['UpdateMyPrefsHooks'] = $dir . 'UpdateMyPrefs.hooks.php';

// Adds Internationalized Messages
$wgExtensionMessagesFiles['UpdateMyPrefs'] = $dir . 'UpdateMyPrefs.i18n.php';

// Hooked functions
$wgHooks['GetPreferences'][] = 'UpdateMyPrefsHooks::addPreferences';

//set default
$wgDefaultUserOptions['updatemyprefs'] = 1;