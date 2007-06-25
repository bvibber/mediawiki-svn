<?php

/**
 * Report lists the most used templates
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
class MostUsedTemplatesReport extends Report {

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
		return 'Mostlinkedtemplates';
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
			NS_TEMPLATE,
		);
	}
	
	/**
	 * Return base SQL for the report
	 *
	 * @param Database $dbr Database object being queried
	 * @return string
	 */
	public function getBaseSql( $dbr ) {
		$templatelinks = $dbr->tableName( 'templatelinks' );
		return
			"SELECT
				tl_title AS rp_id,
				" . NS_TEMPLATE . " AS rp_namespace,
				tl_title AS rp_title,
				0 AS rp_redirect,
				COUNT(*) AS count
			FROM {$templatelinks}";
	}

	/**
	 * Get the column used for paging when the report is run live
	 *
	 * @return string
	 */
	public function getPagingColumn() {
		return 'tl_title';
	}

	/**
	 * Get a partial WHERE clause to filter on namespace when
	 * the report is run live
	 *
	 * @param int $namespace Namespace to limit to
	 * @return string
	 */
	public function getNamespaceClause( $namespace ) {
		return "tl_namespace = {$namespace}";
	}

	/**
	 * Get additional SQL to be inserted between the
	 * conditions and ORDER clauses when the report is run live
	 *
	 * @param Database $dbr Database object being queried
	 * @return string
	 */
	public function getExtraSql( $dbr ) {
		return ' GROUP BY 1, 2, 3';
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
		return "<li>" . $skin->makeLinkObj( $title,
			htmlspecialchars( $title->getText() ) ) . " ({$links})</li>\n";
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