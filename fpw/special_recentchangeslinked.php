<?

include_once ( "special_recentchanges.php" ) ;

function recentChangesLinked ( ) {
    global $target ;
    global $THESCRIPT, $vpage ;
    global $wikiRecentChangesLinkedTitle ;

    $vpage = new WikiPage ;
    $vpage->title = $title ;
    $vpage->makeSecureTitle () ;
    $niceTarget = $vpage->getNiceTitle ( $target ) ;
    $vpage->special ( str_replace ( "$1" , $niceTarget , $wikiRecentChangesLinkedTitle ) ) ;
    $vpage->makeSecureTitle () ;

    $arr = array () ;

    $connection=getDBconnection() ;

    # the SQL query that retrieves all information
    $sql = "SELECT cur_timestamp, cur_title, cur_comment, cur_user,
                    cur_user_text, cur_minor_edit
            FROM linked, cur
            WHERE linked_from = \"$target\" AND linked_to = cur_title
            GROUP BY cur_title
            ORDER BY cur_timestamp DESC " ;

    # store result in $arr
    $result = mysql_query ( $sql , $connection ) ;
    while ( $s = mysql_fetch_object ( $result ) ) array_push ( $arr , $s ) ;
    mysql_free_result ( $result ) ;

    return recentChangesLayout ( $arr ) ;
}

?>
