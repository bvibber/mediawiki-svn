<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * Implements Special:Ancientpages
 * @ingroup SpecialPage
 */
class AncientPagesPage extends QueryPage {

	function getName() {
		return "Ancientpages";
	}

	function isExpensive() {
		return true;
	}

	function isSyndicated() { return false; }

	function getQueryInfo() {
		return array(
			'tables' => array( 'page', 'revision' ),
			'fields' => array( 'page_namespace AS namespace',
					'page_title AS title',
					'rev_timestamp AS value' ),
			'conds' => array( 'page_namespace' => MWNamespace::getContentNamespaces(),
					'page_is_redirect' => 0,
					'page_latest=rev_id' )
		);
	}

	function usesTimestamps() {
		return true;
	}

	function sortDescending() {
		return false;
	}

	function formatResult( $skin, $result ) {
		global $wgLang, $wgContLang;

		$d = $wgLang->timeanddate( wfTimestamp( TS_MW, $result->value ), true );
		$title = Title::makeTitle( $result->namespace, $result->title );
		$link = $skin->linkKnown(
			$title,
			htmlspecialchars( $wgContLang->convert( $title->getPrefixedText() ) )
		);
		return wfSpecialList($link, htmlspecialchars($d) );
	}
}

function wfSpecialAncientpages() {
	list( $limit, $offset ) = wfCheckLimits();

	$app = new AncientPagesPage();

	$app->doQuery( $offset, $limit );
}
