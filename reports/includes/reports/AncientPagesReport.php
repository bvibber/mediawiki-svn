<?php

/**
 * Report lists pages on the wiki which haven't been
 * updated in a while
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
class AncientPagesReport extends Report {

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
		return 'Ancientpages';
	}

	/**
	 * Get a HTML header for the top of the page
	 *
	 * @return string
	 */
	public function getHeader() {
		global $wgLang, $wgAncientPagesThreshold;
		return wfMsgExt( 'ancientpages-header', 'parse',
			$wgLang->formatNum( $wgAncientPagesThreshold ) );
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
	 * Should redirects be filtered from results?
	 *
	 * @return bool
	 */
	public function excludeRedirects() {
		return true;
	}
	
	/**
	 * Is it appropriate to allow filtering namespaces?
	 *
	 * @return bool
	 */
	public function allowNamespaceFilter() {
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
		) + $GLOBALS['wgContentNamespaces'];
	}
	
	/**
	 * Return base SQL for the report
	 *
	 * @param Database $dbr Database object being queried
	 * @return string
	 */
	public function getBaseSql( $dbr ) {
		list( $page, $revision ) = $dbr->tableNamesN( 'page', 'revision' );
		return
			"SELECT
				page_id AS rp_id,
				page_namespace AS rp_namespace,
				page_title AS rp_title,
				page_is_redirect AS rp_redirect,
				rev_timestamp
			FROM {$page}
			LEFT JOIN {$revision} ON page_id = rev_page";
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
			'rev_id = page_latest',
			'rev_timestamp < ' . $dbr->timestamp( time() - ( $GLOBALS['wgAncientPagesThreshold'] * 86400 ) ),
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
			'rev_timestamp' => $row->rev_timestamp,
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
		global $wgLang;
		$time = $wgLang->timeAndDate( $params['rev_timestamp'], true );
		return "<li>" . $skin->makeLinkObj( $title ) . " [{$time}]</li>\n";
	}
	
}

?>