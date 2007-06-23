<?php

/**
 * Report generates a list of pages without categories
 * in the main, image, template and category namespaces
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
class UncategorisedPagesReport extends Report {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get the name of the report
	 *
	 * @return string
	 */
	public function getName() {
		return 'Uncategorizedpages';
	}
	
	/**
	 * Is it appropriate to allow filtering redirects?
	 *
	 * @return bool
	 */
	public function allowRedirectFilter() {
		return true;
	}
	
	/**
	 * Get a list of namespaces this report can be run
	 * against - false indicates *all* namespaces
	 *
	 * @return mixed
	 */
	public function getApplicableNamespaces() {
		return array(
			NS_MAIN,
			NS_IMAGE,
			NS_TEMPLATE,
			NS_CATEGORY
		);
	}
	
	/**
	 * Return base SQL for the report
	 *
	 * @param Database $dbr Database object being queried
	 * @return string
	 */
	public function getBaseSql( $dbr ) {
		list( $page, $categorylinks ) = $dbr->tableNamesN( 'page', 'categorylinks' );
		return
			"SELECT
				page_id AS rp_id,
				page_namespace AS rp_namespace,
				page_title AS rp_title,
				page_is_redirect AS rp_redirect
			FROM {$page}
			LEFT JOIN {$categorylinks} ON page_id = cl_from";
	}

	/**
	 * Return additional WHERE clauses and other conditions
	 * to which the paging clauses will be appened when
	 * the report runs live
	 *
	 * @param Database $dbr Database object being queried
	 * @return array
	 */
	public function getExtraConditions( $dbr ) {
		return array(
			'cl_from IS NULL',
		);
	}
	
	/**
	 * Given a result object, extract additional parameters
	 * as a dictionary for later use
	 *
	 * @param object $row Result row
	 * @return array
	 */
	public function extractParameters( $row ) {
		return array();
	}
	
	/**
	 * Format an individual result row
	 *
	 * @param Title $title Result title
	 * @param object $row Result row
	 * @param array $params Result parameters
	 * @param Skin $skin User skin
	 * @return string
	 */
	public function formatRow( $title, $row, $params, $skin ) {
		return "<li>" . $skin->makeLinkObj( $title ) . "</li>\n";
	}
	
}

?>