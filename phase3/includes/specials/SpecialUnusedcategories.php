<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * @ingroup SpecialPage
 */
class UnusedCategoriesPage extends QueryPage {

	// inexpensive?
	function isExpensive() { return true; }

	function __construct() {
		SpecialPage::__construct( 'Unusedcategories' );
	}

	function getPageHeader() {
		return wfMsgExt( 'unusedcategoriestext', array( 'parse' ) );
	}

	function getQueryInfo() {
		return array (
			'tables' => array ( 'page', 'categorylinks' ),
			'fields' => array ( 'page_namespace AS namespace',
					'page_title AS title',
					'page_title AS value' ),
			'conds' => array ( 'cl_from IS NULL',
					'page_namespace' => NS_CATEGORY,
					'page_is_redirect' => 0 ),
			'join_conds' => array ( 'categorylinks' => array (
					'LEFT JOIN', 'cl_to = page_title' ) )
		);
	}

	function formatResult( $skin, $result ) {
		$title = Title::makeTitle( NS_CATEGORY, $result->title );
		return $skin->makeKnownLinkObj( $title, $title->getText() );
	}
}
