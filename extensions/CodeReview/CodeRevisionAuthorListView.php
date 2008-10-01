<?php

// Special:Code/MediaWiki/author
class CodeRevisionAuthorListView extends CodeView {
	function __construct( $repoName ) {
		parent::__construct();
		$this->mRepo = CodeRepository::newFromName( $repoName );
	}

	function execute() {
		global $wgOut;
		$authors = $this->mRepo->getAuthorList();
		$name = $this->mRepo->getName();
		$text = '';
		foreach( $authors as $user ) {
			$text .= "* [[Special:Code/$name/author/$user|$user]]\n";
		}
		$wgOut->addWikiText( $text );
	}
}

