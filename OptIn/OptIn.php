<?php
/**
 * Usability Initiative OptIn extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the include file for the OptIn portion of the
 * UsabilityInitiative extension of MediaWiki.
 *
 * Usage: This file is included automatically by ../UsabilityInitiative.php
 *
 * @author Roan Kattouw <roan.kattouw@gmail.com>
 * @license GPL v2 or later
 * @version 0.1.1
 */

/* Configuration */

// Preferences to set when users opt in
// array( prefname => value )
$wgOptInPrefs = array( 'skin' => 'vector', 'usebetatoolbar' => 1 );

/* Setup */

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'OptIn',
	'author' => 'Roan Kattouw',
	'version' => '0.1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'optin-desc',
);

// Adds Autoload Classes
$wgAutoloadClasses['SpecialOptIn'] =
	dirname( __FILE__ ) . '/SpecialOptIn.php';

// Adds Internationalized Messages
$wgExtensionMessagesFiles['OptIn'] =
	dirname( __FILE__ ) . '/OptIn.i18n.php';

$wgSpecialPages['OptIn'] = 'SpecialOptIn';
