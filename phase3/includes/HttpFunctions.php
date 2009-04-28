<?php
/**
 * HTTP handling class
 *
 */


class Http {
	const SYNC_DOWNLOAD = 1;  //syncronys upload (in a single request) 
	const ASYNC_DOWNLOAD = 2; //asynchronous upload we should spawn out another process and monitor progress if possible)

	var $body = '';
	/**
	 * Simple wrapper for Http::request( 'GET' )
	 */
	public static function get( $url, $opts = array() ) {
		$opt['method'] = 'GET';
		$req = newReq($url, $opts );	
		return $req->request();	
	}		
	/**
	 * Simple wrapper for Http::request( 'POST' )
	 */
	public static function post( $url, $opts = array() ) {
		$opts['method']='POST';
		$req = newReq($url, $opts );	
		return $req->request();	
	}
	public static function doDownload( $url, $target_file_path , $dl_mode = self::SYNC_DOWNLOAD ){
		global $wgPhpCliPath, $wgMaxUploadSize;
		print "doDownload:$target_file_path";
		//do a quick check to HEAD to insure the file size is not > $wgMaxUploadSize to large no need to download it
		$head = get_headers($url, 1);
		if(isset($head['Content-Length']) && $head['Content-Length'] > $wgMaxUploadSize){
			return Status::newFatal('requested file length ' .$head['Content-Length'] . ' is greater than $wgMaxUploadSize: ' . $wgMaxUploadSize);	
		}
		
		//check if we can find phpCliPath (for doing a background shell request to php to do the download: 
		if( $wgPhpCliPath && wfShellExecEnabled() && $dl_mode == self::ASYNC_DOWNLOAD){			
			//setup session and shell call: 			
			return self::initBackgroundDownload( $url, $target_file_path );
			
			//return success status and download_session_key
			
			//(separate ajax request can now check on the status of the shell exec)... and even kill or cancel it)
			 
		}else if( $dl_mode== self::SYNC_DOWNLOAD ){
			//else just download as much as we can in the time we have left:
			$opts['method']='GET';
			$opts['target_file_path'] = $target_file_path;
			$req = self::newReq($url, $opts );	
			return $req->request();	
		}		
	}
	//setup a new request
	public static function newReq($url, $opt){
		$req = new Http();
		$req->url = $url;
		$req->method = (isset($opt['method']))?$opt['method']:'GET';		
		$req->target_file_path = (isset($opt['target_file_path']))?$opt['target_file_path']:false;
		return $req;
	}
	/**
	 * a non blocking request (generally an exit point in the application)
	 * should write to a file location and give updates 
	 *	 
	 */
	private function initBackgroundDownload( $url, $target_file_path ){
		global $wgMaxUploadSize;
		$status = Status::newGood();	
		//generate a session id with all the details for the download (pid, target_file_path ) 

		//later add in (destName & description) 
		$session_id = session_id();
		print "should spin out a process with id: $session_id\n";
		//maintenance/http_download.php passing it a upload session_id
			
		//return status
		return $status; 
	}				
	/**
	 * used to run a session based download. Is initiated via the shell. 
	 *
	 * @param string $dn_session_id  // the session id to grab download details from
	 */
	public static function doSessionIdDownload( $dn_session_id ){
		//get all the vars we need from session_id
		session_id( $dn_session_id );
		$url = $_SESSION[ 'wsDownload' ][ 'url' ];
		$target_file_path_path = $_SESSION[ 'wsDownload' ][ 'target_file_path_path' ];

		//new req here: 
	}
	/**
	 * Get the contents of a file by HTTP
	 * @param $url string Full URL to act on	 
	 * @param $Opt associative array Optional array of options:
	 * 		'method'	  => 'GET', 'POST' etc. 
	 * 		'target_file_path' => if curl should output to a target file
	 * 		'adapter'	  => 'curl', 'soket'
	 */
	 private function request() {
		global $wgHTTPTimeout, $wgHTTPProxy, $wgTitle;
				
		wfDebug( __METHOD__ . ": $method $url\n" );		
		# Use curl if available
		if ( function_exists( 'curl_init' ) ) {			
			return $this->doCurlReq();
		}else{			
			return $this->doPhpReq();
		}
	 }
	 private function doCurlReq(){
	 	$status = Status::newGood();
		$c = curl_init( $this->url );
				
		//proxy setup: 
		if ( self::isLocalURL( $this->url ) ) {
			curl_setopt( $c, CURLOPT_PROXY, 'localhost:80' );
		} else if ($wgHTTPProxy) {
			curl_setopt($c, CURLOPT_PROXY, $wgHTTPProxy);
		}

		curl_setopt( $c, CURLOPT_TIMEOUT, $wgHTTPTimeout );			
			
		
		curl_setopt( $c, CURLOPT_USERAGENT, self :: userAgent() );
		
		if ( $this->method == 'POST' ) {
			curl_setopt( $c, CURLOPT_POST, true );
			curl_setopt( $c, CURLOPT_POSTFIELDS, '' );
		}else{
			curl_setopt( $c, CURLOPT_CUSTOMREQUEST, $this->method );
		}

		# Set the referer to $wgTitle, even in command-line mode
		# This is useful for interwiki transclusion, where the foreign
		# server wants to know what the referring page is.
		# $_SERVER['REQUEST_URI'] gives a less reliable indication of the
		# referring page.
		if ( is_object( $wgTitle ) ) {
			curl_setopt( $c, CURLOPT_REFERER, $wgTitle->getFullURL() );
		}
		
		//set the write back function (if we are writing to a file) 
		if( $this->target_file_path ){			
			$cwrite = new simpleFileWriter( $this->target_file_path );									
			curl_setopt( $c, CURLOPT_WRITEFUNCTION, array($cwrite, 'callbackWriteBody') );
		}

		//start output grabber: 
		if(!$this->target_file_path)
			ob_start();			
		
		//run the actual curl_exec:
		try {
            if (false === curl_exec($c)) {
                $status = Status::newFatal( 'Error sending request: #' . curl_errno($c) .
                                                       				' '. curl_error($c) );
            }
        } catch (Exception $e) {
        	//do something with curl exec error?
        }	 	 
		//if direct request output the results to the stats value: 
		if( !$this->target_file_path && $status->isOK() ){       						
        	$status->value = ob_get_contents();
			ob_end_clean();
		}      		
		//if we wrote to a target file close up or return error
		if( $this->target_file_path ){
			$cwrite->close();			
			if( ! $cwrite->status->isOK() ){
				return $cwrite->status;			
			}
		}
		
		# Don't return the text of error messages, return false on error
		$retcode = curl_getinfo( $c, CURLINFO_HTTP_CODE );
		if ( $retcode != 200 ) {
			wfDebug( __METHOD__ . ": HTTP return code $retcode\n" );
			$status = Status::newFatal( "HTTP return code $retcode\n" );
		}
		# Don't return truncated output
		$errno = curl_errno( $c );
		if ( $errno != CURLE_OK ) {
			$errstr = curl_error( $c );
			wfDebug( __METHOD__ . ": CURL error code $errno: $errstr\n" );
				$status = Status::newFatal( " CURL error code $errno: $errstr\n" );
		}
		curl_close( $c );			 
					
		//return the result obj							
		return $status;							
	}
	public function doPhpReq(){
		#$use file_get_contents...			
		# This doesn't have local fetch capabilities...

		$headers = array( "User-Agent: " . self :: userAgent() );
		if( strcasecmp( $method, 'post' ) == 0 ) {
			// Required for HTTP 1.0 POSTs
			$headers[] = "Content-Length: 0";
		}
		$opts = array(
			'http' => array(
				'method' => $method,
				'header' => implode( "\r\n", $headers ),
				'timeout' => $timeout ) );
		$ctx = stream_context_create( $opts );

		$status->value = file_get_contents( $url, false, $ctx );
		if(!$status->value){
			$status->error('file_get_contents-failed');
		}
		return $status;
	}
	/**
	 * Check if the URL can be served by localhost
	 * @param $url string Full url to check
	 * @return bool
	 */
	public static function isLocalURL( $url ) {
		global $wgCommandLineMode, $wgConf;
		if ( $wgCommandLineMode ) {
			return false;
		}

		// Extract host part
		$matches = array();
		if ( preg_match( '!^http://([\w.-]+)[/:].*$!', $url, $matches ) ) {
			$host = $matches[1];
			// Split up dotwise
			$domainParts = explode( '.', $host );
			// Check if this domain or any superdomain is listed in $wgConf as a local virtual host
			$domainParts = array_reverse( $domainParts );
			for ( $i = 0; $i < count( $domainParts ); $i++ ) {
				$domainPart = $domainParts[$i];
				if ( $i == 0 ) {
					$domain = $domainPart;
				} else {
					$domain = $domainPart . '.' . $domain;
				}
				if ( $wgConf->isLocalVHost( $domain ) ) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Return a standard user-agent we can use for external requests.
	 */
	public static function userAgent() {
		global $wgVersion;
		return "MediaWiki/$wgVersion";
	}
}
/**
 * a simpleFileWriter 
 */
class simpleFileWriter{
	var $target_file_path;
	var $status = null;		
	function simpleFileWriter($target_file_path){
		$this->target_file_path = $target_file_path;
		$this->status = Status::newGood();
		//open the file:
		$this->fp = fopen( $this->target_file_path, 'w');
		if( $this->fp === false ){
			$this->status = Status::newFatal('HTTP::could-not-open-file-for-writing'); 
		}
	}
	public function callbackWriteBody($ch, $data_packet){
		global $wgMaxUploadSize;
		//check file size: 
		clearstatcache();
		if( filesize( $this->target_file_path) > $wgMaxUploadSize){
			$this->status = Status::newFatal('HTTP::file-has-grown-beyond-upload-limit-killing: '.  filesize( $this->target_file_path) . ' > ' . $wgMaxUploadSize);
			return ;
		}			
		//write out the content
		if( fwrite($this->fp, $data_packet) === false){
			$this->status = Status::newFatal('HTTP::could-not-write-to-file');
			return 0;
		}					
		return strlen($data_packet);		
	}
	public function close(){
		if(false === fclose( $this->fp )){
			$this->status = Status::newFatal('HTTP::could-not-close-file');
		}		
	}
}