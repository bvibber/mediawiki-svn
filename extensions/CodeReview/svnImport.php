<?php

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = dirname( __FILE__ ) . '/../..';
}
require( "$IP/maintenance/Maintenance.php" );

class SvnImport extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->mDescription = "Import revisions to Code Review from a Subversion repo";
		$this->addOption( 'precache', 'Pre-cache diffs for last N revisions.  ' .
						'May be a positive integer, 0 (for none) or \'all\'.  Default is 0', false, true );
		$this->addArg( 'repo', 'The name of the repo. Use \'all\' to import from all defined repos' );
		$this->addArg( 'start', "The revision to begin the import from.  If not specified then " .
						"it starts from the last repo imported to the wiki.  Ignored if " .
						"'all' is specified for <repo>", false );
	}

	public function execute() {
		$cacheSize = 0;
		if ( $this->hasOption( 'precache' ) ) {
			$cacheSize = $this->getOption( 'precache' );
			if ( strtolower( $cacheSize ) !== "all" ) {
				if ( preg_match( '/^\d+$/', $cacheSize ) ) {
					$cacheSize = intval( $cacheSize );
				} else {
					$this->error( "Invalid argument for --precache (must be a positive integer, 0 or 'all')", true );
				}
			}
		}

		$repo = $this->getArg();
		if ( $repo == "all" ) {
			$repoList = CodeRepository::getRepoList();
			foreach ( $repoList as $repoInfo ) {
				$this->importRepo( $repoInfo->getName(), null, $cacheSize );
			}
		} else {
			$this->importRepo( $repo, $this->getArg( 1 ), $cacheSize );
		}
	}

	private function importRepo( $repoName, $start = null, $cacheSize = 0 ) {
		global $wgCodeReviewImportBatchSize;

		$repo = CodeRepository::newFromName( $repoName );

		if ( !$repo ) {
			$this->error( "Invalid repo $repoName" );
			return;
		}

		$svn = SubversionAdaptor::newFromRepo( $repo->getPath() );
		$lastStoredRev = $repo->getLastStoredRev();

		$chunkSize = $wgCodeReviewImportBatchSize;

		$startTime = microtime( true );
		$revCount = 0;
		$start = ( $start !== null ) ? intval( $start ) : $lastStoredRev + 1;
		if ( $start > ( $lastStoredRev + 1 ) ) {
			$this->error( "Invalid starting point r{$start}" );
			return;
		}

		$this->output( "Syncing repo $repoName from r$start to HEAD...\n" );

		if ( !$svn->canConnect() ) {
			$this->error( "Unable to connect to repository." );
			return;
		}

		while ( true ) {
			$log = $svn->getLog( '', $start, $start + $chunkSize - 1 );
			if ( empty( $log ) ) {
				# Repo seems to give a blank when max rev is invalid, which
				# stops new revisions from being added. Try to avoid this
				# by trying less at a time from the last point.
				if ( $chunkSize <= 1 ) {
					break; // done!
				}
				$chunkSize = max( 1, floor( $chunkSize / 4 ) );
				continue;
			} else {
				$start += $chunkSize;
			}
			if ( !is_array( $log ) ) {
				var_dump( $log ); // @TODO: cleanup :)
				$this->error( 'wtf', true );
			}
			foreach ( $log as $data ) {
				$revCount++;
				$delta = microtime( true ) - $startTime;
				$revSpeed = $revCount / $delta;

				$codeRev = CodeRevision::newFromSvn( $repo, $data );
				$codeRev->save();

				$this->output( sprintf( "%d %s %s (%0.1f revs/sec)\n",
					$codeRev->mId,
					wfTimestamp( TS_DB, $codeRev->mTimestamp ),
					$codeRev->mAuthor,
					$revSpeed ) );
			}
			wfWaitForSlaves( 5 );
		}

		if ( $cacheSize !== 0 ) {
			$dbw = wfGetDB( DB_MASTER );
			$options = array( 'ORDER BY' => 'cr_id DESC' );

			if ( $cacheSize == "all" ) {
				$this->output( "Pre-caching all uncached diffs...\n" );
			} else {
				if ( $cacheSize == 1 ) {
					$this->output( "Pre-caching the latest diff...\n" );
				} else {
					$this->output( "Pre-caching the latest $cacheSize diffs...\n" );
				}
				$options['LIMIT'] = $cacheSize;
			}

			$res = $dbw->select( 'code_rev', 'cr_id',
				array( 'cr_repo_id' => $repo->getId(), 'cr_diff IS NULL OR cr_diff = ""' ),
				__METHOD__,
				$options
			);
			while ( $row = $dbw->fetchObject( $res ) ) {
				$repo->getRevision( $row->cr_id );
				$repo->getDiff( $row->cr_id ); // trigger caching
				$this->output( "Diff r{$row->cr_id} done\n" );
			}
		}
		else {
			$this->output( "Pre-caching skipped.\n" );
		}
		$this->output( "Done!\n" );
	}
}

$maintClass = "SvnImport";
require_once( DO_MAINTENANCE );
