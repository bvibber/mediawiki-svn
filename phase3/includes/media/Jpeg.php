<?php
/**
 * @file
 * @ingroup Media
 */

/** JPEG specific handler.
 * Inherits most stuff from BitmapHandler, just here to do the metadata handler differently
 * @ingroup Media
 */
class JpegHandler extends BitmapHandler {

	function getMetadata ( $image, $filename ) {
		try {
			$meta = BitmapMetadataHandler::newForJpeg( $filename );
			$temp = $meta->getMetadataArray();
			if ( $temp ) {
				$temp['MEDIAWIKI_EXIF_VERSION'] = Exif::version();
				return serialize( $temp );
			} else {
				return '0';
			}
		}
		catch ( MWException $e ) {
			// BitmapMetadataHandler throws an exception in certain exceptional cases like if file does not exist.
			wfDebug( __METHOD__ . ': ' . $e->getMessage() . "\n" );
			return '0';
		}
	}

	function convertMetadataVersion( $metadata, $version = 1 ) {
		// basically flattens arrays.
		$version = explode(';', $version, 2);
		$version = intval($version[0]);
		if ( $version < 1 || $version >= 2 ) {
			return $metadata;
		}

		if ( !is_array( $metadata ) ) {
			$metadata = unserialize( $metadata );
		}
		if ( !isset( $metadata['MEDIAWIKI_EXIF_VERSION'] ) || $metadata['MEDIAWIKI_EXIF_VERSION'] != 2 ) {
			return $metadata;
		}

		foreach ( $metadata as &$val ) {
			if ( is_array( $val ) ) {
				$val = formatExif::flattenArray( $val );
			}
		}
		$metadata['MEDIAWIKI_EXIF_VERSION'] = 1;
		return $metadata;
	}
}
