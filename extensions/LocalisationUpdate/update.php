<?php

$IP = strval( getenv( 'MW_INSTALL_PATH' ) ) !== ''
	? getenv( 'MW_INSTALL_PATH' )
	: realpath( dirname( __FILE__ ) . "/../../" );

require_once( "$IP/maintenance/commandLine.inc" );

$verbose = false;

if ( $argc > 1 && $argv[0] == "verbose" )
	$verbose = true;

$starttime = microtime( true );

// Prevent the script from timing out
set_time_limit( 0 );
ini_set( "max_execution_time", 0 );

LocalisationUpdate::updateMessages( $verbose );

$endtime = microtime( true );
$totaltime = ( $endtime - $starttime );
print "All done in " . $totaltime . " seconds\n";
