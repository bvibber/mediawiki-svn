<?php

// If the number of page revisions (since users last rating) is greater than this
// then consider the last rating "stale"
$wgArticleAssessmentStaleCount = 5;

// Array of the "ratings" id's to store. Allows it to be a bit more dynamic
$wgArticleAssessmentRatings = array( 1, 2, 3, 4 );

// Which category the pages must belong to have the rating widget added (with _ in text)
// Extension is "disabled" if this field is an empty string (as per default configuration)
$wgArticleAssessmentCategory = '';

// Set to 'combined' or 'raw' if you need to debug this extension's JS
$wgArticleAssessmentResourceMode = 'minified';

// Path to jQuery UI's JS
$wgArticleAssessmentJUIJSPath = null; // Defaults to "$wgExtensionAssetsPath/ArticleAssessmentPilot/js/jui.combined.min.js"

// Path to jQuery UI's CSS
$wgArticleAssessmentJUICSSPath = null; // Defaults to "$wgExtensionAssetsPath/ArticleAssessmentPilot/css/jquery-ui-1.7.2.css"

// Auto-load files
$dir = dirname( __FILE__ ) . '/';
$wgAutoloadClasses['ApiQueryArticleAssessment'] = $dir . 'api/ApiQueryArticleAssessment.php';
$wgAutoloadClasses['ApiArticleAssessment'] = $dir . 'api/ApiArticleAssessment.php';
$wgAutoloadClasses['ArticleAssessmentPilotHooks'] = $dir . 'ArticleAssessmentPilot.hooks.php';

// Schema and tables
$wgHooks['LoadExtensionSchemaUpdates'][] = 'ArticleAssessmentPilotHooks::schema';
$wgHooks['ParserTestTables'][] = 'ArticleAssessmentPilotHooks::parserTestTables';

// Hooks
$wgHooks['BeforePageDisplay'][] = 'ArticleAssessmentPilotHooks::addResources';
$wgHooks['MakeGlobalVariablesScript'][] = 'ArticleAssessmentPilotHooks::addVariables';

// API modules
$wgAPIListModules['articleassessment'] = 'ApiQueryArticleAssessment';
$wgAPIModules['articleassessment'] = 'ApiArticleAssessment';

// i18n and aliases
// Adds Internationalized Messages
$wgExtensionMessagesFiles['ArticleAssessmentPilot'] = $dir . 'ArticleAssessmentPilot.i18n.php';

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Article Assessment Pilot',
	'author' => array( 'Nimish Gautam', 'Sam Reed', 'Adam Miller' ),
	'version' => '0.1.0',
	'descriptionmsg' => 'articleassessment-desc',
	'url' => 'http://www.mediawiki.org/wiki/Extension:ArticleAssessmentPilot'
);

// Survey setup
// This is totally a hack, but it's easy and had to be done fast
require_once( $dir . '../SimpleSurvey/SimpleSurvey.php' );

// Would ordinarily call this articleassessment but survey names are 16 chars max
$wgPrefSwitchSurveys['articlerating'] = array(
	'updatable' => false,
	'submit-msg' => 'articleassessment-survey-submit',
	'questions' => array(
		'whyrated' => array(
			'question' => 'articleassessment-survey-question-whyrated',
			'type' => 'checks',
			'answers' => array(
				'contribute-rating' => 'articleassessment-survey-answer-whyrated-contribute-rating',
				'development' => 'articleassessment-survey-answer-whyrated-development',
				'contribute-wiki' => 'articleassessment-survey-answer-whyrated-contribute-wiki',
				'sharing-opinion' => 'articleassessment-survey-answer-whyrated-sharing-opinion',
				'didntrate' => 'articleassessment-survey-answer-whyrated-didntrate',
			),
			'other' => 'articleassessment-survey-answer-whyrated-other',
		),
		'useful' => array(
			'question' => 'articleassessment-survey-question-useful',
			'type' => 'boolean',
			'iffalse' => 'articleassessment-survey-question-useful-iffalse',
		),
		'expert' => array(
			'question' => 'articleassessment-survey-question-expert',
			'type' => 'boolean',
			'iftrue' => 'articleassessment-survey-question-expert-iftrue',
		),
		'comments' => array(
			'question' => 'articleassessment-survey-question-comments',
			'type' => 'text',
		),
	),
);
$wgValidSurveys[] = 'articlerating';
