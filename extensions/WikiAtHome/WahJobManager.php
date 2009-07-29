<?php
class WahJobManager {
	//encoding profiles (settings set in config)

	function __construct(&$file, $encodeKey){
		$this->file = $file;
		$this->sEncodeKey = $encodeKey;
		$this->sNamespace =$this->file->title->getNamespace();
		$this->sTitle = $this->file->title->getDBkey();
	}

	/*
	 * get the percentage done (return 1 if done)
	 */
	function getDonePerc(){
		$fname = 'WahJobManager::getDonePerc';
		//grab the jobset
		$dbr = &wfGetDb( DB_READ );
		$res = $dbr->select('wah_jobset',
			'*',
			array(
				'set_namespace' => $this->sNamespace,
				'set_title'		=> $this->sTitle,
				'set_encodekey'	=> $this->sEncodeKey
			),
			__METHOD__
		);
		if( $dbr->numRows( $res ) == 0 ){
			//we should setup the job:
			$this->doJobSetup();
			//return 0 percent done
			return 0;
		}else{
			$setRow = $dbr->fetchObject( $res );
			$this->sId = $setRow->set_id;
			$this->sJobsCount = $setRow->set_jobs_count;
			//get an estimate of how many of the current job are NULL (not completed)
			$doneRes = $dbr->select('wah_jobqueue',
				'job_id',
				array(
					'job_set_id' => $this->sId,
					'job_done_time IS NOT NULL'
				),
				$fname
			);
			$doneCount = $dbr->numRows( $doneRes );
			if( $doneCount == $this->sJobsCount )
				return 1;
			//return 1 when doneCount == sJobCount
			//(we also set this at a higher level and avoid hitting the wah_jobqueue table alltogehter)
			return round( $doneCount / $this->sJobsCount , 3);
		}
	}
	/*
	 * setups up a new job
	 */
	function doJobSetup(){
		global $wgChunkDuration, $wgDerivativeSettings;
		$fname = 'WahJobManager::doJobSetup';
		$dbw = &wfGetDb( DB_WRITE );
		//figure out how many sub-jobs we will have:
		$length = $this->file->handler->getLength( $this->file );

		$set_job_count = ceil( $length / $wgChunkDuration );

		//first insert the job set
		$dbw->insert('wah_jobset',
			array(
				'set_namespace' => $this->sNamespace,
				'set_title'		=> $this->sTitle,
				'set_jobs_count' => $set_job_count,
				'set_encodekey'	=> $this->sEncodeKey
			),$fname
		);
		$this->sId = $dbw->insertId();

		//generate the job data
		$jobInsertArray = array();
		for( $i=0 ; $i < $set_job_count; $i++ ){
			$encSettingsAry = $wgDerivativeSettings[ $this->sEncodeKey ];
			$encSettingsAry['starttime']= $i*$wgChunkDuration;
			//should be oky that the last endtime is > than length
			$encSettingsAry['endtime']	= $encSettingsAry['starttime'] + $wgChunkDuration;

			$jobJsonAry = array(
				'jobType'		=> 'transcode',
				'fTitle'  		=> $this->sTitle,
				'chunkNumber'	=> $i,
				'encodeSettings'=> $encSettingsAry
			);

			//add starttime and endtime
			$jobInsertArray[] =
				array(
					'job_set_id' => $this->sId,
					'job_assigned_time' => time(),
					'job_json' => ApiFormatJson::getJsonEncode( $jobJsonAry )
				);
		}
		//now insert the jobInsertArray
		$dbw->insert('wah_jobqueue',$jobInsertArray, $fname);
	}

}

?>