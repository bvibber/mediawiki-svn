<?
include_once ( "special_makelog.php" ) ;

# The following function should be localized because it is languages specific
# It splits a string into an array of separate words as I believe
# the MATCH operator in MySQL also does. Right now it select words
# consisting of readable ISO-8895-1 characters

function allWords ( $str ) {
  preg_match_all ( "/([0-9a-zA-Z\xCO-\xFF]+)/", $str, $m );
  return $m[0];
}

function searchLineDisplay ( $v , $words) {
    $v = trim(str_replace("\n","",$v)) ;
    $v = str_replace ( "'''" , "" , $v ) ;
    $v = str_replace ( "''" , "" , $v ) ;
    $v = ereg_replace ( "\{\{\{.*\}\}\}" , "?" , $v ) ;
    $v = trim ( $v ) ;
    while ( substr($v,0,1) == ":" ) $v = substr($v,1) ;
    while ( substr($v,0,1) == "*" ) $v = substr($v,1) ;
    while ( substr($v,0,1) == "#" ) $v = substr($v,1) ;
    foreach ( $words as $w ) {
        #$v = eregi_replace ( $w , "'''".$w."'''" , $v ) ;
        $v = eregi_replace ( "(^|\])([^\[]+)($w)", "\\1\\2<b>\\3</b>", $v ) ; # highlight search terms NOT in [[links]]
        $v = eregi_replace ( "(\[+[^\]]*)($w)([^\[]*\]+)" , "<b>\\1\\2\\3</b>" , $v ) ; # highlight entire links that contain name. Ugly but works for now
    }

    $v = "<font size=-1>$v</font>" ;
    return $v ;
}

function doSearch () {
    global $THESCRIPT ;
    global $vpage , $search , $startat , $user , $wikiRecodeInput ;
    global $wikiSearchTitle , $wikiSearchedVoid , $wikiNoSearchResult ;
    global $allSearch ;     # contains total size of the search result
    global $titleSearch ;     # contains size of result of query on titles

    $vpage = new WikiPage ;
    $vpage->special ( $wikiSearchTitle ) ;

    $r = array () ;
    $s = "" ;

    if ( $search == "" )
        $s = $wikiSearchedVoid ;
    else {
        $search = $wikiRecodeInput ( $search ) ;
        if ( !isset ( $startat ) ) $startat = 1 ;
        $perpage = $user->options["resultsPerPage"] ;
        global $wikiSQLServer ;
        $connection = getDBconnection () ;
        mysql_select_db ( $wikiSQLServer , $connection ) ;
        
        # first we establish the total size of the result
        # if it wasn't already established
        
        if ( !isset ( $allSearch ) ) {
        
            $sql1 = "SELECT COUNT(*) AS cnt
                    FROM cur
                    WHERE MATCH (cur_ind_title) AGAINST (\"$search\")
                      AND cur_title NOT LIKE \"%:%\"";
                
            $result = mysql_query ( $sql1 , $connection ) ;
            $row = mysql_fetch_object ( $result );
            $titleSearch = $row->cnt ;
            mysql_free_result ( $result ) ;
        
            $sql2 = "SELECT COUNT(*) AS cnt
                    FROM cur
                    WHERE MATCH (cur_text) AGAINST (\"$search\") AND
                      NOT MATCH (cur_ind_title) AGAINST (\"$search\")
                      AND cur_title NOT LIKE \"%:%\"";

            $result = mysql_query ( $sql2 , $connection ) ;
            $row = mysql_fetch_object ( $result );
            $allSearch = $titleSearch + $row->cnt ;
            mysql_free_result ( $result ) ;
        }
        
        # Now we proced with presenting the found results

        if ( $allSearch == 0 ) {
            
            # nothing found
        
            global $wikiUnsuccessfulSearch , $wikiUnsuccessfulSearches ;
            $s = "<h2>".str_replace("$1",$search,$wikiNoSearchResult)."</h2>" ;
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
            # We don't bother about which queries we actually have
            # to ask, but we let the LIMIT clause sort this out.

            $offset1 = $startat - 1;
            $sql1 = "SELECT cur_title, cur_text
                    FROM cur
                    WHERE MATCH (cur_ind_title) AGAINST (\"$search\")
                      AND cur_title NOT LIKE \"%:%\"
                    LIMIT $offset1, $perpage" ;

            $result1 = mysql_query ( $sql1 , $connection );

            $offset2 = max ( $startat - $titleSearch - 1, 1 );
            $limit2 = max ( $perpage - max( $titleSearch - $startat, 0 ) , 0 ); 
            $sql2 = "SELECT cur_title, cur_text
                    FROM cur
                    WHERE MATCH (cur_text) AGAINST (\"$search\") AND
                      NOT MATCH (cur_ind_title) AGAINST (\"$search\")
                      AND cur_title NOT LIKE \"%:%\"
                    LIMIT $offset2, $limit2";
                    
            $result2 = mysql_query ( $sql2 , $connection );
            
            # to save memory (cur_text can be really big) we do no collect
            # all results in an array, but process them one by one
     
            # presentation of the found pages
            
            $s .= "<table>" ;
            $realcnt = $startat;
            $words = allWords ( $search );                              # split string into separate words
            foreach ( array ($result1, $result2) as $result ) {
                while ( $row = mysql_fetch_object ( $result ) ) {
                    $ct = preg_split ( "/(\\n)|<br>|<BR>/", $row->cur_text, 100 ) ;        # let's see the first 100 paragraphs
                    $y = searchLineDisplay( array_shift( $ct ), $words ) ;
                    $foundpar = false;                    
                    foreach ( $ct as $par ) {
                        foreach ( $words as $w ) {                      # mark words of $words in $par
                            if ( stristr( $par, $w ) and strlen( $par ) < 100000 ) {    # prevent choking on absurd paragraphs
                                $y .= "...<br>..." . searchLineDisplay( "$par\n", $words ) ;
                                $foundpar = 1;
                                break ;
                            }
                        }
                        if ( $foundpar ) break;
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

    $vpage->contents = $s ;
    return $vpage->renderPage () ;
}

?>
