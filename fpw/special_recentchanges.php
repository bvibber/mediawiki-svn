<?
include_once ( "special_recentchangeslayout.php" ) ;

function recentchanges () {
    global $THESCRIPT , $user , $useCachedPages ;
    global $vpage , $maxcnt , $daysAgo , $from , $wikiRecentChangesText , $wikiRecentChangesTitle ;
    global $wikiRecentChangesLastDays , $wikiRecentChangesSince , $wikiViewLastDays , $wikiViewMaxNum , $wikiListOnlyNewChanges ;
    $vpage->special ( $wikiRecentChangesTitle ) ;
    $vpage->makeSecureTitle() ;
    if ( !isset ( $maxcnt ) ) $maxcnt = 50 ;
    if ( !isset ( $daysAgo ) ) $daysAgo = 3 ;
    
    $from2 = substr ( $from , 0 , 4 ) . "-" . substr ( $from , 4 , 2 ) . "-" . substr ( $from , 6 , 2 ) ;
    $from2 .= " " . substr ( $from , 8 , 2 ) . ":" . substr ( $from , 10 , 2 ) . ":" . substr ( $from , 12 , 2 ) ;

    $ret = "" ;
    if ( $wikiRecentChangesText != "" ) $ret .= "$wikiRecentChangesText<br><br>" ;

    $ret .= "<nowiki>" ;
    if ( !isset($from) ) $ret .= str_replace ( "$1" , $maxcnt , str_replace ( "$2" , $daysAgo , $wikiRecentChangesLastDays ) ) ;
    else $ret .= str_replace ( "$1" , $maxcnt , str_replace ( "$2" , $from2 , $wikiRecentChangesSince ) ) ;

    $ret .= "<br>\n" ;
    $n = explode ( "$1" , $wikiViewMaxNum ) ;
    $ret .= $n[0] ;
    $ret .= "<a href=\"".wikiLink("special:RecentChanges&daysAgo=$daysAgo&maxcnt=50")."\">50</a> | " ;
    $ret .= "<a href=\"".wikiLink("special:RecentChanges&daysAgo=$daysAgo&maxcnt=100")."\">100</a> | " ;
    $ret .= "<a href=\"".wikiLink("special:RecentChanges&daysAgo=$daysAgo&maxcnt=250")."\">250</a> | " ;
    $ret .= "<a href=\"".wikiLink("special:RecentChanges&daysAgo=$daysAgo&maxcnt=500")."\">500</a> | " ;
    $ret .= "<a href=\"".wikiLink("special:RecentChanges&daysAgo=$daysAgo&maxcnt=1000")."\">1000</a> | " ;
    $ret .= "<a href=\"".wikiLink("special:RecentChanges&daysAgo=$daysAgo&maxcnt=2500")."\">2500</a> | " ;
    $ret .= "<a href=\"".wikiLink("special:RecentChanges&daysAgo=$daysAgo&maxcnt=5000")."\">5000</a> " ;
    $ret .= $n[1]."; \n" ;
    $n = explode ( "$1" , $wikiViewLastDays ) ;
    $ret .= $n[0] ;
    $ret .= "<a href=\"".wikiLink("special:RecentChanges&maxcnt=$maxcnt&daysAgo=1")."\">1 </a> | " ;
    $ret .= "<a href=\"".wikiLink("special:RecentChanges&maxcnt=$maxcnt&daysAgo=2")."\">2 </a> | " ;
    $ret .= "<a href=\"".wikiLink("special:RecentChanges&maxcnt=$maxcnt&daysAgo=3")."\">3 </a> | " ;
    $ret .= "<a href=\"".wikiLink("special:RecentChanges&maxcnt=$maxcnt&daysAgo=5")."\">5 </a> | " ;
    $ret .= "<a href=\"".wikiLink("special:RecentChanges&maxcnt=$maxcnt&daysAgo=7")."\">7 </a> | " ;
    $ret .= "<a href=\"".wikiLink("special:RecentChanges&maxcnt=$maxcnt&daysAgo=14")."\">14 </a> ".$n[1]."; \n" ;

    $mindate = date ( "Ymd000000" , time () - $daysAgo*24*60*60 ) ;
    $mindate = timestampAddHour ( $mindate , $user->options["hourDiff"] ) ;

    $now = date ( "YmdHis" , time() ) ;
    $now = timestampAddHour ( $now , $user->options["hourDiff"] ) ;

    $ret .= "<a href=\"".wikiLink("special:RecentChanges&from=$now")."\">$wikiListOnlyNewChanges</a>" ;
    $ret .= "</nowiki>" ;
    $ret .= "\n----\n" ;
    $arr = array () ;

    global $wikiSQLServer ;
    $connection=getDBconnection() ;
    mysql_select_db ( $wikiSQLServer , $connection ) ;

    if ( $user->options["hideMinor"] != "yes" ) {
    
      # here starts the code for retrieving the recent changes information
      # for the major _and_ the minor edits

      # optional parts of the query
      $fromCondOld = ( isset($from) ) ? "AND old_timestamp > $from" : "";
      $fromCondCur = ( isset($from) ) ? "AND cur_timestamp > $from" : "";
            
      # the SQL query that retrieves all information
      $sql = "SELECT cur_timestamp, cur_title, cur_comment, cur_user,
                     cur_user_text, cur_minor_edit,
                     COUNT(old_id) + (IF(cur_old_version = 0,0,1)) AS changes
              FROM cur LEFT OUTER JOIN old ON cur_title = old_title
                AND old_timestamp > SUBDATE(CURRENT_TIMESTAMP, INTERVAL $daysAgo DAY)
                AND old_old_version <> 0
                $fromCondOld
              WHERE cur_timestamp > SUBDATE(CURRENT_TIMESTAMP, INTERVAL $daysAgo DAY)
                $fromCondCur              
              GROUP BY cur_title
              ORDER BY cur_timestamp DESC
              LIMIT $maxcnt" ;

      # store result in $arr
      $result = mysql_query ( $sql , $connection ) ;
      while ( $s = mysql_fetch_object ( $result ) ) array_push ( $arr , $s ) ;
      mysql_free_result ( $result ) ;
        
    } else {

      # here starts the code for retreiving the recent changes information
      # without the minor edits

      # optional parts of the query
      $fromCondCur = ( isset($from) ) ? "AND cur_timestamp > $from" : "";
            
      # the SQL query that retrieves the current pages that are not the result of a minor edit
      $sql1 = "SELECT cur_timestamp, cur_title, cur_comment, cur_user,
                     cur_user_text, cur_minor_edit
              FROM cur
              WHERE cur_timestamp > SUBDATE(CURRENT_TIMESTAMP, INTERVAL $daysAgo DAY)
                AND cur_minor_edit != 1
                $fromCondCur              
              ORDER BY cur_title
              LIMIT $maxcnt" ;

      # store result in $arr1
      $arr1 = array () ;
      $result = mysql_query ( $sql1 , $connection ) ;
      while ( $s = mysql_fetch_object ( $result ) ) array_push ( $arr1 , $s ) ;
      mysql_free_result ( $result ) ;

      # optional parts of the query
      $fromCondOld1 = ( isset($from) ) ? "AND o1.old_timestamp > $from" : "";
      $fromCondOld2 = ( isset($from) ) ? "AND o2.old_timestamp > $from" : "";

      # the SQL query that retrieves the old pages that are not the result of a minor edit
      # and groups them
      $sql2 = "SELECT o1.old_timestamp AS cur_timestamp, o1.old_title AS cur_title,
                      o1.old_comment AS cur_comment, o1.old_user AS cur_user,
                      o1.old_user_text AS cur_user_text, o1.old_minor_edit,
                      COUNT(DISTINCT o2.old_id ) AS changes
               FROM old AS o1 LEFT OUTER JOIN old AS o2
                    ON o1.old_title = o2.old_title
                       AND o2.old_timestamp > SUBDATE(CURRENT_TIMESTAMP, INTERVAL 3 DAY)
                       AND o2.old_old_version <> 0 AND o2.old_minor_edit != 1
                       $fromCondOld2                       
               WHERE o1.old_timestamp > SUBDATE(CURRENT_TIMESTAMP, INTERVAL 3 DAY)
                     AND o1.old_minor_edit != 1
                     $fromCondOld1
               GROUP BY o1.old_title, o1.old_timestamp
               HAVING cur_timestamp = MAX(o2.old_timestamp)
               ORDER BY o1.old_title
               LIMIT $maxcnt";
               
      # store result in $arr2
      $arr2 = array () ;
      $result = mysql_query ( $sql2 , $connection ) ;
      while ( $s = mysql_fetch_object ( $result ) ) array_push ( $arr2 , $s ) ;
      mysql_free_result ( $result ) ;

      # Now we merge the two results.
      # Evidently this is better done by the database,
      # but MySQL doesn't implement enough SQL for that.
      foreach ( $arr1 as $row1 ) {
        while ( $arr2 and ( $arr2[0]->cur_title < $row1->cur_title ) )
          array_push ( $arr, array_shift ( $arr2 ) );        
        $extra = ($row1->cur_minor_edit == 2) ? 0 : 1;
        if ( $arr2 and ( $arr2[0]->cur_title == $row1->cur_title ) ) {
          $arr2[0]->changes += $extra;
          array_push ( $arr, array_shift ( $arr2 ) );
        } else {
          $row1->changes = $extra;
          array_push ( $arr, $row1 );
        }
      }
      $arr = array_merge ($arr, $arr2);
      
      # Now we sort the result on the timestamp
      function cmp ($a, $b) {
        $x = $a->cur_timestamp;
        $y = $b->cur_timestamp;
        if ( $x == $y ) return 0;
        return ($x > $y) ? -1 : 1;
      }

      usort ($arr, "cmp");
    }

    #mysql_close ( $connection ) ;
    $ret .= recentChangesLayout($arr) ;
    
    return $ret ;
   }

?>
