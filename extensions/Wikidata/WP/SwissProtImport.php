<?php

class SwissProtImportEntry {
	public $attributes;
	
	public function __construct() {
		$this->attributes = array();
	}
	
	public function import($fileHandle){
		$line = fgets($fileHandle);
		while((!feof($fileHandle)) and (getPrefix($line) != "//")){
			$attribute = $this->getEntryAttribute($line);
			$line = $attribute->import($line, $fileHandle);
		} 
	}
	
	public function echoEntry(){
		foreach ($this->attributes as $prefix => $attribute) {
			foreach ($attribute->lines as $line) {
				echo $line;					
			}
		}
	}
	
	private function getEntryAttribute($line){
    $line = rtrim($line,"\n");
		$prefix = getPrefix($line);
    if (!array_key_exists($prefix, $this->attributes)) {
    	$this->attributes[$prefix]=new SwissProtEntryAttribute($prefix);	
    }
    return $this->attributes[$prefix]; 	    
	}
	
	public function getIdentifier(){
		$idAttribute = $this->attributes["ID"];
		if ($idAttribute) {
			$line = $idAttribute->lines[0];
			$line = stripPrefix($line);
			$endIdentifierPosition = strpos($line, " ");
			return substr($line, 0, $endIdentifierPosition);				
		}
	}
	
	public function getDescriptionAttribute(){
		$descriptionAttibute = $this->attributes["DE"];
		if ($descriptionAttibute) {
			return new DescriptionSwissProtEntryAttribute($descriptionAttibute);
		}
	}
}

function getPrefix($line) {
	return substr($line, 0, 2);			
}

function stripPrefix($line) {
	$line = substr($line, 2, strlen($line)-2);		
	return ltrim($line);
}

function joinLinesWithoutPrefixes($attribute) {
	$result = "";
	foreach ($attribute->lines as $line) {
		$unPrefixedLine = stripPrefix($line);
		$unPrefixedLine = rtrim($unPrefixedLine,"\n");
		$result .= $unPrefixedLine;  
	}
	return $result;
}

class SwissProtEntryAttribute {
	public $prefix;
	public $lines;

	public function __construct($prefix) {
		$this->prefix = $prefix;
		$this->lines = array();
	}
	
	public function import($firstLine, $fileHandle){
		$this->lines[]=$firstLine;
		$line = fgets($fileHandle);
					
		while ( (!feof($fileHandle)) and ( (getPrefix($line) == $this->prefix) or (getPrefix($line) == "  ") )){
			$this->lines[]=$line;								
			$line = fgets($fileHandle);
		}
		return $line; 
	}
}

class DescriptionSwissProtEntryAttribute {
	private $attribute;
	private $includesString;
	private $containsString;
	public $protein;
	public $containsProteins;
	public $includesProteins;
	public $isFragment;
	
	public function __construct($attribute) {
		$this->attribute = $attribute;
		$this->parse();
	}
	
	public function parse() {
		$fullDescription = rtrim(joinLinesWithoutPrefixes($this->attribute), ".");
		$fragmentPosition = strpos($fullDescription, "(Fragment");
					
		if ($this->isFragment = ($fragmentPosition!==false)) {
			$fullDescription = rtrim(substr($fullDescription, 0, $fragmentPosition));
		}
				
		$startContainsPosition = strpos($fullDescription, "[Contains:");
		$startIncludesPosition = strpos($fullDescription, "[Includes:");
		
		if (($startContainsPosition!==false) or ($startIncludesPosition!==false)) {
			$endProteinPosition = max($startContainsPosition, $startIncludesPosition);
			$this->getContainsAndIncludesStrings($fullDescription, $startContainsPosition, $startIncludesPosition);
		}
		else {
			$endProteinPosition = strlen($fullDescription);
		}  
		$proteinString = rtrim(substr($fullDescription, 0, $endProteinPosition));
		$this->protein = new Protein($proteinString);
	}
	
	private function getContainsAndIncludesStrings($fullDescription, $startContainsPosition, $startIncludesPosition) {
			if ($startContainsPosition===false) {
				$startContainsPosition = strlen($fullDescription);
			}
			if ($startIncludesPosition===false) {
				$startIncludesPosition = strlen($fullDescription);
			}
			
			if ($startContainsPosition > $startIncludesPosition) {
				$endIncludesPosition = strpos($fullDescription, "]");
				$endContainsPosition = strpos($fullDescription, "]", $endIncludesPosition+1);
			}
			else {
				$endContainsPosition = strpos($fullDescription, "]");
				$endIncludesPosition = strpos($fullDescription, "]", $endContainsPosition+1);
			}
			$this->includesString = trim(substr($fullDescription, $startIncludesPosition + 10, $endIncludesPosition-$startIncludesPosition-10));
			$this->containsString = trim(substr($fullDescription, $startContainsPosition + 10, $endContainsPosition-$startContainsPosition-10));
	}
}

class Protein {
	public $name;
	public $synonyms;
	
	public function __construct($nameSynonymsString) {
		$openingBracketPosition = strpos($nameSynonymsString, "(");
		if ($openingBracketPosition === false) {
			$this->name = trim($nameSynonymsString);
		}
		else {
			$this->name = trim(substr($nameSynonymsString, 0, $openingBracketPosition));
			$nameSynonymsString = substr($nameSynonymsString, $openingBracketPosition, strlen($nameSynonymsString)-$openingBracketPosition);
			$openingBracketPosition = strpos($nameSynonymsString, "(");
		}
		while($openingBracketPosition !== false) {
			$closingBracketPosition = strpos($nameSynonymsString, ")");
			$this->synonyms[]= trim(substr($nameSynonymsString, $openingBracketPosition+1, $closingBracketPosition - $openingBracketPosition-1));
			$nameSynonymsString = substr($nameSynonymsString, $closingBracketPosition+1, strlen($nameSynonymsString)-$closingBracketPosition);
			$openingBracketPosition = strpos($nameSynonymsString, "(");	
		}	
	}
}
	
?>
