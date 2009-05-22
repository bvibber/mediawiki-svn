<?php

$IP = strval( getenv( 'MW_INSTALL_PATH' ) ) !== ''
	? getenv( 'MW_INSTALL_PATH' )
	: realpath( dirname( __FILE__ ) . "/../../" );

require_once( "$IP/maintenance/commandLine.inc" );

$verbose = false;

if ( $argc > 1 && $argv[0] == "verbose" )
	$verbose = true;

$mtime = microtime();
$mtime = explode( " ", $mtime );
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime;

LocalisationUpdate::updateMessages( $verbose );

$mtime = microtime();
$mtime = explode( " ", $mtime );
$mtime = $mtime[1] + $mtime[0];
$endtime = $mtime;
$totaltime = ( $endtime - $starttime );
print "All done in " . $totaltime . " seconds\n";
