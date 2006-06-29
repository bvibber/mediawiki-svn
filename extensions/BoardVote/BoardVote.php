<?php

# Wikimedia Foundation Board of Trustees Election

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
	die( "Not a valid entry point\n" );
}

# Default settings
$wgBoardVoteDB = "boardvote";
$wgBoardCandidates = array();
$wgGPGCommand = "gpg";
$wgGPGRecipient = "boardvote";
$wgGPGHomedir = false;
$wgGPGPubKey = "C:\\Program Files\\gpg\\pub.txt";
$wgBoardVoteEditCount = 400;
$wgBoardVoteCountDate = '20050530000000';

if ( isset( $wgGroupPermissions ) ) {
	$wgGroupPermissions['boardvote'] = array( 'boardvote' => true );
}

# Register special page
if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/BoardVote_body.php', 'BoardVote', 'BoardVotePage' );

?>
