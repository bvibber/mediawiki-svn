<?php

class OggHandler extends MediaHandler {
	const OGG_METADATA_VERSION = 1;

	function isEnabled() {
		if ( !class_exists( 'File_Ogg' ) ) {
			return include( 'File/Ogg.php' );
		} else {
			return true;
		}
	}

	function validateParam( $name, $value ) {
		// TODO
		return false;
	}

	function makeParamString( $params ) {
		$s = '';
		foreach ( $params as $name => $value ) {
			if ( $s !== '' ) {
				$s .= '-';
			}
			$s .= "$name=$value";
		}
	}

	function parseParamString( $str ) {
		// TODO
		return array();
	}

	function normaliseParams( $image, &$params ) {
		// TODO
		return true;
	}

	function getImageSize( $image, $path ) {
		// TODO
		return false;
	}

	function getMetadata( $image, $path ) {
		$metadata = array( 'version' => self::OGG_METADATA_VERSION );
		try {
			$f = new File_Ogg( $path );
			$streams = array();
			foreach ( $f->listStreams() as $streamType => $streamIDs ) {
				foreach ( $streamIDs as $streamID ) {
					$stream = $f->getStream( $streamID );
					$streams[$streamID] = array(
						'type' => $stream->getType(),
						'vendor' => $stream->getVendor(),
						'length' => $stream->getLength(),
						'header' => $stream->getHeader(),
						'comments' => $stream->getComments()
					);
				}
			}
			$metadata['streams'] = $streams;
		} catch ( PEAR_Exception $e ) {
			// File not found, invalid stream, etc.
			$metadata['error'] = array(
				'message' => $e->getMessage(),
				'code' => $e->getCode()
			);
		}
		return serialize( $metadata );
	}

	function getMetadataType( $image ) {
		return 'ogg';
	}

	function isMetadataValid( $image, $metadata ) {
		$unser = @unserialize( $metadata );
		return isset( $unser['version'] ) && $unser['version'] === self::OGG_METADATA_VERSION;
	}
	
	function doTransform( $image, $dstPath, $dstUrl, $params, $flags = 0 ) {
		// TODO
		return new MediaTransformError( 'Not yet implemented', 200 );
	}

	function canRender() {
		// TODO
		return false;
	}

	function formatMetadata( $image, $metadata ) {
		if ( !$this->isMetadataValid( $image, $metadata ) ) {
			return false;
		}
		$metadata = unserialize( $metadata );
		$formatted = array();
		if ( isset( $metadata['error'] ) ) {
			self::addMeta( $formatted, 'visible', 'ogg', 'error', $metadata['error']['message'] );
			return $formatted;
		}
		$formatted = array();
		$n = 0;
		foreach ( $metadata['streams'] as $stream ) {
			$prefix = "Stream $n ";
			$type = strtolower( $stream['type'] );
			self::addMeta( $formatted, 'visible', 'ogg', 'type', $stream['type'], $n );
			self::addMeta( $formatted, 'visible', 'ogg', 'vendor', $stream['vendor'], $n );
			self::addMeta( $formatted, 'visible', 'ogg', 'length', $stream['length'], $n );

			foreach ( $stream['header'] as $name => $value ) {
				self::addMeta( $formatted, 'visible', $type, $name, $value, $n );
				$visible[$prefix . $name] = wfEscapeWikiText( $value );
			}
			foreach ( $stream['comments'] as $name => $value ) {
				self::addMeta( $formatted, 'visible', $type, $name, $value, $n );
			}
		}
		return $formatted;
	}


}




?>
