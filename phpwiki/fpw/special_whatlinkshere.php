<?
function whatLinksHere () {
	global $THESCRIPT , $target , $user , $protecting , $newrestrictions ;
	global $vpage , $target , $wikiLinkhereTitle ;
	global $wikiLinkhereBacklink , $wikiLinkhereNoBacklink , $wikiBacklinkNolink , $wikiBacklinkFollowing ;
	$vpage = new WikiPage ;
	$vpage->title = $title ;
	$vpage->makeSecureTitle () ;
	$ti = $vpage->secureTitle ;
	$niceTarget = $vpage->getNiceTitle ( $target ) ;
	$vpage->special ( str_replace ( "$1" , $niceTarget , $wikiLinkhereTitle ) ) ;
	$vpage->makeSecureTitle () ;

	global $wikiSQLServer ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;

	# The question is kinda long, but I don't want to sort everything out manually, so...
	$question = "SELECT cur_title FROM cur WHERE" ;
	$question .= " cur_linked_links LIKE \"$target\" OR " ;
	$question .= " cur_linked_links LIKE \"$target\n%\" OR" ;
	$question .= " cur_linked_links LIKE \"%\n$target\n%\" OR" ;
	$question .= " cur_linked_links LIKE \"%\n$target\" OR " ;
	$question .= " cur_unlinked_links LIKE \"$target\" OR " ;
	$question .= " cur_unlinked_links LIKE \"$target\n%\" OR" ;
	$question .= " cur_unlinked_links LIKE \"%\n$target\n%\" OR" ;
	$question .= " cur_unlinked_links LIKE \"%\n$target\"" ;

	$result = mysql_query ( $question , $connection ) ;
	$p = array () ;
	if ( $result != "" ) {
		while ( $s = mysql_fetch_object ( $result ) ) array_push ( $p , $s->cur_title ) ;
		mysql_free_result ( $result ) ;
		}

	$question = "SELECT cur_linked_links,cur_unlinked_links FROM cur WHERE cur_title=\"$target\"" ;
	$result = mysql_query ( $question , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	mysql_free_result ( $result ) ;

	$out = explode ( "\n" , $s->cur_linked_links."\n".$s->cur_unlinked_links ) ;
	$dlb = array () ;
	$dnlb = array () ;

	foreach ( $p as $x ) {
		$y = $vpage->getNiceTitle ( $x ) ;
		if ( in_array ( $x , $out ) ) array_push ( $dlb , $y ) ;
		else array_push ( $dnlb , $y ) ;
		}

	asort ( $dlb ) ;
	$dlb = implode ( "]]\n*[[" , $dlb ) ;
	if ( $dlb != "" ) $dlb = "<h3>".str_replace("$1",$niceTarget,$wikiLinkhereBacklink)."</h3>\n*[[$dlb]]\n" ;

	asort ( $dnlb ) ;
	$dnlb = implode ( "]]\n*[[" , $dnlb ) ;
	if ( $dnlb != "" ) $dnlb = "<h3>".str_replace("$1",$niceTarget,$wikiLinkhereNoBacklink)."</h3>\n*[[$dnlb]]\n" ;

	$ret = $dnlb.$dlb ;
	if ( $ret == "" ) $ret = "<h1>".str_replace("$1",$niceTarget,$wikiBacklinkNolink)."</h1>" ;
	else $ret = "<h1>".str_replace("$1",$niceTarget,$wikiBacklinkFollowing)."</h1>\n$ret" ;

	return $ret ;
	}
?>
