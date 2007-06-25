<?php

/**
 * Report lists pages with the most incoming links
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
class MostLinkedPagesReport extends Report {

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
		return 'Mostlinked';
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
		return
			"SELECT
				pl_title AS rp_id,
				pl_namespace AS rp_namespace,
				pl_title AS rp_title,
				page_is_redirect AS rp_redirect,
				COUNT(*) AS count
			FROM {$pagelinks}
			LEFT JOIN {$page} ON
				pl_namespace = page_namespace
				AND pl_title = page_title";
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
			'page_id > 0',
		);
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