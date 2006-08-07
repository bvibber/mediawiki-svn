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

function mergeStructureBlocks($lhs, $rhs) {
	$result = $lhs;
	
	for ($i = 0; $i < count($rhs); $i++) {
		if ($i < count($result)) 
			$result[$i] = array_merge($result[$i], $rhs[$i]);
		else
			$result[$i] = $rhs[$i]; 
	}

	return $result;
}

function getStructureBlock($structure) {
	$block = array();
	$width = 0;
	$height = 0;
	
	foreach($structure->attributes as $attribute) {
		$type = $attribute->type;
		
		if (is_a($type, RecordType)) {
			list($childBlock, $childWidth, $childHeight) = getStructureBlock($type->getStructure());
			array_unshift($childBlock, array(array($attribute, $childWidth, $childHeight + 1)));
			$width += $childWidth;
			$height = max($height, $childHeight + 1);
			$block = mergeStructureBlocks($block, $childBlock);
		}
		else { 
			$block = mergeStructureBlocks($block, array(array(array($attribute, 1, 1))));
			$height = max($height, 1);
			$width++;
		}
	}
	
	return array($block, $width, $height);
}

function getStructureAsTableHeaderRows($structure) {
	list($structureBlock, $width, $height) = getStructureBlock($structure);
	
	$result = array();
	
	for ($i = 0; $i < $height; $i++) {
		$row = '';
		
		foreach($structureBlock[$i] as $block) {
			list($attribute, $blockWidth, $blockHeight) = $block;
			$type = $attribute->type;
			
			if (!is_a($type, RecordType) && !is_a($type, RecordSetType))
				$class = ' class="'. $type .'"';	
			else		
				$class = '';
				
			$row .= '<th' . $class .' colspan="'. $blockWidth . 
							'" rowspan="'. ($height - $blockHeight - $i + 1) . '">'. $attribute->name . '</th>';
		}
		
		$result[] = $row;	
	}
	
	return $result;
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
			if (is_a($type, RecordSetType)) 
				$class = "relation";
			else 
				$class = $type;
			
			$displayValue = $childEditor->view($idPath, $value);
			$result .= '<td class="'. $class .' column-'. parityClass($startColumn) . '">'. $displayValue . '</td>';
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
			if (is_a($type, RecordSetType))  
				$class = "relation";
			else 
				$class = $type;
			
			$displayValue = $childEditor->edit($idPath, $value);
			$result .= '<td class="'. $class .' column-'. parityClass($startColumn) . '">'. $displayValue . '</td>';
				
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
	
	foreach(getStructureAsTableHeaderRows($structure) as $headerRow)
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
			$result .= '<td class="'. $type .' column-'. parityClass($startColumn) . '">' . $childEditor->add($idPath) . '</td>';
			$startColumn++;
		}
		
		$idPath->popAttribute();
	}
	
	return $result;
}

?>
