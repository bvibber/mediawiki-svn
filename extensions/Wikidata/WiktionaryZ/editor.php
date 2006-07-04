<?php

require_once("HTMLtable.php");

interface Editor {
	public function getAttribute();
	public function getUpdateAttribute();
	public function getAddAttribute();
	
	public function view($id, $keyPath, $value);
	public function edit($id, $keyPath, $value);
	public function add($id, $keyPath);
	public function save($id, $keyPath, $value);

	public function getUpdateValue($id, $keyPath);
	public function getAddValue($id, $keyPath);
	
	public function getEditors();
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

class TableEditor extends DefaultEditor {
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
	
	public function view($id, $keyPath, $value) {
		return getRelationAsHTMLTable($this, $id, $keyPath, $value);
	}
	
	public function edit($id, $keyPath, $value) {
		$result = '<table id="'. $id .'" class="wiki-data-table">';	
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
			$keyPath->push(project($tuple, $key));
			$tupleKeyName = getTupleKeyName($tuple, $key);
			
			if ($this->allowRemove)
				$result .= '<td class="remove">' . getRemoveCheckBox('remove-'. $id . '-' . $tupleKeyName) . '</td>';
			
			$result .= getTupleAsEditTableCells($tuple,  $id . '-' . $tupleKeyName, $keyPath, $this);
			$keyPath->pop();		
			
			if ($this->repeatInput)
				$result .= '<td/>';
			
			$result .= '</tr>';
		}
		
		if ($this->allowAdd) 
			$result .= getAddRowAsHTML($id, $keyPath, $this, $this->repeatInput, $this->allowRemove);
		
		$result .= '</table>';
	
		return $result;
		//return getRelationAsEditHTML($this, $id, $keyPath, $value, $this->allowAdd, $this->allowRemove, false);
	}
	
	public function add($id, $keyPath) {
		$result = '<table id="'. $id .'" class="wiki-data-table">';	
		$headerRows = getHeadingAsTableHeaderRows($this->getAddHeading());
	
//		if ($repeatInput)		
//			$headerRows[0] .= '<th class="add" rowspan="' . count($headerRows) . '">Input rows</th>';
			
		foreach ($headerRows as $headerRow)
			$result .= '<tr>' . $headerRow . '</tr>';
		
		$result .= getAddRowAsHTML($id . '-' . $this->attribute->id, $keyPath, $this, false, false);
		$result .= '</table>';

		return $result;
	}
	
	public function getUpdateValue($id, $keyPath) {
		return null;
	}
	
	public function getAddValue($id, $keyPath) {
		$addHeading = $this->getAddHeading();
		
		if (count($addHeading->attributes) > 0) {
			$relation = new ArrayRelation($addHeading, $addHeading);  // TODO Determine real key
			$values = array();
			
			foreach($this->editors as $editor) 
				if ($attribute = $editor->getAddAttribute())  
					$values[] = $editor->getAddValue($id. '-' . $attribute->id, $keyPath);
				
			$relation->addTuple($values);	
						 
			return $relation;
		}
		else
			return null;	
	}

	protected function saveTuple($id, $keyPath, $tuple) {
		foreach($this->editors as $editor) {
			$attribute = $editor->getAttribute();
			$value = $tuple->getAttributeValue($attribute);
			$editor->save($id, $keyPath, $value);
		}
	}
	
	protected function updateTuple($id, $keyPath, $tuple, $heading, $editors) {
		if (count($editors) > 0) {
			$updateTuple = $this->getUpdateTuple($id, $keyPath, $heading, $editors);
			
			if (!equalTuples($heading, $tuple, $updateTuple))		
				$this->controller->update($keyPath, $updateTuple);
		}
	}
	
	protected function removeTuple($id, $keyPath) {
		global
			$wgRequest;
		
		if ($wgRequest->getCheck('remove-'. $id)) {	
			$this->controller->remove($keyPath, null);
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
	
	public function getAddTuple($id, $keyPath, $heading, $editors) {
		$result = new ArrayTuple($heading);
		
		foreach($editors as $editor) 
			if ($attribute = $editor->getAddAttribute())
				$result->setAttributeValue($attribute, $editor->getAddValue($id . '-' . $attribute->id, $keyPath));
		
		return $result;
	}
	
	public function getUpdateTuple($id, $keyPath, $heading, $editors) {
		$result = new ArrayTuple($heading);
		
		foreach($editors as $editor) 
			if ($attribute = $editor->getUpdateAttribute())
				$result->setAttributeValue($attribute, $editor->getUpdateValue($id . '-' . $attribute->id, $keyPath));
		
		return $result;
	}
	
	public function save($id, $keyPath, $value) {
		if ($this->allowAdd) {
			$addHeading = $this->getAddHeading();
			$addEditors = $this->getAddEditors();
			$tuple = $this->getAddTuple($id, $keyPath, $addHeading, $addEditors);
			$this->controller->add($keyPath, $tuple);
		}
		
		$tupleCount = $value->getTupleCount();
		$key = $value->getKey();
		$updateHeading = $this->getUpdateHeading();
		$updateEditors = $this->getUpdateEditors();
		
		for ($i = 0; $i < $tupleCount; $i++) {
			$tuple = $value->getTuple($i);
			$tupleKeyName = getTupleKeyName($tuple, $key);
			$keyPath->push(project($tuple, $key));
			$tupleId = $id . '-' . $tupleKeyName;
			
			if (!$this->allowRemove || !$this->removeTuple($tupleId, $keyPath)) {
				$this->saveTuple($tupleId, $keyPath, $tuple);
				$this->updateTuple($tupleId, $keyPath, $tuple, $updateHeading, $updateEditors);
			}
			
			$keyPath->pop();
		}
	}
	
	public function getUpdateAttribute() {
		return null;	
	}
	
	public function getAddAttribute() {
		$addHeading = $this->getAddHeading();
		
		if (count($addHeading->attributes) > 0)
			return new Attribute($this->attribute->id, $this->attribute->name, new RelationType($addHeading));
		else
			return null;	
	}
}

class TupleTableCellEditor extends DefaultEditor {
	public function view($id, $keyPath, $value) {
	}

	public function edit($id, $keyPath, $value) {
	}

	public function add($id, $keyPath) {
	}
	
	public function save($id, $keyPath, $value) {
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
	
	public function getUpdateValue($id, $keyPath) {
		$result = new ArrayTuple($this->getUpdateHeading());
		
		foreach($this->editors as $editor) 
			if ($attribute = $editor->getUpdateAttribute()) 
				$result->setAttributeValue($attribute, $editor->getUpdateValue($id . '-' . $attribute->id, $keyPath));
		
		return $result;
	}
	
	public function getAddValue($id, $keyPath) {
		$result = new ArrayTuple($this->getAddHeading());
		
		foreach($this->editors as $editor) 
			if ($attribute = $editor->getAddAttribute()) 
				$result->setAttributeValue($attribute, $editor->getAddValue($id . '-' . $attribute->id, $keyPath));
		
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
		return "add-" . $id . '-' . $this->attribute->id;
	}	
	
	protected function updateId($id) {
		return "update-" . $id . '-' . $this->attribute->id;
	}
	
	public function save($id, $keyPath, $value) {
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
	
	public abstract function getInputValue($id, $keyPath);
	
	public function getUpdateValue($id, $keyPath) {
		return $this->getInputValue("update-" . $id, $keyPath);
	}
	
	public function getAddValue($id, $keyPath) {
		return $this->getInputValue("add-" . $id, $keyPath);
	}
}

class LanguageEditor extends ScalarEditor {
	public function view($id, $keyPath, $value) {
		return languageIdAsText($value);
	}
	
	public function edit($id, $keyPath, $value) {
		if ($this->allowUpdate)
			return getLanguageSelect($this->updateId($id));
		else
			return languageIdAsText($value);
	}
	
	public function add($id, $keyPath) {
		return getLanguageSelect($this->addId($id));
	}
	
	public function getInputValue($id, $keyPath) {
		global
			$wgRequest;
			
		return $wgRequest->getInt($id);		
	}
}

class SpellingEditor extends ScalarEditor {
	public function view($id, $keyPath, $value) {
		return spellingAsLink($value);
	}
	
	public function edit($id, $keyPath, $value) {
		if ($this->allowUpdate)
			return getTextBox($this->updateId($id));
		else
			return spellingAsLink($value);
	}
	
	public function add($id, $keyPath) {
		if ($this->isAddField)
			return getTextBox($this->addId($id));
		else
			return "";
	}

	public function getInputValue($id, $keyPath) {
		global
			$wgRequest;
			
		return trim($wgRequest->getText($id));		
	}
}

class TextEditor extends ScalarEditor {
	public function view($id, $keyPath, $value) {
		return htmlspecialchars($value);
	}

	public function edit($id, $keyPath, $value) {
		if ($this->allowUpdate) {
			return getTextArea($this->updateId($id), $value, 3);
		}
		else
			return htmlspecialchars($value);
	}
	
	public function add($id, $keyPath) {
		if ($this->isAddField)
			return getTextArea($this->addId($id), "", 3);
		else
			return "";
	}
	
	public function getInputValue($id, $keyPath) {
		global
			$wgRequest;
			
		return trim($wgRequest->getText($id));		
	}
}

class ShortTextEditor extends ScalarEditor {
	public function view($id, $keyPath, $value) {
		return htmlspecialchars($value);
	}

	public function edit($id, $keyPath, $value) {
		if ($this->allowUpdate) {
			return getTextBox($this->updateId($id), $value);
		}
		else
			return htmlspecialchars($value);
	}
	
	public function add($id, $keyPath) {
		if ($this->isAddField)
			return getTextBox($this->addId($id), "");
		else
			return "";
	}
	
	public function getInputValue($id, $keyPath) {
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

	public function view($id, $keyPath, $value) {
		return booleanAsHTML($value);
	}

	public function edit($id, $keyPath, $value) {
		if ($this->allowUpdate)
			return getCheckBox($this->updateId($id), $value);
		else
			return booleanAsHTML($id, $value);
	}
	
	public function add($id, $keyPath) {
		if ($this->isAddField)
			return getCheckBox($this->addId($id), $this->defaultValue);
		else
			return "";
	}
	
	public function getInputValue($id, $keyPath) {
		global
			$wgRequest;
			
		return $wgRequest->getCheck($id);		
	}
}

abstract class SuggestEditor extends ScalarEditor {
	public function add($id, $keyPath) {
		if ($this->isAddField)
			return getSuggest($this->addId($id), $this->suggestType());
		else
			return "";
	}
	
	protected abstract function suggestType();
	
	public function edit($id, $keyPath, $value) {
		if ($this->allowUpdate) {
			return getSuggest($this->updateId($id), $this->suggestType()); 
		}
		else
			return $this->view($id, $keyPath, $value);
	}

	public function getInputValue($id, $keyPath) {
		global
			$wgRequest;
			
		return trim($wgRequest->getText($id));		
	}
}

class RelationTypeEditor extends SuggestEditor {
	protected function suggestType() {
		return "relation-type";
	}
	
	public function view($id, $keyPath, $value) {
		return definedMeaningAsLink($value);
	}

}

class DefinedMeaningEditor extends SuggestEditor {
	protected function suggestType() {
		return "defined-meaning";
	}
	
	public function view($id, $keyPath, $value) {
		return definedMeaningAsLink($value);
	}
}

class AttributeEditor extends SuggestEditor {
	protected function suggestType() {
		return "attribute";
	}
	
	public function view($id, $keyPath, $value) {
		return definedMeaningAsLink($value);
	}
}

class CollectionEditor extends SuggestEditor {
	protected function suggestType() {
		return "collection";
	}
	
	public function view($id, $keyPath, $value) {
		return collectionAsLink($value);
	}
}

?>
