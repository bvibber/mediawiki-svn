<?
function protectpage () {
	global $THESCRIPT , $target , $user , $protecting , $newrestrictions , $vpage ;
	global $wikiProtectTitle , $wikiProtectDenied , $wikiProtectNow , $wikiProtectText , $wikiProtectCurrent ;
	$target = str_replace ( "\\\\" , "\\" , $target ) ;
	$target = str_replace ( "\\\\" , "\\" , $target ) ;
	$vpage = new WikiPage ;
	$vpage->title = $title ;
	$vpage->makeSecureTitle () ;
	$ti = $vpage->secureTitle ;
	$vpage->special ( str_replace ( "$1" , $target , $wikiProtectTitle ) ) ;
	$vpage->makeSecureTitle () ;
	if ( !in_array ( "is_sysop" , $user->rights ) ) return $wikiProtectDenied ;
	if ( $protecting == "yes" ) {
		$r = explode ( "," , $newrestrictions ) ;
		$nr = array () ;
		foreach ( $r as $x )
			if ( strtolower ( substr ( $x , 0 , 3 ) ) == "is_" )
				array_push ( $nr , strtolower ( $x ) ) ;
		$nr = implode ( "," , $nr ) ;
		$t = getMySQL ( "cur" , "cur_timestamp" , "cur_title=\"$target\"" ) ;
		setMySQL ( "cur" , "cur_restrictions" , $nr , "cur_title=\"$target\"" ) ;
		$ret = "<font size=\"+2\">".str_replace("$1",$target,str_replace("$2",$nr,$wikiProtectNow))."</font>" ;
		setMySQL ( "cur" , "cur_timestamp" , $t , "cur_title=\"$target\"" ) ;
	} else {
		$p = getMySQL ( "cur" , "cur_restrictions" , "cur_title=\"$target\"" ) ;

		$ret = str_replace("$1",$target,$wikiProtectText) ;
		$ret .= "<br><br><FORM action=\"".wikiLink("special:protectpage&target=".nurlencode($target)."&protecting=yes")."\" method=post>$wikiProtectCurrent\n" ;
		$ret .= "<INPUT TABINDEX=1 TYPE=text NAME=newrestrictions VALUE=\"$p\" SIZE=30>\n" ;
		$ret .= "<INPUT TABINDEX=2 TYPE=submit NAME=save VALUE=\"Save\">" ;
		$ret .= "</FORM>\n" ;
		}
	return "<nowiki>$ret</nowiki>" ;
	}
?>