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
		$req = new HttpRequest($url, $opts );	
		return $req->doRequest();	
	}		
	/**
	 * Simple wrapper for Http::request( 'POST' )
	 */
	public static function post( $url, $opts = array() ) {
		$opts['method']='POST';
		$req = new HttpRequest( $url, $opts );	
		return $req->doRequest();	
	}
	public static function doDownload( $url, $target_file_path , $dl_mode = self::SYNC_DOWNLOAD ){
		global $wgPhpCliPath, $wgMaxUploadSize;				
		//do a quick check to HEAD to insure the file size is not > $wgMaxUploadSize to large no need to download it
		$head = get_headers($url, 1);
		if(isset($head['Content-Length']) && $head['Content-Length'] > $wgMaxUploadSize){
			return Status::newFatal('requested file length ' . $head['Content-Length'] . ' is greater than $wgMaxUploadSize: ' . $wgMaxUploadSize);	
		}		
		//check if we can find phpCliPath (for doing a background shell request to php to do the download: 
		if( $wgPhpCliPath && wfShellExecEnabled() && $dl_mode == self::ASYNC_DOWNLOAD){		
			wfDebug("\ASYNC_DOWNLOAD\n");
			//setup session and shell call: 			
			return self::initBackgroundDownload( $url, $target_file_path );							
		}else if( $dl_mode== self::SYNC_DOWNLOAD ){
			wfDebug("\nSYNC_DOWNLOAD\n");
			//SYNC_DOWNLOAD download as much as we can in the time we have to execute
			$opts['method']='GET';
			$opts['target_file_path'] = $target_file_path;
			$req = new HttpRequest($url, $opts );	
			return $req->doRequest();	
		}		
	}
	/**
	 * a non blocking request (generally an exit point in the application)
	 * should write to a file location and give updates 
	 *	 
	 */
	private function initBackgroundDownload( $url, $target_file_path ){
		global $wgMaxUploadSize, $IP, $wgPhpCliPath;
		$status = Status::newGood();	
		
		//generate a session id with all the details for the download (pid, target_file_path ) 
		$upload_session_key = self::getUploadSessionKey();		
		$session_id = session_id();
		
		//store the url and target path: 
		$_SESSION[ 'wsDownload' ][$upload_session_key]['url'] = $url;
		$_SESSION[ 'wsDownload' ][$upload_session_key]['target_file_path'] = $target_file_path;
		
		//run the background download request: 	
		$cmd = $wgPhpCliPath . ' ' . $IP . "/maintenance/http_session_download.php --sid {$session_id} --usk {$upload_session_key}";		
		$pid = wfShellBackgroundExec($cmd , $retval);				
		wfDebug('GOT PID: '. $pid . " running cmd: $cmd\n" );
		//the pid is not of much use since we won't be visiting this same apache any-time soon.				
		if(!$pid)
			return Status::newFatal('could not run background shell exec');
			
		//update the status value with the $upload_session_key (for the user to check on the status of the upload) 
		$status->value = $upload_session_key;
		
		//return good status with 
		return $status; 
	}			
	function getUploadSessionKey(){
		$key = mt_rand( 0, 0x7fffffff );
		$_SESSION['wsUploadData'][$key] = array();
		return $key;
	}	
	/**
	 * used to run a session based download. Is initiated via the shell. 
	 *
	 * @param string $session_id  // the session id to grab download details from
	 * @param string $upload_session_key //the key of the given upload session 
	 * 			(a given client could have started a few http uploads at once)
	 */
	public static function doSessionIdDownload( $session_id, $upload_session_key ){
		global $wgUser;					
		wfDebug("\n\ndoSessionIdDownload\n\n");
		//set session to the provided key:
		session_id($session_id);
		//start the session					
		if( session_start() === false){
			wfDebug( __METHOD__ . ' could not start session');		
		}
		//get all the vars we need from session_id			
		if(!isset($_SESSION[ 'wsDownload' ][$upload_session_key])){
			wfDebug(  __METHOD__ .' Error:could not find upload session');
			exit();
		}
		//setup the global user from the session key we just inherited
		$wgUser = User::newFromSession();
		
		//grab the session data to setup the request: 
		$sessionData = $_SESSION[ 'wsDownload' ][$upload_session_key];
		//close down the session so we can other http queries can get session updates:
		session_write_close();		
				
		$req = new HttpRequest( $sessionData['url'], array(
			'target_file_path' => $sessionData['target_file_path'],			
			'upload_session_key' => $upload_session_key			
		) );	
		//run the actual request .. (this can take some time) 
		wfDebug("do Request: " . $sessionData['url'] . ' tf: ' .$sessionData['target_file_path'] );
		$status = $req->doRequest();		
		
		if( $status->isOK() ){		
			//start up the session again:			
			if( session_start() === false){
				wfDebug( __METHOD__ . ' ERROR:: Could not start session');	
			}			
			//re-grab the updated session data: 
			$sessionData = $_SESSION[ 'wsDownload' ][$upload_session_key];
			$reqData = $sessionData['mParam'];												
			
			$reqData['action'] = 'upload';		
						
			wfDebug('running FauxRequest: ' . print_r($reqData, true) );
					
			$faxReq = new FauxRequest($reqData, true);											
			$processor = new ApiMain($faxReq, $wgEnableWriteAPI);
			
			//init the mUpload var for the $processor
			$processor->execFromSession($sessionData['target_file_path']);
			
			ob_start();
			$processor->doExecUpload();
			$apiUploadResult = ob_get_contents();			
			ob_get_clean();
			
				
			//the status updates runner will grab the result form the session: 
			$_SESSION[ 'wsDownload' ][$upload_session_key]['apiUploadResult'] = $apiUploadResult;			
			session_write_close();				
		}
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
class HttpRequest{
	var $target_file_path;
	var $upload_session_key;
	function __construct($url, $opt){
		$this->url = $url;
		$this->method = (isset($opt['method']))?$opt['method']:'GET';		
		$this->target_file_path = (isset($opt['target_file_path']))?$opt['target_file_path']:false;
		$this->upload_session_key = (isset($opt['upload_session_key']))?$opt['upload_session_key']:false;			
	}
/**
	 * Get the contents of a file by HTTP
	 * @param $url string Full URL to act on	 
	 * @param $Opt associative array Optional array of options:
	 * 		'method'	  => 'GET', 'POST' etc. 
	 * 		'target_file_path' => if curl should output to a target file
	 * 		'adapter'	  => 'curl', 'soket'
	 */
	 public function doRequest() {							
		# Use curl if available
		if ( function_exists( 'curl_init' ) ) {			
			return $this->doCurlReq();
		}else{			
			return $this->doPhpReq();
		}
	 }
	 private function doCurlReq(){
	 	global $wgHTTPFileTimeout, $wgHTTPProxy, $wgTitle;
	 	
	 	$status = Status::newGood();	 	 
	 	wfDebug("\ncurReq: $this->url (sleep 1 sec)\n");
	 	sleep(1);	
		$c = curl_init( $this->url );			
		
		//proxy setup: 
		if ( Http::isLocalURL( $this->url ) ) {
			curl_setopt( $c, CURLOPT_PROXY, 'localhost:80' );
		} else if ($wgHTTPProxy) {
			curl_setopt($c, CURLOPT_PROXY, $wgHTTPProxy);
		}
		
		curl_setopt( $c, CURLOPT_TIMEOUT, $wgHTTPFileTimeout );			
			
		
		curl_setopt( $c, CURLOPT_USERAGENT, Http::userAgent() );
		
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
			$cwrite = new simpleFileWriter( $this->target_file_path, $this->upload_session_key );				
			if(!$cwrite->status->isOK()){
				wfDebug("ERROR in setting up simpleFileWriter\n");		
				$status = $cwrite->status;	
			}
			curl_setopt( $c, CURLOPT_WRITEFUNCTION, array($cwrite, 'callbackWriteBody') );
		}

		//start output grabber: 
		if(!$this->target_file_path)
			ob_start();			
		
		//run the actual curl_exec:
		try {
            if (false === curl_exec($c)) {
            	$error_txt ='Error sending request: #' . curl_errno($c) .' '. curl_error($c);
            	wfDebug($error_txt . "\n");
                $status = Status::newFatal( $error_txt);
            }
        } catch (Exception $e) {
        	//do something with curl exec error?
        }	 	 
        wfDebug("\nDONE WITH curl_exec \n");
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
}
/**
 * a simpleFileWriter with session id updates 
 * 
 */
class simpleFileWriter{
	var $target_file_path;
	var $status = null;	
	var $session_id = null;	
	var $session_update_interval = 0; //how offten to update the session while downloading 
	
	function simpleFileWriter($target_file_path, $session_id=false){
		$this->target_file_path = $target_file_path;
		$this->status = Status::newGood();
		//open the file:
		$this->fp = fopen( $this->target_file_path, 'w');
		if( $this->fp === false ){
			$this->status = Status::newFatal('HTTP::could-not-open-file-for-writing'); 
		}
		//true start time
		$this->prevTime = time();
	}
	public function callbackWriteBody($ch, $data_packet){
		global $wgMaxUploadSize;	
		wfDebug("\ncallbackWriteBody::" . strlen($data_packet) . "\n");
		//check file size: 
		clearstatcache();
		$this->current_fsize = filesize( $this->target_file_path);
		
		if( $this->current_fsize > $wgMaxUploadSize){
			wfDebug( __METHOD__ . " ::http download too large\n");
			$this->status = Status::newFatal('HTTP::file-has-grown-beyond-upload-limit-killing: downloaded more than ' . 
				Language::formatSize($wgMaxUploadSize) . ' ');			
			return 0;
		}
		wfDebug("passed fsize check\n");			
		//write out the content
		if( fwrite($this->fp, $data_packet) === false){
			wfDebug(__METHOD__ ." ::could-not-write-to-file\n");
			$this->status = Status::newFatal('HTTP::could-not-write-to-file');
			return 0;
		}
		wfDebug("did fwrite oky\n");
						
		wfDebug("\n" .'check if we should update: ' . time() . ' - ' .$this->prevTime . ' > '. $this->session_update_interval . "\n");
		//if more than 2 second have passed update_session_progress
		if($this->upload_session_key && (time() - $this->prevTime) > $this->session_update_interval ) {
			$this->prevTime = time();
			$session_status = $this->update_session_progress();
			if( !$session_status->isOK() ){
				$this->status = $session_status;
				wfDebug( __METHOD__ . ' update session failed or was canceled');
				return 0;
			}
		}
		return strlen($data_packet);		
	}
	public function update_session_progress(){
		$status = Status::newGood();
		//start the session					
		if( session_start() === false){
			wfDebug( __METHOD__ . ' could not start session');
			exit(0);		
		}
		$sd =& $_SESSION[ 'wsDownload' ][$this->upload_session_key];
		//check if the user canceled the request: 
		if( $sd['user_cancel'] == true ){
			//kill the download
			return Status::newFatal('user-canceled-request');						
		}		
		//update the progress bytes download so far: 
		$sd['loaded'] = $this->current_fsize;
		wfDebug('set session loaded amount to: ' . $sd['loaded']);
		//close down the session so we can other http queries can get session updates:
		session_write_close();		
		return $status;		
	}
	public function close(){
		if(false === fclose( $this->fp )){
			$this->status = Status::newFatal('HTTP::could-not-close-file');
		}		
	}
}