<?
function none () { # Dummy
	return "" ;
	}

function selflinks () {
	global $wikiNone ;
	$connection=getDBconnection() ;

	$sql = "SELECT linked_from FROM linked WHERE linked_from=linked_to" ;
	$result = mysql_query ( $sql , $connection ) ;
	$ret = "" ;
	if ( $result != "" ) {
		$a = array () ;
		while ( $s = mysql_fetch_object ( $result ) ) array_push ( $a , $s->linked_from ) ;
		mysql_free_result ( $result ) ;
		asort ( $a ) ;
		foreach ( $a as $x ) $ret .= "# [[$x]]\n" ;

	} else $ret = strtoupper ( $wikiNone ) ;
	return $ret ;
	}

function talklinks () {
	global $wikiNone ;
	$connection=getDBconnection() ;

	$sql = "SELECT linked_from FROM linked WHERE linked_to=concat(\"Talk:\",linked_from)" ;
	$result = mysql_query ( $sql , $connection ) ;
	$ret = "" ;
	if ( $result != "" ) {
		$a = array () ;
		while ( $s = mysql_fetch_object ( $result ) ) array_push ( $a , $s->linked_from ) ;
		mysql_free_result ( $result ) ;
		asort ( $a ) ;
		foreach ( $a as $x ) $ret .= "# [[$x]]\n" ;

	} else $ret = strtoupper ( $wikiNone ) ;
	return $ret ;
	}

function doubleredirects () {
	global $wikiNone ;
	$connection=getDBconnection() ;

	$sql = "SELECT linked_from,linked_to,cb.cur_text AS rt 
		FROM linked,cur AS ca, cur AS cb
		WHERE linked_from=ca.cur_title 
		AND linked_to=cb.cur_title 
		AND ca.cur_text LIKE \"#REDIRECT %\" 
		AND cb.cur_text LIKE \"#REDIRECT %\"" ;

	$result = mysql_query ( $sql , $connection ) ;
	$ret = "" ;
	if ( $result != "" ) {
		$a = array () ;
		while ( $s = mysql_fetch_object ( $result ) ) {
			$b = explode ( "\n" , $s->rt ) ;
			$s->rt = array_shift ( $b ) ;
			array_push ( $a , $s ) ;
			}
		mysql_free_result ( $result ) ;
		asort ( $a ) ;
		foreach ( $a as $x ) $ret .= "# [[$x->linked_from]] redirects to [[$x->linked_to]], which reads \"$x->rt\"\n" ;

	} else $ret = strtoupper ( $wikiNone ) ;
	return $ret ;
	}

function fileusage () {
	global $wikiNone , $THESCRIPT , $user ;
	$uploaddir = ereg_replace("[A-Za-z0-9_.]+$", "upload", $THESCRIPT);
	$connection=getDBconnection() ;

	$del = "" ;
	if ( in_array ( "is_sysop" , $user->rights ) ) $del = " <tt><a href=\"$1\"><b>Delete this file</b></a></tt>" ;

	$ret = "" ;
	$mydir = opendir ( "upload" ) ;
	while ($entry = readdir($mydir)) {
		if ($entry != '.' && $entry != '..') {
			$file = "$uploaddir/$entry" ;
			$ret .= "# <a href=\"$file\">".htmlspecialchars($entry)."</a>" ;
			$file = "$uploaddir/".str_replace(" ","%20",$entry) ; # For filenames with blanks

			$sql = "SELECT cur_title FROM cur WHERE cur_title NOT LIKE \"Log:%\" AND cur_text LIKE \"%$file%\"" ;
			$result = mysql_query ( $sql , $connection ) ;
			if ( $result != "" ) {
				$a = array () ;
				while ( $s = mysql_fetch_object ( $result ) ) array_push ( $a , $s->cur_title ) ;
				mysql_free_result ( $result ) ;
				}
			if ( count ( $a ) > 0 ) {
				asort ( $a ) ;
				$ret .= " (file is used by [[".implode("]] - [[",$a)."]])" ;
			} else $ret .= " <font color=red>(file is not used in this wikipedia)</font>" ;

			$ret .= str_replace ( "$1" , wikiLink("special:upload&removeFile=$entry") , $del ) ;
			$ret .= "\n" ;
			}
		}
	return $ret ;
	}

function maintenance () {
	global $THESCRIPT , $vpage , $user , $option ;
	global $wikiMaintenance , $wikiMaintenenceDenied , $wikiMaintenanceText , $wikiNone ;
	
	$vpage->special ( $wikiMaintenance ) ;
	$vpage->namespace = "" ;

	if ( !in_array ( "is_sysop" , $user->rights ) ) return $wikiMaintenenceDenied ;

	$ret = str_replace ( "$1" , "/wiki/special:maintenance&option=" , $wikiMaintenanceText ) ;
	if ( $option == "" ) $option = $wikiNone ;
	$ret = str_replace ( "$2" , strtoupper($option) , $ret ) ;

	$qres = $option () ; # This is dangerous!

	$ret = str_replace ( "$3" , $qres , $ret ) ;

	return $ret ;
	}

?>
