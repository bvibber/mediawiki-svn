<?php

/**
 * Customised Pager for Reports
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
class ReportPager extends IndexPager {

	/**
	 * Report we're paging for
	 */
	protected $report = null;
	
	/**
	 * Constructor
	 *
	 * @param Report $report Report to page for
	 */
	public function __construct( Report $report ) {
		$this->report = $report;
		parent::__construct();
		$this->mLimitsShown = array( 50, 100, 250, 500, 1000 );
	}
	
	/**
	 * Get the index field for paging
	 *
	 * @return string
	 */
	public function getIndexField() {
		return 'rp_id';
	}
	
	/**
	 * Execute the report SQL and stash the result
	 */
	public function doQuery() {
		$fname = get_class( $this->report ) . '::doQuery';
		wfProfileIn( $fname );
		
		# Base conditions
		$conds = $this->report->getExtraConditions( $this->mDb );
		if( ( $ns = $this->getNamespace() ) !== false )
			$conds[] = $this->report->getNamespaceClause( intval( $ns ) );
		if( !$this->getRedirects() || $this->report->excludeRedirects() )
			$conds[] = $this->report->getRedirectClause();
			
		# Paging conditions
		if( $this->mDefaultDirection ) {
			# DESC
			$op = ' < ';
			$order = $this->report->getPagingColumn() . ' DESC';
		} else {
			# ASC
			$op = ' > ';
			$order = $this->report->getPagingColumn();
		}
		$options[] = 'ORDER BY ' . implode( ', ', $this->report->getOrderingClauses() + array( $order ) );
		$options[] = 'LIMIT ' . ( $this->mLimit + 1 );
		$conds[] = $this->report->getPagingColumn() . $op . $this->mDb->addQuotes( $this->mOffset );
		
		$sql = $this->report->getBaseSql( $this->mDb )
			. ' WHERE ' . implode( ' AND ', $conds )
			. ' ' . implode( ' ', $options );
		$this->mResult = new ResultWrapper(
			$this->mDb,
			$this->mDb->query( $sql, $fname )
		);
		$this->preprocessResults();

		$this->extractResultInfo( $this->mOffset, $this->mLimit + 1, $this->mResult );
		$this->mQueryDone = true;

		wfProfileOut( $fname );
	}
	
	/**
	 * Loop through the results and do a batch existence
	 * check on all titles
	 */
	protected function preprocessResults() {
		if( $this->mResult->numRows() > 0 ) {
			$batch = new LinkBatch();
			while( $row = $this->mResult->fetchObject() )
				$batch->addObj( Title::makeTitleSafe( $row->rp_namespace, $row->rp_title ) );
			$batch->execute();
			$this->mResult->rewind();
		}
	}
	
	/**
	 * Format an individual result row
	 *
	 * @param object $row Result row
	 * @return string
	 */
	public function formatRow( $row ) {
		return $this->report->formatRow(
			Title::makeTitleSafe( $row->rp_namespace, $row->rp_title ),
			$row,
			$this->report->extractParameters( $row ),
			$this->getSkin()
		);
	}
	
	/**
	 * Build the navigation bar with paging and limit links
	 *
	 * @return string
	 */
	public function getNavigationBar() {
		foreach( array( 'first', 'last', 'prev', 'next' ) as $link )
			$labels[$link] = wfMsgHtml( 'report-paging-' . $link );
		return '( ' . implode( ' | ', $this->getPagingLinks( $labels ) ) . ' ) ( '
			. implode( ' | ', $this->getLimitLinks() ) . ' )';
	}
	
	/**
	 * Return start-of-list markup
	 *
	 * @return string
	 */
	public function getStartBody() {
		return "<ul>\n";
	}
	
	/**
	 * Return end-of-list markup
	 *
	 * @return string
	 */
	public function getEndBody() {
		return "</ul>\n";
	}
	
	/**
	 * Get the namespace to filter results for, or false
	 * to omit filtering
	 *
	 * @return mixed
	 */
	public function getNamespace() {
		return $this->report->getNamespace();
	}
	
	/**
	 * Include redirects?
	 *
	 * @return bool
	 */
	public function getRedirects() {
		return $this->report->allowRedirectFilter()
			? $this->mRequest->getCheck( 'redirects' )
			: true;
	}
	
	/**
	 * Not used in this implementation, but PHP requires
	 * that all abstract functions are catered for...
	 */
	public function getQueryInfo() {}

}

?>