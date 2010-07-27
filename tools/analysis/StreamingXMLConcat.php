#!/usr/bin/php -q

<?php

date_default_timezone_set("UTC");

if(count($argv) != 4){
	print("\n\tUsage: $argv[0] <pasthist> <recenthist> <output> \n");
	exit(-1);		
}

$concat = new XMLHistoryConcat($argv[1], $argv[2], $argv[3]);
$concat->run();

class XMLHistoryConcat{

	public $pastFileName;
	public $recentFileName;
	public $outputFile;
	public $newContent;
	public $id;
	
	public function __construct($pastFN, $recentFN, $outputFN){
		$this->pastFileName = $pastFN;
		$this->recentFileName = $recentFN;
		$this->outputFile = $outputFN;
	}
	
	//gets new content and sets the overlap revision id
	public function getNewContent($recentFN){
		
		$reader = new XMLReader();
		$reader->open($recentFN);
		$foundfirst = false;
		$id = null;
		
		while ($reader->read()){
			if ($reader->nodeType == XMLREADER::ELEMENT
				&& $reader->localName == "revision" && $foundfirst == false) {
					$revision = new SimpleXMLElement($reader->readOuterXml());
					$foundfirst = true;
					$this->id = $revision->id;
			}
			if ($reader->nodeType == XMLREADER::ELEMENT
				&& $reader->localName == "revision" && $foundfirst == true){
				$this->newContent .= $reader->readOuterXml()."\n";
			}
		}
	}
	
	public function run(){
		$newfile = fopen($this->outputFile, "w");
		fwrite($newfile, "<page>\n");
		$reader = new XMLReader();
		$reader->open($this->pastFileName);
		$this->getNewContent($this->recentFileName);
		
		while ($reader->read()){
			//copy article title/id info
			if ($reader->nodeType == XMLREADER::ELEMENT
				&& ($reader->localName == "title")){
					fwrite($newfile, $reader->readOuterXml()."\n");
				}
			//write past content until overlap id
			if ($reader->nodeType == XMLREADER::ELEMENT
				&& $reader->localName == "revision") {
					$revision = new SimpleXMLElement($reader->readOuterXml());
					if(strcmp($revision->id, $this->id) != 0){
						fwrite($newfile, $reader->readOuterXml()."\n");
					}
					else{
						echo "Overlap at: ".$this->id;
						break;
					}
			}
		}
		
		//write new content
		fwrite($newfile, $this->newContent);
		fwrite($newfile, "</page>");
	}
}
?>