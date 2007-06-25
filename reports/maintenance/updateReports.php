<?php

/**
 * Script to refresh the report cache for all reports
 *
 * @addtogroup Maintenance
 * @author Rob Church <robchur@gmail.com>
 */

require_once( dirname( __FILE__ ) . '/commandLine.inc' );

$limit = isset( $options['limit'] )
	? $options['limit']
	: $GLOBALS['wgReportCacheLimit'];
	
$reports = isset( $options['reports'] )
	? explode( ',', $options['reports'] )
	: Report::getReports();
	
echo( "Updating report cache\n\n" );

foreach( $reports as $report ) {
	if( class_exists( $report ) ) {
		$obj = new $report;
		if( !$obj->isDisabled() ) {
			echo( "{$report}:\n" );
			ReportCache::recache( $obj, $limit, 'updateReportsCallback' );
		} else {
			echo( "{$report} is disabled\n" );
		}
	} else {
		echo( "Unknown report '{$report}'\n" );
	}
}
echo( "Done!\n" );

function updateReportsCallback( $report, $namespace, $rows ) {
	echo( "\tNamespace {$namespace}\t{$rows} rows\n" );
}

?>