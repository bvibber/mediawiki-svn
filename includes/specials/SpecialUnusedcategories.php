<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * @ingroup SpecialPage
 */
class UnusedCategoriesPage extends QueryPage {

	function isExpensive() { return true; }

	function getName() {
		return 'Unusedcategories';
	}

	function getPageHeader() {
		return wfMsgExt( 'unusedcategoriestext', array( 'parse' ) );
	}

	function getSQL() {
		$NScat = NS_CATEGORY;
		$dbr = wfGetDB( DB_SLAVE );
		list( $categorylinks, $page, $category ) = $dbr->tableNamesN( 'categorylinks', 'page', 'category' );
		return "SELECT 'Unusedcategories' as type,
				{$NScat} as namespace, page_title as title, page_title as value
				FROM $page
				INNER JOIN $category ON cat_title=page_title
				LEFT JOIN $categorylinks ON cl_inline=cat_id
				WHERE cl_from IS NULL
				AND page_namespace = {$NScat}
				AND page_is_redirect = 0";
	}

	function formatResult( $skin, $result ) {
		$title = Title::makeTitle( NS_CATEGORY, $result->title );
		return $skin->makeLinkObj( $title, $title->getText() );
	}
}

/** constructor */
function wfSpecialUnusedCategories() {
	list( $limit, $offset ) = wfCheckLimits();
	$uc = new UnusedCategoriesPage();
	return $uc->doQuery( $offset, $limit );
}
