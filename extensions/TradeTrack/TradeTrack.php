<?php
// Process system for managing trademark usage requests.

$wgExtensionCredits['specialpage'][] = array(
	'path'           => __FILE__,
	'name'           => 'Trade Track',
	'author'         => array( 'Brandon Harris' ),
	'url'            => 'http://www.mediawiki.org/wiki/Extension:TradeTrack',
	'descriptionmsg' => 'tradetrack-desc',
);

$wgSpecialPages['TradeTrack'] = 'SpecialTradeTrack';

$wgTradeTrackEmailSubject = "A new Trademark Request has Arrived";
$wgTradeTrackFromEmail = "tradetrack@wikimedia.org";

$wgAutoloadClasses['SpecialTradeTrack'] = dirname(__FILE__) . "/SpecialTradeTrack.php";
$wgAutoloadClasses['TradeTrackScreen'] = dirname(__FILE__) . "/templates/TradeTrackScreen.php";
$wgAutoloadClasses['TradeTrackScreenDetailsForm'] = dirname(__FILE__) . "/templates/TradeTrackScreenDetailsForm.php";
$wgAutoloadClasses['TradeTrackScreenNonComAgreement'] = dirname(__FILE__) . "/templates/TradeTrackScreenNonComAgreement.php";
$wgAutoloadClasses['TradeTrackScreenRouting'] = dirname(__FILE__) . "/templates/TradeTrackScreenRouting.php";
$wgAutoloadClasses['TradeTrackScreenThanks'] = dirname(__FILE__) . "/templates/TradeTrackScreenThanks.php";
$wgAutoloadClasses['TradeTrackEmail'] = dirname(__FILE__) . "/templates/TradeTrackEmail.php";

$wgExtensionMessagesFiles['TradeTrack'] = dirname( __FILE__ ) . "/TradeTrack.i18n.php";

$wgTradeTrackEmailCommercial = "bharris@wikimedia.org";  // Who gets commercial requests (Kul)
$wgTradeTrackEmailNonCommercial = "bharris@wikimedia.org"; // Who gets non-commercial requests (Mike)
$wgTradeTrackEmailMedia = "bharris@wikimedia.org"; // Who gets media requests (Jay)

