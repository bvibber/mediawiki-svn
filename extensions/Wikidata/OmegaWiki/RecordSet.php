<?php

require_once('forms.php');
require_once('converter.php');
require_once('Attribute.php');
require_once('Record.php');

interface RecordSet {
	public function getStructure();
	public function getKey();
	public function getRecordCount();
	public function getRecord($index);
}

class ArrayRecordSet implements RecordSet {
	protected $structure;
	protected $key;
	protected $records = array();
	
	public function __construct($structure, $key) {
		$this->structure = $structure;
		$this->key = $key;
	}
	
	public function add($record) {
		$this->records[] = $record;
	}

	public function addRecord($values) {
		$record = new ArrayRecord($this->structure);
		$record->setAttributeValuesByOrder($values);

		$this->records[] = $record;				
	}
	
	public function getStructure() {
		return $this->structure;
	}
	
	public function getKey() {
		return $this->key;	
	}
	
	public function getRecordCount() {
		return count($this->records);
	}
	
	public function getRecord($index) {
		return $this->records[$index];
	}

	/**
	 * @return carriage return separated list of values
	 */
	public function __tostring() {
		return implode("\n",$this->records);
	}
}

class ConvertingRecordSet implements RecordSet {
	protected $relation;
	protected $converters;
	protected $structure;
	
	public function __construct($relation, $converters) {
		$this->relation = $relation;
		$this->converters = $converters;
		$this->structure = $this->determineStructure();
	}

	public function getStructure() {
		return $this->structure;
	}
	
	public function getKey() {
		return $this->relation->getKey();	
	}
	
	public function getRecordCount() {
		return $this->relation->getRecordCount();	
	}
	
	public function getRecord($index) {
		$record = $this->relation->getRecord($index);
		$result = new ArrayRecord($this->structure);
		
		foreach ($this->converters as $converter) 
			$result->setSubRecord($converter->convert($record));
			
		return $result;
	}
	
	protected function determineStructure() {
		$attributes = array();

		foreach ($this->converters as $converter) 
			$attributes = array_merge($attributes, $converter->getStructure()->attributes);
			
		return new Structure($attributes);
	}
}

function getRelationAsHTMLList($relation) {
	$structure = $relation->getStructure();

	$result = getStructureAsListStructure($structure);
	$result .= '<ul class="wiki-data-unordered-list">';
	
	for($i = 0; $i < $relation->getRecordCount(); $i++) {
		$record = $relation->getRecord($i);
		$result .= '<li>';
		$result .= getRecordAsListItem($structure, $record);
		$result .= '</li>';
	}
	
	$result .='</ul>';
	return $result;
}

function getStructureAsListStructure($structure) {
	$result = '<h5>';
	
	foreach($structure->attributes as $attribute) {
		$result .= getAttributeAsText($attribute);
		$result .= ' - ';
	}
	
	$result = rtrim($result, ' - ') . '</h5>';
	return $result;
}

function getAttributeAsText($attribute){
	$type = $attribute->type;
	if (is_a($type, RecordType)) {
		$structure = $type->getStructure();
		foreach($structure->attributes as $innerAttribute) {
			$result .= getAttributeAsText($innerAttribute);
			$result .= ' - ';
		}
		$result = rtrim($result, ' - ');
	}
	else {
		$result = $attribute->name;
	}
	return $result;
}

function getRecordAsListItem($structure, $record) {
	$result = '';
	
	foreach($structure->attributes as $attribute) {
		$type = $attribute->type;
		$value = $record->getAttributeValue($attribute);
		
		if (is_a($type, RecordType)) {
			$result .= getRecordAsListItem($type->getStructure(), $value);	
		}
		else {
			$result .= convertToHTML($value, $type);			
		}
		$result .= ' - ';
	}
	$result = rtrim($result, ' - ');		
	
	return $result;
}

function getRecordKeyName($record, $key) {
	$ids = array();
	
	foreach($key->attributes as $attribute)
		$ids[] = $record->getAttributeValue($attribute);
	
	return implode("-", $ids);
}

function splitRecordSet($recordSet, $groupAttribute) {
	$result = array();
	$structure = $recordSet->getStructure();
	$key = $recordSet->getKey();
	
	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);
		$groupAttributeValue = $record->getAttributeValue($groupAttribute);
		$groupRecordSet = $result[$groupAttributeValue];
		
		if ($groupRecordSet == null) {
			$groupRecordSet = new ArrayRecordSet($structure, $key);
			$result[$groupAttributeValue] = $groupRecordSet;
		}
		
		$groupRecordSet->add($record);
	}
	
	return $result; 
}

?>
