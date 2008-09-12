<?php

class SpecialCode extends SpecialPage {
	function __construct() {
		wfLoadExtensionMessages('CodeReview');
		parent::__construct( 'Code' );
	}

	function execute( $subpage ) {
		global $wgOut, $wgRequest, $wgUser;
		$this->setHeaders();
		
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
			default:
				$wgOut->error( "Unexpected number of parameters" );
				return;
			}
		}
		$view->execute();
	}
}



// Special:Code
class CodeRepoListView {
	function __construct() {
	}
	
	function execute() {
		global $wgOut;
		$wgOut->addWikiText( '* [[Special:Code/MediaWiki|MediaWiki]]' );
	}
}

// Special:Code/MediaWiki
class CodeRevisionListView {
	var $mLimit = 20;
	
	function __construct( $repoName ) {
		$this->mRepo = CodeRepository::newFromName( $repoName );
	}
	
	function execute() {
		global $wgOut;
		$wgOut->addWikiText( $this->mRepo->getName() );
		
		$revs = $this->mRepo->getRevisionRange();
		if( $revs ) {
			$table = "<table width=\"100%\">";
			foreach( $revs as $rev ) {
				$table .= $this->formatRevision( $rev );
			}
			$table .= "</table>";
			$wgOut->addHtml( $table );
		}
	}
	
	function formatRevision( CodeRevision $rev ) {
		global $wgLang;
		$encFields = array(
			$rev->getId(),
			$this->messageFragment( $rev ),
			$this->authorLink( $rev ),
			$wgLang->timeanddate( $rev->getTimestamp() ) );
		$cells = '<tr><td>' .
			implode( '</td><td>',
				$encFields ) .
			'</td></tr>';
		return $cells;
	}
	
	function authorLink( $rev ) {
		global $wgUser;
		
		$author = $rev->getAuthor();
		// fixme -- get the user_text field as well
		return $wgUser->getSkin()->userLink( 0, $author );
	}
	
	function messageFragment( $rev ) {
		global $wgLang;
		$message = trim( $rev->getMessage() );
		$lines = explode( "\n", $message, 2 );
		$first = $lines[0];
		$trimmed = $wgLang->truncate( $first, 60, '...' );
		return htmlspecialchars( $trimmed );
	}
}

// Special:Code/MediaWiki/40696
class CodeRevisionView {
}
