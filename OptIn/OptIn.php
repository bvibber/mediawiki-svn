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

/**
 * The default behavior of this extension is to show a link at the begining of
 * the personal tools while a user is opted in, allowing them to easily opt back
 * out at any time. Using the following global configuration variables you can
 * make the link always show, resulting in a link that invites them to opt-in
 * when they aren't opted-in, or never show, even if they are opted in.
 * 
 * These variables can be set before or after inclusion of this file.
 */
if ( !isset( $wgOptInAlwaysShowPersonalLink ) ) {
	$wgOptInAlwaysShowPersonalLink = false;
}
if ( !isset( $wgOptInNeverShowPersonalLink ) ) {
	$wgOptInNeverShowPersonalLink = false;
}

$wgOptInStyleVersion = 3;

// Preferences to set when users opt in
// array( prefname => value )
$wgOptInPrefs = array( 'skin' => 'vector', 'usebetatoolbar' => 1 );

// Survey questions to ask when users opt out
$wgOptInSurvey = array(
	array(	'question' => 'optin-survey-question-whyoptout',
		'type' => 'checkboxes',
		'answers' => array(
			'optin-survey-answer-whyoptout-didntlike',
			'optin-survey-answer-whyoptout-hard',
			'optin-survey-answer-whyoptout-didntwork' ),
		'other' => 'optin-survey-answer-whyoptout-other' ),
	array(	'question' => 'optin-survey-question-browser',
		'type' => 'dropdown',
		'answers' => array(
			'optin-survey-answer-browser-ie5',
			'optin-survey-answer-browser-ie6',
			'optin-survey-answer-browser-ie7',
			'optin-survey-answer-browser-ie8',
			'optin-survey-answer-browser-ff1',
			'optin-survey-answer-browser-ff2',
			'optin-survey-answer-browser-ff3',
			'optin-survey-answer-browser-cb',
			'optin-survey-answer-browser-c1',
			'optin-survey-answer-browser-c2',
			'optin-survey-answer-browser-s3',
			'optin-survey-answer-browser-s4',
			'optin-survey-answer-browser-o9',
			'optin-survey-answer-browser-o9.5',
			'optin-survey-answer-browser-o10' ),
		'other' => 'optin-survey-answer-browser-other' ),
	array(	'question' => 'optin-survey-question-os',
		'type' => 'dropdown',
		'answers' => array(
			'optin-survey-answer-os-windows',
			'optin-survey-answer-os-macos',
			'optin-survey-answer-os-linux' ),
		'other' => 'optin-survey-answer-os-other' ),
	array(	'question' => 'optin-survey-question-res',
		'type' => 'resolution' ),
	array(	'question' => 'optin-survey-question-feedback',
		'type' => 'textarea' )
);

/* Setup */

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'OptIn',
	'author' => 'Roan Kattouw',
	'version' => '0.1.2',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'optin-desc',
);

// Adds Autoload Classes
$wgAutoloadClasses['SpecialOptIn'] =
	dirname( __FILE__ ) . '/SpecialOptIn.php';
$wgAutoloadClasses['OptInHooks'] =
	dirname( __FILE__ ) . '/OptIn.hooks.php';

// Adds Internationalized Messages
$wgExtensionMessagesFiles['OptIn'] =
	dirname( __FILE__ ) . '/OptIn.i18n.php';
$wgExtensionAliasesFiles['OptIn'] =
	dirname( __FILE__ ) . '/OptIn.alias.php';

$wgSpecialPages['OptIn'] = 'SpecialOptIn';
$wgSpecialPageGroups['OptIn'] = 'wiki';

$wgHooks['LoadExtensionSchemaUpdates'][] = 'OptInHooks::schema';

$wgHooks['PersonalUrls'][] = 'OptInHooks::personalUrls';

