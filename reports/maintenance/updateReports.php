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
	: 1000;
	
$reports = isset( $options['reports'] )
	? explode( ',', $options['reports'] )
	: Report::getReports();
	
echo( "Updating report cache\n\n" );

foreach( $reports as $report ) {
	if( class_exists( $report ) ) {
		echo( "{$report}:\n" );
		ReportCache::recache( new $report, $limit, 'updateReportsCallback' );
	} else {
		echo( "Unknown report '{$report}'\n" );
	}
}
echo( "Done!\n" );

function updateReportsCallback( $report, $namespace, $rows ) {
	echo( "\tNamespace {$namespace}\t{$rows} rows\n" );
}

?>