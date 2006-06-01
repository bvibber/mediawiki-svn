<?php

class Attribute {
	public $name = "";
	public $type = "";
	
	public function __construct($name, $type) {
		$this->name = $name;
		$this->type = $type;
	}
}

interface TableModel {
	public function getAttributes();
	public function getRowCount();
	public function getRow($row);
}

class ArrayTable implements TableModel {
	protected $attributes = array();
	protected $cells = array();
	
	public function __construct($attributes) {
		$this->attributes = $attributes;
	}
	
	public function addRow($row) {
		$this->cells[] = $row;
	}
	
	public function getAttributes() {
		return $this->attributes;
	}
	
	public function getRowCount() {
		return count($this->cells);
	}
	
	public function getRow($row) {
		return $this->cells[$row];
	}
}

function getQueryAsTable($sql) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query($sql);

	$attributes = array();
	$fieldCount = $dbr->numFields($queryResult);
	
	for ($i = 0; $i < $fieldCount; $i++)
		$attributes[] = new Attribute($dbr->fieldName($queryResult, $i), "Text");
		
	$result = new ArrayTable($attributes);
	
	while ($row = $dbr->fetchRow($queryResult)) {
		$tableRow = array();
		
		for ($i = 0; $i < $fieldCount; $i++)
			$tableRow[] = $row[$i];
			
		$result->addRow($tableRow);
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

function getTableAsHTML($tableModel) {
	$result = '<table class="wiki-data-table"><tr>';	
	$attributes = $tableModel->getAttributes();
	
	foreach($attributes as $attribute)
		$result .= '<th class="'. $attribute->type .'">' . $attribute->name . '</th>';
		
	$result .= '</tr>';
	
	for($i = 0; $i < $tableModel->getRowCount(); $i++) {
//		$result .= '<tr>';
//		$j = 0;
//		
//		foreach($tableModel->getRow($i) as $cell) {
//			$type = $attributes[$j]->type;			
//			$result .= '<td class="'. $type .' column-'. parityClass($j) . '">'. convertToHTML($cell, $type) . '</td>';
//			$j++;
//		}
//		
//		$result .= '</tr>';
		$result .= '<tr>' . getTableCellsAsHTML($attributes, convertValuesToHTML($attributes, $tableModel->getRow($i))) .'</tr>';
	}
	
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

function getTableAsEditHTML($tableModel, $inputRowId, $inputRowFields, $repeatInput) {
	$result = '<table class="wiki-data-table"><tr>';	
	$attributes = $tableModel->getAttributes();
	
	foreach($attributes as $attribute)
		$result .= '<th class="'. $attribute->type .'">' . $attribute->name . '</th>';

	if ($repeatInput)		
		$result .= '<th>Input rows</th>';
		
	$result .= '</tr>';
	
	for($i = 0; $i < $tableModel->getRowCount(); $i++) {
		$result .= '<tr>' . getTableCellsAsHTML($attributes, convertValuesToHTML($attributes, $tableModel->getRow($i)));
		
		if ($repeatInput)
			$result .= '<td/>';
		
		$result .= '</tr>';
	}
	
	$result .= getInputRowAsHTML($inputRowId, $attributes, $inputRowFields, $repeatInput) . '</table>';

	return $result;
}

?>
