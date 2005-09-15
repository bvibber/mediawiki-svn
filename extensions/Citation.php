<?php
# Example use :
#
# There is a <citation>author=Manske M ||title="The best paper ever" ||journal=''Biochemistry'' ||volume='''5''', 11</citation> citation here!


$wgHooks['ParserBeforeTidy'][] = 'citation_hooker' ;
$wgExtensionFunctions[] = "wfCitation";

function wfCitation () {
	global $wgParser ;
	$wgParser->setHook ( "citation" , 'parse_citation' ) ;
}

$wgCitationCache = array () ;
$wgCitationCounter = 1 ;
$wgCitationRunning = false ;

function citation_hooker ( $parser , $text ) {
	global $wgCitationCache , $wgCitationCounter , $wgCitationRunning ;
	if ( $wgCitationRunning ) return ;
	if ( count ( $wgCitationCache ) == 0 ) return ;
	
	$ret = "" ;
	foreach ( $wgCitationCache AS $num => $entry ) {
		$x = "<li>" . $entry . " <a href='#citeback{$num}'>&uarr;</a></li>\n" ;
		$ret .= $x ;
	}
	$ret = "<hr/><ol>" . $ret . "</ol>" ;
	
	$text .= $ret ;
}

function parse_citation ( $text ) {
	global $wgCitationRunning ;
	if ( $wgCitationRunning ) return ;
	$ret = "" ;
	$attheend = false ;
	$res = array () ;
	$order = "" ;
	$href = "" ;
	$a = explode ( "||" , $text ) ;
	
	foreach ( $a AS $line ) {
		$data = explode ( "=" , $line , 2 ) ;
		while ( count ( $data ) < 2 ) $data[] = "" ;
		$key = urlencode ( trim ( strtolower ( array_shift ( $data ) ) ) ) ;
		$value = array_shift ( $data ) ;
		
		// Parsed now : "$key" = "$value"
		if ( $key == "attheend" ) $attheend = true ;
		else if ( $key == "order" ) $order = $value ;
		else if ( $key == "href" ) $href = $value ;
		else if ( $value != "" ) $res[$key] = $value ;
	}
	
	if ( $order == "" ) { # No order given, just use the one used
		$order = array_keys ( $res ) ;
	} else { # Special order
		$order = explode ( "," , $order ) ;
	}
	
	foreach ( $order AS $item ) {
		if ( !isset ( $res[$item] ) ) continue ;
		$ret .= $res[$item] ;
	}
	
	if ( $href != "" ) $ret .= " [{$href}]" ;
	

	global $wgTitle , $wgOut ;
	$p = new Parser ;
	$wgCitationRunning = true ;
	$ret = $p->parse ( $ret , $wgTitle , $wgOut->mParserOptions, false ) ;
	$wgCitationRunning = false ;
	$ret = $ret->getText();

	if ( $attheend ) {
		global $wgCitationCache , $wgCitationCounter ;
		$ret = "<a name='citation{$wgCitationCounter}'></a>{$ret}" ;
		$wgCitationCache[$wgCitationCounter] = $ret ;
		$ret = "<a href='#citation{$wgCitationCounter}' name='citeback{$wgCitationCounter}'>{" . $wgCitationCounter . "}</a>" ;
		$wgCitationCounter++ ;
	} else {
		$ret = "<span style='font-size:8pt'>[{$ret}]</span>" ;
	}

	return $ret ;
}



?>
