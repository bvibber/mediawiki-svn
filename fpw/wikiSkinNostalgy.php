<?
# ##############################################################################
# Nostalgy skin
# ##############################################################################

include_once ( "wikiSkinStandard.php" ) ;

class skinNostalgy extends skinClass {

	function getHeader ( $page ) {
	        global $wikiMainPageTitle , $wikiArticleSubtitle , $wikiPrintable , $wikiWatch , $wikiMainPage ;
	        global $user , $action , $wikiNoWatch , $wikiLogIn , $wikiLogOut , $wikiSearch ;
	        global $wikiHelp , $wikiHelpLink , $wikiPreferences , $wikiLanguageNames , $wikiWhatLinksHere ;
	        global $wikiCharset , $wikiEncodingCharsets , $wikiEncodingNames , $wikiLogoFile ;
	        global $framed,  $search , $THESCRIPT;

		if ( isset ( $framed ) and $framed != "top" ) return "" ;
		$t = $page->getNiceTitle ( $page->title ) ;
		if ( substr_count ( $t , ":" ) > 0 ) $t = ucfirstIntl ( $t ) ;

		$ret = "<a href=\"".wikiLink("")."\"><img border=0 align=right src=\"$wikiLogoFile\" alt=\"[$wikiMainPage]\"></a>\n" ;
		if ( $page->isSpecialPage && $action == "" ) $ret .= "<font size=\"+3\">".$t."</font>" ;

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

		$ret .= " | <b>".$user->getLink()."</b> | " ;

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

		$ret .= $page->getLinkBar()."<hr>\n" ;

		return $ret ;
		}

	function getQuickBar ( $page ) {
		}

	function getMiddle ( $page ) {
		}

	function getFooter ( $page ) {
		}

	}
?>