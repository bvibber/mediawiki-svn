<?

function recentChangesLayout ( &$arr , $embolden = false) {
    if ( count ( $arr ) == 0 ) return "" ;
    global $THESCRIPT , $user , $wikiDiff , $wikiGetDate , $wikiUser , $wikiChanges, $wikiChange, $wikiRCLegend, $wikiEditTypes ;
    $lastDay = "" ;
    $color1 = $user->options["tabLine1"] ;
    $color2 = $user->options["tabLine2"] ;

    if ( in_array ( "is_sysop" , $user->rights ) ) $isSysop = true ;
    else $isSysop = false ;
    
    # Bold watchlist entries?
    if ( $embolden ) {
	$a = getMySQL ( "user" , "user_watch" , "user_id=$user->id" ) ;
	$watchlist = explode ( "\n" , $a ) ;
    } else
    	$watchlist = array () ;

    $xyz = new WikiTitle ;
    $ret = str_replace ( "$1" , $wikiEditTypes["1"] , str_replace ( "$2" , $wikiEditTypes["2"] ,  $wikiRCLegend ) ) ;
    if ( $user->options["changesLayout"] == "table" )
      $ret .= "<table width=\"100%\" border=0 cellpadding=2 cellspacing=0>\n" ;
    else $ret .= "<ul>\n" ; 
    $dummy = wikiLink("x=y") ;
    foreach ( $arr as $s ) {
        $nt = $xyz->getNiceTitle ( $s->cur_title ) ;
        $url = nurlencode ( $s->cur_title ) ;

	if ( $embolden and (in_array ( $nt , $watchlist ) or
	    in_array ( preg_replace ( "/^[^:]*:?([^:]+)\$/" , "\$1" , $nt ) , $watchlist ) ) ) {
	    $b = "<b>";
	    $unb = "</b>";
	} else {
	    $b = $unb = "" ;
	}

        # Adjusting date for user's timezone. Inline timestampAddHour to avoid
        # calling date() and tsc() repeatedly. 
        $adjusted_time_sc = tsc ( $s->cur_timestamp ) + 3600 * $user->options["hourDiff"];
        $day = date ( "l, F d, Y" , $adjusted_time_sc);
        $time = date ( "H:i" , $adjusted_time_sc ) ;
        if ( $day != $lastDay ) {
            $lastDay = $day ;
            $tday = ucfirst ( $wikiGetDate ( $adjusted_time_sc ) ) ;
            if ( $user->options["changesLayout"] == "table" )
              $ret.="<tr><td width=\"100%\" colspan=7".$user->options["tabLine0"]."><b>$tday</b></td></tr>";
            else $ret .= "</ul><b>$tday</b><ul>\n" ;
            $color = $color1 ;
            }
        $u = $s->cur_user_text ;
        if ( $s->cur_user != 0 ) {
            $xyz->SetTitle ( $u ) ;
            $u = "<a href=\"".wikiLink("$wikiUser:$xyz->url")."\">$u</a>" ;
        } elseif ( !$isSysop ) {
	    if ( preg_match ( '/^(\d{0,3}\.\d{0,3}\.\d{0,3}\.)(\d{0,3})$/' , $u , $regs ) )
	    	$u = $regs[1]."xxx" ;
#           $u = "<font color=red>$u</font>" ; # IPs in red, deactivated
        } else {
		$u .= $s->appendix ;
	}
        $comment = trim($s->cur_comment) ;
        if ( $comment == "*" ) $comment = "" ;
        $o_comment = $comment ;
        if ( $s->cur_minor_edit == 1 ) $comment = "<font size=-1><i>$comment</i></font>" ;
        $minor = $wikiEditTypes[$s->cur_minor_edit] ;

        if ( $user->options["changesLayout"] == "table" ) $t = "<tr><td$color valign=top width=0%>$b" ;
        else $t = "<li>$b" ;

        if ( $s->version == "current" ) $t .= "<a href=\"".wikiLink("$url&diff=yes")."\">$wikiDiff</a>&nbsp;" ;
        else if ( $s->version != "" ) $t .= "<a href=\"".wikiLink("$url&oldID=".$s->old_id."&version=".$s->version."&diff=yes")."\">$wikiDiff</a>&nbsp;";
        else $t .= "<a href=\"".wikiLink("$url&diff=yes")."\">$wikiDiff</a>" ;

        if ( $user->options["changesLayout"] == "table" ) $t .= "$unb</td><td$color valign=top>$b" ;
        else $t .= " " ;

        if ( $s->version == "current" ) $t .= "<a href=\"".wikiLink("$url")."\">$nt</a>$unb</td>" ;
        else if ( $s->version != "" ) $t .= "<a href=\"".wikiLink("$url&oldID=".$s->old_id."&version=".$s->version)."\">$nt (".$s->version.")</a>$unb</td>" ;
        else $t .= "<a href=\"".wikiLink("$url")."\">$nt</a>$unb</td>" ;

        if ( $user->options["changesLayout"] == "table" ) $t .= "<td$color valign=top width=0% nowrap>$b$time$unb</td>" ;
        else $t = str_replace ( "$unb</td>" , "; " , $t ) . " $time" ;

        $noc = $s->changes ;
        $changes = ( $noc > 1 ) ? $wikiChanges : $wikiChange;
        if ( $noc > 0 )
          $noc = "$noc <a href=\"".wikiLink("$url&action=history")."\">$changes</a>" ;
        else
          $noc = "";
        
        if ( $user->options["changesLayout"] == "table" ) {
          $t .= "<td$color valign=top width=0% nowrap>$b$noc$unb</td>" ;
          $t .= "<td$color valign=top nowrap>$b$u$unb</td>" ;
          $t .= "<td$color valign=top>$b$minor$unb</td>" ;
          $t .= "<td$color >$comment</td>" ;
          $t .= "</tr>\n" ;
        }
        else { 
          if ( $noc != "" ) $t .= " ($noc)" ;
          $t .= " . . . " ;
          $t .= $u ;
          $t .= " $minor$unb" ;
          if ( $o_comment != "" ) $t .= " <b>[$comment]</b>" ;
          $t .= "</li>\n" ;
        }
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
