<?php

# Add messages
global $wgMessageCache, $wgBoardVoteMessages;
foreach( $wgBoardVoteMessages as $lang => $messages ) {
	$wgMessageCache->addMessages( $messages, $lang );
}

class GoToBoardVotePage extends SpecialPage {
	function __construct() {
		SpecialPage::SpecialPage( "Go_to_board_vote" );
	}

	function execute( $par ) {
		global $wgOut, $wgDBname, $site, $lang, $wgLang, $wgUser;
		global $wgBoardVoteEditCount, $wgBoardVoteCountDate, $wgBoardVoteFirstEdit;

		if ( $wgUser->isLoggedIn() ) {
			$url = 'https://secure.wikimedia.org/wikipedia/test/w/index.php?' . wfArrayToCGI( array(
				'title' => 'Special:Boardvote' . ( $par ? "/$par" : '' ),
				'sid' => session_id(),
				'db' => $wgDBname,
				'site' => $site,
				'lang' => $lang,
				'uselang' => $wgLang->getCode()
			) );
			$wgOut->redirect( $url );
		} else {
			$this->setHeaders();
			$wgOut->addWikiText( wfMsg( "boardvote_notloggedin", $wgBoardVoteEditCount, 
				$wgLang->timeanddate( $wgBoardVoteCountDate ),
				$wgLang->timeanddate( $wgBoardVoteFirstEdit )
			) );
		}

	}
}
		
