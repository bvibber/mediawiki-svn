<?php

class RepoStats {
	private $repo;

	public $time;
	
	// Statistics
	public $revisions,
		$authors,
		$states,
		$fixmes;

	public static function newFromRepo( CodeRepository $repo ) {
		global $wgMemc;

		$key = wfMemcKey( 'codereview1', 'stats', $repo->getName() );
		$stats = $wgMemc->get( $key );
		wfDebug( "{$repo->getName()} repo stats: cache " );
		if ( $stats ) {
			wfDebug( "hit\n" );
			return $stats;
		}
		wfDebug( "miss\n" );
		$stats = new RepoStats( $repo );
		$stats->generate();
		$wgMemc->set( $key, $stats, 12 * 60 * 60 ); // 12 hours
		return $stats;
	}

	public function __construct( CodeRepository $repo ) {
		$this->repo = $repo;
		$this->time = wfTimestamp( TS_MW );
	}

	private function generate() {
		wfProfileIn( __METHOD__ );
		$dbr = wfGetDB( DB_SLAVE );

		$this->revisions = $dbr->selectField( 'code_rev',
			'COUNT(*)',
			array( 'cr_repo_id' => $this->repo->getId() ),
			__METHOD__
		);

		$this->authors = $dbr->selectField( 'code_rev',
			'COUNT(DISTINCT cr_author)',
			array( 'cr_repo_id' => $this->repo->getId() ),
			__METHOD__
		);

		$this->states = array();
		$res = $dbr->select( 'code_rev',
			array( 'cr_status', 'COUNT(*) AS revs' ),
			array( 'cr_repo_id' => $this->repo->getId() ),
			__METHOD__,
			array( 'GROUP BY' => 'cr_status' )
		);
		foreach ( $res as $row ) {
			$this->states[$row->cr_status] = $row->revs;
		}

		$this->fixmes = array();
		$res = $dbr->select( 'code_rev',
			array( 'COUNT(*) AS revs', 'cr_author' ),
			array( 'cr_repo_id' => $this->repo->getId(), 'cr_status' => 'fixme' ),
			__METHOD__,
			array(
				'GROUP BY' => 'cr_author',
				'ORDER BY' => 'revs DESC',
				'LIMIT' => 500,
			)
		);
		foreach ( $res as $row ) {
			$this->fixmes[$row->cr_author] = $row->revs;
		}

		wfProfileOut( __METHOD__ );
	}
}