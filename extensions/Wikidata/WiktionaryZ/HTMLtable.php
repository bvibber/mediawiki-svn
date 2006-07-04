<?php

require_once("attribute.php");
require_once("tuple.php");
require_once("relation.php");

function parityClass($value) {
	if ($value % 2 == 0)
		return "even";
	else
		return "odd";
}

/* Functions to create a hierarchical table header
 * using rowspan and colspan for <th> elements
 */

function mergeHeadingBlocks($lhs, $rhs) {
	$result = $lhs;
	
	for ($i = 0; $i < count($rhs); $i++) {
		if ($i < count($result)) 
			$result[$i] = array_merge($result[$i], $rhs[$i]);
		else
			$result[$i] = $rhs[$i]; 
	}

	return $result;
}

function getHeadingBlock($heading) {
	$block = array();
	$width = 0;
	$height = 0;
	
	foreach($heading->attributes as $attribute) {
		$type = $attribute->type;
		
		if (is_a($type, TupleType)) {
			list($childBlock, $childWidth, $childHeight) = getHeadingBlock($type->getHeading());
			array_unshift($childBlock, array(array($attribute, $childWidth, $childHeight + 1)));
			$width += $childWidth;
			$height = max($height, $childHeight + 1);
			$block = mergeHeadingBlocks($block, $childBlock);
		}
		else { 
			$block = mergeHeadingBlocks($block, array(array(array($attribute, 1, 1))));
			$height = max($height, 1);
			$width++;
		}
	}
	
	return array($block, $width, $height);
}

function getHeadingAsTableHeaderRows($heading) {
	list($headingBlock, $width, $height) = getHeadingBlock($heading);
	
	$result = array();
	
	for ($i = 0; $i < $height; $i++) {
		$row = '';
		
		foreach($headingBlock[$i] as $block) {
			list($attribute, $blockWidth, $blockHeight) = $block;
			$type = $attribute->type;
			
			if (!is_a($type, TupleType) && !is_a($type, RelationType))
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

function getTupleAsTableCells($id, $keyPath, $editor, $tuple, &$startColumn = 0) {
	$result = '';
	
	foreach($editor->getEditors() as $childEditor) {
		$attribute = $childEditor->getAttribute();
		$type = $attribute->type;
		$value = $tuple->getAttributeValue($attribute);
		
		if (is_a($childEditor, TupleTableCellEditor)) 
			$result .= getTupleAsTableCells($id . '-' . $attribute->id, $keyPath, $childEditor, $value, $startColumn);	
		else {
			if (is_a($type, RelationType)) 
				$class = "relation";
			else 
				$class = $type;
			
			$displayValue = $childEditor->view($id, $keyPath, $value);
			$result .= '<td class="'. $class .' column-'. parityClass($startColumn) . '">'. $displayValue . '</td>';
			$startColumn++;
		}
	}
	
	return $result;
}

function getTupleAsEditTableCells($tuple, $id, $keyPath, $editor, &$startColumn = 0) {
	$result = '';
	
	foreach($editor->getEditors() as $childEditor) {
		$attribute = $childEditor->getAttribute();
		$type = $attribute->type;
		$value = $tuple->getAttributeValue($attribute);
			
		if (is_a($childEditor, TupleTableCellEditor))			
			$result .= getTupleAsEditTableCells($value, $id . '-' . $attribute->id, $keyPath, $childEditor, $startColumn); 
		else {	
			if (is_a($type, RelationType))  
				$class = "relation";
			else 
				$class = $type;
			
			$displayValue = $childEditor->edit($id, $keyPath, $value);
			$result .= '<td class="'. $class .' column-'. parityClass($startColumn) . '">'. $displayValue . '</td>';
				
			$startColumn++;
		}
	}
	
	return $result;
}

function getRelationAsHTMLTable($editor, $id, $keyPath, $relation) {
	$result = '<table id="'. $id .'" class="wiki-data-table">';	
	$heading = $relation->getHeading();
	
	foreach(getHeadingAsTableHeaderRows($editor->getHeading()) as $headerRow)
		$result .= '<tr>' . $headerRow . '</tr>';
	
	$tupleCount = $relation->getTupleCount();
	
	for($i = 0; $i < $tupleCount; $i++) {
		$tuple = $relation->getTuple($i);
		$result .= '<tr>' . getTupleAsTableCells($id, $keyPath, $editor, $tuple) .'</tr>';
	}
	
	$result .= '</table>';

	return $result;
}

function getRelationAsSuggestionTable($editor, $id, $keyPath, $relation) {
	$result = '<table id="' . $id .'" class="wiki-data-table">';	
	$heading = $editor->getHeading();
	$key = $relation->getKey();
	
	foreach(getHeadingAsTableHeaderRows($heading) as $headerRow)
		$result .= '<tr>' . $headerRow . '</tr>';
	
	$tupleCount = $relation->getTupleCount();
	
	for($i = 0; $i < $tupleCount; $i++) {
		$tuple = $relation->getTuple($i);
		$keyPath->push(project($tuple, $key));
		$id = getTupleKeyName($relation->getTuple($i), $key);
		$result .= '<tr id="'. $id .'" class="suggestion-row inactive" onclick="suggestRowClicked(this)" onmouseover="mouseOverRow(this)" onmouseout="mouseOutRow(this)">' . getTupleAsTableCells($id, $keyPath, $editor, $tuple) .'</tr>';
		$keyPath->pop();
	}
	
	$result .= '</table>';

	return $result;
}

function getAddRowAsHTML($id, $keyPath, $editor, $repeatInput, $allowRemove) {
	if ($repeatInput)
		$rowClass = 'repeat';
	else 
		$rowClass = '';
		
	$result = '<tr id="'. $id. '" class="' . $rowClass . '">';
	
	if ($allowRemove)
		$result .= '<td/>';
	
	$result .= getHeadingAsAddCells($id, $keyPath, $editor);
				
	if ($repeatInput)
		$result .= '<td class="add"/>';
		
	return $result . '</tr>'; 
}

function getHeadingAsAddCells($id, $keyPath, $editor, &$startColumn = 0) {
	$result = '';
	
	foreach($editor->getEditors() as $childEditor) {
		$attribute = $childEditor->getAttribute();
		$type = $attribute->type;
		
		if (is_a($childEditor, TupleTableCellEditor))
			$result .= getHeadingAsAddCells($id . '-' . $attribute->id, $keyPath, $childEditor, $startColumn);
		else {
			$result .= '<td class="'. $type .' column-'. parityClass($startColumn) . '">' . $childEditor->add($id, $keyPath) . '</td>';
			$startColumn++;
		}
	}
	
	return $result;
}

function getRelationAsEditHTML($editor, $id, $keyPath, $relation, $allowAdd, $allowRemove, $repeatInput) {
	$result = '<table id="'. $id .'" class="wiki-data-table">';	
	$key = $relation->getKey();
	
	$headerRows = getHeadingAsTableHeaderRows($editor->getHeading());

	if ($allowRemove)
		$headerRows[0] = '<th class="remove" rowspan="' . count($headerRows) . '"><img src="skins/amethyst/delete.png" title="Mark rows to remove" alt="Remove"/></th>' . $headerRows[0];
		
	if ($repeatInput)		
		$headerRows[0] .= '<th class="add" rowspan="' . count($headerRows) . '">Input rows</th>';
		
	foreach ($headerRows as $headerRow)
		$result .= '<tr>' . $headerRow . '</tr>';
	
	$tupleCount = $relation->getTupleCount();
	
	for ($i = 0; $i < $tupleCount; $i++) {
		$result .= '<tr>';
		$tuple = $relation->getTuple($i);
		$keyPath->push(project($tuple, $key));
		$tupleKeyName = getTupleKeyName($tuple, $key);
		
		if ($allowRemove)
			$result .= '<td class="remove">' . getRemoveCheckBox('remove-'. $id . '-' . $tupleKeyName) . '</td>';
		
		$result .= getTupleAsEditTableCells($tuple,  $id . '-' . $tupleKeyName, $keyPath, $editor);
		$keyPath->pop();		
		
		if ($repeatInput)
			$result .= '<td/>';
		
		$result .= '</tr>';
	}
	
	if ($allowAdd) 
		$result .= getAddRowAsHTML($id, $keyPath, $editor, $repeatInput, $allowRemove);
	
	$result .= '</table>';

	return $result;
}


?>
