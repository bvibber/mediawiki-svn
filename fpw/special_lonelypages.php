<?

function LonelyPages () {
    global $THESCRIPT ;
    global $linkedLinks , $unlinkedLinks , $vpage ;
    global $wikiLonelyPagesTitle , $wikiLonelyPagesText , $wikiTalk , $wikiUser ;
    
    $vpage->special ( $wikiLonelyPagesTitle ) ;
    $vpage->namespace = "" ;
    $allPages = array () ;
    $ret = $wikiLonelyPagesText ;

    $connection = getDBconnection () ;
    $sql1 = "SELECT cur_title
            FROM cur
            WHERE cur_title NOT LIKE \"".ucfirstIntl($wikiUser).":%\"
              AND cur_title NOT LIKE \"%" . substr($wikiTalk,2) . "%:%\"
              AND cur_text NOT LIKE \"#redirect%\"
              AND cur_text != \"\"
            ORDER BY cur_title " ;
    $result1 = mysql_query ( $sql1 , $connection ) ;
    
    $sql2 = "SELECT DISTINCT linked_to
            FROM linked
            ORDER BY linked_to " ;
    $result2 = mysql_query ( $sql2 , $connection ) ;
    
    # now we "merge" the two results while removing from the first list the ones in the second
    $s1 = mysql_fetch_object ( $result1 ) ;
    $s2 = mysql_fetch_object ( $result2 ) ;
    while ( $s1 and $s2 ) {
        if ( $s1->cur_title < $s2->linked_to ) {
            $ret .= "# [[$s1->cur_title|".$vpage->getNiceTitle($s1->cur_title)."]]<br>\n" ;
            $s1 = mysql_fetch_object ( $result1 ) ;
        } elseif ( $s1->cur_title > $s2->linked_to ) {
            $s2 = mysql_fetch_object ( $result2 ) ;
        } else {
            $s1 = mysql_fetch_object ( $result1 ) ;
            $s2 = mysql_fetch_object ( $result2 ) ;
        }
    }
    while ( $s1 ) {
        $ret .= "# [[$s1->cur_title|".$vpage->getNiceTitle($s1->cur_title)."]]<br>\n" ;
        $s1 = mysql_fetch_object ( $result1 ) ;
    }

    return $ret ;
}
?>
