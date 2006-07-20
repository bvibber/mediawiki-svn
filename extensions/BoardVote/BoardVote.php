<?php

# Wikimedia Foundation Board of Trustees Election

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
	die( "Not a valid entry point\n" );
}

# Internationalisation file
require_once( 'BoardVote.i18n.php' );

# Default settings
$wgBoardVoteDB = "boardvote";
$wgBoardCandidates = array();
$wgGPGCommand = "gpg";
$wgGPGRecipient = "boardvote";
$wgGPGHomedir = false;
$wgGPGPubKey = "C:\\Program Files\\gpg\\pub.txt";
$wgBoardVoteEditCount = 400;
$wgBoardVoteCountDate = '20050530000000';

# Vote admins
$wgGroupPermissions['boardvote']['boardvote'] = true;

# Register special page
if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/BoardVote_body.php', 'Boardvote', 'BoardVotePage' );

?>
