<?
if (!defined('MEDIAWIKI')) die();

$wgHooks['AlternateEdit'][] = 'wfStaticEditHook' ;

$wgStaticWikiExternalSite = "http://en.wikipedia.org/w/" ;

function wfStaticEditHook ( $a ) {
	global $wgStaticWikiExternalSite ;
	global $wgOut , $wgTitle , $wgRequest ;

	$pstyle = "style='border-bottom:1px solid black; font-size:12pt; font-weight:bold'" ;

	$title = $wgTitle->getText () ;
	$url_title = $wgTitle->getPrefixedDBkey() ;
	
	$wgOut->setPageTitle ( wfMsg( 'importing', $wgTitle->getPrefixedText() ) ) ;
	
	# Read list of latest revisions
	$side = "" ;
	$history = file_get_contents ( $wgStaticWikiExternalSite . "index.php?title=" . $url_title . "&action=history" ) ;
	$history = explode ( "<li>" , $history ) ;
	array_shift ( $history ) ;
	$match = "/w/index.php?title=" . $url_title . "&amp;oldid=" ;
	$revisions = array () ;
	foreach ( $history AS $line ) {
		$a = explode ( 'href="' , $line ) ;
		array_shift ( $a ) ;
		foreach ( $a AS $x ) {
			$y = explode ( '"' , $x ) ;
			$x = array_shift ( $y ) ;
			if ( substr ( $x , 0 , strlen ( $match ) ) != $match ) continue ;
			$x = substr ( $x , strlen ( $match ) ) ;
			$revisions[] = $x ;
			array_shift ( $y ) ;
			$y = implode ( '"' , $y ) ;
			$y = explode ( '>' , $y ) ;
			array_shift ( $y ) ;
			$y = implode ( ">" , $y ) ;
			$y = explode ( "<" , $y ) ;
			$y = trim ( array_shift ( $y ) ) ;
			$date[$x] = $y ;
		}
	}

	# Revision to view
	$show_revision = $wgRequest->getText ( "showrevision" , $revisions[0] ) ;
	
	# Generating list of links for the sidebar
	$side = "<p {$pstyle}>" . "The last " . count ( $revisions ) . " revisions" . "</p>\n" ;
	$side .= "<table cellspacing=0 cellpadding=2>" ;
	foreach ( $revisions AS $r ) {
		$link_title = ' title="#' . $r . " (" . $date[$r] . ')"' ;
		$l1 = '<a href="?title=' . $url_title . '&action=edit&showrevision=' . $r . '"' . $link_title . '>' . $date[$r] . '</a>' ;
		$l2 = '<a href="?title=' . $url_title . '&action=edit&addrevision=' . $r . '"' . $link_title . '>' . "Import" . '</a>' ;
		$l3 = '<a href="' . $wgStaticWikiExternalSite . 'index.php?title=' . $url_title . '&oldid=' . $r . '"' . $link_title . '>' . "Original" . '</a>' ;
		$s = "<td align='right'>" . $l1 . "</td><td>" . $l2 . "</td><td>" . $l3 . "</td>\n" ;
		if ( $r == $show_revision ) $s = "<tr style='background-color:#DDDDDD'>{$s}</tr>" ;
		else $s = "<tr>{$s}</tr>" ;
		$side .= $s ;
	}
	$side .= "</table>" ;
	
	# Retrieving source text for the revision
	$url = $wgStaticWikiExternalSite . "index.php?title=" . $url_title . "&oldid=" . $show_revision . "&action=raw" ;
	$text = file_get_contents ( $url ) ;
	
	# Output	
	$wgOut->addHTML ( "<table width='100%'><tr><td style='border-right:1px solid black' valign='top' width='100%'>" ) ;
	$wgOut->addHTML ( "<p {$pstyle}>Revision #" . $show_revision . " at " . $date[$show_revision] . " of <i>" . $title . "</i></p>\n" ) ;
	$wgOut->addWikiText ( $text ) ;
	$wgOut->addHTML ( "</td><td nowrap valign='top'>" . $side . "</td></tr></table>" ) ;
	return true ;
}


?>
