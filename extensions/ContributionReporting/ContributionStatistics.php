<?php
/**
 * ContributionStatistics extension
 *
 * @file
 * @ingroup Extensions
 * 
 * This file contains the main include file for the ContributionStatistics
 * extension of MediaWiki.
 *
 * Usage: Add the following line in LocalSettings.php:
 * require_once( "$IP/extensions/ContributionStatistics/ContributionStatistics.php" );
 *
 * @author Trevor Parscal <tparscal@wikimedia.org>
 * @license GPL v2
 * @version 0.1.0
 */

// Check environment
if ( !defined( 'MEDIAWIKI' ) ) {
	echo( "This is an extension to the MediaWiki package and cannot be run standalone.\n" );
	die( - 1 );
}

/* Configuration */

// Credits
$wgExtensionCredits['other'][] = array(
	'name' => 'ContributionStatistics',
	'author' => 'Trevor Parscal',
	'url' => 'http://www.mediawiki.org/wiki/Extension:ContributionStatistics',
	'description' => 'Displays statistics for contributions made to the WikiMedia Foundation',
	'svn-date' => '$LastChangedDate: 2008-11-05 11:44:10 -0800 (Wed, 05 Nov 2008) $',
	'svn-revision' => '$LastChangedRevision: 43233 $',
	'description-msg' => 'drafts-desc',
);

// Shortcut to this extension directory
$dir = dirname( __FILE__ ) . '/';

// CutOff for fiscal year
$egContributionStatisticsFiscalYearCutOff = 'July 1';

// Days back to show
$egContributionStatisticsViewDays = 7;

// Internationalization
$wgExtensionMessagesFiles['ContributionStatistics'] = $dir . 'ContributionStatistics.i18n.php';

// Register the Drafts special page
$wgSpecialPages['ContributionStatistics'] = 'SpecialContributionStatistics';
$wgAutoloadClasses['SpecialContributionStatistics'] = $dir . 'ContributionStatistics.pages.php';

// Automatically use a local or special database connection
function efContributionStatisticsConnection() {
	if ( function_exists( 'contributionReportingConnection' ) ) {
		return contributionReportingConnection();
	} else {
		return wfGetDB( DB_SLAVE );
	}
}