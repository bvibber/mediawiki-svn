<?php

/**
 * Report generates a list of pages which redirect to
 * redirect pages
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
class DoubleRedirectsReport extends Report {

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
		return 'Doubleredirects';
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
				r1.rd_namespace AS in_namespace,
				r1.rd_title AS in_title,
				r2.rd_namespace AS tr_namespace,
				r2.rd_title AS tr_title
			FROM {$redirect} AS r1
			LEFT JOIN {$page} AS p1 ON p1.page_id = rd_from
			LEFT JOIN {$page} AS p2 ON r1.rd_namespace = p2.page_namespace AND r1.rd_title = p2.page_title
			LEFT JOIN {$redirect} AS r2 ON p2.page_id = r2.rd_from";
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
			'p2.page_is_redirect = 1',
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
			'in_namespace' => $row->in_namespace,
			'in_title' => $row->in_title,
			'tr_namespace' => $row->tr_namespace,
			'tr_title' => $row->tr_title,
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
		$ititle = Title::makeTitleSafe( $params['in_namespace'], $params['in_title'] );
		$ttitle = Title::makeTitleSafe( $params['tr_namespace'], $params['tr_title'] );
		return "<li>" . $skin->makeLinkObj( $rtitle ) . " {$arrow} "
			. $skin->makeLinkObj( $ititle ) . " {$arrow} "
			. $skin->makeLinkObj( $ttitle ) . "</li>\n";
	}
	
}

?>