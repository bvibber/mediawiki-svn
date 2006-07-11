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
		
		public function getDescriptions(){
			$descriptionAttibute = $this->attributes["DE"];
			if ($descriptionAttibute) {
				$fullDescription = joinLinesWithoutPrefixes($descriptionAttibute);
				return $fullDescription;
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
//			echo $firstLine;
			$this->lines[]=$firstLine;
			$line = fgets($fileHandle);
						
			while ( (!feof($fileHandle)) and ( (getPrefix($line) == $this->prefix) or (getPrefix($line) == "  ") )){
//				echo $line;
				$this->lines[]=$line;								
				$line = fgets($fileHandle);
			}
			return $line; 
		}
	}
?>
