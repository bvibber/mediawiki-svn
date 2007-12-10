<?php

# Wikimedia Foundation Board of Trustees Election

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
	die( "Not a valid entry point\n" );
}

# Extension credits
$wgExtensionCredits['other'][] = array(
	'name' => 'BoardVote',
	'version' => '1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:BoardVote',
	'author' => array( 'Tim Starling', 'Rob Church', 'Brion Vibber' ),
	'description' => '[[meta:Board elections|Wikimedia Board of Trustees election]]',
);

# Default settings
$wgBoardVoteDB = "boardvote";
$wgBoardCandidates = array();
$wgGPGCommand = "gpg";
$wgGPGRecipient = "boardvote";
$wgGPGHomedir = false;
$wgGPGPubKey = "C:\\Program Files\\gpg\\pub.txt";
$wgBoardVoteEditCount = 400;
$wgBoardVoteFirstEdit = '20070301000000';
$wgBoardVoteCountDate = '20070601000000';
$wgBoardVoteStartDate = '20070628000000';
$wgBoardVoteEndDate =   '20070708000000';
$wgBoardVoteDBServer = $wgDBserver;

# Vote admins
$wgGroupPermissions['boardvote']['boardvote'] = true;

$wgHooks['LoadAllMessages'][] = 'wfBoardVoteInitMessages';

# Register special page
if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}

if ( !defined( 'BOARDVOTE_REDIRECT_ONLY' ) ) {
	extAddSpecialPage( dirname(__FILE__) . '/BoardVote_body.php', 'Boardvote', 'BoardVotePage' );
	$wgExtensionFunctions[] = 'wfSetupBoardVote';
} else {
	extAddSpecialPage( dirname(__FILE__) . '/GoToBoardVote_body.php', 'Boardvote', 'GoToBoardVotePage' );
}

function wfSetupBoardVote() {
	wfSetupSession();
	if ( isset( $_SESSION['bvLang'] ) && !isset( $_REQUEST['uselang'] ) ) {
		wfDebug( __METHOD__.": Setting user language to {$_SESSION['bvLang']}\n" );
		$_REQUEST['uselang'] = $_SESSION['bvLang'];
	}
}
function wfBoardVoteInitMessages() {
	static $done = false;
	if ( $done ) return true;
	$done = true;
	require_once( dirname(__FILE__ ) . '/BoardVote.i18n.php' );

	# Add messages
	global $wgMessageCache;
	foreach( $wgBoardVoteMessages as $lang => $messages ) {
		$wgMessageCache->addMessages( $messages, $lang );
	}

	return true;
}



