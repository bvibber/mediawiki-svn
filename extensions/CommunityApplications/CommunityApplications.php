<?php

if ( ! defined( 'MEDIAWIKI' ) ) {
	die;
}

// Reader for CommunityHiring data
$wgSpecialPages['CommunityApplications'] = 'SpecialCommunityApplications';
$wgAutoloadClasses['SpecialCommunityApplications'] = dirname(__FILE__) . "/SpecialCommunityApplications.php";

$wgExtensionMessagesFiles['CommunityApplications'] = dirname( __FILE__ ) . "/CommunityApplications.i18n.php";

$wgCommunityHiringDatabase = false;
