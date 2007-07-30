<?php

/**
 * Parser hook extension to add a <randomimage> tag
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionFunctions[] = 'efRandomImage';
	$wgExtensionCredits['parserhook'][] = array(
		'name' => 'RandomImage',
		'author' => 'Rob Church',
		'url' => 'http://www.mediawiki.org/wiki/Extension:RandomImage',
		'description' => 'Provides a random media picker using <tt>&lt;randomimage /&gt;</tt>',
	);
	
	/**
	 * Set this to true to disable the parser cache for pages which
	 * contain a <randomimage> tag; this keeps the galleries up to date
	 * at the cost of a performance overhead on page views
	 */
	$wgRandomImageNoCache = false;
	
	/**
	 * Extension initialisation function
	 */
	function efRandomImage() {
		global $wgParser;
		$wgParser->setHook( 'randomimage', 'efRenderRandomImage' );
	}
	
	/**
	 * Extension rendering function
	 *
	 * @param $text Text inside tags
	 * @param $args Tag arguments
	 * @param $parser Parent parser
	 * @return string
	 */
	function efRenderRandomImage( $input, $args, &$parser ) {
		global $wgRandomImageNoCache;
		if( $wgRandomImageNoCache )
			$parser->disableCache();
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
				$float = strtolower( $options['float'] );
				# FIXME: Get the real magic words
				if( in_array( $float, array( 'left', 'right', 'centre' ) ) )
					$this->float = $float;
			}
		}
		
		function pickImage() {
			$dbr = wfGetDB( DB_SLAVE );
			$page = $dbr->tableName( 'page' );
			$nspc = NS_IMAGE;
			$rand = wfRandom();
			$index = $dbr->useIndexClause( 'page_random' );
			$sql = "SELECT page_title FROM {$page} {$index} WHERE page_namespace = {$nspc}
					AND page_is_redirect = 0 AND page_random > {$rand} ORDER BY page_random LIMIT 1";
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
			# One more attempt
			if( !$title )
				$title = $this->pickImage();
			if( $title ) {
				$wiki = $this->imageMarkup( $title, $caption );
				$output = $this->parser->parse( $wiki, $this->parser->getTitle(), $this->parser->getOptions(), false, false );
				return $output->getText();
			} else {
				return '';
			}
		}
			
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	exit( 1 );
}