<?
# ##############################################################################
# Standard skin
# ##############################################################################

class skinStandard extends skinClass {

	function getHeader ( $page ) {
	        global $wikiMainPageTitle , $wikiArticleSubtitle , $wikiPrintable , $wikiWatch , $wikiMainPage ;
	        global $user , $action , $wikiNoWatch , $wikiLogIn , $wikiLogOut , $wikiSearch ;
	        global $wikiHelp , $wikiHelpLink , $wikiPreferences , $wikiLanguageNames , $wikiWhatLinksHere ;
	        global $wikiCharset , $wikiEncodingCharsets , $wikiEncodingNames , $wikiLogoFile ;
	        global $framed,  $search , $THESCRIPT;

		if ( isset ( $framed ) and $framed != "top" ) return "" ;
		$t = $page->getNiceTitle ( $page->title ) ;
		if ( substr_count ( $t , ":" ) > 0 ) $t = ucfirstIntl ( $t ) ;

		if ( $user->options["skin"] == "Nostalgy" ) {
			$ret = "<a href=\"".wikiLink("")."\"><img border=0 align=right src=\"$wikiLogoFile\" alt=\"[$wikiMainPage]\"></a>\n" ;
			if ( $page->isSpecialPage && $action == "" ) $ret .= "<font size=\"+3\">".$t."</font>" ;
		} else {
			$ret = "<table ".$user->options["quickBarBackground"]. "width=\"100%\" class=\"topbar\" cellspacing=0>\n<tr>" ;
			if ( $user->options["leftImage"] != "" )
			    $ret .= "<td width=\"1%\" rowspan=2 bgcolor=\"#000000\"><img src=\"".$user->options["leftImage"]."\"></td>" ;
			$ret .= "<td valign=top height=1>" ;
			if ( $page->isSpecialPage && $action == "" ) $ret .= "<font size=\"+3\">".$t."</font>" ;
		}
		if ( $action == "" ) {
			$ret .= "<br>\n<br>\n<a href=\"".wikiLink("special:whatlinkshere&amp;target=$page->url")."\">$wikiWhatLinksHere</a>" ;
		} else {
		    $ret .= "<font size=\"+3\"><b><u>" ;
		    if ( $page->secureTitle == $wikiMainPage and $action == "view" ) $ret .= $wikiMainPageTitle.$page->thisVersion ;
		    else $ret .= $page->getNiceTitle($t).$page->thisVersion ;
	#           if ( $page->secureTitle == "Main_Page" and $action == "view" ) $ret .= "<font color=blue>$wikiMainPageTitle</font>$page->thisVersion" ;
	#           else $ret .= "<a href=\"".wikiLink("&amp;search=$page->title")."\">".$page->getNiceTitle($t)."</a>$page->thisVersion" ;
		    $ret .= "</u></b></font>" ;
		    $subText = array () ;
		    if ( $action == "view" and !$page->isSpecialPage ) $ret .=  "<br>$wikiArticleSubtitle\n" ;
		    if ( $user->isLoggedIn && ! $page->isSpecialPage ) {
			if ( $user->doWatch($page->title) )
			    array_push($subText,"<a href=\"".wikiLink("$page->url&amp;action=watch&amp;mode=no")."\">$wikiNoWatch</a>");
			else array_push($subText,"<a href=\"".wikiLink("$page->url&amp;action=watch&amp;mode=yes")."\">$wikiWatch</a>") ;
			}
		    if ( $action == "view" and !$page->isSpecialPage ) array_push ( $subText , "<a href=\"".wikiLink("$page->url&amp;action=print")."\">$wikiPrintable</a>" ) ;
		    if ( $action == "view" and !$page->isSpecialPage ) array_push ( $subText , "<a href=\"".wikiLink("special:whatlinkshere&amp;target=$page->url")."\">$wikiWhatLinksHere</a>" ) ;
		    if ( $page->backLink != "" ) array_push ( $subText , $page->backLink ) ;
		    if ( $page->namespace == "user" and $page->subpageTitle == "" )
			array_push ( $subText , "<a href=\"".wikiLink("special:contributions&amp;theuser=$page->mainTitle")."\">This user's contributions</a>");
		    $ret .= "<br>".implode ( " | " , $subText ) ;
		    if ( count ( $page->otherLanguages ) > 0 ) {
			global $wikiOtherLanguagesText ;
			$subText = array () ;
			$olk = array_keys ( $page->otherLanguages ) ;
			foreach ( $olk as $x )
			    array_push ( $subText , "<a href=\"".$page->otherLanguages[$x]."\">".$wikiLanguageNames[$x]."</a>" ) ;
			$subText = implode ( ", " , $subText ) ;
			$ret .= "<br>".str_replace ( "$1" , $subText , $wikiOtherLanguagesText ) ;
			}
		    }

		if ( $user->options["skin"] == "Nostalgy" ) $ret .= " | <b>".$user->getLink()."</b> | " ;
		else $ret .= "</td>\n<td valign=top width=200 rowspan=2 nowrap>".$user->getLink()."<br>" ;

		if ( $user->isLoggedIn ) $ret .= "<a href=\"".wikiLink("special:userLogout")."\">$wikiLogOut</a> | <a href=\"".wikiLink("special:editUserSettings")."\">$wikiPreferences</a>" ;
		else $ret .= "<a href=\"".wikiLink("special:userLogin")."\">$wikiLogIn</a>" ;
		$ret .= " | <a href=\"".wikiLink($wikiHelpLink)."\">$wikiHelp</a><br>\n" ;

		# Text encoding
		if(count($wikiEncodingNames) > 1) { # Shortcut for switching character encodings
		    #$u = $THESCRIPT . "?" . getenv("QUERY_STRING");
		    $u = getenv ( "REQUEST_URI" ) ;
		    $u = preg_replace("/[\?\&]encoding=[0-9]+/", "", $u);
		    $u .= ((!strchr($u, "?") && strstr($THESCRIPT,$u)) ? "?" : "&amp;");
		    foreach ( $wikiEncodingNames as $i => $enc ) {
			if($i > 0) $ret .= " | ";
			if($i == $user->options["encoding"]) $ret .= "<b>";
			$ret .= "<a href=\"" . $u . "encoding=$i\">$enc</a>";
			if($i == $user->options["encoding"]) $ret .= "</b>";
		    }
		}

		if ( $user->options["skin"] == "Nostalgy" ) {
			$ret .= $page->getLinkBar()."<hr>\n" ;

		} else {
			$ret .= "<FORM method=get action=\"$THESCRIPT\"><INPUT TYPE=text NAME=search SIZE=16 VALUE=\"$search\"><INPUT TYPE=submit value=\"$wikiSearch\"></FORM>" ;
			$ret .= "</td>\n<td rowspan=2 width=1><a href=\"".wikiLink("")."\"><img border=0 src=\"$wikiLogoFile\" alt=\"[$wikiMainPage]\"></a></td></tr>\n" ;
			$ret .= "<tr><td valign=bottom>".$page->getLinkBar()."</td></tr></table>" ;
			}
		return $ret ;
		}

	function getFooter ( $page ) {
		global $wikiSearch , $wikiCategories , $wikiOtherNamespaces , $wikiCounter , $wikiLastChange , $wikiDiff;
		global $wikiGetDate , $framed, $search , $wikiValidate , $user , $THESCRIPT ;
		global $wikiFindMore , $wikiOK , $wikiWikipediaHome , $wikiAboutWikipedia ;
		global $wikiGetDate , $wikiLastChangeCologne , $wikiRequests , $wikiRedirectFrom ;

		if ( isset ( $framed ) ) return "" ;
		$ret = $page->getLinkBar() ;
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

	/*      # Category functionality deactivated
		$cat = $page->getParam ( "CATEGORY" ) ;
		if ( count ( $cat ) > 0 ) {
		    $ret .= $wikiCategories ;
		    $t = new wikiTitle ;
		    $m = "" ;
		    foreach ( $cat as $x ) {
			$t->setTitle ( $x ) ;
			$ret .= "$m<a href=\"".wikiLink($t->url)."\">".$page->getNiceTitle($x)."</a>" ;
			if ( $m == "" ) $m = " | " ;
			}
		    }*/

		if ( $user->options[skin] == "Cologne Blue" ) {
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

	# Old time generator
	/*		$tts = tsc ( $page->timestamp ) + 3600 * $user->options["hourDiff"] ;
			$lc = $wikiGetDate ( tsc ( $tts ) ) ;
			$lc .= ", ".substr ( $tts , 8 , 2 ) ;
			$lc .= ":".substr ( $tts , 10 , 2 ) ;
			$lc = substr ( strstr ( $lc , ", " ) , 2 ) ;*/

			$ret .= "<span class=footnote>".str_replace ( '$1' , $lc , $wikiLastChangeCologne ) ;
			$ret .= " <a href=\"".wikiLink("$page->url&amp;diff=yes")."\">$wikiDiff</a> " ;
			$ret .= "; ".str_replace ( '$1' , $page->counter , $wikiRequests ) ;

			# User contributions
			if ( $page->namespace == "user" ) $ret .= "; <a href=\"".wikiLink("special:contributions&amp;theuser=$page->mainTitle")."\">This user's contributions</a>" ;

			# Redirect from...
			if ( $page->backLink != "" ) $ret .= "; $wikiRederectFrom $page->backLink" ;
			}

			$ret .= "</span></td></tr></table>\n" ;
		} else {
			$ret .= "<FORM method=get action=\"$THESCRIPT\">" ;
			$ret .= "<INPUT TYPE=text NAME=search SIZE=16 VALUE=\"$search\">" ;
			$ret .= "<INPUT TYPE=submit value=\"$wikiSearch\">" ;
			$ret .= " &nbsp; &nbsp; <a href=\"http://validator.w3.org/check/referer\" target=blank>$wikiValidate</a>" ;
			$ret .= "</FORM>" ;
			}

		return $ret ;
		}

	}

?>
