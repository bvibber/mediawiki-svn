<?php

wfBoardVoteInitMessages();

class GoToBoardVotePage extends SpecialPage {
	function __construct() {
		parent::__construct( "Boardvote" );
	}

	function execute( $par ) {
		global $wgOut, $wgDBname, $site, $lang, $wgLang, $wgUser;
		global $wgBoardVoteEditCount, $wgBoardVoteCountDate, $wgBoardVoteFirstEdit;

		$this->setHeaders();
		if ( $wgUser->isLoggedIn() ) {
			$url = 'https://wikimedia.spi-inc.org/index.php?' . wfArrayToCGI( array(
				'title' => 'Special:Boardvote' . ( $par ? "/$par" : '' ),
				'sid' => session_id(),
				'db' => $wgDBname,
				'site' => $site,
				'lang' => $lang,
				'uselang' => $wgLang->getCode()
			) );

			$wgOut->addWikiText( wfMsg( "boardvote_redirecting", $url ) );
			$wgOut->addMeta( 'http:Refresh', '20;url=' . htmlspecialchars( $url ) );
		} else {
			$wgOut->addWikiText( wfMsg( "boardvote_notloggedin", $wgBoardVoteEditCount,
				$wgLang->timeanddate( $wgBoardVoteCountDate ),
				$wgLang->timeanddate( $wgBoardVoteFirstEdit )
			) );
		}

	}
}
