<?php
/*
* first destination checks are made (if ignorewarnings is not checked) errors / warning is returned. 
* 
* we return the uploadUrl
* we then accept chunk uploads from the client.
* return chunk id on each POSTED chunk
* once the client posts done=1 concatenated the files together.
* more info at: http://firefogg.org/dev/chunk_post.html
*/
class UploadFromChunks extends UploadBase {
	
	var $chunk_mode; //init, chunk, done 	
	var $mSessionKey = false;
	var $status = array();
	
	const INIT 	= 1;
	const CHUNK = 2;
	const DONE 	= 3;
	
	function initializeFromParams( $param ) {		
		$this->initFromSessionKey( $param['sessionkey'] );		
				
		//set the chunk mode: 
		if( !$this->mSessionKey && !$parm['done'] ){
			//session key not set init the chunk upload system: 
			 $this->chunk_mode = UploadFromChunks::INIT;			  
		}else if( $this->mSessionKey && !$parm['done']){
			//this is a chunk piece	
			$this->chunk_mode = UploadFromChunks::CHUNK;
			 
		}else if( $this->mSessionKey && $parm['done']){
			//this is the last chunk			 
			$this->chunk_mode = UploadFromChunks::DONE;
		}			
		return $this->status;
	}	
	
	function initFromSessionKey( $sessionKey ){
		if( !$sessionKey || empty( $sessionKey ) ){
			return false;
		}
		$this->mSessionKey = $sessionKey;
		if( isset( $_SESSION['wsUploadData'][$this->mSessionKey]['version'] ) &&
			$_SESSION['wsUploadData'][$this->mSessionKey]['version'] == self::SESSION_VERSION ) {
				//update the local object from the session
				$this->mComment			= $_SESSION[ 'wsUploadData' ][ $this->mSessionKey ][ 'mComment' ];
				$this->mWatch			= $_SESSION[ 'wsUploadData' ][ $this->mSessionKey ][ 'mWatch' ];
				$this->mFilteredName	= $_SESSION[ 'wsUploadData' ][ $this->mSessionKey ][ 'mFilteredName' ];	
				$this->mTempAppendPath  = $_SESSION[ 'wsUploadData' ][ $this->mSessionKey ][ 'mTempAppendPath' ];
		}else{
			$this->status = Array( 'error'=> 'missing session data');
			return false;
		}
		
	}	
	static function isValidRequest( $request ) {
		$sessionData = $request->getSessionData('wsUploadData');
		if(! self::isValidSessionKey( 
			$request->getInt( 'wpSessionKey' ),
			$sessionData) )
				return false;			
		//check for the file: 
		return (bool)$request->getFileTempName( 'file' );								
	}		
	
	/* check warnings depending on chunk_mode*/
	function checkWarnings(){
		$warning = array();
		return $warning;
	}
	
 	/* Verify whether the upload is sane. 
	 * Returns self::OK or else an array with error information
	 */
	function verifyUpload( $resultDetails ) {
		/*
		 * check the internal chunk mode for alternative Verify path
		 * (for now just return "OK" 
		 */
		if( $this->chunk_mode ==  UploadFromChunks::INIT)
			return self::OK;
		
		return parent::verifyUpload( $resultDetails );
	}		

	function setupChunkSession( $comment, $watch ) {
		$key = $this->getSessionKey();
		//since we can't pass things along in POST store them in the Session:
		$_SESSION['wsUploadData'][$key] = array(			
			'mComment'			=> $comment,
			'mWatch'			=> $watch,
			'mFilteredName'		=> $this->mFilteredName,	
			'mTempAppendPath'	=> null,				
			'version'         	=> self::SESSION_VERSION,
	   	);
	   	return $key;
	}
	
	//lets us return an api result (as flow for chunk uploads is kind of different than others. 
	function getAPIresult($comment, $watch){	
		if( $this->chunk_mode == UploadFromChunks::INIT ){
			//verifyUpload & checkWarnings have already run .. just create the upload store return the upload session key
			return array( 
				'sessionkey'=> $this->setupChunkSession( $comment, $watch )
			);
		}else if( $this->chunk_mode == UploadFromChunks::CHUNK ){
			
			$this->doChunkAppend();
			
			//return success:
			return array(
				'result' => 1						
			);						
			
		}else if( $this->chunk_mode == UploadFromChunks::DONE ){
			//append the last chunk: 
			if( $this->doChunkAppend() ){
				//process the upload normally: 
				return UploadFrom::OK;
			}			
		}		
	}
	//append the given chunk to the temporary uploaded file. (if no temporary uploaded file exists created it.
	function doChunkAppend(){
		//if we don't have a mTempAppendPath to append to generate that:  
		if( ! $this->mTempAppendPath ){
			//make a chunk store path. (append tmp file to chunk)
			print "save Temp: " . $this->mTempPath . ' '.  $this->mDestName . "\n";  	
			if( isset( $this->mDestName ) ){		
				$stash = $this->saveTempUploadedFile( $this->mDestName, $this->mTempPath );
				if( !$stash ) {
					# Couldn't save the file.
					return false;				
				}
				//update the mDestName
				$this->mTempAppendPath = $stash;
				$_SESSION[ 'wsUploadData' ][ $this->mSessionKey ] = $this->mTempAppendPath;
			}
		}else{
			//make sure the file exists: 
			if( is_file( $this->mTempAppendPath ) ){
				print "append: " . $this->mTempPath . ' to ' . $this->mTempAppendPath . "\n";
				$this->appendToUploadFile( $this->mTempAppendPath,  $this->mTempPath );	
			}
		}
	}
		
	function checkAPIresultOverride(){
		if( $this->chunk_mode == UploadFromChunks::INIT ){
			return true;
		}else{
			return false;
		}
	}
}
