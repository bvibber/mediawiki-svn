<?php

// settings

// number of new revisions to mark the last rating as old
$wgArticleAssessmentStaleCount = 5;

// Number of "ratings" to store. Allows it to be a bit more dynamic
$wgArticleAssessmentRatingCount = 4;

// Auto-load files
$dir = dirname( __FILE__ ) . '/';
$wgAutoloadClasses['ApiListArticleAssessment'] = $dir . 'api/ApiListArticleAssessment.php';
$wgAutoloadClasses['ApiArticleAssessment'] = $dir . 'api/ApiArticleAssessment.php';
$wgAutoloadClasses['ArticleAssessmentPilotHooks'] = $dir . 'ArticleAssessmentPilot.hooks.php';

// Schema and tables
$wgHooks['LoadExtensionSchemaUpdates'][] = 'ArticleAssessmentPilotHooks::schema';
$wgHooks['ParserTestTables'][] = 'ArticleAssessmentPilotHooks::parserTestTables';

// Hooks
$wgHooks['BeforePageDisplay'][] = 'ArticleAssessmentPilotHooks::addResources';
$wgHooks['SkinAfterContent'][] = 'ArticleAssessmentPilotHooks::addCode';

// API modules
$wgAPIListModules['articleassessment'] = 'ApiListArticleAssessment';
$wgAPIModules['articleassessment'] = 'ApiArticleAssessment';

// i18n and aliases
// Adds Internationalized Messages
$wgExtensionMessagesFiles['ArticleAssessmentPilot'] = $dir . 'ArticleAssessmentPilot.i18n.php';

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Article Assessment Pilot',
	'author' => array( 'Nimish Gautam', 'Sam Reed' ),
	'version' => '0.1.0',
	'descriptionmsg' => 'articleassessment-desc',
	'url' => 'http://www.mediawiki.org/wiki/Extension:ArticleAssessmentPilot'
);

//Array of page id's for which to include the rating stuffs on
$wgArticleAssessmentPages = array();