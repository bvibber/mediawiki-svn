<?
# ##############################################################################
# Cologne Blue skin
# ##############################################################################

class skinCologneBlue extends skinClass {

	function getHeader ( &$page ) {
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

	function getQuickBar ( &$page ) {
	        global $wikiMainPage , $wikiRecentChanges , $wikiRecentChangesLink , $wikiUpload , $wikiPopularPages , $wikiLongPages , $action , $wikiHome ;
        	global $user , $oldID , $version , $wikiEditThisPage , $wikiDeleteThisPage , $wikiHistory , $wikiMyWatchlist , $wikiAskSQL , $wikiUser ;
	        global $wikiStatistics , $wikiNewPages , $wikiOrphans , $wikiMostWanted , $wikiAllPages , $wikiRandomPage , $wikiStubs , $wikiListUsers ;
        	global $wikiRecentLinked, $wikiRecentLinkedLink , $wikiBugReports , $wikiBugReportsLink , $wikiGetBriefDate , $wikiGetDate , $wikiDiff ;
		global $wikiMyOptions, $wikiMyself , $wikiLogOut , $wikiMySettings , $wikiShortPages , $wikiLongPages , $wikiUserList , $wikiEditingHistory , $wikiTopics ;
		global $wikiAddToWatchlist , $wikiEditPage , $wikiPrintable , $wikiTalk , $wikiEdit , $wikiPageOptions , $wikiBrowse , $wikiFind , $wikiOK;
		global $wikiEditingHelp , $wikiWikipediaEditingHelp , $wikiShowLastChange , $wikiProtectThisPage , $wikiMainPage , $THESCRIPT , $wikiVoteForPage ;
		global $wikiMoveThisPage ;

		$fonts = "face=verdana,arial" ;
		$bg = "nowrap" ;
		$ret = "" ;

	        $ret .= "<FORM method=get action=\"$THESCRIPT\">" ;
		$ret .= "&nbsp;<span class=menuhead>$wikiFind</span><br>\n" ;
		$ret .= "&nbsp;<INPUT TYPE=text NAME=search SIZE=12 VALUE=\"$search\"><INPUT TYPE=submit value=\"$wikiOK\"></FORM>" ;
		$ret .= "<font $fonts>\n<table border=0 cellspacing=3 cellpadding=2 width='100%'><tr><td $bg>" ;

		$ret .= "<p class=menu>\n" ;
		$ret .= "<span class=menuhead>$wikiBrowse</span><br>\n" ;
		$ret .= "<a class=menulink href=\"".wikiLink("")."\">$wikiMainPage</a><br>\n" ;
		$ret .= "<a class=menulink href=\"".wikiLink("special:RecentChanges")."\">$wikiRecentChanges</a><br>\n" ;
		$ret .= "<a class=menulink href=\"".wikiLink("special:NewPages")."\">$wikiNewPages</a><br>\n" ;
		$ret .= "<a class=menulink href=\"".wikiLink("special:ShortPages")."\">$wikiShortPages</a><br>\n" ;
		$ret .= "<a class=menulink href=\"".wikiLink("special:LongPages")."\">$wikiLongPages</a><br>\n" ;
		$ret .= "<a class=menulink href=\"".wikiLink("special:PopularPages")."\">$wikiPopularPages</a><br>\n" ;
		$ret .= "<a class=menulink href=\"".wikiLink("special:WantedPages")."\">$wikiMostWanted</a><br>\n" ;
		$ret .= "<a class=menulink href=\"".wikiLink("special:RandomPage")."\">$wikiRandomPage</a><br>\n" ;
		$ret .= "<a class=menulink href=\"".wikiLink($wikiBugReportsLink)."\">$wikiBugReports</a><br>\n" ;
#		$ret .= "<a class=menulink href=\"".wikiLink("special:AllPages")."\">$wikiAllPages</a><br>\n" ; # Took out due to request
		$ret .= "<a class=menulink href=\"".wikiLink("special:Statistics")."\">$wikiStatistics</a><br>\n" ;
		$ret .= "<a class=menulink href=\"".wikiLink("special:LonelyPages")."\">$wikiOrphans</a><br>\n" ;
		$ret .= "<a class=menulink href=\"".wikiLink("special:ListUsers")."\">$wikiUserList</a>\n" ;
		$ret .= "</p>" ;


		# Page edit
		$ret .= "</td></tr><tr><td><p class=menu>\n" ;
		$ret .= "<span class=menuhead>$wikiEdit</span><br>\n" ;
		if ( !$page->isSpecialPage ) {
			if ( $page->canEdit() ) $ret .= "<a class=menulink href=\"".wikiLink($page->url."&action=edit")."\"><font size='2'>$wikiEditPage</font></a><br>\n" ;
			if ( $page->canDelete() ) $ret .= "<a class=menulink href=\"".wikiLink("special:deletepage&target=".$page->url)."\">$wikiDeleteThisPage</a><br>\n" ;
			if ( $page->canDelete() ) $ret .= "<a class=menulink href=\"".wikiLink("special:movepage&target=".$page->url)."\">$wikiMoveThisPage</a><br>\n" ;
			if ( $page->canProtect() ) $ret .= "<a class=menulink href=\"".wikiLink("special:protectpage&target=".$page->url)."\">$wikiProtectThisPage</a><br>\n" ;
			if ( $user->isLoggedIn ) $ret .= "<a class=menulink href=\"".wikiLink("special:vote&target=".$page->url)."\">$wikiVoteForPage</a><br>\n" ;
			}
		$ret .= "<a class=menulink href=\"".wikiLink($wikiWikipediaEditingHelp)."\">$wikiEditingHelp</a><br>\n" ;
		$ret .= "<a class=menulink href=\"".wikiLink("special:Upload")."\">$wikiUpload</a></p>\n" ;

		# Page options
		if ( !$page->isSpecialPage ) {
			$ret .= "</td></tr><tr><td><p class=menu>" ;
			$ret .= "<span class=menuhead>$wikiPageOptions</span><br>\n" ;
			$ret .= "<a class=menulink href=\"".wikiLink($page->url."&action=print")."\">$wikiPrintable</a><br>\n" ;
			$ret .= "<a class=menulink href=\"".wikiLink($page->url."&action=watch&mode=yes")."\">$wikiAddToWatchlist</a><br>\n" ;

			$n = $page->namespace ;
			if ( stristr ( $n , "talk" ) == false ) {
				$ret .= "<a class=menulink href=\"".wikiLink(nurlencode($page->getTalkPage()))."\">" . ucfirstIntl ( $wikiTalk ) . "</a>\n" ;
			} else {
				$nn = str_replace ( "Talk" , "" , $page->namespace ) ;
				$nn = str_replace ( "_" , " " , $nn ) ;
				$nn = trim ( str_replace ( "talk" , "" , $nn ) ) ;
				$ret .= "<a class=menulink href=\"".wikiLink(nurlencode($nn.":".$page->mainTitle))."\">" . "Topic" . "</a>\n" ;
				}
			$ret .= "</p>\n" ;
			}

		# Page Info
		if ( !$page->isSpecialPage ) {
			global $wikiPageInfo , $wikiWhatLinksHere , $wikiLinkedPages , $wikiEditingHistory , $wikiLastChangeCologne , $wikiShowDiff , $wikiRequests ;
			$ret .= "</td></tr><tr><td><p class=menu>" ;
			$ret .= "<span class=menuhead>$wikiPageInfo</span><br>\n" ;
			$ret .= "<a class=menulink href=\"".wikiLink("special:WhatLinksHere&target=".$page->secureTitle)."\">$wikiWhatLinksHere</a><br>\n" ;
			$ret .= "<a class=menulink href=\"".wikiLink("special:RecentChangesLinked&target=".$page->secureTitle)."\">$wikiLinkedPages</a><br>\n" ;
			$ret .= "<a class=menulink href=\"".wikiLink($page->url."&action=history")."\">$wikiEditingHistory</a><br>\n" ;
			$ret .= "<a class=menulink href=\"".wikiLink($page->url."&diff=yes")."\">$wikiShowLastChange</a></p>\n" ;
			}

		# My options
	        if ( $user->isLoggedIn ) {
			$ret .= "</td></tr><tr><td><p class=menu>" ;
			$ret .= "<span class=menuhead>$wikiMyOptions</span><br>\n" ;
			$ret .= "<a class=menulink href=\"".wikiLink(nurlencode("$wikiUser:$user->name"))."\">$wikiMyself</a><br>\n" ;
			$ret .= "<a class=menulink href=\"".wikiLink("special:watchlist")."\">$wikiMyWatchlist</a><br>\n" ;
			$ret .= "<a class=menulink href=\"".wikiLink("special:editUserSettings")."\">$wikiMySettings</a><br>\n" ;
			$ret .= "<a class=menulink href=\"".wikiLink("special:userLogout")."\">$wikiLogOut</a></p>\n" ;
		} else {
			$ret .= "</td></tr><tr><td><p class=menu>" ;
			$ret .= "<a class=menulink href=\"".wikiLink("special:userLogin")."\">$wikiLogIn</a></p>\n" ;
			}

		$ret .= "</td></tr></table></font>" ;
		return $ret ;
		}

	function getMiddle ( &$page , $text ) {
	        global $user , $action ;
		if ( $action != "print" )
			$text = "<h1>".$page->getNiceTitle($page->title)."</h1>\n".$text ;

		$ret = "\n<div class=\"bodytext\">$text</div>" ;
	        if ( $action == "print" ) return $ret ;
        	$oaction = $action ;
	        if ( $action == "edit" ) $action = "" ;

	        $column = $this->getQuickBar ( $page ) ;

		$cw = 130 ;
	        $column = "<td class=\"quickbar\" ".$user->options["quickBarBackground"]." width=$cw valign=top nowrap>".$column."</td>" ;
        	$ret = "<td valign=top>\n".$ret."\n</td>" ;

	        $table = "<table width=\"100%\" class=\"middle\" cellpadding=2 cellspacing=0><tr>" ;
        	$qb = $user->options["quickBar"] ;
		if ( $qb != "left" ) $qb = "right" ;

		$tableend = "</table>" ;
		if ( $action != "print" ) $tableend = "" ;
               	if ( $qb == "left" ) $ret = $table.$column.$ret."</tr>$tableend" ;
                else if ( $qb == "right" ) $ret = $table.$ret.$column."</tr>$tableend" ;
        	$action = $oaction ;
	        return $ret ;
		}

	function getFooter ( &$page ) {
		global $wikiSearch , $wikiCategories , $wikiOtherNamespaces , $wikiCounter , $wikiLastChange , $wikiDiff;
		global $wikiGetDate , $framed, $search , $wikiValidate , $user , $THESCRIPT ;
		global $wikiFindMore , $wikiOK , $wikiWikipediaHome , $wikiAboutWikipedia ;
		global $wikiGetDate , $wikiLastChangeCologne , $wikiRequests , $wikiRedirectFrom ;

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


	function getStylesheet ( &$page ) {
		global $styleSheet , $action , $namespaceBackground , $user ;

		$cb = "Cologne Blue" ;
		$skin = $cb ;
	
		$ret = "<style type=\"text/css\"><!--\n";
		$ret .= "body { ";
		$ret .= "	background-color: #FFFFFF;\n" ;
		$ret .= "	margin-top: 0px;\n" ;
		$ret .= "	margin-left: 0px;\n" ;
		$ret .= "	margin-right: 0px;\n" ;
		$textcolor = $user->options[text];
		$bgcolor = $user->options[background];
		$namespaceBackground = "" ;
		if ( $namespaceBackground != "" ) $bgcolor = $namespaceBackground ;
		if ( $textcolor == "" )
		    $textcolor = "black"; # For un-coloring links. Should be "inherit" but Netscape 4.x messes it up
		else
		    $ret .= "color: $textcolor; ";
		if ( $bgcolor == "" )
		    $bgcolor = "white";
		else
		    $ret .= "background: $bgcolor; ";
		$ret .= "margin: 0; " ;
		$ret .= "}\n";

			$ret .= "
			.header {
				background-color: #7089AA;
			}

			.footnote {
				font-size: 8pt;
				color: #666666;
			}

			p.topmenu {
				margin-bottom: 4px;
				text-transform: uppercase;
				color: #FFFFFF;
				font-family: Verdana, sans-serif;
				font-size: 8pt;
			}

			a.topmenu {
				color: #FFFFFF;
				text-decoration: none;
				font-size: 10pt;
			}

			.tagline {
				color: #000000;
				text-transform: uppercase;
				font-family: Verdana, sans-serif;
				font-size: 11px;
			}

			p, form, dl {
				font-family: Verdana, sans-serif;
				font-size: 10pt;
			}

			h1 {
				font-family: Arial, Helvetica, sans-serif;
				color: #858585;
				font-size: 18pt;
				font-weight: bold;
			}
			span.spacer {
				font-family: sans-serif;
				font-size: 20px;
			}

			input {
				font-family: Verdana, sans-serif;
				font-size: 8pt;
			}

			p.menu {
				font-family: Verdana, sans-serif;
				font-size: 8pt;
				line-height: 13pt;
			}

			span.menuhead {
				font-family: Verdana, sans-serif;
				font-size: 8pt;
				font-weight: bold;
				color: #999999;
			}

			a.menulink {
				font-weight: bold;
				text-decoration: none;
				color: #4B6587;
			}

			a:hover {
				text-decoration: underline;
			}

			.bodytext {
				font-family: Verdana, sans-serif;
				font-size: 10pt;
				text-align: justify;
				margin: 0 1em;
			}
			a, a.external {
				color: #4B6587;
			}

			a.external {
				text-decoration: none;
			}
			a.interwiki { color: #3333BB; text-decoration: none; }
			" ;

		$ret .= "a { text-decoration: " . (($user->options[underlineLinks] == "no") ? "none" : "underline") . "; }\n";

		$qbside = ( $user->options["quickBar"] == "left" ) ? "right" : "left";
		$qbside = "nope" ; # nope is a dummy, will be ignored
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
		    }
		$ret .= "//--></style>";

		$styleSheet = $ret ;

		}

	}

?>