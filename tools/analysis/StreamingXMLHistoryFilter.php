#!/usr/bin/php -q
<?php
if(count($argv) != 3){
	print("\n\tUsage: $argv[0] <inputfile> <historyfile>\n");
	exit(-1);		
}

$parser = new StreamingXMLHistoryFilter($argv[1], $argv[2]);
$parser->run();

class StreamingXMLHistoryFilter{

	public $fileList;
	public $pageNameArray;
	public $historyFile;
	
	public function __construct( $fileList, $historyFile){
		$this->pageNameArray = array();
		$this->fileList = $fileList;
		$this->historyFile = $historyFile;
	}
    	
	public function writeOut($title, $pageText){
		$file = fopen($title, "a+");
		fwrite($file,$pageText);
		fclose($file);
	}
	
	public function createFileList(){
		$fileListHandle = fopen($this->fileList, "r");
		while (!feof($fileListHandle)) {
        	$fName = fgets($fileListHandle);
        	$this->pageNameArray[] = trim($fName);
    	}
    	fclose($fileListHandle);
	}
	
	
	public function run(){
		
		$this->createFileList();
		
		$reader = new XMLReader();
		$reader->open($this->historyFile);		
		
		// NOTE: in the interests of a smaller memory footprint
		// this relies on the XML title being the next element after the page
		while ( $reader->read()){
			if ( $reader->nodeType == XMLREADER::ELEMENT
				&& $reader->localName == "page") {
					$pageText = $reader->readOuterXML();		
					$reader->read(); //text element
					$reader->next(); //next node
					$title = $reader->readInnerXML();
					if(in_array($title, $this->pageNameArray)){
						$this->writeOut($title, $pageText);			
					}
				}//revision	
		} //while	
	} //run
}

