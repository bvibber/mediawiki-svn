<?
function popularpages () {
	global $wikiSQLServer , $vpage ;
	$a = array () ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "SELECT cur_title,cur_counter FROM cur GROUP BY cur_title ORDER BY cur_counter DESC LIMIT 100" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) )
		array_push ( $a , $s ) ;
	if ( $result != false ) mysql_free_result ( $result ) ;

	$ret = "" ;
	$ret .= "<table>\n" ;
	foreach ( $a as $x ) {
		$ret .= "<tr>\n" ;
		$ret .= "<td align=right nowrap>".number_format($x->cur_counter,0)."</td>\n" ;
		$ret .= "<td>[[".$vpage->getNiceTitle($x->cur_title)."]]</td>\n" ;
		$ret .= "</tr>\n" ;
		}
	$ret .= "</table>" ;
	return $ret ;
	}
?>
