<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * implements Special:Popularpages
 * @ingroup SpecialPage
 */
class PopularPagesPage extends QueryPage {

	function getName() {
		return "Popularpages";
	}

	function isExpensive() {
		# page_counter is not indexed
		return true;
	}
	function isSyndicated() { return false; }

	function getQueryInfo() {
		return array (
			'tables' => array( 'page' ),
			'fields' => array( "'{$this->getName()}' AS type",
		       			'page_namespace AS namespace',
					'page_title AS title',
					'page_counter AS value'),
			'conds' => array( 'page_is_redirect' => 0,
					'page_namespace' => MWNamespace::getContentNamespaces() ) );
	}

	function formatResult( $skin, $result ) {
		global $wgLang, $wgContLang;
		$title = Title::makeTitle( $result->namespace, $result->title );
		$link = $skin->linkKnown(
			$title,
			htmlspecialchars( $wgContLang->convert( $title->getPrefixedText() ) )
		);
		$nv = wfMsgExt(
			'nviews',
			array( 'parsemag', 'escape'),
			$wgLang->formatNum( $result->value )
		);
		return wfSpecialList($link, $nv);
	}
}

/**
 * Constructor
 */
function wfSpecialPopularpages() {
	list( $limit, $offset ) = wfCheckLimits();

	$ppp = new PopularPagesPage();

	return $ppp->doQuery( $offset, $limit );
}
