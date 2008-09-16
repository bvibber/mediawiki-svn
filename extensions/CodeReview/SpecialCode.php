<?php
if (!defined('MEDIAWIKI')) die();

class SpecialCode extends SpecialPage {
	function __construct() {
		parent::__construct( 'Code' );
	}

	function execute( $subpage ) {
		global $wgOut, $wgRequest, $wgUser, $wgScriptPath;

		wfLoadExtensionMessages( 'CodeReview' );

		$this->setHeaders();
		$wgOut->addStyle( "$wgScriptPath/extensions/CodeReview/codereview.css" );

		if( $subpage == '' ) {
			$view = new CodeRepoListView();
		} else {
			$params = explode( '/', $subpage );
			switch( count( $params ) ) {
			case 1:
				$view = new CodeRevisionListView( $params[0] );
				break;
			case 2:
				$view = new CodeRevisionView( $params[0], $params[1] );
				break;
			case 4:
				if( $params[2] == 'reply' ) {
					$view = new CodeRevisionView( $params[0], $params[1], $params[3] );
					break;
				}
			default:
				throw new MWException( "Unexpected number of parameters" );
			}
		}
		$view->execute();
	}
}

/**
 * Extended by CodeRevisionListView and CodeRevisionView
 */
abstract class CodeView {
	var $mRepo;

	function __construct() {
		global $wgUser;
		$this->mSkin = $wgUser->getSkin();
	}

	abstract function execute();

	function authorLink( $author ) {
		static $userLinks = array();
		if( isset( $userLinks[$author] ) )
			return $userLinks[$author];

		$dbr = wfGetDB( DB_SLAVE );
		$wikiUser = $dbr->selectField(
			'code_authors',
			'ca_user_text',
			array(
				'ca_repo_id' => $this->mRepo->getId(),
				'ca_author' => $author,
			),
			__METHOD__
		);
		$user = null;
		if( $wikiUser )
			$user = User::newFromName( $wikiUser );
		if( $user instanceof User )
			$link = $author . ' (' . $this->mSkin->userLink( $user->getId(), $user->getName() ) . ')';
		else
			$link = htmlspecialchars( $author );
		return $userLinks[$author] = $link;
	}

	function formatMessage( $text ){
		$text = nl2br( htmlspecialchars( $text ) );
		$linker = new CodeCommentLinkerHtml( $this->mRepo );
		return $linker->link( $text );
	}

	function messageFragment( $value ) {
		global $wgLang;
		$message = trim( $value );
		$lines = explode( "\n", $message, 2 );
		$first = $lines[0];
		$trimmed = $wgLang->truncate( $first, 60, '...' );
		return $this->formatMessage( $trimmed );
	}
}

class CodeCommentLinker {
	function __construct( $repo ) {
		global $wgUser;
		$this->mSkin = $wgUser->getSkin();
		$this->mRepo = $repo;
	}

	function link( $text ) {
		$text = preg_replace_callback( '/\br(\d+)\b/', array( $this, 'messageRevLink' ), $text );
		$text = preg_replace_callback( '/\bbug #?(\d+)\b/i', array( $this, 'messageBugLink' ), $text );
		return $text;
	}

	function messageBugLink( $arr ){
		$text = $arr[0];
		$bugNo = intval( $arr[1] );
		$url = $this->mRepo->getBugPath( $bugNo );

		return $this->makeExternalLink( $url, $text );
	}

	function messageRevLink( $matches ) {
		$text = $matches[0];
		$rev = intval( $matches[1] );

		$repo = $this->mRepo->getName();
		$title = SpecialPage::getTitleFor( 'Code', "$repo/$rev" );

		return $this->makeInternalLink( $title, $text );
	}

}

class CodeCommentLinkerHtml extends CodeCommentLinker {
	function makeExternalLink( $url, $text ) {
		return $this->mSkin->makeExternalLink( $url, $text );
	}

	function makeInternalLink( $title, $text ) {
		return $this->mSkin->link( $title, $text );
	}
}

class CodeCommentLinkerWiki extends CodeCommentLinker {
	function makeExternalLink( $url, $text ) {
		return "[$url $text]";
	}

	function makeInternalLink( $title, $text ) {
		return "[[" . $title->getPrefixedText() . "|$text]]";
	}
}
