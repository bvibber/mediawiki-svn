<?
# ##############################################################################
# Standard skin
# ##############################################################################

class skinStandard extends skinClass {

	function getHeader ( &$page ) {
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

	function getQuickBar ( &$page ) {
		global $wikiMainPage , $wikiRecentChanges , $wikiRecentChangesLink , $wikiUpload , $wikiPopularPages , $wikiLongPages , $action ;
		global $user , $oldID , $version , $wikiEditThisPage , $wikiDeleteThisPage , $wikiHistory , $wikiMyWatchlist , $wikiAskSQL ;
		global $wikiStatistics , $wikiNewPages , $wikiOrphans , $wikiMostWanted , $wikiAllPages , $wikiRandomPage , $wikiStubs , $wikiListUsers ;
		global $wikiRecentLinked, $wikiRecentLinkedLink , $wikiBugReports , $wikiBugReportsLink , $wikiGetBriefDate , $wikiProtectThisPage ;
		global $wikiVoteForPage , $wikiMoveThisPage;

		$editOldVersion = "" ;
		if ( $oldID != "" ) $editOldVersion="&amp;oldID=$oldID&amp;version=$version" ;
		$column = "" ;
		$column .= "<a href=\"".wikiLink("")."\">$wikiMainPage</a>\n" ;
		$column .= "<br><a href=\"".wikiLink("special:$wikiRecentChangesLink")."\">$wikiRecentChanges</a>\n" ;
		if ( !$page->isSpecialPage )
		    $column .= "<br><a href=\"".wikiLink("special:$wikiRecentLinkedLink&amp;target=".$page->url)."\">$wikiRecentLinked</a>\n" ;
		if ( $page->canEdit() )
		    $column .= "<br><a href=\"".wikiLink($page->url."$editOldVersion&amp;action=edit")."\">$wikiEditThisPage</a>\n" ;
		else if ( !$page->isSpecialPage ) $column .= "<br>Protected page\n" ;

		$temp = $page->isSpecialPage ;
		if ( $action == "" ) $page->isSpecialPage = false ;
		if ( $page->canDelete() ) $column .= "<br><a href=\"".wikiLink("special:deletepage&amp;target=".$page->url)."\">$wikiDeleteThisPage</a>\n" ;
		if ( $page->canDelete() ) $column .= "<br><a href=\"".wikiLink("special:movepage&amp;target=".$page->url)."\">$wikiMoveThisPage</a>\n" ;
		$page->isSpecialPage = $temp ;

		if ( $page->canProtect() ) $column .= "<br><a href=\"".wikiLink("special:protectpage&amp;target=".$page->url)."\">$wikiProtectThisPage</a>\n" ;
	# To be implemented later
	#       if ( $page->canAdvance() ) $column .= "<br><a href=\"".wikiLink("special:Advance&amp;topic=$page->safeTitle")."\">Advance</a>\n" ;
		if ( $user->isLoggedIn ) $column .= "<br><a href=\"".wikiLink("special:vote&target=".$page->url)."\">$wikiVoteForPage</a>\n" ;

		if ( in_array ( "is_sysop" , $user->rights ) ) $column .= "<br><a href=\"".wikiLink("special:AskSQL")."\">$wikiAskSQL</a>\n" ;
		if ( !$page->isSpecialPage ) $column .= "<br><a href=\"".wikiLink($page->url."&amp;action=history")."\">$wikiHistory</a>\n" ;
		$column .= "<br><a href=\"".wikiLink("special:Upload")."\">$wikiUpload</a>\n" ;
		$column .= "<hr>" ;
		$column .= "<a href=\"".wikiLink("special:Statistics")."\">$wikiStatistics</a>" ;
		$column .= "<br>\n<a href=\"".wikiLink("special:NewPages")."\">$wikiNewPages</a>" ;
		$column .= "<br>\n<a href=\"".wikiLink("special:LonelyPages")."\">$wikiOrphans</a>" ;
		$column .= "<br>\n<a href=\"".wikiLink("special:WantedPages")."\">$wikiMostWanted</a>" ;
		$column .= "<br>\n<a href=\"".wikiLink("special:PopularPages")."\">$wikiPopularPages</a>" ;
		$column .= "<br>\n<a href=\"".wikiLink("special:AllPages")."\">$wikiAllPages</a>" ;
		$column .= "<br>\n<a href=\"".wikiLink("special:RandomPage")."\">$wikiRandomPage</a>" ;
		$column .= "<br>\n<a href=\"".wikiLink("special:ShortPages")."\">$wikiStubs</a>" ;
		$column .= "<br>\n<a href=\"".wikiLink("special:LongPages")."\">$wikiLongPages</a>" ;
		$column .= "<br>\n<a href=\"".wikiLink("special:ListUsers")."\">$wikiListUsers</a>" ;
		if ( $user->isLoggedIn ) {
		    $column .= "<br>\n<a href=\"".wikiLink("special:WatchList")."\">$wikiMyWatchlist</a>" ;
		    }
		$column .= "<br>\n<a href=\"".wikiLink($wikiBugReportsLink)."\">$wikiBugReports</a>" ;
		$column .= "<br>\n<a href=\"".$wikiGetBriefDate()."\">".$wikiGetBriefDate()."</a>" ;
		$a = $page->getOtherNamespaces () ;
		if ( count ( $a ) > 0 ) $column .= "<hr>".implode ( "<br>\n" , $a ) ;

/*
		# Category functionality deactivated
		$cat = $page->getParam ( "CATEGORY" ) ;
		if ( count ( $cat ) > 0 ) {
		    $column .= "<hr>" ;
		    $t = new wikiTitle ;
		    foreach ( $cat as $x ) {
			$t->setTitle ( $x ) ;
			$column .= "<a href=\"".wikiLink($t->url")."\">".$page->getNiceTitle($x)."</a><br>\n" ;
			}
		    }
*/

		return $column ;
		}

	function getMiddle ( &$page , $text ) {
		global $user , $action ;

		$ret = "\n<div class=\"bodytext\">$text</div>" ;
		if ( $action == "print" ) return $ret ;
		$oaction = $action ;
		if ( $action == "edit" ) $action = "" ;
		if ( $user->options["quickBar"] == "right" or $user->options["quickBar"] == "left" or $user->options["forceQuickBar"] != "" ) {
		    $column = $page->getQuickBar();
		    $cw = 110 ;
		    $column = "<td class=\"quickbar\" ".$user->options["quickBarBackground"]." width=$cw valign=top nowrap>".$column."</td>" ;
		    $ret = "<td valign=top>\n".$ret."\n</td>" ;

		    $table = "<table width=\"100%\" class=\"middle\" cellpadding=2 cellspacing=0><tr>" ;
		    $qb = $user->options["quickBar"] ;
		    if ( $user->options["forceQuickBar"] != "" ) {
			if ( $user->options["forceQuickBar"] == "anywhere" ) {
				if ( $qb != "left" ) $qb = "right" ; # Forcing right quickbar if unspecified
				}
			else $qb = $user->options["forceQuickBar"] ;
			}

		    global $framed ;
		    if ( isset ( $framed ) ) {
			if ( $framed == "bar" ) $ret = $column ;
			else if ( $framed == "main" ) $ret = $ret ;
			else $ret = "" ;
		    } else {
			$tableend = "</table>" ;
			if ( $qb == "left" ) $ret = $table.$column.$ret."</tr>$tableend" ;
			else if ( $qb == "right" ) $ret = $table.$ret.$column."</tr>$tableend" ;
			}
		    }
		$action = $oaction ;
		return $ret ;
		}

	function getFooter ( &$page ) {
		global $wikiSearch , $wikiCategories , $wikiOtherNamespaces , $wikiCounter , $wikiLastChange , $wikiDiff;
		global $wikiGetDate , $framed, $search , $wikiValidate , $user , $THESCRIPT ;
		global $wikiFindMore , $wikiOK , $wikiWikipediaHome , $wikiAboutWikipedia ;
		global $wikiGetDate , $wikiRequests , $wikiRedirectFrom ;

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

		$ret .= "<FORM method=get action=\"$THESCRIPT\">" ;
		$ret .= "<INPUT TYPE=text NAME=search SIZE=16 VALUE=\"$search\">" ;
		$ret .= "<INPUT TYPE=submit value=\"$wikiSearch\">" ;
		$ret .= " &nbsp; &nbsp; <a href=\"http://validator.w3.org/check/referer\" target=blank>$wikiValidate</a>" ;
		$ret .= "</FORM>" ;

		return $ret ;
		}

	function getStylesheet ( &$page ) {
		global $stylesheet , $user , $namespaceBackground ;
		$skin = $user->options[skin] ;
		$ret = "<style type=\"text/css\"><!--\n";
		$ret .= "body { ";
		$textcolor = $user->options[text];
		$bgcolor = $user->options[background];
		if ( $namespaceBackground != "" ) $bgcolor = $namespaceBackground ;
		if ( $textcolor == "" ) $textcolor = "black"; # For un-coloring links. Should be "inherit" but Netscape 4.x messes it up
		else $ret .= "color: $textcolor; ";
		if ( $bgcolor == "" ) $bgcolor = "white";
		else $ret .= "background: $bgcolor; ";
		$ret .= "}\n";

		$ret .= "a { text-decoration: " . (($user->options[underlineLinks] == "no") ? "none" : "underline") . "; }\n";

		$qbside = ( $user->options["quickBar"] == "left" ) ? "right" : "left";

		$ret .= "a.interwiki, a.external { color: #3333BB; text-decoration: none; }\n" .
		    "a.red { color: red; text-decoration: none; }\n" .
		    "a.green { color: blue; text-decoration: none; }\n" .
		    "a.syslink { color:white; text-decoration:none; }\n" .
		    "a.CBlink { color:#4B6587; text-decoration:none; font-size:11pt; }\n" . # Was:#0000AA
		    ".topbar { border-bottom-width: 2; border-bottom-style: ridge; }\n" .
		    ".middle { background:white }\n" .
		    ".quickbar { background:$bgcolor; border-$qbside-width: 2; border-$qbside-style: ridge; }\n" .
		    ".footer { border-top-color: black; border-top-width: 2; border-top-style: groove; }\n";

		if ( $action == "print" ) {
		    $ret .= "a { color: inherit; text-decoration: none; font-style: italic; }\n ";
		    $ret .= "a.newlink { color: inherit; font-style: inherit; }\n.newlinkedge { display: none; }\n";
		} elseif ( $user->options[markupNewTopics] == "red") {
		    $ret .= "a.newlink { color: red; }\n.newlinkedge { display: none; }\n";
		} elseif ( $user->options[markupNewTopics] == "inverse") {
		    $ret .= "a.newlink { color: white; background: blue; }\n.newlinkedge { display: inline; }\n";
		} else {
		    $ret .= "a.newlink { color: $textcolor; text-decoration: none; }\n.newlinkedge { display: inline; }\n";
		    }
		$ret .= "//--></style>";

		$stylesheet = $ret ;
		}

	}

?>
