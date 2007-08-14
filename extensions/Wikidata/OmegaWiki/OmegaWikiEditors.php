<?php

require_once('Editor.php');
require_once('OmegaWikiAttributes.php');
require_once('WikiDataBootstrappedMeanings.php');
require_once('Fetcher.php');
require_once('WikiDataGlobals.php');
require_once('GotoSourceTemplate.php');
require_once('ViewInformation.php');

class DummyViewer extends Viewer {
	public function view(IdStack $idPath, $value) {
		return "";	
	}

	public function showsData($value) {
		return true;
	}
}

class ObjectAttributeValuesEditor extends WrappingEditor {
	protected $recordSetTableEditor;
	protected $propertyAttribute;
	protected $valueAttribute;
	
	public function __construct(Attribute $attribute, $propertyCaption, ViewInformation $viewInformation) {
		parent::__construct(new RecordUnorderedListEditor($attribute, 5));
		
		$this->recordSetTableEditor = new RecordSetTableEditor(
			$attribute, 
			new SimplePermissionController(false), 
			new ShowEditFieldChecker(true), 
			new AllowAddController(false), 
			false, 
			false, 
			null
		);
		
		$this->propertyAttribute = new Attribute("property", $propertyCaption, "short-text");
		$this->valueAttribute = new Attribute("value", "Value", "short-text");
		
		foreach ($viewInformation->getPropertyToColumnFilters() as $propertyToColumnFilter) 
			$this->recordSetTableEditor->addEditor(new DummyViewer($propertyToColumnFilter->getAttribute()));
			

		$o=OmegaWikiAttributes::getInstance();
			
		$this->recordSetTableEditor->addEditor(new DummyViewer($o->objectAttributes));
		addTableMetadataEditors($this->recordSetTableEditor, $viewInformation);
	}
	
	protected function attributeInStructure(Attribute $attribute, Structure $structure) {
		$result = false;
		$attributes = $structure->getAttributes();
		$i = 0;
		
		while (!$result && $i < count($attributes)) {
			$result = $attribute->id == $attributes[$i]->id;
			$i++; 
		}
		
		return $result;
	}
	
	protected function attributeInStructures(Attribute $attribute, array &$structures) {
		$result = false;
		$i = 0;
		
		while (!$result && $i < count($structures)) {
			$result = $this->attributeInStructure($attribute, $structures[$i]);
			$i++;
		}
		
		return $result;
	}
	
	protected function getSubStructureForAttribute(Structure $structure, Attribute $attribute) {
		$attributes = $structure->getAttributes();
		$result = null;
		$i = 0;
		
		while ($result == null && $i < count($attributes)) 
			if ($attribute->id == $attributes[$i]->id)
				$result = $attributes[$i]->type;
			else
				$i++;	
		
		return $result;
	}
	
	protected function filterStructuresOnAttribute(array &$structures, Attribute $attribute) {
		$result = array();
		
		foreach ($structures as $structure) {
			$subStructure = $this->getSubStructureForAttribute($structure, $attribute);
			
			if ($subStructure != null)
				$result[] = $subStructure;
		}
		
		return $result;
	}
	
	protected function filterAttributesByStructures(array &$attributes, array &$structures) {
		$result = array();

		foreach ($attributes as $attribute) { 
			if ($attribute->type instanceof Structure) {
				$filteredAttributes = $this->filterAttributesByStructures(
					$attribute->type->getAttributes(),
					$this->filterStructuresOnAttribute($structures, $attribute) 
				);
				
				if (count($filteredAttributes) > 0)
					$result[] = new Attribute($attribute->id, $attribute->name, new Structure($filteredAttributes));
			}
			else if ($this->attributeInStructures($attribute, $structures))
				$result[] = $attribute;
		}
		
		return $result;
	}
	
	public function determineVisibleSuffixAttributes($value) {
		$visibleStructures = array();
		
		foreach ($this->getEditors() as $editor) {
			$visibleStructure = $editor->getTableStructureForView($value->getAttributeValue($editor->getAttribute()));
			
			if (count($visibleStructure->getAttributes()) > 0)
				$visibleStructures[] = $visibleStructure;
		}

		return $this->filterAttributesByStructures(
			$this->recordSetTableEditor->getTableStructure($this->recordSetTableEditor)->getAttributes(), 
			$visibleStructures
		);
	}
	
	public function addEditor(Editor $editor) {
		$this->wrappedEditor->addEditor($editor);
	}
	
	protected function getVisibleStructureForEditor(Editor $editor, array &$suffixAttributes) {
		$leadingAttributes = array();
		$childEditors = $editor->getEditors();
		
		for ($i = 0; $i < 2; $i++)
			$leadingAttributes[] = $childEditors[$i]->getAttribute();
			
		return new Structure(array_merge($leadingAttributes, $suffixAttributes));
	}

	public function view(IdStack $idPath, $value) {
		$visibleSuffixAttributes = $this->determineVisibleSuffixAttributes($value); 
		
		$visibleStructure = new Structure(array_merge(
			array($this->propertyAttribute, $this->valueAttribute),
			$visibleSuffixAttributes
		));
		
		$result = $this->recordSetTableEditor->viewHeader($idPath, $visibleStructure);

		foreach ($this->getEditors() as $editor) {
			$attribute = $editor->getAttribute();
			$idPath->pushAttribute($attribute);
			$result .= $editor->viewRows(
				$idPath, 
				$value->getAttributeValue($attribute),
				$this->getVisibleStructureForEditor($editor, $visibleSuffixAttributes)
			);
			$idPath->popAttribute();
		} 
		
		$result .= $this->recordSetTableEditor->viewFooter($idPath, $visibleStructure);

		return $result;
	}
}

function initializeObjectAttributeEditors(ViewInformation $viewInformation) {

	$o=OmegaWikiAttributes::getInstance();
	global

		$textValueObjectAttributesEditors, 
		$linkValueObjectAttributesEditors, 
		$translatedTextValueObjectAttributesEditors, 
		$optionValueObjectAttributesEditors,  $annotationMeaningName,
		$wgPropertyAttributeName;
		
	$linkValueObjectAttributesEditors = array();
	$textValueObjectAttributesEditors = array();
	$translatedTextValueObjectAttributesEditors = array();
	$optionValueObjectAttributesEditors = array();	
	
	foreach ($viewInformation->getPropertyToColumnFilters() as $propertyToColumnFilter) { 
		$attribute = $propertyToColumnFilter->getAttribute();
		$propertyCaption = $propertyToColumnFilter->getPropertyCaption();
		
		$textValueObjectAttributesEditors[] = new ObjectAttributeValuesEditor($attribute, $propertyCaption, $viewInformation);
		$linkValueObjectAttributesEditors[] = new ObjectAttributeValuesEditor($attribute, $propertyCaption, $viewInformation);
		$translatedTextValueObjectAttributesEditors[] = new ObjectAttributeValuesEditor($attribute, $propertyCaption, $viewInformation);
		$optionValueObjectAttributesEditors[] = new ObjectAttributeValuesEditor($attribute, $propertyCaption, $viewInformation);
	}
	
	$textValueObjectAttributesEditors[] = new ObjectAttributeValuesEditor($o->objectAttributes, $wgPropertyAttributeName, $viewInformation);
	$linkValueObjectAttributesEditors[] = new ObjectAttributeValuesEditor($o->objectAttributes, $wgPropertyAttributeName, $viewInformation);
	$translatedTextValueObjectAttributesEditors[] = new ObjectAttributeValuesEditor($o->objectAttributes, $wgPropertyAttributeName, $viewInformation);
	$optionValueObjectAttributesEditors[] = new ObjectAttributeValuesEditor($o->objectAttributes, $wgPropertyAttributeName, $viewInformation);
	
	foreach ($textValueObjectAttributesEditors as $textValueObjectAttributesEditor)
		addObjectAttributesEditors($textValueObjectAttributesEditor, $viewInformation, new ObjectIdFetcher(0, $o->textAttributeId), $annotationMeaningName, new ObjectIdFetcher(1, $o->definedMeaningId));

	foreach ($linkValueObjectAttributesEditors as $linkValueObjectAttributesEditor)
		addObjectAttributesEditors($linkValueObjectAttributesEditor, $viewInformation, new ObjectIdFetcher(0, $o->linkAttributeId), $annotationMeaningName, new ObjectIdFetcher(1, $o->definedMeaningId));

	foreach ($translatedTextValueObjectAttributesEditors as $translatedTextValueObjectAttributesEditor)
		addObjectAttributesEditors($translatedTextValueObjectAttributesEditor, $viewInformation, new ObjectIdFetcher(0, $o->translatedTextAttributeId), $annotationMeaningName, new ObjectIdFetcher(1, $o->definedMeaningId));
		
	foreach ($optionValueObjectAttributesEditors as $optionValueObjectAttributesEditor)
		addObjectAttributesEditors($optionValueObjectAttributesEditor, $viewInformation, new ObjectIdFetcher(0, $o->optionAttributeId), $annotationMeaningName, new ObjectIdFetcher(1, $o->definedMeaningId));
}

function getTransactionEditor(Attribute $attribute) {

	$o=OmegaWikiAttributes::getInstance();

	$transactionEditor = new RecordTableCellEditor($attribute);
	$transactionEditor->addEditor(createUserViewer($o->user));
	$transactionEditor->addEditor(new TimestampEditor($o->timestamp, new SimplePermissionController(false), true));

	return $transactionEditor;
}

function createTableLifeSpanEditor(Attribute $attribute) {

	$o=OmegaWikiAttributes::getInstance();
	
	$result = new RecordTableCellEditor($attribute);
	$result->addEditor(getTransactionEditor($o->addTransaction));
	$result->addEditor(getTransactionEditor($o->removeTransaction));
	
	return $result;
}

function getTableLifeSpanEditor($showRecordLifeSpan) {

	$o=OmegaWikiAttributes::getInstance();
	global
		   $wgRequest;

	$result = array();
	
	if ($wgRequest->getText('action') == 'history' && $showRecordLifeSpan) 
		$result[] = createTableLifeSpanEditor($o->recordLifeSpan);
		
	return $result;
}

function getTableMetadataEditors(ViewInformation $viewInformation) {
	return getTableLifeSpanEditor($viewInformation->showRecordLifeSpan);
}

function addTableMetadataEditors($editor, ViewInformation $viewInformation) {
	$metadataEditors = getTableMetadataEditors($viewInformation);
	
	foreach ($metadataEditors as $metadataEditor)
		$editor->addEditor($metadataEditor);
}

function getDefinitionEditor(ViewInformation $viewInformation) {

	$o=OmegaWikiAttributes::getInstance();
	global
		  $wgPopupAnnotationName, 
		  $definitionMeaningName, 
		$wgPropertyAttributeName;

	$editor = new RecordDivListEditor($o->definition);
	$editor->addEditor(getTranslatedTextEditor(
		$o->translatedText, 
		new DefinedMeaningDefinitionController(),
		new DefinedMeaningFilteredDefinitionController($viewInformation->filterLanguageId), 
		$viewInformation
	));
	
	foreach ($viewInformation->getPropertyToColumnFilters() as $propertyToColumnFilter) {
		$attribute = $propertyToColumnFilter->getAttribute();
		$propertyCaption = $propertyToColumnFilter->getPropertyCaption();
		$editor->addEditor(new PopUpEditor(
			createDefinitionObjectAttributesEditor($viewInformation, $attribute, $propertyCaption, $o->definedMeaningId, 0, $definitionMeaningName),	
			$attribute->name
		));
	}
		
	$editor->addEditor(new PopUpEditor(
		createDefinitionObjectAttributesEditor($viewInformation, $o->objectAttributes, $wgPropertyAttributeName, $o->definedMeaningId, 0, $definitionMeaningName),	
		$wgPopupAnnotationName
	));

	return $editor;	
}

function createPropertyToColumnFilterEditors(ViewInformation $viewInformation, Attribute $idAttribute, $levelsFromDefinedMeaning, $levelName) {
	$result = array();

	foreach ($viewInformation->getPropertyToColumnFilters() as $propertyToColumnFilter) {
		$attribute = $propertyToColumnFilter->getAttribute();
		$propertyCaption = $propertyToColumnFilter->getPropertyCaption();
		$result[] = createObjectAttributesEditor($viewInformation, $attribute, $propertyCaption, $idAttribute, $levelsFromDefinedMeaning, $levelName);	
	}
	
	return $result;
}

function addPropertyToColumnFilterEditors(Editor $editor, ViewInformation $viewInformation, Attribute $idAttribute, $levelsFromDefinedMeaning, $levelName) {
	foreach (createPropertyToColumnFilterEditors($viewInformation, $idAttribute, $levelsFromDefinedMeaning, $levelName) as $propertyToColumnEditor) {
		$attribute = $propertyToColumnEditor->getAttribute();
		$editor->addEditor(new PopUpEditor($propertyToColumnEditor, $attribute->name));
	}
}	

function getTranslatedTextEditor(Attribute $attribute, UpdateController $updateController, UpdateAttributeController $updateAttributeController, ViewInformation $viewInformation) {
		$o=OmegaWikiAttributes::getInstance();	
	if ($viewInformation->filterLanguageId == 0 || $viewInformation->showRecordLifeSpan) {
		$editor = new RecordSetTableEditor($attribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, true, $updateController);
		
		if ($viewInformation->filterLanguageId == 0)
			$editor->addEditor(new LanguageEditor($o->language, new SimplePermissionController(false), true));
			
		$editor->addEditor(new TextEditor($o->text, new SimplePermissionController(true), true));
		addTableMetadataEditors($editor, $viewInformation);
	}
	else 
		$editor = new TextEditor($attribute, new SimplePermissionController(true), true, false, 0, $updateAttributeController);

	return $editor;
}

function addObjectAttributesEditors(Editor $objectAttributesEditor, ViewInformation $viewInformation, Fetcher $objectIdFetcher, $levelDefinedMeaningName, Fetcher $dmObjectIdFetcher) {
	$objectAttributesEditor->addEditor(getTextAttributeValuesEditor($viewInformation, new TextAttributeValuesController($objectIdFetcher), $levelDefinedMeaningName, $dmObjectIdFetcher));
	$objectAttributesEditor->addEditor(getTranslatedTextAttributeValuesEditor($viewInformation, new TranslatedTextAttributeValuesController($objectIdFetcher, $viewInformation->filterLanguageId), $levelDefinedMeaningName, $dmObjectIdFetcher));
	$objectAttributesEditor->addEditor(getLinkAttributeValuesEditor($viewInformation, new LinkAttributeValuesController($objectIdFetcher), $levelDefinedMeaningName, $dmObjectIdFetcher));
	$objectAttributesEditor->addEditor(getOptionAttributeValuesEditor($viewInformation, new OptionAttributeValuesController($objectIdFetcher), $levelDefinedMeaningName, $dmObjectIdFetcher));
}

function createObjectAttributesEditor(ViewInformation $viewInformation, Attribute $attribute, $propertyCaption, Attribute $idAttribute, $levelsFromDefinedMeaning, $levelName) {

	$o=OmegaWikiAttributes::getInstance();
	
	$result = new ObjectAttributeValuesEditor($attribute, $propertyCaption, $viewInformation); 
	
	addObjectAttributesEditors(
		$result, 
		$viewInformation, 
		new ObjectIdFetcher(0, $idAttribute), 
		$levelName, 
		new ObjectIdFetcher($levelsFromDefinedMeaning, $o->definedMeaningId)
	);
	
	return $result;
}

function createDefinitionObjectAttributesEditor(ViewInformation $viewInformation, Attribute $attribute, $propertyCaption, Attribute $idAttribute, $levelsFromDefinedMeaning, $levelName) {

	$o=OmegaWikiAttributes::getInstance();
	
	$result = new ObjectAttributeValuesEditor($attribute, $propertyCaption, $viewInformation); 
	
	addObjectAttributesEditors(
		$result, 
		$viewInformation, 
		new DefinitionObjectIdFetcher(0, $idAttribute), 
		$levelName, 
		new ObjectIdFetcher($levelsFromDefinedMeaning, $o->definedMeaningId)
	);
	
	return $result;
}

function getAlternativeDefinitionsEditor(ViewInformation $viewInformation) {

	$o=OmegaWikiAttributes::getInstance();

	$editor = new RecordSetTableEditor(
		$o->alternativeDefinitions, 
		new SimplePermissionController(true), 
		new ShowEditFieldChecker(true), 
		new AllowAddController(true), 
		true, 
		false, 
		new DefinedMeaningAlternativeDefinitionsController($viewInformation->filterLanguageId)
	);
	
	$editor->addEditor(getTranslatedTextEditor(
		$o->alternativeDefinition, 
		new DefinedMeaningAlternativeDefinitionController(),
		new DefinedMeaningFilteredAlternativeDefinitionController($viewInformation), 
		$viewInformation)
	);
	$editor->addEditor(new DefinedMeaningReferenceEditor($o->source, new SimplePermissionController(false), true));
	
	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getExpressionTableCellEditor(Attribute $attribute, ViewInformation $viewInformation) {

	$o=OmegaWikiAttributes::getInstance();

	if ($viewInformation->filterLanguageId == 0) {
		$editor = new RecordTableCellEditor($attribute);
		$editor->addEditor(new LanguageEditor($o->language, new SimplePermissionController(false), true));
		$editor->addEditor(new SpellingEditor($o->spelling, new SimplePermissionController(false), true));
	}
	else	
		$editor = new SpellingEditor($attribute, new SimplePermissionController(false), true);
	
	return $editor;
}

function getClassAttributesEditor(ViewInformation $viewInformation) {

	$o=OmegaWikiAttributes::getInstance();

	$tableEditor = new RecordSetTableEditor($o->classAttributes, new SimplePermissionController(true), new ShowEditFieldForClassesChecker(0, $o->definedMeaningId), new AllowAddController(true), true, false, new ClassAttributesController());
	$tableEditor->addEditor(new ClassAttributesLevelDefinedMeaningEditor($o->classAttributeLevel, new SimplePermissionController(false), true));
	$tableEditor->addEditor(new DefinedMeaningReferenceEditor($o->classAttributeAttribute, new SimplePermissionController(false), true));
	$tableEditor->addEditor(new ClassAttributesTypeEditor($o->classAttributeType, new SimplePermissionController(false), true));
	$tableEditor->addEditor(new PopupEditor(getOptionAttributeOptionsEditor(), 'Options'));

	addTableMetadataEditors($tableEditor, $viewInformation);
	
	return $tableEditor;
}

function getSynonymsAndTranslationsEditor(ViewInformation $viewInformation) {

	$o=OmegaWikiAttributes::getInstance();
	global

		 $wgPopupAnnotationName,
		 $synTransMeaningName, 
		$wgPropertyAttributeName;

	$tableEditor = new RecordSetTableEditor(
		$o->synonymsAndTranslations, 
		new SimplePermissionController(true), 
		new ShowEditFieldChecker(true), 
		new AllowAddController(true), 
		true, 
		false, 
		new SynonymTranslationController($viewInformation->filterLanguageId)
	);
	
	$tableEditor->addEditor(getExpressionTableCellEditor($o->expression, $viewInformation));
	$tableEditor->addEditor(new BooleanEditor($o->identicalMeaning, new SimplePermissionController(true), true, true));
	
	addPropertyToColumnFilterEditors($tableEditor, $viewInformation, $o->syntransId, 1, $synTransMeaningName);
	
	$tableEditor->addEditor(new PopUpEditor(
		createObjectAttributesEditor($viewInformation, $o->objectAttributes, $wgPropertyAttributeName, $o->syntransId, 1, $synTransMeaningName), 
		$wgPopupAnnotationName
	));

	addTableMetadataEditors($tableEditor, $viewInformation);

	return $tableEditor;
}

function getDefinedMeaningRelationsEditor(ViewInformation $viewInformation) {

	$o=OmegaWikiAttributes::getInstance();
	global

		$relationsObjectAttributesEditor,  $relationMeaningName, $wgPopupAnnotationName,
		$wgPropertyAttributeName;

	$editor = new RecordSetTableEditor($o->relations, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningRelationController());
	$editor->addEditor(new RelationTypeReferenceEditor($o->relationType, new SimplePermissionController(false), true));
	$editor->addEditor(new DefinedMeaningReferenceEditor($o->otherDefinedMeaning, new SimplePermissionController(false), true));
	
	addPropertyToColumnFilterEditors($editor, $viewInformation, $o->relationId, 1, $relationMeaningName);
	
	$editor->addEditor(new PopUpEditor(
		createObjectAttributesEditor($viewInformation, $o->objectAttributes, $wgPropertyAttributeName, $o->relationId, 1, $relationMeaningName), 
		$wgPopupAnnotationName
	));

	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getDefinedMeaningReciprocalRelationsEditor(ViewInformation $viewInformation) {

	$o=OmegaWikiAttributes::getInstance();
	global

		$relationsObjectAttributesEditor,  $relationMeaningName, $wgPopupAnnotationName,
		$wgPropertyAttributeName;

	$editor = new RecordSetTableEditor($o->reciprocalRelations, new SimplePermissionController(false), new ShowEditFieldChecker(true), new AllowAddController(false), false, false, null);
	$editor->addEditor(new DefinedMeaningReferenceEditor($o->otherDefinedMeaning, new SimplePermissionController(false), true));
	$editor->addEditor(new RelationTypeReferenceEditor($o->relationType, new SimplePermissionController(false), true));
	
	addPropertyToColumnFilterEditors($editor, $viewInformation, $o->relationId, 1, $relationMeaningName);
	
	$editor->addEditor(new PopUpEditor(
		createObjectAttributesEditor($viewInformation, $o->objectAttributes, $wgPropertyAttributeName, $o->relationId, 1, $relationMeaningName), 
		$wgPopupAnnotationName
	));

	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getDefinedMeaningClassMembershipEditor(ViewInformation $viewInformation) {

	$o=OmegaWikiAttributes::getInstance();

	$editor = new RecordSetTableEditor($o->classMembership, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningClassMembershipController());
	$editor->addEditor(new ClassReferenceEditor($o->class, new SimplePermissionController(false), true));

	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getGroupedRelationTypeEditor(Attribute $groupedRelationsAttribute, Attribute $groupedRelationIdAttribute, Attribute $otherDefinedMeaningAttribute, $relationTypeId, ViewInformation $viewInformation, Editor $objectAttributesEditor) {
	global
		$wgPopupAnnotationName;
	
	$editor = new RecordSetTableEditor(
		$groupedRelationsAttribute, 
		new SimplePermissionController(true), 
		new ShowEditFieldChecker(true), 
		new AllowAddController(true), 
		true, 
		false, 
		new GroupedRelationTypeController($relationTypeId, $groupedRelationIdAttribute, $otherDefinedMeaningAttribute)
	);

	$editor->addEditor(new DefinedMeaningReferenceEditor($otherDefinedMeaningAttribute, new SimplePermissionController(false), true));
	
	if ($objectAttributesEditor != null)
		$editor->addEditor(new PopUpEditor($objectAttributesEditor, $wgPopupAnnotationName));

	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getDefinedMeaningCollectionMembershipEditor(ViewInformation $viewInformation) {

	$o=OmegaWikiAttributes::getInstance();
	global

		 $wgGotoSourceTemplates;

	$editor = new RecordSetTableEditor($o->collectionMembership, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningCollectionController());
	$editor->addEditor(new CollectionReferenceEditor($o->collectionMeaning, new SimplePermissionController(false), true));
	$editor->addEditor(new ShortTextEditor($o->sourceIdentifier, new SimplePermissionController(true), true));
	
	if (count($wgGotoSourceTemplates) > 0)
		$editor->addEditor(new GotoSourceEditor($o->gotoSource, new SimplePermissionController(true), true));

	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function addPopupEditors(Editor $editor, array &$columnEditors) {
	foreach ($columnEditors as $columnEditor)
		$editor->addEditor(new PopUpEditor($columnEditor, $columnEditor->getAttribute()->name));
}

function getTextAttributeValuesEditor(ViewInformation $viewInformation, $controller, $levelDefinedMeaningName, Fetcher $objectIdFetcher) {

	$o=OmegaWikiAttributes::getInstance();
	global
		   $textValueObjectAttributesEditors;

	$editor = new RecordSetTableEditor($o->textAttributeValues, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);
	$editor->addEditor(new TextAttributeEditor($o->textAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(new TextEditor($o->text, new SimplePermissionController(true), true));
	
	addPopupEditors($editor, $textValueObjectAttributesEditors);
	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getLinkAttributeValuesEditor(ViewInformation $viewInformation, UpdateController $controller, $levelDefinedMeaningName, Fetcher $objectIdFetcher) {

	$o=OmegaWikiAttributes::getInstance();
	global
		   $linkValueObjectAttributesEditors;

	$editor = new RecordSetTableEditor($o->linkAttributeValues, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);
	$editor->addEditor(new LinkAttributeEditor($o->linkAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	
	if ($viewInformation->viewOrEdit == "view")
		$linkEditor = new LinkEditor($o->link, new SimplePermissionController(true), true);
	else {
		$linkEditor = new RecordTableCellEditor($o->link);
		$linkEditor->addEditor(new ShortTextEditor($o->linkURL, new SimplePermissionController(true), true, "urlFieldChanged(this);"));
		$linkEditor->addEditor(new ShortTextEditor($o->linkLabel, new SimplePermissionController(true), true));
	}	
		
	$editor->addEditor($linkEditor);

	addPopupEditors($editor, $linkValueObjectAttributesEditors);
	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getTranslatedTextAttributeValuesEditor(ViewInformation $viewInformation, UpdateController $controller, $levelDefinedMeaningName, Fetcher $objectIdFetcher) {

	$o=OmegaWikiAttributes::getInstance();
	global

		$translatedTextValueObjectAttributesEditors;

	$editor = new RecordSetTableEditor($o->translatedTextAttributeValues, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);
	$editor->addEditor(new TranslatedTextAttributeEditor($o->translatedTextAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(getTranslatedTextEditor(
		$o->translatedTextValue, 
		new TranslatedTextAttributeValueController(),
		new FilteredTranslatedTextAttributeValueController($viewInformation->filterLanguageId), 
		$viewInformation
	));
	
	addPopupEditors($editor, $translatedTextValueObjectAttributesEditors);
	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getOptionAttributeValuesEditor(ViewInformation $viewInformation, UpdateController $controller, $levelDefinedMeaningName, Fetcher $objectIdFetcher) {

	$o=OmegaWikiAttributes::getInstance();
	global

		$optionValueObjectAttributesEditors;

	$editor = new RecordSetTableEditor($o->optionAttributeValues, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);

	$editor->addEditor(new OptionAttributeEditor($o->optionAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(new OptionSelectEditor($o->optionAttributeOption, new SimplePermissionController(false), true));
	
	addPopupEditors($editor, $optionValueObjectAttributesEditors);
	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getOptionAttributeOptionsEditor() {

	$o=OmegaWikiAttributes::getInstance();
	$o=OmegaWikiAttributes::getInstance();

	$editor = new RecordSetTableEditor($o->optionAttributeOptions, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new OptionAttributeOptionsController());
	$editor->addEditor(new DefinedMeaningReferenceEditor($o->optionAttributeOption, new SimplePermissionController(false), true)); 
	$editor->addEditor(new LanguageEditor($o->language, new SimplePermissionController(false), true));

	return $editor;
}

function getExpressionMeaningsEditor(Attribute $attribute, $allowAdd, ViewInformation $viewInformation) {

	$o=OmegaWikiAttributes::getInstance();
	
	$definedMeaningEditor = getDefinedMeaningEditor($viewInformation);

	$definedMeaningCaptionEditor = new DefinedMeaningHeaderEditor($o->definedMeaningId, new SimplePermissionController(false), true, 75);
	$definedMeaningCaptionEditor->setAddText("New exact meaning");

	$expressionMeaningsEditor = new RecordSetListEditor($attribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController($allowAdd), false, $allowAdd, new ExpressionMeaningController($viewInformation->filterLanguageId), 3, false);
	$expressionMeaningsEditor->setCaptionEditor($definedMeaningCaptionEditor);
	$expressionMeaningsEditor->setValueEditor($definedMeaningEditor);
	
	return $expressionMeaningsEditor;
}

function getExpressionsEditor($spelling, ViewInformation $viewInformation) {

	$o=OmegaWikiAttributes::getInstance();

	$o=OmegaWikiAttributes::getInstance();

	$expressionMeaningsRecordEditor = new RecordUnorderedListEditor($o->expressionMeanings, 3);
	
	$exactMeaningsEditor = getExpressionMeaningsEditor($o->expressionExactMeanings, true, $viewInformation);
	$expressionMeaningsRecordEditor->addEditor($exactMeaningsEditor);
	$expressionMeaningsRecordEditor->addEditor(getExpressionMeaningsEditor($o->expressionApproximateMeanings, false, $viewInformation));
	
	$expressionMeaningsRecordEditor->expandEditor($exactMeaningsEditor);
	
	if ($viewInformation->filterLanguageId == 0) {
		$expressionEditor = new RecordSpanEditor($o->expression, ': ', ' - ');
		$expressionEditor->addEditor(new LanguageEditor($o->language, new SimplePermissionController(false), true));

		$expressionsEditor = new RecordSetListEditor(
			$o->expressions, 
			new SimplePermissionController(true), 
			new ShowEditFieldChecker(true), 
			new AllowAddController(true), 
			false, 
			false, 
			new ExpressionController($spelling, $viewInformation->filterLanguageId), 
			2, 
			true
		);
		$expressionsEditor->setCaptionEditor($expressionEditor);
		$expressionsEditor->setValueEditor($expressionMeaningsRecordEditor);
	}
	else {
		$expressionEditor = new RecordSubRecordEditor($o->expression);
		$expressionEditor->setSubRecordEditor($expressionMeaningsRecordEditor);
		
		$expressionsEditor = new RecordSetFirstRecordEditor(
			$o->expressions, 
			new SimplePermissionController(true), 
			new ShowEditFieldChecker(true), 
			new AllowAddController(true), 
			false, 
			false, 
			new ExpressionController($spelling, $viewInformation->filterLanguageId)
		);
		$expressionsEditor->setRecordEditor($expressionEditor);
	}

	return $expressionsEditor;
}

function getDefinedMeaningEditor(ViewInformation $viewInformation) {

	$o=OmegaWikiAttributes::getInstance();
	global
		$wdDefinedMeaningAttributesOrder,  $definedMeaningMeaningName,

		 $relationMeaningName,  
		$wgPropertyAttributeName;
	
	$definitionEditor = getDefinitionEditor($viewInformation);
	$alternativeDefinitionsEditor = getAlternativeDefinitionsEditor($viewInformation);
	$classAttributesEditor = getClassAttributesEditor($viewInformation);		
	$synonymsAndTranslationsEditor = getSynonymsAndTranslationsEditor($viewInformation);
	$relationsEditor = getDefinedMeaningRelationsEditor($viewInformation);
	$reciprocalRelationsEditor = getDefinedMeaningReciprocalRelationsEditor($viewInformation);
	$classMembershipEditor = getDefinedMeaningClassMembershipEditor($viewInformation);
	$collectionMembershipEditor = getDefinedMeaningCollectionMembershipEditor($viewInformation);
	
	$possiblySynonymousEditor = getGroupedRelationTypeEditor(
		$o->possiblySynonymous, 
		$o->possiblySynonymousId, 
		$o->possibleSynonym, 
		$viewInformation->possiblySynonymousRelationTypeId,
		$viewInformation, 
		createObjectAttributesEditor($viewInformation, $o->objectAttributes, $wgPropertyAttributeName, $o->possiblySynonymousId, 1, $relationMeaningName)
	); 
	
	$availableEditors = new AttributeEditorMap();
	$availableEditors->addEditor($definitionEditor);
	$availableEditors->addEditor($alternativeDefinitionsEditor);
	$availableEditors->addEditor($classAttributesEditor);
	$availableEditors->addEditor($synonymsAndTranslationsEditor);
	$availableEditors->addEditor($relationsEditor);
	$availableEditors->addEditor($reciprocalRelationsEditor);
	$availableEditors->addEditor($classMembershipEditor);
	$availableEditors->addEditor($collectionMembershipEditor);

	foreach (createPropertyToColumnFilterEditors($viewInformation, $o->definedMeaningId, 0, $definedMeaningMeaningName) as $propertyToColumnEditor) 	
		$availableEditors->addEditor($propertyToColumnEditor);
	
	$availableEditors->addEditor(createObjectAttributesEditor($viewInformation, $o->definedMeaningAttributes, $wgPropertyAttributeName, $o->definedMeaningId, 0, $definedMeaningMeaningName));

	if ($viewInformation->possiblySynonymousRelationTypeId != 0)
		$availableEditors->addEditor($possiblySynonymousEditor);

	$definedMeaningEditor = new RecordUnorderedListEditor($o->definedMeaning, 4);
	
	foreach ($wdDefinedMeaningAttributesOrder as $attributeId) {
		$editor = $availableEditors->getEditorForAttributeId($attributeId);
		
		if ($editor != null)
			$definedMeaningEditor->addEditor($editor);
	}

	$definedMeaningEditor->expandEditor($definitionEditor);
	$definedMeaningEditor->expandEditor($synonymsAndTranslationsEditor);
	return $definedMeaningEditor;
}

function createTableViewer($attribute) {
	return new RecordSetTableEditor(
		$attribute, 
		new SimplePermissionController(false), 
		new ShowEditFieldChecker(true), 
		new AllowAddController(false), 
		false, 
		false, 
		null
	);
}

function createLanguageViewer($attribute) {
	return new LanguageEditor($attribute, new SimplePermissionController(false), false);
}

function createLongTextViewer($attribute) {
	$result = new TextEditor($attribute, new SimplePermissionController(false), false);
	
	return $result;
}

function createShortTextViewer($attribute) {
	return new ShortTextEditor($attribute, new SimplePermissionController(false), false);
}

function createLinkViewer($attribute) {
	return new LinkEditor($attribute, new SimplePermissionController(false), false);
}

function createBooleanViewer($attribute) {
	return new BooleanEditor($attribute, new SimplePermissionController(false), false, false);
}

function createDefinedMeaningReferenceViewer($attribute) {
	return new DefinedMeaningReferenceEditor($attribute, new SimplePermissionController(false), false);
}

function createSuggestionsTableViewer($attribute) {
	$result = createTableViewer($attribute);
	$result->setHideEmptyColumns(false);
	$result->setRowHTMLAttributes(array(
		"class" => "suggestion-row",
		"onclick" => "suggestRowClicked(event, this)",
		"onmouseover" => "mouseOverRow(this)",
		"onmouseout" => "mouseOutRow(this)"
	));
	
	return $result;
}

function createUserViewer($attribute) {
	return new UserEditor($attribute, new SimplePermissionController(false), false);
}

function createTranslatedTextViewer($attribute) {
	
	$o=OmegaWikiAttributes::getInstance();

	$result = createTableViewer($attribute);
	$result->addEditor(createLanguageViewer($o->language));
	$result->addEditor(createLongTextViewer($o->text));
	
	return $result;
}

?>
