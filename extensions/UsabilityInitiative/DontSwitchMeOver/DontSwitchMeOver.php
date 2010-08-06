<?php
/**
 * "Don't switch me over" extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the include for the "don't switch me over" extension
 * 
 * This allows users to indicate that they don't want to be switched over to
 * Vector in any future rollout.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/UsabilityInitiative/DontSwitchMeOver/DontSwitchMeOver.php" );
 *
 * @author Nimish Gautam <ngautam@wikimedia.org>
 * @license GPL v2 or later
 * @version 0.1.1
 */

/* Configuration */

// Preferences to switch back to. This has to be set because the old default
// skin isn't remembered after a switchover.
// You can also add more preferences here, and on wikis with PrefSwitch setting
// $wgDontSwitchMeOverPrefs = $wgPrefSwitchPrefs['off']; is probably wise
$wgDontSwitchMeOverPrefs = array(
	'skin' => 'monobook'
);

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => "Don't Switch Me Over",
	'author' => array( 'Roan Kattouw', 'Nimish Gautam' ),
	'version' => '0.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'dontswitchmeover-desc',
);

// Includes parent extension
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative.php" );

// Adds autoload classes
$dir = dirname( __FILE__ ) . '/';
$wgAutoloadClasses['DontSwitchMeOverHooks'] = $dir . 'DontSwitchMeOver.hooks.php';

// Adds internationalized messages
$wgExtensionMessagesFiles['DontSwitchMeOver'] = $dir . 'DontSwitchMeOver.i18n.php';

// Hooked functions
$wgHooks['GetPreferences'][] = 'DontSwitchMeOverHooks::addPreferences';

// Set default
$wgDefaultUserOptions['dontswitchmeover'] = 0;
