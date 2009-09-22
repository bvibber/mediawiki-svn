<?php

define('OGGCHOP_META_VERSION', 1);

define('OGGCHOP_META_EXT', '.meta');

$oggDir = dirname(__FILE__);

//require the PEAR php module
ini_set( 'include_path',
	"$oggDir/PEAR/File_Ogg" .
	PATH_SEPARATOR .
	ini_get( 'include_path' ));

class OggChop {
	//initial variable values:
	var $meta = false;
	var $loadWaitCount = 0;

	//header values:
	var $contentLength = 0;

	function __construct( $oggPath ){
		$this->oggPath = $oggPath;
	}
	/*
	 * play takes in start_sec and end_sec and sends out packet
	 *
	 * @param float $start_sec ( start time in float seconds)
	 * @param floast $end_sec (optional end time in float)
	 */
	function play($start_sec=false, $end_sec = false){
		//make sure we have the metadata ready:
		$this->loadMeta();

		//get http byte range headers::
		$this->getByteRangeRequest();

		//if both start and end are false send the full file:
		if(!$start_sec && !$end_sec){
			//set from full file context::
			$this->contentLength = filesize( $this->oggPath );
			$this->contentRange = array(
				's' => 0,
				'e' => $this->contentLength -1,
				't' => $this->contentLength
			);
			$this->duration = $this->getMeta('duration');
			$this->sendHeaders();
			//output the full file:

			//turn off output buffering
			while (ob_get_level() > 0) {
		   		ob_end_flush();
			}
			@readfile( $this->oggPath );
			//exit the application (might be a cleaner way to do this)
			die();
		}else{
			$kEnd = false;
			//we have a temporal request
			if(!$start_sec || $start_sec < 0)
				$start_sec = 0;

			if(!$end_sec || $end_sec > $this->getMeta('duration')){
				$end_sec = $this->getMeta('duration');
				$kEnd = array(
					$this->getMeta('duration'),
					filesize( $this->oggPath )
				);
			}

			//set the duration:
			$this->duration = $end_sec - $start_sec;

			//set the content size for the segment:
			$kStart = $this->getKeyFrameByteFromTime( $start_sec );
			if( ! $kEnd )
				$kEnd = $this->getKeyFrameByteFromTime( $end_sec , false);

			//debug output:
			/*
			print_r($this->meta['theoraKeyFrameInx']);
			print "Start : ". print_r($kStart, true) ."\n";
			print "End Byte:" . print_r($kEnd, true) . "\n";
			die();

			@@todo build the ogg skeleton header
			1) that gives the offset between
			// $kStart time and the requested time.

			//for now just start output at the given byte range
			$this->outputByteRange( $kStart[1], $kEnd[1]);
		}

	}
	function getKeyFrameByteFromTime( $reqTime, $prev_key=true ){
		//::binary array search goes here::

		//linear search (may be faster in some cases (like start of the file seeks)
		$timeDiff = $this->getMeta('duration');
		reset($this->meta['theoraKeyFrameInx']);
		$pByte = current( $this->meta['theoraKeyFrameInx'] );
		$pKtime = key( $this->meta['theoraKeyFrameInx'] );
		foreach($this->meta['theoraKeyFrameInx'] as $kTime => $byte){
			if($kTime > $reqTime)
				break;
			$pByte = $byte;
			$pKtime = $kTime;
		}
		//return the keyframe array by default the prev key
		if($prev_key){
			return array($pKtime, $pByte);
		}else{
			return array($kTime, $byte);
		}
	}
	/*
	 * outputByteRange
	 */
	function outputByteRange($startByte, $endByte = null){
		//media files use large chunk size:
		$chunkSize = 32768;
		$this->fp = fopen( $this->oggPath, 'r');
		fseek($this->fp, $startByte);
		while (! feof($this->fp))
		{
			if( $endByte != null ){
				if( ftell( $this->fp ) + $chunkSize > $endByte ){
					$read_amount = ( ftell ( $this->fp ) + $chunkSize ) - $endByte;
					echo fread($this->fp, $read_amount);
					break;
				}
			}
			echo fread($this->fp, $chunkSize);
		}
	}
	function getByteRangeRequest(){
		//set local vars for byte range request handling
	}
	function sendHeaders(){
		header ("Accept-Ranges: bytes");

		//set range conditional headers:
		if( $this->contentLength )
			header ( "Content-Length: " . $this->contentLength );

		//set the X-content duration:
		if( $this->duration )
			header ( "X-Content-Duration: " . $this->duration );

		//set content range see spec:
		// http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.16
		if( $this->contentRange	)
			header ( "Content-Range: bytes " .
				$this->contentRange['s'] . "-" .
				$this->contentRange['e'] . "/" .
				$this->contentRange['t']
			);

		//constant headers (for video)
		if( isset($this->meta['height']) )
			header( "X-Content-Video-Height: " . $this->meta['height'] );

		if( isset($this->meta['width']) )
			header( "X-Content-Video-Width: " . $this->meta['width'] );

		//set mime type (only video for now)
		header ("Content-Type: video/ogg");

	}
	function sendByteRange( $startByte, $endByte){

	}
	/*
	 * getMeta (returns the value of a metadata key)
	 */
	function getMeta( $key ){
		if( !$this->meta ){
			$this->loadMeta();
		}
		if( isset( $this->meta[$key] ) )
			return $this->meta[ $key ];
		return false;
	}
	function loadMeta(){
		//load from the file:
		if( is_file( $this->oggPath . OGGCHOP_META_EXT ) ){
			$oggMeta = file_get_contents( $this->oggPath . OGGCHOP_META_EXT);
			//check if a separate request is working on generating the file:
			if( trim( $oggMeta ) == 'loading' ){
				if( $this->loadWaitCount >= 24 ){
					//we have waited 2 min with no luck..
					//@@todo we should flag that ogg file as broken?
					// and just redirect to normal output? (for now just set meta to false)
					$this->meta = false;
					//fail:
					return false;
				}else{
					//some other request is "loading" metadata sleep for 5 seconds and try again
					sleep(5);
					$this->loadWaitCount++;
					return $this->loadMeta();
				}
			}else{
				$this->meta = unserialize ( $oggMeta );
				if( $this->meta['version'] == 'OGGCHOP_META_VERSION' ){
					//we have a good version of the metadata return true:
					return true;
				}else{
					$this->meta = false;
				}
			}
		}
		//if the file does not exist or $this->meta is still false::
		if( ! is_file( $this->oggPath . OGGCHOP_META_EXT ) || $this->meta === false ){
			//set the meta file to "loading" (avoids multiple indexing requests)
			file_put_contents( $this->oggPath . OGGCHOP_META_EXT, 'loading');

			//load up the File/Ogg Pear module
			if ( !class_exists( 'File_Ogg' ) ) {
				require( 'File/Ogg.php' );
			}
			$f = new File_Ogg( $this->oggPath );
			$streams = array();
			$this->meta = array(
				'version' => OGGCHOP_META_VERSION
			);
			foreach ( $f->listStreams() as $streamType => $streamIDs ) {
				foreach ( $streamIDs as $streamID ) {
					$stream = $f->getStream( $streamID );
					//for now only support a fist theora stream we find:
					if( strtolower( $stream->getType() ) == 'theora'){
						$this->meta['theoraKeyFrameInx'] = $stream->getKeyFrameIndex();
						//set the width and height:
						$head =  $stream->getHeader();
						$this->meta['width'] = $head['PICW'];
						$this->meta['height'] = $head['PICH'];
						break;
					}
					/* more detailed per-stream metadata::
					 * $this->meta['streams'][$streamID] = array(
						'serial' => $stream->getSerial(),
						'group' => $stream->getGroup(),
						'type' => $stream->getType(),
						'vendor' => $stream->getVendor(),
						'length' => $stream->getLength(),
						'size' => $stream->getSize(),
						'header' => $stream->getHeader(),
						'comments' => $stream->getComments()
					);*/
				}
			}
			$this->meta['duration'] = $f->getLength();
			//cahce the metadata::
			file_put_contents( $this->oggPath . OGGCHOP_META_EXT, serialize( $this->meta) );
			return true;
		}
	}
}
