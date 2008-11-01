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
	'author' => array( 'David Strauss', 'Brion Vibber', 'Siebrand Mazeland' ),
	'descriptionmsg' => 'contributionreporting-desc',
);

$dir = dirname( __FILE__ ) . '/';

$wgExtensionMessagesFiles['ContributionReporting'] = $dir . 'ContributionReporting.i18n.php';
$wgExtensionAliasesFiles['ContributionReporting'] = $dir . 'ContributionReporting.alias.php';
$wgAutoloadClasses['ContributionHistory'] = $dir . 'ContributionHistory_body.php';
$wgAutoloadClasses['ContributionTotal'] = $dir . 'ContributionTotal_body.php';
$wgSpecialPages['ContributionHistory'] = 'ContributionHistory';
$wgSpecialPages['ContributionTotal'] = 'ContributionTotal';

function contributionReportingConnection() {
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
