<?
function special_pages () {
	global $THESCRIPT , $vpage , $user , $wikiSpecialTitle , $wikiSpecialText ;
	$vpage->special ( $wikiSpecialTitle ) ;
	$ret = $wikiSpecialText ;
	$ret .= "<table border=1><tr><td>\n" ;
	$ret .= $vpage->getQuickBar () ;
	$ret .= "</td><td valign=top>" ;
	$ret = "<nowiki>$ret</nowiki>\n" ;
	$ret .= "[[log:Uploads|Upload log]]<br>\n" ;
	$ret .= "[[log:Page_Deletions|Deleted pages log]]<br><br>\n" ;

	$ret .= "[[wikipedia:Votes for deletion|Votes for deletion]]<br>\n" ;
	$ret .= "[[wikipedia:Votes for rewrite|Votes for rewrite]]<br>\n" ;
	$ret .= "[[wikipedia:Votes for wikification|Votes for wikification]]<br>\n" ;
	$ret .= "[[wikipedia:Votes for NPOV|Votes for NPOV]]<br>\n" ;
	$ret .= "[[wikipedia:Votes for article-of-the-day|Votes for article-of-the-day]]<br>\n" ;

	$ret .= "</td></tr></table>" ;
	return $ret ;
	}
?>