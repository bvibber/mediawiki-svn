<?
# The wikiPage class is used for both database management and rendering (display) of articles
# It inherits some functions and variables from the wikiTitle class

#include_once ( "wikiSkins.php" ) ;


class WikiPage extends WikiTitle {
    var $contents ; # The actual article body
    var $backLink ; # For redirects
    var $knownLinkedLinks , $knownUnlinkedLinks ; # Used for faster display
    var $otherLanguages ; # This article in other languages
    var $params ; # For the params entry; updated on Save only
    var $counter ; # Page view counter
    var $timestamp ; # Time and date of last edit
    var $cache ; # For cached pages
    var $canBeCached ;
    var $skin , $skinLoaded = false ;


    var $dtOpen; # Used for handling DLs
    var $lastSection; # Used for p/pre sections

#### Database management functions

    # This loads an article from the database, or calls a special function instead (all pages with "special:" namespace)
    function load ( $t , $doRedirect = true ) {
        global $action , $user , $wikiNoSuchSpecialPage , $wikiAllowedSpecialPages , $diff ;
	if ( $diff == "yes" ) $doRedirect = false ;
        if ( $doRedirect ) $this->backLink = "" ;
        $updateCounter = "" ;
        $this->knownLinkedLinks = array () ;
        $this->knownUnlinkedLinks = array () ;
        $this->SetTitle ( $t ) ;
        $this->isSpecialPage = false ;
        $this->canBeCached = true ;
        $this->revision = "current" ;
        if ( $this->namespace == "special" ) { # Special page, calling appropriate function
            $allowed = $wikiAllowedSpecialPages ; # List of allowed special pages
            if ( in_array ( "is_sysop" , $user->rights ) ) { # Functions just for sysops
               array_push ( $allowed , "asksql" ) ;
               array_push ( $allowed , "blockip" ) ;
            }
            $call = $this->mainTitle ;
            if ( !in_array ( strtolower ( $call ) , $allowed ) ) {
                $this->isSpecialPage = true ;
                $this->contents = str_replace ( "$1" , $call , $wikiNoSuchSpecialPage ) ;
                return ;
                }
            $this->title = $call ;
            $inc = "./special_".strtolower($call).".php" ;
            include_once ( $inc ) ;
            $this->contents = $call () ;
            $this->isSpecialPage = true ;
            $this->canBeCached = false ;
            return ; # contents of special page is returned here!!!
            }

        # No special page, loading article form the database
        global $useCachedPages ;
        $connection = getDBconnection () ;
        $thisVersion = "" ;
        $this->params = array () ;
        global $oldID , $version , $wikiOldVersion , $wikiDescribePage , $wikiRedirectFrom ;
        if ( isset ( $oldID ) ) { # an old article version
            $sql = "SELECT * FROM old WHERE old_id=$oldID" ;
            $result = mysql_query ( $sql , $connection ) ;
            if ( $s = mysql_fetch_object ( $result ) ) {
                $this->SetTitle ( $s->old_title ) ;
                $this->contents = $s->old_text ;
                $this->timestamp = $s->old_timestamp ;
                $this->thisVersion = str_replace ( "$1" , $version , $wikiOldVersion ) ;
                $this->thisVersion = str_replace ( "$2" , $this->secureTitle , $this->thisVersion ) ;
                $this->thisVersion = "<br><font size=-1>".$this->thisVersion."</font>" ;
                }
            else $this->contents = $wikiDescribePage ;
        } else { # The current article version
            $sql = "SELECT cur_title, cur_text, cur_timestamp, cur_cache, cur_params, cur_counter
                    FROM cur
                    WHERE cur_title=\"".$this->secureTitle."\"" ;
            $result = mysql_query ( $sql , $connection ) ;
            if ( $s = mysql_fetch_object ( $result ) ) {
                $this->SetTitle ( $s->cur_title ) ;
                $this->contents = $s->cur_text ;
                $this->knownLinkedLinks = array () ;
                $sql_l = "SELECT DISTINCT linked_to FROM linked WHERE linked_from = \"$s->cur_title\" " ;
                $result_l = mysql_query ( $sql_l , $connection ) ;
                while ( $s_l = mysql_fetch_object ( $result_l ) )
                    array_push ( $this->knownLinkedLinks, $s_l->linked_to ) ;
                mysql_free_result ( $result_l ) ;
                $this->knownUnlinkedLinks = array () ;
                $sql_u = "SELECT DISTINCT unlinked_to FROM unlinked WHERE unlinked_from = \"$s->cur_title\" " ;
                $result_u = mysql_query ( $sql_u , $connection ) ;
                while ( $s_u = mysql_fetch_object ( $result_u ) )
                    array_push ( $this->knownUnlinkedLinks, $s_u->unlinked_to ) ;
                mysql_free_result ( $result_u ) ;
                $this->timestamp = $s->cur_timestamp ;
                if ( $useCachedPages ) $this->cache = $s->cur_cache ;
                if ( $s->cur_params != "" ) $this->params = explode ( "\n" , $s->cur_params ) ;
                $this->counter = $s->cur_counter+1 ;
                $updateCounter = $this->counter ;
                }
            else $this->contents = $wikiDescribePage ;
            }
        mysql_free_result ( $result ) ;

        if ( $updateCounter != "" ) {
            $sql = "UPDATE cur SET cur_counter=$updateCounter,cur_timestamp=cur_timestamp WHERE cur_title=\"".$this->secureTitle."\"" ;
            $result = mysql_query ( $sql , $connection ) ;
            }

        if ( strtolower ( substr ( $this->contents , 0 , 9 ) ) == "#redirect" and $doRedirect and $action != "edit" and !isset ($oldID) ) { # #REDIRECT
            $link = wikiLink ( $this->getNiceTitle() ) ;
            $link = "<a href=\"$link&amp;action=view&amp;redirect=no\">".$this->getNiceTitle()."</a>" ;
            $link = str_replace ( "$1" , $link , $wikiRedirectFrom ) ;
            $this->backLink = $link ;
	    $target = preg_replace ( '/^#redirect\s+\[\[\s*([^\]\n]+)\s*\]\].*$/i' , '$1' , $this->contents ) ;
            $this->load ( trim($target) , false , $backLink ) ;
            }
        }

    # This function - well, you know...
    function special ( $t ) {
        $this->title = $t ;
        $this->isSpecialPage = true ;
        }

/*
	# Currently not used, maybe later
    # Look for all matches in $this->params
    function getParam ( $p ) {
        $ret = array () ;
        if ( !isset ( $this->params ) or count ( $this->params ) == 0 ) return $ret ;
        $p = strtolower ( $p ) ;
        foreach ( $this->params as $x ) {
            $y = explode ( " " , $x , 2 ) ;
            if ( count ( $y ) > 1 and strtolower ( trim ( $y[0] ) ) == $p ) array_push ( $ret , trim ( $y[1] ) ) ;
            }
        return $ret ;
        }
*/

    # This lists all namespaces that contain an article with the same name
    # Called by QuickBar() and getFooter()
    function getOtherNamespaces () {
        $a = array () ;
        if ( $this->isSpecialPage ) return $a ;

	global $wikiNS2Talk , $wikiTalk2NS , $wikiTalk , $wikiArticle ;
	$ns = strtolower ( $this->namespace ) ;
	$link = "" ;
	if ( isset ( $wikiNS2Talk[$ns] ) ) {
		$link = $wikiNS2Talk[$ns] ;
		$text = $wikiTalk ;
	} else if ( isset ( $wikiTalk2NS[$ns] ) ) {
		$link = $wikiTalk2NS[$ns] ;
		$text = $wikiArticle ;
		}

	if ( $link != "" ) $link .= ":" ;	
	$link .= $this->mainTitle ;

	if ( $link != "" ) {
		$text = ucfirstIntl ( $text ) ;
		$t = new wikiTitle ;
		$t->setTitle ( $link ) ;
		$id = getMySQL ( "cur" , "cur_id" , "cur_title=\"".$t->secureTitle."\"" ) ;
		if ( $id != "" ) $class = "green" ;
		else $class = "red" ;
		array_push ( $a , "<a class=\"$class\" href=\"".wikiLink($link)."\">$text</a>" ) ;
		}

	return $a ;
	}

    # This creates a new article if there is none with the same title yet
    function ensureExistence () {
        global $useCachedPages ;
        $this->makeSecureTitle () ;
        if ( $this->doesTopicExist() ) return ;
        $connection = getDBconnection () ;
        $sql = "INSERT INTO cur (cur_title, cur_ind_title)
                VALUES (\"$this->secureTitle\", REPLACE(\"$this->secureTitle\",'_',' '))" ;
        mysql_query ( $sql , $connection ) ;
        # since the page now exists we move all links in the table unlinked to the table linked
        $sql = "INSERT INTO linked ( linked_from, linked_to )
                SELECT unlinked_from, unlinked_to
                FROM unlinked
                WHERE unlinked_to = \"$this->secureTitle\"" ;
        mysql_query ( $sql , $connection ) ;
        $sql = "DELETE FROM unlinked WHERE unlinked_to = \"$this->secureTitle\"" ;
        mysql_query ( $sql , $connection ) ;
        # Flushing cache for all pages that linked to the empty topic
        if ( $useCachedPages ) {
            $sql1 = "SELECT DISTINCT linked_from FROM linked WHERE linked_to = \"$this->secureTitle\" " ;
            $result1 = mysql_query ( $sql , $connection ) ;
            while ( $s1 = mysql_fetch_object ( $result1 ) ) {
                $sql2 = "UPDATE cur SET cur_cache=\"\", cur_timestamp=cur_timestamp WHERE cur_title = \"%$s1->linked_from\" " ;
                mysql_query ( $sql2 , $connection ) ;
            }
            mysql_free_result ( $result1 );
        }
    }

    # This function performs a backup from the "cur" to the "old" table, building a
    #  single-linked chain with the cur_old_version/old_old_version entries
    # The target data set is defined by $this->secureTitle
    function backup () {
        $id = getMySQL ( "cur" , "cur_id" , "cur_title=\"$this->secureTitle\"" ) ;
        $oid = getMySQL ( "cur" , "cur_old_version" , "cur_id=$id" ) ;

        $connection = getDBconnection () ;
        $sql = "SELECT * FROM cur WHERE cur_id=$id" ;
        $result = mysql_query ( $sql , $connection ) ;
	if ( $result == "" ) return ;
        $s = mysql_fetch_object ( $result ) ;
        mysql_free_result ( $result ) ;

        $sql = "INSERT INTO old (old_title,old_old_version,old_text,old_comment,old_user,old_user_text,old_timestamp,old_minor_edit)";
        $sql .= " VALUES (\"$this->secureTitle\",\"$oid\",\"".addslashes ( $s->cur_text ) ."\",\"".addslashes ( $s->cur_comment ) ."\",\"$s->cur_user\",\"$s->cur_user_text\",$s->cur_timestamp,$s->cur_minor_edit)" ;
        mysql_query ( $sql , $connection ) ;

        $sql = "SELECT old_id FROM old WHERE old_old_version=\"$oid\" AND old_title=\"$this->secureTitle\"" ;
        $result = mysql_query ( $sql , $connection ) ;
        $s = mysql_fetch_object ( $result ) ;
        mysql_free_result ( $result ) ;

        $oid = $s->old_id ;
        setMySQL ( "cur" , "cur_old_version" , $oid , "cur_id=$id" ) ;
        }

    # This function stores the passed parameters into the database (the "cur" table)
    # The target data set is defined by $this->secureTitle
    function setEntry ( $text , $comment , $userID , $userName , $minorEdit , $addSQL = "" ) {
        global $useCachedPages ;
        $cond = "cur_title=\"$this->secureTitle\"" ;

        global $linkedLinks , $unlinkedLinks ;
        
        $connection = getDBconnection () ;        
        
        $this->parseContents ( $text , true ) ; # Calling with savingMode flag set, so only internal Links are parsed

        # store linked links in linked table
        $sql = "DELETE FROM linked WHERE linked_from = \"$this->secureTitle\" ;" ;
        $r = mysql_query ( $sql , $connection ) ;
        $linkTitle = new wikiTitle ;
        foreach ( array_keys ( $linkedLinks ) as $linked_link ) { 
            $linkTitle->title = $linked_link ;
            $linkTitle->makeSecureTitle () ;
            $secureLinkTitle = $linkTitle->secureTitle ;
            if ( $secureLinkTitle ) {
                $sql = "INSERT INTO linked (linked_from, linked_to) VALUES ( \"$this->secureTitle\" , \"$secureLinkTitle\" ) ;" ;
                $r = mysql_query ( $sql , $connection ) ;
            }
        }

        # store unlinked links in unlinked table
        $sql = "DELETE FROM unlinked WHERE unlinked_from = \"$this->secureTitle\" ;" ;
        $r = mysql_query ( $sql , $connection ) ;
        $linkTitle = new wikiTitle ;        
        foreach ( array_keys ( $unlinkedLinks ) as $unlinked_link ) {
            $linkTitle->title = $unlinked_link ;
            $linkTitle->makeSecureTitle () ;
            $secureLinkTitle = $linkTitle->secureTitle ;
            if ( secureLinkTitle ) {
                $sql = "INSERT INTO unlinked (unlinked_from, unlinked_to) VALUES ( \"$this->secureTitle\" , \"$secureLinkTitle\" ) ;" ;
                $r = mysql_query ( $sql , $connection ) ;
            }
        }
        
        $pa = implode ( "\n" , $this->params ) ;

        if ( $useCachedPages ) $addCache = "cur_cache=\"\"," ;

        #$text = str_replace ( "\"" , "\\\"" , $text ) ;
#       $comment = str_replace ( "\"" , "\\\"" , $comment ) ;
        #$userName = str_replace ( "\"" , "\\\"" , $userName ) ;
	$text = addslashes ( $text ) ;
	#$comment = addslashes ( $comment ) ;
	$userName = addslashes ( $userName ) ;
        $comment = htmlspecialchars ( $comment ) ;
        $sql = "UPDATE cur SET cur_text=\"$text\",cur_comment=\"$comment\",cur_user=\"$userID\"," ;
        $sql .= "cur_user_text=\"$userName\",cur_minor_edit=\"$minorEdit\",";
        $sql .= "$addCache cur_params=\"$pa\"$addSQL WHERE $cond" ;
        $r = mysql_query ( $sql , $connection ) ;
        }

#### Rendering functions
    # This function converts wiki-style internal links like [[Main Page]] to the appropriate HTML code
    # It has to handle namespaces, subpages, and alternate names (as in [[namespace:page/subpage name]])
    function replaceInternalLinks ( $s ) {
        global $wikiInterwiki , $action , $wikiOtherLanguages ;
        global $user , $unlinkedLinks , $linkedLinks , $wikiPrintLinksMarkup ;
        if ( !isset ( $this->knownLinkedLinks ) ) $this->knownLinkedLinks = array () ;
        if ( !isset ( $this->knownUnlinkedLinks ) ) $this->knownUnlinkedLinks = array () ;
        $abc = " abcdefghijklmnopqrstuvwxyz" ;
        $a = explode ( "[[" , " ".$s ) ;
        $s = array_shift ( $a ) ;
        $s = substr ( $s , 1 ) ;
        $connection = getDBconnection () ;
        $iws = array_keys ( $wikiInterwiki ) ;
        foreach ( $a as $t ) {
            $b = explode ( "]]" , $t , 2 ) ;
            if ( count($b) < 2 ) { # No matching ]]
                $s .= "[[".$b[0] ;
            } else {
                $c = explode ( "|" , $b[0] , 2 ) ;
                $link = $c[0] ;

                $topic = new WikiTitle ;
                $topic->setTitle ( $link ) ;
                $link = $this->getLinkTo ( $topic ) ;
                $topic->setTitle ( $link ) ;

                if ( count ( $c ) == 1 ) array_push ( $c , $topic->getMainTitle() ) ;
                while ( $b[1] != "" and strpos ( $abc , substr ( $b[1] , 0 , 1 ) ) > 0 ) {
                    $c[1] .= substr ( $b[1] , 0 , 1 ) ;
                    $b[1] = substr ( $b[1] , 1 ) ;
                    }
                $text = $c[1] ;

                if ( in_array ( $topic->secureTitle , $this->knownLinkedLinks ) ) $doesItExist = true ;
                else $doesItExist = $topic->doesTopicExist( $connection ) ;
		$isItValid = $topic->validateTitle() ;

                # Check for interwiki links
                $iwl = "" ;
                foreach ( $iws as $ii )
                    if ( strtolower ( $ii ) == strtolower ( $topic->namespace ) )
                        $iwl = $wikiInterwiki[$ii] ;
                if ( $iwl != "" ) { # Interwiki Link
                    $tt = ucfirstIntl ( str_replace ( " " , "_" , $topic->mainTitle ) ) ;
                    $iwl = str_replace ( "$1" , $tt , $iwl ) ;
                    if ( $c[0] == $c[1] ) $text = $topic->getNiceTitle ( $topic->mainTitle ) ;
                    $linkStyle = "class=\"interwiki\"";
                    $s .= "<a $linkStyle href=\"$iwl\">$text</a>" ;
                } else if ( in_array ( strtolower ( $topic->namespace ) , array_keys ( $wikiOtherLanguages ) ) ) {
                    $tt = ucfirstIntl ( str_replace ( " " , "_" , $topic->mainTitle ) ) ;
                    $iwl = str_replace ( "$1" , $tt , $wikiOtherLanguages[strtolower($topic->namespace)] ) ;
                    if ( $c[0] == $c[1] ) $text = $topic->getNiceTitle ( $topic->mainTitle ) ;
                    $this->otherLanguages[$topic->namespace] = $iwl ;
                } else if ( $doesItExist && $isItValid ) {
                    $linkedLinks[$topic->secureTitle]++ ;
                    if ( $user->options["showHover"] == "yes" ) $hover = "title=\"" . htmlspecialchars ( $link ) . "\"" ;
                    #if ( $user->options["underlineLinks"] == "no" ) $linkStyle = " style=\"color:blue;text-decoration:none\"" ;
                    $ulink = nurlencode ( $link ) ;
                    $s .= "<a href=\"".wikiLink($ulink)."\" $hover>$text</a>" ;
                } else if ($isItValid ) {
                    $unlinkedLinks[$link]++ ;
                    if ( $user->options["showHover"] == "yes" ) $hover = "title=\"Edit '" . htmlspecialchars ( $link ) . "'\"" ;
                    $ulink = wikiLink( nurlencode ( $link ) . "&amp;action=edit" ) ;
                    if ( substr_count ( $text , " " ) > 0 ) {
                        $s .= "<span class=\"newlinkedge\">[</span>";
                        $bracket = "]";
                    } else {
                        $bracket = "";
                    }
                    $s .= "<a class=\"newlink\" href=\"$ulink\" $hover>$text</a>" ;
                    $s .= "<span class=\"newlinkedge\">$bracket<a href=\"$ulink\" $hover>?</a></span>";
                } else {
		    # Invalid local link
		    $s .= "[[".$b[0]."]]".$b[1];
		    continue ;
		    }

                $s .= $b[1] ;
                }
            }
        return $s ;
        }

    # This function replaces wiki-style image links with the HTML code to display them
    function parseImages ( $s ) {
        $s = ereg_replace ( "([^[])http://([a-zA-Z0-9_/:.~\%\-]*)\.(png|PNG|jpg|JPG|jpeg|JPEG|gif|GIF)" , "\\1<img src=\"http://\\2.\\3\">" , $s ) ;
        return $s ;
        }

    # This function replaces wiki-style external links (both with and without []) with HTML links
    function subReplaceExternalLinks ( $s , $what , $autonumber ) {
	if ( substr_count ( $s , "[$what" ) == 0 ) return $s ; # Speedup
        global $user ;
        $cnt = 1 ;
        $a = explode ( "[$what" , " ".$s ) ;
        $s = array_shift ( $a ) ;
        $s = substr ( $s , 1 ) ;
        $image = "" ; # with an <img tag, this will be displayed before external links
        #$linkStyle = "style=\"color:#3333BB;text-decoration:none\"" ;
        $linkStyle = "class=\"external\"";
        foreach ( $a as $t ) {
            $b = spliti ( "]" , $t , 2 ) ;
            if ( count($b) < 2 ) $s .= "[Broken link : $b[0]]" ;
            else {
                $c = explode ( " " , $b[0] , 2 ) ;
                if ( count ( $c ) == 1 ) array_push ( $c , "" ) ;
                $link = $c[0] ;
                $text = trim ( $c[1] ) ;
                if ( $text == "" ) {
			if ( $autonumber ) $text = "[".$cnt++."]" ;
			else $text = "[$what$link]" ;
                } else {
                    if ( substr_count ( $text , " " ) > 0 and $user->options["underlineLinks"] == "no" )
                        $text = "[$text]" ;
                    }
                if ( substr_count ( $b[1] , "<hr>" ) > 0 ) $cnt = 1 ;
                $link = "~$what".$link ;
                if ( $user->options["showHover"] == "yes" ) $hover = "title=\"$link\"" ;
                $theLink = "<a href=\"$link\" $linkStyle $hover>$image$text</a>" ;
                $s .= $theLink.$b[1] ;
                }
            }

        $o_no_dot = "A-Za-z0-9_~/=?\:\%\+\&\#\-" ;
        $o = "\.$o_no_dot" ;
        $s = eregi_replace ( "([^~\"])http://([$o]+[$o_no_dot])([^$o_no_dot])" , "\\1<a href=\"http://\\2\" $linkStyle>".$image."http://\\2</a>\\3" , $s ) ;
        $s = str_replace ( "~$what" , "$what" , $s ) ;

        return $s ;
        }

	function replaceExternalLinks ( $s ) {
		$s = $this->subReplaceExternalLinks ( $s , "http:" , true ) ;
		$s = $this->subReplaceExternalLinks ( $s , "ftp:" , false ) ;
		$s = $this->subReplaceExternalLinks ( $s , "gopher:" , false ) ;
		$s = $this->subReplaceExternalLinks ( $s , "news:" , false ) ;
		$s = $this->subReplaceExternalLinks ( $s , "mailto:" , false ) ;
		return $s ;
		}

    # This function replaces the newly introduced wiki variables with their values (for display only!)
    function replaceVariables ( $s ) {
        global $wikiDate ;
        $countvars = substr_count ( $s , "{{" ) ;
	if ( $countvars == 0 ) return $s ;
        $var=date("m"); $s = str_replace ( "{{CURRENTMONTH}}" , $var , $s ) ;
        $var=$wikiDate[strtolower(date("F"))]; $s = str_replace ( "{{CURRENTMONTHNAME}}" , $var , $s ) ;
        $var=date("j"); $s = str_replace ( "{{CURRENTDAY}}" , $var , $s ) ;
        $var=$wikiDate[strtolower(date("l"))]; $s = str_replace ( "{{CURRENTDAYNAME}}" , $var , $s ) ;
        $var=date("Y"); $s = str_replace ( "{{CURRENTYEAR}}" , $var , $s ) ;
        if ( strstr ( $s , "{{NUMBEROFARTICLES}}" ) ) { # This should count only "real" articles!
            $connection=getDBconnection() ;
            $sql="SELECT COUNT(*) as number FROM cur WHERE cur_title NOT LIKE \"%:%\" AND cur_text LIKE \"%,%\"";
            $result = mysql_query ( $sql , $connection ) ;
            $var = mysql_fetch_object ( $result ) ;
            $var = number_format ( $var->number , 0 ) ;
            mysql_free_result ( $result ) ;
            $s = str_replace ( "{{NUMBEROFARTICLES}}" , $var , $s ) ;
            }

        if ( $countvars != substr_count ( $s , "{{" ) )
            $this->canBeCached = false ;

        return $s ;
        }

    # This function is called to replace wiki-style tags with HTML, e.g., the first occurrence of ''' with <b>, the second with </b>
    function pingPongReplace ( $f , $r1 , $r2 , $s , $startAtLine = false ) {
	if ( substr_count ( $s , $f ) == 0 ) return $s ;
        $ret = "" ;
        $lines = explode ( "\n" , $s ) ;
        foreach ( $lines as $s ) {
            if ( $startAtLine == false or substr ( $s , 0 , strlen ( $f ) ) == $f ) {
                $a = explode ( $f , " ".$s ) ;
                $app = "" ;
                $s = substr ( array_shift ( $a ) , 1 ) ;
                $r = $r1 ;
                if ( count ( $a ) % 2 != 0 ) $app = $f.array_pop ( $a ) ;
                foreach ( $a as $t ) {
                    $s .= $r.$t ;
                    if ( $r == $r1 ) $r = $r2 ;
                    else $r = $r1 ;
                    }
            }
            if ( $ret != "" ) $ret .= "\n" ;
            $ret .= $s.$app ;
            }
        return $ret ;
        }

    # Called from parseContents before saving
    function scanForParams ( $s ) {
        $this->params = array () ;
        return ;
        }

    # This function organizes the <nowiki> parts and calls subPageContents() for the wiki parts
    function parseContents ( $s , $savingMode = false ) {
        global $linkedLinks , $unlinkedLinks , $framed ;
        $linkedLinks = array () ;
        $unlinkedLinks = array () ;
        $this->otherLanguages = array () ;
        $s .= "\n" ;

        # Parsing <pre> here
        $a = spliti ( "<pre>" , $s ) ;
	if ( count ( $a ) > 1 ) { # Speedup
	        $s = array_shift ( $a ) ;
	        foreach ( $a as $x ) {
	            $b = spliti ( "</pre>" , $x , 2 ) ;
	            if ( count ( $b ) == 1 ) $s .= "&lt;pre&gt;$x" ;
			else {
			$x = str_replace ( array ( "<" , ">" ) , array ( "&lt;" , "&gt;" ) , $b[0] ) ;
			$s .= "<pre>$x</pre>$b[1]" ;
	                }
	            }
        	$s = str_replace ( "<pre>" , "<pre><nowiki>" , $s ) ;
	        $s = str_replace ( "</pre>" , "</nowiki></pre>" , $s ) ;
		}

	# Parsing <nowiki> here
        $a = spliti ( "<nowiki>" , $s ) ;
        $b = array () ;
	if ( count ( $a ) > 1 ) { # Speedup
	        # $d needs to contain a unique string - this can be altered at will, as long it stays unique!
	        $d = "3iyZiyA7iMwg5rhxP0Dcc9oTnj8qD1jm1Sfv" ;
	        $s = array_shift ( $a ) ;
	        foreach ( $a as $x ) {
	            $c = spliti ( "</nowiki>" , $x , 2 ) ;
	            if ( count ( $c ) == 2 ) {
	                array_push ( $b , $c[0] ) ;
	                $s .= $d.$c[1] ;
	            } else $s .= "<nowiki>".$x ;
	            }
		}

        # If called from setEntry(), only parse internal links and return dummy entry
        if ( $savingMode ) {
            $this->scanForParams ( $s ) ;
            return $this->replaceInternalLinks ( $s ) ;
            }

        $k = explode ( "." , $s , 2 ) ;
        $k = explode ( "\n" , $k[0] , 2 ) ;
        $s = $this->subParseContents ( $s , $savingMode ) ;

        # Meta tags
        global $metaDescription , $metaKeywords , $wikiMetaDescription ;
        if ( $metaDescription == "" and $metaKeywords == "" ) {
            $k = str_replace ( "\"" , "" , $k[0] ) ;
            $k = str_replace ( "[" , "" , $k ) ;
            $k = str_replace ( "]" , "" , $k ) ;
            $k = str_replace ( "'" , "" , $k ) ;
            $k = ereg_replace ( "<[^>]*>" , "" , $k ) ;
            $metaDescription = str_replace ( "$1" , $k , $wikiMetaDescription ) ;

            $k = array () ;
            array_push ( $k , "wikipedia" ) ;
            array_push ( $k , $this->title ) ;
            foreach ( $this->knownLinkedLinks as $x ) array_push ( $k , $x ) ;
            foreach ( $this->knownUnlinkedLinks as $x ) array_push ( $k , $x ) ;
            $k = implode ( "," , $k ) ;
            $k = str_replace ( "\"" , "" , $k ) ;
            $k = str_replace ( "_" , " " , $k ) ;
            $metaKeywords = $k ;
            }

        # replacing $d with the actual nowiki contents
	if ( count ( $b ) > 0 ) {
	        $a = spliti ( $d , $s ) ;
        	$s = array_shift ( $a ) ;
	        foreach ( $a as $x ) {
        	    $nw = array_shift ( $b ) ;
	            $s .= $nw . $x ;
        	    }
		}

        return $s ;
        }

    # This function removes "forbidden" HTML tags
    function removeHTMLtags ( $s ) {
        # Only allow known tags
        $htmlpairs = array( "b", "i", "u", "font", "big", "small", "sub", "sup", "h1", "h2", "h3", "h4", "h5", "h6",
            "cite", "code", "em", "s", "strike", "strong", "tt", "var", "div", "center", "blockquote", "ol",
            "ul", "dl", "table", "caption", "pre" , "ruby", "rt" , "rb" , "rp" ); # Tags which must be closed
        $htmlsingle = array( "br", "p", "hr", "li", "dt", "dd" ); # Tags which don't need to be closed

	# Tags which can be nested, currently this is a little fuzzy
	$htmlnest = array ( "table" , "tr" , "td" , "th" , "div" , "blockquote" , "ol" , "ul" , "dl" , "font" , "big" , "small" , "sub" , "sup" ) ;
	$tabletags = array ( "td" , "th" , "tr" ) ; # Tags never allowed outside of a <table>

	$htmlsingle = array_merge ( $tabletags , $htmlsingle ) ;
        $htmlelements = array_merge ( $htmlsingle , $htmlpairs );

        # Allowed attributes -- we don't want scripting, etc
        $htmlattrs = array(
        # General
        "title" , "align" , "lang" , "dir" , "width" , "height" , "bgcolor" ,
        #br 
        "clear" ,
        # hr
        "noshade" , 
        # blockquote, q
        "cite" ,
        # font
        "size" , "face" , "color" ,
        # lists
        "type" , "start" , "value", "compact" , # All deprecated, BTW
        # tables
        "summary" , "width" , "border" , "frame" , "rules" , "cellspacing" , "cellpadding" ,
        "valign" , "char" , "charoff" , "colgroup" , "col" , "span" , "abbr" , "axis" , "headers" , "scope" , "rowspan" , "colspan" ,
        # I don't *think* these are dangerous
        "id", "class" , "name" , "style" );

    # Delete comments right up front
    $s = preg_replace( "/<!--.*-->/", "", $s );

    $bits = explode ( "<" , $s ) ;
    $s = array_shift ( $bits ) ;
    $tagstack = array() ; $tablestack = array () ;
    foreach ( $bits as $x ) {
        preg_match ( "/^(\/?)(\w+)([^>]*)(\/{0,1}>)([^<]*)$/", $x, $regs );
        list ( $qbar , $slash , $t , $params , $brace , $rest ) = $regs;
	$badtag = 0 ;
        #echo "($slash|$t|$params|$brace|$rest)";
        if ( in_array ( $t = strtolower ( $t ) , $htmlelements ) ) {
		# Check our stack
		if ( $slash ) {
			# Closing a tag...
			if ( ! in_array ( $t , $htmlsingle ) && ($ot = array_pop ( $tagstack ) ) != $t ) {
				array_push ( $tagstack , $ot ) ;
				$badtag = 1 ;
			} else {
				if ( $t == "table" ) {
					$tagstack = array_pop ( $tablestack ) ;
					}
				$newparams = "" ;
			}
		} else {
			# Keep track for later
			if ( in_array ( $t , $tabletags ) && ! in_array ( "table" , $tagstack ) ) {
				$badtag = 1;
			} elseif ( in_array ( $t , $tagstack ) && ! in_array ( $t , $htmlnest ) ) {
				$badtag = 1 ;
			} elseif ( ! in_array ( $t , $htmlsingle ) ) {
				if ( $t == "table" ) {
					array_push ( $tablestack , $tagstack ) ;
					$tagstack = array () ;
					}
				array_push ( $tagstack , $t ) ;
				}
				# Strip non-approved attributes from the tag
				$newparams = preg_replace (
	                		"/(\\w+)(\\s*=\\s*([^\\s\">]+|\"[^\">]*\"))?/e" ,
			                "(in_array(strtolower(\"\$1\"),\$htmlattrs)?(\"\$1\".((\"x\$3\" != \"x\")?\"=\$3\":'')):'')" ,
			                $params) ;
			}
	    
		if ( ! $badtag ) {
			$rest = str_replace ( ">" , "&gt;" , $rest ) ;
			#echo "($slash)($t)($params)->($newparams)($brace)($rest)";
			$s .= "<$slash$t$newparams$brace$rest";
			continue;
			}
		}
	$s .= "&lt;" . str_replace ( ">" , "&gt;" , $x ) ;
        }

	# Close off any remaining tags
	while ( $t = array_pop ( $tagstack ) ) {
		$s .= "</$t>\n";
		if ( $t == "table" )
			$tagstack = array_pop ( $tablestack ) ;
		}

	return $s;
        }

    # This function will auto-number headings
    function autoNumberHeadings ( $s ) {
        if ( $this->isSpecialPage ) return $s ;
        $j = 0 ;
        $n = -1 ;
        for ( $i ; $i < 9 ; $i++ ) {
            if ( stristr ( $s , "<h$i>" ) != false ) {
                $j++ ;
                if ( $n == -1 ) $n = $i ;
                }
            }
        if ( $j < 2 ) return $s ;
        $i = $n ;
        $v = array ( 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 ) ;
        $t = "" ;
        while ( count ( spliti ( "<h" , $s , 2 ) ) == 2 ) {
            $a = spliti ( "<h" , $s , 2 ) ;
            $j = substr ( $a[1] , 0 , 1 ) ;
            if ( strtolower ( $j ) != "r" ) {
                $t .= $a[0]."<h".$j.">" ;
                $v[$j]++ ;
                $b = array () ;
                for ( $k = $i ; $k <= $j ; $k++ ) array_push ( $b , $v[$k] ) ;
                for ( $k = $j+1 ; $k < 9 ; $k++ ) $v[$k] = 0 ;
                $t .= implode ( "." , $b ) . " " ;
                $s = substr ( $a[1] , 2 ) ;
            } else { # <HR> tag, not a heading!
                $t .= $a[0]."<hr>" ;
                $s = substr ( $a[1] , 2 ) ;
                }
            }
        return $t.$s ;
        }

    function ISBN ( $s ) {
        $a = split ( "ISBN " , " $s" ) ;
	if ( count ( $a ) < 2 ) return $s ;
        $s = substr ( array_shift ( $a ) , 1 ) ;
        $valid = "0123456789-ABCDEFGHIJKLMNOPQRSTUVWXYZ" ;
        foreach ( $a as $x ) {
            $isbn = "" ;
            $blank = "" ;
            while ( substr ( $x , 0 , 1 ) == " " ) {
                $blank .= " " ;
                $x = substr ( $x , 1 ) ;
                }
            while ( strstr ( $valid , substr ( $x , 0 , 1 ) ) != false ) {
                $isbn .= substr ( $x , 0 , 1 ) ;
                $x = substr ( $x , 1 ) ;
                }
            $num = str_replace ( "-" , "" , $isbn ) ;
            $num = str_replace ( " " , "" , $num ) ;
            if ( $num == "" ) {
                $s .= "ISBN $blank$x" ;
            } else { # Removed BarnesAndNoble and Amazon, left link to PriceScan
                $s .= "<a href=\"http://www.pricescan.com/books/bookDetail.asp?isbn=$num\">ISBN $isbn</a> " ;
#               $s .= "<a href=\"http://shop.barnesandnoble.com/bookSearch/isbnInquiry.asp?isbn=$num\">ISBN $isbn</a> " ;
#               $s .= "(<a href=\"http://www.amazon.com/exec/obidos/ISBN=$num\">Amazon</a>, " ;
#               $s .= "<a href=\"http://www.pricescan.com/books/bookDetail.asp?isbn=$num\">Pricescan</a>)" ;
                $s .= $x ;
                }
            }
        return $s ;
        }

    # Some functions here useful for processing */#/: lines:
    #
    # Regrettably, the fact that we allow miscellaneous HTML in
    # in wiki text as well as our own markup makes it almost impossible
    # to parse a full DOM and output nicely-balanced and checked
    # XHTML, so we settle for HTML 4.0 and never close <p> tags (if
    # we tried we'd have headings inside paragraphs and such muck).
    # Someday...
    
    # getCommon() returns the length of the longest common substring
    #
    function closeParagraph( )
    {
        $result = "";
        if ( 0 != strcmp( "p", $this->lastSection ) &&
             0 != strcmp( "", $this->lastSection ) ) {
            $result = "</" . $this->lastSection  . ">";
        }
        $this->lastSection = "";
        return $result;
    }
    # of both arguments, starting at the beginning of both.
    #
    function getCommon( $st1, $st2 )
    {
        $fl = strlen( $st1 );
        $shorter = strlen( $st2 );
        if ($fl < $shorter) { $shorter = $fl; }

        for ( $i = 0; $i < $shorter; ++$i ) {
            if ( 0 != strcmp( substr( $st1, $i, 1 ), substr( $st2, $i, 1 ) ) ) {
                break;
            }
        }
        return $i;
    }

    # These next three functions open, continue, and close the list
    # element appropriate to the prefix character passed into them.
    #
    function openList( $char )
    {
        $result = $this->closeParagraph();

        if ( "*" == $char ) { $result .= "<ul><li>"; }
        else if ( "#" == $char ) { $result .= "<ol><li>"; }
        else if ( ":" == $char ) { $result .= "<dl><dd>"; }
        else if ( ";" == $char ) {
            $result .= "<dl><dt>";
            $this->dtOpen = true;
        }
        else { $result = "<!-- ERR 1 -->"; }

        return $result;
    }

    function nextItem( $char )
    {
        if ( "*" == $char || "#" == $char ) { return "</li><li>"; }
        else if ( ":" == $char || ";" == $char ) {
            $close = "</dd>";
            if ( $this->dtOpen ) { $close = "</dt>"; }
            if ( ";" == $char ) {
                $this->dtOpen = true;
                return $close . "<dt>";
            } else {
                $this->dtOpen = false;
                return $close . "<dd>";
            }
        }
        return "<!-- ERR 2 -->";
    }

    function closeList( $char )
    {
        if ( "*" == $char ) { return "</li></ul>"; }
        else if ( "#" == $char ) { return "</li></ol>"; }
        else if ( ":" == $char ) {
            if ( $this->dtOpen ) {
                $this->dtOpen = false;
                return "</dt></dl>";
            } else {
                return "</dd></dl>";
            }
        }
        return "<!-- ERR 3 -->";
    }

    # This function does the actual parsing of the wiki parts of the article, for regions NOT marked with <nowiki>
    function subParseContents ( $s ) {
        global $user ;
# Removed automatic links for CamelCase; wasn't working, anyway...
#       $s = ereg_replace ( "([\.|\n| )([a-z0-9]*[A-Z0-9]+[A-Za-z0-9]*)( |\n|\.)" , "\\1[[\\2]]\\3" , $s ) ;
        if ( ! $this->isSpecialPage )
            $s = $this->removeHTMLtags ( $s ) ; # Removing "forbidden" HTML tags
        #$s = ereg_replace ( "&amp;([a-zA-Z0-9#]+);" , "&\\1;" , $s ) ; # That's a long story... FIXME: What is this for? It mostly seems to make it very hard to write the code for an entity instead of the entity itself.

        # Now some repalcements wiki->HTML
        $s = ereg_replace ( "(^|\n)-----*" , "\\1<hr>" , $s ) ;
        $s = str_replace ( "<HR>" , "<hr>" , $s ) ;
        $s = $this->replaceVariables ( $s ) ;
        $s = $this->pingPongReplace ( "'''''" , "<i><b>" , "</b></i>" , $s ) ;
        $s = $this->pingPongReplace ( "'''" , "<b>" , "</b>" , $s ) ;
        $s = $this->pingPongReplace ( "''" , "<i>" , "</i>" , $s ) ;

        $s = preg_replace ( "/(^|\\n)==== ([^\\n]*) ====\s*(\\r|$)/" , "\\1<h4>\\2</h4>\\3" , $s ) ;
        $s = preg_replace ( "/(^|\\n)=== ([^\\n]*) ===\s*(\\r|$)/" , "\\1<h3>\\2</h3>\\3" , $s ) ;
        $s = preg_replace ( "/(^|\\n)== ([^\\n]*) ==\s*(\\r|$)/" , "\\1<h2>\\2</h2>\\3" , $s ) ;
        $s = preg_replace ( "/(^|\\n)= ([^\\n]*) =\s*(\\r|$)/" , "\\1<h1>\\2</h1>\\3" , $s ) ;

        $s = ereg_replace ( "\n====*" , "<hr>" , $s ) ;

        # Automatic links to subpages (e.g., /Talk -> [[/Talk]]   #DEACTIVATED
#       $s = ereg_replace ( "([\n ])/([a-zA-Z0-9]+)" , "\\1[[/\\2|/\\2]]" , $s ) ;

        # Parsing through the text line by line
        # The main thing happening here is handling of lines starting with * # : etc.
        $a = explode ( "\n" , $s ) ;
        $s = "<p>" ;
        $lastPref = "";
        $lastSection = "";
        $this->dtOpen = false;
        $inBlockElem = false;

        foreach ( $a as $t ) {
            $oLine = $t;
            $opl = strlen( $lastPref );
            $npl = strspn( $t, "*#:;" );
            $pref = substr( $t, 0, $npl );
            $pref2 = str_replace( ";", ":", $pref );
            $t = substr( $t, $npl );

            if ( 0 != $npl && 0 == strcmp( $lastPref, $pref2 ) ) {
                $s .= $this->nextItem( substr( $pref, -1 ) );

                if ( ";" == substr( $pref, -1 ) ) {
                    $cpos = strpos( $t, ":" );
                    if ( false === $cpos ) {
                        # PHP weirdness: need empty block here.
                    } else {
                        $term = substr( $t, 0, $cpos );
                        $s .= $term . "</dt><dd>";
                        $t = substr( $t, $cpos + 1 );
                    }
                }
            } else if (0 != $npl || 0 != $opl) {
                $cpl = $this->getCommon( $pref, $lastPref );

                while ( $cpl < $opl ) {
                    $s .= $this->closeList( substr( $lastPref, ($opl - 1), 1 ) );
                    --$opl;
                }
                while ( $npl > $cpl ) {
                    $char = substr( $pref, $cpl, 1 );
                    $s .= $this->openList( $char );

                    if ( ";" == $char ) {
                        $cpos = strpos( $t, ":" );
                        if ( false === $cpos ) {
                        } else {
                            $term = substr( $t, 0, $cpos );
                            $s .= $term . "</dt><dd>";
                            $t = substr( $t, $cpos + 1 );
                        }
                    }
                    ++$cpl;
                }
                $lastPref = $pref2;
            }
            if ( 0 == $npl ) { # No prefix--go to paragraph mode
                if ( preg_match( "/(<table|<blockquote|<h1|<h2|<h3|<h4|<h5|<h6)/i", $t ) ) {
                    $s .= $this->closeParagraph();
                    $inBlockElem = true;
                }
                if ( ! $inBlockElem ) {
                    if ( 0 == strcmp( " ", substr( $t, 0, 1 ) ) ) {
                        $newSection = "pre";
                    } else {
                        $newSection = "p";
                    }
                    if ( 0 == strcmp( "", trim( $oLine ) ) ) {
                        $s .= $this->closeParagraph();
                        $s .= "<" . $newSection . ">";
                    } else if ( 0 != strcmp( $this->lastSection, $newSection ) ) {
                        $s .= $this->closeParagraph();
                        if ( 0 != strcmp( "p", $newSection ) ) {
                            $s .= "<" . $newSection . ">";
                        }
                    }
                    $this->lastSection = $newSection;
                }
                if ( $inBlockElem &&
                     preg_match( "/(<\/table|<\/blockquote|<\/h1|<\/h2|<\/h3|<\/h4|<\/h5|<\/h6)/i", $t ) ) {
                    $inBlockElem = false;
                }
            }
            $s .= $t . "\n";
        }
        $s .= $this->closeParagraph();

        # Stuff for the skins
        if ( $user->options["textTableBackground"] != "" ) {
            $s = str_replace ( "<table" , "<table".$user->options["textTableBackground"] , $s ) ;
            }

        # And now, for the final...
        $s = $this->parseImages ( $s ) ;
        $s = $this->replaceExternalLinks ( $s ) ;
        $s = $this->replaceInternalLinks ( $s ) ;
        $s = $this->ISBN ( $s ) ;
        if ( $user->options["numberHeadings"] == "yes" ) $s = $this->autoNumberHeadings ( $s ) ;
        return $s ;
        }

#### Header and footer section

    # This generates the bar at the top and bottom of each page
    # Used by getHeader() and getFooter()
    function getLinkBar () {
        global $wikiMainPage ;
        global $user , $oldID , $version ;
        $editOldVersion = "" ;
        if ( $oldID != "" ) $editOldVersion="&amp;oldID=$oldID&amp;version=$version" ;
        $ret = "<a href=\"".wikiLink(urlencode($wikiMainPage))."\">$wikiMainPage</a>" ;

#	This is outdated
#        $spl = $this->getSubpageList () ;
#        if ( count ( $spl ) > 0 and $this->subpageTitle != "" and $user->options["showStructure"] == "yes" ) {
#            $zz = trim ( $this->parseContents ( $spl[0] ) ) ;
#            $zz = strstr ( $zz , "<a"  ) ;
#            $zz = str_replace ( "</p>" , "" , $zz ) ;
#            $zz = $this->getNiceTitle ( $zz ) ;
#            $ret .= " | ".$zz ;
#            }

        global $wikiRecentChanges , $wikiRecentChangesLink , $wikiEditThisPage , $wikiHistory , $wikiRandomPage , $wikiSpecialPages ;
        global $wikiSpecialPagesLink ;
        $ret .= " | <a href=\"".wikiLink("special:$wikiRecentChangesLink")."\">$wikiRecentChanges</a>" ;
        if ( $this->canEdit() ) $ret .= " | <a href=\"".wikiLink($this->url."$editOldVersion&amp;action=edit")."\">$wikiEditThisPage</a>" ;
        else if ( !$this->isSpecialPage ) $ret .= " | Protected page" ;
        if ( !$this->isSpecialPage ) $ret .= " | <a href=\"".wikiLink($this->url."&amp;action=history")."\">$wikiHistory</a>\n" ;
        $ret .= " | <a href=\"".wikiLink("special:RandomPage")."\">$wikiRandomPage</a>" ;
        $ret .= " | <a href=\"".wikiLink("special:$wikiSpecialPagesLink")."\">$wikiSpecialPages</a>" ;
        return $ret ;
        }



    # This generates the header with title, user name and functions, wikipedia logo, search box etc.
    function getHeader () {
	$this->loadSkin () ;
	$this->styleSheet () ;
	return $this->skin->getHeader ( $this ) ;
        }

    # This generates the QuickBar (also used by the list of special pages function)
    function getQuickBar () {
	$this->loadSkin () ;
	return $this->skin->getQuickBar ( $this ) ;
        }

    # This calls the parser and eventually adds the QuickBar. Used for display of normal article pages
    # Some special pages have their own rendering function
    function getMiddle ( $ret ) {
	$this->loadSkin () ;
	return $this->skin->getMiddle ( $this , $ret ) ;
        }

    # This generates the footer with link bar, search box, etc.
    function getFooter () {
	$this->loadSkin () ;
	return $this->skin->getFooter ( $this ) ;
        }

    # This generates header, diff (if wanted), article body (with QuickBar), and footer
    # The whole page (for normal pages) is generated here
    function renderPage ( $doPrint = false ) {
        global $pageTitle , $diff , $wikiArticleSource , $wikiCurrentServer , $wikiPrintLinksMarkup , $useCachedPages ;
        $pageTitle = $this->title ;
        if ( isset ( $diff ) ) {
            $this->canBeCached = false; # A little crude, but effective
            $middle = $this->doDiff().$this->contents ;
            }
        else $middle = $this->contents ;
        if ( $useCachedPages and !$this->isSpecialPage and $this->canBeCached) {
            if ( $this->cache != "" ) { # Using cache
                $middle = $this->cache ;
                #$middle = "<p>(cached)</p>" . $this->cache ; #FIXME

        # Need to check for other-language links, which do not appear in the link arrays
        $this->otherLanguages = array () ;
        global $wikiOtherLanguages ;
        preg_replace ( "/\[\[([a-z]{2})\:\s*([^\]]+)\s*\]\]/ie" ,
            "( ( ( \$langurl = \$wikiOtherLanguages[\$lang = strtolower ( \"\$1\" )] ) != '' )
            ? ( \$this->otherLanguages[\$lang] = str_replace ( '\\$1' ,
                ucfirstIntl ( str_replace ( array ( '+' , '%25' ) , array ( '_' , '%' ) , nurlencode ( \"\$2\" ) ) ) ,
                \$langurl ) )
            : '' )" ,
            $this->contents ) ;
            } else {
                $middle = $this->parseContents($middle) ;
                if ( $this->canBeCached ) { # Generating cache

        $this->cache = str_replace ( "\"" , "\\\"" , $middle ) ;
        $connection = getDBconnection () ;
        $sql = "UPDATE cur SET cur_cache=\"$this->cache\", cur_timestamp=cur_timestamp WHERE cur_title=\"$this->secureTitle\"" ;
        mysql_query ( $sql , $connection ) ;
        $sql = "UPDATE cur SET cur_text=\"\", cur_timestamp=cur_timestamp WHERE cur_title=\"Log:RecentChanges\"" ;
        mysql_query ( $sql , $connection ) ;

                    
                    }
                }
        } else {
            $middle = $this->parseContents($middle) ;
            }
        $middle = $this->getMiddle($middle) ;
        if ( $doPrint ) {
	    global $wikiPrintFooter ;
            $header = "<h1>".$this->getNiceTitle($pageTitle)."</h1>\n" ;
            $link = str_replace ( "$1" , $this->url , $wikiArticleSource ) ;
	    $footer = str_replace ( array ( "$1" , "$2" ) , array ( $wikiCurrentServer , $link ) , $wikiPrintFooter ) ;
            $ret = $header.$middle ;
            $ret = eregi_replace ( "<a[^>]*>\\?</a>" , "" , $ret ) ;
            $ret = eregi_replace ( "<a[^>]*>\\[" , "<a>" , $ret ) ;
            $ret = eregi_replace ( "\\]</a>" , "</a>" , $ret ) ;
            $ret = eregi_replace ( "<a[^>]*>([^<]*)</a>" , "<$wikiPrintLinksMarkup>\\1</$wikiPrintLinksMarkup>" , $ret ) ;
            return $ret.$footer ;
        } else {
            return $this->getHeader().$middle.$this->getFooter() ;
            }
        }

    # This displays the diff. Currently, only diff with the last edit!
    function doDiff () {
        global $oldID , $version , $user , $title , $wikiUndiff ;
        global $wikiBeginDiff , $wikiEndDiff , $wikiDiffLegend , $wikiDiffFirstVersion , $wikiDiffImpossible ;
        $ret = "<nowiki><font color=red><b>$wikiBeginDiff</b></font> (<a href=\"".wikiLink($title)."\">$wikiUndiff</a>)<br>\n\n" ;
        $connection = getDBconnection () ;

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
        if ( $fc == "" ) $fc = "=black" ;
        $fc = substr ( $fc , strpos("=",$fc)+1 ) ;
        $bc = " bordercolor=white" ;
        $fc = "black" ; # HOTFIX
        $fc = " color=".$fc ;
        $result = mysql_query ( $sql , $connection ) ;
        if ( $result != "" and $s->old_old_version != 0 ) {
            $s = mysql_fetch_object ( $result ) ;
            mysql_free_result ( $result ) ;
            # cut into lines, don't distinguish between different line-end conventions:
            $old_lines = explode ( "\n" , str_replace( "\r\n", "\n", htmlspecialchars( $s->old_text ) ) );
            $new_lines = explode ( "\n" , str_replace( "\r\n", "\n", htmlspecialchars( $this->contents ) ) ) ;
            include_once( "./difflib.php" );
            $diffs = new Diff($old_lines, $new_lines);
            $formatter = new TableDiffFormatter();
            $ret .= $formatter->format($diffs);
        } else if ( isset ( $oldID ) and $s->old_old_version == 0 ) $ret .= $wikiDiffFirstVersion ;
        else if ( !isset ( $oldID ) ) $ret .= $wikiDiffImpossible ;
        
        $ret .= "<font color=red><b>$wikiEndDiff</b></font><hr></nowiki>\n" ;
        return $ret ;
        }

	function styleSheet () {
		$this->loadSkin () ;
		return $this->skin->getStylesheet ( $this ) ;		
		}

	function loadSkin () {
		global $user ;
		if ( $this->skinLoaded ) return ;

		$s = "skin".str_replace ( " " , "" , $user->options[skin] ) ;
		if ( $s == "skin" OR $s == "skinNone" ) $s = "skinStandard" ;
		$this->skin = new $s ;

		$this->skinLoaded = true ;
		}

    }
?>
