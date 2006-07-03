<?php

require_once('forms.php');
require_once('converter.php');
require_once('attribute.php');
require_once('tuple.php');

interface Relation {
	public function getHeading();
	public function getKey();
	public function getTupleCount();
	public function getTuple($index);
}

class ArrayRelation implements Relation {
	protected $heading;
	protected $key;
	protected $tuples = array();
	
	public function __construct($heading, $key) {
		$this->heading = $heading;
		$this->key = $key;
	}
	
	public function addTuple($values) {
		$tuple = new ArrayTuple($this->heading);
		$tuple->setAttributeValuesByOrder($values);

		$this->tuples[] = $tuple;				
	}
	
	public function getHeading() {
		return $this->heading;
	}
	
	public function getKey() {
		return $this->key;	
	}
	
	public function getTupleCount() {
		return count($this->tuples);
	}
	
	public function getTuple($index) {
		return $this->tuples[$index];
	}
}

class ConvertingRelation implements Relation {
	protected $relation;
	protected $converters;
	protected $heading;
	
	public function __construct($relation, $converters) {
		$this->relation = $relation;
		$this->converters = $converters;
		$this->heading = $this->determineHeading();
	}

	public function getHeading() {
		return $this->heading;
	}
	
	public function getKey() {
		return $this->relation->getKey();	
	}
	
	public function getTupleCount() {
		return $this->relation->getTupleCount();	
	}
	
	public function getTuple($index) {
		$tuple = $this->relation->getTuple($index);
		$result = new ArrayTuple($this->heading);
		
		foreach ($this->converters as $converter) 
			$result->setSubTuple($converter->convert($tuple));
			
		return $result;
	}
	
	protected function determineHeading() {
		$attributes = array();

		foreach ($this->converters as $converter) 
			$attributes = array_merge($attributes, $converter->getHeading()->attributes);
			
		return new Heading($attributes);
	}
}

function getQueryAsRelation($sql) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query($sql);

	$attributes = array();
	$fieldCount = $dbr->numFields($queryResult);
	
	for ($i = 0; $i < $fieldCount; $i++)
		$attributes[] = new Attribute($dbr->fieldName($queryResult, $i), "Text");
		
	$heading = new Heading($attributes);	
	$result = new ArrayRelation($heading);
	
	while ($row = $dbr->fetchRow($queryResult)) {
		$tuple = array();
		
		for ($i = 0; $i < $fieldCount; $i++)
			$tuple[] = $row[$i];
			
		$result->addTuple($tuple);
	}

	$dbr->freeResult($queryResult);
	
	return $result;		
}

function getRelationAsHTMLList($relation) {
	$heading = $relation->getHeading();

	$result = getHeadingAsListHeading($heading);
	$result .= '<ul class="wiki-data-unordered-list">';
	
	for($i = 0; $i < $relation->getTupleCount(); $i++) {
		$tuple = $relation->getTuple($i);
		$result .= '<li>';
		$result .= getTupleAsListItem($heading, $tuple);
		$result .= '</li>';
	}
	
	$result .='</ul>';
	return $result;
}

function getHeadingAsListHeading($heading) {
	$result = '<h5>';
	
	foreach($heading->attributes as $attribute) {
		$result .= getAttributeAsText($attribute);
		$result .= ' - ';
	}
	
	$result = rtrim($result, ' - ') . '</h5>';
	return $result;
}

function getAttributeAsText($attribute){
	$type = $attribute->type;
	if (is_a($type, TupleType)) {
		$heading = $type->getHeading();
		foreach($heading->attributes as $innerAttribute) {
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

function getTupleAsListItem($heading, $tuple) {
	$result = '';
	
	foreach($heading->attributes as $attribute) {
		$type = $attribute->type;
		$value = $tuple->getAttributeValue($attribute);
		
		if (is_a($type, TupleType)) {
			$result .= getTupleAsListItem($type->getHeading(), $value);	
		}
		else {
			$result .= convertToHTML($value, $type);			
		}
		$result .= ' - ';
	}
	$result = rtrim($result, ' - ');		
	
	return $result;
}

function getTupleKeyName($tuple, $key) {
	$ids = array();
	
	foreach($key->attributes as $attribute)
		$ids[] = $tuple->getAttributeValue($attribute);
	
	return implode("-", $ids);
}

?>
