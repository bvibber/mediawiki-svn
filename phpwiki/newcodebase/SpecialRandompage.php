<?

function wfSpecialRandompage()
{
	global $wgOut, $wgTitle, $wgArticle, $force;
	$fname = "wfSpecialRandompage";

	wfSeedRandom();
	$sqlget = "SELECT ra_title FROM random WHERE ra_current=1 " .
	  "ORDER BY RAND() LIMIT 1";
	$res = wfQuery( $sqlget, $fname );
	if ( 0 == wfNumRows( $res ) or $force == "yes") { # Time to reload
		# Reload 1000 random titles into random table.  Doing it this
		# roundabout way avoids the need for any locking.
		#
		$sql = "INSERT INTO random(ra_current,ra_title) SELECT 0,cur_title " .
		  "FROM cur WHERE cur_namespace=0 AND cur_is_redirect=0 " .
		  "ORDER BY RAND() LIMIT 1000";
		wfQuery( $sql, $fname );

		$sql = "UPDATE random SET ra_current=(ra_current+1)";
		wfQuery( $sql, $fname );

		$sql = "DELETE FROM random WHERE ra_current>1";
		wfQuery( $sql, $fname );

		$res = wfQuery( $sqlget, $fname );
	}
	$s = wfFetchObject( $res );
	$sql = "UPDATE random SET ra_current=(ra_current+1) WHERE ra_title='"
		. wfStrencode( $s->ra_title ) . "'";
	wfQuery( $sql, $fname );
	$rt = wfUrlEncode( $s->ra_title );

	$wgOut->reportTime(); # for logfile
	$wgOut->redirect( wfLocalUrl( $rt ) );
}

?>
