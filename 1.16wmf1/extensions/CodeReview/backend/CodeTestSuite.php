<?php

class CodeTestSuite {
	public static function newFromRow( CodeRepository $repo, $row ) {
		$suite = new CodeTestSuite();
		$suite->id = intval( $row->ctsuite_id );
		$suite->repo = $repo;
		$suite->repoId = $repo->getId();
		$suite->branchPath = $row->ctsuite_branch_path;
		$suite->name = $row->ctsuite_name;
		$suite->desc = $row->ctsuite_desc;
		return $suite;
	}
	
	function getRun( $revId ) {
		return CodeTestRun::newFromRevId( $this, $revId );
	}

	function setStatus( $revId, $status ) {
		$run = $this->getRun( $revId );
		if( $run ) {
			$run->setStatus( $status );
		} else {
			$run = CodeTestRun::insertRun( $this, $revId, $status );
		}
		return $run;
	}
	
	function saveResults( $revId, $results ) {
		$run = $this->getRun( $revId );
		if( $run ) {
			$run->saveResults( $results );
		} else {
			$run = CodeTestRun::insertRun( $this, $revId, "complete", $results );
		}
		return $run;
	}
}
