<?php
function myLog( $log ) {
	if ( isset( $_SERVER ) && array_key_exists( 'REQUEST_METHOD', $_SERVER ) ) {
		print( $log . "<br />" );
	} else {
		print( $log . "\n" );
	}
}

$verbose = false;

if ( ( $argc > 0 && $argv[1] == "verbose" ) OR ( isset( $_GET['verbose'] ) ) )
	$verbose = true;

$mtime = microtime();
$mtime = explode( " ", $mtime );
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime;

define( "MEDIAWIKI", true );

$IP = strval( getenv( 'MW_INSTALL_PATH' ) ) !== ''
	? getenv( 'MW_INSTALL_PATH' )
	: realpath( dirname( __FILE__ ) . "/../../" );

if ( file_exists( $IP . "/StartProfiler.php" ) ) {
	require_once( $IP . "/StartProfiler.php" );
} else {
	require_once( $IP . "/includes/ProfilerStub.php" );
}

require_once( $IP . "/includes/AutoLoader.php" );
require_once( $IP . "/includes/Defines.php" );
require_once( $IP . "/LocalSettings.php" );

require_once( $IP . "/includes/Setup.php" );
require_once( $IP . "/install-utils.inc" );


if ( is_callable( "updateMessages" ) )
	updateMessages( $verbose );
else
	myLog( "Error: LocalisationUpdate extension is not (correctly) included in LocalSettings.php" );

$mtime = microtime();
$mtime = explode( " ", $mtime );
$mtime = $mtime[1] + $mtime[0];
$endtime = $mtime;
$totaltime = ( $endtime - $starttime );
myLog( "All done in " . $totaltime . " seconds" );
