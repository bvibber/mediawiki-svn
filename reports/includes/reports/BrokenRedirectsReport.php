<?php

/**
 * Report generates a list of pages which redirect to
 * non-existing titles
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
class BrokenRedirectsReport extends Report {

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
		return 'Brokenredirects';
	}
	
	/**
	 * Is it appropriate to allow filtering redirects?
	 *
	 * @return bool
	 */
	public function allowRedirectFilter() {
		return false;
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
		) + $GLOBALS['wgContentNamespaces'];
	}

	/**
	 * Return base SQL for the report
	 *
	 * @param Database $dbr Database object being queried
	 * @return string
	 */
	public function getBaseSql( $dbr ) {
		list( $page, $redirect ) = $dbr->tableNamesN( 'page', 'redirect' );
		return
			"SELECT
				p1.page_id AS rp_id,
				p1.page_namespace AS rp_namespace,
				p1.page_title AS rp_title,
				p1.page_is_redirect AS rp_redirect,
				rd_namespace,
				rd_title
			FROM {$redirect}
			LEFT JOIN {$page} AS p1 ON p1.page_id = rd_from
			LEFT JOIN {$page} AS p2 ON rd_namespace = p2.page_namespace AND rd_title = p2.page_title";
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
			'p2.page_id IS NULL',
		);
	}

	/**
	 * Get the column used for paging when the report is run live
	 *
	 * @return string
	 */
	public function getPagingColumn() {
		return 'p1.page_id';
	}

	/**
	 * Get a partial WHERE clause to filter on namespace when
	 * the report is run live
	 *
	 * @param int $namespace Namespace to limit to
	 * @return string
	 */
	public function getNamespaceClause( $namespace ) {
		return "p1.page_namespace = {$namespace}";
	}
	
	/**
	 * Given a result object, extract additional parameters
	 * as a dictionary for later use
	 *
	 * @param object $row Result row
	 * @return array
	 */
	public function extractParameters( $row ) {
		return array(
			'rd_namespace' => $row->rd_namespace,
			'rd_title' => $row->rd_title,
		);
	}
	
	/**
	 * Format an individual result row
	 *
	 * @param Title $rtitle Result title
	 * @param object $row Result row
	 * @param array $params Result parameters
	 * @param Skin $skin User skin
	 * @return string
	 */
	public function formatRow( $rtitle, $row, $params, $skin ) {
		$arrow = $GLOBALS['wgContLang']->getArrow();
		$ttitle = Title::makeTitleSafe( $params['rd_namespace'], $params['rd_title'] );
		return "<li>" . $skin->makeLinkObj( $rtitle ) . " {$arrow} "
			. $skin->makeLinkObj( $ttitle ) . "</li>\n";
	}
	
}

?>