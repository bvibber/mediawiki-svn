<?php

require_once('forms.php');

class Attribute {
	public $id = "";	
	public $name = "";
	public $type = "";
	
	public function __construct($id, $name, $type) {
		$this->id = $id;	
		$this->name = $name;
		$this->type = $type;
	}
}

class Heading {
	public $attributes;
	
	public function __construct($attributes) {
		$this->attributes = $attributes;
	}
}

interface RelationModel {
	public function getHeading();
	public function getKey();
	public function getTupleCount();
	public function getTuple($tuple);
}

class ArrayRelation implements RelationModel {
	protected $heading;
	protected $key;
	protected $tuples = array();
	
	public function __construct($heading, $key) {
		$this->heading = $heading;
		$this->key = $key;
	}
	
	public function addTuple($values) {
		$tuple = array();
		
		for ($i = 0; $i < count($this->heading->attributes); $i++)
			$tuple[$this->heading->attributes[$i]->id] = $values[$i];
			
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
	
	public function getTuple($tuple) {
		return $this->tuples[$tuple];
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

function parityClass($value) {
	if ($value % 2 == 0)
		return "even";
	else
		return "odd";
}

function convertValuesToHTML($attributes, $values) {
	$result = array();
	$i = 0;
	
	foreach ($values as $value) 
		$result[] = convertToHTML($value, $attributes[$i++]->type);		
	
	return $result;
}

function getTableCellsAsHTML($attributes, $values) {
	$result = '';
	$i = 0;
	
	foreach($values as $value) {
		$type = $attributes[$i]->type;			
		$result .= '<td class="'. $type .' column-'. parityClass($i) . '">'. $value . '</td>';
		$i++;
	}
	
	return $result;
}

function getRelationAsHTML($relationModel) {
	$result = '<table class="wiki-data-table"><tr>';	
	$attributes = $relationModel->getHeading()->attributes;
	
	foreach($attributes as $attribute)
		$result .= '<th class="'. $attribute->type .'">' . $attribute->name . '</th>';
		
	$result .= '</tr>';
	
	for($i = 0; $i < $relationModel->getTupleCount(); $i++) 
		$result .= '<tr>' . getTableCellsAsHTML($attributes, convertValuesToHTML($attributes, $relationModel->getTuple($i))) .'</tr>';
	
	$result .= '</table>';

	return $result;
}

function getInputRowAsHTML($rowId, $attributes, $values, $repeatInput, $allowRemove) {
	if ($repeatInput)
		$rowClass = 'repeat';
	else 
		$rowClass = '';
		
	$result = '<tr id="'. $rowId. '" class="' . $rowClass . '">';
	
	if ($allowRemove)
		$result .= '<td/>';
	
	$result .= getTableCellsAsHTML($attributes, $values);
				
	if ($repeatInput)
		$result .= '<td class="add"/>';
		
	return $result . '</tr>'; 
}

function removeCheckBoxName($tuple, $key, $removeId) {
	$ids = array();
	
	foreach($key->attributes as $attribute)
		$ids[] = $tuple[$attribute->id];
	
	return $removeId . implode("-", $ids);
}

function getRelationAsEditHTML($relationModel, $inputRowId, $removeId, $inputRowFields, $repeatInput, $allowRemove) {
	$result = '<table class="wiki-data-table"><tr>';	
	$attributes = $relationModel->getHeading()->attributes;
	$key = $relationModel->getKey();
	
	if ($allowRemove)
		$result .= '<th class="remove">Remove</th>';
	
	foreach($attributes as $attribute)
		$result .= '<th class="'. $attribute->type .'">' . $attribute->name . '</th>';

	if ($repeatInput)		
		$result .= '<th class="add">Input rows</th>';
		
	$result .= '</tr>';
	
	for ($i = 0; $i < $relationModel->getTupleCount(); $i++) {
		$result .= '<tr>';
		$tuple = $relationModel->getTuple($i);
		
		if ($allowRemove)
			$result .= '<td class="remove">' . getCheckBox(removeCheckBoxName($tuple, $key, $removeId), false) . '</td>';
		
		$result .= getTableCellsAsHTML($attributes, convertValuesToHTML($attributes, $tuple));
		
		if ($repeatInput)
			$result .= '<td/>';
		
		$result .= '</tr>';
	}
	
	$result .= getInputRowAsHTML($inputRowId, $attributes, $inputRowFields, $repeatInput, $allowRemove) . '</table>';

	return $result;
}

?>
