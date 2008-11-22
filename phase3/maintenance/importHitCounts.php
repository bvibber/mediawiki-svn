<?php

require( 'commandLine.inc' );

$dbw = wfGetDB( DB_MASTER );

## Hack for small numbers of articles.
$articleIDCache = array();

$dbw->begin();

while ($line = readline( '> ' )) {
	list( $timestamp, $data ) = explode( ':', $line, 2 );
	
	list( $lang, $article, $hits, $bytes ) = explode( ' ', $data );
	
	## Get the timestamp in a sensible format.
	list ($date,$time) = explode( '-', $timestamp );
	$year = substr($date,0,4);
	$month = substr($date,4,2);
	$day = substr($date,6,2);
	$hour = substr($time,0,2);
	$minute = substr($time,2,2);
	$second = substr($time,4,2);
	
	$periodStart = mktime( $hour, $minute, $second, $month, $day, $year );
	$periodEnd = $periodStart + 3600; ## Hard-coded 1 hour
	
	print "Hit count of $hits for $article over time period ".date('c', $periodStart)." to ".date('c', $periodEnd)."\n";
	
	## Import into the database...
	if (empty($articleIDCache[$article])) {
		$article_obj = new Article( Title::newFromText( $article ) );
		$articleIDCache[$article] = $article_obj->getID();
	}
	
	$article_id = $articleIDCache[$article];
	
	$dbw->replace( 'hit_statistics', 
		array( array( 'hs_page', 'hs_period_start' ) ),
		array( 'hs_page' => $article_id, 'hs_period_start' => $dbw->timestamp($periodStart), 'hs_period_end' => $dbw->timestamp($periodEnd), 'hs_period_length' => 3600, 'hs_count' => $hits ),
		__METHOD__ );
}

$dbw->commit();