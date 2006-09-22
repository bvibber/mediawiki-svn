<?php

/* Copyright (C) 2006 by Charta Software
 *   http://www.charta.org/
 */

require_once("HTMLtable.php");
require_once("Controller.php");

function addCollapsablePrefixToClass($class) {
	return "collapsable-$class";
}

class IdStack {
	protected $keyStack;
	protected $idStack = array();
	protected $currentId;
	protected $classStack = array();
	protected $currentClass;

	public function __construct($prefix) {
	 	$this->keyStack = new RecordStack();
	 	$this->currentId = $prefix;
	 	$this->currentClass = $prefix;
	}

	protected function getKeyIds($record) {
		$ids = array();

		foreach($record->getStructure()->attributes as $attribute)
			$ids[] = $record->getAttributeValue($attribute);

		return $ids;
	}

	protected function pushId($id) {
		$this->idStack[] = $this->currentId;
		$this->currentId .= '-' . $id;
	}

	protected function popId() {
		$this->currentId = array_pop($this->idStack);
	}

	protected function pushClass($class) {
		$this->classStack[] = $this->currentClass;
		$this->currentClass .= '-' . $class;
	}

	protected function popClass() {
		$this->currentClass = array_pop($this->classStack);
	}

	public function pushKey($record) {
		$this->keyStack->push($record);
		$this->pushId(implode("-", $this->getKeyIds($record)));
	}

	public function pushAttribute($attribute) {
		$this->pushId($attribute->id);
		$this->pushClass($attribute->id);
	}

	public function popKey() {
		$this->popId();
		return $this->keyStack->pop();
	}

	public function popAttribute() {
		$this->popId();
		$this->popClass();
	}

	public function getId() {
		return $this->currentId;
	}

	public function getClass() {
		return $this->currentClass;
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

	public function getExpansionPrefix($class, $elementId) {
		return '<span id="prefix-collapsed-' . $elementId . '" class="collapse-' . $class . '">+</span><span id="prefix-expanded-' . $elementId . '" class="expand-' . $class . '">&ndash;</span>';
	}

	static private $staticExpansionStyles = array();

	protected function setExpansion($expand, $elementType) {
		$expansionStyles =& DefaultEditor::$staticExpansionStyles;
		if ($expand) {
			$expansionStyles[".collapse-" . $elementType] = "display:none;";
			$expansionStyles[".expand-" . $elementType] = "display:inline;";
		} else {
			$expansionStyles[".collapse-" . $elementType] = "display:inline;";
			$expansionStyles[".expand-" . $elementType] = "display:none;";
		}
	}

	public static function getExpansionCss() {
		$s = "<style type='text/css'>\n";
		$s .= "/*/*/ /*<![CDATA[*/\n"; # <-- Hide the styles from Netscape 4 without hiding them from IE/Mac
		foreach(DefaultEditor::$staticExpansionStyles as $expansionStyleName => $expansionStyleValue)
			$s .= $expansionStyleName . " {" . $expansionStyleValue . "}\n";
		$s .= "/*]]>*/ /* */\n";
		$s .= "</style>\n";
		return $s;
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

abstract class RecordSetEditor extends DefaultEditor {
	protected $permissionController;
	protected $allowAdd;
	protected $allowRemove;
	protected $isAddField;
	protected $controller;

	public function __construct($attribute, $permissionController, $allowAdd, $allowRemove, $isAddField, $controller) {
		parent::__construct($attribute);

		$this->permissionController = $permissionController;
		$this->allowAdd = $allowAdd;
		$this->allowRemove = $allowRemove;
		$this->isAddField = $isAddField;
		$this->controller = $controller;
	}

	public function getAddValue($idPath) {
		$addStructure = $this->getAddStructure();

		if (count($addStructure->attributes) > 0) {
			$relation = new ArrayRecordSet($addStructure, $addStructure);  // TODO Determine real key
			$values = array();

			foreach($this->editors as $editor)
				if ($attribute = $editor->getAddAttribute()) {
					$idPath->pushAttribute($attribute);
					$values[] = $editor->getAddValue($idPath);
					$idPath->popAttribute();
				}

			$relation->addRecord($values);

			return $relation;
		}
		else
			return null;
	}

	protected function saveRecord($idPath, $record) {
		foreach($this->editors as $editor) {
			$attribute = $editor->getAttribute();
			$value = $record->getAttributeValue($attribute);
			$idPath->pushAttribute($attribute);
			$editor->save($idPath, $value);
			$idPath->popAttribute();
		}
	}

	protected function updateRecord($idPath, $record, $structure, $editors) {
		if (count($editors) > 0) {
			$updateRecord = $this->getUpdateRecord($idPath, $structure, $editors);

			if (!equalRecords($structure, $record, $updateRecord))
				$this->controller->update($idPath->getKeyStack(), $updateRecord);
		}
	}

	protected function removeRecord($idPath) {
		global
			$wgRequest;

		if ($wgRequest->getCheck('remove-'. $idPath->getId())) {
			$this->controller->remove($idPath->getKeyStack());
			return true;
		}
		else
			return false;
	}

	public function getStructure() {
		$attributes = array();

		foreach($this->editors as $editor)
			$attributes[] = $editor->getAttribute();

		return new Structure($attributes);
	}

	public function getUpdateValue($idPath) {
		return null;
	}

	protected function getUpdateStructure() {
		$attributes = array();

		foreach($this->editors as $editor)
			if ($updateAttribute = $editor->getUpdateAttribute())
				$attributes[] = $updateAttribute;

		return new Structure($attributes);
	}

	protected function getAddStructure() {
		$attributes = array();

		foreach($this->editors as $editor)
			if ($addAttribute = $editor->getAddAttribute())
				$attributes[] = $addAttribute;

		return new Structure($attributes);
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

	public function getAddRecord($idPath, $structure, $editors) {
		$result = new ArrayRecord($structure);

		foreach($editors as $editor)
			if ($attribute = $editor->getAddAttribute()) {
				$idPath->pushAttribute($attribute);
				$result->setAttributeValue($attribute, $editor->getAddValue($idPath));
				$idPath->popAttribute();
			}

		return $result;
	}

	public function getUpdateRecord($idPath, $structure, $editors) {
		$result = new ArrayRecord($structure);

		foreach($editors as $editor)
			if ($attribute = $editor->getUpdateAttribute()) {
				$idPath->pushAttribute($attribute);
				$result->setAttributeValue($attribute, $editor->getUpdateValue($idPath));
				$idPath->popAttribute();
			}

		return $result;
	}

	public function save($idPath, $value) {
		if ($this->allowAdd && $this->controller != null) {
			$addStructure = $this->getAddStructure();

			if (count($addStructure->attributes) > 0) {
				$addEditors = $this->getAddEditors();
				$record = $this->getAddRecord($idPath, $addStructure, $addEditors);
				$this->controller->add($idPath->getKeyStack(), $record);
			}
		}

		$recordCount = $value->getRecordCount();
		$key = $value->getKey();
		$updateStructure = $this->getUpdateStructure();
		$updateEditors = $this->getUpdateEditors();

		for ($i = 0; $i < $recordCount; $i++) {
			$record = $value->getRecord($i);
			$idPath->pushKey(project($record, $key));

			if (!$this->allowRemove || !$this->removeRecord($idPath)) {
				$this->saveRecord($idPath, $record);
				$this->updateRecord($idPath, $record, $updateStructure, $updateEditors);
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
			$addStructure = $this->getAddStructure();

			if (count($addStructure->attributes) > 0)
				$result = new Attribute($this->attribute->id, $this->attribute->name, new RecordSetType($addStructure));
		}

		return $result;
	}
}

class RecordSetTableEditor extends RecordSetEditor {
	public function view($idPath, $value) {
		$result = '<table id="'. $idPath->getId() .'" class="wiki-data-table">';
		$structure = $value->getStructure();
		$key = $value->getKey();

		foreach(getStructureAsTableHeaderRows($this->getTableStructure($this)) as $headerRow)
			$result .= '<tr>' . $headerRow . '</tr>';

		$recordCount = $value->getRecordCount();

		for($i = 0; $i < $recordCount; $i++) {
			$record = $value->getRecord($i);
			$idPath->pushKey(project($record, $key));
			$result .= '<tr id="'. $idPath->getId() .'">' . getRecordAsTableCells($idPath, $this, $record) .'</tr>';
			$idPath->popKey();
		}

		$result .= '</table>';

		return $result;
	}

	public function edit($idPath, $value) {
		$result = '<table id="'. $idPath->getId() .'" class="wiki-data-table">';
		$key = $value->getKey();

		$headerRows = getStructureAsTableHeaderRows($this->getTableStructure($this));

		if ($this->allowRemove)
			$headerRows[0] = '<th class="remove" rowspan="' . count($headerRows) . '"><img src="skins/amethyst/delete.png" title="Mark rows to remove" alt="Remove"/></th>' . $headerRows[0];

		if ($this->repeatInput)
			$headerRows[0] .= '<th class="add" rowspan="' . count($headerRows) . '">Input rows</th>';

		foreach ($headerRows as $headerRow)
			$result .= '<tr>' . $headerRow . '</tr>';

		$recordCount = $value->getRecordCount();

		for ($i = 0; $i < $recordCount; $i++) {
			$result .= '<tr>';
			$record = $value->getRecord($i);
			$idPath->pushKey(project($record, $key));

			if ($this->allowRemove) {
				$result .= '<td class="remove">';
				
				if ($this->permissionController->allowRemovalOfValue($idPath, $record))
				 	$result .= getRemoveCheckBox('remove-'. $idPath->getId());
				 	
				$result .= '</td>';
			}
			
			if ($this->permissionController->allowUpdateOfValue($idPath, $record))
				$result .= getRecordAsEditTableCells($record, $idPath, $this);
			else
				$result .= getRecordAsTableCells($idPath, $this, $record);
			
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
			$headerRows = getStructureAsTableHeaderRows($this->getAddStructure());

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

		$result = '<tr id="add-'. $idPath->getId() . '" class="' . $rowClass . '">';

		if ($allowRemove)
			$result .= '<td class="add"><img src="extensions/Wikidata/Images/Add.png" title="Enter new rows to add" alt="Add"/></td>';

		$result .= getStructureAsAddCells($idPath, $this);

		if ($repeatInput)
			$result .= '<td class="input-rows"/>';

		return $result . '</tr>';
	}

	public function getTableStructure($editor) {
		$attributes = array();

		foreach($editor->getEditors() as $childEditor) {
			$childAttribute = $childEditor->getAttribute();

			if (is_a($childEditor, RecordTableCellEditor))
				$type = new RecordType($this->getTableStructure($childEditor));
			else
				$type = 'short-text';

			$attributes[] = new Attribute($childAttribute->id, $childAttribute->name, $type);;
		}

		return new Structure($attributes);
	}
}

abstract class RecordEditor extends DefaultEditor {
	protected function getUpdateStructure() {
		$attributes = array();

		foreach($this->editors as $editor)
			if ($updateAttribute = $editor->getUpdateAttribute())
				$attributes[] = $updateAttribute;

		return new Structure($attributes);
	}

	protected function getAddStructure() {
		$attributes = array();

		foreach($this->editors as $editor)
			if ($addAttribute = $editor->getAddAttribute())
				$attributes[] = $addAttribute;

		return new Structure($attributes);
	}

	public function getUpdateValue($idPath) {
		$result = new ArrayRecord($this->getUpdateStructure());

		foreach($this->editors as $editor)
			if ($attribute = $editor->getUpdateAttribute()) {
				$idPath->pushAttribute($attribute);
				$result->setAttributeValue($attribute, $editor->getUpdateValue($idPath));
				$idPath->popAttribute();
			}

		return $result;
	}

	public function getAddValue($idPath) {
		$result = new ArrayRecord($this->getAddStructure());

		foreach($this->editors as $editor)
			if ($attribute = $editor->getAddAttribute()) {
				$idPath->pushAttribute($attribute);
				$result->setAttributeValue($attribute, $editor->getAddValue($idPath));
				$idPath->popAttribute();
			}

		return $result;
	}

	public function getUpdateAttribute() {
		$updateStructure = $this->getUpdateStructure();

		if (count($updateStructure->attributes) > 0)
			return new Attribute($this->attribute->id, $this->attribute->name, new RecordType($updateStructure));
		else
			return null;
	}

	public function getAddAttribute() {
		$addStructure = $this->getAddStructure();

		if (count($addStructure->attributes) > 0)
			return new Attribute($this->attribute->id, $this->attribute->name, new RecordType($addStructure));
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

class RecordTableCellEditor extends RecordEditor {
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
	protected $permissionController;
	protected $isAddField;

	public function __construct($attribute, $permissionController, $isAddField) {
		parent::__construct($attribute);

		$this->permissionController = $permissionController;
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
		if ($this->permissionController->allowUpdateOfAttribute($this->attribute))
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

	public abstract function getViewHTML($idPath, $value);
	public abstract function getEditHTML($idPath, $value);
	public abstract function getInputValue($id);

	public function getUpdateValue($idPath) {
		return $this->getInputValue("update-" . $idPath->getId());
	}

	public function getAddValue($idPath) {
		return $this->getInputValue("add-" . $idPath->getId());
	}

	public function view($idPath, $value) {
		return $this->getViewHTML($idPath, $value);
	}

	public function edit($idPath, $value) {
		if ($this->permissionController->allowUpdateOfValue($idPath, $value))
			return $this->getEditHTML($idPath, $value);
		else
			return $this->getViewHTML($idPath, $value);
	}
}

class LanguageEditor extends ScalarEditor {
	public function getViewHTML($idPath, $value) {
			return languageIdAsText($value);
	}

	public function getEditHTML($idPath, $value) {
		return getSuggest($this->updateId($idPath->getId()), "language");
	}
	
	public function add($idPath) {
		return getSuggest($this->addId($idPath->getId()), "language");
	}

	public function getInputValue($id) {
		global
			$wgRequest;

		return $wgRequest->getInt($id);
	}
}

class SpellingEditor extends ScalarEditor {
	public function getViewHTML($idPath, $value) {
		return spellingAsLink($value);
	}

	public function getEditHTML($idPath, $value) {
			return getTextBox($this->updateId($idPath->getId()));
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

class DefinedMeaningHeaderEditor extends ScalarEditor {
	protected $truncate;
	protected $truncateAt;
	protected $addText = "";

	public function __construct($attribute, $permissionController, $truncate=false, $truncateAt=0) {
		parent::__construct($attribute, $permissionController, false);

		$this->truncate = $truncate;
		$this->truncateAt = $truncateAt;
	}

	public function getViewHTML($idPath, $value) {
		$definition = getDefinedMeaningDefinition($value);
		$definedMeaningAsLink = definedMeaningAsLink($value);
		$escapedDefinition = htmlspecialchars($definition);

		if ($this->truncate || strlen($definition) >= $this->truncateAt)
			$escapedDefinition = '<span title="'. $escapedDefinition .'">'. htmlspecialchars(substr($definition, 0, $this->truncateAt)) . '...</span>';
			
		return $definedMeaningAsLink . ": " . $escapedDefinition;			
	}

	public function getEditHTML($idPath, $value) {
		//Not editable
		return null;
	}

	public function add($idPath) {
		if ($this->isAddField)
			return getTextArea($this->addId($idPath->getId()), "", 3);
		else
			return $this->addText;
	}

	public function getInputValue($id) {
		global
			$wgRequest;

		return trim($wgRequest->getText($id));
	}

	public function setAddText($addText) {
		$this->addText = $addText;
	}
}

class TextEditor extends ScalarEditor {
	protected $truncate;
	protected $truncateAt;
	protected $addText = "";

	public function __construct($attribute, $permissionController, $isAddField, $truncate=false, $truncateAt=0) {
		parent::__construct($attribute, $permissionController, $isAddField);

		$this->truncate = $truncate;
		$this->truncateAt = $truncateAt;
	}

	public function getViewHTML($idPath, $value) {
		$escapedValue = htmlspecialchars($value);

//		global $wgParser, $wgTitle, $wgOut;
//		$parserOutput = $wgParser->parse($value, $wgTitle, $wgOut->mParserOptions, true, true, $wgOut->mRevisionId);

		if (!$this->truncate || strlen($value) < $this->truncateAt)
			return $escapedValue;//$parserOutput->getText();
		else
			return '<span title="'. $escapedValue .'">'. htmlspecialchars(substr($value, 0, $this->truncateAt)) . '...</span>';
	}

	public function getEditHTML($idPath, $value) {
			return getTextArea($this->updateId($idPath->getId()), $value, 3);
	}

	public function add($idPath) {
		if ($this->isAddField)
			return getTextArea($this->addId($idPath->getId()), "", 3);
		else
			return $this->addText;
	}

	public function getInputValue($id) {
		global
			$wgRequest;

		return trim($wgRequest->getText($id));
	}

	public function setAddText($addText) {
		$this->addText = $addText;
	}
}

class ShortTextEditor extends ScalarEditor {
	public function getViewHTML($idPath, $value) {
		return htmlspecialchars($value);
	}

	public function getEditHTML($idPath, $value) {
			return getTextBox($this->updateId($idPath->getId()), $value);
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

	public function __construct($attribute, $permissionController, $isAddField, $defaultValue) {
		parent::__construct($attribute, $permissionController, $isAddField);

		$this->defaultValue = $defaultValue;
	}

	public function getViewHTML($idPath, $value) {
		return booleanAsHTML($value);
	}

	public function getEditHTML($idPath, $value) {
			return getCheckBox($this->updateId($idPath->getId()), $value);
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

	public function getEditHTML($idPath, $value) {
		return getSuggest($this->updateId($idPath->getId()), $this->suggestType()); 
	}

	public function getInputValue($id) {
		global
			$wgRequest;

		return trim($wgRequest->getText($id));
	}
}

class DefinedMeaningReferenceEditor extends SuggestEditor {
	protected function suggestType() {
		return "defined-meaning";
	}

	public function getViewHTML($idPath, $value) {
		global
			$definedMeaningIdAttribute, $definedMeaningLabelAttribute, $definedMeaningDefiningExpressionAttribute;
			
		$definedMeaningId = $value->getAttributeValue($definedMeaningIdAttribute);
		$definedMeaningLabel = $value->getAttributeValue($definedMeaningLabelAttribute);
		$definedMeaningDefiningExpression = $value->getAttributeValue($definedMeaningDefiningExpressionAttribute);
		
		return definedMeaningReferenceAsLink($definedMeaningId, $definedMeaningLabel, $definedMeaningDefiningExpression);
	}
}

class RelationTypeReferenceEditor extends DefinedMeaningReferenceEditor {
	protected function suggestType() {
		return "relation-type";
	}
}

class ClassReferenceEditor extends DefinedMeaningReferenceEditor {
	protected function suggestType() {
		return "class";
	}

//	public function getViewHTML($idPath, $value) {
//		return definedMeaningAsLink($value);
//	}
}

class CollectionReferenceEditor extends DefinedMeaningReferenceEditor {
	protected function suggestType() {
		return "collection";
	}

//	public function getViewHTML($idPath, $value) {
//		return collectionAsLink($value);
//	}
}

class TextAttributeEditor extends DefinedMeaningReferenceEditor {
	protected function suggestType() {
		return "text-attribute";
	}

//	public function getViewHTML($idPath, $value) {
//		return definedMeaningAsLink($value);
//	}
}

class RecordListEditor extends RecordEditor {
	protected $expandedEditors = array();
	protected $headerLevel = 1;

	public function __construct($attribute, $headerLevel) {
		parent::__construct($attribute);
		
		$this->headerLevel = $headerLevel;
	}
	
	public function view($idPath, $value) {
		$result = '<ul class="collapsable-items">';

		foreach ($this->editors as $editor) {
			$attribute = $editor->getAttribute();
			$idPath->pushAttribute($attribute);
			$class = $idPath->getClass();
			$attributeId = $idPath->getId();
			$expansionPrefix = $this->getExpansionPrefix($class, $attributeId);
			$this->setExpansionByEditor($editor, $class);
			$result .= '<li>'.
						'<h'. $this->headerLevel .'><span id="collapse-'. $attributeId .'" class="toggle '. addCollapsablePrefixToClass($class) .'" onclick="toggle(this, event);">' . $expansionPrefix . '</span>&nbsp;' . $attribute->name . '</h'. $this->headerLevel .'>' .
						'<div id="collapsable-'. $attributeId . '" class="expand-' . $class . '">' . $editor->view($idPath, $value->getAttributeValue($attribute)) . '</div>' .
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

			$idPath->pushAttribute($attribute);
			$class = $idPath->getClass();
			$attributeId = $idPath->getId();
			$this->setExpansionByEditor($editor, $class);
			$expansionPrefix = $this->getExpansionPrefix($class, $attributeId);

			$result .= '<li>'.
						'<h'. $this->headerLevel .'><span id="collapse-'. $attributeId .'" class="toggle '. addCollapsablePrefixToClass($class) .'" onclick="toggle(this, event);">' . $expansionPrefix . '</span>&nbsp;' . $attribute->name . '</h'. $this->headerLevel .'>' .
						'<div id="collapsable-'. $attributeId . '" class="expand-' . $class . '">' . $editor->edit($idPath, $value->getAttributeValue($attribute)) . '</div>' .
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
				$attribute = $editor->getAttribute();
				$idPath->pushAttribute($attribute);
				$class = $idPath->getClass();
				$attributeId = $idPath->getId();
				$this->setExpansionByEditor($editor, $class);
				$expansionPrefix = $this->getExpansionPrefix($class, $attributeId);

				$result .= '<li>'.
							'<h'. $this->headerLevel .'><span id="collapse-'. $attributeId .'" class="toggle '. addCollapsablePrefixToClass($class) .'" onclick="toggle(this, event);">' . $expansionPrefix . '</span>&nbsp;' . $attribute->name . '</h'. $this->headerLevel .'>' .
							'<div id="collapsable-'. $attributeId . '" class="expand-' . $class . '">' . $editor->add($idPath) . '</div>' .
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

	public function setExpansionByEditor($editor, $elementType) {
		$this->setExpansion(in_array($editor, $this->expandedEditors), $elementType);
	}
}

class RecordSetListEditor extends RecordSetEditor {
	protected $headerLevel;
	protected $childrenExpanded;
	protected $captionEditor;
	protected $valueEditor;

	public function __construct($attribute, $permissionController, $allowAdd, $allowRemove, $isAddField, $controller, $headerLevel, $childrenExpanded) {
		parent::__construct($attribute, $permissionController, $allowAdd, $allowRemove, $isAddField, $controller);

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
		$recordCount = $value->getRecordCount();
		$key = $value->getKey();
		$captionAttribute = $this->captionEditor->getAttribute();
		$valueAttribute = $this->valueEditor->getAttribute();

		for ($i = 0; $i < $recordCount; $i++) {
			$record = $value->getRecord($i);
			$idPath->pushKey(project($record, $key));
			$recordId = $idPath->getId();
//			$captionClass = $idPath->getClass();
			$captionClass = $idPath->getClass() . "-record";
			$captionExpansionPrefix = $this->getExpansionPrefix($captionClass, $recordId);
			$this->setExpansion($this->childrenExpanded, $captionClass);
//			$valueClass = $idPath->getClass();
			$valueClass = $idPath->getClass() . "-record";
			$this->setExpansion($this->childrenExpanded, $valueClass);

			$idPath->pushAttribute($captionAttribute);
			$result .= '<li>'.
						'<h' . $this->headerLevel .'><span id="collapse-'. $recordId .'" class="toggle '. addCollapsablePrefixToClass($captionClass) .'" onclick="toggle(this, event);">' . $captionExpansionPrefix . '&nbsp;' . $this->captionEditor->view($idPath, $record->getAttributeValue($captionAttribute)) . '</span></h' . $this->headerLevel .'>';
			$idPath->popAttribute();

			$idPath->pushAttribute($valueAttribute);
			$result .= '<div id="collapsable-'. $recordId . '" class="expand-' . $valueClass . '">' . $this->valueEditor->view($idPath, $record->getAttributeValue($valueAttribute)) . '</div>' .
						'</li>';
			$idPath->popAttribute();

			$idPath->popKey();
		}

		$result .= '</ul>';

		return $result;
	}

	public function edit($idPath, $value) {
		$result = '<ul class="collapsable-items">';
		$recordCount = $value->getRecordCount();
		$key = $value->getKey();
		$captionAttribute = $this->captionEditor->getAttribute();
		$valueAttribute = $this->valueEditor->getAttribute();

		for ($i = 0; $i < $recordCount; $i++) {
			$record = $value->getRecord($i);
			$idPath->pushKey(project($record, $key));

			$recordId = $idPath->getId();
			$captionClass = $idPath->getClass();
			$captionExpansionPrefix = $this->getExpansionPrefix($captionClass, $recordId);
			$this->setExpansion($this->childrenExpanded, $captionClass);
			$valueClass = $idPath->getClass();
			$this->setExpansion($this->childrenExpanded, $valueClass);

			$idPath->pushAttribute($captionAttribute);
			$result .= '<li>'.
						'<h' . $this->headerLevel .'><span id="collapse-'. $recordId .'" class="toggle '. addCollapsablePrefixToClass($captionClass) .'" onclick="toggle(this, event);">' . $captionExpansionPrefix . '&nbsp;' . $this->captionEditor->edit($idPath, $record->getAttributeValue($captionAttribute)) . '</span></h' . $this->headerLevel .'>';
			$idPath->popAttribute();

			$idPath->pushAttribute($valueAttribute);
			$result .= '<div id="collapsable-'. $recordId . '" class="expand-' . $valueClass . '">' . $this->valueEditor->edit($idPath, $record->getAttributeValue($valueAttribute)) . '</div>' .
						'</li>';
			$idPath->popAttribute();

			$idPath->popKey();
		}

		if ($this->allowAdd) {
			$recordId = 'add-' . $idPath->getId();
			$idPath->pushAttribute($captionAttribute);
			$class = $idPath->getClass();

			$this->setExpansion(true, $class);

			$result .= '<li>'.
						'<h' . $this->headerLevel . '><span id="collapse-'. $recordId .'" class="toggle '. addCollapsablePrefixToClass($class) .'" onclick="toggle(this, event);"' . $this->getExpansionPrefix($idPath->getClass(), $idPath->getId()) . ' <img src="extensions/Wikidata/Images/Add.png" title="Enter new list item to add" alt="Add"/> ' . $this->captionEditor->add($idPath) . '</h' . $this->headerLevel .'>';
			$idPath->popAttribute();

			$idPath->pushAttribute($valueAttribute);
			$result .= '<div id="collapsable-'. $recordId . '" class="expand-' . $class . '">' . $this->valueEditor->add($idPath) . '</div>' .
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

		$recordId = 'add-' . $idPath->getId();

		$idPath->pushAttribute($captionAttribute);
		$class = $idPath->getClass();

		$this->setExpansion(true, $class);

		$result .= '<li>'.
					'<h' . $this->headerLevel .'><span id="collapse-'. $recordId .'" class="toggle '. addCollapsablePrefixToClass($class) .'" onclick="toggle(this, event);">' . $this->getExpansionPrefix($idPath->getClass(), $idPath->getId()) . '</span>&nbsp;' . $this->captionEditor->add($idPath) . '</h' . $this->headerLevel .'>';
		$idPath->popAttribute();

		$idPath->pushAttribute($valueAttribute);
		$result .= '<div id="collapsable-'. $recordId . '" class="expand-' . $class . '">' . $this->valueEditor->add($idPath) . '</div>' .
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

class RecordSpanEditor extends RecordEditor {
	protected $attributeSeparator;
	protected $valueSeparator;

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

class UserEditor extends ScalarEditor {
	public function getViewHTML($idPath, $value) {
		global
			$wgUser;
			
		if ($value != "")	
			return $wgUser->getSkin()->makeLink("User:".$value, $value);
		else
			return "";
	}
	
	public function getEditHTML($idPath, $value) {
		return $this->getViewHTML($idPath, $value);
	}

	public function getInputValue($id) {
	}
	
	public function add($idPath) {
	}
}

?>
