<?
function special_pages () {
	global $THESCRIPT , $vpage , $user , $wikiSpecialTitle , $wikiSpecialText ;
	$vpage->special ( $wikiSpecialTitle ) ;
	$ret = $wikiSpecialText ;
	$ret .= $vpage->getQuickBar () ;
	$ret = "<nowiki>$ret</nowiki>" ;
	return $ret ;
	}
?>