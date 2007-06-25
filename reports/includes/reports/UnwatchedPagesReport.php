<?php

/**
 * Report generates a list of pages not being watched
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
class UnwatchedPagesReport extends Report {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Permission required to view the report
	 *
	 * @return string
	 */
	public function getPermission() {
		return 'unwatchedpages';
	}

	/**
	 * Get the name of the report
	 *
	 * @return string
	 */
	public function getName() {
		return 'Unwatchedpages';
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
	 * Exclude redirects?
	 *
	 * @return bool
	 */
	public function excludeRedirects() {
		return true;
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
		list( $page, $watchlist ) = $dbr->tableNamesN( 'page', 'watchlist' );
		return
			"SELECT
				page_id AS rp_id,
				page_namespace AS rp_namespace,
				page_title AS rp_title,
				page_is_redirect AS rp_redirect
			FROM {$page}
			LEFT JOIN {$watchlist} ON ( page_namespace = wl_namespace AND page_title = wl_title )";
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
			'wl_user IS NULL',
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
		$plink = $skin->makeLinkObj( $title );
		$wlink = $skin->makeKnownLinkObj( $title, wfMsgHtml( 'unwatchedpages-watch' ), 'action=watch' );
		return "<li>{$plink} ({$wlink})</li>\n";
	}
	
}

?>