<?
function statistics () {
	global $THESCRIPT , $vpage , $wikiStatisticsTitle , $wikiStatTotalPages , $wikiStatistics ;
	global $wikiStatTalkPages , $wikiStatCommaPages , $wikiStatWikipediaNoTalk , $wikiStatSubNoTalk ,
		$wikiStatNoTalk , $wikiStatArticles , $wikiStatJunk , $wikiStatOld , $wikiStatUsers , $wikiStatSysops ,
		$wikiTalk , $wikiUserStatistics , $wikiStatRedirect , $wikiStatSkin , $wikiSkins ;
	
	$vpage->special ( $wikiStatistics ) ;
	$vpage->namespace = "" ;
	
	$connection=getDBconnection() ;
	$ret = "" ;
	$ret .= "<h2>$wikiStatisticsTitle</h2><ul>" ;

	$nf1 = "<font color=red><b>" ;
	$nf2 = "</b></font>" ;
	
	$Talk = ucfirstIntl ( $wikiTalk ) ;
	$talk = $wikiTalk ;

	# TOTAL	
	$sql = "SELECT COUNT(*) AS number FROM cur" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$totalPages = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$totalPages$nf2" , $wikiStatTotalPages )."</li>" ;
	mysql_free_result ( $result ) ;

        # GENUINE ENCYCLOPEDIA
	$sql = "SELECT COUNT(*) AS number FROM cur WHERE 
                  cur_title NOT LIKE \"%/%\" AND
                  cur_title NOT LIKE \"$Talk:%\" AND 
                  cur_title NOT LIKE \"%ikipedi%\" AND 
                  cur_text LIKE \"%,%\" AND
                  cur_text NOT LIKE \"#REDIRECT\"";
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$genuinePages = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$genuinePages$nf2" , $wikiStatArticles )."</li>" ;
	mysql_free_result ( $result ) ;

        # INCLUDING SUBPAGES
	$sql = "SELECT COUNT(*) AS number FROM cur WHERE 
                  cur_title NOT LIKE \"%/$Talk\" AND
                  cur_title NOT LIKE \"$Talk:%\" AND 
                  cur_title NOT LIKE \"%ikipedi%\" AND 
                  cur_text LIKE \"%,%\" AND
                  cur_text NOT LIKE \"#REDIRECT\"";
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$genuineIncludingSubPages = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$genuineIncludingSubPages$nf2" , $wikiStatNoTalk )."</li>" ;
	mysql_free_result ( $result ) ;

	# OLD PAGES
	$sql = "SELECT COUNT(*) as number FROM old" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$oldPages = $s->number ;
	$p = round ( $oldPages / $totalPages , 2 ) ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$oldPages$nf2" , str_replace ( "$2" , $p , $wikiStatOld ) )."</li>" ;
	mysql_free_result ( $result ) ;


	$ret .= "</ul><hr>" ;
	$ret .= "<h2>$wikiUserStatistics</h2><ul>" ;
	
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

	foreach ( array_keys ( $wikiSkins ) AS $skin ) {
		$iskin = $wikiSkins[$skin] ;
		$u = urlencode ( "skin=$iskin" ) ;
		$sql = "SELECT COUNT(*) as number FROM user WHERE user_options LIKE \"%$u%\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		$s = mysql_fetch_object ( $result ) ;
		$ret .= "<li>".str_replace ( array("$1","$2") , array($s->number,$skin) , $wikiStatSkin )."</li>\n" ;
		mysql_free_result ( $result ) ;
		}


	$ret .= "</ul>" ;
	return $ret ;
	}

?>
