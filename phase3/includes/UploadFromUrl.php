<?php
class UploadFromUrl extends UploadBase {
	protected $mTempDownloadPath;
	
	//by default do a SYNC_DOWNLOAD 
	protected $dl_mode = null;
	
	static function isAllowed( $user ) {
		if( !$user->isAllowed( 'upload_by_url' ) )
			return 'upload_by_url';
		return parent::isAllowed( $user );
	}
	static function isEnabled() {
		global $wgAllowCopyUploads;
		return $wgAllowCopyUploads && parent::isEnabled();
	}	
	/*entry point for Api upload:: ASYNC_DOWNLOAD (if possible) */
	function initialize( $name, $url ) {		
		global $wgTmpDirectory;
		
		if(!$this->dl_mode)
			$this->dl_mode = Http::ASYNC_DOWNLOAD;
		
		$local_file = tempnam( $wgTmpDirectory, 'WEBUPLOAD' );
		parent::initialize( $name, $local_file, 0, true );

		$this->mUrl = trim( $url );
	}
	/*entry point for SpecialUpload no ASYNC_DOWNLOAD possible: */
	function initializeFromRequest( &$request ) {		
		//set dl mode if not set:
		if(!$this->dl_mode)
			$this->dl_mode = Http::SYNC_DOWNLOAD;	
			
		$desiredDestName = $request->getText( 'wpDestFile' );
		if( !$desiredDestName )
			$desiredDestName = $request->getText( 'wpUploadFile' );		
		return $this->initialize( 
			$desiredDestName, 
	 		$request->getVal('wpUploadFileURL')
		);
	}
	/**
	 * Do the real fetching stuff
	 */
	function fetchFile( ) {
		//entry point for SpecialUplaod 
		if( stripos($this->mUrl, 'http://') !== 0 && stripos($this->mUrl, 'ftp://') !== 0 ) {
			return Status::newFatal('upload-proto-error');
		}
		//print "fetchFile:: $this->dl_mode";
		//now do the actual download to the shared target: 	
		$status = Http::doDownload ( $this->mUrl, $this->mTempPath, $this->dl_mode);		
		//update the local filesize var: 
		$this->mFileSize = filesize($this->mTempPath);
				
		return $status;	
		
		/*
		$res = $this->curlCopy();
		if( $res !== true ) {
			return array(
				'status' => self::BEFORE_PROCESSING,
				'error' => $res,
			);
		}*/
		
	}
	

	/**
	 * Callback function for CURL-based web transfer
	 * Write data to file unless we've passed the length limit;
	 * if so, abort immediately.
	 * @access private
	 
	function uploadCurlCallback( $ch, $data ) {
		global $wgMaxUploadSize;
		$length = strlen( $data );
		$this->mFileSize += $length; 
		if( $this->mFileSize > $wgMaxUploadSize ) {
			return 0;
		}
		fwrite( $this->mCurlDestHandle, $data );
		return $length;
	}
*/
	//this can be deprecated in favor of http_request2 functions
	static function isValidRequest( $request ){
		if( !$request->getVal('wpUploadFileURL') )
			return false;
		//check that is a valid url:
		return preg_match('/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/',
						  $request->getVal('wpUploadFileURL'), $matches);
	}
}