<?php
/*
Usage :

<seealso>
Item1
Item2|Text
</seealso>

Set system message "seealso" to head text, e.g., "See also"
Set system message "seealso_local" to use a localized version, e.g., to "sieheauch"
*/

$wgExtensionCredits['parserhook'][] = array(
	'name'        => 'Seealso',
	'version'     => '1.1',
	'url'         => 'http://www.mediawiki.org/wiki/Extension:See_also',
	'author'      => 'Magnus Manske',
	'description' => 'Localised \'See also\' headings using the tag <nowiki><seealso></nowiki>',
);

$wgExtensionFunctions[] = "wfSeealso";

function wfSeealso () {
	global $wgParser ;
	$wgParser->setHook ( "seealso" , 'parse_seealso' ) ;
	$l = trim ( wfMsg ( "seealso_local" , "" ) ) ;
	if ( $l != "" )
		$wgParser->setHook ( $l , 'parse_seealso' ) ;
}

function parse_seealso ( $text, $params, &$parser ) {
	$a = explode ( "\n" , $text ) ;
	$ret = "== " . trim ( wfMsg ( "seealso" ) ) . " ==\n" ;
	foreach ( $a AS $x ) {
		$x = trim ( $x ) ;
		if ( $x == "" ) continue ;
		$ret .= "* [[" . $x . "]]\n" ;
	}
	$p = new Parser ;
	$ret = $p->parse ( $ret , $parser->getTitle() , $parser->getOptions(), false ) ;
	$ret = $ret->getText();
	return $ret ;
}
