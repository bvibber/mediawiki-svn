<?php

/**
 * Parser hook extension to add a <randomimage> tag
 * Affected by caching, but that's probably acceptable (and useful) here
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionFunctions[] = 'efRandomImage';
	$wgExtensionCredits['parserhook'][] = array( 'name' => 'RandomImage', 'author' => 'Rob Church', 'url' => 'http://meta.wikimedia.org/wiki/RandomImage' );
	
	function efRandomImage() {
		global $wgParser;
		$wgParser->setHook( 'randomimage', 'efRenderRandomImage' );
	}
	
	function efRenderRandomImage( $input, $args, &$parser ) {
		$image = new RandomImage( $args, $parser );
		return $image->render( $input );
	}
	
	class RandomImage {
	
		var $parser;
		
		var $width = 'thumb';
		var $float = false;
		
		function RandomImage( $options, &$parser ) {
			$this->parser =& $parser;
			$this->setOptions( $options );
		}
	
		function setOptions( $options ) {
			if( isset( $options['size'] ) ) {
				$s = $options['size'];
				if( is_numeric( $s ) )
					$this->width = intval( $s ) . 'px'; # FIXME: Does "px" have a magic word equiv?
			}
			if( isset( $options['float'] ) ) {
				$f = strtolower( $options['float'] );
				wfDebugLog( 'randomimage', 'Float is ' . $f );
				# FIXME: Get the real magic words
				if( array_search( $f, array( 'left', 'right', 'centre' ) ) !== false )
					$this->float = $f;
			}
		}
		
		function pickImage() {
			$dbr =& wfGetDB( DB_SLAVE );
			$page = $dbr->tableName( 'page' );
			$nspc = NS_IMAGE;
			$rand = wfRandom();
			$index = $dbr->useIndexClause( 'page_random' );
			$sql = "SELECT page_title FROM {$page} {$index} WHERE page_namespace = {$nspc}
					AND page_is_redirect = 0 AND page_random > {$rand}";
			$res = $dbr->query( $sql, 'RandomImage::pickImage' );
			if( $row = $dbr->fetchObject( $res ) ) {
				$ret = Title::makeTitleSafe( $nspc, $row->page_title );
			} else {
				$ret = false;
			}
			$dbr->freeResult( $res );
			return $ret;
		}
		
		function imageMarkup( &$title, $caption ) {
			global $wgContLang;
			$elements[] = $title->getPrefixedText();
			$elements[] = $this->width;
			if( $this->float )
				$elements[] = $this->float;
			if( $caption )
				$elements[] = $caption;
			return '[[' . implode( '|', $elements ) . ']]';
		}
		
		function render( $caption ) {
			$title = $this->pickImage();
			if( $title ) {
				$wiki = $this->imageMarkup( $title, $caption );
				$output = $this->parser->parse( $wiki, $this->parser->mTitle, $this->parser->mOptions, false, false );
				return $output->getText();
			} else {
				wfDebugLog( 'randomimage', 'Image picker returned false.' );
				return '';
			}
		}
			
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

?>