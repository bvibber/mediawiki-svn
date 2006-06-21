<?php

require_once('forms.php');
require_once('converter.php');
require_once('attribute.php');

interface RelationModel {
	public function getHeading();
	public function getKey();
	public function getTupleCount();
	public function getTuple($index);
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
	
	public function getTuple($index) {
		return $this->tuples[$index];
	}
}

class HTMLRelation implements RelationModel {
	protected $relationModel;
	protected $converters = array();
	
	public function __construct($relationModel, $updatableHeading) {
		$this->relationModel = $relationModel;
		$this->configureConverters($updatableHeading);
	}

	public function getHeading() {
		return $this->heading;
	}
	
	public function getKey() {
		return $this->relationModel->getKey();	
	}
	
	public function getTupleCount() {
		return $this->relationModel->getTupleCount();	
	}
	
	public function getTuple($index) {
		$tuple = $this->relationModel->getTuple($index);
		$result = array();
		
		foreach ($this->converters as $converter) 
			$result = array_merge($result, $converter->convert($tuple));
			
		return $result;
	}
	
	protected function configureConverters($updatableHeading) {
		$attributes = array();
		$updatableAttributes = $updatableHeading->attributes;
		
		foreach($this->relationModel->getHeading()->attributes as $attribute) {
			if (in_array($attribute, $updatableAttributes))
				$converter = new IdentityConverter($attribute);
			else {
				switch($attribute->type) {
					case "expression": $converter = new ExpressionConverter($attribute); break;				
					case "defining-expression": $converter = new DefiningExpressionConverter($attribute); break;
					default: $converter = new DefaultConverter($attribute); break;
				}
			}
			
			$this->converters[] = $converter;
			$attributes = array_merge($attributes, $converter->getAttributes());
		}
		
		$this->heading = new Heading($attributes);
	}
	
	protected function convertTupleToHTML($tuple) {
		$result = array();
		$attributes = $this->relationModel->getHeading()->attributes;
		
		foreach ($attributes as $attribute)  
			$result[$attribute->id] = convertToHTML($tuple[$attribute->id], $attribute->type);
				
		return $result;
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

function getTableCellsAsEditHTML($attributes, $values, $updateId, $updatableHeading) {
	$result = '';
	$i = 0;
	
	foreach($values as $value) {
		$type = $attributes[$i]->type;
				
		if (in_array($attributes[$i], $updatableHeading->attributes)) {
			$inputFields = getInputFieldsForAttribute($updateId, $attributes[$i], $value);
			$result .= '<td class="'. $type .' column-'. parityClass($i) . '">'. $inputFields[0] . '</td>';
		}
		else			
			$result .= '<td class="'. $type .' column-'. parityClass($i) . '">'. $value . '</td>';
			
		$i++;
	}
	
	return $result;
}

function getRelationAsHTMLTable($relationModel) {
	$relationModel = new HTMLRelation($relationModel, new Heading(array()));
	$result = '<table class="wiki-data-table"><tr>';	
	$attributes = $relationModel->getHeading()->attributes;
	
	foreach($attributes as $attribute)
		$result .= '<th class="'. $attribute->type .'">' . $attribute->name . '</th>';
		
	$result .= '</tr>';
	
	for($i = 0; $i < $relationModel->getTupleCount(); $i++) 
		$result .= '<tr>' . getTableCellsAsHTML($attributes, $relationModel->getTuple($i)) .'</tr>';
	
	$result .= '</table>';

	return $result;
}

function getAddRowAsHTML($rowId, $attributes, $values, $repeatInput, $allowRemove) {
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

function getTupleKeyName($tuple, $key) {
	$ids = array();
	
	foreach($key->attributes as $attribute)
		$ids[] = $tuple[$attribute->id];
	
	return implode("-", $ids);
}

function getRelationAsEditHTML($relationModel, $addRowId, $removeId, $updateId, $addRowFields, $repeatInput, $allowAdd, $allowRemove, $updatableHeading) {
	$htmlRelation = new HTMLRelation($relationModel, $updatableHeading);
	
	$result = '<table class="wiki-data-table"><tr>';	
	$attributes = $htmlRelation->getHeading()->attributes;
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
		$tupleKeyName = getTupleKeyName($relationModel->getTuple($i), $key);
		
		if ($allowRemove)
			$result .= '<td class="remove">' . getRemoveCheckBox($removeId . $tupleKeyName) . '</td>';
		
		$result .= getTableCellsAsEditHTML($attributes, $htmlRelation->getTuple($i), $updateId . $tupleKeyName . '-', $updatableHeading);
		
		if ($repeatInput)
			$result .= '<td/>';
		
		$result .= '</tr>';
	}
	
	if ($allowAdd)
		$result .= getAddRowAsHTML($addRowId, $attributes, $addRowFields, $repeatInput, $allowRemove);
	
	$result .= '</table>';

	return $result;
}

?>
