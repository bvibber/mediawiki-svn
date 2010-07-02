<?php
// Community department job applications

$wgSpecialPages['CommunityHiring'] = 'SpecialCommunityHiring';
$wgAutoloadClasses['SpecialCommunityHiring'] = dirname(__FILE__) . "/SpecialCommunityHiring.php";

$wgExtensionMessagesFiles['CommunityHiring'] = dirname( __FILE__ ) . "/CommunityHiring.i18n.php";
