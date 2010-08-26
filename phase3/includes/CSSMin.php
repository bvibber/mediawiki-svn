<?php

class CSSMin {
	
	/* Constants */
	
	/**
	 * Maximum file size to still qualify for in-line embedding as a data-URI
	 * 
	 * 24,576 is used because Internet Explorer has a 32,768 byte limit for data URIs, which when base64 encoded will
	 * result in a 1/3 increase in size.
	 */
	const EMBED_SIZE_LIMIT = 24576;
	
	/* Static Methods */
	
	/*
	 * Remaps CSS URL paths and automatically embeds data URIs for URL rules preceded by an /* @embed * / comment
	 * 
	 * @param $source string CSS data to remap
	 * @param $path string File path where the source was read from
	 */
	public static function remap( $source, $path ) {
		$pattern = '/((?<embed>\s*\/\*\s*\@embed\s*\*\/)(?<rule>[^\;\}]*))?url\((?<file>[^)]*)\)(?<extra>[^;]*)[\;]?/';
		$offset = 0;
		while ( preg_match( $pattern, $source, $match, PREG_OFFSET_CAPTURE, $offset ) ) {
			// Remove single or double quotes
			$url = trim( $match['file'][0], "'\"" );
			// Only proceed if the URL is to a local file
			if ( !preg_match( '/^[a-zA-Z]*\:\/\//', $url ) ) {
				// Shortcuts
				$embed = $match['embed'][0];
				$rule = $match['rule'][0];
				$extra = $match['extra'][0];
				// Strip query string from URL
				$file = "{$path}/" . preg_replace( '/([^\?]*)(.*)/', '$1', $url );
				// Only proceed if we can access the file
				if ( file_exists( $file ) ) {
					// Add version parameter as a time-stamp in ISO 8601 format, using Z for the timezone, meaning GMT
					$url = "{$file}?" . gmdate( 'Y-m-d\TH:i:s\Z', round( filemtime( $file ), -2 ) );
					// Detect when URLs were preceeded with embed tags, and also verify file size is below the limit
					if ( $match['embed'][1] > 0 && filesize( $file ) < self::EMBED_SIZE_LIMIT ) {
						// If we ever get to PHP 5.3, we should use the Fileinfo extension instead of mime_content_type
						$type = mime_content_type( $file );
						// Strip off any trailing = symbols (makes browsers freak out)
						$data = rtrim( base64_encode( file_get_contents( $file ) ), '=' );
						// Build 2 CSS properties; one which uses a base64 encoded data URI in place of the @embed
						// comment to try and retain line-number integrity , and the other with a remapped an versioned
						// URL and an Internet Explorer hack making it ignored in all browsers that support data URIs
						$replacement = "{$rule}url(data:{$type};base64,{$data}){$extra};{$rule}url({$url}){$extra}!ie;";
					} else {
						// Build a CSS property with a remapped and versioned URL
						$replacement = "{$embed}{$rule}url({$url}){$extra};";
					}
					// Perform replacement on the source
					$source = substr_replace( $source, $replacement, $match[0][1], strlen( $match[0][0] ) );
					// Move the offset to the end of the replacement in the source
					$offset = $match[0][1] + strlen( $replacement );
					continue;
				}
			}
			// Move the offset to the end of the match, leaving it alone
			$offset = $match[0][1] + strlen( $match[0][0] );
		}
		return $source;
	}
	
	/*
	 * Removes whitespace from CSS data
	 * 
	 * @param $source string CSS data to minify
	 */
	public static function minify( $css ) {
		return trim(
			str_replace(
				array( '; ', ': ', ' {', '{ ', ', ', '} ', ';}' ),
				array( ';', ':', '{', '{', ',', '}', '}' ),
				preg_replace( array( '/\s+/', '/\/\*.*?\*\//s' ), array( ' ', '' ), $css )
			)
		);
	}
}