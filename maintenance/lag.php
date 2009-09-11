<?php

$wgUseNormalUser = true;
require_once('commandLine.inc');

if ( isset( $options['r'] ) ) {
	print 'time     ';
	foreach( $wgDBservers as $i => $server ) {
		$hostname = gethostbyaddr( $wgDBservers[$i]['host'] );
		$hostname = str_replace( '.pmtpa.wmnet', '', $hostname );
		printf("%-12s ", $hostname );
	}
	print("\n");
	
	while( 1 ) {
		$lags = $wgLoadBalancer->getLagTimes();
		unset( $lags[0] );
		print( date( 'H:i:s' ) . ' ' );
		foreach( $lags as $i => $lag ) {
			printf("%-12s " , $lag === false ? 'false' : $lag );
		}
		print("\n");
		sleep(5);
	}
} else {
	$lb = wfGetLB();
	$lags = $lb->getLagTimes();
	foreach( $lags as $i => $lag ) {
		$name = $lb->getServerName( $i );
		printf("%-20s %s\n" , $name, $lag === false ? 'false' : $lag );
	}
}
?>
