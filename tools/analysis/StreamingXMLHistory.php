#!/usr/bin/php -q
<?php
//keep PHP 5.3 happy
date_default_timezone_set("UTC");

if(count($argv) != 3){
	print("\n\tUsage: $argv[0] <inputfile> <outputfile>\n");
	exit(-1);		
}

$parser = new StreamingXMLHistoryParser($argv[1], $argv[2]);
$parser->run();


 class Edit{
 	public $isAccepted;
 	
 	public function __construct($isAccepted){
 		$this->isAccepted = $isAccepted;
 	}
 	
 	public function accept(){
 		$this->isAccepted = true;
 	}
 	
 	public function reject(){
 		$this->isAccepted = false;
 	}
 	
 }


 class Revert{
	public $revertToIndex;
	public $selfIndex;
	public $isAccepted; //true = accepted, false = rejected
	public $revTypes;
	
	public function setStatus( $status ){
		$this->isAccepted = $status;
		$this->updateHistory();
	}
	
	public function updateHistory(){
		for($i = ($this->selfIndex -1); $i > $this->revertToIndex; $i--){
			if(get_class( $this->revTypes[$i] ) == "Revert" ){
				$this->revTypes[$i]->setStatus( !$this->isAccepted );	
			}
			else{
				//we're accepting a revert, which means rejecting everything in between
				$this->revTypes[$i]->isAccepted = !$this->isAccepted;
			}	
		}
	}
	
	public function __construct($selfIndex, &$revTypes, $isAccepted, $revertToIndex){
		$this->selfIndex = $selfIndex;
		$this->revTypes = &$revTypes;
		$this->isAccepted = $isAccepted;
		$this->revertToIndex = $revertToIndex;
	}
}


class StreamingXMLHistoryParser{

	public $inputFileName;
	public $outputFileName;
	public $outputFile;

	//md5 hashes of the revision texts
	public $md5History;
	
	//revision types
	public $revTypes;
	
	//size of previous revision
	public $oldSize;
	
	public function __construct( $inputFN, $outputFN){
		$this->inputFileName = $inputFN;
		$this->outputFileName = $outputFN;
		$this->outputFile = fopen($this->outputFileName, "w+");
		$this->md5History = array();
		$this->revTypes = array();
		$this->oldSize = 0;
	}

	public function writeRevisionStatus(){
		$csvOutput = fopen($this->outputFileName.".REVSTATUS", "w+");
		fputcsv($csvOutput, array("status"));
		
		$counter = 0;
		foreach($this->revTypes as $i){
			$csvLine = "";
			if( get_class($i) == "Revert" ){
				if( ($i->selfIndex - $i->revertToIndex) == 1){
					$csvLine .= "status-change-";
				}
				else{
					$csvLine .= "Revert-";	
				}
			}
			$csvLine .= ($i->isAccepted)?"accepted":"rejected";
			$csvData = array( $csvLine );
			fputcsv($csvOutput, $csvData);
			$counter++;
		}
		
		fclose($csvOutput);
	}
		
	public function writeCSVHeader(){
		$csvData = array(
			"Rev ID",
			"UNIX Timestamp",
			"Contributor ID", 
			"Comment",
			"Revision MD5",
			"new?",
			"edit size",
			"net size change",
			"anonymous?"
		);
		fputcsv($this->outputFile, $csvData);
	}
	
	public function run(){
		$reader = new XMLReader();
		$reader->open($this->inputFileName);		
		$this->writeCSVHeader();
		$current_rev = 0;
		//read each revision
		while ( $reader->read()){
			if ( $reader->nodeType == XMLREADER::ELEMENT
				&& $reader->localName == "revision") {
					
					$current_rev++;
					$this->parseRev($reader->readOuterXML());
				}//revision	
		} //while
		$this->writeRevisionStatus();
		
	}
	
	
	//foreach revision...
	public function parseRev($xmlTEXT){
		$revision = new SimpleXMLElement($xmlTEXT);
		$textSize = strlen($revision->text);
		
		$md5 = md5($revision->text);
		$isNew = "no";
		
		$revertIndex = array_search($md5, $this->md5History);
		
		if($revertIndex === FALSE ){
			$isNew = 'yes';
			$this->revTypes[] = new Edit(true);
		}
		else{
			$revert = new Revert(count($this->revTypes), $this->revTypes, true, $revertIndex);
			$this->revTypes[] = $revert;
			$revert->updateHistory();
		}
		$this->md5History[] = $md5;
		
		$csvData = array(
			$revision->id,
			strtotime($revision->timestamp),
			isset($revision->contributor->username)?
				$revision->contributor->username : $revision->contributor->ip, 
			isset($revision->comment) ?
				(preg_replace("[\n|\r]", " ", $revision->comment)) : "",
			$md5,
			$isNew,
			$textSize,
			$textSize - $this->oldSize,
			isset($revision->contributor->username)? "no":"yes"
		);
		$this->oldSize = $textSize;
		fputcsv($this->outputFile, $csvData);
	}
	
	
}



