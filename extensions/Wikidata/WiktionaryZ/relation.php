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

interface Converter {
	public function convert($value);
	public function getAttributes();
}

class DefaultConverter implements Converter {
	protected $attribute;
	
	public function __construct($attribute) {
		$this->attribute = $attribute;
	}
	
	public function convert($tuple) {
		return array(convertToHTML($tuple[$this->attribute->id], $this->attribute->type));
	}
	
	public function getAttributes() {
		return array($this->attribute);
	}
}

class DefiningExpressionConverter extends DefaultConverter {
	public function convert($tuple) {
		return array(definingExpressionAsLink($tuple[$this->attribute->id]));
	}
}

class ExpressionConverter extends DefaultConverter {
	protected $attributes = array();
	
	public function __construct($attribute) {
		parent::__construct($attribute);
		$this->attributes[] = new Attribute("language", "Language", "language"); 
		$this->attributes[] = new Attribute("spelling", "Spelling", "spelling");
	}
	
	public function getAttributes() {
		return $this->attributes;
	}
	
	public function convert($tuple) {
		$dbr =& wfGetDB(DB_SLAVE);
		$expressionId = $tuple[$this->attribute->id];
		$queryResult = $dbr->query("SELECT language_id, spelling from uw_expression_ns WHERE expression_id=$expressionId");
		$expression = $dbr->fetchObject($queryResult); 
	
		return array(languageIdAsText($expression->language_id), spellingAsLink($expression->spelling));
	}
}

class HTMLRelation implements RelationModel {
	protected $relationModel;
	protected $converters = array();
	protected $heading;	
	
	public function __construct($relationModel) {
		$this->relationModel = $relationModel;
		$this->configureConverters();
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
	
	protected function configureConverters() {
		$attributes = array();
		
		foreach($this->relationModel->getHeading()->attributes as $attribute) {
			switch($attribute->type) {
				case "expression": $converter = new ExpressionConverter($attribute); break;				
				case "defining-expression": $converter = new DefiningExpressionConverter($attribute); break;
				default: $converter = new DefaultConverter($attribute); break;
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

function getRelationAsHTML($relationModel) {
	$relationModel = new HTMLRelation($relationModel);
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
	$htmlRelation = new HTMLRelation($relationModel);
	
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
		
		if ($allowRemove)
			$result .= '<td class="remove">' . getCheckBox(removeCheckBoxName($relationModel->getTuple($i), $key, $removeId), false) . '</td>';
		
		$result .= getTableCellsAsHTML($attributes, $htmlRelation->getTuple($i));
		
		if ($repeatInput)
			$result .= '<td/>';
		
		$result .= '</tr>';
	}
	
	$result .= getInputRowAsHTML($inputRowId, $attributes, $inputRowFields, $repeatInput, $allowRemove) . '</table>';

	return $result;
}

?>
