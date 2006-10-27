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

	public function showsData($value);
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
	
	public function showsData($value) {
		return $value->getRecordCount() > 0;
	}
}

class RecordSetTableEditor extends RecordSetEditor {
	public function view($idPath, $value) {
		$result = '<table id="'. $idPath->getId() .'" class="wiki-data-table">';
		$structure = $value->getStructure();
		$key = $value->getKey();

		foreach(getStructureAsTableHeaderRows($this->getTableStructure($this), 0) as $headerRow)
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
		global
			$wgStylePath;
			
		$result = '<table id="'. $idPath->getId() .'" class="wiki-data-table">';
		$key = $value->getKey();

		if ($this->allowRemove)
			$columnOffset = 1;
		else
			$columnOffset = 0;
			
		$headerRows = getStructureAsTableHeaderRows($this->getTableStructure($this), $columnOffset);

		if ($this->allowRemove)
			$headerRows[0] = '<th class="remove" rowspan="' . count($headerRows) . '"><img src="'.$wgStylePath.'/amethyst/delete.png" title="Mark rows to remove" alt="Remove"/></th>' . $headerRows[0];

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
			$headerRows = getStructureAsTableHeaderRows($this->getAddStructure(), 0);

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
		global
			$wgScriptPath;
		
		if ($repeatInput)
			$rowClass = 'repeat';
		else
			$rowClass = '';

		$result = '<tr id="add-'. $idPath->getId() . '" class="' . $rowClass . '">';

		if ($allowRemove)
			$result .= '<td class="add"><img src="'.$wgScriptPath.'/extensions/Wikidata/Images/Add.png" title="Enter new rows to add" alt="Add"/></td>';

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

			$attributes[] = new Attribute($childAttribute->id, $childAttribute->name, $type);
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
	
	public function showsData($value) {
		return true;
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
	
	public function showsData($value) {
		return true;
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

		if ($this->truncate && strlen($definition) > $this->truncateAt)
			$escapedDefinition = '<span title="'. $escapedDefinition .'">'. htmlspecialchars(substr($definition, 0, $this->truncateAt)) . '...</span>';
			
		return $definedMeaningAsLink . ": " . $escapedDefinition;			
	}

	public function getEditHTML($idPath, $value) {
		return "";
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

		if (!$this->truncate || strlen($value) <= $this->truncateAt)
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
		
		return definedMeaningReferenceAsLink($definedMeaningId, $definedMeaningDefiningExpression, $definedMeaningLabel);
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
}

class CollectionReferenceEditor extends DefinedMeaningReferenceEditor {
	protected function suggestType() {
		return "collection";
	}
}

class TextAttributeEditor extends DefinedMeaningReferenceEditor {
	protected function suggestType() {
		return "text-attribute";
	}
}

class TranslatedTextAttributeEditor extends DefinedMeaningReferenceEditor {
	protected function suggestType() {
		return "translated-text-attribute";
	}
}

class RecordListEditor extends RecordEditor {
	protected $expandedEditors = array();
	protected $headerLevel = 1;
	protected $htmlTag;

	public function __construct($attribute, $headerLevel, $htmlTag) {
		parent::__construct($attribute);
		
		$this->htmlTag = $htmlTag;
		$this->headerLevel = $headerLevel;
	}
	
	public function showsData($value) {
		$index = 0;
		$showsData = false;
		while($index < count($this->editors) && !$showsData) {
			$editor = $this->editors[$index];
			$attribute = $editor->getAttribute();
			$attributeValue = $value->getAttributeValue($attribute);
			$showsData = $editor->showsData($attributeValue);
			$index += 1;			
		}
		return $showsData;
	}
	
	public function view($idPath, $value) {
		foreach ($this->editors as $editor) {
			$attribute = $editor->getAttribute();
			$idPath->pushAttribute($attribute);
			$class = $idPath->getClass();
			$attributeId = $idPath->getId();
			$attributeValue = $value->getAttributeValue($attribute);			

			if ($editor->showsData($attributeValue)) 	
				$result .=	'<' . $this->htmlTag . '>' . 
				           		$this->childHeader($editor, $attribute, $class, $attributeId) .
				           		$this->viewChild($editor, $idPath, $value, $attribute, $class, $attributeId) .
				           	'</' . $this->htmlTag . '>';
			           
			$idPath->popAttribute();			           
		}
		return $result;
	}

	public function edit($idPath, $value) {
		foreach ($this->editors as $editor) {
			$attribute = $editor->getAttribute();
			$idPath->pushAttribute($attribute);
			$class = $idPath->getClass();
			$attributeId = $idPath->getId();

			$result .= 	'<' . $this->htmlTag . '>'.
				        	$this->childHeader($editor, $attribute, $class, $attributeId) .
						    $this->editChild($editor, $idPath, $value,  $attribute, $class, $attributeId) .
					 	'</' . $this->htmlTag . '>';

			$idPath->popAttribute();
		}
		return $result;
	}
	
	public function add($idPath) {
		foreach($this->editors as $editor) {
			if ($attribute = $editor->getAddAttribute()) {
				$idPath->pushAttribute($attribute);
				$class = $idPath->getClass();
				$attributeId = $idPath->getId();

				$result .=	'<' . $this->htmlTag . '>'.
								$this->childHeader($editor, $attribute, $class, $attributeId) .
								$this->addChild($editor, $idPath, $attribute, $class, $attributeId) .
							'</' . $this->htmlTag . '>';

				$editor->add($idPath);
				$idPath->popAttribute();
			}
		}
		return $result;
	}
	
	protected function childHeader($editor, $attribute, $class, $attributeId){
		$expansionPrefix = $this->getExpansionPrefix($class, $attributeId);
		$this->setExpansionByEditor($editor, $class);
		return '<h'. $this->headerLevel .'><span id="collapse-'. $attributeId .'" class="toggle '. addCollapsablePrefixToClass($class) .'" onclick="toggle(this, event);">' . $expansionPrefix . '&nbsp;' . $attribute->name . '</span></h'. $this->headerLevel .'>';
	}
	
	protected function viewChild($editor, $idPath, $value, $attribute, $class, $attributeId){
		return '<div id="collapsable-'. $attributeId . '" class="expand-' . $class . '">' . $editor->view($idPath, $value->getAttributeValue($attribute)) . '</div>';
	}

	protected function editChild($editor, $idPath, $value, $attribute, $class, $attributeId) {
		return '<div id="collapsable-'. $attributeId . '" class="expand-' . $class . '">' . $editor->edit($idPath, $value->getAttributeValue($attribute)) . '</div>';
	}

	protected function addChild($editor, $idPath, $attribute, $class, $attributeId) {
		return '<div id="collapsable-'. $attributeId . '" class="expand-' . $class . '">' . $editor->add($idPath) . '</div>';
	}

	public function expandEditor($editor) {
		$this->expandedEditors[] = $editor;
	}

	public function setExpansionByEditor($editor, $elementType) {
		$this->setExpansion(in_array($editor, $this->expandedEditors), $elementType);
	}
}

class RecordUnorderedListEditor extends RecordListEditor {
	public function __construct($attribute, $headerLevel) {
		parent::__construct($attribute, $headerLevel, "li");
	}
	
	public function view($idPath, $value) {
		return	'<ul class="collapsable-items">' .
					parent::view($idPath, $value) .
				'</ul>';
	}

	public function edit($idPath, $value) {
		return 	'<ul class="collapsable-items">' .
					parent::edit($idPath, $value) .
				'</ul>';
	}

	public function add($idPath) {
		return	'<ul class="collapsable-items">' .
					parent::add($idPath) .
				'</ul>';
	}
}

class RecordDivListEditor extends RecordListEditor {
	public function __construct($attribute) {
		parent::__construct($attribute, 0, "div");
	}

	public function view($idPath, $value) {
		return	'<div class="collapsable-items">' .
					parent::view($idPath, $value) .
				'</div>';
	}

	public function edit($idPath, $value) {
		return 	'<div class="collapsable-items">' .
					parent::edit($idPath, $value) .
				'</div>';
	}

	public function add($idPath) {
		return	'<div class="collapsable-items">' .
					parent::add($idPath) .
				'</div>';
	}
	
	protected function childHeader($editor, $attribute, $class, $attributeId){
		return "";
	}
}

class PopUpRecordEditor extends RecordEditor {
	protected $wrappedEditor;
	public function __construct($wrappedEditor) {
		parent::__construct($wrappedEditor->getAttribute());		
		$this->wrappedEditor = $wrappedEditor;
	}

	public function view($idPath, $value) {
		return 	$this->startToggleCode($idPath->getId()) .
				$this->wrappedEditor->view($idPath, $value) . 
				$this->endToggleCode($idPath->getId());
	}
	
	public function edit($idPath, $value) {
		return 	$this->startToggleCode($idPath->getId()) .
				$this->wrappedEditor->edit($idPath, $value) .
				$this->endToggleCode($idPath->getId());
	}

	public function add($idPath) {
		return 	$this->startToggleCode($idPath->getId()) .
				$this->wrappedEditor->add($idPath) .
				$this->endToggleCode($idPath->getId());
	}
	
	public function save($idPath, $value) {
		$this->wrappedEditor->save($idPath, $value);	
	}
	
	protected function startToggleCode($attributeId) {
		return 	'<span id="attribute-record-editor-toggle-' . $attributeId . '">' .
				'<span id="attribute-record-editor-title-' . $attributeId . '" style="font-weight: bolder; font-size: 90%;">attributes</span>' . 
				'<div id="attribute-toggleable" style="position: absolute; border: 1px solid #000000; display: none; background-color: white; padding: 4px">';
	}

	protected function endToggleCode($attributeId) {
		return 	'</div>' .
			   	'</span>' . 
				'<p><script type="text/javascript">var attributeShowText = "open >>"; var attributeHideText = "<< close"; showAttributeToggle("' . $attributeId . '");</script></p>';
	}
	
	public function showsData($value) {
		return $this->wrappedEditor->showsData($value);
	}
	
	public function expandEditor($editor) {
		$this->wrappedEditor->expandEditor($editor);
	}

	public function setExpansionByEditor($editor, $elementType) {
		$this->wrappedEditor->setExpansionByEditor($editor, $elementType);
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
			$captionClass = $idPath->getClass() . "-record";
			$captionExpansionPrefix = $this->getExpansionPrefix($captionClass, $recordId);
			$this->setExpansion($this->childrenExpanded, $captionClass);
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
		global
			$wgScriptPath;
		
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
						'<h' . $this->headerLevel . '><span id="collapse-'. $recordId .'" class="toggle '. addCollapsablePrefixToClass($class) .'" onclick="toggle(this, event);"' . $this->getExpansionPrefix($idPath->getClass(), $idPath->getId()) . ' <img src="'.$wgScriptPath.'/extensions/Wikidata/Images/Add.png" title="Enter new list item to add" alt="Add"/> ' . $this->captionEditor->add($idPath) . '</h' . $this->headerLevel .'>';
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
					'<h' . $this->headerLevel .'><span id="collapse-'. $recordId .'" class="toggle '. addCollapsablePrefixToClass($class) .'" onclick="toggle(this, event);">' . $this->getExpansionPrefix($idPath->getClass(), $idPath->getId()) . '&nbsp;' . $this->captionEditor->add($idPath) . '</span></h' . $this->headerLevel .'>';
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
	
	public function showsData($value) {
		return true;
	}
}

class RecordSpanEditor extends RecordEditor {
	protected $attributeSeparator;
	protected $valueSeparator;
	protected $showAttributeNames;

	public function __construct($attribute, $valueSeparator, $attributeSeparator, $showAttributeNames = true) {
		parent::__construct($attribute);

		$this->attributeSeparator = $attributeSeparator;
		$this->valueSeparator = $valueSeparator;
		$this->showAttributeNames = $showAttributeNames;
	}

	public function view($idPath, $value) {
		$fields = array();

		foreach($this->editors as $editor) {
			$attribute = $editor->getAttribute();
			$idPath->pushAttribute($attribute);
			$attributeValue = $editor->view($idPath, $value->getAttributeValue($attribute));
			
			if ($this->showAttributeNames)	
				$fields[] = $attribute->name . $this->valueSeparator . $attributeValue;
			else
				$fields[] = $attributeValue; 
				
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

class TimestampEditor extends ScalarEditor {
	public function getViewHTML($idPath, $value) {
		if ($value != "")
			return 
				substr($value, 0, 4) . '-' . substr($value, 4, 2) . '-' . substr($value, 6, 2) . ' ' .
				substr($value, 8, 2) . ':' . substr($value, 10, 2) . ':' . substr($value, 12, 2);
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
