<?
function special_pages () {
	global $THESCRIPT , $vpage , $user , $wikiSpecialTitle , $wikiSpecialText ;
	$vpage->special ( $wikiSpecialTitle ) ;
	$ret = $wikiSpecialText ;
	$ret .= $vpage->getQuickBar () ;
	$ret = "<nowiki>$ret</nowiki>\n----\n" ;
	$ret .= "[[log:Uploads|Upload log]]<br>\n" ;
	$ret .= "[[log:Page_Deletions|Deleted pages log]]<br>\n" ;
	return $ret ;
	}
?>