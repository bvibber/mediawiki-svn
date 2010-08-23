<?php

//settings

//number of reviews
$wgArticleAssessmentRevisionCutoff = 5;

//Auto-load files
$dir = dirname( __FILE__ ) . '/';
$wgAutoloadClasses['ApiListArticleAssessment'] = $dir . 'api/ApiListArticleAssessment.php';
$wgAutoloadClasses['ArticleAssessmentPilotHooks'] = $dir . 'ArticleAssessmentPilot.hooks.php';

//Schema and tables
$wgHooks['LoadExtensionSchemaUpdates'][] = 'ArticleAssessmentPilotHooks::schema';
$wgHooks['ParserTestTables'][] = 'ArticleAssessmentPilotHooks::parserTestTables';

//Hooks
$wgHooks['SkinAfterContent'][] = 'ArticleAssessmentPilotHooks::addCode';

//API modules
$wgAPIModules['articleassessment'] = 'ApiListArticleAssessment';


//i18n and aliases
// Adds Internationalized Messages
$wgExtensionMessagesFiles['ArticleAssessmentPilot'] = $dir . 'ArticleAssessmentPilot.i18n.php';

//Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Article Assessment Pilot',
	'author' => 'Nimish Gautam',
	'version' => '0.1.0',
	'descriptionmsg' => 'articleassessment-pilot-desc',
	'url' => 'http://www.mediawiki.org/wiki/Extension:ArticleAsessmentPilot'
);