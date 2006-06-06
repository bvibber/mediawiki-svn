<?php

class Attribute {
	public $name = "";
	public $type = "";
	
	public function __construct($name, $type) {
		$this->name = $name;
		$this->type = $type;
	}
}

interface RelationModel {
	public function getAttributes();
	public function getTupleCount();
	public function getTuple($tuple);
}

class ArrayRelation implements RelationModel {
	protected $attributes = array();
	protected $cells = array();
	
	public function __construct($attributes) {
		$this->attributes = $attributes;
	}
	
	public function addTuple($tuple) {
		$this->cells[] = $tuple;
	}
	
	public function getAttributes() {
		return $this->attributes;
	}
	
	public function getTupleCount() {
		return count($this->cells);
	}
	
	public function getTuple($tuple) {
		return $this->cells[$tuple];
	}
}

function getQueryAsRelation($sql) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query($sql);

	$attributes = array();
	$fieldCount = $dbr->numFields($queryResult);
	
	for ($i = 0; $i < $fieldCount; $i++)
		$attributes[] = new Attribute($dbr->fieldName($queryResult, $i), "Text");
		
	$result = new ArrayRelation($attributes);
	
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
	$attributes = $relationModel->getAttributes();
	
	foreach($attributes as $attribute)
		$result .= '<th class="'. $attribute->type .'">' . $attribute->name . '</th>';
		
	$result .= '</tr>';
	
	for($i = 0; $i < $relationModel->getTupleCount(); $i++) 
		$result .= '<tr>' . getTableCellsAsHTML($attributes, convertValuesToHTML($attributes, $relationModel->getTuple($i))) .'</tr>';
	
	$result .= '</table>';

	return $result;
}

function getInputRowAsHTML($rowId, $attributes, $values, $repeatInput) {
	if ($repeatInput)
		$rowClass = 'repeat';
	else 
		$rowClass = '';
		
	$result = '<tr id="'. $rowId. '" class="' . $rowClass . '">' . 
				getTableCellsAsHTML($attributes, $values);
				
	if ($repeatInput)
		$result .= '<td/>';
		
	return $result . '</tr>'; 
}

function getRelationAsEditHTML($relationModel, $inputRowId, $inputRowFields, $repeatInput) {
	$result = '<table class="wiki-data-table"><tr>';	
	$attributes = $relationModel->getAttributes();
	
	foreach($attributes as $attribute)
		$result .= '<th class="'. $attribute->type .'">' . $attribute->name . '</th>';

	if ($repeatInput)		
		$result .= '<th>Input rows</th>';
		
	$result .= '</tr>';
	
	for($i = 0; $i < $relationModel->getTupleCount(); $i++) {
		$result .= '<tr>' . getTableCellsAsHTML($attributes, convertValuesToHTML($attributes, $relationModel->getTuple($i)));
		
		if ($repeatInput)
			$result .= '<td/>';
		
		$result .= '</tr>';
	}
	
	$result .= getInputRowAsHTML($inputRowId, $attributes, $inputRowFields, $repeatInput) . '</table>';

	return $result;
}

?>
