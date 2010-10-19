<?php

# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/ContributionTracking/ContributionTracking.php" );
EOT;
	exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
	'path'           => __FILE__,
	'name'           => 'ContributionTracking',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:ContributionTracking',
	'author'         => 'David Strauss',
	'descriptionmsg' => 'contributiontracking-desc',
);

$dir = dirname( __FILE__ ) . '/';

$wgExtensionMessagesFiles['ContributionTracking'] = $dir . 'ContributionTracking.i18n.php';
$wgExtensionAliasesFiles['ContributionTracking'] = $dir . 'ContributionTracking.alias.php';
$wgAutoloadClasses['ContributionTracking'] = $dir . 'ContributionTracking_body.php';
$wgSpecialPages['ContributionTracking'] = 'ContributionTracking';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'efContributionTrackingLoadUpdates'; 


$wgContributionTrackingDBserver = $wgDBserver;
$wgContributionTrackingDBname = $wgDBname;
$wgContributionTrackingDBuser = $wgDBuser;
$wgContributionTrackingDBpassword = $wgDBpassword;

function efContributionTrackingLoadUpdates(){
 	global $wgExtNewTables, $wgExtNewFields;
 	$dir = dirname( __FILE__ ) . '/';
 	$wgExtNewTables[] = array( 'contribution_tracking', $dir . 'ContributionTracking.sql' );
 	$wgExtNewTables[] = array( 'contribution_tracking_owa_ref', $dir . 'ContributionTracking_OWA_ref.sql' );
 	
 	$wgExtNewFields[] = array(
 		'contribution_tracking',
 		'owa_session',
 		$dir . 'patch-owa.sql',
 	);
 	return true; 	
	
}

	//convert a referrer URL to an index in the owa_ref table
function ef_contribution_tracking_owa_get_ref_id($ref){
		// Replication lag means sometimes a new event will not exist in the table yet
		$dbw = wfGetDB( DB_MASTER );
		$id_num = $dbw->selectField(
			'contribution_tracking_owa_ref',
			'id',
			array( 'url' => $ref ),
			__METHOD__
		);
		// Once we're on mysql 5, we can use replace() instead of this selectField --> insert or update hooey
		if ( $id_num === false ) {
			$dbw->insert(
				'contribution_tracking_owa_ref',
				array( 'url' => (string) $event_name ),
				__METHOD__
			);
			$id_num = $dbw->insertId();
		}
		return $id_num === false ? 0 : $id_num;
	}

function contributionTrackingConnection() {
	global $wgContributionTrackingDBserver, $wgContributionTrackingDBname;
	global $wgContributionTrackingDBuser, $wgContributionTrackingDBpassword;

	static $db;

	if ( !$db ) {
		$db = new DatabaseMysql(
			$wgContributionTrackingDBserver,
			$wgContributionTrackingDBuser,
			$wgContributionTrackingDBpassword,
			$wgContributionTrackingDBname );
		$db->query( "SET names utf8" );
	}

	return $db;
}
