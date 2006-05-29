<?php

/**
 * Parser hook extension adds a <sort> tag to wiki markup
 *
 * <sort>
 * <sort order="asc" class="ol">
 *
 * Both attributes are optional; the default is for an ascending
 * sort using an unordered list. Order can be ASC or DESC, case
 * insensitive. Class can be OL or UL, also case insensitive.
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0
 *
 * @todo Profile this to see how it copes with larger lists; might need to
 * 			re-think a sizeable portion of the main sort function so we don't
 *			flood the application server(s) with multiple parse operations
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionFunctions[] = 'efSort';
	$wgExtensionCredits['parserhook'][] = array( 'name' => 'Sort', 'author' => 'Rob Church' );
	
	function efSort() {
		global $wgParser;
		$wgParser->setHook( 'sort', 'efRenderSort' );
	}
	
	function efRenderSort( $input, $args, &$parser ) {
		$sorter = new Sorter( $parser );
		$sorter->loadSettings( $args );
		return $sorter->sortToHtml( $input );
	}
	
	class Sorter {
	
		var $parser;
		var $order;
		var $class;
		
		function Sorter( &$parser ) {
			$this->parser =& $parser;
			$this->order = 'asc';
			$this->class = 'ul';
		}
		
		function loadSettings( $settings ) {
			if( isset( $settings['order'] ) )
				$this->order = strtolower( $settings['order'] ) == 'desc' ? 'desc' : 'asc';
			if( isset( $settings['class'] ) ) {
				$c = strtolower( $settings['class'] );
				if( $c == 'ul' || $c == 'ol' )
					$this->class = $c;
			}
		}
		
		function sortToHtml( $text ) {
			wfProfileIn( 'Sorter::sortToHtml' );
			$lines = $this->internalSort( $text );
			$list = $this->makeList( $lines );
			wfProfileOut( 'Sorter::sortToHtml' );
			return $this->parse( $list );
		}
		
		function internalSort( $text ) {
			wfProfileIn( 'Sorter::internalSort' );
			$lines = explode( "\n", $text );
			$inter = array();
			foreach( $lines as $line )
				$inter[ $line ] = $this->stripWikiTokens( $line );
			natsort( $inter );
			if( $this->order == 'desc' )
				$inter = array_reverse( $inter, true );
			wfProfileOut( 'Sorter::internalSort' );
			return array_keys( $inter );
		}
		
		function stripWikiTokens( $text ) {
			$find = array( '[', '{', '\'', '}', ']' );
			$resl = str_replace( $find, '', $text );
			return $resl;
		}
		
		function makeList( $lines ) {
			wfProfileIn( 'Sorter::makeList' );
			$token = $this->class == 'ul' ? '*' : '#';
			foreach( $lines as $line ) {
				if( strlen( $line ) > 0 )
					$list[] = $token . trim( $line );
			}			
			wfProfileOut( 'Sorter::makeList' );
			return implode( "\n", $list );
		}
		
		function parse( $text ) {
			wfProfileIn( 'Sorter::parse' );
			$title =& $this->parser->mTitle;
			$options =& $this->parser->mOptions;
			$output = $this->parser->parse( $text, $title, $options, false, false );
			wfProfileOut( 'Sorter::parse' );
			return $output->getText();
		}
	
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( -1 );
}

?>