<?php error_reporting(E_ALL); 

require( "class/pData.class" );
require( "class/pChart.class" );
require( "DumpStatsTypes.php" );

/*
 * Top Level class for creating XML graphs from pCharts
 */

class XMLDumpStats {

	private $wiki = '';
	private $step = '';
	private $doPages = false;
	private $doRevs = false;

	private $chartWidth = 900;
	private $chartHeight = 300;

	private $pChartPath =  '/var/www/sandbox/pChart/';
	private $pChartFontPath = '/backups/pChart/Fonts/'; 
	private $font = 'tahoma.ttf';

	function __construct ( $wiki ) {
		$this->wiki = $wiki;
	}

	// Set the step name for tagging purposes
	function setStep( $step ) {
		$this->step = $step;
	}

	// Pull the step were charting for
	function getStep() {
		return $this->step;
	}

	// Pull the wiki were charting for
	function getWiki() {
		return $this->wiki;
	}	

	// Load from data file and setup basic axis		
	function newFromFile( $file ) {
	}

	// Create all the basics up to plotting
	function createGraph() {
		$this->Graph = new pChart( $this->chartWidth, /*$this->chartHeight*/ 900 ); #bug

		$this->Graph->setFontProperties( $this->pChartFontPath . $this->font, 8 );   
		$this->Graph->setGraphArea(70,30,680,200);   
		$this->Graph->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);   
		$this->Graph->drawRoundedRectangle(5,5,695,225,5,230,230,230);   
		$this->Graph->drawGraphArea(255,255,255,TRUE);
		$this->Graph->drawScale($this->DataSet->GetData(),$this->DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,0,TRUE,250);   
		$this->Graph->drawGrid(4,TRUE,230,230,230,50);
		$this->Graph->setFontProperties( $this->pChartFontPath . $this->font, 6);   
		$this->Graph->drawTreshold(0,143,55,72,TRUE,TRUE); 
	}

	// Plot
	function drawPoints() {
 		$this->Graph->drawLineGraph($this->DataSet->GetData(),$this->DataSet->GetDataDescription()); 
	}

	// Finish Up
	function finalizeGraph() {
		 $this->Graph->setFontProperties( $this->pChartFontPath . $this->font,8);
		 $this->Graph->drawLegend(75,35,$this->DataSet->GetDataDescription(),255,255,255);
 		 $this->Graph->setFontProperties( $this->pChartFontPath . $this->font ,10);
	}

	function writeTitle() {
 		 $this->Graph->drawTitle(60,22, $this->step . " " . "page rate for " . $this->wiki,50,50,50,585);
	}

	// Write Out
	function renderGraph( $filename ) {
 		 $this->Graph->Render("$filename.png");
	}

	// Build everything
	function build() {
		$this->createGraph();
		$this->drawPoints();
		$this->finalizeGraph();
		$this->writeTitle();
	}
}
?>
