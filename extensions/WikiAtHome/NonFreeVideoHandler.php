<?php
/*
 * creates an stub for non-free video that is waiting to be transcoded (just redirects to the real file)
 */
class NonFreeVideoHandler extends MediaHandler {
	const METADATA_VERSION = 1;

	static $magicDone = false;

	function isEnabled() {
		return true;
	}

	static function registerMagicWords( &$magicData, $code ) {
		wfLoadExtensionMessages( 'WikiAtHome' );
		return true;
	}

	function getParamMap() {
		wfLoadExtensionMessages( 'WikiAtHome' );
		/*return array(
			'img_width' => 'width',
			'ogg_noplayer' => 'noplayer',
			'ogg_noicon' => 'noicon',
			'ogg_thumbtime' => 'thumbtime',
		);*/
	}

	function validateParam( $name, $value ) {
		if ( $name == 'thumbtime' ) {
			if ( $this->parseTimeString( $value ) === false ) {
				return false;
			}
		}
		return true;
	}

	function parseParamString( $str ) {
		$m = false;
		if ( preg_match( '/^seek=(\d+)$/', $str, $m ) ) {
			return array( 'thumbtime' => $m[0] );
		}
		return array();
	}

	function normaliseParams( $image, &$params ) {
		if ( isset( $params['thumbtime'] ) ) {
			$length = $this->getLength( $image );
			$time = $this->parseTimeString( $params['thumbtime'] );
			if ( $time === false ) {
				return false;
			} elseif ( $time > $length - 1 ) {
				$params['thumbtime'] = $length - 1;
			} elseif ( $time <= 0 ) {
				$params['thumbtime'] = 0;
			}
		}
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
		if( isset($metadata['video'] )){
			foreach ( $metadata['video'] as $stream ) {
				return array(
					$stream->width,
					$stream->height
				);
			}
		}
		return array( false, false );
	}
	function makeParamString( $params ) {
		if ( isset( $params['thumbtime'] ) ) {
			$time = $this->parseTimeString( $params['thumbtime'] );
			if ( $time !== false ) {
				return 'seek=' . $time;
			}
		}
		return 'mid';
	}

	function getMetadata( $image, $path ) {
		global $wgffmpeg2theora;
		$metadata = array( 'version' => self::METADATA_VERSION );
		//if we have  fffmpeg2theora
		if( $wgffmpeg2theora && is_file( $wgffmpeg2theora ) ){

			$cmd = wfEscapeShellArg( $wgffmpeg2theora ) . ' ' . wfEscapeShellArg ( $path ). ' --info';
			wfProfileIn( 'ffmpeg2theora' );
			wfDebug( __METHOD__.": $cmd\n" );
			$json_meta_str = wfShellExec( $cmd );
			wfProfileOut( 'ffmpeg2theora' );

			$json_file_meta = json_decode( $json_meta_str );
			if( $json_file_meta ){
				foreach($json_file_meta as $k=>$v){
					if( !isset( $metadata[ $k ]))
						$metadata[ $k ] = $v;
				}
			}else{
				$metadata['error'] = array(
					'message' => 'could not parse ffmpeg2theora output',
					'code' => 2
				);
			}
		}else{
			$metadata['error'] = array(
				'message' => 'missing ffmpeg2theora<br> check that ffmpeg2theora is installed and that $wgffmpeg2theora points to its location',
				'code' => 1
			);
		}
		return serialize( $metadata );
	}
	function unpackMetadata( $metadata ) {
		$unser = @unserialize( $metadata );
		if ( isset( $unser['version'] ) && $unser['version'] == self::METADATA_VERSION ) {
			return $unser;
		} else {
			return false;
		}
	}

	function doTransform( $file, $dstPath, $dstUrl, $params, $flags = 0 ) {
		global $wgFFmpegLocation;

		//check the $params range for now lets do two derivatives one for 400x300 and
		//another at the source resolution

		//get the job manager .. check status and output current state or defer to oggHanndler_body for output
		$jobSet = new WahJobMannager();
		if( $jobSet->isDone() ){
			//we should be able to output ogg then:
		}

		//check jobset table for this

			//no derivative then add the jobset


		//do oggHandler render on Derivative

		return new NonFreeTransformOutput( $file, $file->getURL(), $dstUrl, $width, $height, $length, $dstPath, $noIcon);
		//check the
	}

	function getMetadataType( $image ) {
		return 'vid';
	}

	function isMetadataValid( $image, $metadata ) {
		return $this->unpackMetadata( $metadata ) !== false;
	}

	function getThumbType( $ext, $mime ) {
		return array( 'jpg', 'image/jpeg' );
	}

	function canRender( $file ) { return true; }
	function mustRender( $file ) { return true; }

	function getLength( $file ) {
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return 0;
		} else {
			return $metadata['duration'];
		}
	}
	function getStreamTypes( $file ) {
		$streamTypes = array();
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}
		if(isset($metadata['video'])){
			foreach ( $metadata['video'] as $stream ) {
				$streamTypes[ $stream->codec ] = true;
			}
		}
		if(isset($metadata['audio'])){
			foreach ( $metadata['audio'] as $stream ) {
				$streamTypes[ $stream->codec ] = true;
			}
		}
		return array_keys( $streamTypes );
	}
	function getShortDesc( $file ) {
		global $wgLang;
		wfLoadExtensionMessages( 'WikiAtHome' );
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}
		if ( isset( $metadata['video'] ) && $metadata['video'] ) {
			// Count multiplexed audio/video as video for short descriptions
			$msg = 'wah-short-video';
		} elseif ( isset( $metadata['audio'] ) && $metadata['audio'] ) {
			$msg = 'wah-short-audio';
		} else {
			$msg = 'wah-short-general';
		}
		return wfMsg( $msg, implode( '/', $streamTypes ),
			$wgLang->formatTimePeriod( $this->getLength( $file ) ) );
	}

	function getLongDesc( $file ) {
		global $wgLang;
		wfLoadExtensionMessages( 'WikiAtHome' );
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			$unpacked = $this->unpackMetadata( $file->getMetadata() );
			return wfMsg( 'wah-long-error', $unpacked['error']['message'] );
		}
		if ( isset( $metadata['video'] ) && $metadata['video'] ) {
			if ( isset( $metadata['audio'] ) && $metadata['audio'] ) {
				$msg = 'wah-long-multiplexed';
			} else {
				$msg = 'wah-long-video';
			}
		} elseif ( isset( $metadata['audio'] ) && $metadata['audio'] ) {
			$msg = 'wah-long-audio';
		} else {
			$msg = 'wah-long-general';
		}
		$size = 0;
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			$length = 0;
		} else {
			$length = $this->getLength( $file );
			$size = $metadata['size'];
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
		wfLoadExtensionMessages( 'WikiAtHome' );
		if ( $file->getWidth() ) {
			return wfMsg( 'video-dims', $wgLang->formatTimePeriod( $this->getLength( $file ) ),
				$wgLang->formatNum( $file->getWidth() ),
				$wgLang->formatNum( $file->getHeight() ) );
		} else {
			return $wgLang->formatTimePeriod( $this->getLength( $file ) );
		}
	}
}

class MediaQueueTransformOutput extends MediaTransformOutput {
	static $serial = 0;

	function __construct( $file, $videoUrl, $thumbUrl, $width, $height, $length, $isVideo,
		$path, $noIcon = false, $offset )
	{
		$this->file = $file;
		$this->videoUrl = $videoUrl;
		$this->url = $thumbUrl;
		$this->width = round( $width );
		$this->height = round( $height );
		$this->length = round( $length );
		$this->offset = round( $offset );
		$this->isVideo = $isVideo;
		$this->path = $path;
		$this->noIcon = $noIcon;
	}

	function toHtml( $options = array() ) {


		wfLoadExtensionMessages( 'WikiAtHome' );
		return time();
	}
}
?>