<?
include_once ( "special_makelog.php" ) ;

function syntErr ( $num, $arg ) {
    global $srchSyntErr, $minSrchSize ;

    $errtxt = preg_replace ( "/\\$1/", $arg, $srchSyntErr[$num] ) ;
    $errtxt = preg_replace ( "/\\$2/", $minSrchSize, $errtxt ) ;
    return "<B> [!! " . $errtxt . "] </B>\n" ;
}

# this function parses the search string and returns
# an SQL-like condition. It is in the form of a recursive descent LL(1)
# parser. The LL(1) syntax is:
# E  ::= T E'
# E' ::= '' | <or> E
# T  ::= ( E ) T" | <not> T' T" | <word> T"
# T' ::= <word> | ( E )
# T" ::= '' | <and> T | T
#
# Errors:
# 0 "SYNTAX ERROR: missing '$1'; inserted",
# 1 "SYNTAX ERROR: unexpected '$1'; ignored",
# 2 "SYNTAX ERROR: illegal symbol '$1'; ignored",
# 3 "SYNTAX ERROR: the word '$1' is too short, the index requires at least $2 characters",
# 4 "SYNTAX ERROR: missing search word; inserted" 

#FIXME: Someone who understands this function, could you put in some comments? It would be very helpful.

function srchStrParse ( $state ) {
    global $and, $or, $not ;   # boolean search operators
    global $search ;        # the string we are parsing
    global $minSrchSize ;   # smallest wordsize that is indexed by the fulltext index
    global $errors ;        # list of syntax errors
    static $pos = 0;        # position of parser in string
    
    switch ($state) {
        case 0:
            while ( preg_match ( "/\s/", $search{$pos} ) ) $pos++ ;
            $res = srchStrParse ( 2 ) ;
            $res .= srchStrParse ( 1 ) ; 
            break ;
        case 1:
            while ( preg_match ( "/\s/", $search{$pos} ) ) $pos++ ;
            if ( $search{$pos} ) {
                if ( $search{$pos} == "(" ) {
                    $res2 = srchStrParse ( 0 );         # we presume that the "or" is missing
                    $res = syntErr ( 0, $or ) ." $res2";
                } elseif ( $search{$pos} == ")" ) {
                    $res = "" ;
                } else {
                    $oldpos = $pos;
                    while ( preg_match ( "/\w/", $search{$pos} ) ) $pos++ ;
                    $word = substr ( $search, $oldpos, $pos - $oldpos );
                    if ( preg_match( "/^".$not."$/i", $word ) ) {
                        $pos = $oldpos;
                        $res2 = srchStrParse ( 0 ) ;    # we presume that the "or" is missing
                        $res = syntErr ( 0, $or ) ." $res2";                
                    } elseif ( preg_match ( "/^".$and."$/i", $word ) ) {
                        $res2 = srchStrParse ( 0 ) ;    # we presume that the "and" is redundant
                        $res = syntErr ( 1, $and ) ." $res2";                
                    } elseif ( preg_match ( "/^".$or."$/i", $word ) ) {                        
                        $res2 = srchStrParse ( 0 ) ;
                        $res = " OR $res2";                
                    } elseif ( strlen ( $word ) >= $minSrchSize ) {
                        $pos = $oldpos;
                        $res2 = srchStrParse ( 0 ) ;    # we presume that the "or" is missing
                        $res = syntErr ( 0, $or ) ." $res2";                
                    } elseif ( strlen ( $word ) > 0 ) {
                        $pos = $oldpos;
                        $res2 = srchStrParse ( 0 ) ;    # we presume that the "or" is missing
                        $res = syntErr ( 0, $or ) ." $res2";                
                    } else {
                        $sym = $search{$pos} ;
                        $pos++;
                        $res2 = srchStrParse ( 1 ) ;    # presume the current symbol is redundant
                        $res = syntErr ( 2, $sym ) . " $res2" ;
                    }
                }
            } else
                $res = "" ;
            break ;
        case 2:
            while ( preg_match ( "/\s/", $search{$pos} ) ) $pos++ ;
            if ( $search{$pos} ) {
                if ( $search{$pos} == "(" ) {
                    $pos++ ;
                    $res1 = srchStrParse ( 0 ) ;
                    while ( preg_match ( "/\s/", $search{$pos} ) ) $pos++ ;
                    if ( $search{$pos} == ")" ) {
                        $pos++ ; 
                        $res2 = srchStrParse ( 4 ) ;
                        $res = "( $res1 ) $res2" ;
                    } else {
                        $res2 = srchStrParse ( 4 ) ;    # we presume the ")" is missing
                        $res = "( $res1 " . syntErr ( 0, ")" ) . " $res2" ;
                    }
                } elseif ( $search{$pos} == ")" ) {
                    $pos++;
                    $res2 = srchStrParse ( 2 );         # we presume the ")" is redundant
                    $res = syntErr ( 1, ")" ) . " $res2" ;                 
                } else {
                    $oldpos = $pos;
		    while ( preg_match ( "/[\\w\\x80-\\xff]/", $search{$pos} ) ) $pos++ ;
                    $word = substr ( $search, $oldpos, $pos - $oldpos );
                    if ( preg_match( "/^".$not."$/i", $word ) ) {
                        $res2 = srchStrParse ( 3 );
                        $res3 = srchStrParse ( 4 );
                        $res = "NOT $res2 $res3" ;
                    } elseif ( preg_match ( "/^".$and."$/i", $word ) ) {
                        $res2 = srchStrParse ( 2 );     # presume the and is redundant
                        $res = syntErr ( 1, $and ) . " $res2" ; 
                    } elseif ( preg_match ( "/^".$or."$/i", $word ) ) {                        
                        $res2 = srchStrParse ( 2 );     # presume the or is redundant
                        $res = syntErr ( 1, $or ) . " $res2" ;                         
                    } elseif ( strlen ( $word ) >= $minSrchSize ) {
                        $res2 = srchStrParse ( 4 );
                        $res = "\"$word\" $res2";
                    } elseif ( strlen ( $word ) > 0 ) {
                        $res2 = srchStrParse ( 4 ) ;
                        $res = syntErr ( 3, $word ) . " $res2";
                    } else {
                        $sym = $search{$pos} ;
                        $pos++;
                        $res2 = srchStrParse ( 2 ) ;    # presume the current symbol is redundant
                        $res = syntErr ( 2, $sym ) . " $res2" ;
			echo "grrr"; #FIXME
                    }
                }
            } else
                $res = syntErr ( 4, "" ) ;    # there should have been some kind of condition here
            break;
        case 3:
            while ( preg_match ( "/\s/", $search{$pos} ) ) $pos++ ;
            if ( $search{$pos} ) {
                if ( $search{$pos} == "(" ) {
                    $pos++ ;
                    $res1 = srchStrParse ( 0 ) ;
                    while ( preg_match ( "/\s/", $search{$pos} ) ) $pos++ ;
                    if ( $search{$pos} == ")" ) {
                        $pos++ ; 
                        $res = " ( $res1 )" ;
                    } else  # we presume the closing bracket is missing
                        $res = " ( $res1 " . syntErr ( 0, ")" ) ;
                } elseif ( $search{$pos} == ")" ) {
                    $pos++;
                    $res2 = srchStrParse ( 3 ) ;    # we presume the bracket is redundant
                    $res = " [-)] $res2" ;
                } else {
                    $oldpos = $pos;
                    while ( preg_match ( "/\w/", $search{$pos} ) ) $pos++ ;
                    $word = substr ( $search, $oldpos, $pos - $oldpos );
                    if ( preg_match( "/^".$not."$/i", $word ) ) {
                        $res2 = srchStrParse ( 3 ) ;    # we presume the not is redundant
                        $res = syntErr ( 1, $not ) . " $res2" ;
                    } elseif ( preg_match ( "/^".$and."$/i", $word ) ) {
                        $res2 = srchStrParse ( 3 ) ;    # we presume the and is redundant
                        $res = syntErr ( 1, $and ) . " $res2" ;
                    } elseif ( preg_match ( "/^".$or."$/i", $word ) ) {                        
                        $res2 = srchStrParse ( 3 ) ;    # we presume the or is redundant
                        $res = syntErr ( 1, $or ) . " $res2" ;
                    } elseif ( strlen ( $word ) >= $minSrchSize ) {
                        $res = " \"$word\"" ;
                    } elseif ( strlen ( $word ) > 0 ) {
                        $res = syntErr ( 3, $word ) ;
                    } else {
                        $sym = $search{$pos} ;
                        $pos++;
                        $res2 = srchStrParse ( 3 ) ;    # presume the current symbol is redundant
                        $res = syntErr ( 2, $sym ) . " $res2" ;
                    }
                }
            } else
                $res = syntErr ( 4, "" ) ;
            break;
        case 4:
            while ( preg_match ( "/\s/", $search{$pos} ) ) $pos++ ;
            if ( $search{$pos} ) {
                if ( $search{$pos} == "(" ) {
                    $res2 = srchStrParse ( 2 ) ;
                    $res = " AND $res2" ;
                } elseif ( $search{$pos} == ")" ) {
                    $res = "";
                } else {
                    $oldpos = $pos;
                    while ( preg_match ( "/\w/", $search{$pos} ) ) $pos++ ;
                    $word = substr ( $search, $oldpos, $pos - $oldpos );
                    if ( preg_match( "/^".$not."$/i", $word ) ) {
                        $pos = $oldpos ;
                        $res2 = srchStrParse ( 2 ) ;
                        $res = " AND $res2" ;
                    } elseif ( preg_match ( "/^".$and."$/i", $word ) ) {
                        $res2 = srchStrParse ( 2 ) ;
                        $res = " AND $res2" ;
                    } elseif ( preg_match ( "/^".$or."$/i", $word ) ) {                        
                        $pos = $oldpos;
                        $res = "";
                    } elseif ( strlen ( $word ) >= $minSrchSize ) {
                        $pos = $oldpos ;
                        $res2 = srchStrParse ( 2 ) ;                     
                        $res = " AND $res2" ; 
                    } elseif ( strlen ( $word ) > 0 ) {
                        $pos = $oldpos ;
                        $res = srchStrParse ( 2 ) ;                     
                    } else {
                        $sym = $search{$pos} ;
                        $pos++;
                        $res2 = srchStrParse ( 4 ) ;    # presume the current symbol is redundant
                        $res = syntErr ( 2, $sym ) . " $res2" ;                
                    }
                }
            } else
                $res = "";
            break;
    }
    return $res ;
}

# FIX ME! : this is probably redundant and should be imported from somewhere else
$allTags = array ( "b", "i", "u", "font", "big", "small", "sub", "sup", "h1", "h2", "h3", "h4", "h5", "h6",
            "cite", "code", "em", "s", "strike", "strong", "tt", "var", "div", "center", "blockquote", "ol",
            "ul", "dl", "table", "caption", "pre", "br", "p", "hr", "li", "dt", "dd", "td" , "th" , "tr" );

# the following function displays a piece of an article in the search result
# with the search words highlighted

function searchLineDisplay ( $v , $words) {
    global $allTags;
    
    # replace all allowed HTML tags
    foreach ( $allTags as $tn ) {
        $v = preg_replace ( "/<(\/?)".$tn."[^>]*>/iU" , " " , $v ) ;
    }
    # break URLs
    $v = preg_replace ( "/http:\/\//i" , "http: //" , $v ) ;

    # replace / remove / neutralize wiki markup
    $v = trim(str_replace("\n"," ",$v)) ;
    $v = str_replace ( "'''" , "" , $v ) ;
    $v = str_replace ( "''" , "" , $v ) ;
    $v = ereg_replace ( "\{\{\{.*\}\}\}" , "?" , $v ) ;
    $v = trim ( $v ) ;
    if ( $v{0} == ":" ) $v = " $v" ;
    if ( $v{0} == "*" ) $v = " $v" ;
    if ( $v{0} == "#" ) $v = " $v" ;
    if ( $v{0} == "-" ) $v = " $v" ;
    
    # highlight the search terms
    foreach ( $words as $w ) {
        $v = preg_replace ( "/(\b".preg_quote( $w, "/" )."\b)/i" , "'''\\1'''" , $v ) ;    # highlight search term
        # move highlighting outside link, if link is not already highlighted
        while ( preg_match ( "/([^']|[^'].|^)(\[\[[^\[\]]*)'''([^\[\]]*)'''([^\[\]]*\]\])/i", $v ) )
            $v = preg_replace ( "/([^']|[^'].|^)(\[\[[^\[\]]*)'''([^\[\]]*)'''([^\[\]]*\]\])/i", "\\1'''\\2\\3\\4'''", $v ) ;
        # remove highlighting inside link if link is already highlighted
        while ( preg_match ( "/('')(\[\[[^\[\]]*)'''([^\[\]]*)'''([^\[\]]*\]\])/i", $v ) )
            $v = preg_replace ( "/('')(\[\[[^\[\]]*)'''([^\[\]]*)'''([^\[\]]*\]\])/i", "\\1\\2\\3\\4", $v ) ;
    }

    $v = "<font size=-1>$v</font>" ;
    return $v ;
}

function doSearch () {
    global $THESCRIPT ;
    global $vpage , $search , $startat , $user , $wikiRecodeInput ;
    global $wikiSearchTitle , $wikiSearchedVoid , $wikiNoSearchResult , $wikiSearchHelp ;
    global $wikiSearchError ;
    global $allSearch ;         # contains total size of the search result
    global $titleSearch ;       # contains size of result of query on titles

    $vpage = new WikiPage ;
    $vpage->special ( $wikiSearchTitle ) ;

    $r = array () ;
    $s = "" ;

    if ( $search == "" )
        $s = $wikiSearchedVoid . "\n\n" . $wikiSearchHelp ;
    else {
        $search = $wikiRecodeInput ( $search ) ;
        if ( !isset ( $startat ) ) $startat = 1 ;
        $perpage = $user->options["resultsPerPage"] ;
        $connection = getDBconnection () ;
        
        $parsedCond = srchStrParse ( 0 ) ;     # translate search to semi-SQL
        
        if ( preg_match ( "/\[!!/", $parsedCond ) ) {
            $s = "<H3> $wikiSearchError </H3> \n " . $parsedCond ;
	    $s .= "\n\n" . $wikiSearchHelp ;
	  }
        else {
            preg_match_all ( "/\"(\w+)\"/", $parsedCond, $matches ) ;
            $words = $matches[1] ;          # determine the search words (positive & negative)

            $fallbackSearch = 0 ;
	    do {
	    if ( $fallbackSearch ) {
	    	# Fallback if fulltext search is causing trouble
		# create SQL conditon for title search
        	$titleSQL = preg_replace ( "/\"([\\w\\x80-\\xff]+)\"/", "cur_ind_title LIKE \"%\\1%\"", $parsedCond ) ;
	        # create SQL conditon for body
        	$bodySQL = preg_replace ( "/\"([\\w\\x80-\\xff]+)\"/", "cur_text LIKE \"%\\1%\"", $parsedCond ) ;
		#echo $titleSQL, $bodySQL;
	    } else {
	    	# We can't do a fulltext search on anything smaller than 4 chars unless we recompile MySQL.
	        #$parsedCond2 = preg_replace ( "/([\\w\\x80-\\xff]{1,3})\"/" , "" , $parsedCond ) ;
		# create SQL conditon for title search
        	$titleSQL = preg_replace ( "/\"([\\w\\x80-\\xff]+)\"/", "MATCH (cur_ind_title) AGAINST (\"\\1\")", $parsedCond ) ;
	        # create SQL conditon for body
        	$bodySQL = preg_replace ( "/\"([\\w\\x80-\\xff]+)\"/", "MATCH (cur_text) AGAINST (\"\\1\")", $parsedCond ) ;
	    }
            
            # first we establish the total size of the result
            # if it wasn't already established
            
            if ( !isset ( $allSearch ) ) {
            
                $sql1 = "SELECT COUNT(*) AS cnt
                        FROM cur
                        WHERE ( $titleSQL )
                          AND cur_title NOT LIKE \"%:%\"";
                    
                $result = mysql_query ( $sql1 , $connection ) ;
                $row = mysql_fetch_object ( $result );
                $titleSearch = $row->cnt ;
                mysql_free_result ( $result ) ;
            
                $sql2 = "SELECT COUNT(*) AS cnt
                        FROM cur
                        WHERE ( $bodySQL ) AND NOT ( $titleSQL )
                          AND cur_title NOT LIKE \"%:%\"";
    
                $result = mysql_query ( $sql2 , $connection ) ;
                $row = mysql_fetch_object ( $result );
                $allSearch = $titleSearch + $row->cnt ;
                mysql_free_result ( $result ) ;
            	}
	    #if ( $allSearch == 0 ) { $fallbackSearch++ ; unset ( $allSearch ) ; }
	    } while ( !isset ( $allSearch ) && ( $fallbackSearch < 2 ) ) ;
            
            # Now we proceed with presenting the found results
    
            if ( $allSearch == 0 ) {
                
                # nothing found
            
                global $wikiUnsuccessfulSearch , $wikiUnsuccessfulSearches ;
                $s = "<h2>".str_replace("$1",$search,$wikiNoSearchResult)."</h2>" ;
		$s = $s . "\n" . $wikiSearchHelp ;
                # Appending log page "wikpedia:Unsuccessful searches"
                $now = date ( "Y-m" , time() ) ;
                $logText = "*[[$search]]\n" ;
                makeLog ( str_replace ( "$1" , $now , $wikiUnsuccessfulSearches ) ,
                        $logText ,
                        str_replace ( "$1" , $search , $wikiUnsuccessfulSearch ) ) ;
    
            } else {
    
                # intial announcement of found pages
    
                global $wikiFoundHeading , $wikiFoundText ;
                $s .= "<table width=\"100%\" bgcolor=\"#FFFFCC\"><tr><td><font size=\"+1\"><b>$wikiFoundHeading</b></font><br>\n" ;
                $n = str_replace ( "$1" , $allSearch , $wikiFoundText ) ;
                $n = str_replace ( "$2" , htmlspecialchars ( $search ) , $n ) ;
                $s .= "$n</td></tr></table>\n" ;
    
                # We get the part of the result we are interested in.
                # We don't bother the database with a query unless
                # we really have to.
    
                $offset1 = $startat - 1;
                $limit1 = min ( $titleSearch, $perpage );
                if ( $limit1 > 0 ) {
                    $sql1 = "SELECT cur_title, cur_text
                            FROM cur
                            WHERE ( $titleSQL )
                              AND cur_title NOT LIKE \"%:%\"
                            LIMIT $offset1, $limit1" ;
                    $result1 = mysql_query ( $sql1 , $connection );
                } else $result1 = "";
    
                $offset2 = max ( $startat - $titleSearch - 1, 0 );
                $limit2 = max ( $perpage - max( $titleSearch - $startat + 1, 0 ) , 0 ); 
                if ( $limit2 > 0 ) {
                    $sql2 = "SELECT cur_title, cur_text
                            FROM cur
                            WHERE ( $bodySQL ) AND
                              NOT ( $titleSQL )
                              AND cur_title NOT LIKE \"%:%\"
                            LIMIT $offset2, $limit2";
                    $result2 = mysql_query ( $sql2 , $connection );
                } else $result2 = "";
                
                # to save memory (cur_text can be really big) we do no collect
                # all results in an array, but process them one by one
        
                # presentation of the found pages
                
                $s .= "<table>" ;
                $realcnt = $startat;
                foreach ( array ($result1, $result2) as $result ) {
                    if ( $result ) {                                        # don't bother about result we don't have
                        while ( $row = mysql_fetch_object ( $result ) ) {
                            # add extra newlines for what we also consider as paragraph delimiters
                            $ct = preg_replace ("/(\<p [^>]*>|<p>|\n[\*#:\-])/iU", "\r\n\r\n\\1", $row->cur_text ) ;
                            $ct = preg_split ( "/\r\\n\r\\n/", $ct ) ;      # We split everything in paragraphs
                            $par = array_shift( $ct );
                            if ( strlen ( $par ) > 500 ) {                  # if the paragraph is too big we guess the sentences
                                $par = preg_replace ( "/(\.|!|\?)(\s+[A-Z])/U", "\\1\r\n\r\n\\2", $par) ;
                                $lines = preg_split ( "/\r\\n\r\\n/", $par ) ;
                                if ( strlen ( $lines[0] ) > 500 ) {         # still too big?
                                    $par = substr ( $lines[0], 0, 500 ) ;   # no more mister nice guy approach
                                    $lines[0] = substr ( $lines[0], 500, strlen ( $lines[0] ) - 500 ) ;
                                } else
                                    $par = array_shift( $lines ) ;          # take first sentence
                                $ct = array_merge ( $lines, $ct ) ;         # add other sentences back to $ct
                            }
                            $y = searchLineDisplay( $par, $words ) ;
                            $foundpar = false;                    
                            foreach ( $ct as $par ) {
                                if ( strlen ( $par ) > 500 ) {  # if the paragraph is too big we again guess the sentences
                                    $par = preg_replace ( "/(\.|!|\?)(\s+[A-Z])/U", "\\1\r\n\r\n\\2", $par) ;
                                    $pars1 = preg_split ( "/\r\\n\r\\n/", $par ) ;
                                    $pars = array ();
                                    foreach ( $pars1 as $par ) {    # let's see if they are still not too big
                                        while ( strlen ( $par ) > 500 ) {
                                            array_push ( $pars, substr ( $par, 0, 500 ) );  # chop chop
                                            $par = substr ( $par, 500, strlen ( $par ) - 500 );
                                        }
                                        array_push ( $pars, $par );
                                    }
                                } else
                                    $pars = array ( $par );
                                foreach ( $pars as $p ) {
                                    foreach ( $words as $w ) {                      # mark words of $words in $par
                                        if ( preg_match ( "/\b".preg_quote ( $w, "/" )."\b/i", $p ) ) {
                                            $y .= "...<br>..." . searchLineDisplay( "$p\n", $words ) ;
                                            $foundpar = 1;
                                            break 3;
                                        }
                                    }
                                }
                            }
                            for ( $z = $realcnt ; strlen ( $z ) < strlen ( $allSearch ) ; $z = "0$z" ) ;
                            $ct = $vpage->getNiceTitle ( $row->cur_title ) ;
                            $s .= "<tr><td valign=top width=20 align=right><b>$z</b></td><td><font face=\"Helvetica,Arial\">'''[[$ct]]'''</font><br>" ;
                            $s .= $y ;
                            $s .= "</td></tr>" ;
                            $realcnt++ ;
                        }
                        mysql_free_result ( $result ) ;                
                    }
                }
                $s .= "</table>" ;
                
                # present links to other parts of the same search
                
                if ( $allSearch > $perpage ) {
                    $s .= "<nowiki>" ;
                    $last = $startat-$perpage ;
                    $next = $startat+$perpage ;
                    $resultSizes = "&allSearch=$allSearch&titleSearch=$titleSearch";
                    if ( $startat != 1 )
                        $s .= "<a href=\"".wikiLink("&search=$search&startat=$last$resulSizes")."\">&lt;&lt;</a> | ";
                    for ( $a = 1 ; $a <= $allSearch ; $a += $perpage ) {
                        if ( $a != 1 ) $s .= " | " ;
                        if ( $a != $startat ) $s .= "<a href=\"".wikiLink("&search=$search&startat=$a$resultSizes")."\">";
                        $s .= "$a-" ;
                        $s .= min( $a+$perpage-1, $allSearch ) ;
                        if ( $a != $startat ) $s .= "</a>" ;
                    }
                    if ( $startat != $a-$perpage )
                        $s .= " | <a href=\"".wikiLink("&search=$search&startat=".$next.$resultSizes)."\">&gt;&gt;</a>";
                    $s .= "</nowiki>" ;
                }
            }
        }
    }
    $vpage->contents = $s ;
    return $vpage->renderPage () ;
}

?>
