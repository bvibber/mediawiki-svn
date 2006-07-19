<?php

/* Copyright (C) 2006 by Charta Software
 *   http://www.charta.org/
 */ 

require_once("HTMLtable.php");

class IdStack {
	protected $keyStack;
	protected $idStack = array();
	protected $currentId;
	
	public function __construct($prefix) {
	 	$this->keyStack = new TupleStack();
	 	$this->currentId = $prefix;
	}
	
	protected function getKeyIds($tuple) {
		$ids = array();
	
		foreach($tuple->getHeading()->attributes as $attribute)
			$ids[] = $tuple->getAttributeValue($attribute);
		
		return $ids;
	}
	
	protected function pushId($id) {
		$this->idStack[] = $this->currentId;
		$this->currentId .= '-' . $id;
	}
	
	protected function popId() {
		$this->currentId = array_pop($this->idStack);
	}
	
	public function pushKey($tuple) {
		$this->keyStack->push($tuple);
		$this->pushId(implode("-", $this->getKeyIds($tuple)));
	}
	
	public function pushAttribute($attribute) {
		$this->pushId($attribute->id);
	}
	
	public function popKey() {
		$this->popId();
		return $this->keyStack->pop();
	}
	
	public function popAttribute() {
		$this->popId();
	}
	
	public function getId() {
		return $this->currentId;
	}
	
	public function getKeyStack() {
		return $this->keyStack;
	}
}

interface Editor {
	public function getAttribute();
	public function getUpdateAttribute();
	public function getAddAttribute();
	
	public function view($idPath, $value);
	public function edit($idPath, $value);
	public function add($idPath);
	public function save($idPath, $value);

	public function getUpdateValue($idPath);
	public function getAddValue($idPath);
	
	public function getEditors();
}

interface Controller {
	public function add($keyPath, $tuple);
	public function remove($keyPath);
	public function update($keyPath, $tuple);
}

abstract class DefaultEditor implements Editor {
	protected $editors = array();
	protected $attribute;

	public function __construct($attribute) {
		$this->attribute = $attribute;
	}

	public function addEditor($editor) {
		$this->editors[] = $editor;
	}
	
	public function getAttribute() {
		return $this->attribute;
	}
	
	public function getEditors() {
		return $this->editors;
	}
}

abstract class Viewer extends DefaultEditor {
	public function getUpdateAttribute() {
		return null;
	}

	public function getAddAttribute() {
		return null;
	}
	
	public function edit($idPath, $value) {
		return $this->view($idPath, $value);	
	}
	
	public function add($idPath) {
	}
	
	public function save($idPath, $value) {
	}

	public function getUpdateValue($idPath) {
		return null;
	}
	
	public function getAddValue($idPath) {
		return null;
	}
}

abstract class RelationEditor extends DefaultEditor {
	protected $allowAdd;
	protected $allowRemove;
	protected $isAddField;
	protected $controller;	
	
	public function __construct($attribute, $allowAdd, $allowRemove, $isAddField, $controller) {
		parent::__construct($attribute);
		
		$this->allowAdd = $allowAdd;
		$this->allowRemove = $allowRemove;
		$this->isAddField = $isAddField;
		$this->controller = $controller;
	}
	
	public function getAddValue($idPath) {
		$addHeading = $this->getAddHeading();
		
		if (count($addHeading->attributes) > 0) {
			$relation = new ArrayRelation($addHeading, $addHeading);  // TODO Determine real key
			$values = array();
			
			foreach($this->editors as $editor) 
				if ($attribute = $editor->getAddAttribute()) { 
					$idPath->pushAttribute($attribute);
					$values[] = $editor->getAddValue($idPath);
					$idPath->popAttribute();
				}
				
			$relation->addTuple($values);	
						 
			return $relation;
		}
		else
			return null;	
	}

	protected function saveTuple($idPath, $tuple) {
		foreach($this->editors as $editor) {
			$attribute = $editor->getAttribute();
			$value = $tuple->getAttributeValue($attribute);
			$idPath->pushAttribute($attribute);
			$editor->save($idPath, $value);
			$idPath->popAttribute();
		}
	}
	
	protected function updateTuple($idPath, $tuple, $heading, $editors) {
		if (count($editors) > 0) {
			$updateTuple = $this->getUpdateTuple($idPath, $heading, $editors);
			
			if (!equalTuples($heading, $tuple, $updateTuple))		
				$this->controller->update($idPath->getKeyStack(), $updateTuple);
		}
	}
	
	protected function removeTuple($idPath) {
		global
			$wgRequest;
		
		if ($wgRequest->getCheck('remove-'. $idPath->getId())) {	
			$this->controller->remove($idPath->getKeyStack());
			return true;
		}
		else 
			return false;
	}

	public function getHeading() {
		$attributes = array();
		
		foreach($this->editors as $editor) 
			$attributes[] = $editor->getAttribute();
			
		return new Heading($attributes);
	}
	
	public function getUpdateValue($idPath) {
		return null;
	}

	protected function getUpdateHeading() {
		$attributes = array();
		
		foreach($this->editors as $editor) 
			if ($updateAttribute = $editor->getUpdateAttribute())
				$attributes[] = $updateAttribute;
			
		return new Heading($attributes);
	}
	
	protected function getAddHeading() {
		$attributes = array();

		foreach($this->editors as $editor)
			if ($addAttribute = $editor->getAddAttribute())
				$attributes[] = $addAttribute;
			
		return new Heading($attributes);
	}
	
	protected function getUpdateEditors() {
		$updateEditors = array();
		
		foreach($this->editors as $editor)
			if ($editor->getUpdateAttribute())
				$updateEditors[] = $editor;
			
		return $updateEditors;
	}
	
	protected function getAddEditors() {
		$addEditors = array();
		
		foreach($this->editors as $editor)
			if ($editor->getAddAttribute())
				$addEditors[] = $editor;
			
		return $addEditors;
	}
	
	public function getAddTuple($idPath, $heading, $editors) {
		$result = new ArrayTuple($heading);
		
		foreach($editors as $editor) 
			if ($attribute = $editor->getAddAttribute()) {
				$idPath->pushAttribute($attribute);
				$result->setAttributeValue($attribute, $editor->getAddValue($idPath));
				$idPath->popAttribute();
			}
		
		return $result;
	}
	
	public function getUpdateTuple($idPath, $heading, $editors) {
		$result = new ArrayTuple($heading);
		
		foreach($editors as $editor) 
			if ($attribute = $editor->getUpdateAttribute()) {
				$idPath->pushAttribute($attribute);
				$result->setAttributeValue($attribute, $editor->getUpdateValue($idPath));
				$idPath->popAttribute();
			}
		
		return $result;
	}
	
	public function save($idPath, $value) {
		if ($this->allowAdd) {
			$addHeading = $this->getAddHeading();
			
			if (count($addHeading->attributes) > 0) {
				$addEditors = $this->getAddEditors();
				$tuple = $this->getAddTuple($idPath, $addHeading, $addEditors);
				$this->controller->add($idPath->getKeyStack(), $tuple);
			}
		}
		
		$tupleCount = $value->getTupleCount();
		$key = $value->getKey();
		$updateHeading = $this->getUpdateHeading();
		$updateEditors = $this->getUpdateEditors();
		
		for ($i = 0; $i < $tupleCount; $i++) {
			$tuple = $value->getTuple($i);
			$idPath->pushKey(project($tuple, $key));
			
			if (!$this->allowRemove || !$this->removeTuple($idPath)) {
				$this->saveTuple($idPath, $tuple);
				$this->updateTuple($idPath, $tuple, $updateHeading, $updateEditors);
			}
			
			$idPath->popKey();
		}
	}
	
	public function getUpdateAttribute() {
		return null;	
	}
	
	public function getAddAttribute() {
		$result = null;
		
		if ($this->isAddField) {
			$addHeading = $this->getAddHeading();
			
			if (count($addHeading->attributes) > 0)
				$result = new Attribute($this->attribute->id, $this->attribute->name, new RelationType($addHeading));
		}
		
		return $result;	
	}
}

class RelationTableEditor extends RelationEditor {
	public function view($idPath, $value) {
		$result = '<table id="'. $idPath->getId() .'" class="wiki-data-table">';	
		$heading = $value->getHeading();
		$key = $value->getKey();
		
		foreach(getHeadingAsTableHeaderRows($this->getTableHeading($this)) as $headerRow)
			$result .= '<tr>' . $headerRow . '</tr>';
		
		$tupleCount = $value->getTupleCount();
		
		for($i = 0; $i < $tupleCount; $i++) {
			$tuple = $value->getTuple($i);
			$idPath->pushKey(project($tuple, $key));
			$result .= '<tr id="'. $idPath->getId() .'">' . getTupleAsTableCells($idPath, $this, $tuple) .'</tr>';
			$idPath->popKey();
		}
		
		$result .= '</table>';
	
		return $result;
	}
	
	public function edit($idPath, $value) {
		$result = '<table id="'. $idPath->getId() .'" class="wiki-data-table">';	
		$key = $value->getKey();
		
		$headerRows = getHeadingAsTableHeaderRows($this->getHeading());
	
		if ($this->allowRemove)
			$headerRows[0] = '<th class="remove" rowspan="' . count($headerRows) . '"><img src="skins/amethyst/delete.png" title="Mark rows to remove" alt="Remove"/></th>' . $headerRows[0];
			
		if ($this->repeatInput)		
			$headerRows[0] .= '<th class="add" rowspan="' . count($headerRows) . '">Input rows</th>';
			
		foreach ($headerRows as $headerRow)
			$result .= '<tr>' . $headerRow . '</tr>';
		
		$tupleCount = $value->getTupleCount();
		
		for ($i = 0; $i < $tupleCount; $i++) {
			$result .= '<tr>';
			$tuple = $value->getTuple($i);
			$idPath->pushKey(project($tuple, $key));
			
			if ($this->allowRemove)
				$result .= '<td class="remove">' . getRemoveCheckBox('remove-'. $idPath->getId()) . '</td>';
			
			$result .= getTupleAsEditTableCells($tuple, $idPath, $this);
			$idPath->popKey();		
			
			if ($this->repeatInput)
				$result .= '<td/>';
			
			$result .= '</tr>';
		}
		
		if ($this->allowAdd) 
			$result .= $this->getAddRowAsHTML($idPath, $this->repeatInput, $this->allowRemove);
		
		$result .= '</table>';
	
		return $result;
	}
	
	public function add($idPath) {
		if ($this->isAddField) {
			$result = '<table id="'. $idPath->getId() .'" class="wiki-data-table">';	
			$headerRows = getHeadingAsTableHeaderRows($this->getAddHeading());
		
	//		if ($repeatInput)		
	//			$headerRows[0] .= '<th class="add" rowspan="' . count($headerRows) . '">Input rows</th>';
				
			foreach ($headerRows as $headerRow)
				$result .= '<tr>' . $headerRow . '</tr>';
			
			$result .= $this->getAddRowAsHTML($idPath, false, false);
			$result .= '</table>';
	
			return $result;
		}
		else 
			return "";
	}
	
	function getAddRowAsHTML($idPath, $repeatInput, $allowRemove) {
		if ($repeatInput)
			$rowClass = 'repeat';
		else 
			$rowClass = '';
			
		$result = '<tr id="'. $idPath->getId() . '" class="' . $rowClass . '">';
		
		if ($allowRemove)
			$result .= '<td/>';
		
		$result .= getHeadingAsAddCells($idPath, $this);
					
		if ($repeatInput)
			$result .= '<td class="add"/>';
			
		return $result . '</tr>'; 
	}
	
	public function getTableHeading($editor) {
		$attributes = array();
		
		foreach($editor->getEditors() as $childEditor) { 
			$childAttribute = $childEditor->getAttribute();
			
			if (is_a($childEditor, TupleTableCellEditor))
				$type = new TupleType($this->getTableHeading($childEditor));
			else
				$type = 'short-text';
				
			$attributes[] = new Attribute($childAttribute->id, $childAttribute->name, $type);;
		}
			
		return new Heading($attributes);
	}
}

abstract class TupleEditor extends DefaultEditor {
	protected function getUpdateHeading() {
		$attributes = array();
		
		foreach($this->editors as $editor)
			if ($updateAttribute = $editor->getUpdateAttribute())
				$attributes[] = $updateAttribute;
		
		return new Heading($attributes);
	}
	
	protected function getAddHeading() {
		$attributes = array();
		
		foreach($this->editors as $editor)
			if ($addAttribute = $editor->getAddAttribute())
				$attributes[] = $addAttribute;
		
		return new Heading($attributes);
	}
	
	public function getUpdateValue($idPath) {
		$result = new ArrayTuple($this->getUpdateHeading());
		
		foreach($this->editors as $editor) 
			if ($attribute = $editor->getUpdateAttribute()) { 
				$idPath->pushAttribute($attribute);
				$result->setAttributeValue($attribute, $editor->getUpdateValue($idPath));
				$idPath->popAttribute();
			}
		
		return $result;
	}
	
	public function getAddValue($idPath) {
		$result = new ArrayTuple($this->getAddHeading());
		
		foreach($this->editors as $editor) 
			if ($attribute = $editor->getAddAttribute()) { 
				$idPath->pushAttribute($attribute);
				$result->setAttributeValue($attribute, $editor->getAddValue($idPath));
				$idPath->popAttribute();
			}
		
		return $result;
	}
	
	public function getUpdateAttribute() {
		$updateHeading = $this->getUpdateHeading();
		
		if (count($updateHeading->attributes) > 0)
			return new Attribute($this->attribute->id, $this->attribute->name, new TupleType($updateHeading));
		else
			return null;	
	}
	
	public function getAddAttribute() {
		$addHeading = $this->getAddHeading();
		
		if (count($addHeading->attributes) > 0)
			return new Attribute($this->attribute->id, $this->attribute->name, new TupleType($addHeading));
		else
			return null;	
	}
	
	public function save($idPath, $value) {
		foreach($this->editors as $editor) {
			$attribute = $editor->getAttribute();
			$idPath->pushAttribute($attribute);			
			$editor->save($idPath, $value->getAttributeValue($attribute));
			$idPath->popAttribute();
		}
	}
}

class TupleTableCellEditor extends TupleEditor {
	public function view($idPath, $value) {
	}

	public function edit($idPath, $value) {
	}

	public function add($idPath) {
	}
	
	public function save($idPath, $value) {
	}
}

abstract class ScalarEditor extends DefaultEditor {
	protected $allowUpdate;
	protected $isAddField;
	
	public function __construct($attribute, $allowUpdate, $isAddField) {
		parent::__construct($attribute);
		
		$this->allowUpdate = $allowUpdate;
		$this->isAddField = $isAddField;
	}
	
	protected function addId($id) {
		return "add-" . $id;
	}	
	
	protected function updateId($id) {
		return "update-" . $id;
	}
	
	public function save($idPath, $value) {
	}

	public function getUpdateAttribute() {
		if ($this->allowUpdate)
			return $this->attribute;
		else
			return null;	
	}
	
	public function getAddAttribute() {
		if ($this->isAddField)
			return $this->attribute;
		else
			return null;	
	}
	
	public abstract function getInputValue($id);
	
	public function getUpdateValue($idPath) {
		return $this->getInputValue("update-" . $idPath->getId());
	}
	
	public function getAddValue($idPath) {
		return $this->getInputValue("add-" . $idPath->getId());
	}
}

class LanguageEditor extends ScalarEditor {
	public function view($idPath, $value) {
		return languageIdAsText($value);
	}
	
	public function edit($idPath, $value) {
		if ($this->allowUpdate)
			return getLanguageSelect($this->updateId($idPath->getId()));
		else
			return languageIdAsText($value);
	}
	
	public function add($idPath) {
		return getLanguageSelect($this->addId($idPath->getId()));
	}
	
	public function getInputValue($id) {
		global
			$wgRequest;
			
		return $wgRequest->getInt($id);
	}
}

class SpellingEditor extends ScalarEditor {
	public function view($idPath, $value) {
		return spellingAsLink($value);
	}
	
	public function edit($idPath, $value) {
		if ($this->allowUpdate)
			return getTextBox($this->updateId($idPath->getId()));
		else
			return spellingAsLink($value);
	}
	
	public function add($idPath) {
		if ($this->isAddField)
			return getTextBox($this->addId($idPath->getId()));
		else
			return "";
	}

	public function getInputValue($id) {
		global
			$wgRequest;
			
		return trim($wgRequest->getText($id));		
	}
}

class TextEditor extends ScalarEditor {
	public function view($idPath, $value) {
		return htmlspecialchars($value);
	}

	public function edit($idPath, $value) {
		if ($this->allowUpdate) {
			return getTextArea($this->updateId($idPath->getId()), $value, 3);
		}
		else
			return htmlspecialchars($value);
	}
	
	public function add($idPath) {
		if ($this->isAddField)
			return getTextArea($this->addId($idPath->getId()), "", 3);
		else
			return "";
	}
	
	public function getInputValue($id) {
		global
			$wgRequest;
			
		return trim($wgRequest->getText($id));		
	}
}

class ShortTextEditor extends ScalarEditor {
	public function view($idPath, $value) {
		return htmlspecialchars($value);
	}

	public function edit($idPath, $value) {
		if ($this->allowUpdate) 
			return getTextBox($this->updateId($idPath->getId()), $value);
		else
			return htmlspecialchars($value);
	}
	
	public function add($idPath) {
		if ($this->isAddField)
			return getTextBox($this->addId($idPath->getId()), "");
		else
			return "";
	}
	
	public function getInputValue($id) {
		global
			$wgRequest;
			
		return trim($wgRequest->getText($id));		
	}
}

class BooleanEditor extends ScalarEditor {
	protected $defaultValue;
	
	public function __construct($attribute, $allowUpdate, $isAddField, $defaultValue) {
		parent::__construct($attribute, $allowUpdate, $isAddField);
		
		$this->defaultValue = $defaultValue;
	}

	public function view($idPath, $value) {
		return booleanAsHTML($value);
	}

	public function edit($idPath, $value) {
		if ($this->allowUpdate)
			return getCheckBox($this->updateId($idPath->getId()), $value);
		else
			return booleanAsHTML($idPath, $value);
	}
	
	public function add($idPath) {
		if ($this->isAddField)
			return getCheckBox($this->addId($idPath->getId()), $this->defaultValue);
		else
			return "";
	}
	
	public function getInputValue($id) {
		global
			$wgRequest;
			
		return $wgRequest->getCheck($id);		
	}
}

abstract class SuggestEditor extends ScalarEditor {
	public function add($idPath) {
		if ($this->isAddField)
			return getSuggest($this->addId($idPath->getId()), $this->suggestType());
		else
			return "";
	}
	
	protected abstract function suggestType();
	
	public function edit($idPath, $value) {
		if ($this->allowUpdate) 
			return getSuggest($this->updateId($idPath->getId()), $this->suggestType()); 
		else
			return $this->view($idPath, $value);
	}

	public function getInputValue($id) {
		global
			$wgRequest;
			
		return trim($wgRequest->getText($id));		
	}
}

class RelationTypeEditor extends SuggestEditor {
	protected function suggestType() {
		return "relation-type";
	}
	
	public function view($idPath, $value) {
		return definedMeaningAsLink($value);
	}

}

class DefinedMeaningEditor extends SuggestEditor {
	protected function suggestType() {
		return "defined-meaning";
	}
	
	public function view($idPath, $value) {
		return definedMeaningAsLink($value);
	}
}

class AttributeEditor extends SuggestEditor {
	protected function suggestType() {
		return "attribute";
	}
	
	public function view($idPath, $value) {
		return definedMeaningAsLink($value);
	}
}

class CollectionEditor extends SuggestEditor {
	protected function suggestType() {
		return "collection";
	}
	
	public function view($idPath, $value) {
		return collectionAsLink($value);
	}
}

class TupleListEditor extends TupleEditor {
	protected $expandedEditors = array();
	
	public function view($idPath, $value) {
		$result = '<ul class="collapsable-items">';
		
		foreach ($this->editors as $editor) {
			$attribute = $editor->getAttribute();
	
			if (!in_array($editor, $this->expandedEditors)) {
				$style = ' style="display: none;"';
				$character = '+';
			}
			else {
				$style = '';
				$character = '&ndash;';
			}
			
			$idPath->pushAttribute($attribute);
			$attributeId = $idPath->getId();
			$result .= '<li>'.
						'<h4 id="collapse-'. $attributeId .'" class="toggle" onclick="toggle(this, event);">'. $character . ' ' . $attribute->name . '</h4>' .
						'<div id="collapsable-'. $attributeId . '"'. $style .'>' . $editor->view($idPath, $value->getAttributeValue($attribute)) . '</div>' .
						'</li>';
			$idPath->popAttribute();
		}
		
		$result .= '</ul>';
		
		return $result;
	}
	
	public function edit($idPath, $value) {
		$result = '<ul class="collapsable-items">';
		
		foreach ($this->editors as $editor) {
			$attribute = $editor->getAttribute();
	
			if (!in_array($editor, $this->expandedEditors)) {
				$style = ' style="display: none;"';
				$character = '+';
			}
			else {
				$style = '';
				$character = '&ndash;';
			}
			
			$idPath->pushAttribute($attribute);
			$attributeId = $idPath->getId();
			$result .= '<li>'.
						'<h4 id="collapse-'. $attributeId .'" class="toggle" onclick="toggle(this, event);">'. $character . ' ' . $attribute->name . '</h4>' .
						'<div id="collapsable-'. $attributeId . '"'. $style .'>' . $editor->edit($idPath, $value->getAttributeValue($attribute)) . '</div>' .
						'</li>';
			$idPath->popAttribute();
		}
		
		$result .= '</ul>';
		
		return $result;
	}
	
	public function add($idPath) {
		$result = '<ul class="collapsable-items">';
		
		foreach($this->editors as $editor) {
			if ($attribute = $editor->getAddAttribute()) {
				if (!in_array($editor, $this->expandedEditors)) {
					$style = ' style="display: none;"';
					$character = '+';
				}
				else {
					$style = '';
					$character = '&ndash;';
				}

				$attribute = $editor->getAttribute();
				$idPath->pushAttribute($attribute);			
				$attributeId = $idPath->getId();
				$result .= '<li>'.
							'<h4 id="collapse-'. $attributeId .'" class="toggle" onclick="toggle(this, event);">'. $character  .' ' . $attribute->name . '</h4>' .
							'<div id="collapsable-'. $attributeId . '"'. $style .'>' . $editor->add($idPath) . '</div>' .
							'</li>';
				$editor->add($idPath);
				$idPath->popAttribute();
			}
		}
	
		$result .= '</ul>';

		return $result;
	}
	
	public function expandEditor($editor) {
		$this->expandedEditors[] = $editor;
	}
}

class RelationListEditor extends RelationEditor {
	protected $headerLevel;
	protected $childrenExpanded;
	protected $captionEditor;
	protected $valueEditor;

	public function __construct($attribute, $allowAdd, $allowRemove, $isAddField, $controller, $headerLevel, $childrenExpanded) {
		parent::__construct($attribute, $allowAdd, $allowRemove, $isAddField, $controller);
		
		$this->headerLevel = $headerLevel;
		$this->childrenExpanded = $childrenExpanded;
	}

	public function setCaptionEditor($editor) {
		$this->captionEditor = $editor;
		$this->editors[0] = $editor;
	} 

	public function setValueEditor($editor) {
		$this->valueEditor = $editor;
		$this->editors[1] = $editor;
	} 
	
	public function view($idPath, $value) {
		$result = '<ul class="collapsable-items">';
		$tupleCount = $value->getTupleCount();
		$key = $value->getKey();
		$captionAttribute = $this->captionEditor->getAttribute();
		$valueAttribute = $this->valueEditor->getAttribute();
				
		if (!$this->childrenExpanded) {
			$style = ' style="display: none;"';
			$character = '+';
		}
		else {
			$style = '';
			$character = '&ndash;';
		}

		for ($i = 0; $i < $tupleCount; $i++) {
			$tuple = $value->getTuple($i);
			$idPath->pushKey(project($tuple, $key));
			$tupleId = $idPath->getId();
			
			$idPath->pushAttribute($captionAttribute);
			$result .= '<li>'.
						'<h' . $this->headerLevel .' id="collapse-'. $tupleId .'" class="toggle" onclick="toggle(this, event);">'. $character . ' ' . $this->captionEditor->view($idPath, $tuple->getAttributeValue($captionAttribute)) . '</h' . $this->headerLevel .'>';
			$idPath->popAttribute();
			
			$idPath->pushAttribute($valueAttribute);
			$result .= '<div id="collapsable-'. $tupleId . '"'. $style .'>' . $this->valueEditor->view($idPath, $tuple->getAttributeValue($valueAttribute)) . '</div>' .
						'</li>';
			$idPath->popAttribute();

			$idPath->popKey();
		}
		
		$result .= '</ul>';
		
		return $result;
	}
	
	public function edit($idPath, $value) {
		$result = '<ul class="collapsable-items">';
		$tupleCount = $value->getTupleCount();
		$key = $value->getKey();
		$captionAttribute = $this->captionEditor->getAttribute();
		$valueAttribute = $this->valueEditor->getAttribute();
				
		if (!$this->childrenExpanded) {
			$style = ' style="display: none;"';
			$character = '+';
		}
		else {
			$style = '';
			$character = '&ndash;';
		}

		for ($i = 0; $i < $tupleCount; $i++) {
			$tuple = $value->getTuple($i);
			$idPath->pushKey(project($tuple, $key));
			
			$tupleId = $idPath->getId();

			$idPath->pushAttribute($captionAttribute);
			$result .= '<li>'.
						'<h' . $this->headerLevel .' id="collapse-'. $tupleId .'" class="toggle" onclick="toggle(this, event);">'. $character . ' ' . $this->captionEditor->edit($idPath, $tuple->getAttributeValue($captionAttribute)) . '</h' . $this->headerLevel .'>';
			$idPath->popAttribute();

			$idPath->pushAttribute($valueAttribute);
			$result .= '<div id="collapsable-'. $tupleId . '"'. $style .'>' . $this->valueEditor->edit($idPath, $tuple->getAttributeValue($valueAttribute)) . '</div>' .
						'</li>';
			$idPath->popAttribute();

			$idPath->popKey();
		}

		if ($this->allowAdd) {
			if ($tupleCount > 0) {
				$style = ' style="display: none;"';
				$character = '+';
			}
			else {
				$style = '';
				$character = '&ndash;';
			}
			
			$tupleId = 'add-' . $idPath->getId();
			$idPath->pushAttribute($captionAttribute);
			$result .= '<li>'.
						'<h' . $this->headerLevel .' id="collapse-'. $tupleId .'" class="toggle" onclick="toggle(this, event);">'. $character  . ' ' . $this->captionEditor->add($idPath) . '</h' . $this->headerLevel .'>';
			$idPath->popAttribute();

			$idPath->pushAttribute($valueAttribute);
			$result .= '<div id="collapsable-'. $tupleId . '"'. $style .'>' . $this->valueEditor->add($idPath) . '</div>' .
						'</li>';
			$idPath->popAttribute();
		}
		
		$result .= '</ul>';
		
		return $result;
	}
	
	public function add($idPath) {
		$result = '<ul class="collapsable-items">';
		$captionAttribute = $this->captionEditor->getAttribute();
		$valueAttribute = $this->valueEditor->getAttribute();

		$tupleId = 'add-' . $idPath->getId();

		$idPath->pushAttribute($captionAttribute);
		$result .= '<li>'.
					'<h' . $this->headerLevel .' id="collapse-'. $tupleId .'" class="toggle" onclick="toggle(this, event);">&ndash; ' . $this->captionEditor->add($idPath) . '</h' . $this->headerLevel .'>';
		$idPath->popAttribute();

		$idPath->pushAttribute($valueAttribute);
		$result .= '<div id="collapsable-'. $tupleId . '">' . $this->valueEditor->add($idPath) . '</div>' .
					'</li>';
		$idPath->popAttribute();

		$result .= '</ul>';
		
		return $result;
	}
}

class AttributeLabelViewer extends Viewer {
	public function view($idPath, $value) {
		return $this->attribute->name;
	}
	
	public function add($idPath) {
		return "New " . strtolower($this->attribute->name);
	}
}

class TupleSpanEditor extends TupleEditor {
//	protected $attribute;
	protected $attributeSeparator;
	protected $valueSeparator;
//	protected $viewers = array();
	
	public function __construct($attribute, $valueSeparator, $attributeSeparator) {
		parent::__construct($attribute);

		$this->attributeSeparator = $attributeSeparator;
		$this->valueSeparator = $valueSeparator;
	}
		
	public function view($idPath, $value) {
		$fields = array();
		
		foreach($this->editors as $editor) {
			$attribute = $editor->getAttribute();
			$idPath->pushAttribute($attribute);
			$fields[] = $attribute->name . $this->valueSeparator. $editor->view($idPath, $value->getAttributeValue($attribute));
			$idPath->popAttribute();
		}
		
		return implode($this->attributeSeparator, $fields);
	}
	
//	public function getAttribute() {
//		return $this->attribute;
//	}
//	
//	public function addViewer($viewer) {
//		$this->viewers[] = $viewer;
//	}
	
	public function add($idPath) {
		$fields = array();

		foreach($this->editors as $editor) {
			if ($attribute = $editor->getAddAttribute()) {
				$attribute = $editor->getAttribute();
				$idPath->pushAttribute($attribute);			
				$attributeId = $idPath->getId();
				$fields[] = $attribute->name . $this->valueSeparator. $editor->add($idPath);
				$editor->add($idPath);
				$idPath->popAttribute();
			}
		}

		return implode($this->attributeSeparator, $fields);
	}
	
	public function edit($idPath, $value) {
		$fields = array();
		
		foreach($this->editors as $editor) {
			$attribute = $editor->getAttribute();
			$idPath->pushAttribute($attribute);
			$fields[] = $attribute->name . $this->valueSeparator. $editor->view($idPath, $value->getAttributeValue($attribute));
			$idPath->popAttribute();
		}
		
		return implode($this->attributeSeparator, $fields);
	}
} 

?>
