<?php

/* Copyright (C) 2006 by Charta Software
 *   http://www.charta.org/
 */ 

require_once("Attribute.php");
require_once("Record.php");
require_once("RecordSet.php");

function parityClass($value) {
	if ($value % 2 == 0)
		return "even";
	else
		return "odd";
}

/* Functions to create a hierarchical table header
 * using rowspan and colspan for <th> elements
 */

class TableHeaderNode {
	public $attribute = null;
	public $width = 0;
	public $height = 0;
	public $column = 0;
	public $childNodes = array();
}

function getTableHeaderNode($structure, &$currentColumn=0) {
	$tableHeaderNode = new TableHeaderNode();
	
	foreach($structure->attributes as $attribute) {
		$type = $attribute->type;
		
		if (is_a($type, RecordType)) 
			$childNode = getTableHeaderNode($type->getStructure(), $currentColumn);
		else { 
			$childNode = new TableHeaderNode();
			$childNode->width = 1;
			$childNode->height = 1;
			$childNode->column = $currentColumn++;
		}

		$tableHeaderNode->height = max($tableHeaderNode->height, $childNode->height);
		$tableHeaderNode->width += $childNode->width;
		$tableHeaderNode->childNodes[] = $childNode;
		$childNode->attribute = $attribute;
	}
	
	$tableHeaderNode->height++;
	
	return $tableHeaderNode;
}

function addChildNodesToRows($headerNode, &$rows, $currentDepth, $columnOffset) {
	$height = $headerNode->height;
	
	foreach($headerNode->childNodes as $childNode) {
		$attribute = $childNode->attribute;
		$type = $attribute->type;
		
		if (!is_a($type, RecordType) && !is_a($type, RecordSetType))
			$class = ' class="'. $type .' sortable" onclick="sortTable(this, '. count($rows) .', '. ($childNode->column + $columnOffset) .')"';	
		else		
			$class = '';

		$rowSpan = $height - $childNode->height;
		$rows[$currentDepth] .= '<th' . $class .' colspan="'. $childNode->width . 
									'" rowspan="'. $rowSpan . '">'. $attribute->name . '</th>';
									
		addChildNodesToRows($childNode, $rows, $currentDepth + $rowSpan, $columnOffset);
	} 
}

function getStructureAsTableHeaderRows($structure, $columnOffset) {
	$rootNode = getTableHeaderNode($structure);
	$result = array();
	
	for ($i = 0; $i < $rootNode->height - 1; $i++)
		$result[$i] = "";
		
	addChildNodesToRows($rootNode, $result, 0, $columnOffset);

	return $result;
}

function getHTMLClassForType($type) {
	if (is_a($type, RecordSetType))  
		return "relation";
	else if (is_a($type, RecordType)) 
		return $attribute->id;
	else 
		return $type;
}

function getRecordAsTableCells($idPath, $editor, $record, &$startColumn = 0) {
	$result = '';
	
	foreach($editor->getEditors() as $childEditor) {
		$attribute = $childEditor->getAttribute();
		$type = $attribute->type;
		$value = $record->getAttributeValue($attribute);
		$idPath->pushAttribute($attribute);
		$attributeId = $idPath->getId();
		
		if (is_a($childEditor, RecordTableCellEditor)) 
			$result .= getRecordAsTableCells($idPath, $childEditor, $value, $startColumn);	
		else {
			$displayValue = $childEditor->view($idPath, $value);
			$result .= '<td class="'. getHTMLClassForType($type) .' column-'. parityClass($startColumn) . '">'. $displayValue . '</td>';
			$startColumn++;
		}
		
		$idPath->popAttribute();
	}
	
	return $result;
}

function getRecordAsEditTableCells($record, $idPath, $editor, &$startColumn = 0) {
	$result = '';
	
	foreach($editor->getEditors() as $childEditor) {
		$attribute = $childEditor->getAttribute();
		$type = $attribute->type;
		$value = $record->getAttributeValue($attribute);
		$idPath->pushAttribute($attribute);
			
		if (is_a($childEditor, RecordTableCellEditor))			
			$result .= getRecordAsEditTableCells($value, $idPath, $childEditor, $startColumn); 
		else {	
			$displayValue = $childEditor->edit($idPath, $value);
			$result .= '<td class="'. getHTMLClassForType($type) .' column-'. parityClass($startColumn) . '">'. $displayValue . '</td>';
				
			$startColumn++;
		}
		
		$idPath->popAttribute();
	}
	
	return $result;
}

function getRelationAsSuggestionTable($editor, $idPath, $relation) {
	$result = '<table id="' . $idPath->getId() .'" class="wiki-data-table">';	
	$structure = $editor->getStructure();
	$key = $relation->getKey();
	
	foreach(getStructureAsTableHeaderRows($structure, 0) as $headerRow)
		$result .= '<tr>' . $headerRow . '</tr>';
	
	$recordCount = $relation->getRecordCount();
	
	for($i = 0; $i < $recordCount; $i++) {
		$record = $relation->getRecord($i);
		$idPath->pushKey(project($record, $key));
		$id = getRecordKeyName($relation->getRecord($i), $key);
		$result .= '<tr id="'. $id .'" class="suggestion-row inactive" onclick="suggestRowClicked(event, this)" onmouseover="mouseOverRow(this)" onmouseout="mouseOutRow(this)">' . getRecordAsTableCells($idPath, $editor, $record) .'</tr>';
		$idPath->popKey();
	}
	
	$result .= '</table>';

	return $result;
}

function getStructureAsAddCells($idPath, $editor, &$startColumn = 0) {
	$result = '';
	
	foreach($editor->getEditors() as $childEditor) {
		$attribute = $childEditor->getAttribute();
		$type = $attribute->type;
		$idPath->pushAttribute($attribute);
		
		if (is_a($childEditor, RecordTableCellEditor))
			$result .= getStructureAsAddCells($idPath, $childEditor, $startColumn);
		else {
			$result .= '<td class="'. getHTMLClassForType($type) .' column-'. parityClass($startColumn) . '">' . $childEditor->add($idPath) . '</td>';
			$startColumn++;
		}
		
		$idPath->popAttribute();
	}
	
	return $result;
}

?>
