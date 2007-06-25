<?php

/**
 * Report lists all pages which don't exist but have
 * incoming links
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
class WantedPagesReport extends Report {

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
		return 'Wantedpages';
	}
	
	/**
	 * Get a HTML header for the top of the page
	 *
	 * @return string
	 */
	public function getHeader() {
		global $wgLang, $wgWantedPagesThreshold;
		return wfMsgExt( 'wantedpages-header', 'parse',
			$wgLang->formatNum( $wgWantedPagesThreshold ) );
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
		return false;
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
		list( $page, $pagelinks ) = $dbr->tableNamesN( 'page', 'pagelinks' );
		# Note: We have no page identifier, but we can use pagelinks.pl_title
		# instead, since pagers can handle it \o/
		return
			"SELECT
				pl_title AS rp_id,
				pl_namespace AS rp_namespace,
				pl_title AS rp_title,
				0 AS rp_redirect,
				COUNT(*) AS count
			FROM {$pagelinks}
			LEFT JOIN {$page} AS pg1 ON
				pl_namespace = pg1.page_namespace
				AND pl_title = pg1.page_title
			LEFT JOIN {$page} AS pg2 ON
				pl_from = pg2.page_id";
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
			'pg1.page_namespace IS NULL',
			// Excludes links from the MediaWiki namespace; works around
			// an old bug which causes pages like "$1" to appear on the list
			'pg2.page_namespace != 8',
		);
	}
	
	/**
	 * Get the column used for paging when the report is run live
	 *
	 * @return string
	 */
	public function getPagingColumn() {
		return 'pl_title';
	}

	/**
	 * Get a partial WHERE clause to filter on namespace when
	 * the report is run live
	 *
	 * @param int $namespace Namespace to limit to
	 * @return string
	 */
	public function getNamespaceClause( $namespace ) {
		return "pl_namespace = {$namespace}";
	}

	/**
	 * Get additional SQL to be inserted between the
	 * conditions and ORDER clauses when the report is run live
	 *
	 * @param Database $dbr Database object being queried
	 * @return string
	 */
	public function getExtraSql( $dbr ) {
		$count = intval( $GLOBALS['wgWantedPagesThreshold'] ) - 1;
		return
			" GROUP BY 1, 2, 3
			HAVING count > {$count}";
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
		global $wgLang;
		$links = $this->makeLinksLink(
			$title,
			wfMsgExt( 'nlinks', array( 'parsemag', 'escape' ), $params['count'] ),
			$skin
		);
		return "<li>" . $skin->makeLinkObj( $title ) . " ({$links})</li>\n";
	}
	
	/**
	 * Build a "what links here" link with the
	 * specified title as a target
	 *
	 * @param Title $target Title to show links to
	 * @param string $label Link label
	 * @param Skin $skin Skin to use
	 * @return string
	 */
	private function makeLinksLink( $target, $label, $skin ) {
		$wlh = SpecialPage::getTitleFor( 'Whatlinkshere' );
		return $skin->makeKnownLinkObj( $wlh, $label, 'target=' . $target->getPrefixedUrl() );		
	}
	
}

?>