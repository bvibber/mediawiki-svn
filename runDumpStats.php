<?php error_reporting(E_ALL); 

/* 
 * Controller class for XML Stats Charts
 */

include "DumpStats.php";

class DumpStatsGraphBuilder {

	private $dbList = '/backups/db/all.dblist';
	private $privateDBList = '/home/wikipedia/common/private.dblist';
	private $statsFile = 'stats';

	private $steps = array( 'abstract', 'articles', 'xmlstub', 'meta-current', 'meta-history' );

	function build() {
		$this->loadWikis();

		$this->wikiList = array( 'elwiki' ); # hack

		foreach( $this->wikiList as $wiki ) {
			$wiki = trim( $wiki ) ;
			foreach( $this->steps as $step ) {

				$wikiStatsFile = "$wiki-$step-" . $this->statsFile;

				// Pages
				$stats = new XMLDumpStatsPages( $wiki ) ;
				$stats->setStep( $step );
				$stats->newFromFile( 'data' . "/" . $wikiStatsFile ) ;
				$stats->build();
				$filename = "$wiki-$step-pages";
				$stats->renderGraph( $filename );
				 
				// Revisions
				$revs = new XMLDumpStatsRevision( $wiki ) ;
				$revs->setStep( $step );
				$revs->newFromFile( 'data' . "/" . $wikiStatsFile ) ;
				$revs->build();
				$filename = "$wiki-$step-revs";
				$revs->renderGraph( $filename );  
			}
		}
	}

	function loadWikis() {
		$this->wikiList = file( $this->dbList );
	}

}

$builder = new DumpStatsGraphBuilder();
$builder->build();

?>
