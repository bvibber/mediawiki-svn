<?php
# Copyright (C) 2005 Anders Wegge Jakobsen <awegge@gmail.com>
# http://www.mediawiki.org/
# 
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or 
# (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
# http://www.gnu.org/copyleft/gpl.html

/**
 
* Extension to add footnotes to the wiki pages. 
*
* Use with:
*
* <footnote>This text is placed at the end of the page.</footnote>
*
* @author Anders Wegge Jakobsen <awegge@gmail.com>
* @package MediaWiki
* @subpackage Extensions
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die();
}


$wgExtensionFunctions[] = "wfFootnote";

function wfFootnote() {
	global $wgParser ;
	$wgParser->setHook( "footnote" , 'parse_footnote' ) ;
}

$footnotes = array() ;
$footnotecount = 1 ;
$recursion_guard = 0;

function footnote_hooker( $parser , $text ) {
	global $footnotes , $footnotecount, $recursion_guard ;
	
	if( $recursion_guard != 0 ) return;
	if( count( $footnotes ) == 0 ) return ;

	$ret = "" ;
	foreach( $footnotes AS $num => $entry ) {
		$x = " <a name='footnote{$num}'></a>\n";
		$x = $x . "<li>$entry <a href='#footnoteback{$num}'>&uarr;</a></li>\n" ;
		$ret .= $x ;
	}
	$ret = "<hr/><ol>" . $ret . "</ol>" ;
	
	$text .= $ret ;
}

function parse_footnote( $text ) {
	$ret = "" ;

	global $footnotes , $footnotecount, $recursion_guard ;

	global $wgTitle , $wgOut, $p;

	if( !isset( $p )) {
		$p = new Parser ;
	}

	$recursion_guard = 1;
	$ret = $p->parse( $text , $wgTitle , $wgOut->mParserOptions, false ) ;
	$ret = $ret->getText();
	$recursion_guard = 0;

	$footnotes[$footnotecount] = $ret;

	$ret = "<a href='#footnote{$footnotecount}' name='footnoteback{$footnotecount}'><sup>$footnotecount</sup></a>" ;
	
	$footnotecount++ ;
	if( $footnotecount == 2 ) {
		global $wgHooks;
		$wgHooks['ParserBeforeTidy'][] = 'footnote_hooker' ;
	}

	return $ret ;
}
?>
