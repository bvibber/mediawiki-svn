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
		
		$desc = $this->mIsBackwards == $this->mDefaultDirection;
		$limit = $this->mLimit + 1;
		
		$sql = $this->report->getBaseSql( $this->mDb );
		$indexField = $this->report->getPagingColumn();
		$where = array();
		
		# Namespace filter
		if( ( $ns = $this->getNamespace() ) !== false )
			$where[] = $this->report->getNamespaceClause( $ns );
		
		# Redirect filter
		if( !$this->getRedirects() )
			$where[] = $this->report->getRedirectClause();
		
		# Paging, indexing and the guts of the Pager logic
		$options = ' ORDER BY ' . $this->mIndexField;
		if( $this->mDefaultDirection ) {
			# Descending
			$options .= ' DESC';
			$op = '<';
		} else {
			# Ascending
			$op = '>';
		}
		$options .= ' LIMIT ' . $limit;
		$where[] = "{$indexField} {$op} " . $this->mDb->addQuotes( $this->mOffset );
		
		$sql .= ' WHERE ' . implode( ' AND ', $where );
		$res = $this->mDb->query( $sql, $fname );
		$this->mResult = new ResultWrapper( $this->mDb, $res );
		$this->preprocessResults();

		$this->extractResultInfo( $this->mOffset, $limit, $this->mResult );
		$this->mQueryDone = true;

		wfProfileOut( $fname );
	}
	
	/**
	 * Loop through the results and do a batch existence
	 * check on all titles
	 */
	private function preprocessResults() {
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
	
	public function getStartBody() {
		return "<ul>\n";
	}
	
	public function getEndBody() {
		return "</ul>\n";
	}
	
	/**
	 blah
	 */
	public function getNamespace() {
		if( $this->report->allowNamespaceFilter() ) {
			$ns = $this->mRequest->getVal( 'namespace', '' );
			return $ns !== '' ? intval( $ns ) : false;
		} else {
			return false;
		}
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