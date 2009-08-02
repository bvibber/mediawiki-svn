<?php

class FlagArticleTabInstaller {
	function __construct( ) { }

	function insertTab( $skin, &$content_actions ) {
		global $wgTitle;
		if ( !$wgTitle->exists() ) {
			return false;
		}
		$linkParam = "page=" . $wgTitle->getPartialURL();
		wfLoadExtensionMessages( 'FlagArticle' );
		$special = SpecialPage::getTitleFor( 'FlagArticle' );
		$content_actions['flagarticle'] = array(
			'class' => false,
			'text' => wfMsgHTML( 'flagarticle-tab' ),
			'href' => $special->getLocalUrl( $linkParam ) );
		return true;
	}
}
