<?
function statistics () {
	global $THESCRIPT , $wikiSQLServer , $wikiStatisticsTitle , $wikiStatTotalPages ;
	global $wikiStatTalkPages , $wikiStatCommaPages , $wikiStatWikipediaNoTalk , $wikiStatSubNoTalk , $wikiStatNoTalk , $wikiStatArticles , $wikiStatJunk , $wikiStatOld , $wikiStatUsers , $wikiStatSysops ;
	$connection=getDBconnection() ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$ret = "" ;
	$ret .= "<h2>$wikiStatisticsTitle</h2><ul>" ;

	$nf1 = "<font color=red><b>" ;
	$nf2 = "</b></font>" ;

	# TOTAL	
	$sql = "SELECT COUNT(*) AS number FROM cur" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$totalPages = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$totalPages$nf2" , $wikiStatTotalPages )."</li>" ;
	mysql_free_result ( $result ) ;

	# /TALK
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title LIKE \"%/Talk\" OR cur_title LIKE \"Talk:%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$talkPages = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$talkPages$nf2" , $wikiStatTalkPages )."</li>" ;
	mysql_free_result ( $result ) ;

	# , NOT /TALK
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title NOT LIKE \"%/Talk\" AND cur_title NOT LIKE \"talk:%\" AND cur_text LIKE \"%,%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$commaPages = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$commaPages$nf2" , $wikiStatCommaPages )."</li>" ;
	mysql_free_result ( $result ) ;

	# WIKIPEDIA NOT /TALK
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title NOT LIKE \"%/Talk\" AND cur_title NOT LIKE \"talk:%\" AND cur_title LIKE \"%ikipedia%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$wikiPages = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$wikiPages$nf2" , $wikiStatWikipediaNoTalk )."</li>" ;
	mysql_free_result ( $result ) ;

	# WIKIPEDIA NOT /TALK
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title LIKE \"%/%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$subPages = $s->number - $talkPages;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$subPages$nf2" , $wikiStatSubNoTalk )."</li>" ;
	mysql_free_result ( $result ) ;

	# RESULT
	$x = $commaPages - $wikiPages ; # Comma (no /Talk) - wiki pages = articles, including subpages
	$ret .= "<li>".str_replace ( "$1" , "$nf1$x$nf2" , $wikiStatNoTalk )."</li>" ;
	$y = $x - $subPages ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$y$nf2" , $wikiStatArticles )."</li>" ;
	$z = $totalPages - $talkPages - $commaPages ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$z$nf2" , $wikiStatJunk )."</li>" ;

	# OLD PAGES
	$sql = "SELECT COUNT(*) as number FROM old" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$p = round ( $oldPages / $totalPages , 2 ) ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$oldPages$nf2" , str_replace ( "$2" , $p , $wikiStatOld ) )."</li>" ;
	mysql_free_result ( $result ) ;


	$ret .= "</ul><hr>" ;
	$ret .= "<h2>User statistics</h2><ul>" ;
	
	# USERS
	$sql = "SELECT COUNT(*) as number FROM user" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$numUser = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$numUser$nf2" , $wikiStatUsers )."</li>" ;
	mysql_free_result ( $result ) ;
	
	# EDITORS AND SYSOPS
	$sql = "SELECT COUNT(*) as number FROM user WHERE user_rights LIKE \"%is_editor%\" OR user_rights LIKE \"%is_sysop%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$numEditors = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$numEditors$nf2" , $wikiStatSysops )."</li>" ;
	mysql_free_result ( $result ) ;

	#mysql_close ( $connection ) ;
	$ret .= "</ul>" ;
	return $ret ;
	}

?>