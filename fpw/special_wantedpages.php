<?

function WantedPages () {
    global $vpage , $wikiWantedText , $wikiWantedLine , $wikiWantedTitle ;
    global $wikiGetDate ;

    $vpage->special ( $wikiWantedTitle ) ;
    $vpage->namespace = "" ;

    $ret = $wikiWantedText ;

    $connection = getDBconnection () ;
    $sql = "SELECT unlinked_to, COUNT( unlinked_from ) AS ulf
            FROM unlinked
            GROUP BY unlinked_to
            ORDER BY ulf DESC
            LIMIT 50 " ;

    $result = mysql_query ( $sql , $connection ) ;
    
    $ti = new wikiTitle ;
    while ( $s = mysql_fetch_object ( $result ) ) {
        $n = str_replace ( "$1" , "[[$s->unlinked_to|".$ti->getNiceTitle($s->unlinked_to)."]]" , $wikiWantedLine ) ;
        $n = str_replace ( "$2" , $s->ulf , $n ) ;
        $n = str_replace ( "$3" , wikiLink("special:whatlinkshere&target=".nurlencode($s->unlinked_to)) , $n ) ;
        $n = str_replace ( "$4" , $ti->getNiceTitle($s->unlinked_to) , $n ) ;
        $ret .= "*$n\n" ;
    }                
            
    $now = time () ;
    $lc = $wikiGetDate ( $now ) . date ( ", H:i" , $now ) ;
    #$lc .= ", ".substr ( $now , 8 , 2 ) ;
    #$lc .= ":".substr ( $now , 10 , 2 ) ;
    $ret .= "\n<p>" . str_replace ( "$1", $lc, $wikiLastRefreshed ) . "</p>\n" ;

    return $ret ;
}

?>
