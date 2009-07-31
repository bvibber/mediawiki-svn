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
		global $wgUser;
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
			if( isset( $this->mParams['jobset']) && $this->mParams['jobset']){
				$job = WahJobManager::getNewJob( $this->mParams['jobset'] );
			}else{
				$job =  WahJobManager::getNewJob();
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

				$this->getResult()->addValue( null, $this->getModuleName(),
						array(
							'job' => $job4Client
						)
					);
			}
		}else if ( $this->mParams['jobkey'] ){
			print "have jobKey: " .  $this->mParams['jobkey'] ;
			//process the upload
			//check if its a valid job key (job_number _ sh1(job_json) )
			list($job_id, $json_sha1) = explode( '_', $this->mParams['jobkey'] );
			$job = WahJobManager::getJobById( $job_id );
			if( !$job || sha1($job->job_json) != $json_sha1){
				//die on bad job key
				return $this->dieUsage('Your job key is not valid', 'badjobkey' );
			}
			$jobSet =  WahJobManager::getJobSetById( $job->job_set_id );
			//check if its a valid video ogg file (ffmpeg2theora --info)
			$uploadedJobFile = $this->getFileTempname('file');
			$mediaMeta = wahGetMediaJsonMeta( $uploadedJobFile );

			if( !$mediaMeta ){
				//failed basic ffmpeg2theora video validation
				return $this->dieUsage('Not a valid Video File', 'badfile' );
			}
			//check for theora and vorbis streams in the metadata output of the file:
			if( class_exists( OggHandler ) ){
				$isOgg = false;

				if( OggHandler::audioTypes ){
					$audioTypes = OggHandler::audioTypes;
				}
				foreach ( $mediaMeta['video'] as $videoStream ) {
					if(in_array( ucfirst( $videoStream->codec ),  OggHandler::videoTypes))
						$isOgg =true;
				}
				foreach ( $mediaMeta['audio'] as $audioStream ) {
					if(in_array( ucfirst( $audioStream->codec ),  OggHandler::audioTypes))
						$isOgg = true;
				}
				if(!$isOgg){
					return $this->dieUsage('Not a valid Ogg file', 'badfile' );
				}
			}
			//all good so far put it into the derivative temp folder by with each piece as it job_id name
			//@@todo need to rework this a bit for flattening "sequences"
			$fTitle = Title::newFromText( $jobSet->set_title, $jobSet->set_namespace );
			$file = RepoGroup::singleton()->getLocalRepo()->newFile( $fTitle );
			$thumbPath = $file->getThumbPath( $jobSet->set_encodekey );

			$destTarget = $job_id . '.ogg';

			//copy the current chunk to that path:
			$status = RepoGroup::singleton()->getLocalRepo()->store(
				$uploadedJobFile,
				'thumb',
				$destTarget
			);
			if( !$status->isGood() ){
				return $this->dieUsage( 'Could not Copy File');
			}
			return $this->dieUsage('copied file to: ' . $destTarget);

			//update the table with job done time & job user


			//check if its the "last" job shell out a join command

			//double check all the files exist.

			//return success

		}
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