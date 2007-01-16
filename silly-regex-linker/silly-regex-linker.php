<?php
/*
Plugin Name: Silly Regex Linker
Plugin URI: http://leuksman.com/pages/Silly_Regex_Linker
Description: A silly regex linker plugin for WordPress.
Version: 0.1
Author: Brion Vibber
Author URI: http://leuksman.com/
*/

// filter hook...

class SillyRegexLinker {
	public static function applyLinks( $text ) {
		$prev = error_reporting(E_ALL);
		
		$regexes = self::fetchRegexes();
		foreach( $regexes as $regex => $callback ) {
			$text = preg_replace_callback( $regex,
				array( 'SillyRegexLinker', $callback ),
				$text );
		}
		
		error_reporting( $prev );
		return $text;
	}
	
	private static function fetchRegexes () {
		return array(
			'/r(\d+)/' => 'svnLink',
			'/bug\s+(\d+)/' => 'bugLink',
			'/\[\[([^]]+)\|([^]]+)\]\]/' => 'wikiLink',
			'/\[\[([^]|]+)\]\]/' => 'wikiLinkNoText',
		);
	}
	
	private static function wikiLink( $matches ) {
		return self::doWikiLink( $matches[1], $matches[2] );
	}
	
	private static function wikiLinkNoText( $matches ) {
		return self::doWikiLink( $matches[1], $matches[1] );
	}
	
	private static function doWikiLink( $link, $text ) {
		$link = self::deCurl( $link );
		
		list( $base, $page ) = self::splitInterwiki( trim( $link ) );
		
		$clean = str_replace( '%2f', '/',
			str_replace( '%3a', ':',
				rawurlencode(
						str_replace( ' ', '_', $page ) ) ) );
		$url = str_replace( '$1', $clean, $base );
		
		return self::doLink( $url, $text );
	}
	
	private static function deCurl( $text ) {
		return str_replace( '&#8217;', "'", $text );
	}
	
	private static function splitInterwiki( $link ) {
		$chunks = array_map( 'trim', explode( ':', $link, 2 ) );
		if( count( $chunks > 1 ) ) {
			$normalizedPrefix = strtolower( $chunks[0] );
			$interwikiBase = self::getInterwiki( $normalizedPrefix );
			if( $interwikiBase ) {
				return array( $interwikiBase, $chunks[1] );
			}
		}
		$defaultBase = 'http://leuksman.com/pages/$1';
		return array( $defaultBase, $link );
	}
	
	private static function getInterwiki( $prefix ) {
		$map = array(
			'wikipedia' => 'http://en.wikipedia.org/wiki/$1',
			'meta' => 'http://meta.wikimedia.org/wiki/$1',
			'mw' => 'http://www.mediawiki.org/wiki/$1',
		);
		if( isset( $map[$prefix] ) ) {
			return $map[$prefix];
		}
		return false;
	}
	
	private static function svnLink( $matches ) {
		$rev = intval( $matches[1] );
		return self::doLink(
			"http://svn.wikimedia.org/viewvc/mediawiki?view=rev&revision=$rev",
			$matches[0] );
	}
	
	private static function bugLink( $matches ) {
		$bug = intval( $matches[1] );
		return self::doLink(
			"http://bugzilla.wikimedia.org/show_bug.cgi?id=$bug",
			$matches[0] );
	}
	
	private static function doLink( $url, $text ) {
		$escapedUrl = htmlspecialchars( $url );
		return "<a href=\"$escapedUrl\">$text</a>";
	}
}

add_filter( 'the_content', array( 'SillyRegexLinker', 'applyLinks' ) );
add_filter( 'comment_text', array( 'SillyRegexLinker', 'applyLinks' ) );

?>