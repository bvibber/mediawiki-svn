<?

function addVote ( $log , $article ) {
	global $user , $wikiGetBriefDate , $CommentBox ;

	$npage = new WikiPage ;
	$npage->setTitle ( $log ) ;
	$npage->ensureExistence () ;
	$s = getMySQL ( "cur" , "cur_text" , "cur_title=\"$npage->secureTitle\"" ) ;

	$newEntry = true ;
	$t = "* [[$article]]" ;
	$u = "** [[user:$user->name|$user->name]] ".$wikiGetBriefDate() ;
	if ( $CommentBox != "" ) $u .= ", because : <i>$CommentBox</i>" ;
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

	$npage->setEntry ( $s , "Vote by $user->name for $article" , $user->id , $user->name , 1 ) ;
	}

function vote () {
	global $vpage , $target , $doVote , $voted ;
	global $wikiVoteReason ;

	if ( isset ( $doVote ) ) {
		if ( $voted == "" ) {
			$ret = "<font size=+2>You did not say what you want to vote for! <a href=\"".wikiLink("special:vote&target=".urldecode($target))."\">Try again</a>.</font>" ;
		} else {
			$log = "" ;
			if ( $voted == "delete" ) $log = "wikipedia:Votes for deletion" ;
			if ( $voted == "rewrite" ) $log = "wikipedia:Votes for rewrite" ;
			if ( $voted == "wikify" ) $log = "wikipedia:Votes for wikification" ;
			if ( $voted == "NPOV" ) $log = "wikipedia:Votes for NPOV" ;
			if ( $voted == "aotd" ) $log = "wikipedia:Votes for article-of-the-day" ;
			if ( $log != "" ) {
				addVote ( $log , $vpage->getNiceTitle ( urldecode ( $target ) ) ) ;
				$ret = "<font size=+2>".urldecode($target)." has been added to <a href=\"".wikiLink($log)."\">$log</a>!</font>" ;
			} else $ret = "<font size=+2>Something went really wrong here!</font>" ;
			}
	} else {
		$ret = "<font size=+2>I want to vote for \"".$vpage->getNiceTitle(urldecode($target))."\" to be<br><br>\n" ;
		$ret .= "<FORM method=post>\n" ;
		$ret .= "<input type=radio value=delete name=voted>deleted<br>\n" ;
		$ret .= "<input type=radio value=rewrite name=voted>rewritten<br>\n" ;
		$ret .= "<input type=radio value=NPOV name=voted>NPOVed<br>\n" ;
		$ret .= "<input type=radio value=wikify name=voted>wikified<br>\n" ;
		$ret .= "<input type=radio value=aotd name=voted>article-of-the-day<br><br>\n" ;
		$ret .= "$wikiVoteReason<input type=text value=\"\" name=CommentBox size=20> <input type=submit value=\"Vote\" name=doVote>\n" ;
		$ret .= "</FORM>\n</font>\n" ;
		}

	return $ret ;
	}
?>
