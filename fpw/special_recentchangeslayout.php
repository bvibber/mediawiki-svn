<?
# Changes the date in recentChangesLayout() according to user setting; can be used similar elsewhere
function timestampAddHour ( $x , $d ) {
	$x = tsc ( $x ) ;
	$x += $d * 3600 ;
	$x = date ( "YmdHis" , $x ) ;
	return $x ;
	}

function recentChangesLayout ( &$arr ) {
	if ( count ( $arr ) == 0 ) return "" ;
	global $THESCRIPT , $user , $wikiDiff ;
	$lastDay = "" ;
	$color1 = $user->options["tabLine1"] ;
	$color2 = $user->options["tabLine2"] ;

	# Correcting time difference
	$arr2 = array () ;
	foreach ( $arr as $y ) {
		$y->cur_timestamp = timestampAddHour ( $y->cur_timestamp , $user->options["hourDiff"] ) ;
		array_push ( $arr2 , $y ) ;
		}
	$arr = $arr2 ;
	$arr2 = array () ;

	global $wikiRCLegend ;

	if ( in_array ( "is_sysop" , $user->rights ) ) $isSysop = true ;
	else $isSysop = false ;

	$xyz = new WikiTitle ;
	$editTypes = array ( "0"=>"" , "1"=>"<font color=cyan>M</font>" , "2"=>"<font color=green>N</font>" ) ;
	$ret = str_replace ( "$1" , $editTypes["1"] , str_replace ( "$2" , $editTypes["2"] ,  $wikiRCLegend ) ) ;
	if ( $user->options["changesLayout"] == "table" ) $ret .= "<table width=\"100%\" border=0 cellpadding=2 cellspacing=0>\n" ;
	else $ret .= "<ul>\n" ; 
	$dummy = wikiLink("x=y") ;
	foreach ( $arr as $s ) {
		$nt = $xyz->getNiceTitle ( $s->cur_title ) ;
		$url = nurlencode ( $s->cur_title ) ;
		$day = date ( "l, F d, Y" , tsc ( $s->cur_timestamp ) ) ;
		$time = date ( "H:i" , tsc ( $s->cur_timestamp ) ) ;
		if ( $day != $lastDay ) {
			$lastDay = $day ;
			$tday = wikiGetDate ( tsc ( $s->cur_timestamp ) ) ;
			if ( $user->options["changesLayout"] == "table" ) $ret.="<tr><td width=\"100%\" colspan=7".$user->options["tabLine0"]."><b>$tday</b></td></tr>";
			else $ret .= "</ul><b>$tday</b><ul>\n" ;
			$color = $color1 ;
			}
		$u = $s->cur_user_text ;
		if ( $s->cur_user != 0 ) {
			$xyz->SetTitle ( $u ) ;
			$u = "<a href=\"".wikiLink("user:$xyz->url")."\">$u</a>" ;
		} elseif ( !$isSysop ) {
			$u = explode ( "." , $u ) ;
			$u = $u[0].".".$u[1].".".$u[2].".xxx" ;
#			$u = "<font color=red>$u</font>" ; # IPs in red, deactivated
			}
		$comment = trim($s->cur_comment) ;
		if ( $comment == "*" ) $comment = "" ;
		$o_comment = $comment ;
		if ( $s->cur_minor_edit == 1 ) $comment = "<font size=-1><i>$comment</i></font>" ;
		$minor = $editTypes[$s->cur_minor_edit] ;

		if ( $user->options["changesLayout"] == "table" ) $t = "<tr><td$color valign=top width=0%>" ;
		else $t = "<li>" ;

		if ( $s->version == "current" ) $t .= "<a href=\"".wikiLink("$url&diff=yes")."\">$wikiDiff</a>&nbsp;" ;
		else if ( $s->version != "" ) $t .= "<a href=\"".wikiLink("$url&oldID=$s->old_id&version=$s->version&diff=yes")."\">$wikiDiff</a>&nbsp;";
		else $t .= "<a href=\"".wikiLink("$url&diff=yes")."\">$wikiDiff</a>" ;

		if ( $user->options["changesLayout"] == "table" ) $t .= "</td><td$color valign=top>" ;
		else $t .= " " ;

		if ( $s->version == "current" ) $t .= "<a href=\"".wikiLink("$url")."\">$nt</a></td>" ;
		else if ( $s->version != "" ) $t .= "<a href=\"".wikiLink("$url&oldID=$s->old_id&version=$s->version")."\">$nt ($s->version)</a></td>" ;
		else $t .= "<a href=\"".wikiLink("$url")."\">$nt</a>" ;

		if ( $user->options["changesLayout"] == "table" ) $t .= "<td$color valign=top width=0% nowrap>$time</td>" ;
		else $t = str_replace ( "</td>" , "; " , $t ) . " $time" ;

		$noc = $s->changes ;
		if ( $noc > 1 ) $noc = "$noc <a href=\"".wikiLink("$url&action=history")."\">changes</a>" ;
		if ( $user->options["changesLayout"] == "table" ) $t .= "<td$color valign=top width=0% nowrap>$noc</td>" ;
		else { 
			if ( $noc != "" ) $t .= " ($noc)" ;
			$t .= " . . . " ;
			}


		if ( $s->version != "" ) {
			$v = new wikiTitle ;
			$v->SetTitle ( $s->cur_user_text ) ;
			if ( $user->options["changesLayout"] == "table" ) $t .= "<td$color valign=top nowrap>" ;
			if ( $s->cur_user == 0 ) $t .= "$s->cur_user_text</td>" ;
			else $t .= "<a href=\"".wikiLink("user:$v->url")."\">$s->cur_user_text</a></td>" ;
			if ( $user->options["changesLayout"] == "table" ) $t .= "</td>" ;
			else $t .= "; " ;
			}
		else {
			if ( $user->options["changesLayout"] == "table" ) $t .= "<td$color valign=top nowrap>$u</td>" ;
			else $t .= $u ;
			}
		if ( $user->options["changesLayout"] == "table" ) $t .= "<td$color valign=top>$minor</td>" ;
		else $t .= " $minor" ;
		if ( $user->options["changesLayout"] == "table" ) $t .= "<td$color >$comment</td>" ;
		else if ( $o_comment != "" ) $t .= " <b>[$comment]</b>" ;
		if ( $user->options["changesLayout"] == "table" ) $t .= "</tr>\n" ;
		else $t .= "</li>\n" ;
		$ret .= $t ;
		if ( $color == $color1 ) $color = $color2 ;
		else $color = $color1 ;
		}
	if ( $user->options["changesLayout"] == "table" ) $ret .= "</table>" ;
	else {
		$ret = "$ret</ul>\n" ;
		$ret = str_replace ( "</td>" , "" , $ret ) ;
		}
	return "<nowiki>$ret</nowiki>" ;
	}

?>