<?
# Example use :
#
# There is a <citation>author=Manske M ||title="The best paper ever" ||journal=''Biochemistry'' ||volume='''5''', 11</citation> citation here!


$wgHooks['ParserBeforeTidy'][] = 'citation_hooker' ;
$wgExtensionFunctions[] = "wfCitation";

function wfCitation () {
	global $wgParser ;
	$wgParser->setHook ( "citation" , parse_citation ) ;
}

$citeendcache = array () ;
$citecount = 1 ;

function citation_hooker ( $parser , $text ) {
	global $citeendcache , $citecount ;
	if ( count ( $citeendcache ) == 0 ) return ;
	
	$ret = "" ;
	foreach ( $citeendcache AS $num => $entry ) {
		$x = "<li>" . $entry . " <a href='citeback{$num}'>&uarr;</a></li>\n" ;
		$ret .= $x ;
	}
	$ret = "<hr/><ol>" . $ret . "</ol>" ;
	
	$text .= $ret ;
}

function parse_citation ( $text ) {
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
	$ret = $p->parse ( $ret , $wgTitle , $wgOut->mParserOptions, false ) ;
	$ret = $ret->getText();

	if ( $attheend ) {
		global $citeendcache , $citecount ;
		$ret = "<a name='citation{$citecount}'></a>{$ret}" ;
		$citeendcache[$citecount] = $ret ;
		$ret = "<a href='citation{$citecount}' name='citeback{$citecount}'>{" . $citecount . "}</a>" ;
		$citecount++ ;
	} else {
		$ret = "<font size='-2'>[{$ret}]</font>" ;
	}

	return $ret ;
}



?>
