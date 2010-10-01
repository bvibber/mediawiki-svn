<?php

/* 
 * RandomImageGenerator -- does what it says on the tin.
 *
 * Because MediaWiki tests the uniqueness of media upload content, and filenames, it is sometimes useful to generate
 * files that are guaranteed (or at least very likely) to be unique in both those ways.
 * This generates a number of filenames with random names and random content (colored circles) 
 *
 * Requires Imagick, the ImageMagick library for PHP.
 *  
 * @file
 * @author Neil Kandalgaonkar <neilk@wikimedia.org>
 */


/**
 * Does what it says on the tin.
 * Can fetch a random image, or also write a number of them to disk with random filenames.
 */
class RandomImageGenerator {

	private $dictionaryFile;
	private $minWidth = 400;
	private $maxWidth = 800;
	private $minHeight = 400;
	private $maxHeight = 800;
	private $circlesToDraw = 5;
	
	public function __construct( $options ) {
		foreach ( array( 'dictionaryFile', 'minWidth', 'minHeight', 'maxHeight', 'circlesToDraw' ) as $property ) {
			if ( isset( $options[$property] ) ) {
				$this->$property = $options[$property];
			}
		}
		
		if ( !isset( $this->dictionaryFile ) ) {
			foreach ( array( '/usr/share/dict/words', '/usr/dict/words' ) as $dictionaryFile ) {
				if ( is_file( $dictionaryFile ) and is_readable( $dictionaryFile ) ) {
					$this->dictionaryFile = $dictionaryFile;
					break;
				}
			}
		}
		if ( !isset( $this->dictionaryFile ) ) {
			die( "dictionary file not found or not specified properly" );
		}
		if ( !is_file( $this->dictionaryFile ) or !file_exists( $this->dictionaryFile ) ) {
			die( "can't read dictionary file, or it doesn't exist." );
		}
	}

	/**
	 * Writes random images with random filenames to disk in current working directory
	 * 
	 * @param {Integer} number of filenames to write
	 * @param {String} format understood by ImageMagick, such as 'jpg' or 'gif'
	 * @return {Array} filenames we just wrote
	 */
	function writeImages( $number, $format ) {
		$filenames = $this->getRandomFilenames( $number, $format );
		foreach( $filenames  as $filename ) {
			$image = $this->getImage();
			$image->setImageFormat( $format );
			$image->writeImage( $filename );
		}
		return $filenames;
	}


	/** 
	 * Return a number of randomly-generated filenames
	 * Each filename uses two words randomly drawn from the dictionary, like foo_bar.jpg
 	 *
	 * @param {Integer} number of filenames to generate
	 * @param {String} extension, if desired
	 * @return {Array} of filenames
	 */
	private function getRandomFilenames( $number, $extension=null ) {
		$filenames = array();

		foreach( $this->getRandomWordPairs( $number ) as $pair ) {
			$filename = $pair[0] . '_' . $pair[1];
			if ( !is_null( $extension ) ) {
				$filename .= '.' . $extension;
			}
			$filename = preg_replace( '/\s+/', '', $filename );
			$filenames[] = $filename;
		}
	
		return $filenames;
		
	}


	/**
	 * Generate an image consisting of randomly colored and sized circles 
	 * @return {Image}
	 */
	public function getImage() { 

		$imageWidth = mt_rand( $this->minWidth, $this->maxWidth ); 
		$imageHeight = mt_rand( $this->minHeight, $this->maxHeight ); 

		$image = new Imagick();
		$image->newImage( $imageWidth, $imageHeight, new ImagickPixel( $this->getRandomColor() ) );

		$diagonalLength = sqrt( pow( $imageWidth, 2 ) + pow( $imageHeight, 2 ) );

		for ( $i = 0; $i <= $this->circlesToDraw; $i++ ) {
			$radius = mt_rand( 0, $diagonalLength / 4 );
			$originX = mt_rand( -1 * $radius, $imageWidth + $radius );
			$originY = mt_rand( -1 * $radius, $imageHeight + $radius );
			$perimeterX = $originX + $radius;
			$perimeterY = $originY + $radius;

			$draw = new ImagickDraw(); 
			$draw->setFillColor( $this->getRandomColor() );
			$draw->circle( $originX, $originY, $perimeterX, $perimeterY );
			$image->drawImage( $draw );
			
		}

		return $image;
	}

		

	/**
	 * Generate a string of random colors for ImageMagick, like "rgb(12, 37, 98)"
	 * 
	 * @return {String}
	 */
	public function getRandomColor() {
		$components = array();
		for ($i = 0; $i <= 2; $i++ ) {
			$components[] = mt_rand( 0, 255 );
		}
		return 'rgb(' . join(', ', $components) . ')';
	}

	/** 
	 * Get an array of random pairs of random words, like array( array( 'foo', 'bar' ), array( 'quux', 'baz' ) );
	 *
	 * @param {Integer} number of pairs
	 * @return {Array} of two-element arrays 
	 */
	private function getRandomWordPairs( $number ) { 
		$lines = $this->getRandomLines( $number * 2 );
		// construct pairs of words
		$pairs = array();
		$count = count( $lines );
		for( $i = 0; $i < $count; $i += 2 )  {
			$pairs[] = array( $lines[$i], $lines[$i+1] );
		}
		return $pairs;
	}

	
	/**
	 * Return N random lines from a file
	 * 
	 * Will die if the file could not be read or if it had fewer lines than requested.
	 * 
	 * @param {Integer} number of lines desired
	 * @string {String} path to file 
	 * @return {Array} of exactly n elements, drawn randomly from lines the file
	 */
	private function getRandomLines( $number_desired ) { 
		$filepath = $this->dictionaryFile;

		// initialize array of lines
		$lines = array();
		for ( $i = 0; $i < $number_desired; $i++ ) {
			$lines[] = null;
		}

		/*
		 * This algorithm obtains N random lines from a file in one single pass. It does this by replacing elements of 
		 * a fixed-size array of lines, less and less frequently as it reads the file.
		 */
		$fh = fopen( $filepath, "r" ) or die( "couldn't open $filepath" ) ;
		$line_number = 0;
		$max_index = $number_desired - 1;
		while( !feof( $fh ) ) { 
			$line = fgets( $fh );
			if ( $line !== false ) {
				$line_number++;  
				$line = trim( $line ); 
				if ( mt_rand( 0, $line_number ) <= $max_index ) { 
					$lines[ mt_rand( 0, $max_index ) ] = $line;
				}
			}
		}
		fclose( $fh );
		if ( $line_number < $number_desired ) {
			die( "not enough lines in $filepath" );
		}
		
		return $lines;
	}

}
