<?
function LonelyPages () {
	global $THESCRIPT ;
	global $linkedLinks , $unlinkedLinks , $vpage ;
	global $wikiLonelyPagesTitle , $wikiLonelyPagesText ;
	$vpage->special ( $wikiLonelyPagesTitle ) ;
	$vpage->namespace = "" ;
	$allPages = array () ;
	$ret = $wikiLonelyPagesText ;

	$connection = getDBconnection () ;
	$sql = "SELECT cur_title,cur_linked_links,cur_unlinked_links FROM cur WHERE cur_title NOT LIKE \"User:%\" AND cur_title NOT LIKE \"%alk:%\" AND cur_text NOT LIKE \"#redirect%\" AND cur_text != \"\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) {
		$allPages[ucfirst($s->cur_title)] = $allPages[ucfirst($s->cur_title)] * 1 ;
		$u = explode ( "\n" , $s->cur_linked_links ) ; foreach ( $u as $x ) $allPages[ucfirst($x)] += 1 ;
		$u = explode ( "\n" , $s->cur_unlinked_links ) ; foreach ( $u as $x ) $allPages[ucfirst($x)] += 1 ;
		}
	if ( $result != false ) mysql_free_result ( $result ) ;

	asort ( $allPages ) ;
#	$allPages = array_slice ( $allPages , 0 , 50 ) ;

	$orphans = array () ;
	$v = array_keys ( $allPages ) ;
	foreach ( $v as $x ) {
		if ( $allPages[$x] == 0 )
			array_push ( $orphans , $x ) ;
		}

	asort ( $orphans ) ;
	foreach ( $orphans as $x )
		$ret .= "# [[$x|".$vpage->getNiceTitle($x)."]]<br>\n" ;
	return $ret ;
	}
?>
