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
    global $vpage , $returnto ;
    
    if ( !$user->isLoggedIn ) return $wikiUserSettingsError ;

    # Watchlist is namespace-independant
    $tbare = preg_replace ( "/^(.*:)/" , "" , $t ) ;

    # Modifying user_watch
    $separator = "\n" ;
    $a = getMySQL ( "user" , "user_watch" , "user_id=$user->id" ) ;
    if ( $m == "yes" ) $a = modifyArray ( $a , $separator , $tbare , $tbare ) ;
    else {
    	$a = modifyArray ( $a , $separator , $t ) ; # For older watchlists which may contain specific entries
    	$a = modifyArray ( $a , $separator , $tbare ) ;
    }
    setMySQL ( "user" , "user_watch" , $a , "user_id=$user->id" ) ;

    if ( $m == "yes" ) $ret = str_replace ( "$1" , $tbare , $wikiWatchYes ) ;
    else str_replace ( "$1" , $tbare , $wikiWatchNo ) ;
    if ( isset ( $returnto ) )
    	$returnto = "special:WatchList" ;
    else
    	$returnto = nurlencode ( $t ) ;
    $ret .= "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL='".wikiLink($returnto)."'\">" ;
    return $ret ;
    }

function WatchList () {
    global $THESCRIPT ;
    global $vpage , $user , $wikiWatchlistTitle , $wikiWatchlistExistText , $wikiWatchlistNotExistText, $wikiTalk ;
    global $wikiAllowedNamespaces , $wikiErrorPageTitle , $wikiErrorMessage , $wikiNoWatch ;

    $vpage->special ( $wikiWatchlistTitle ) ;
    $ret = "$wikiWatchlistExistText\n\n" ;
    $a = getMySQL ( "user" , "user_watch" , "user_id=$user->id" ) ;
    $separator = "\n" ;
    $b = explode ( $separator , $a ) ;
    $vpage->namespace = "" ;

/*  # The database can sort things by date!!
    $n = array () ;
    foreach ( $b as $x )
        $n[$x] = getMySQL ( "cur" , "cur_timestamp" , "cur_title=\"$x\"" ) ;
    arsort ( $n ) ;
    $k = array_keys ( $n ) ;
*/

    $connection=getDBconnection() ;
    $arr = array () ;
    $foundtitles = array () ;
    $any = false ;
    $notexist = "" ;
    $talk = ucfirstIntl ( $wikiTalk ) ;
    
    # Dirty hack to make all namespaces work until we have separate namespace and subtitle fields in the database
    $watchedtitles = array () ;
    foreach ( $wikiAllowedNamespaces as $namespace ) {
    	if ( $ns = $namespace ) $ns = ucfirstIntl ( $namespace ) . ":" ;
    	foreach ( $b as $title ) {
	    array_push ( $watchedtitles , $ns . $title ) ;
	    }
    	}
    
    # Get existing pages...
    $sql = "SELECT cur_timestamp, cur_title, cur_comment, cur_user, cur_user_text, cur_minor_edit
    	FROM cur
	WHERE cur_title IN (\"" . implode ( "\",\"" , $watchedtitles ) . "\") ORDER BY cur_timestamp DESC";
    if ( $result = mysql_query ( $sql , $connection ) ) {
	$s = mysql_fetch_object ( $result ) ;
	if ( $s ) {
            while ($s) {
		    array_push ( $arr , $s ) ;
		    array_push ( $foundtitles , preg_replace ( '/^[^:]*:/' , '' , $s->cur_title ) ) ;
		    $s = mysql_fetch_object ( $result ) ;
		    }
            mysql_free_result ( $result ) ;
            $any = true ;
	    }
    } else {
    	return $wikiErrorPageTitle . str_replace ( "$1" , htmlspecialchars ( mysql_error () ) , $wikiErrorMessage ) ;
    	}

    # This could be done more efficiently.
    $nonexistent = array_diff ( $b , $foundtitles ) ;
    foreach ( $nonexistent as $x ) {
	$notexist .= "\n* [[".$vpage->getNiceTitle( $x )."]] (<a href=\"$THESCRIPT?title=".rawurlencode($x)."&action=watch&mode=no&returnto=special:WatchList\">$wikiNoWatch</a>)" ;
	}

/*  # There is no need to make a billion separate database requests
    foreach ( $k as $x ) {
        if ( $x != "" ) {
            #$sql = "SELECT cur_timestamp, cur_title, cur_comment, cur_user, cur_user_text, cur_minor_edit
            #        FROM cur
            #        WHERE cur_title=\"$x\" OR cur_title LIKE \"%:$x\"" ;
            $sql = "SELECT cur_timestamp, cur_title, cur_comment, cur_user, cur_user_text, cur_minor_edit
                    FROM cur
                    WHERE cur_title=\"$x\" OR cur_title = \"$talk:$x\"" ; # Temporary ugly partial hack until this function is rewritten to use the db more efficiently
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
*/
    if ( $any )
        $ret .= recentChangesLayout ( $arr ) ;
        
    if ( $notexist )
        $ret .= "\n----\n$wikiWatchlistNotExistText\n\n$notexist" ;

    return $ret ;
    }
?>
