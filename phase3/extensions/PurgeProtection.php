<?php

class ArticlePlugin {
	function purge( &$article ) {
		global $wgDBname, $wgUser, $wgRequest, $wgOut;

		if ( $wgDBname == 'enwiki' && $article->mTitle->getPrefixedDBkey() == 'Main_Page' ) {
			$wgOut->setPageTitle( $article->mTitle->getPrefixedText() );
			$wgOut->setArticleRelated( true );
			if ( $wgUser->isSysop() ) {
				if ( $wgRequest->wasPosted() ) {
					$article->_purge();
				} else {
					$action = $article->mTitle->escapeLocalURL( 'action=purge' );
					$wgOut->addWikiText( "$action\n\n" );
	
					$wgOut->addHTML( "<form method=post action=\"$action\">".
						"<input value=\"Purge\" type=submit name=\"purge\">".
						"</form><hr />" );
					$text = $article->getContent( false );
					$wgOut->addWikiText( $text );
				}
			} else {
				$wgOut->addWikiText( "You need to be an administrator to purge the Main Page cache." );
			}
			// Done, don't perform any further handling
			return true;
		} else {
			// Not handled
			return false;
		}
	}
}

$wgArticlePlugins[] = new ArticlePlugin;
?>
