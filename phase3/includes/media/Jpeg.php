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
			$meta = BitmapMetadataHandler::Jpeg( $filename );
			if ( $meta ) {
				$meta['MEDIAWIKI_EXIF_VERSION'] = Exif::version();
				return serialize( $meta );
			} else {
				/* FIXME, this should probably be something else to do versioning
				 with older files that say have no exif, but have xmp */
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

		// Treat Software as a special case because in can contain
		// an array of (SoftwareName, Version).
		if (isset( $metadata['Software'] ) 
			&& is_array( $metadata['Software'] ) 
			&& is_array( $metadata['Software'][0])
			&& isset( $metadata['Software'][0][0] )
			&& isset( $metadata['Software'][0][1])
		 ) {
			$metadata['Software'] = $metadata['Software'][0][0] . ' (Version '
				. $metadata['Software'][0][1] . ')';
		}

		foreach ( $metadata as &$val ) {
			if ( is_array( $val ) ) {
				$val = FormatMetadata::flattenArray( $val );
			}
		}
		$metadata['MEDIAWIKI_EXIF_VERSION'] = 1;
		return $metadata;
	}
}
