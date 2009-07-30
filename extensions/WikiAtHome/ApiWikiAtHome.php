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
		}
	}
	public function getAllowedParams() {
		return array(
			'file' 		=> null,
			'job_key'	=> null,
			'getnewjob'	=> null,
			'jobset' 	=> null,
			'token' 	=> null
		);
	}

	public function getParamDescription() {
		return array(
			'file' 		=> 'the file or data being uploaded for a given job',
			'job_key'	=> 'used to submit the resulting file of a given job key',
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