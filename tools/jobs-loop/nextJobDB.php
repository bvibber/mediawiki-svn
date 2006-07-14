<?php

$verbose = isset( $argv[1] ) && $argv[1] == '-v';

$user = 'wikiuser';
$password = `/home/wikipedia/bin/wikiuser_pass`;

$dbs = array();

# Connect to enwiki
mysql_connect( 'ariel', $user, $password ) || myerror();
mysql_select_db( 'enwiki' ) || myerror();
( $res = mysql_query( 'SELECT 1 FROM job LIMIT 1' ) ) || myerror();
if ( mysql_num_rows( $res ) ) {
	$dbs[] = 'enwiki';
}
mysql_free_result( $res );

# Now try the rest
mysql_close();
$availableDBs = file( "/home/wikipedia/common/pmtpa.dblist" );
mysql_connect( 'samuel', $user, $password ) || myerror();

foreach ( $availableDBs as $db ) {
	$db = trim( $db );
	if ( $db == 'enwiki' ) {
		continue;
	}
	
	mysql_select_db( $db ) || myerror();
	( $res = mysql_query( 'SELECT 1 FROM job LIMIT 1' ) ) || myerror();
	if ( mysql_num_rows( $res ) ) {
		$dbs[] = $db;
	}
	mysql_free_result( $res );
}
mysql_close();
if ( $verbose ) {
	$n = count( $dbs );
	$stderr = fopen( 'php://stderr', 'w' );
	if ( $n ) {
		$r = mt_rand( 0, $n - 1 );
		fwrite( $stderr, "$n database(s) with jobs pending, selecting #$r\n" );
		echo $dbs[$r] . "\n";
	} else {
		fwrite( $stderr, "No databases with jobs pending\n" );
	}
} else {
	if ( count( $dbs ) ) {
		echo $dbs[ mt_rand( 0, count( $dbs ) - 1 ) ];
	}
}

function myerror() {
	$f = fopen( 'php://stderr', 'w' );
	fwrite( $f, mysql_error() . "\n" );
	exit(1);
}
?>
