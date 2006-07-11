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

function getTupleAsTableCells($idPath, $editor, $tuple, &$startColumn = 0) {
	$result = '';
	
	foreach($editor->getEditors() as $childEditor) {
		$attribute = $childEditor->getAttribute();
		$type = $attribute->type;
		$value = $tuple->getAttributeValue($attribute);
		$idPath->pushAttribute($attribute);
		$attributeId = $idPath->getId();
		
		if (is_a($childEditor, TupleTableCellEditor)) 
			$result .= getTupleAsTableCells($idPath, $childEditor, $value, $startColumn);	
		else {
			if (is_a($type, RelationType)) 
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

function getTupleAsEditTableCells($tuple, $idPath, $editor, &$startColumn = 0) {
	$result = '';
	
	foreach($editor->getEditors() as $childEditor) {
		$attribute = $childEditor->getAttribute();
		$type = $attribute->type;
		$value = $tuple->getAttributeValue($attribute);
		$idPath->pushAttribute($attribute);
			
		if (is_a($childEditor, TupleTableCellEditor))			
			$result .= getTupleAsEditTableCells($value, $idPath, $childEditor, $startColumn); 
		else {	
			if (is_a($type, RelationType))  
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

function getRelationAsHTMLTable($editor, $idPath, $relation) {
	$result = '<table id="'. $idPath->getId() .'" class="wiki-data-table">';	
	$heading = $relation->getHeading();
	$key = $relation->getKey();
	
	foreach(getHeadingAsTableHeaderRows($editor->getTableHeading($editor)) as $headerRow)
		$result .= '<tr>' . $headerRow . '</tr>';
	
	$tupleCount = $relation->getTupleCount();
	
	for($i = 0; $i < $tupleCount; $i++) {
		$tuple = $relation->getTuple($i);
		$idPath->pushKey(project($tuple, $key));
		$result .= '<tr id="'. $idPath->getId() .'">' . getTupleAsTableCells($idPath, $editor, $tuple) .'</tr>';
		$idPath->popKey();
	}
	
	$result .= '</table>';

	return $result;
}

function getRelationAsSuggestionTable($editor, $idPath, $relation) {
	$result = '<table id="' . $idPath->getId() .'" class="wiki-data-table">';	
	$heading = $editor->getHeading();
	$key = $relation->getKey();
	
	foreach(getHeadingAsTableHeaderRows($heading) as $headerRow)
		$result .= '<tr>' . $headerRow . '</tr>';
	
	$tupleCount = $relation->getTupleCount();
	
	for($i = 0; $i < $tupleCount; $i++) {
		$tuple = $relation->getTuple($i);
		$idPath->pushKey(project($tuple, $key));
		$id = getTupleKeyName($relation->getTuple($i), $key);
		$result .= '<tr id="'. $id .'" class="suggestion-row inactive" onclick="suggestRowClicked(this)" onmouseover="mouseOverRow(this)" onmouseout="mouseOutRow(this)">' . getTupleAsTableCells($idPath, $editor, $tuple) .'</tr>';
		$idPath->popKey();
	}
	
	$result .= '</table>';

	return $result;
}

function getAddRowAsHTML($idPath, $editor, $repeatInput, $allowRemove) {
	if ($repeatInput)
		$rowClass = 'repeat';
	else 
		$rowClass = '';
		
	$result = '<tr id="'. $idPath->getId() . '" class="' . $rowClass . '">';
	
	if ($allowRemove)
		$result .= '<td/>';
	
	$result .= getHeadingAsAddCells($idPath, $editor);
				
	if ($repeatInput)
		$result .= '<td class="add"/>';
		
	return $result . '</tr>'; 
}

function getHeadingAsAddCells($idPath, $editor, &$startColumn = 0) {
	$result = '';
	
	foreach($editor->getEditors() as $childEditor) {
		$attribute = $childEditor->getAttribute();
		$type = $attribute->type;
		$idPath->pushAttribute($attribute);
		
		if (is_a($childEditor, TupleTableCellEditor))
			$result .= getHeadingAsAddCells($idPath, $childEditor, $startColumn);
		else {
			$result .= '<td class="'. $type .' column-'. parityClass($startColumn) . '">' . $childEditor->add($idPath) . '</td>';
			$startColumn++;
		}
		
		$idPath->popAttribute();
	}
	
	return $result;
}

?>
