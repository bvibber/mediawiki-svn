<?php

class OggHandler extends MediaHandler {
	const OGG_METADATA_VERSION = 1;

	var $videoTypes = array( 'Theora' );
	var $audioTypes = array( 'Vorbis', 'Speex', 'FLAC' );

	function isEnabled() {
		return true;
	}

	function getParamMap() {
		// TODO: add thumbtime, noplayer
		return array( 'img_width' => 'width' );
	}

	function validateParam( $name, $value ) {
		// TODO
		return true;
	}

	function makeParamString( $params ) {
		// No parameters just yet, the thumbnails are always full-size
		return '';
		/*
		$s = '';
		foreach ( $params as $name => $value ) {
			if ( $s !== '' ) {
				$s .= '-';
			}
			$s .= "$name=$value";
		}*/
	}

	function parseParamString( $str ) {
		// TODO
		return array();
	}

	function normaliseParams( $image, &$params ) {
		// TODO
		return true;
	}

	function getImageSize( $file, $path, $metadata = false ) {
		// Just return the size of the first video stream
		if ( $metadata === false ) {
			$metadata = $file->getMetadata();
		}
		$metadata = $this->unpackMetadata( $metadata );
		if ( isset( $metadata['error'] ) ) {
			return false;
		}
		foreach ( $metadata['streams'] as $stream ) {
			if ( in_array( $stream['type'], $this->videoTypes ) ) {
				return array( 
					$stream['header']['PICW'], 
					$stream['header']['PICH']
				);
			}
		}
		return array( false, false );
	}

	function getMetadata( $image, $path ) {
		$metadata = array( 'version' => self::OGG_METADATA_VERSION );

		if ( !class_exists( 'File_Ogg' ) ) {
			require( 'File/Ogg.php' );
		}	

		try {
			$f = new File_Ogg( $path );
			$streams = array();
			foreach ( $f->listStreams() as $streamType => $streamIDs ) {
				foreach ( $streamIDs as $streamID ) {
					$stream = $f->getStream( $streamID );
					$streams[$streamID] = array(
						'serial' => $stream->getSerial(),
						'group' => $stream->getGroup(),
						'type' => $stream->getType(),
						'vendor' => $stream->getVendor(),
						'length' => $stream->getLength(),
						'size' => $stream->getSize(),
						'header' => $stream->getHeader(),
						'comments' => $stream->getComments()
					);
				}
			}
			$metadata['streams'] = $streams;
			$metadata['length'] = $f->getLength();
		} catch ( PEAR_Exception $e ) {
			// File not found, invalid stream, etc.
			$metadata['error'] = array(
				'message' => $e->getMessage(),
				'code' => $e->getCode()
			);
		}
		return serialize( $metadata );
	}

	function unpackMetadata( $metadata ) {
		$unser = @unserialize( $metadata );
		if ( isset( $unser['version'] ) && $unser['version'] == self::OGG_METADATA_VERSION ) {
			return $unser;
		} else {
			return false;
		}
	}

	function getMetadataType( $image ) {
		return 'ogg';
	}

	function isMetadataValid( $image, $metadata ) {
		return $this->unpackMetadata( $metadata ) !== false;
	}

	function getThumbType( $ext, $mime ) {
		return array( 'jpg', 'image/jpeg' );
	}
	
	function doTransform( $file, $dstPath, $dstUrl, $params, $flags = 0 ) {
		global $wgFFmpegLocation;

		// Hack for miscellaneous callers
		global $wgOut;
		$this->setHeaders( $wgOut );

		$width = $params['width'];
		$srcWidth = $file->getWidth();
		$srcHeight = $file->getHeight();
		$height = $srcWidth == 0 ? $srcHeight : $width * $srcHeight / $srcWidth;
		$length = $this->getLength( $file );

		if ( $srcHeight == 0 || $srcWidth == 0 ) {
			// Make audio player
			$icon = $file->iconThumb();
			if ( empty( $params['width'] ) ) {
				$width = 200;
			} else {
				$width = $params['width'];
			}
			$height = $icon->getHeight();
			return new OggAudioDisplay( $file, $file->getURL(), $icon->getUrl(), $width, $height, $length );
		}

		if ( $flags & self::TRANSFORM_LATER ) {
			return new OggVideoDisplay( $file, $file->getURL(), $dstUrl, $width, $height, $length );
		}

		wfMkdirParents( dirname( $dstPath ) );

		wfDebug( "Creating video thumbnail at $dstPath\n" );

		$cmd = wfEscapeShellArg( $wgFFmpegLocation ) . 
			' -i ' . wfEscapeShellArg( $file->getPath() ) . 
			# MJPEG, that's the same as JPEG except it's supported by the windows build of ffmpeg
			# No audio, one frame
			' -f mjpeg -an -vframes 1' .
			# Seek to midpoint, it tends to be more interesting than the fade in at the start
			' -ss ' . intval( $length / 2 ) . ' ' .
			wfEscapeShellArg( $dstPath ) . ' 2>&1';

		$retval = 0;
		$returnText = wfShellExec( $cmd, $retval );

		if ( $retval ) {
			// Filter nonsense
			$lines = explode( "\n", str_replace( "\r\n", "\n", $returnText ) );
			if ( substr( $lines[0], 0, 6 ) == 'FFmpeg' ) {
				for ( $i = 1; $i < count( $lines ); $i++ ) {
					if ( substr( $lines[$i], 0, 2 ) != '  ' ) {
						break;
					}
				}
				$lines = array_slice( $lines, $i );
			}
			// Return error box
			return new MediaTransformError( 'thumbnail_error', $width, $height, implode( "\n", $lines ) );
		}
		return new OggVideoDisplay( $file, $file->getURL(), $dstUrl, $width, $height, $length );
	}

	function canRender() { return true; }
	function mustRender( $file ) { return true; }

	/*
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
			self::addMeta( $formatted, 'visible', 'ogg', 'size', $stream['size'], $n );

			foreach ( $stream['header'] as $name => $value ) {
				self::addMeta( $formatted, 'visible', $type, $name, $value, $n );
				$visible[$prefix . $name] = wfEscapeWikiText( $value );
			}
			foreach ( $stream['comments'] as $name => $value ) {
				self::addMeta( $formatted, 'visible', $type, $name, $value, $n );
			}
		}
		return $formatted;
	}*/

	function getLength( $file ) {
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return 0;
		} else {
			return $metadata['length'];
		}
	}

	function getStreamTypes( $file ) {
		$streamTypes = '';
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}
		foreach ( $metadata['streams'] as $stream ) {
			$streamTypes[$stream['type']] = true;
		}
		return array_keys( $streamTypes );
	}

	function getShortDesc( $file ) {
		global $wgLang;
		wfLoadExtensionMessages( 'OggHandler' );
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}
		if ( array_intersect( $streamTypes, $this->videoTypes ) ) {
			// Count multiplexed audio/video as video for short descriptions
			$msg = 'ogg-short-video';
		} elseif ( array_intersect( $streamTypes, $this->audioTypes ) ) {
			$msg = 'ogg-short-audio';
		} else {
			$msg = 'ogg-short-general';
		}
		return wfMsg( $msg, implode( '/', $streamTypes ), 
			$wgLang->formatTimePeriod( $this->getLength( $file ) ) );
	}

	function getLongDesc( $file ) {
		global $wgLang;
		wfLoadExtensionMessages( 'OggHandler' );
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			$unpacked = $this->unpackMetadata( $file->getMetadata() );
			return wfMsg( 'ogg-long-error', $unpacked['error']['message'] );
		}
		if ( array_intersect( $streamTypes, $this->videoTypes ) ) {
			if ( array_intersect( $streamTypes, $this->audioTypes ) ) {
				$msg = 'ogg-long-multiplexed';
			} else {
				$msg = 'ogg-long-video';
			}
		} elseif ( array_intersect( $streamTypes, $this->audioTypes ) ) {
			$msg = 'ogg-long-audio';
		} else {
			$msg = 'ogg-long-general';
		}
		$size = 0;
		$unpacked = $this->unpackMetadata( $file->getMetadata() );
		if ( !$unpacked || isset( $metadata['error'] ) ) {
			$length = 0;
		} else {
			$length = $this->getLength( $file );
			foreach ( $unpacked['streams'] as $stream ) {
				$size += $stream['size'];
			}
		}
		$bitrate = $length == 0 ? 0 : $size / $length * 8;
		return wfMsg( $msg, implode( '/', $streamTypes ),
			$wgLang->formatTimePeriod( $length ), 
			$wgLang->formatBitrate( $bitrate ),
			$wgLang->formatNum( $file->getWidth() ),
			$wgLang->formatNum( $file->getHeight() )
	   	);
	}

	function getDimensionsString( $file ) {
		global $wgLang;
		wfLoadExtensionMessages( 'OggHandler' );
		if ( $file->getWidth() ) {
			return wfMsg( 'video-dims', $wgLang->formatTimePeriod( $this->getLength( $file ) ), 
				$wgLang->formatNum( $file->getWidth() ), 
				$wgLang->formatNum( $file->getHeight() ) );
		} else {
			return $wgLang->formatTimePeriod( $this->getLength( $file ) );
		}
	}

	function setHeaders( $out ) {
		global $wgScriptPath, $wgOggScriptVersion, $wgCortadoJarFile;
		if ( $out->hasHeadItem( 'OggHandler' ) ) {
			return;
		}

		wfLoadExtensionMessages( 'OggHandler' );

		$msgNames = array( 'ogg-play', 'ogg-pause', 'ogg-stop', 'ogg-no-player',
			'ogg-player-videoElement', 'ogg-player-oggPlugin', 'ogg-player-cortado', 'ogg-player-vlc-mozilla', 
			'ogg-player-vlc-activex', 'ogg-player-quicktime-mozilla', 'ogg-player-quicktime-activex',
			'ogg-player-thumbnail', 'ogg-player-selected', 'ogg-use-player', 'ogg-more', 'ogg-download',
	   		'ogg-desc-link', 'ogg-dismiss' );
		$msgValues = array_map( 'wfMsg', $msgNames );
		$jsMsgs = Xml::encodeJsVar( (object)array_combine( $msgNames, $msgValues ) );
		$cortadoUrl = $wgCortadoJarFile;
		if( substr( $cortadoUrl, 0, 1 ) != '/'
			&& substr( $cortadoUrl, 0, 4 ) != 'http' ) {
			$cortadoUrl = "$wgScriptPath/extensions/OggHandler/$cortadoUrl";
		}
		$encCortadoUrl = Xml::encodeJsVar( $cortadoUrl );
		$encSmallFileUrl = Xml::encodeJsVar( "$wgScriptPath/extensions/OggHandler/null_file" );

		$out->addHeadItem( 'OggHandler', <<<EOT
<script type="text/javascript" src="$wgScriptPath/extensions/OggHandler/OggPlayer.js?$wgOggScriptVersion"></script>
<script type="text/javascript">
wgOggPlayer.msg = $jsMsgs;
wgOggPlayer.cortadoUrl = $encCortadoUrl;
wgOggPlayer.smallFileUrl = $encSmallFileUrl;
</script>
<style type="text/css">
.ogg-player-options {
	border: solid 1px #ccc;
	padding: 2pt;
	text-align: left;
	font-size: 10pt;
}
</style>
EOT
		);
		
	}

	function parserTransformHook( $parser, $file ) {
		if ( isset( $parser->mOutput->hasOggTransform ) ) {
			return;
		}
		$parser->mOutput->hasOggTransform = true;
		$parser->mOutput->addOutputHook( 'OggHandler' );
	}

	static function outputHook( $outputPage, $parserOutput, $data ) {
		$instance = MediaHandler::getHandler( 'application/ogg' );
		if ( $instance ) {
			$instance->setHeaders( $outputPage );
		}
	}
}

class OggTransformOutput extends MediaTransformOutput {
	static $serial = 0;

	function __construct( $file, $videoUrl, $thumbUrl, $width, $height, $length, $isVideo ) {
		$this->file = $file;
		$this->videoUrl = $videoUrl;
		$this->url = $thumbUrl;
		$this->width = round( $width );
		$this->height = round( $height );
		$this->length = round( $length );
		$this->isVideo = $isVideo;
	}

	function toHtml( $options = array() ) {
		wfLoadExtensionMessages( 'OggHandler' );
		if ( count( func_get_args() ) == 2 ) {
			throw new MWException( __METHOD__ .' called in the old style' );
		}

		OggTransformOutput::$serial++;

		if ( substr( $this->videoUrl, 0, 4 ) != 'http' ) {
			global $wgServer;
			$encUrl = Xml::encodeJsVar( $wgServer . $this->videoUrl );
		} else {
			$encUrl = Xml::encodeJsVar( $this->videoUrl );
		}
		$length = intval( $this->length );
		$width = intval( $this->width );
		$height = intval( $this->height );
		$alt = empty( $options['alt'] ) ? '' : $options['alt'];
		$attribs = array( 'src' => $this->url );
		if ( $this->isVideo ) {
			$msgStartPlayer = wfMsg( 'ogg-play-video' );
			$attribs['width'] = $width;
			$attribs['height'] = $height;
			$playerHeight = $height;
		} else {
			$msgStartPlayer = wfMsg( 'ogg-play-sound' );
			$playerHeight = 0;
			// Don't add width and height to the icon image, it won't match its true size
		}

		$thumb = Xml::element( 'img', $attribs, null );
		if ( !empty( $options['desc-link'] ) ) {
			$linkAttribs = $this->getDescLinkAttribs( $alt );
			$thumb = Xml::tags( 'a', $linkAttribs, $thumb );
			$encLink = Xml::encodeJsVar( $linkAttribs['href'] );
		} else {
			// We don't respect the file-link option, click-through to download is not appropriate
			$encLink = 'false';
		}
		$thumb .= "<br/>\n";

		$id = "ogg_player_" . OggTransformOutput::$serial;

		$s = Xml::tags( 'div', array( 'id' => $id, /*'align' => 'center',*/ 'style' => 'width: ' . $width . 'px' ), 
			$thumb .
			Xml::element( 'button', 
				array(
					'onclick' => "wgOggPlayer.init(false, '$id', $encUrl, $width, $playerHeight, $length, $encLink);",
				), 
				$msgStartPlayer
			)
		);
		return $s;
	}
}

class OggVideoDisplay extends OggTransformOutput {
	function __construct( $file, $videoUrl, $thumbUrl, $width, $height, $length ) {
		parent::__construct( $file, $videoUrl, $thumbUrl, $width, $height, $length, true );
	}
}

class OggAudioDisplay extends OggTransformOutput {
	function __construct( $file, $videoUrl, $iconUrl, $width, $height, $length ) {
		parent::__construct( $file, $videoUrl, $iconUrl, $width, $height, $length, false );
	}
}

?>
