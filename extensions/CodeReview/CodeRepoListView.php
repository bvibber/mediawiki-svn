<?php

// Special:Code
class CodeRepoListView {

	function execute() {
		global $wgOut;
		$repos = CodeRepository::getRepoList();
		if( !count( $repos ) ){
			$wgOut->addWikiMsg( 'code-no-repo' );
			return;
		}
		$text = '';
		foreach( $repos as $repo ){
			$name = $repo->getName();
			$text .= "* [[Special:Code/$name|$name]]\n";
		}
		$wgOut->addWikiText( $text );
	}
}

