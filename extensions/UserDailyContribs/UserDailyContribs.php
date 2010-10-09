<?php
/**
 * User Daily Contributions extension
 * 
 * This extension adds a step to saving an article that incriments a counter for a user's activity in a given day.
 * 
 * @file
 * @ingroup Extensions
 * 
 * @author Nimish Gautam <ngautam@wikimedia.org>
 * @author Trevor Parscal <tparscal@wikimedia.org>
 * @license GPL v2 or later
 * @version 0.2.0
 */

/* Setup */

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'User Daily Contributions',
	'author' => array( 'Nimish Gautam', 'Trevor Parscal' ),
	'version' => '0.2.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'userdailycontribs-desc',
);
$wgAutoloadClasses['UserDailyContribsHooks'] = dirname( __FILE__ ) . '/UserDailyContribs.hooks.php';
$wgExtensionMessagesFiles['UserDailyContribs'] = dirname( __FILE__ ) . '/UserDailyContribs.i18n.php';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'UserDailyContribsHooks::loadExtensionSchemaUpdates';
$wgHooks['ArticleSaveComplete'][] = 'UserDailyContribsHooks::articleSaveComplete';
$wgHooks['ParserTestTables'][] = 'UserDailyContribsHooks::parserTestTables';
