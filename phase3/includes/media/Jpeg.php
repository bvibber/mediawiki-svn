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
}
