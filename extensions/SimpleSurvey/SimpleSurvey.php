<?php

$dir = dirname( __FILE__ ) . '/';

//from prefswitch in usability initiative
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative/UsabilityInitiative.php" );
$prefswitchdir = dirname( dirname( __FILE__ ) ) ."/UsabilityInitiative/PrefSwitch";

// Adds Autoload Classes
$wgAutoloadClasses = array_merge(
	$wgAutoloadClasses,
	array(
		'PrefSwitchSurvey' => $prefswitchdir . '/PrefSwitch.classes.php',
		'PrefSwitchSurveyField' => $prefswitchdir . '/PrefSwitch.classes.php',
		'PrefSwitchSurveyFieldSelect' => $prefswitchdir . '/PrefSwitch.classes.php',
		'PrefSwitchSurveyFieldRadios' => $prefswitchdir . '/PrefSwitch.classes.php',
		'PrefSwitchSurveyFieldChecks' => $prefswitchdir . '/PrefSwitch.classes.php',
		'PrefSwitchSurveyFieldBoolean' => $prefswitchdir . '/PrefSwitch.classes.php',
		'PrefSwitchSurveyFieldDimensions' => $prefswitchdir . '/PrefSwitch.classes.php',
		'PrefSwitchSurveyFieldText' => $prefswitchdir . '/PrefSwitch.classes.php',
		'SimpleSurvey' => $dir . "SimpleSurvey.classes.php",
		'SpecialSimpleSurvey' => $dir. 'SpecialSimpleSurvey.php',
	)
);
unset($prefswitchdir);


//add special pages
$wgSpecialPages['SimpleSurvey'] = 'SpecialSimpleSurvey';
$wgSpecialPageGroups['SimpleSurvey'] = 'wiki';
$wgExtensionMessagesFiles['SimpleSurvey'] = $dir . 'SimpleSurvey.i18n.php';


$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'SimpleSurvey',
	'author' => array( 'Nimish Gautam' ),
	'version' => '0.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
);

// Register database operations
$wgHooks['LoadExtensionSchemaUpdates'][] = 'SimpleSurvey::schema';

$wgValidSurveys = array();

//add surveys
require_once($dir . "Surveys.php");
unset($dir);


// Always include the browser stuff...
foreach ( $wgPrefSwitchSurveys as &$survey ) {
	$survey['questions']['browser'] = array(
		'visibility' => "hidden",
		'question' => 'prefswitch-survey-question-browser',
		'type' => 'select',
		'answers' => array(
			'ie5' => 'prefswitch-survey-answer-browser-ie5',
			'ie6' => 'prefswitch-survey-answer-browser-ie6',
			'ie7' => 'prefswitch-survey-answer-browser-ie7',
			'ie8' => 'prefswitch-survey-answer-browser-ie8',
			'ff1' => 'prefswitch-survey-answer-browser-ff1',
			'ff2' => 'prefswitch-survey-answer-browser-ff2',
			'ff3'=> 'prefswitch-survey-answer-browser-ff3',
			'cb' => 'prefswitch-survey-answer-browser-cb',
			'c1' => 'prefswitch-survey-answer-browser-c1',
			'c2' => 'prefswitch-survey-answer-browser-c2',
			's3' => 'prefswitch-survey-answer-browser-s3',
			's4' => 'prefswitch-survey-answer-browser-s4',
			'o9' => 'prefswitch-survey-answer-browser-o9',
			'o9.5' => 'prefswitch-survey-answer-browser-o9.5',
			'o10' => 'prefswitch-survey-answer-browser-o10',
		),
		'other' => 'prefswitch-survey-answer-browser-other',
	);
	$survey['questions']['os'] = array(
		'question' => 'prefswitch-survey-question-os',
		'visibility' => "hidden",
		'type' => 'select',
			'answers' => array(
			'windows' => 'prefswitch-survey-answer-os-windows',
			'windowsmobile' => 'prefswitch-survey-answer-os-windowsmobile',
			'macos' => 'prefswitch-survey-answer-os-macos',
			'iphoneos' => 'prefswitch-survey-answer-os-iphoneos',
			'linux' => 'prefswitch-survey-answer-os-linux',
		),
		'other' => 'prefswitch-survey-answer-os-other',
	);
	$survey['questions']['res'] = array(
		'question' => 'prefswitch-survey-question-res',
		'visibility' => "hidden",
		'type' => 'dimensions',
	);
}
unset( $survey );



