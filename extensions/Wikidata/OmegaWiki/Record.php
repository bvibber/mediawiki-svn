<?php

require_once('Attribute.php');

interface Record {
	public function getStructure();
	public function getAttributeValue($attribute);
	public function project($structure);
}

class ArrayRecord implements Record {
	protected $structure;
	protected $values = array();
	
	public function __construct($structure) {
		$this->structure = $structure;
	}
	
	public function getStructure() {
		return $this->structure;
	}
	
	public function getAttributeValue($attribute) {
		return $this->values[$attribute->id];
	}
	

	public function project($structure) {
		$result = project($this, $structure);
	}

	public function setAttributeValue($attribute, $value) {
		$this->values[$attribute->id] = $value;
	}
	
	public function setAttributeValuesByOrder($values) {
		for ($i = 0; $i < count($this->structure->attributes); $i++)
			$this->values[$this->structure->attributes[$i]->id] = $values[$i];
	}
	
	public function setSubRecord($record) {
		foreach($record->getStructure()->attributes as $attribute)
			$this->values[$attribute->id] = $record->getAttributeValue($attribute);
	}
}

function project($record, $structure) {
	$result = new ArrayRecord($structure);
	
	foreach ($structure->attributes as $attribute) {
		$type = $attribute->type;
		$value = $record->getAttributeValue($attribute);
		
		if ($type instanceof RecordType)
			$result->setAttributeValue($attribute, project($record, $type->getStructure()));
		else
			$result->setAttributeValue($attribute, $value);
	}
		
	return $result;
}

function equalRecords($structure, $lhs, $rhs) {
	$result = true;
	$attributes = $structure->attributes;
	$i = 0;
	
	while($result && $i < count($attributes)) {
		$attribute = $attributes[$i];
		$type = $attribute->type;
		$lhsValue = $lhs->getAttributeValue($attribute);
		$rhsValue = $rhs->getAttributeValue($attribute);
		
		if ($type instanceof RecordType)
			$result = equalRecords($type->getStructure(), $lhsValue, $rhsValue);
		else
			$result = $lhsValue == $rhsValue;
			
		$i++;
	}
	
	return $result;
}

class RecordStack {
	protected $stack = array();
	
	public function push($record) {
		$this->stack[] = $record;
	}
	
	public function pop() {
		return array_pop($this->stack);
	}
	
	public function peek($level) {
		return $this->stack[count($this->stack) - $level - 1];
	}
}

?>
