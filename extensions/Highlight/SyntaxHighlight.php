<?php
/**
 * Syntax highlighting extension for MediaWiki 1.5 using GeSHi
 * Copyright (C) 2005 Brion Vibber <brion@pobox.com>
 * http://www.mediawiki.org/
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

/**
 * @package MediaWiki
 * @subpackage Extensions
 * @author Brion Vibber
 *
 * This extension wraps the GeSHi highlighter: http://qbnz.com/highlighter/
 *
 * Unlike the older GeSHi MediaWiki extension floating around, this makes
 * use of the new extension parameter support in MediaWiki 1.5 so it only
 * has to register one tag, <source>.
 *
 * A language is specified like: <source lang="c">void main() {}</source> 
 * If you forget, or give an unsupported value, the extension spits out
 * some help text and a list of all supported languages.
 *
 * The extension has been tested with GeSHi 1.0.7 and MediaWiki 1.5 CVS
 * as of 2005-06-22.
 *
 * @todo Localize help text
 * @todo Handle multiple error return types
 * @todo Allow other parameters, such as line numbering
 */

if( !defined( 'MEDIAWIKI' ) )
	die();

require_once( 'geshi/geshi.php' );

$wgExtensionFunctions[] = 'syntaxSetup';

function syntaxSetup() {
	global $wgParser;
	$wgParser->setHook( 'source', 'syntaxHook' );
}

function syntaxHook( $text, $params = array() ) {
	return isset( $params['lang'] )
		? syntaxFormat( $text, $params['lang'] )
		: syntaxHelp();
}

function syntaxFormat( $text, $lang ) {
	$geshi = new GeSHi( $text, $lang );
	$geshi->set_encoding( 'UTF-8' );
	
	$out   = $geshi->parse_code();
	$error = $geshi->error();
	return $error
		? syntaxHelp( $error )
		: $out;
}

function syntaxHelp() {
	return "<div style='border: solid red 1px'>" .
		"<p>You need to specify a language like this: " .
		"<samp>&lt;source lang=&quot;html&quot;&gt;...&lt;/source&gt;</samp> " .
		"Supported languages for syntax highlighting:</p>\n" .
		syntaxFormatList( syntaxLanguageList() ) .
		"</div>\n";
}

function syntaxFormatList( $list ) {
	return empty( $list )
		? "<p>(error loading support language list)</p>\n"
		:  "<p style='margin-left: 0.5in; margin-right: 0.5in'>" .
			implode( ", ", array_map( 'syntaxListItem', $list ) ) .
			"</p>\n";
}

function syntaxListItem( $item ) {
	return "<samp>" . htmlspecialchars( $item ) . "</samp>";
}

function syntaxLanguageList() {
	$langs = array();
	$langroot = @opendir( GESHI_LANG_ROOT );
	if( $langroot ) {
		while( $item = readdir( $langroot ) ) {
			if( preg_match( '/^(.*)\\.php$/', $item, $matches ) ) {
				$langs[] = $matches[1];
			}
		}
		closedir( $langroot );
	}
	sort( $langs );
	return $langs;
}

?>