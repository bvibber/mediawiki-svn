<?
function whatLinksHere () {
    global $THESCRIPT , $target , $user , $protecting , $newrestrictions ;
    global $vpage , $target , $wikiLinkhereTitle ;
    global $wikiLinkhereBacklink , $wikiLinkhereNoBacklink , $wikiBacklinkNolink , $wikiBacklinkFollowing ;
    
    $vpage = new WikiPage ;
    $vpage->title = $title ;
    $vpage->makeSecureTitle () ;
    $ti = $vpage->secureTitle ;
    $niceTarget = $vpage->getNiceTitle ( $target ) ;
    $vpage->special ( str_replace ( "$1" , $niceTarget , $wikiLinkhereTitle ) ) ;
    $vpage->makeSecureTitle () ;

    $connection = getDBconnection () ;
    # first we get all pages linked to $target
    $sql1 = "SELECT DISTINCT linked_from AS lfrom
            FROM linked
            WHERE linked_to = \"$target\"
            ORDER BY lfrom " ;
    $result1 = mysql_query ( $sql1 , $connection ) ;

    # we also get all pages that have an unlinked link to $target
    # Note that at least one of these two queries is empty
    $sql2 = "SELECT DISTINCT unlinked_from AS lfrom
            FROM unlinked
            WHERE unlinked_to = \"$target\"
            ORDER BY lfrom " ;
    $result2 = mysql_query ( $sql2 , $connection ) ;
    
    # then we get all pages linked from $target
    $sql3 = "SELECT DISTINCT linked_to
            FROM linked
            WHERE linked_from = \"$target\"
            ORDER BY linked_to " ;
    $result3 = mysql_query ( $sql3 , $connection ) ;
    
    $dlb = "" ;     # the pages that $target links back to
    $dnlb = "" ;    # the pages that $target does not link back to
    
    # we find out if $target has links
    $s3 = mysql_fetch_object ( $result3 ) ;
    if ( $s3 ) {    # yes, so we take $result1 and $result2 and split it into $dlb and $ndlb
        foreach ( array ( $result1, $result2 ) as $aresult ) {
            while ( $s1 =  mysql_fetch_object ( $aresult ) ) {
                $nt = $vpage->getNiceTitle ( $s1->lfrom ) ;
                while ( $s3 and $s3->linked_to < $s1->lfrom )
                    $s3 = mysql_fetch_object ( $result3 ) ;
                if ( $s3 )
                    if ( $s3->linked_to > $s1->lfrom )
                        $dnlb .= "\n* [[$nt]]" ;
                    else
                        $dlb .= "\n* [[$nt]]" ;
                else
                    $dnlb .= "\n* [[$nt]]" ;
            }
        }
    } else {        # no, so we take $result1 and $result2 and put it in $dnlb
        foreach ( array ( $result1, $result2 ) as $aresult ) {
            while ( $s1 =  mysql_fetch_object ( $aresult ) ) {
                $nt = $vpage->getNiceTitle ( $s1->lfrom ) ;
                $dnlb .= "\n* [[$nt]]" ;
            }
            
        }    
    }
    mysql_free_result ( $result1 ) ;
    mysql_free_result ( $result2 ) ;
    mysql_free_result ( $result3 ) ;
    
    if ( $dlb ) $dlb = "<h3>".str_replace("$1",$niceTarget,$wikiLinkhereBacklink)."</h3>$dlb\n" ;
    if ( $dnlb ) $dnlb = "<h3>".str_replace("$1",$niceTarget,$wikiLinkhereNoBacklink)."</h3>$dnlb\n" ;

    $ret = $dnlb.$dlb ;
    if ( $ret == "" ) $ret = "<h1>".str_replace("$1",$niceTarget,$wikiBacklinkNolink)."</h1>" ;
    else $ret = "<h1>".str_replace("$1",$niceTarget,$wikiBacklinkFollowing)."</h1>\n$ret" ;

    return $ret ;
}

?>
