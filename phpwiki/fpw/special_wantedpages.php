<?
function refreshWantedPages () {
	global $showNumberPages , $linkedLinks , $unlinkedLinks , $vpage , $wikiWantedText , $wikiWantedLine ;
	global $wikiWantedToggleNumbers, $wikiLastRefreshed ;
	$vpage->special ( "The Most Wanted Pages" ) ;
	$vpage->namespace = "" ;
	if ( $showNumberPages == "" ) $showNumberPages = "off" ;
	if ( $showNumberPages == "off" ) $nsnp = "on" ;
	else $nsnp = "off" ;
	$allPages = array () ;
	$ret = $wikiWantedText ;
#	$ret .= "<nowiki><a href=\"".wikiLink("special:WantedPages?showNumberPages=$nsnp")."\">";
#	$ret .= str_replace("$1",$showNumberPages,$wikiWantedToggleNumbers)."</a></nowiki><br>\n" ;

	global $wikiSQLServer ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "SELECT cur_title,cur_linked_links,cur_unlinked_links FROM cur" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) {
		$allPages[ucfirst($s->cur_title)] = -999999999999 ; # Effectively removing existing topics from list
		$fc = substr ( $s->cur_title , 0 , 1 ) ;
		if ( $showNumberPages == "on" OR $fc < "0" OR $fc > "9" ) {
			$u = explode ( "\n" , $s->cur_unlinked_links ) ;
			$v = array () ;
			foreach ( $u as $x ) {
				$w = ucfirst ( $x ) ;
				if ( $v[$w] != true ) # Count only one link per page
					{
					$allPages[$w] += 1 ;
					$v[$w] = true ;
					}
				}
			unset ( $v ) ;
			}
		}
	mysql_free_result ( $result ) ;
	#mysql_close ( $connection ) ;

	arsort ( $allPages ) ;
	$somePages = array_slice ( $allPages , 0 , 400 ) ; # Reducing needed memory
	unset ( $allPages ) ;
	$allPages = $somePages ;
	unset ( $somePages ) ;

	$ti = new wikiTitle ;
	$k = array_keys ( $allPages ) ;

	$a = 0 ;
	$o = array () ;
	while ( count ( $o ) < 50 ) {
		$x = $k[$a] ;
		$a++ ;
		$ti->setTitle ( $x ) ;
		if ( $x != "" and !$ti->doesTopicExist() ) {
			$n = str_replace ( "$1" , "[[$x|".$ti->getNiceTitle($x)."]]" , $wikiWantedLine ) ;
			$n = str_replace ( "$2" , $allPages[$x] , $n ) ;
			$n = str_replace ( "$3" , wikiLink("special:whatlinkshere&target=".nurlencode($x)) , $n ) ;
			$n = str_replace ( "$4" , $ti->getNiceTitle($x) , $n ) ;
			array_push ( $o , "*$n\n" ) ;
			}
		}
	$ret .= implode ( "" , $o ) ;
	
	$now = time () ;
	$lc = wikiGetDate ( $now ) . date ( ", H:i" , $now ) ;
	#$lc .= ", ".substr ( $now , 8 , 2 ) ;
	#$lc .= ":".substr ( $now , 10 , 2 ) ;
	$ret .= "\n<p>" . str_replace ( "$1", $lc, $wikiLastRefreshed ) . "</p>\n" ;

	return $ret ;
	}

function WantedPages () {
	global $doRefresh , $wikiRefreshThisPage , $wikiResourcesWarning , $wikiNoRefresh ;
	$pn = "Log:Most_Wanted" ;
	$minRefreshPeriod = 5*60; # Refuse to update more often than every 5 minutes
	$ret = "<nowiki>" ;

	$ret .= "<p align=center><font size='+1'><b><a href=\"" ;
	$ret .= wikiLink ( "special:WantedPages&doRefresh=yes" ) ;
	$ret .= "\">$wikiRefreshThisPage</a></b></font><br>$wikiResourcesWarning</p></nowiki>\n" ;
	if ( $doRefresh == "yes" ) {
		$timestamp = getMySQL ( "cur" , "UNIX_TIMESTAMP(cur_timestamp)" , "cur_title=\"$pn\"" ) ;
		if ( ( $lastrefreshed = time() - $timestamp ) < $minRefreshPeriod ) {
			$ret .= "<p><i>" . str_replace ( array ( "$1", "$2" ) ,
				array ( round ( $lastrefreshed / 60 ) , round ( ( $minRefreshPeriod - $lastrefreshed ) / 60 ) ),
				$wikiNoRefresh ) . "</i></p>\n\n";
		} else {
			$o = refreshWantedPages () ;
			$ret .= $o ;
			$p = new wikiPage ;
			$p->setTitle ( $pn ) ;
			$p->ensureExistence () ;
			#$p->setEntry ( $o , "Refresh" , 0 , "System" , 1 , ",cur_timestamp=cur_timestamp" ) ; # Storing, don't show on RC
			$p->setEntry ( $o , "Refresh" , 0 , "System" , 1 , "" ) ; # Storing, don't show on RC
			$timestamp2 = getMySQL ( "cur" , "UNIX_TIMESTAMP(cur_timestamp)" , "cur_title=\"$pn\"" ) ;
			return $ret;
			}
		}
	$ret .= getMySQL ( "cur" , "cur_text" , "cur_title=\"$pn\"" ) ;
	return $ret ;
	}
?>
