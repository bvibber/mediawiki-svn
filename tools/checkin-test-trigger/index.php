<?php

// Queue a revision update...

ini_set( 'display_errors', false);

$base = dirname(__FILE__);
require "$base/config.php";

if( isset( $_GET['rev'] ) ) {
	$revId = intval($_GET['rev']);
	if( $revId ) {
		$queueFile = "$queueDir/$revId";
		file_put_contents( $queueFile, $revId );
	}
}
