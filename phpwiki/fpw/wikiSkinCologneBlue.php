<?
# ##############################################################################
# Cologne Blue skin
# ##############################################################################

class skinCologneBlue extends skinClass {

	function getHeader ( $page ) {
		global $wikiHome , $wikiAbout , $wikiFAQ , $wikiSpecialPages , $wikiLogIn , $wikiLogOut , $wikiHeaderSubtitle , $wikiWikipediaFAQ ;
		global $wikiLogoFile , $user , $wikiHelp , $wikiWikipediaHelp , $wikiWikipedia ;
#		$fonts = "face='verdana,sans-serif' color=white" ;
		$fonts = "face=serif color=white" ;
		$ret .= "<table width='100%' border=0 cellspacing=0 cellpadding=1>\n" ;

		# Row 1
		$ret .= "<tr class=header>\n" ;
		$ret .= "<td><span class=spacer>&nbsp;&nbsp;</span></td>\n" ;
		$ret .= "<td align=left>" ;
		$ret .= "<a class=syslink href=\"".WikiLink("")."\">" ;
		$ret .= "<font size='+5' $fonts>" ; 
		$ret .= strtoupperIntl($wikiWikipedia) ;
		$ret .= "</font></a></td>\n" ;
		$ret .= "<td align=right valign=bottom nowrap>\n" ;
		$ret .= "<p class=topmenu>" ;
		$ret .= "<a class=topmenu href='".WikiLink("")."'>" . strtoupperIntl ( $wikiHome ) . "</a> | " ;
		$ret .= "<a class=topmenu href='".WikiLink("Wikipedia")."'>" . strtoupperIntl ( $wikiAbout ) . "</a> | " ;
		$ret .= "<a class=topmenu href='".WikiLink("$wikiWikipediaHelp")."'>" . strtoupperIntl ( $wikiHelp ) . "</a> | " ;
		$ret .= "<a class=topmenu href='".WikiLink("$wikiWikipediaFAQ")."'>" . strtoupperIntl ( $wikiFAQ ) . "</a> | " ;
		$ret .= "<a class=topmenu href='".WikiLink("special:Special_pages")."'>" . strtoupperIntl ( $wikiSpecialPages ) . "</a> | " ;
		if ( $user->isLoggedIn )
			$ret .= "<a class=topmenu href='".WikiLink("special:userLogout")."'>" . strtoupperIntl ( $wikiLogOut ) ."</a> " ;
		else
			$ret .= "<a class=topmenu href='".WikiLink("special:userLogin")."'>" . strtoupperIntl ( $wikiLogIn ) ."</a> " ;
		$ret .= "</p></td>\n" ;
		$ret .= "<td><span class=spacer>&nbsp;&nbsp;</span></td>\n" ;
		$ret .= "</tr>\n" ;

		#Row 2
		$ret .= "<tr>\n" ;
		$ret .= "<td><span class=spacer>&nbsp;&nbsp;</span></td>\n" ;

		$ret .= "<td valign=top align=left><p class=tagline>" ;
		$ret .= "<a style='color:#000000' href=\"".WikiLink("wikipedia")."\">" ;
		$ret .= strtoupperIntl ( $wikiHeaderSubtitle ) ;
		$ret .= "</a></p></td>" ;

		$ret .= "<td bgcolor=white align=right valign=top><small>" ;
        	if ( count ( $page->otherLanguages ) > 0 ) {
                	global $wikiOtherLanguagesText , $wikiLanguageNames ;
	                $subText = array () ;
	                $olk = array_keys ( $page->otherLanguages ) ;
	                foreach ( $page->otherLanguages as $x => $url )
	                    array_push ( $subText , "<a href=\"".$url."\">".$wikiLanguageNames[$x]."</a>" ) ;
	                $subText = implode ( ", " , $subText ) ;
	                $ret .= str_replace ( "$1" , $subText , $wikiOtherLanguagesText ) ;
	                }

		$ret .= "</small>&nbsp;</td></tr>\n" ;
		$ret .= "<tr><td><div stype='height: 20px'>&nbsp;</div>\n</td></tr></table>" ;

		return $ret ;
		}

	function getQuickBar ( $page ) {
		return $page->getLinkBar () ;
		}

	function getFooter ( $page ) {
		global $wikiSearch , $wikiCategories , $wikiOtherNamespaces , $wikiCounter , $wikiLastChange , $wikiDiff;
		global $wikiGetDate , $framed, $search , $wikiValidate , $user , $THESCRIPT ;
		global $wikiFindMore , $wikiOK , $wikiWikipediaHome , $wikiAboutWikipedia ;
		global $wikiGetDate , $wikiLastChangeCologne , $wikiRequests , $wikiRedirectFrom ;

		$ret = $this->getQuickBar ( $page ) ;
		$ret = "<table width=\"100%\" $border class=\"footer\" cellspacing=0><tr><td>$ret</td></tr></table>" ;

		# Page counter
		if ( !$page->isSpecialPage )
		    $ret .= str_replace ( "$1" , $page->counter , $wikiCounter ) ;

		# Other namespaces
		$a = $page->getOtherNamespaces () ;
		if ( count ( $a ) > 0 ) $ret .= " ".$wikiOtherNamespaces.implode ( " | " , $a )." " ;

		# Last change / Diff
		if ( !$page->isSpecialPage ) {
		    $lc = $wikiGetDate ( tsc ( $page->timestamp ) ) ;
		    $lc .= ", ".substr ( $page->timestamp , 8 , 2 ) ;
		    $lc .= ":".substr ( $page->timestamp , 10 , 2 ) ;
		    $ret .= "<br>\n" ;
		    $ret .= str_replace ( "$1" , $lc , $wikiLastChange ) ;
		    $ret .= " <a href=\"".wikiLink("$page->url&amp;diff=yes")."\">$wikiDiff</a> " ;
		    }

		$ret = "<tr><td colspan=1></td><td>\n" ;
		$ret .= "<FORM class=footnote method=get action=\"$THESCRIPT\">" ;
		$ret .= "$wikiFindMore : " ;
		$ret .= "<INPUT TYPE=text NAME=search SIZE=16 VALUE=\"$search\">" ;
		$ret .= "<INPUT TYPE=submit value=\"$wikiOK\">" ;
		$ret .= " &nbsp; <a class=CBlink href=\"".wikiLink("")."\">$wikiWikipediaHome</a> | <a class=CBlink href=\"".wikiLink("wikipedia")."\">$wikiAboutWikipedia</a>" ;
		$ret .= "</FORM>" ;

		if ( !$page->isSpecialPage ) {
			$adjusted_time_sc = tsc ( $page->timestamp ) + 3600 * $user->options["hourDiff"];
			$day = date ( "l, F d, Y" , $adjusted_time_sc);
			$time = date ( "H:i" , $adjusted_time_sc ) ;
			$lc = "$day, $time" ;

			$ret .= "<span class=footnote>".str_replace ( '$1' , $lc , $wikiLastChangeCologne ) ;
			$ret .= " <a href=\"".wikiLink("$page->url&amp;diff=yes")."\">$wikiDiff</a> " ;
			$ret .= "; ".str_replace ( '$1' , $page->counter , $wikiRequests ) ;

			# User contributions
			if ( $page->namespace == "user" ) $ret .= "; <a href=\"".wikiLink("special:contributions&amp;theuser=$page->mainTitle")."\">This user's contributions</a>" ;

			# Redirect from...
			if ( $page->backLink != "" ) $ret .= "; $wikiRederectFrom $page->backLink" ;
			}

		$ret .= "</span></td></tr></table>\n" ;

		return $ret ;
		}

	}


?>