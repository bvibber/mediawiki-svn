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
			//assign a new job:
			return $this->getNewJob( 'jobqueue' );
		}
	}
	public function getNewJob(){
		global $wgNumberOfClientsPerJobSet;
		$dbr = wfGetDb( DB_READ );
		//check if we have jobset
		//its always best to assigning from jobset (since the user already has the data)
		if( $this->mParams[ 'jobset' ] ){
			//try to get one from the current jobset
			$res = $dbr->select( 'wah_jobqueue',
				'*',
				array(
					'job_set_id' =>  intval( $this->mParams[ 'jobset' ] ),
					'job_done_time IS NULL',
					'job_assigned_time >= '. time() - $wgJobTimeOut
				),
				__METHOD__,
				array(
					'LIMIT'=>1
				)
			);
			if( $dbr->numRows( $res ) != 0){
				$job = $dbr->fetchObject( $res );

				return $this->getResult()->addValue( null, $this->getModuleName(),
							array(
								'job' => $job
							)
						);
			}
		}

		//just do a normal priority select of jobset
		$res = $dbr->select( 'wah_jobset',
			'*',
			array(
				'set_done_time IS NULL',
				'set_client_count < '.$wgNumberOfClientsPerJobSet
			),
			__METHOD__,
			array(				
				'LIMIT'		=> 1
			)
		);
		if( $dbr->numRows( $res ) != 0){
			//no jobs:
			return $this->getResult()->addValue( null, $this->getModuleName(),
						array(
							'nojobs' => true
						)
					);
		}else{
			//get a job from the jobset and increment the set_client_count 
			//(if the user has an unfinished job) reassin it (in cases where job is lost in trasport)			
			$jobSet = $dbr->fetchObject( $res );
			

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
}

?>