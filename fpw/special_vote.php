<?

function addVote ( $log , $article ) {
	global $user , $wikiGetBriefDate , $CommentBox , $wikiVoteBecause , $wikiVoteMessage ;

	$npage = new WikiPage ;
	$npage->setTitle ( $log ) ;
	$npage->ensureExistence () ;
	$s = getMySQL ( "cur" , "cur_text" , "cur_title=\"$npage->secureTitle\"" ) ;

	$newEntry = true ;
	$t = "* [[$article]]" ;
	$u = "** [[user:$user->name|$user->name]] ".$wikiGetBriefDate() ;
	if ( $CommentBox != "" ) $u .= str_replace ( "$1" , $CommentBox , $wikiVoteBecause ) ;
	$v = explode ( "\n" , $s ) ;
	$s = array () ;
	foreach ( $v AS $x ) {
		array_push ( $s , $x ) ;
		if ( $x == $t ) {
			$newEntry = false ;
			array_push ( $s , $u ) ;
			}
		}
	$s = implode ( "\n" , $s ) ;

	if ( $newEntry )
		$s .= "$t\n$u\n" ;

	$npage->setEntry ( $s , str_replace ( "$1" , $article , str_replace ( "$2" , $user->name , $wikiVoteMessage ) ) , $user->id , $user->name , 1 ) ;
	}

function vote () {
	global $vpage , $target , $doVote , $voted ;
	global $wikiVoteReason , $wikiVoteWarn , $wikiVotes , $wikiVoteAdded , $wikiVoteError , $wikiVoteChoices ;

	if ( isset ( $doVote ) ) {
		if ( $voted == "" ) {
			$ret = str_replace ( "$1" , wikiLink("special:vote&target=".urldecode($target)) , $wikiVoteWarn ) ;

		} else {
			$log = "" ;
			$log = "wikipedia:".$wikiVotes[$voted] ;
			if ( $log != "" ) {
				addVote ( $log , $vpage->getNiceTitle ( urldecode ( $target ) ) ) ;
				$ret = str_replace ( array("$1","$2","$3") , array(urldecode($target),wikiLink($log),$log) , $wikiVoteAdded ) ;
			} else $ret = $vikiVoteError ;
			}
	} else {
		$ret = "<font size=+2>I want to vote for \"".$vpage->getNiceTitle(urldecode($target))."\" to be<br><br>\n" ;
		$ret .= "<FORM method=post>\n" ;
		$ret .= $wikiVoteChoices ;
		$ret .= "</FORM>\n</font>\n" ;
		}

	return $ret ;
	}
?>
