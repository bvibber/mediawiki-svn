<?php

if( php_sapi_name() != 'cli' ) {
	die("");
}

if( !isset( $argv[1] ) ) {
	die( "You must provide a vote log file on the command line.\n" );
}

$end = "-----END PGP MESSAGE-----";
$contents = file_get_contents( $argv[1] );
if( $contents === false ) {
	die( "Couldn't open input file.\n" );
}
$entries = explode( $end, $contents );

$tally = array();
$infile = tempnam( "/tmp", "gpg" );
$outfile = tempnam( "/tmp", "gpg" );

foreach ( $entries as $entry ) {
	$entry = trim( $entry.$end );
	
	if ( $entry == $end ) {
		continue;
	}
#	print "{{{$entry}}}\n\n";
	$file = fopen( $infile, "w" );
	fwrite( $file, trim( $entry ) . "\n" );
	fclose( $file );
	`gpg -q --batch --yes -do $outfile $infile`;
	$lines = file( $outfile );
	$set = process_line( $lines[0] );
	foreach ( $set as $c ) {
		if  ( !array_key_exists( $c, $tally ) ) {
			$tally[$c] = 0;
		}
		$tally[$c]++;
	}
}

unlink( $infile );
unlink( $outfile );

arsort( $tally );

foreach ( $tally as $candidate => $count ) {
	printf( "%-30s%d\n", $candidate, $count );
}
	

#-----------------------------------------------------------

function process_line( $line )
{
	$importantBit = substr( $line, strpos( $line, ":" ) + 1 );
	$set = array_map( "trim", explode( ",", $importantBit ) );
	if ( $set[0] == "" ) {
		$set = array();
	}
	return $set;
}


