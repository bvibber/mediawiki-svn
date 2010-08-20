<?php
/**
 * @file
 * @ingroup Media
 */

/**
 * Handler for GIF images.
 *
 * @ingroup Media
 */
class GIFHandler extends BitmapHandler {

	const BROKEN_FILE = '0'; // value to store in img_metadata if error extracting metadata.
	
	function getMetadata( $image, $filename ) {
		try {
			$parsedGIFMetadata = BitmapMetadataHandler::GIF( $filename );
		} catch( Exception $e ) {
			// Broken file?
			wfDebug( __METHOD__ . ': ' . $e->getMessage() . "\n" );
			return self::BROKEN_FILE;
		}

		return serialize($parsedGIFMetadata);
	}
	
	function formatMetadata( $image ) {
		$meta = $image->getMetadata();

		if ( !$meta ) {
			return false;
		}
		$meta = unserialize( $meta );
		 if ( !isset( $meta['metadata'] ) || count( $meta['metadata'] ) <= 1 ) {
			return false;
		}

		if ( isset( $meta['metadata']['_MW_GIF_VERSION'] ) ) {
			unset( $meta['metadata']['_MW_GIF_VERSION'] );
		}
		return $this->formatMetadataHelper( $meta['metadata'] );
	}

	function getImageArea( $image, $width, $height ) {
		$ser = $image->getMetadata();
		if ($ser) {
			$metadata = unserialize($ser);
			return $width * $height * $metadata['frameCount'];
		} else {
			return $width * $height;
		}
	}

	function isAnimatedImage( $image ) {
		$ser = $image->getMetadata();
		if ($ser) {
			$metadata = unserialize($ser);
			if( $metadata['frameCount'] > 1 ) return true;
		}
		return false;
	}
	
	function getMetadataType( $image ) {
		return 'parsed-gif';
	}
	
	function isMetadataValid( $image, $metadata ) {
		if ( $metadata === self::BROKEN_FILE ) {
			// Do not repetitivly regenerate metadata on broken file.
			return self::METADATA_GOOD;
		}

		wfSuppressWarnings();
		$data = unserialize( $metadata );
		wfRestoreWarnings();

		if ( !$data || !is_array( $data ) ) {
			wfDebug(__METHOD__ . ' invalid GIF metadata' );
			return self::METADATA_BAD;
		}

		if ( !isset( $data['metadata']['_MW_GIF_VERSION'] )
			|| $data['metadata']['_MW_GIF_VERSION'] != GIFMetadataExtractor::VERSION ) {
			wfDebug(__METHOD__ . ' old but compatible GIF metadata' );
			return self::METADATA_COMPATIBLE;
		}
		return self::METADATA_GOOD;
	}


	function getLongDesc( $image ) {
		global $wgUser, $wgLang;
		$sk = $wgUser->getSkin();
		$original = parent::getLongDesc( $image );

		wfSuppressWarnings();
		$metadata = unserialize($image->getMetadata());
		wfRestoreWarnings();
		
		if (!$metadata || $metadata['frameCount'] <=  1)
			return $original;
		
		$info = array();
		$info[] = substr( $original, 1, strlen( $original )-2 );
		
		if ($metadata['looped'])
			$info[] = wfMsgExt( 'file-info-gif-looped', 'parseinline' );
		
		if ($metadata['frameCount'] > 1)
			$info[] = wfMsgExt( 'file-info-gif-frames', 'parseinline', $metadata['frameCount'] );
		
		if ($metadata['duration'])
			$info[] = $wgLang->formatTimePeriod( $metadata['duration'] );
		
		$infoString = $wgLang->commaList( $info );
		
		return "($infoString)";
	}
}
