<?php
/*
 * the api module responsible for dishing out jobs, and taking in results.
 */

if (!defined('MEDIAWIKI')) {
  die();
}

/**
 * A module that allows for distributing wikimedia workload with a present focus on transcoding
 *
 * @ingroup API
 */
class ApiWikiAtHome extends ApiBase {

	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	public function execute(){
		global $wgUser, $wgRequest;
		$this->getMain()->isWriteMode();
		$this->mParams = $this->extractRequestParams();
		$request = $this->getMain()->getRequest();				
		
		// do token checks:
		if( is_null( $this->mParams['token'] ) )
			$this->dieUsageMsg( array( 'missingparam', 'token' ) );
		if( !$wgUser->matchEditToken( $this->mParams['token'] ) )
			$this->dieUsageMsg( array( 'sessionfailure' ) );

		//do actions:
		if( $this->mParams['getnewjob'] ){
			//do job req:
			return $this->proccessJobReq();			
		}else if ( $this->mParams['jobkey'] ){
			return $this->doProccessJobKey ( $this->mParams['jobkey'] ) ;					
		}
	}
	/*
	 * Process a newJob req:
	 */
	function proccessJobReq(){		
		
		if( isset( $this->mParams['jobset']) && $this->mParams['jobset']){
			$job = WahJobManager::getNewJob( $this->mParams['jobset'] );
		}else{
			$job = WahJobManager::getNewJob();
		}

		if(!$job){
			return $this->getResult()->addValue( null, $this->getModuleName(),
					array(
						'nojobs' => true
					)
				);
		}else{
			$job4Client = array();
			//unpack the $job_json
			$job4Client['job_json'] = json_decode( $job->job_json ) ;
			//we set the job key to job_id _ sha1
			$job4Client['job_key'] 	= $job->job_id . '_'. sha1( $job->job_json );
			$job4Client['job_title']= $job->title;
			$job4Client['job_ns']	= $job->ns;
			$job4Client['job_set_id'] = $job->job_set_id;

			$tTitle = Title::newFromText($job->title, $job->ns);

			$job4Client['job_fullTitle'] = $tTitle->getFullText();

			//@@todo avoid an api round trip return url here:
			//$job4Client['job_url'] = $file->getFullURL();

			$this->getResult()->addValue( 
				null, 
				$this->getModuleName(),
				array(
					'job' => $job4Client
				)
			);
		}
	}
	/*
	 * proccess the job key: 
	 */
	function doProccessJobKey( $job_key ){
		global $wgRequest, $wgUser;
		//check if its a valid job key (job_number _ sh1(job_json) )
		list($job_id, $json_sha1) = explode( '_', $job_key );

		//get the job object
		$job = WahJobManager::getJobById( $job_id );
		
		if( !$job || sha1($job->job_json) != $json_sha1){
			//die on bad job key				
			return $this->dieUsageMsg( array( 'code' => 'badjobkey', 'info'=>'Bad Job key' ) );				
		}							
		
		$jobSet =  WahJobManager::getJobSetById( $job->job_set_id );
		//check if its a valid video ogg file (ffmpeg2theora --info)
		$uploadedJobFile = $wgRequest->getFileTempname('file');
		$mediaMeta = wahGetMediaJsonMeta( $uploadedJobFile );

		if( !$mediaMeta ){
			//failed basic ffmpeg2theora video validation
			return $this->dieUsageMsg( array( 'code'=>'badfile', 'info'=>"Not a valid Video file") );
		}
					
		//gab the ogg types from OggHandler.php
		global $wgOggVideoTypes, $wgOggAudioTypes;
		//check for theora and vorbis streams in the metadata output of the file:
		if( isset($wgOggVideoTypes) && isset($wgOggAudioTypes) ){
			$isOgg = false;
			
			foreach ( $mediaMeta->video as $videoStream ) {
				if(in_array( ucfirst( $videoStream->codec ),  $wgOggVideoTypes))
					$isOgg =true;
			}
			foreach ( $mediaMeta->audio as $audioStream ) {
				if(in_array( ucfirst( $audioStream->codec ),  $wgOggAudioTypes))
					$isOgg = true;
			}
			if(!$isOgg){
				return $this->dieUsageMsg( array('code'=>'badfile', 'info'=>'Not a valid Ogg file') );
			}
		}
		
		//all good so far put it into the derivative temp folder by with each piece as it job_id name
		//@@todo need to rework this a bit for flattening "sequences"
		$fTitle = Title::newFromText( $jobSet->set_title, $jobSet->set_namespace );
		$file = RepoGroup::singleton()->getLocalRepo()->newFile( $fTitle );
		$thumbPath = $file->getThumbPath( $jobSet->set_encodekey );

		$destTarget = $thumbPath . '/'. $job->job_order_id . '.ogg';
		if( is_file($destTarget) ){
			//someone else beat this user to finish the job (with a $wgJobTimeOut handicap )
			return $this->dieUsageMsg( array( 'code'=>'alreadydone', 'info'=>'The job has already been completed') );
		}
		//move the current chunk to that path:
		$status = RepoGroup::singleton()->getLocalRepo()->store(
			$uploadedJobFile,
			'thumb',
			$destTarget
		);						
		if( !$status->isGood() ){
			return $this->dieUsageMsg( array('code'=>'fileerror', 'info'=>'Could Not Move The Uploaded File') );
		}
		$dbw = &wfGetDb( DB_READ );
		//update the jobqueue table with job done time & user
		$dbw->update('wah_jobqueue',
			array(
				'job_done_user_id' 	=> $wgUser->getId(),
				'job_done_time'		=> time()
			),
			array(
				'job_id'			=> $job_id
			),
			__METHOD__,
			array(
				'LIMIT' => 1
			)
		);		

		// reduce job_client_count by 1 now that this client is "done"
		$dbw->update('wah_jobset', 
			array(
				'set_client_count = set_client_count -1'
			),
			array(
				'set_id' => $jobSet->set_id
			),
			__METHOD__,
			array(
				'LIMIT' => 1
			)
		);			
		//check if its the "last" job shell out a join command
		$wjm = WahJobManager::newFromSet( $jobSet );	
		$percDone = $wjm->getDonePerc();
		if($percDone != 1){
			//the stream is not done but success
			return $this->getResult()->addValue( null, $this->getModuleName(),
					array(
						'chunkaccepted' => true,
						'setdone'		=> false
					)
				);			
		}else if( $percDone == 1){		
			//all the files are "done" according to the DB:	
			//make sure all the files exist in the 
			$fileList = '';
			for( $i=0; $i < $jobSet->set_jobs_count ; $i++ ){
				//make sure all the files are present: 
				if(!is_file($thumbPath . $i . '.ogg' )){
					wfDebug('Missing wikiAtHome chunk $i');
					//unset the job complete state
					$dbw->update( 'wah_jobqueue',
						array(
							'job_done_time = NULL',
							'job_done_user_id = NULL'
						),
						array(
							'job_set_id' 	=> $jobSet->set_id,
							'job_order_id' 	=> $i
						),
						__METHOD__,
						array(
							'LIMIT' => 1
						)									
					); 
					//return missing files (maybe something is ~broken~)
					return $this->dieUsageMsg( array(
						'code'=>'missingfile', 
						'info'=>"WikiAtHome database out of sync with file system missing file $i"
						) 
					);						
				}
				$fileList+= " {$thumbPath}/{$i}.ogg";
			}
			//do merge request (not sure if we need to shell out for this or if we can do it in place)
			//should be disk speed limited:
			global $wgOggCat;
			$finalDestTarget = "{$thumbPath}.ogg";
			$cmd = wfEscapeShellArg( $wgOggCat ) . ' ' .$finalDestTarget . ' ' . wfEscapeShellArg ( $fileList );
			wfProfileIn( 'oggCat' );	
			wfShellExec( $cmd );	
			wfProfileOut( 'oggCat' );	

			//the stream done but success
			return $this->getResult()->addValue( null, $this->getModuleName(),
				array(
					'chunkaccepted' => true,
					'setdone'		=> true,
				)
			);		
	}		

		//return success
		
	}
	public function getAllowedParams() {
		return array(
			'file' 		=> null,
			'jobkey'	=> null,
			'getnewjob'	=> null,
			'jobset' 	=> null,
			'token' 	=> null
		);
	}

	public function getParamDescription() {
		return array(
			'file' 		=> 'the file or data being uploaded for a given job',
			'jobkey'	=> 'used to submit the resulting file of a given job key',
			'getnewjob'	=> 'set to ture to get a new job',
			'jobset' 	=> 'jobset used with getnewjob to set jobset prefrence',
			'token' 	=> 'the edittoken (needed to submit job chunks)'
		);
	}

	public function getDescription() {
		return array(
			'Wiki@Home enables you to help with resource intensive operations at home ;)',
			' First login to the server. Then request a newjob.',
			' Process the job and send it back to the server.',
			' On subquent queries you can use "jobset" to request a job on data you already have downloaded',
			'Note that the HTTP POST must be done as a file upload (i.e. using multipart/form-data)'
		);
	}

	protected function getExamples() {
		return array(
			'Get A Job:',
			'    api.php?action=wikiathome&getnewjob=new',
			'Submit a Job:',
			'	 api.php?action=wikiathome&job_key=343&file={file_data}'
		);
	}
	public function getVersion() {
		return __CLASS__ . ': $Id: ApiWikiAtHome.php 51812 2009-06-12 23:45:20Z dale $';
	}
}

?>