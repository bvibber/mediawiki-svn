<?php

// If the number of page revisions (since users last rating) is greater than this
// then consider the last rating "stale"
$wgArticleAssessmentStaleCount = 5;

// Array of the "ratings" id's to store. Allows it to be a bit more dynamic
$wgArticleAssessmentRatings = array( 1, 2, 3, 4 );

// Which category the pages must belong to have the rating widget added (with _ in text)
// Extension is "disabled" if this field is an empty string (as per default configuration)
$wgArticleAssessmentCategory = '';

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
