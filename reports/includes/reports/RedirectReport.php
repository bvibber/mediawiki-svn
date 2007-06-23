<?php

/**
 * Report generates a list of redirects in the main namespace
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
class RedirectReport extends Report {

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
		return 'Listredirects';
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
		return false;
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
				page_id AS rp_id,
				page_namespace AS rp_namespace,
				page_title AS rp_title,
				page_is_redirect AS rp_redirect,
				rd_namespace,
				rd_title
			FROM {$redirect}
			LEFT JOIN {$page} ON rd_from = page_id";
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
		$ttitle = Title::makeTitleSafe( $params['rd_namespace'], $params['rd_title'] );
		return '<li>' . $skin->makeLinkObj( $rtitle, '', 'redirect=no' ) . ' &rarr; '
			. $skin->makeLinkObj( $ttitle ) . "</li>\n";
	}
	
}

?>