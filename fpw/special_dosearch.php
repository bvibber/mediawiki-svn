<?
include_once ( "special_makelog.php" ) ;

function searchLineDisplay ( $v ) {
	global $search ;
	$v = trim(str_replace("\n","",$v)) ;
	$v = str_replace ( "'''" , "" , $v ) ;
	$v = str_replace ( "''" , "" , $v ) ;
	$v = ereg_replace ( "\{\{\{.*\}\}\}" , "?" , $v ) ;
	$v = trim ( $v ) ;
	while ( substr($v,0,1) == ":" ) $v = substr($v,1) ;
	while ( substr($v,0,1) == "*" ) $v = substr($v,1) ;
	while ( substr($v,0,1) == "#" ) $v = substr($v,1) ;
	$v = eregi_replace ( $search , "'''".$search."'''" , $v ) ;
	$v = "<font size=-1>$v</font>" ;
	return $v ;
	}

function doSearch () {
	global $THESCRIPT ;
	global $vpage , $search , $startat , $user ;
	global $wikiSearchTitle , $wikiSearchedVoid , $wikiNoSearchResult ;
	$vpage = new WikiPage ;
	$vpage->special ( $wikiSearchTitle ) ;
	$r = array () ;
	$s = "" ;

	if ( $search == "" ) $s = $wikiSearchedVoid ;
	else {
		$search = wikiRecodeInput ( $search ) ;
		if ( !isset ( $startat ) ) $startat = 1 ;
		$perpage = $user->options["resultsPerPage"] ;
		global $wikiSQLServer ;
		$connection = getDBconnection () ;
		mysql_select_db ( $wikiSQLServer , $connection ) ;

/*
		# Old search algorithm
		$sql = "SELECT * FROM cur WHERE cur_title LIKE \"%$search%\" OR cur_text LIKE \"%$search%\" ORDER BY cur_title" ;
*/

		# New search algorithm
		$totalcnt = 0 ;
		$s2 = str_replace ( "_" , " " , $search ) ;
		$s2 = ereg_replace ( "[^A-Za-z0-9 ]" , "" , $s2 ) ;
		$s2 = str_replace ( "  " , " " , $s2 ) ;
		$s2 = explode ( " " , $s2 ) ;

		$exclude = "cur_title NOT LIKE \"%alk:%\"" ;
		if ( $exclude != "" ) $exclude = "($exclude) AND " ;

		# Phase 1
		$s3 = array () ;
		foreach ( $s2 as $x ) {
			$s4 = "(cur_title LIKE \"%".strtolower(substr($x,0,1)).substr($x,1)."%\" OR cur_title LIKE \"%".ucfirst($x)."%\")" ;
			array_push ( $s3 , $s4 ) ;
			}
		$s3 = implode ( " AND " , $s3 ) ;
		$sql = "SELECT * FROM cur WHERE $exclude( $s3 ) ORDER BY cur_title" ;
		$result = mysql_query ( $sql , $connection ) ;
		if ( $result != "" ) {
			while ( $s = mysql_fetch_object ( $result ) ) {
				if ( $totalcnt+1 >= $startat and count ( $r ) < $perpage )
					array_push ( $r , $s ) ;
					$totalcnt++ ;
				}
			mysql_free_result ( $result ) ;
			}

		# Phase 2
		$s3 = implode ( "%\" AND cur_text LIKE \"%" , $s2 ) ;
		$sql = "SELECT * FROM cur WHERE $exclude(cur_text LIKE \"%$s3%\" ) ORDER BY cur_title" ;
		$result = mysql_query ( $sql , $connection ) ;
		if ( $result != "" ) {
			while ( $s = mysql_fetch_object ( $result ) ) {
				if ( $totalcnt+1 >= $startat and count ( $r ) < $perpage )
					array_push ( $r , $s ) ;
					$totalcnt++ ;
				}
			mysql_free_result ( $result ) ;
			}


		#mysql_close ( $connection ) ;
		}

	if ( $s == "" and count ( $r ) == 0 ) {
		global $wikiUnsuccessfulSearch , $wikiUnsuccessfulSearches ;
		$s = "<h2>".str_replace("$1",$search,$wikiNoSearchResult)."</h2>" ;
		# Appending log page "wikpedia:Unsuccessful searches"
		$now = date ( "Y-m" , time() ) ;
		$logText = "*[[$search]]\n" ;
		makeLog ( str_replace ( "$1" , $now , $wikiUnsuccessfulSearches ) , $logText , str_replace ( "$1" , $search , $wikiUnsuccessfulSearch ) ) ;

	} else if ( $s == "" ) {
		global $wikiFoundHeading , $wikiFoundText ;
		$n = count ( $r ) ;
		$s .= "<table width=\"100%\" bgcolor=\"#FFFFCC\"><tr><td><font size=\"+1\"><b>$wikiFoundHeading</b></font><br>\n" ;
		$n = str_replace ( "$1" , $totalcnt , $wikiFoundText ) ;
		$n = str_replace ( "$2" , $search , $n ) ;
		$s .= "$n</td></tr></table>\n" ;
		$s .= "<table>" ;
		$realcnt = $startat ;
		$minlen = strlen ( $realcnt + count ( $r ) ) ;
		foreach ( $r as $x ) {
			$u = spliti ( "\n" , $x->cur_text ) ;
			$u = spliti ( "--" , $u[0] ) ;
			$y = searchLineDisplay ( array_shift ( $u ) ) ;
			foreach ( $u as $v ) {
				if ( stristr($v,$search) != false ) {
					$y .= "...<br>...".searchLineDisplay($v) ;
					break ;
					}
				}

			for ( $z = $realcnt ; strlen ( $z ) < $minlen ; $z = "0$z" ) ;
			$ct = $vpage->getNiceTitle ( $x->cur_title ) ;
			$s .= "<tr><td valign=top width=20 align=right><b>$z</b></td><td><font face=\"Helvetica,Arial\">'''[[$ct]]'''</font><br>" ;
			$s .= $y ;
			$s .= "</td></tr>" ;
			$realcnt++ ;
			}
		$s .= "</table>" ;
		if ( $totalcnt > $perpage ) {
			$s .= "<nowiki>" ;
			$last = $startat-$perpage ;
			$next = $startat+$perpage ;
			if ( $startat != 1 ) $s .= "<a href=\"".wikiLink("&search=$search&startat=$last")."\">&lt;&lt;</a> | ";
			for ( $a = 1 ; $a <= $totalcnt ; $a += $perpage ) {
				if ( $a != 1 ) $s .= " | " ;
				if ( $a != $startat ) $s .= "<a href=\"".wikiLink("&search=$search&startat=$a")."\">";
				$s .= "$a-" ;
				$s .= $a+$perpage-1 ;
				if ( $a != $startat ) $s .= "</a>" ;
				}
			if ( $startat != $a-$perpage ) $s .= " | <a href=\"".wikiLink("&search=$search&startat=".$next)."\">&gt;&gt;</a>";
			$s .= "</nowiki>" ;
			}
		}

	$vpage->contents = $s ;
	return $vpage->renderPage () ;
	}

?>