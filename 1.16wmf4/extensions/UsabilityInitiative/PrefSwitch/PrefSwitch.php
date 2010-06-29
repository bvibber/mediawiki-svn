<?php
/**
 * Usability Initiative PrefSwitch extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the include file for the PrefSwitch portion of the
 * UsabilityInitiative extension of MediaWiki.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/UsabilityInitiative/PrefSwitch/PrefSwitch.php" );
 *
 * @author Roan Kattouw <roan.kattouw@gmail.com>, Trevor Parscal <tparscal@wikimedia.org>
 * @license GPL v2 or later
 * @version 0.1.1
 */

/* Configuration */

$wgPrefSwitchStyleVersion = 1;

// Preferences to set when users switch prefs
// array(
//		'off' => array( pref => value ),
//		'on' => array( pref => value ),
//	)
$wgPrefSwitchPrefs = array(
	'off' => array(
	    'skin' => 'monobook',
	    'usebetatoolbar' => 0,
	    'usebetatoolbar-cgd'=> 0,
	),
	'on' =>  array(
	    'skin' => 'vector',
	    'usebetatoolbar' => 1,
	    'usebetatoolbar-cgd' => 1,
	),
);

// Survey questions to ask when users switch prefs
// array(
//		survey-id => array(
//			'submit-msg' => message key for submit button caption
//			'updateable' => boolean,
//			'questions' => array(
//				field-id => array(
//					'question' => msg-id,
//					'type' => msg-id,
//					'answers' => array(
//						answer => msg-id,
//						...
//					),
//					'other' => msg-id,
//					'ifyes' => msg-id
//				),
//				...
//			),
//		)
// )
$wgPrefSwitchSurveys = array();
$wgPrefSwitchSurveys['feedback'] = array(
	'submit-msg' => 'prefswitch-survey-submit-feedback',
	'updatable' => true,
	'questions' => array(
		'like' => array(
			'question' => 'prefswitch-survey-question-like',
			'type' => 'text',
		),
		'dislike' => array(
			'question' => 'prefswitch-survey-question-dislike',
			'type' => 'text',
		),
	),
);
$wgPrefSwitchSurveys['off'] = array(
	'submit-msg' => 'prefswitch-survey-submit-off',
	'updatable' => false,
	'questions' => array_merge(
		$wgPrefSwitchSurveys['feedback']['questions'],
		array(
			'whyrevert' => array(
				'question' => 'prefswitch-survey-question-whyoff',
				'type' => 'checks',
				'answers' => array(
					'hard' => 'prefswitch-survey-answer-whyoff-hard',
					'didntwork' => 'prefswitch-survey-answer-whyoff-didntwork',
					'notpredictable' => 'prefswitch-survey-answer-whyoff-notpredictable',
					'look' => 'prefswitch-survey-answer-whyoff-didntlike-look',
					'layout' => 'prefswitch-survey-answer-whyoff-didntlike-layout',
					'toolbar' => 'prefswitch-survey-answer-whyoff-didntlike-toolbar',
				),
				'other' => 'prefswitch-survey-answer-whyoff-other',
			),
		)
	),
);
// Always include the browser stuff...
foreach ( $wgPrefSwitchSurveys as &$survey ) {
	$survey['questions']['browser'] = array(
		'question' => 'prefswitch-survey-question-browser',
		'type' => 'select',
		'answers' => array(
			'ie5' => 'prefswitch-survey-answer-browser-ie5',
			'ie6' => 'prefswitch-survey-answer-browser-ie6',
			'ie7' => 'prefswitch-survey-answer-browser-ie7',
			'ie8' => 'prefswitch-survey-answer-browser-ie8',
			'ie9' => 'prefswitch-survey-answer-browser-ie9',
			'ff1' => 'prefswitch-survey-answer-browser-ff1',
			'ff2' => 'prefswitch-survey-answer-browser-ff2',
			'ff3'=> 'prefswitch-survey-answer-browser-ff3',
			'cb' => 'prefswitch-survey-answer-browser-cb',
			'c1' => 'prefswitch-survey-answer-browser-c1',
			'c2' => 'prefswitch-survey-answer-browser-c2',
			'c3' => 'prefswitch-survey-answer-browser-c3',
			'c4' => 'prefswitch-survey-answer-browser-c4',
			'c5' => 'prefswitch-survey-answer-browser-c5',
			's3' => 'prefswitch-survey-answer-browser-s3',
			's4' => 'prefswitch-survey-answer-browser-s4',
			's5' => 'prefswitch-survey-answer-browser-s5',
			'o9' => 'prefswitch-survey-answer-browser-o9',
			'o9.5' => 'prefswitch-survey-answer-browser-o9.5',
			'o10' => 'prefswitch-survey-answer-browser-o10',
		),
		'other' => 'prefswitch-survey-answer-browser-other',
	);
	$survey['questions']['os'] = array(
		'question' => 'prefswitch-survey-question-os',
		'type' => 'select',
		'answers' => array(
			'windows' => 'prefswitch-survey-answer-os-windows',
			'windowsmobile' => 'prefswitch-survey-answer-os-windowsmobile',
			'macos' => 'prefswitch-survey-answer-os-macos',
			'iphoneos' => 'prefswitch-survey-answer-os-iphoneos',
			'ios' => 'prefswitch-survey-answer-os-ios',
			'linux' => 'prefswitch-survey-answer-os-linux',
		),
		'other' => 'prefswitch-survey-answer-os-other',
	);
	$survey['questions']['res'] = array(
		'question' => 'prefswitch-survey-question-res',
		'type' => 'dimensions',
	);
}
unset( $survey );

/* Setup */

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'PrefSwitch',
	'author' => array( 'Trevor Parscal', 'Roan Kattouw' ),
	'version' => '0.1.2',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'prefswitch-desc',
);

// Includes parent extension
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative.php" );

// Adds Autoload Classes
$wgAutoloadClasses = array_merge(
	$wgAutoloadClasses,
	array(
		'SpecialPrefSwitch' => dirname( __FILE__ ) . '/SpecialPrefSwitch.php',
		'PrefSwitchHooks' => dirname( __FILE__ ) . '/PrefSwitch.hooks.php',
		'PrefSwitchSurvey' => dirname( __FILE__ ) . '/PrefSwitch.classes.php',
		'PrefSwitchSurveyField' => dirname( __FILE__ ) . '/PrefSwitch.classes.php',
		'PrefSwitchSurveyFieldSelect' => dirname( __FILE__ ) . '/PrefSwitch.classes.php',
		'PrefSwitchSurveyFieldRadios' => dirname( __FILE__ ) . '/PrefSwitch.classes.php',
		'PrefSwitchSurveyFieldChecks' => dirname( __FILE__ ) . '/PrefSwitch.classes.php',
		'PrefSwitchSurveyFieldBoolean' => dirname( __FILE__ ) . '/PrefSwitch.classes.php',
		'PrefSwitchSurveyFieldDimensions' => dirname( __FILE__ ) . '/PrefSwitch.classes.php',
		'PrefSwitchSurveyFieldText' => dirname( __FILE__ ) . '/PrefSwitch.classes.php',
	)
);

// Adds Internationalized Messages
$wgExtensionMessagesFiles['PrefSwitchLink'] =
	dirname( __FILE__ ) . '/PrefSwitchLink.i18n.php';
$wgExtensionMessagesFiles['PrefSwitch'] =
	dirname( __FILE__ ) . '/PrefSwitch.i18n.php';
$wgExtensionAliasesFiles['PrefSwitch'] =
	dirname( __FILE__ ) . '/PrefSwitch.alias.php';

$wgSpecialPages['PrefSwitch'] = 'SpecialPrefSwitch';
$wgSpecialPageGroups['PrefSwitch'] = 'wiki';

// Register Hooks
$wgHooks['LoadExtensionSchemaUpdates'][] = 'PrefSwitchHooks::schema';
$wgHooks['PersonalUrls'][] = 'PrefSwitchHooks::personalUrls';
