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
	'svn-date' => '$LastChangedDate$',
	'svn-revision' => '$LastChangedRevision$',
	'description-msg' => 'contribstats-desc',
);

// Shortcut to this extension directory
$dir = dirname( __FILE__ ) . '/';

// CutOff for fiscal year
$egContributionStatisticsFiscalYearCutOff = 'July 1';

// Days back to show
$egContributionStatisticsViewDays = 7;

// Internationalization
$wgExtensionMessagesFiles['ContributionStatistics'] = $dir . 'ContributionStatistics.i18n.php';
$wgExtensionAliasesFiles['ContributionStatistics'] = $dir . 'ContributionStatistics.alias.php';

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