<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * A special page looking for page without any category.
 * @ingroup SpecialPage
 */
// FIXME: Make $requestedNamespace selectable, unify all subclasses into one
class UncategorizedPagesPage extends PageQueryPage {
	var $requestedNamespace = false;

	function getName() {
		return "Uncategorizedpages";
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
			'tables' => array ( 'page', 'categorylinks' ),
			'fields' => array ( 'page_namespace AS namespace',
					'page_title AS title',
					'page_title AS value' ),
			// default for page_namespace is all content namespaces (if requestedNamespace is false)
			// otherwise, page_namespace is requestedNamespace
			'conds' => array ( 'cl_from IS NULL',
					'page_namespace' => ( $this->requestedNamespace!==false ? $this->requestedNamespace : MWNamespace::getContentNamespaces() ),
					'page_is_redirect' => 0 ),
			'join_conds' => array ( 'categorylinks' => array (
					'LEFT JOIN', 'cl_from = page_id' ) )
		);
	}
}

/**
 * constructor
 */
function wfSpecialUncategorizedpages() {
	list( $limit, $offset ) = wfCheckLimits();

	$lpp = new UncategorizedPagesPage();

	return $lpp->doQuery( $offset, $limit );
}
