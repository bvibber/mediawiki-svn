<?
# This function list the contributions of a user
function contributions () {
	global $THESCRIPT , $target , $user , $protecting , $newrestrictions ;
	global $vpage , $theuser ;
	global $wikiContribTitle , $wikiContribText , $wikiContribDenied ;
	$vpage = new WikiPage ;
	$vpage->title = $title ;
	$vpage->makeSecureTitle () ;
	$ti = $vpage->secureTitle ;
	$vpage->special ( str_replace ( "$1" , $theuser , $wikiContribTitle ) ) ;
	$vpage->makeSecureTitle () ;
	if ( $theuser == "" ) return "<nowiki><h1>$wikiContribDenied</h1></nowiki>" ;
	$theuser = str_replace ( "_" , " " , $theuser ) ;
	$ret = "<nowiki>".str_replace("$1",$theuser,$wikiContribText)."</nowiki>\n" ;

	$connection = getDBconnection () ;

	$question = "select DISTINCT cur_title
                        FROM  cur LEFT OUTER JOIN old ON cur_title = old_title
                        WHERE cur_title NOT REGEXP \"([tT]alk|^Log):\" 
                          AND 
                           ((cur_user_text = \"$theuser\" AND cur_minor_edit <> 1) 
                               OR 
                            (old_user_text = \"$theuser\" AND old_minor_edit <> 1))
                        ORDER BY cur_title";
	$result = mysql_query ( $question , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) 
		$ret .= "* [[".$vpage->getNiceTitle($s->cur_title)."]]\n" ;

	return $ret ;
	}
?>
