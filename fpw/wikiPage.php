<?
class WikiPage extends WikiTitle {
	var $contents ;
	
	#Functions
	function load ( $t , $doRedirect = true ) {
		global $action ;
		$this->title = $t ;
		$this->makeSecureTitle () ;
		$this->isSpecialPage = false ;
		$this->revision = "current" ;
		if ( $this->namespace == "special" ) {
			$allowed = array () ; # I know this is crude, but...
			array_push ( $allowed , "userlogin" ) ;
			array_push ( $allowed , "userlogout" ) ;
			array_push ( $allowed , "recentchanges" ) ;
			array_push ( $allowed , "upload" ) ;
			array_push ( $allowed , "statistics" ) ;
			array_push ( $allowed , "lonelypages" ) ;
			array_push ( $allowed , "wantedpages" ) ;
			array_push ( $allowed , "allpages" ) ;
			array_push ( $allowed , "randompage" ) ;
			array_push ( $allowed , "shortpages" ) ;
			array_push ( $allowed , "listusers" ) ;
			array_push ( $allowed , "watchlist" ) ;
			array_push ( $allowed , "special_pages" ) ;
			array_push ( $allowed , "editusersettings" ) ;
			$call = $this->mainTitle ;
			if ( !in_array ( strtolower ( $call ) , $allowed ) ) {
				$this->isSpecialPage = true ;
				$this->contents = "<h1>No such special page \"$call\"!</h1>" ;
				return ;
				}
			$this->title = $call ;
			$this->contents = $call () ;
			$this->isSpecialPage = true ;
			return ;
			}
		$connection = getDBconnection () ;
		mysql_select_db ( "wikipedia" , $connection ) ;
		$thisVersion = "" ;
		global $oldID , $version ;
		if ( isset ( $oldID ) ) {
			$sql = "SELECT * FROM old WHERE old_id=$oldID" ;
			$result = mysql_query ( $sql , $connection ) ;
			if ( $s = mysql_fetch_object ( $result ) ) {
				$this->title=$s->old_title ;
				$this->makeSecureTitle () ;
				$this->contents = $s->old_text ;
				$this->thisVersion = "<br><font size=-1>This is the old version #$version; see the <a href=\"$PHP_SELF?title=$this->secureTitle\">current version</a></font>" ;
				}
			else $this->contents = "Describe the new page here." ;
		} else {
			$sql = "SELECT * FROM cur WHERE cur_title=\"".$this->secureTitle."\"" ;
			$result = mysql_query ( $sql , $connection ) ;
			if ( $s = mysql_fetch_object ( $result ) ) {
				$this->title=$s->cur_title ;
				$this->makeSecureTitle () ;
				$this->contents = $s->cur_text ;
				}
			else $this->contents = "Describe the new page here." ;
			}
		mysql_free_result ( $result ) ;
		mysql_close ( $connection ) ;
		$this->makeURL () ;
		$this->splitTitle () ;
		if ( strtolower ( substr ( $this->contents , 0 , 9 ) ) == "#redirect" and $doRedirect and $action != "edit" ) {
			$z = $this->contents ;
			$z = strstr ( $z , "[[" ) ;
			$z = str_replace ( "[[" , "" , $z ) ;
			$z = str_replace ( "]]" , "" , $z ) ;
			$this->load ( trim($z) , false ) ;
			}
		}
	function special ( $t ) {
		$this->title = $t ;
		$this->isSpecialPage = true ;
		}
	function getSubpageList () {
		$a = array () ;
		$t = ucfirst ( $this->namespace ) ;
		if ( $t != "" ) $t .= ":" ;
		$t .= $this->mainTitle ;
		$mother = $t ;
		$t .= "/" ;
		$connection = getDBconnection () ;
		mysql_select_db ( "wikipedia" , $connection ) ;
		$sql = "SELECT cur_title FROM cur WHERE cur_title LIKE \"$t%\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		$u = new WikiTitle ;
		while ( $s = mysql_fetch_object ( $result ) ) {
			$t = strstr ( $s->cur_title , "/" ) ;
			$t = $u->getNiceTitle ( $t ) ;
			$t = "[[$t]]" ;
			array_push ( $a , $t ) ;
			}
		if ( $result != "" ) mysql_free_result ( $result ) ;
		mysql_close ( $connection ) ;
		if ( count ( $a ) > 0 ) array_unshift ( $a , "[[$mother]]" ) ;
		return $a ;
		}
	function getOtherNamespaces () {
		$a = array () ;
		if ( $this->isSpecialPage ) return $a ;
		$n = explode ( ":" , $this->secureTitle ) ;
		if ( count ( $n ) == 1 ) $n = $n[0] ;
		else $n = $n[1] ;
		$connection = getDBconnection () ;
		mysql_select_db ( "wikipedia" , $connection ) ;
		$sql = "SELECT cur_title FROM cur WHERE cur_title LIKE \"%:$n\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		$u = new WikiTitle ;
		if ( $this->namespace != "" ) {
			$dummy = new wikiTitle ;
			$dummy->setTitle ( $n ) ;
			if ( $dummy->doesTopicExist ( $connection ) )
				array_push ( $a , "<a style=\"color:green;text-decoration:none\" href=\"$PHP_SELF?title=$n\">:".$this->getNiceTitle($n)."</a>" ) ;
			}
		if ( $this->namespace != "talk" ) array_push ( $a , "<a style=\"color:green;text-decoration:none\" href=\"$PHP_SELF?title=talk:$n\">Talk</a>" ) ;
		while ( $s = mysql_fetch_object ( $result ) ) {
			$t = explode ( ":" , $s->cur_title ) ;
			$t = $u->getNiceTitle ( $t[0] ) ;
			if ( strtolower ( $t ) != "talk" and strtolower ( $t ) != $this->namespace )
				array_push ( $a , "<a style=\"color:green;text-decoration:none\" href=\"$PHP_SELF?title=$t:$n\">$t</a>" ) ;
			}
		if ( $result != "" ) mysql_free_result ( $result ) ;
		mysql_close ( $connection ) ;
		return $a ;
		}
	function ensureExistence () {
		$this->makeSecureTitle () ;
		if ( $this->doesTopicExist() ) return ;
		$connection = getDBconnection () ;
		mysql_select_db ( "wikipedia" , $connection ) ;
		$sql = "INSERT INTO cur (cur_title) VALUES (\"$this->secureTitle\")" ;
		$result = mysql_query ( $sql , $connection ) ;
		mysql_close ( $connection ) ;		
		}
	function backup () {
		$id = getMySQL ( "cur" , "cur_id" , "cur_title=\"$this->secureTitle\"" ) ;
		$oid = getMySQL ( "cur" , "cur_old_version" , "cur_id=$id" ) ;

		$connection = getDBconnection () ;
		mysql_select_db ( "wikipedia" , $connection ) ;
		$sql = "SELECT * FROM cur WHERE cur_id=$id" ;
		$result = mysql_query ( $sql , $connection ) ;
		$s = mysql_fetch_object ( $result ) ;
		mysql_free_result ( $result ) ;

		$s->cur_text = str_replace ( "\"" , "\\\"" , $s->cur_text ) ;
		$sql = "INSERT INTO old (old_title,old_old_version,old_text,old_comment,old_user,old_user_text,old_minor_edit)";
		$sql .= " VALUES (\"$this->secureTitle\",\"$oid\",\"".$s->cur_text."\",\"".$s->cur_comment."\",\"$s->cur_user\",\"$s->cur_user_text\",$s->cur_minor_edit)" ;
		mysql_query ( $sql , $connection ) ;

		$sql = "SELECT old_id FROM old WHERE old_old_version=\"$oid\" AND old_title=\"$this->secureTitle\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		$s = mysql_fetch_object ( $result ) ;
		mysql_free_result ( $result ) ;

		$oid = $s->old_id ;
		setMySQL ( "cur" , "cur_old_version" , $oid , "cur_id=$id" ) ;

		mysql_close ( $connection ) ;
		}
	function setEntry ( $text , $comment , $userID , $userName , $minorEdit ) {
		$cond = "cur_title=\"$this->secureTitle\"" ;
		$connection = getDBconnection () ;
		mysql_select_db ( "wikipedia" , $connection ) ;
		$text = str_replace ( "\"" , "\\\"" , $text ) ;
		$sql = "UPDATE cur SET cur_text=\"$text\",cur_comment=\"$comment\",cur_user=\"$userID\"," ;
		$sql .= "cur_user_text=\"$userName\",cur_minor_edit=\"$minorEdit\" WHERE $cond" ;
		mysql_query ( $sql , $connection ) ;
		mysql_close ( $connection ) ;		
		}

	# Output functions
	function replaceInternalLinks ( $s ) {
		global $user , $unlinkedLinks , $linkedLinks ;
		$a = explode ( "[[" , " ".$s ) ;
		$s = array_shift ( $a ) ;
		$s = substr ( $s , 1 ) ;
		$connection = getDBconnection () ;
		foreach ( $a as $t ) {
			$b = explode ( "]]" , $t , 2 ) ;
			if ( count($b) < 2 ) $s .= "Illegal link : ?$b[0]?" ;
			else {
				$c = explode ( "|" , $b[0] , 2 ) ;
				$link = $c[0] ;

				$topic = new WikiTitle ;
				$topic->setTitle ( $link ) ;
				$link = $this->getLinkTo ( $topic ) ;
				$topic->setTitle ( $link ) ;

				if ( count ( $c ) == 1 ) array_push ( $c , $topic->getMainTitle() ) ;
				$text = $c[1] ;


				if ( $topic->doesTopicExist( $connection ) ) {
					$linkedLinks[$topic->secureTitle]++ ;
					if ( $user->options["showHover"] == "yes" ) $hover = "title=\"$link\"" ;
					$s .= "<a href=\"$PHP_SELF?title=".urlencode($link)."\" $hover>$text</a>" ;
				} else {
					$unlinkedLinks[$link]++ ;
					$text2 = $text ;
					$style="" ;
					if ( $user->options["showHover"] == "yes" ) $hover = "title=\"Edit '$link'\"" ;
					if ( substr_count ( $text2 , " " ) > 0 ) $text2 = "[$text2]" ;
					if ( $user->options["underlineLinks"] == "no" ) { $text = $text2 ; $style = ";text-decoration:none" ; }
					if ( $user->options["markupNewTopics"] == "red" )
						$s .= "<a style=\"color:red$style\" href=\"$PHP_SELF?action=edit&title=".urlencode($link)."\" $hover>$text</a>" ;
					else if ( $user->options["markupNewTopics"] == "inverse" )
						$s .= "<a style=\"color:white;background:blue$style\" href=\"$PHP_SELF?action=edit&title=".urlencode($link)."\" $hover>$text</a>" ;
					else $s .= "$text2<a href=\"$PHP_SELF?action=edit&title=".urlencode($link)."\" $hover>?</a>" ;
					}
				$s .= $b[1] ;
				}
			}
		mysql_close ( $connection ) ;
		return $s ;
		}
	function parseImages ( $s ) {
		$s = ereg_replace ( "http://([a-zA-Z0-9_/:.]*)\.(png|jpg|jpeg|tif|tiff|gif)" , "<img src=\"http://\\1.\\2\">" , $s ) ;
		return $s ;
		}
	function replaceExternalLinks ( $s ) {
		global $user ;
		$cnt = 1 ;
		$a = explode ( "[http://" , " ".$s ) ;
		$s = array_shift ( $a ) ;
		$s = substr ( $s , 1 ) ;
		foreach ( $a as $t ) {
			$b = spliti ( "]" , $t , 2 ) ;
			if ( count($b) < 2 ) $s .= "Illegal link : ?$b[0]?" ;
			else {
				$c = explode ( " " , $b[0] , 2 ) ;
				if ( count ( $c ) == 1 ) array_push ( $c , "" ) ;
				$link = $c[0] ;
				$text = trim ( $c[1] ) ;
				if ( $text == "" ) $text = "[".$cnt++."]" ;
				else {
					if ( substr_count ( $text , " " ) > 0 and $user->options["underlineLinks"] == "no" )
						$text = "[$text]" ;
					}
				if ( substr_count ( $b[1] , "<hr>" ) > 0 ) $cnt = 1 ;
				$link = "~http://".$link ;
				if ( $user->options["showHover"] == "yes" ) $hover = "title=\"$link\"" ;
				$s .= "<a href=\"$link\" $hover>$text</a>" ;
				$s .= $b[1] ;
				}
			}

		$o = "A-Za-z0-9/\.:?&=~%-@^" ;
		$s = eregi_replace ( "([^~])http://([$o]+)([^$o])" , "\\1<a href=\"http://\\2\">http://\\2</a>\\3" , $s ) ;
		$s = str_replace ( "~http://" , "http://" , $s ) ;

		return $s ;
		}
	function replaceVariables ( $s ) {
		$var=date("m"); $s = str_replace ( "{{{CURRENTMONTH}}}" , $var , $s ) ;
		$var=date("F"); $s = str_replace ( "{{{CURRENTMONTHNAME}}}" , $var , $s ) ;
		$var=date("j"); $s = str_replace ( "{{{CURRENTDAY}}}" , $var , $s ) ;
		$var=date("l"); $s = str_replace ( "{{{CURRENTDAYNAME}}}" , $var , $s ) ;
		$var=date("Y"); $s = str_replace ( "{{{CURRENTYEAR}}}" , $var , $s ) ;
		if ( strstr ( $s , "{{{NUMBEROFARTICLES}}}" ) ) {
			$connection=getDBconnection() ;
			mysql_select_db ( "wikipedia" , $connection ) ;
			$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title NOT LIKE \"%/Talk\" AND cur_title NOT LIKE \"%ikipedia%\" AND cur_text LIKE \"%,%\"" ;
			$result = mysql_query ( $sql , $connection ) ;
			$var = mysql_fetch_object ( $result ) ;
			$var = $var->number ;
			mysql_free_result ( $result ) ;
			$s = str_replace ( "{{{NUMBEROFARTICLES}}}" , $var , $s ) ;
			}
		return $s ;
		}
	function replaceAll ( $f , $r , &$s ) {
		$t = "" ;
		while ( $s != $t ) {
			$t = $s ;
			$s = str_replace ( $f , $r , $s ) ;
			}
		return $s ;
		}
	function pingPongReplace ( $f , $r1 , $r2 , $s ) {
		$a = explode ( $f , " ".$s ) ;
		$s = substr ( array_shift ( $a ) , 1 ) ;
		$r = $r1 ;
		foreach ( $a as $t ) {
			$s .= $r.$t ;
			if ( $r == $r1 ) $r = $r2 ;
			else $r = $r1 ;
			}
		return $s ;
		}
	function parseContents ( $s ) {
		global $linkedLinks , $unlinkedLinks ;
		$linkedLinks = array () ;
		$unlinkedLinks = array () ;
		$s .= "\n" ;
		$a = spliti ( "<nowiki>" , $s ) ;
		$s = $this->subParseContents ( array_shift ( $a ) ) ;
		foreach ( $a as $x ) {
			$b = spliti ( "</nowiki>" , $x , 2 ) ;
			$s .= $b[0] ;
			if ( count ( $b ) == 2 ) {
				$sub = $this->subParseContents ( $b[1] ) ;
				$sub = substr ( strstr ( $sub , ">" ) , 1 ) ;
				$s .= $sub ;
				}
			}
		return $s ;
		}
	function removeHTMLtags ( $s ) {
		$s = eregi_replace ( "<a (.*)>" , "&lt;a \\1&gt;" , $s ) ;
		$s = eregi_replace ( "</a(.*)>" , "&lt;/a\\1&gt;" , $s ) ;
		$s = eregi_replace ( "<script(.*)>" , "&lt;script\\1&gt;" , $s ) ;
		$s = eregi_replace ( "</script(.*)>" , "&lt;/script\\1&gt;" , $s ) ;
		return $s ;
		}
	function subParseContents ( $s ) {
		global $user ;
# Removed autoLink for mixedThings; wasn't working, anyway...
#		$s = ereg_replace ( "([\.|\n| )([a-z0-9]*[A-Z0-9]+[A-Za-z0-9]*)( |\n|\.)" , "\\1[[\\2]]\\3" , $s ) ;
		$s = $this->removeHTMLtags ( $s ) ;
		$s = ereg_replace ( "-----*" , "<hr>" , $s ) ;
		$s = str_replace ( "<HR>" , "<hr>" , $s ) ;
		$s = $this->replaceVariables ( $s ) ;
		$s = $this->pingPongReplace ( "'''" , "<b>" , "</b>" , $s ) ;
		$s = $this->pingPongReplace ( "''" , "<i>" , "</i>" , $s ) ;

		$s = ereg_replace ( "([\n| ])/([a-zA-Z0-9_]*)" , "\\1[[/\\2|/\\2]]" , $s ) ;

		$justify = "" ;
		if ( $user->options["justify"] == "yes" ) $justify = " align=justify" ;
		$a = explode ( "\n" , $s ) ;
		$s = "<p$justify>" ;
		$obegin = "" ;
		foreach ( $a as $t ) {
			$pre = "" ;
			$post = "" ;
			$ppre = "" ;
			$ppost = "" ;
			if ( trim ( $t ) == "" ) $post .= "</p><p$justify>" ;

			if ( substr($t,0,1) == " " ) { $ppre = "<font face=courier size=-1>" ; $ppost = "</font>".$ppost ; $t = substr ( $t , 1 ) ; }
			if ( substr($t,0,1) == "*" ) { $ppre .= "<li>" ; $ppost .= "</li>" ; }
			if ( substr($t,0,1) == "#" ) { $ppre .= "<li>" ; $ppost .= "</li>" ; }
			if ( substr($t,0,1) == ":" ) { $ppre .= "<dt><dd>" ; }

			$nbegin = "" ;
			while ( $t != "" and substr($obegin,0,1)==substr($t,0,1) ) {
				$nbegin .= substr($obegin,0,1) ;
				$t = substr ( $t , 1 ) ;
				$obegin = substr ( $obegin , 1 ) ;
				}

			$obegin = str_replace ( "*" , "</ul>" , $obegin ) ;
			$obegin = str_replace ( "#" , "</ol>" , $obegin ) ;
			$obegin = str_replace ( ":" , "</DL>" , $obegin ) ;
			$pre .= $obegin ;
			$obegin = $nbegin ;

			while ( substr ( $t , 0 , 1 ) == " " ) {
				$pre .= "&nbsp;" ;
				$t = substr ( $t , 1 ) ;
				$obegin .= "*" ;
				}

			while ( substr ( $t , 0 , 1 ) == "*" ) {
				$pre .= "<ul>" ;
				$t = substr ( $t , 1 ) ;
				$obegin .= "*" ;
				}

			while ( substr ( $t , 0 , 1 ) == "#" ) {
				$pre .= "<ol>" ;
				$t = substr ( $t , 1 ) ;
				$obegin .= "#" ;
				}

			while ( substr ( $t , 0 , 1 ) == ":" ) {
				$pre .= "<DL>" ;
				$t = substr ( $t , 1 ) ;
				$obegin .= ":" ;
				}

			$t = str_replace ( "  " , "&nbsp; " , $t ) ;

			$t = $pre.$ppre.$t.$ppost.$post ;
			$s .= $t."\n" ;
			}
		$s .= "</p>" ;

		$this->replaceAll ( "<p$justify>\n</p>" , "<p$justify></p>" , $s ) ;
		$this->replaceAll ( "<p$justify></p>" , "" , $s ) ;

		if ( $user->options["textTableBackground"] != "" ) {
			$s = str_replace ( "<table" , "<table".$user->options["textTableBackground"] , $s ) ;
			}

		$s = $this->parseImages ( $s ) ;
		$s = $this->replaceExternalLinks ( $s ) ;
		$s = $this->replaceInternalLinks ( $s ) ;
		return $s ;
		}

	# Header and footer section
	function getLinkBar () {
		$ret = "<a href=\"$PHP_SELF?\">HomePage</a>" ;

		$spl = $this->getSubpageList () ;
		if ( count ( $spl ) > 0 and $this->subpageTitle != "" ) {
			$zz = trim ( $this->parseContents ( $spl[0] ) ) ;
			$zz = strstr ( $zz , "<a"  ) ;
			$zz = str_replace ( "</p>" , "" , $zz ) ;
			$ret .= " | ".$zz ;
			}

		$ret .= " | <a href=\"$PHP_SELF?title=special:RecentChanges\">Recent Changes</a>" ;
		if ( $this->canEdit() ) $ret .= " | <a href=\"$PHP_SELF?action=edit&title=$this->url\">Edit this page</a>" ;
		if ( !$this->isSpecialPage ) $ret .= " | <a href=\"$PHP_SELF?action=history&title=$this->url\">History</a>\n" ;
		$ret .= " | <a href=\"$PHP_SELF?title=special:RandomPage\">Random Page</a>" ;
		$ret .= " | <a href=\"$PHP_SELF?title=special:Special_pages\">Special Pages</a>" ;
		return $ret ;
		}
	function getHeader () {
		global $user , $action ;
		$t = $this->getNiceTitle ( $this->title ) ;
		if ( substr_count ( $t , ":" ) > 0 ) $t = ucfirst ( $t ) ;
		global $HTTP_USER_AGENT ;
		if ( stristr ( $HTTP_USER_AGENT , "MSIE" ) ) $border = "border=1 frame=below rules=none" ;
		else $border = "border=0" ;
		$ret = "<table ".$user->options["quickBarBackground"]." width=100% $border bordercolor=black cellspacing=0>\n<tr>" ;
		if ( $user->options["leftImage"] != "" )
			$ret .= "<td width=1% rowspan=2 bgcolor=#000000><img src=\"".$user->options["leftImage"]."\"></td>" ;
		$ret .= "<td valign=top height=1>" ;
		if ( $this->isSpecialPage ) {
			if ( $action == "edit" ) {
				$ret .= "<font size=+3>Editing ".$t."</font><br>Your changes will not be committed until you hit the <b>Save</b> button.<br>" ;
				$ret .= "You can get help <a href=\"$PHP_SELF?title=wikipedia:help/edit\">here</a>." ;
			} else $ret .= "<font size=+3>".$t."</font>" ;
		} else {
			$ret .= "<font size=+3><a href=\"$PHP_SELF?search=$this->title\">".$this->getNiceTitle($t)."</a>$this->thisVersion</font>" ;
			if ( $user->isLoggedIn ) {
				if ( $user->doWatch($this->title) )
					$ret.="<br><a href=\"$PHP_SELF?action=watch&title=$this->secureTitle&mode=no\">Stop watching this article for me</a>";
				else $ret .= "<br><a href=\"$PHP_SELF?action=watch&title=$this->secureTitle&mode=yes\">Watch this article for me</a>" ;
				}
			}
		$ret .= "</td>\n<td valign=top width=200 rowspan=2 nowrap>".$user->getLink()."<br>" ;
		if ( $user->isLoggedIn ) $ret .= "<a href=\"$PHP_SELF?title=special:userLogout\">Log out</a> | <a href=\"$PHP_SELF?title=special:editUserSettings\">Preferences</a>" ;
		else $ret .= "<a href=\"$PHP_SELF?title=special:userLogin\">Log in</a>" ;
		$ret .= " | <a href=\"$PHP_SELF?title=wikipedia:Help\">Help</a>" ;
		$ret .= "<FORM>Search: <INPUT TYPE=text NAME=search SIZE=20></FORM>" ;
		$ret .= "</td>\n<td rowspan=2 width=1><a href=\"$PHP_SELF?\"><img border=0 src=\"wiki.png\"></a></td></tr>\n" ;
		$ret .= "<tr><td valign=bottom>".$this->getLinkBar()."</td></tr></table>" ;
		return $ret ; 
		}
	function getQuickBar () {
		global $user ;
		$column = "<nowiki>" ;
		$column .= "<a href=\"$PHP_SELF?title=HomePage\">HomePage</a>\n" ;
		$column .= "<br><a href=\"$PHP_SELF?title=special:RecentChanges\">Recent Changes</a>\n" ;
		if ( $this->canEdit() ) $column .= "<br><a href=\"$PHP_SELF?action=edit&title=$this->url\">Edit this page</a>\n" ;
# No user management due to request of Larry
#		if ( $this->canDelete() ) $column .= "<br><a href=\"$PHP_SELF?action=deletepage&title=$this->url\">Delete this page</a>\n" ;
#		if ( $this->canProtect() ) $column .= "<br><a href=\"$PHP_SELF?action=protectpage&title=$this->url\">Protect this page</a>\n" ;
#		if ( $this->canAdvance() ) $column .= "<br><a href=\"$PHP_SELF?title=special:Advance&topic=$this->safeTitle\">Advance</a>\n" ;
		if ( !$this->isSpecialPage ) $column .= "<br><a href=\"$PHP_SELF?action=history&title=$this->url\">History</a>\n" ;
		$column .= "<br><a href=\"$PHP_SELF?title=special:Upload\">Upload files</a>\n" ;
		$column .= "<hr>" ;
		$column .= "<a href=\"$PHP_SELF?title=special:Statistics\">Statistics</a>" ;
		$column .= "<br>\n<a href=\"$PHP_SELF?title=special:LonelyPages\">Orphans</a>" ;
		$column .= "<br>\n<a href=\"$PHP_SELF?title=special:WantedPages\">Most wanted</a>" ;
		$column .= "<br>\n<a href=\"$PHP_SELF?title=special:AllPages\">All pages</a>" ;
		$column .= "<br>\n<a href=\"$PHP_SELF?title=special:RandomPage\">Random Page</a>" ;
		$column .= "<br>\n<a href=\"$PHP_SELF?title=special:ShortPages\">Stub articles</a>" ;
		$column .= "<br>\n<a href=\"$PHP_SELF?title=special:ListUsers\">List users</a>" ;
		if ( $user->isLoggedIn ) {
			$column .= "<br>\n<a href=\"$PHP_SELF?title=special:WatchList\">My watchlist</a>" ;
			}
		$a = $this->getOtherNamespaces () ;
		if ( count ( $a ) > 0 ) $column .= "<hr>".implode ( "<br>\n" , $a ) ;
		return $column."</nowiki>" ;
		}
	function getMiddle ( $ret ) {
		global $user , $action ;
		$oaction = $action ;
		if ( $action == "edit" ) $action = "" ;
		if ( $user->options["quickBar"] == "right" or $user->options["quickBar"] == "left" or $user->options["forceQuickBar"] != "" ) {
			$column = $this->getQuickBar();
			$spl = $this->getSubpageList () ;
			if ( count ( $spl ) > 0 ) $column .= "<font size=-1>".$this->parseContents ( "<hr>".implode ( "<br>\n" , $spl ) )."</font>" ;

			$column = "<td ".$user->options["quickBarBackground"]." width=120 valign=top nowrap>".$column."</td>" ;
			$ret = "<td valign=top>".$ret."</td>" ;
			global $HTTP_USER_AGENT ;
			if ( stristr ( $HTTP_USER_AGENT , "MSIE" ) ) $border = "border=1 frame=void rules=cols" ;
			else $border = "border=0" ;
			$table = "<table width=100% $border bordercolor=black cellpadding=2 cellspacing=0><tr>" ;
			$qb = $user->options["quickBar"] ;
			if ( $user->options["forceQuickBar"] != "" ) $qb = $user->options["forceQuickBar"] ;
			if ( $qb == "left" ) $ret = $table.$column.$ret."</tr></table>" ;
			else if ( $qb == "right" ) $ret = $table.$ret.$column."</tr></table>" ;
			}
		$action = $oaction ;
		return $ret ; 
		}
	function getFooter () {
		$ret = $this->getLinkBar() ;
		global $HTTP_USER_AGENT ;
		if ( stristr ( $HTTP_USER_AGENT , "MSIE" ) ) $border = "border=1 frame=above rules=none" ; 
		else $border = "border=0" ;
		$ret = "<table width=100% $border bordercolor=black cellspacing=0><tr><td>$ret</td></tr></table>" ;
		if ( !$this->isSpecialPage ) $ret .= "<a href=\"$PHP_SELF?title=$this->secureTitle&diff=yes\">(diff)</a> " ;
		$a = $this->getOtherNamespaces () ;
		if ( count ( $a ) > 0 ) $ret .= "Other namespaces : ".implode ( " | " , $a ) ;
		$ret .= "<FORM>Search: <INPUT TYPE=text NAME=search SIZE=20></FORM>" ;
		return $ret ; 
		}
	function renderPage () {
		global $pageTitle , $diff ;
		$pageTitle = $this->title ;
		if ( isset ( $diff ) ) $middle = $this->doDiff().$this->contents ;
		else $middle = $this->contents ;
		$middle = $this->getMiddle($this->parseContents($middle)) ;
		return $this->getHeader().$middle.$this->getFooter() ;
		}
	function doDiff () {
		global $oldID , $version , $user ;
		$ret = "<nowiki><font color=red><b>BEGIN DIFF</b></font><br>\n" ;
		$connection = getDBconnection () ;
		mysql_select_db ( "wikipedia" , $connection ) ;

		if ( isset ( $oldID ) ) { # Diff between old versions
			$sql = "SELECT old_old_version FROM old WHERE old_id=$oldID" ;
			$result = mysql_query ( $sql , $connection ) ;
			$s = mysql_fetch_object ( $result ) ;
			$sql = "SELECT * FROM old WHERE old_id=$s->old_old_version" ;
		} else { # Diff between old and new version
			$sql = "SELECT cur_old_version FROM cur WHERE cur_title=\"$this->secureTitle\"" ;
			$result = mysql_query ( $sql , $connection ) ;
			$s = mysql_fetch_object ( $result ) ;
			$sql = "SELECT * FROM old WHERE old_id=$s->cur_old_version" ;
			$s->old_old_version = $s->cur_old_version ;
			}

		$fc = $user->options["background"] ;
		if ( $fc == "" ) $fc = "=white" ;
		$fc = substr ( $fc , strpos("=",$fc)+1 ) ;
		$bc = " bordercolor=".$fc ;
		$fc = " color=".$fc ;
		$result = mysql_query ( $sql , $connection ) ;
		if ( $result != "" and $s->old_old_version != 0 ) {
			$s = mysql_fetch_object ( $result ) ;
			mysql_free_result ( $result ) ;
			$a1 = explode ( "\n" , $this->contents ) ;
			$a2 = explode ( "\n" , $s->old_text ) ;
			$nl = array () ;
			$dl = array () ;
			foreach ( $a1 as $x ) if ( !in_array ( $x , $a2 ) ) array_push ( $nl , htmlentities ( $x ) ) ;
			foreach ( $a2 as $x ) if ( !in_array ( $x , $a1 ) ) array_push ( $dl , htmlentities ( $x ) ) ;
			# Output
			$ret .= "<font color=#0000FF>Blue text</font> was added or changed, <font color=red>red text</font> was changed or deleted." ;
			$ret .= "<table width=100% border=1$bc cellspacing=0 cellpadding=2>\n" ;
			foreach ( $nl as $x ) $ret .= "<tr><td bgcolor=#0000FF><font$fc>$x</font></td></tr>\n" ;
			foreach ( $dl as $x ) $ret .= "<tr><td bgcolor=#DD0000><font$fc>$x</font></td></tr>\n" ;
			$ret .= "</table>\n" ;
		} else if ( isset ( $oldID ) and $s->old_old_version == 0 ) $ret .= "This is the first version of this article. All text is new!<br>\n" ;
		else if ( !isset ( $oldID ) ) $ret .= "This is the first version of this article. All text is new!<br>\n" ;
		else $ret .= "No diff possible. Reason unknown. Blame the programmer! Tell him SQL said \"$sql\"<br>\n" ;
		mysql_close ( $connection ) ;
		
		$ret .= "<font color=red><b>END DIFF</b></font><hr></nowiki>\n" ;
		return $ret ;
		}
	}
?>