<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * @ingroup SpecialPage
 */
class DeadendPagesPage extends PageQueryPage {

	function __construct() {
		SpecialPage::__construct( 'Deadendpages' );
	}

	function getPageHeader() {
		return wfMsgExt( 'deadendpagestext', array( 'parse' ) );
	}

	// inexpensive?
	/**
	 * LEFT JOIN is expensive
	 *
	 * @return true
	 */
	function isExpensive() {
		return true;
	}

	function isSyndicated() { return false; }

	/**
	 * @return false
	 */
	function sortDescending() {
		return false;
	}

	function getQueryInfo() {
		return array(
			'tables' => array( 'page', 'pagelinks' ),
			'fields' => array( 'page_namespace AS namespace',
					'page_title AS title',
					'page_title AS value'
			),
			'conds' => array( 'pl_from IS NULL',
					'page_namespace' => MWNamespace::getContentNamespaces(),
					'page_is_redirect' => 0
			),
			'join_conds' => array( 'pagelinks' => array( 'LEFT JOIN', array(
					'page_id=pl_from'
			) ) )
		);
	}
	
	function getOrderFields() {
		// For some crazy reason ordering by a constant
		// causes a filesort
		if( count( MWNamespace::getContentNamespaces() ) > 1 )
			return array( 'page_namespace', 'page_title' );
		else
			return array( 'page_title' );
	}
}
