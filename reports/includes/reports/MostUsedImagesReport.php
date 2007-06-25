<?php

/**
 * Report lists the most used images
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
class MostUsedImagesReport extends Report {

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
		return 'Mostusedimages';
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
	 * Is it appropriate to allow filtering namespaces?
	 *
	 * @return bool
	 */
	public function allowNamespaceFilter() {
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
			NS_IMAGE,
		);
	}
	
	/**
	 * Return base SQL for the report
	 *
	 * @param Database $dbr Database object being queried
	 * @return string
	 */
	public function getBaseSql( $dbr ) {
		$imagelinks = $dbr->tableName( 'imagelinks' );
		return
			"SELECT
				il_to AS rp_id,
				" . NS_IMAGE . " AS rp_namespace,
				il_to AS rp_title,
				0 AS rp_redirect,
				COUNT(*) AS count
			FROM {$imagelinks}";
	}

	/**
	 * Get the column used for paging when the report is run live
	 *
	 * @return string
	 */
	public function getPagingColumn() {
		return 'il_to';
	}

	/**
	 * Get a partial WHERE clause to filter on namespace when
	 * the report is run live
	 *
	 * @param int $namespace Namespace to limit to
	 * @return string
	 */
	public function getNamespaceClause( $namespace ) {
		// Not applicable for this report
		return '1 = 1';
	}

	/**
	 * Get additional SQL to be inserted between the
	 * conditions and ORDER clauses when the report is run live
	 *
	 * @param Database $dbr Database object being queried
	 * @return string
	 */
	public function getExtraSql( $dbr ) {
		return ' GROUP BY 2, 3';
	}

	/**
	 * Get ORDER BY clauses to be applied when the
	 * report is run live
	 *
	 * @return array
	 */
	public function getOrderingClauses() {
		return array(
			'count DESC',
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
		return array(
			'count' => $row->count,
		);
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
		// Not needed; we forced the use of galleries
	}
	
	/**
	 * Get the namespace to filter results for, or false
	 * to omit filtering
	 *
	 * @return mixed
	 */
	public function getNamespace() {
		return NS_IMAGE;
	}

}

?>