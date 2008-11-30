<?php

# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/ContributionReporting/ContributionReporting.php" );
EOT;
	exit( 1 );
}

// Override these with appropriate DB settings for the CiviCRM database...
$wgContributionReportingDBserver = $wgDBserver;
$wgContributionReportingDBuser = $wgDBuser;
$wgContributionReportingDBpassword = $wgDBpassword;
$wgContributionReportingDBname = $wgDBname;

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Contribution Reporting',
	'url' => 'http://www.mediawiki.org/wiki/Extension:ContributionReporting',
	'svn-date' => '$LastChangedDate$',
	'svn-revision' => '$LastChangedRevision$',
	'author' => array( 'David Strauss', 'Brion Vibber', 'Siebrand Mazeland', 'Trevor Parscal' ),
	'descriptionmsg' => 'contributionreporting-desc',
);

$dir = dirname( __FILE__ ) . '/';

$wgExtensionMessagesFiles['ContributionReporting'] = $dir . 'ContributionReporting.i18n.php';
$wgExtensionAliasesFiles['ContributionReporting'] = $dir . 'ContributionReporting.alias.php';

$wgAutoloadClasses['ContributionHistory'] = $dir . 'ContributionHistory_body.php';
$wgAutoloadClasses['ContributionTotal'] = $dir . 'ContributionTotal_body.php';
$wgAutoloadClasses['SpecialContributionStatistics'] = $dir . 'ContributionStatistics_body.php';
$wgAutoloadClasses['SpecialFundraiserStatistics'] = $dir . 'FundraiserStatistics_body.php';

$wgSpecialPages['ContributionHistory'] = 'ContributionHistory';
$wgSpecialPages['ContributionTotal'] = 'ContributionTotal';
$wgSpecialPages['ContributionStatistics'] = 'SpecialContributionStatistics';
$wgSpecialPages['FundraiserStatistics'] = 'SpecialFundraiserStatistics';

// Shortcut to this extension directory
$dir = dirname( __FILE__ ) . '/';

// CutOff for fiscal year
$egContributionStatisticsFiscalYearCutOff = 'July 1';

// Days back to show
$egContributionStatisticsViewDays = 7;

// Fundraiser dates
$egFundraiserStatisticsFundraisers = array(
	array(
		'id' => '2007',
		'title' => '2007 Fundraiser',
		'start' => 'Oct 22 2007',
		'end' => 'Jan 3 2008'
	),
	array(
		'id' => '2008',
		'title' => '2008 Fundraiser',
		'start' => 'Nov 4 2008',
		'end' => 'Jan 9 2009'
	)
);

// Thesholds for fundraiser statistics
$egFundraiserStatisticsMinimum = 1;
$egFundraiserStatisticsMaximum = 10000;

// Automatically use a local or special database connection
function efContributionReportingConnection() {
	global $wgContributionReportingDBserver, $wgContributionReportingDBname;
	global $wgContributionReportingDBuser, $wgContributionReportingDBpassword;

	static $db;

	if ( !$db ) {
		$db = new DatabaseMysql(
			$wgContributionReportingDBserver,
			$wgContributionReportingDBuser,
			$wgContributionReportingDBpassword,
			$wgContributionReportingDBname );
		$db->query( "SET names utf8" );
	}

	return $db;
}

