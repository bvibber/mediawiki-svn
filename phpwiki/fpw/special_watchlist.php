<?
include_once ( "special_recentchanges.php" ) ;

function modifyArray ( $a , $sep , $rem , $add = "" ) {
    $b = explode ( $sep , $a ) ;
    $c = array () ;
    foreach ( $b as $x ) {
        if ( $x != "" and $x != $rem )
            array_push ( $c , $x ) ;
        }
    if ( $add != "" ) array_push ( $c , $add ) ;
    return implode ( $sep , $c ) ;
    }

function watch ( $t , $m ) {
    global $THESCRIPT , $user , $wikiUserSettingsError , $wikiWatchYes , $wikiWatchNo ;
    global $vpage;
    
    if ( !$user->isLoggedIn ) return $wikiUserSettingsError ;

    # Watchlist is namespace-independant
    $t = preg_replace ( "/^(.*:)/" , "" , $t ) ;

    # Modifying user_watch
    $separator = "\n" ;
    $a = getMySQL ( "user" , "user_watch" , "user_id=$user->id" ) ;
    if ( $m == "yes" ) $a = modifyArray ( $a , $separator , $t , $t ) ;
    else $a = modifyArray ( $a , $separator , $t ) ;
    setMySQL ( "user" , "user_watch" , $a , "user_id=$user->id" ) ;

    if ( $m == "yes" ) $ret = str_replace ( "$1" , $t , $wikiWatchYes ) ;
    else str_replace ( "$1" , $t , $wikiWatchNo ) ;
    $ret .= "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL='".wikiLink(nurlencode($t))."'\">" ;
    return $ret ;
    }

function WatchList () {
    global $THESCRIPT ;
    global $vpage , $user , $wikiWatchlistTitle , $wikiWatchlistExistText , $wikiWatchlistNotExistText;

    $vpage->special ( $wikiWatchlistTitle ) ;
    $ret = "$wikiWatchlistExistText\n\n" ;
    $a = getMySQL ( "user" , "user_watch" , "user_id=$user->id" ) ;
    $separator = "\n" ;
    $b = explode ( $separator , $a ) ;
    $vpage->namespace = "" ;

    $n = array () ;
    foreach ( $b as $x )
        $n[$x] = getMySQL ( "cur" , "cur_timestamp" , "cur_title=\"$x\"" ) ;
    arsort ( $n ) ;
    $k = array_keys ( $n ) ;

    $connection=getDBconnection() ;
    $arr = array () ;
    $any = false ;
    $notexist = "" ;
    foreach ( $k as $x ) {
        if ( $x != "" ) {
            $sql = "SELECT cur_timestamp, cur_title, cur_comment, cur_user, cur_user_text, cur_minor_edit
                    FROM cur
                    WHERE cur_title=\"$x\" OR cur_title LIKE \"%:$x\"" ;
            $result = mysql_query ( $sql , $connection ) ;
            $s = mysql_fetch_object ( $result ) ;
            if ( $s )
                while ($s) {
		    array_push ( $arr , $s ) ;    # don't push if page no longer exists
		    $s = mysql_fetch_object ( $result ) ;
		    }
            else
                $notexist .= "\n* [[".$vpage->getNiceTitle( $x )."]]" ;
            mysql_free_result ( $result ) ;
            $any = true ;
            }
        }
    if ( $any )
        $ret .= recentChangesLayout ( $arr ) ;
        
    if ( $notexist )
        $ret .= "\n----\n$wikiWatchlistNotExistText\n\n$notexist" ;

    return $ret ;
    }
?>
