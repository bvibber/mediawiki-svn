<?
include_once ( "special_makelog.php" ) ;

function removeFromLinkList ( $item , $link ) {
    $connection = getDBconnection () ;
    $sql = "SELECT cur_id FROM cur WHERE $item LIKE \"%$link%\"" ;
    $result = mysql_query ( $sql , $connection ) ;
    $ids = array () ;
    while ( $s = mysql_fetch_object ( $result ) ) array_push ( $ids , $s->cur_id ) ;
    mysql_free_result ( $result ) ;

    foreach ( $ids as $x ) {
        $sql = "SELECT cur_timestamp,$item FROM cur WHERE cur_id=$x" ;
        $result = mysql_query ( $sql , $connection ) ;
        $s = mysql_fetch_object ( $result )  ;
        mysql_free_result ( $result ) ;
        $y = explode ( "\n" , $s->$item ) ;
        $z = array () ;
        foreach ( $y as $u ) {
            if ( $u != $link )
                array_push ( $z , $u ) ;
            }
        $y = implode ( "\n" , $z ) ;
        $sql = "UPDATE cur SET cur_timestamp=\"$s->cur_timestamp\",$item=\"$y\" WHERE cur_id=$x" ;
        $result = mysql_query ( $sql , $connection ) ;
        }

    }

function deletepage () {
    global $THESCRIPT , $target , $user , $iamsure ;
    global $vpage , $wikiSQLServer ;
    $target = str_replace ( "\\\\" , "\\" , $target ) ;
    $target = str_replace ( "\\\\" , "\\" , $target ) ;
    $vpage = new WikiPage ;
    $vpage->title = $title ;
    $vpage->makeSecureTitle () ;
    $ti = $vpage->secureTitle ;

    global $wikiDeleteTitle , $wikiDeleteDenied , $wikiDeleteSuccess , $wikiDeleteMsg1 , $wikiDeleteMsg2 , $wikiDeleteAsk ;
    $vpage->special ( str_replace ( "$1" , $target , $wikiDeleteTitle ) ) ;
    $vpage->makeSecureTitle () ;
    if ( !in_array ( "is_sysop" , $user->rights ) ) return $wikiDeleteDenied ;
    if ( $iamsure == "yes" ) {
        $ret = "<h2>".str_replace("$1",$target,$wikiDeleteSuccess)."</h2>" ;
        $connection = getDBconnection () ;
        $sql = "DELETE FROM cur WHERE cur_title=\"$target\"" ;
        $result = mysql_query ( $sql , $connection ) ;

        # Appending log page "log:Page Deletions"
        $now = date ( "Y-m-d H:i:s" , time () ) ;
        $logTarget = $vpage->getNiceTitle ( $target ) ;
        $logText = str_replace("$1",$now,str_replace("$2",$user->name,str_replace("$3",$logTarget,$wikiDeleteMsg1))) ;
        makeLog ( "log:Page Deletions" , $logText , str_replace("$1",$logTarget,$wikiDeleteMsg2)) ;

        removeFromLinkList ( "cur_linked_links" , $target ) ;
        removeFromLinkList ( "cur_unlinked_links" , $target ) ; # !! this is strange, if a page is removed it shouldn't be in any unlinked_links array, but if it is then it might as well stay, right? JH
        
        # links to $target are moved from table linked to table unlinked
        $sql = "INSERT INTO unlinked ( unlinked_from, unlinked_to )
                SELECT linked_from, linked_to
                FROM linked
                WHERE linked_to = \"$target\"" ;
        mysql_query ( $sql , $connection ) ;
        $sql = "DELETE FROM linked WHERE linked_to = \"$target\"" ;
        mysql_query ( $sql , $connection ) ;
        # and links from $target are removed
        $sql = "DELETE FROM unlinked WHERE unlinked_from = \"$target\"" ;
        mysql_query ( $sql , $connection ) ;
        $sql = "DELETE FROM linked WHERE linked_from = \"$target\"" ;
        mysql_query ( $sql , $connection ) ;

    } else {
        $ret = "<font size=\"+2\">".str_replace(array("$1","$2"),array($target,wikiLink("special:deletepage&target=$target")),$wikiDeleteAsk)."</font>" ;
        }
    return "<nowiki>$ret</nowiki>" ;
    }

?>
