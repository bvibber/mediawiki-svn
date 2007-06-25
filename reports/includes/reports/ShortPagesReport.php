<?php

/**
 * Report generates a list of short content pages
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
class ShortPagesReport extends Report {

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
		return 'Shortpages';
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
		$page = $dbr->tableName( 'page' );
		# Even with the improved index, MySQL still has to be
		# forced to use it...
		$index = $dbr->useIndexClause( 'page_len' );
		return
			"SELECT
				page_id AS rp_id,
				page_namespace AS rp_namespace,
				page_title AS rp_title,
				page_is_redirect AS rp_redirect,
				page_len
			FROM {$page} {$index}";
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
			'page_len < ' . intval( $GLOBALS['wgShortPagesThreshold'] )
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
			'page_len' => $row->page_len,
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
		return '<li>' . $skin->makeLinkObj( $title ) . ' [' . $skin->formatSize( $params['page_len'] )
			. "]</li>\n";
	}
	
}

?>