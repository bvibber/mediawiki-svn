<?php

wfBoardVoteInitMessages();

class GoToBoardVotePage extends UnlistedSpecialPage {
	function __construct() {
		parent::__construct( "Boardvote" );
	}

	function execute( $par ) {
		global $wgOut, $wgDBname, $site, $lang, $wgLang, $wgUser;
		global $wgBoardVoteEditCount, $wgBoardVoteCountDate, $wgBoardVoteFirstEdit;

		if ( $wgUser->isLoggedIn() ) {
			$url = 'http://wikimedia.spi-inc.org/index.php?' . wfArrayToCGI( array(
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
		
