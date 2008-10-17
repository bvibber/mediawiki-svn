<?php

// Special:Code/MediaWiki/tag
class CodeTagListView extends CodeView {
	function __construct( $repoName ) {
		parent::__construct();
		$this->mRepo = CodeRepository::newFromName( $repoName );
	}

	function execute() {
		global $wgOut;
		$tags = $this->mRepo->getTagList();
		$name = $this->mRepo->getName();
		$text = '';
		foreach( $tags as $tag ) {
			$msg = "code-status-$tag-desc";
			$exp = wfEmptyMsg( $msg, wfMsg( $msg ) ) ? '' : ' - ' . wfMsgHtml( $msg );
			$text .= "* [[Special:Code/$name/tag/$tag|$tag]]$exp\n";
		}
		$wgOut->addWikiText( $text );
	}
}

