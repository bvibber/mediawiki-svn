<?
# A little hack for direct MySQL access; for sysops only!
# We're already restricted to is_sysop by a check in wikiPage::load()
function askSQL () {
	global $THESCRIPT , $wikiAskSQLtext , $Save , $question , $wikiSQLSafetyMessage , $user ;
	$ret = "" ;
	if ($question == "") {
		$question = "SELECT  ...  FROM  ...  WHERE  ...";
	} else {
		$question = stripslashes ( $question ) ;
	}
	if ( isset ( $Save ) ) {
		$ret .= htmlspecialchars ( $question ) . "<br>" ;
		if ( ! in_array ( "is_developer" , $user->rights ) && strncasecmp($question , "select" , 6) ) {
			$ret .= $wikiSQLSafetyMessage ;
		} else {
			unset ( $Save ) ;
			$connection = getDBconnection () ;
			$result = mysql_query ( $question , $connection ) ;
			if ( $result == FALSE ) {
				$ret .= "<p><b>" . htmlspecialchars ( mysql_error() ) . "</b></p>\n";
			} elseif ( $result == TRUE ) {
				$ret .= "<p><b>Query successful.</b></p>\n" ;
			}
			$n = 0 ;
			@$n = mysql_num_fields ( $result ) ;
			if ( $n ) {
				$k = array () ;
				for ( $x = 0 ; $x < $n ; $x++ ) array_push ( $k , mysql_field_name ( $result , $x ) ) ;
				$a = array () ;
				while ( $s = mysql_fetch_object ( $result ) ) {
					array_push ( $a , $s ) ;
					}
				mysql_free_result ( $result ) ;
		
				$ret .= "<table border=1 bordercolor=black cellspacing=0 cellpadding=2><tr>\n" ;
				foreach ( $k as $x ) $ret .= "<th>$x</th>" ;
				$ret .= "</tr>\n" ;
				foreach ( $a as $y ) {
					$ret .= "<tr>\n";
					foreach ( $k as $x ) $ret .= "<td valign=top>".$y->$x."</td>\n" ;
					$ret .= "</tr>\n" ;
					}
				$ret .= "</table>" ;
				}
			}
		}
	$form = $wikiAskSQLtext ;
	$form .= "<FORM method=POST>\n" ;
	$form .= "<input type=hidden name=Save value=1>\n";
	$form .= "<input type=text value=\"" . htmlspecialchars ( $question ) ."\" name=question size=70> \n" ;
	$form .= "<input type=submit value=Submit> \n" ;
	$form .= "</FORM><p>\n" ;
	return $form.$ret ;
	}
?>
