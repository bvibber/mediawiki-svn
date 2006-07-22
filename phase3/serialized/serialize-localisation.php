<?php

$optionsWithArgs = array( 'o' );
require_once( dirname(__FILE__).'/../maintenance/commandLine.inc' );
require_once( dirname(__FILE__).'/serialize.php' );

$stderr = fopen( 'php://stderr', 'w' );
if ( !isset( $args[0] ) ) {
	fwrite( $stderr, "No input file specified\n" );
	exit( 1 );
}
$file = $argv[1];
$code = str_replace( 'Messages', '', basename( $file ) );
$code = str_replace( '.php', '', $code );
$code = strtolower( str_replace( '_', '-', $code ) );

/**
 * Truncate the output file now, so that we don't just load back the same cache
 */
if ( isset( $options['o'] ) ) {
	$out = fopen( $options['o'], 'wb' );
	if ( !$out ) {
		fwrite( $stderr, "Unable to open file \"{$options['o']}\" for output\n" );
		exit( 1 );
	}
} else {
	$out = fopen( 'php://stdout', 'wb' );
}

$localisation = Language::getLocalisationArray( $code );
if ( wfIsWindows() ) {
	$localisation = unixLineEndings( $localisation );
}


fwrite( $out, serialize( $localisation ) );

?>
