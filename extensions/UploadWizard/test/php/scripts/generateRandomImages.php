<?php

/* 
 * generateRandomImages -- does what it says on the tin.
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

$defaults = array( 
	'dict' => "/usr/share/dict/words",
	'number' => 10,
	'minWidth' => 400,
	'maxWidth' => 800,
	'minHeight' => 400,
	'maxHeight' => 800,
	'format' => 'jpg'
);

writeRandomImages( getOptions( $defaults ) );




/**
 * Override defaults with command-line options
 * 
 * @param {Array} key-value default values
 * @return {Array} defaults with CLI overrides
 */ 
function getOptions( $defaults ) { 
	
	// all options are optional, so append '::' to spec
	$getoptSpec = array_map( function($s) { return $s . "::"; }, array_keys( $defaults ) );
	$cliOptions = getopt( null, $getoptSpec );

	$options = array();
	foreach ( $defaults as $key => $value ) {
		$options[$key] = array_key_exists( $key, $cliOptions ) ? $cliOptions[$key] : $defaults[$key];	
	}

	return $options;
}



/**
 * writes random images with random files to disk in current working directory
 * 
 * @param {Array} key-value options
 */
function writeRandomImages( $options ) {
	global $dictionary;

	// each filename uses two words from the dictionary
	$wordsDesired = $options['number'] * 2;

	foreach( getPairs( getRandomLines( $wordsDesired, $options['dict'] ) ) as $pair ) { 
		$filename = $pair[0] . '_' . $pair[1] . '.' . $options['format'];
		
		// strip all whitespace (in case we somehow have inner whitespace)
		$filename = preg_replace( '/\s+/', '', $filename );

		$image = getRandomImage( $options['minWidth'], $options['maxWidth'], $options['minHeight'], $options['maxHeight'] );
		$image->setImageFormat( $options['format'] );
		$image->writeImage( $filename );
	}
}


/**
 * Generate an image consisting of randomly colored and sized circles 
 * @return {Image}
 */
function getRandomImage($minWidth, $maxWidth, $minHeight, $maxHeight) { 
	global $options;

	$imageWidth = mt_rand( $minWidth, $maxWidth ); 
	$imageHeight = mt_rand( $minHeight, $maxHeight ); 

	$image = new Imagick();
	$image->newImage( $imageWidth, $imageHeight, new ImagickPixel( getRandomColor() ) );


	$diagonalLength = sqrt( pow( $imageWidth, 2 ) + pow( $imageHeight, 2 ) );

	for ( $i = 0; $i <= 5; $i++ ) {
		$radius = mt_rand( 0, $diagonalLength / 4 );
		$originX = mt_rand( -1 * $radius, $imageWidth + $radius );
		$originY = mt_rand( -1 * $radius, $imageHeight + $radius );
		$perimeterX = $originX + $radius;
		$perimeterY = $originY + $radius;

		$draw = new ImagickDraw(); 
		$draw->setFillColor( getRandomColor() );
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
function getRandomColor() {
	$components = array();
	for ($i = 0; $i <= 2; $i++ ) {
		$components[] = mt_rand( 0, 255 );
	}
	return 'rgb(' . join(', ', $components) . ')';
}

/** 
 * Turn an array into an array of pairs.
 *
 * @param {Array} an array
 * @return {Array} of two-element arrays 
 */
function getPairs( $arr ) { 
	// construct pairs of words
	$pairs = array();
	$count = count( $arr );
	for( $i = 0; $i < $count; $i += 2 )  {
		$pairs[] = array( $arr[$i], $arr[$i+1] );
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
function getRandomLines( $number_desired, $filepath ) { 
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

?>
