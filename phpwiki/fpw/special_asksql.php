<?
# A little hack for direct MySQL access; for sysops only!
function askSQL () {
	global $THESCRIPT , $wikiAskSQLtext , $Save , $question ;
	$ret = "" ;
	if ( isset ( $Save ) ) {
		$ret .= "$question<br>" ;
		unset ( $Save ) ;
		global $wikiSQLServer ;
		$connection = getDBconnection () ;
		mysql_select_db ( $wikiSQLServer , $connection ) ;
		$question = str_replace ( "\\\"" , "\"" , $question ) ;
		$result = mysql_query ( $question , $connection ) ;
		$n = mysql_num_fields ( $result ) ;
		$k = array () ;
		for ( $x = 0 ; $x < $n ; $x++ ) array_push ( $k , mysql_field_name ( $result , $x ) ) ;
		$a = array () ;
		while ( $s = mysql_fetch_object ( $result ) ) {
			array_push ( $a , $s ) ;
			}
		mysql_free_result ( $result ) ;

		$ret .= "<table width=\"100%\" border=1 bordercolor=black cellspacing=0 cellpadding=2><tr>" ;
		foreach ( $k as $x ) $ret .= "<th>$x</th>" ;
		$ret .= "</tr><tr>" ;
		foreach ( $a as $y ) {
			foreach ( $k as $x ) $ret .= "<td>".$y->$x."</td>" ;
			$ret .= "</tr><tr>" ;
			}
		$ret .= "</tr></table>" ;
		}
	$form = $wikiAskSQLtext ;
	$form .= "<FORM method=POST>" ;
	$form .= "<input type=text value=\"$question\" name=question size=110> \n" ;
	$form .= "<input type=submit value=Ask name=Save> \n" ;
	$form .= "</FORM>" ;
	return $form.$ret ;
	}
?>
