<?php

require_once( 'MWSearchUpdater.php' );

$wgExtensionFunctions[] = 'mwSearchUpdateHookSetup';

$mwSearchUDPAddress = array('localhost','vega');
$mwSearchUDPPort = 8111;

function mwSearchUpdateHookSetup() {
	global $wgHooks;
	$wgHooks['ArticleSaveComplete'  ][] = 'mwSearchUpdateSave';
	$wgHooks['ArticleDeleteComplete'][] = 'mwSearchUpdateDelete';
	$wgHooks['TitleMoveComplete'    ][] = 'mwSearchUpdateMove';
	$wgHooks['ArticleUndelete'      ][] = 'mwSearchUpdateUndelete';
}

function sendNotification($line){
	global $mwSearchUDPAddress, $mwSearchUDPPort;
	$conn = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP );
	foreach($mwSearchUDPAddress as $host){
		socket_sendto( $conn, $line, strlen($line), 0, $host, $mwSearchUDPPort );
	}
	socket_close( $conn );
}

function mwSearchUpdateSave( $article, $user, $text, $summary, $isminor, $iswatch, $section ) {
	global $wgDBname;
	$title = $article->getTitle()->getPrefixedText();
	sendNotification("$wgDBname UPDATE $title\n");
	return true;
}

function mwSearchUpdateDelete( $article, $user, $reason ) {
	global $wgDBname;
	$title = $article->getTitle()->getPrefixedText();
	sendNotification("$wgDBname DELETE $title\n");
	return true;
}

function mwSearchUpdateUndelete( $title, $isnewid ) {
	global $wgDBname;
	$titleText = $title->getPrefixedText();
	sendNotification("$wgDBname UPDATE $titleText\n");
	return true;
}

function mwSearchUpdateMove( $from, $to, $user, $pageid, $redirid ) {
	global $wgDBname;
	
	$title1 = $to->getPrefixedText();
	$title2 = $from->getPrefixedText();
	sendNotification("$wgDBname UPDATE $title1\n$wgDBname UPDATE $title2\n");

	return true;
}
?>
