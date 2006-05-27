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
		
		function internalSort( $text ) {
			$raw = explode( "\n", $text );
			$lines = array();
			foreach( $raw as $line ) {
				if( trim( $line ) != '' ) {	
					$html = $this->parse( $line );
					$lines[ $html ] = $this->stripHtml( $html );
				}
			}
			natsort( $lines );
			if( $this->order == 'desc' )
				$lines = array_reverse( $lines, true );
			return $lines;
		}
		
		function makeList( $sorted ) {
			$tag = $this->class == 'ol' ? 'ol' : 'ul';
			foreach( $sorted as $html => $text )
				$list[] = wfOpenElement( 'li' ) . $html . wfCloseElement( 'li' );
			return wfOpenElement( $tag ) . implode( "\n", $list ) . wfCloseElement( $tag );
		}
		
		function sortToHtml( $text ) {
			return $this->makeList( $this->internalSort( $text ) );
		}
		
		function stripHtml( $text ) {
			return preg_replace( '@<[\/\!]*?[^<>]*?>@si', '', $text );
		}
	
		function parse( $text ) {
			$title =& $this->parser->mTitle;
			$options =& $this->parser->mOptions;
			$output = $this->parser->parse( $text, $title, $options, false, false );
			return $output->getText();
		}
	
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( -1 );
}

?>