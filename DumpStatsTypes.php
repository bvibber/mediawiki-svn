<?php error_reporting(E_ALL);

/*
 * Per output style classes for pages and revisions
 */

class XMLDumpStatsRevision extends XMLDumpStats {

	private $doRevs = true;

	function newFromFile( $file ) {
		if ( ! file_exists( $file ) ) { 
			die( "File $file doesn't exist\n" );
		}

		$this->DataSet = new pData;

		$this->DataSet->ImportFromCSV( $file , ",", array(1), FALSE, 0 ); # Need to know # of columns
		$this->DataSet->SetSerieName( "revisions", "Serie1" );
		$this->DataSet->SetYAxisName( "revs/sec" ); 
		$this->DataSet->SetXAxisName( "rev id" );
		$this->DataSet->AddAllSeries();
		$this->DataSet->SetAbsciseLabelSerie();
	}

	function writeTitle() {
		$this->Graph->drawTitle( 60, 22, $this->getStep() . " " . "revision rate for " . 
			$this->getWiki(), 50, 50, 50, 585);	
	}
}

class XMLDumpStatsPages extends XMLDumpStats {

	private $doPages = true;

	function newFromFile( $file ) {
		if ( ! file_exists( $file ) ) { 
			die( "File $file doesn't exist\n" );
		}

		$this->DataSet = new pData;

		$this->DataSet->ImportFromCSV( $file , ",", array(1), FALSE, 0 ); # Need to know # of columns
		$this->DataSet->SetSerieName( "pages","Serie1" );
		$this->DataSet->SetYAxisName( "pages/sec" ); 
		$this->DataSet->SetXAxisName( "page id " );
		$this->DataSet->AddAllSeries();
		$this->DataSet->SetAbsciseLabelSerie();
	}

	function writeTitle() {
		$this->Graph->drawTitle( 60, 22, $this->getStep() . " " . "page rate for " . 
			$this->getWiki(), 50, 50, 50, 585);	
	}
}
?>
