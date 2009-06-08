<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * implements Special:Uncategorizedcategories
 * @ingroup SpecialPage
 */
class UncategorizedCategoriesPage extends UncategorizedPagesPage {
	var $requestedNamespace = NS_CATEGORY;
	
	function __construct() {
		SpecialPage::__construct( 'Uncategorizedcategories' );
	}
}
