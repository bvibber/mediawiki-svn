<?php

// Special:Code/MediaWiki/author
class CodeAuthorListView extends CodeView {
	function __construct( $repoName ) {
		parent::__construct();
		$this->mRepo = CodeRepository::newFromName( $repoName );
	}

	function execute() {
		global $wgOut, $wgLang;
		$authors = $this->mRepo->getAuthorList();
		$repo = $this->mRepo->getName();
		$text = wfMsg( 'code-authors-text' ) . "\n\n";
		$text .= '<strong>' . wfMsg( 'code-author-total', $wgLang->formatNum( count( $authors ) ) )  . "</strong>\n";
		foreach ( $authors as $committer ) {
			if ( $committer ) {
				$text .= "* [[Special:Code/$repo/author/$committer|$committer]]";
				$user = $this->mRepo->authorWikiUser( $committer );
				if ( $user ) {
					$title = htmlspecialchars( $user->getUserPage()->getPrefixedText() );
					$name = htmlspecialchars( $user->getName() );
					$text .= " ([[$title|$name]])";
				}
				$text .= "\n";
			}
		}
		$wgOut->addWikiText( $text );
	}
}
