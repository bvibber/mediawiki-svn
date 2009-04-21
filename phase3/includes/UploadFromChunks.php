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
	
	function initializeFromParams( &$param , &$request) {		
		$this->initFromSessionKey( $param['chunksessionkey'] );			
		//set the chunk mode: 
		if( !$this->mSessionKey && !$param['done'] ){
			//session key not set init the chunk upload system: 
			$this->chunk_mode = UploadFromChunks::INIT;
			$this->mDesiredDestName = $param['filename'];
			
		}else if( $this->mSessionKey && !$param['done']){
			//this is a chunk piece	
			$this->chunk_mode = UploadFromChunks::CHUNK;

			//set chunk related vars: 			
			$this->mTempPath = $request->getFileTempName( 'chunk' );
			$this->mFileSize = $request->getFileSize( 'chunk' );		
			
		}else if( $this->mSessionKey && $param['done']){
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
				$this->mDesiredDestName	= $_SESSION[ 'wsUploadData' ][ $this->mSessionKey ][ 'mDesiredDestName' ];
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
	function isEmptyFile(){
		//does not apply to chunk init
		if(  $this->chunk_mode ==  UploadFromChunks::INIT ){
			return false;
		}else{
			return parent::isEmptyFile();
		}		
	}
 	/* Verify whether the upload is sane. 
	 * Returns self::OK or else an array with error information
	 */
	function verifyUpload( $resultDetails ) {	
		//no checks on chunk upload mode:
		if( $this->chunk_mode ==  UploadFromChunks::INIT )
			return self::OK;

		//verify on init and last chunk request 
		if(	$this->chunk_mode == UploadFromChunks::CHUNK || 
			$this->chunk_mode == UploadFromChunks::DONE )
			return parent::verifyUpload( $resultDetails );
	}		
	//only run verifyFile on completed uploaded chunks
	function verifyFile( $tmpFile ){		
		if( $this->chunk_mode == UploadFromChunks::DONE){
			return parent::verifyFile($tmpFile);		
		}else{
			return true;
		}
	}
	function setupChunkSession( $comment, $watch ) {
		$this->mSessionKey = $this->getSessionKey();						
				
		$_SESSION['wsUploadData'][ $this->mSessionKey ] = array(				
			'mComment'			=> $comment,
			'mWatch'			=> $watch,
			'mFilteredName'		=> $this->mFilteredName,	
			'mTempAppendPath'	=> null,	//the repo append path (not temporary local node mTempPath)
			'mDesiredDestName'	=> $this->mDesiredDestName,
			'version'         	=> self::SESSION_VERSION,
	   	);
	   		   	 
	   	return $this->mSessionKey;
	}
	
	//lets us return an api result (as flow for chunk uploads is kind of different than others. 
	function performUpload($summary='', $comment='', $watch='', $user){	
		global $wgServer, $wgScriptPath;
		if( $this->chunk_mode == UploadFromChunks::INIT ){			
			
			//firefogg expects a specific result per: 
			//http://www.firefogg.org/dev/chunk_post.html
			ob_clean();						
			echo ApiFormatJson::getJsonEncode( array( "uploadUrl" => "{$wgServer}{$wgScriptPath}/api.php?action=upload&format=json&enablechunks=true&chunksessionkey=".
						$this->setupChunkSession( $comment, $watch ) ) );
			/*print "{\"uploadUrl\" : \"{$wgServer}{$wgScriptPath}/api.php?action=upload&format=json&enablechunks=true&chunksessionkey=".			
						$this->setupChunkSession( $comment, $watch ) . "\"}";*/
			exit(0);
			
			/*
			 * @@todo would be more ideal to have firefogg pass results back to the client to construct next chunk url 
			return array( 
				'sessionkey'=> $this->setupChunkSession( $comment, $watch )
			);
			*/
		}else if( $this->chunk_mode == UploadFromChunks::CHUNK ){		
			$status = $this->doChunkAppend(); 	
			if( $status->isOK() ){			
				//return success:
				//firefogg expects a specific result per: 
				//http://www.firefogg.org/dev/chunk_post.html	
				ob_clean();	
				echo ApiFormatJson::getJsonEncode( array("result"=>1) );				
				exit(0);
				/*return array(
					'result' => 1						
				);*/						
			}else{
				return $status;		
			}
		}else if( $this->chunk_mode == UploadFromChunks::DONE ){
			//append the last chunk: 
			if( $this->doChunkAppend() ){
				//validate the uploaded file 
				
				//process the upload normally: 
				return Status::newGood('chunk upload done');
			}			
		}		
	}
	//append the given chunk to the temporary uploaded file. (if no temporary uploaded file exists created it.
	function doChunkAppend(){
		//if we don't have a mTempAppendPath to generate a file from the chunk packaged var:  
		if( ! $this->mTempAppendPath ){
			//die();
			//get temp name: 								
			//make a chunk store path. (append tmp file to chunk)					
			$status = $this->saveTempUploadedFile( $this->mDestName, $this->mTempPath );
			
			if( $status->isOK() ) {				
				$this->mTempAppendPath = $status->value;
				$_SESSION[ 'wsUploadData' ][ $this->mSessionKey ][ 'mTempAppendPath' ] = $this->mTempAppendPath;				
			}										
			return $status;			
		}else{
			//make sure the file exists: 
			if( is_file( $this->mTempAppendPath ) ){
				print "append: " . $this->mTempPath . ' to ' . $this->mTempAppendPath . "\n";
				$status = $this->appendToUploadFile( $this->mTempAppendPath,  $this->mTempPath );
				return $status;
			}else{
				return Status::newFatal('chunk-file-append-missing');
			}
		}
	}		
}
