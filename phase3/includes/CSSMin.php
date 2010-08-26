<?php

class CSSMin {
	
	/* Constants */
	
	const MAX_EMBED_SIZE = 1024;
	
	/* Static Methods */
	
	/*
	 * Remaps CSS URL paths and automatically embeds data URIs for URL rules preceded by an /* @embed * / comment
	 */
	public static function remap( $source, $path ) {
		// Pre-process for URL rewriting
		$offset = 0;
		while ( preg_match(
			'/((?<embed>\s*\/\*\s*\@embed\s*\*\/)(?<rule>[^\;\}]*))?url\((?<file>[^)]*)\)(?<extra>[^;]*)[\;]?/',
			$source, $match, PREG_OFFSET_CAPTURE, $offset
		) ) {
			$url = $path . '/' . trim( $match['file'][0], "'\"" );
			$file = preg_replace( '/([^\?]*)(.*)/', '$1', $url );
			$embed = $match['embed'][0];
			$rule = $match['rule'][0];
			$extra = $match['extra'][0];
			if ( $match['embed'][1] > 0 && file_exists( $file ) && filesize( $file ) <= self::MAX_EMBED_SIZE ) {
				// If we ever get to PHP 5.3, we should use the Fileinfo extension instead of mime_content_type
				$type = mime_content_type( $file );
				$data = rtrim( base64_encode( file_get_contents( $file ) ), '=' );
				$replacement = "{$rule}url(data:{$type};base64,{$data}){$extra};{$rule}url({$url}){$extra}!ie;";
			} else {
				$replacement = "{$embed}{$rule}url({$url}){$extra};";
			}
			$source = substr_replace( $source, $replacement, $match[0][1], strlen( $match[0][0] ) );
			$offset = $match[0][1] + strlen( $replacement );
		}
		return $source;
	}
	
	/*
	 * As seen at http://www.lateralcode.com/css-minifier/
	 */
	public static function minify( $css ) {
		$css = preg_replace( '#\s+#', ' ', $css );
		$css = preg_replace( '#/\*.*?\*/#s', '', $css );
		$css = str_replace( '; ', ';', $css );
		$css = str_replace( ': ', ':', $css );
		$css = str_replace( ' {', '{', $css );
		$css = str_replace( '{ ', '{', $css );
		$css = str_replace( ', ', ',', $css );
		$css = str_replace( '} ', '}', $css );
		$css = str_replace( ';}', '}', $css );
		return trim( $css );
	}
}