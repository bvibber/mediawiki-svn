<?
include_once ( "special_recentchangeslayout.php" ) ;

function doHistory ( $title ) {
    global $THESCRIPT , $vpage , $wikiHistoryTitle , $wikiCurrentVersion , $wikiHistoryHeader , $wikiBlockIP ;
    $vpage = new WikiPage ;
    $vpage->SetTitle ( $title ) ;
    $ti = $vpage->secureTitle ;
    $url = $vpage->url;
    $vpage->special ( str_replace ( "$1" , $title , $wikiHistoryTitle ) ) ;
    $vpage->makeSecureTitle () ;

    $a = array () ;
    $connection = getDBconnection () ;
    
    # first we get the current record
    $sql1 = "SELECT cur_timestamp, cur_title, cur_comment, cur_user,
                     cur_user_text, cur_minor_edit
             FROM cur
             WHERE cur_title=\"$ti\"" ;
    $result = mysql_query ( $sql1 , $connection ) ;
    $s = mysql_fetch_object ( $result ) ;
    array_push ( $a , $s ) ;
    mysql_free_result ( $result ) ;
    
    # then we get the old records
    $sql2 = "SELECT old_timestamp AS cur_timestamp, old_title AS cur_title, old_comment AS cur_comment,
                    old_user AS cur_user, old_user_text AS cur_user_text, old_minor_edit AS cur_minor_edit,
                    old_id
             FROM old
             WHERE old_title=\"$ti\"
             ORDER BY cur_timestamp DESC" ;
    $result = mysql_query ( $sql2 , $connection ) ;
    while ( $s = mysql_fetch_object ( $result ) ) array_push ( $a , $s ) ;
    mysql_free_result ( $result ) ;

    # now we number the versions
    $version = 0;
    foreach ( array_keys ( $a ) as $i ) {
      if ( $version > 0 ) $a[$i]->version = $version;
      if ( $a[$i]->cur_used == 0 ) $a[$i]->appendix = " (<a href=\"".wikiLink("special:blockIP&target=".$a[$i]->cur_user_text)."\">$wikiBlockIP</a>)" ; # This will show for sysops only!
      $version++;
    }


    $t = recentChangesLayout ( $a ) ;
    $t = "<b>".str_replace(array("$1","$2"),array($url,$title),$wikiHistoryHeader)."</b>".$t ;

    $ret = $vpage->getHeader() ;
    $ret .= $vpage->getMiddle($t) ;
    $ret .= $vpage->getFooter() ;
    return $ret ;
    }

?>
