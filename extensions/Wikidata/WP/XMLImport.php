<?php

$depth = array();
$specificXMLParser;

interface XMLElementHandler {
	public function getHandlerForNewElement($name);
	public function setAttributes($attributes);
	public function processData($data);
	public function close();
	public function notify($childHandler);
}

interface XMLParser {
	public function newElement($name, $attributes);
	public function processData($data);
	public function endElement();
}

class BaseXMLParser implements XMLParser {
	public $stack;
	
	public function __construct() {
		$this->stack = array(); 
	}
	
	public function newElement($name, $attributes) {
		$handler = end($this->stack)->getHandlerForNewElement($name);	
		$handler->setAttributes($attributes);
		$this->stack[] = $handler;
	}
	
	public function processData($data){
		end($this->stack)->processData($data);
	}
	
	public function endElement() {
		$handler = array_pop($this->stack);
		$handler->close();
		if (count($this->stack)>0){
			end($this->stack)->notify($handler);			
		} 
	}
}

class DefaultXMLElementHandler implements XMLElementHandler {
	public $name;
	public $attributes;
	public $data;
	
	public function getHandlerForNewElement($name) {
		$result = new DefaultXMLElementHandler;
		$result->name = $name;
		return $result;
	}
	
	public function setAttributes($attributes) {
		$this->attributes = $attributes;	
	} 
	
	public function processData($data) {
		$this->data = $data;
	}
	
	public function close() {
	}
	
	public function notify($childHandler) {
	}
}

//class ChildrenXMLElementHandler implements XMLElementHandler {
//	public $currentChild;
//	public $children;
//	
//	public function __construct() {
//		$this->children = array(); 
//	}
//
//	public function addCurrentChildElementHandler($childName, $childElementHandler) {
//		$this->children[]=$childElementHandler;
//		$this->currentChild = $childElementHandler;
//	}
//	
//	public function closed() {
//		return !($this->currentChild && !$this->currentChild->closed());
//	}
//	
//	public function newElement($childName) {
//		if($this->currentChild && !$this->currentChild->closed()) {
//			return $this->currentChild->newElement($childName);
//		}
//		else {
//			return false;
//		}
//	}
//	
//	public function endElement($name) {
//		if($this->currentChild && !$this->currentChild->closed()){
//			return $this->currentChild->endElement($name);
//		}
//		else {
//			return false;
//		}
//	}
//	
//	public function setAttributes($attributes) {
//		if($this->currentChild && !$this->currentChild->closed()){
//			return $this->currentChild->setAttributes($attributes);
//		}
//		else {
//			return false;
//		}
//	}
//	
//	public function setData($data) {
//		if($this->currentChild && !$this->currentChild->closed()){
//			return $this->currentChild->setData($data);
//		}
//		else {
//			return false;
//		}
//	}
//}
//
//class EmptyXMLElementHandler implements XMLElementHandler {
//	public $name;
//	public $m_closed;
//	
//	public function __construct() {
//		$this->m_closed = false;
//	}
//	
//	public function setAttributes($attributes) {
//		return !$this->closed();
//	}
//	
//	public function closed() {
//		return $this->m_closed;
//	}
//	
//	public function newElement($childName) {
//		return !$this->closed();
//	}
//	
//	public function endElement($name) {
//		if ($this->closed()) {
//			return false;
//		}
//		else {
//			$this->m_closed = ($this->name == $name);
//			return true;	
//		}
//	}
//	
//	public function setData($data) {
//		return !$this->closed();
//	}
//}


function parserNewElement($parser, $name, $attrs){
	global $specificXMLParser;
	$specificXMLParser->newElement($name, $attrs);
}

function parserEndElement($parser, $name){
	global $specificXMLParser;
	$specificXMLParser->endElement();
}

function parserData($parser, $data){
	global $specificXMLParser;
	if ($data != NULL){
		$specificXMLParser->processData($data);	
	}
}

function parseXML($fileHandle, $xmlParser) {
	global $specificXMLParser;
	$specificXMLParser = $xmlParser;
	
	$standardXmlParser = xml_parser_create();
	xml_set_element_handler($standardXmlParser, "parserNewElement", "parserEndElement");
	xml_set_character_data_handler($standardXmlParser, "parserData");
	
	while ($data = fread($fileHandle, 4096)) {
	    if (!xml_parse($standardXmlParser, $data, feof($fileHandle))) {
	        die(sprintf("XML error: %s at line %d",
	                    xml_error_string(xml_get_error_code($standardXmlParser)),
	                    xml_get_current_line_number($standardXmlParser)));
	    }
	}
	xml_parser_free($standardXmlParser);	
}

function startXMLLayout($parser, $name, $attrs) {
	global $depth;
	
	for ($i = 0; $i < $depth[$parser]; $i++) {
	    echo "  ";
	}
	echo "$name\n";
	foreach ($attrs as $key => $value) { 
		for ($i = 0; $i < $depth[$parser]; $i++) {
	   	echo "  ";
		}
		echo "$key = $value\n";    			
	}
	$depth[$parser]++;
 }

function characterXMLLayout($parser, $data) {
	global $depth;
	for ($i = 0; $i < $depth[$parser]; $i++) {
	    echo "  ";
	}
	echo "$data\n";
}

function endXMLLayout($parser, $name) {
    global $depth;
    if ($name == "ENTRY") {
    	die('\nfirst entry end\n');
    }
    $depth[$parser]--;
}

?>
