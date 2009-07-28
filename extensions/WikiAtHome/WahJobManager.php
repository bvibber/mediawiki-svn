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
		}
	}
	/*
	 * setups upa new job
	 */
	function doJobSetup(){
		global $wgChunkDuration;
		$dbw = &wfGetDb( DB_WRITE );
		//figure out how many sub-jobs we will have: 
		$length = $this->file->handler->getLength( $this->file );
		
		$set_job_count = ceil( $length / $wgChunkDuration); 
		
		//first insert the job set
		$res = $dbw->insert('wah_jobset', 
			array(
				'set_namespace' => $this->sNamespace,
				'set_title'		=> $this->set_title,
				'set_jobs_count' => $set_job_count,
				'set_encodekey'	=> $this->sEncodeKey			
			)
		);
		$this->sId = $dbw->insertId();
		
		//generate the job data 
		
	}
	
}

?>