<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * A special page looking for articles with no article linking to them,
 * thus being lonely.
 * @ingroup SpecialPage
 */
class LonelyPagesPage extends PageQueryPage {

	function getName() {
		return "Lonelypages";
	}
	function getPageHeader() {
		return wfMsgExt( 'lonelypagestext', array( 'parse' ) );
	}

	function sortDescending() {
		return false;
	}

	function isExpensive() {
		return true;
	}
	function isSyndicated() { return false; }

	function getQueryInfo() {
		return array (
			'tables' => array ( 'page', 'pagelinks',
					'templatelinks' ),
			'fields' => array ( "'{$this->getName()}' AS type",
					'page_namespace AS namespace',
					'page_title AS title',
					'page_title AS value' ),
			'conds' => array ( 'pl_namespace IS NULL',
					'page_namespace' => MWNamespace::getContentNamespaces(),
					'page_is_redirect' => 0,
					'tl_namespace IS NULL' ),
			// TODO: test this JOIN
			'join_conds' => array (
					'pagelinks' => array (
						'LEFT JOIN', array (
						'pl_namespace = page_namespace',
						'pl_title = page_title' ) ),
					'templatelinks' => array (
						'LEFT JOIN', array (
						'tl_namespace = page_namespace',
						'tl_title = page_title' ) ) )
		);
	}
}

/**
 * Constructor
 */
function wfSpecialLonelypages() {
	list( $limit, $offset ) = wfCheckLimits();

	$lpp = new LonelyPagesPage();

	return $lpp->doQuery( $offset, $limit );
}
