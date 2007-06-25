<?php

/**
 * ReportPager which draws from the report cache, rather
 * than running reports live
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
class CachedReportPager extends ReportPager {

	/**
	 * Constructor
	 *
	 * @param Report $report Report to page for
	 */
	public function __construct( Report $report ) {
		parent::__construct( $report );
	}

	/**
	 * Execute the report SQL and stash the result
	 */
	public function doQuery() {
		$fname = get_class( $this->report ) . '::doQuery';
		wfProfileIn( $fname );
		
		# Base conditions
		$conds['rp_report'] = $this->report->getName();
		if( ( $ns = $this->getNamespace() ) !== false )
			$conds['rp_namespace'] = intval( $ns );
		if( !$this->getRedirects() || $this->report->excludeRedirects() )
			$conds['rp_redirect'] = 0;
		
		# Paging conditions
		if( $this->mDefaultDirection ) {
			# DESC
			$op = ' < ';
			$options['ORDER BY'] = $this->getIndexField() . ' DESC';
		} else {
			# ASC
			$op = ' > ';
			$options['ORDER BY'] = $this->getIndexField();
		}
		$options['LIMIT'] = $this->mLimit + 1;
		$conds[] = $this->getIndexField() . $op . $this->mDb->addQuotes( $this->mOffset );
		
		$this->mResult = new ResultWrapper(
			$this->mDb,
			$this->mDb->select( 'reportcache', '*', $conds, $fname, $options )
		);
		$this->preprocessResults();

		$this->extractResultInfo( $this->mOffset, $this->mLimit + 1, $this->mResult );
		$this->mQueryDone = true;

		wfProfileOut( $fname );
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
			ReportCache::decodeParams( $this->mDb->decodeBlob( $row->rp_params ) ),
			$this->getSkin()
		);
	}
	
}

?>