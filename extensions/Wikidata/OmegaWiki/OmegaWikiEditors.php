<?php

require_once('Editor.php');
require_once('OmegaWikiAttributes.php');
require_once('WikiDataBootstrappedMeanings.php');
require_once('Fetcher.php');
require_once('WikiDataGlobals.php');
require_once('GotoSourceTemplate.php');
require_once('ViewInformation.php');

function initializeObjectAttributeEditors(ViewInformation $viewInformation) {
	global
		$objectAttributesAttribute, $definedMeaningIdAttribute,
		$textValueObjectAttributesEditor, $textAttributeIdAttribute,
		$urlValueObjectAttributesEditor, $urlAttributeIdAttribute,
		$translatedTextValueObjectAttributesEditor, $translatedTextAttributeIdAttribute,
		$optionValueObjectAttributesEditor, $optionAttributeIdAttribute, $annotationMeaningName;
		
	
	$textValueObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$urlValueObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$translatedTextValueObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$optionValueObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	
	setObjectAttributesEditor($textValueObjectAttributesEditor, $viewInformation, new ObjectIdFetcher(0, $textAttributeIdAttribute), $annotationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($urlValueObjectAttributesEditor, $viewInformation, new ObjectIdFetcher(0, $urlAttributeIdAttribute), $annotationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($translatedTextValueObjectAttributesEditor, $viewInformation, new ObjectIdFetcher(0, $translatedTextAttributeIdAttribute), $annotationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($optionValueObjectAttributesEditor, $viewInformation, new ObjectIdFetcher(0, $optionAttributeIdAttribute), $annotationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
}

function getTransactionEditor(Attribute $attribute) {
	global
		$userAttribute, $timestampAttribute;

	$transactionEditor = new RecordTableCellEditor($attribute);
	$transactionEditor->addEditor(createUserViewer($userAttribute));
	$transactionEditor->addEditor(new TimestampEditor($timestampAttribute, new SimplePermissionController(false), true));

	return $transactionEditor;
}

function createTableLifeSpanEditor(Attribute $attribute) {
	global
		$addTransactionAttribute, $removeTransactionAttribute;
	
	$result = new RecordTableCellEditor($attribute);
	$result->addEditor(getTransactionEditor($addTransactionAttribute));
	$result->addEditor(getTransactionEditor($removeTransactionAttribute));
	
	return $result;
}

function addTableLifeSpanEditor(Editor $editor, $showRecordLifeSpan) {
	global
		$recordLifeSpanAttribute, $addTransactionAttribute, $removeTransactionAttribute, $wgRequest;

	if ($wgRequest->getText('action') == 'history' && $showRecordLifeSpan) 
		$editor->addEditor(createTableLifeSpanEditor($recordLifeSpanAttribute));
}

function addTableMetadataEditors($editor, ViewInformation $viewInformation) {
	addTableLifeSpanEditor($editor, $viewInformation->showRecordLifeSpan);
}

function getDefinitionEditor(ViewInformation $viewInformation) {
	global
		$definitionAttribute, $translatedTextAttribute, $wgPopupAnnotationName, 
		$objectAttributesAttribute, $definedMeaningIdAttribute, $definitionMeaningName, $objectAttributesAttribute;

	$editor = new RecordDivListEditor($definitionAttribute);
	$editor->addEditor(getTranslatedTextEditor(
		$translatedTextAttribute, 
		new DefinedMeaningDefinitionController(),
		new DefinedMeaningFilteredDefinitionController($viewInformation->filterLanguageId), 
		$viewInformation
	));
	$editor->addEditor(new PopUpEditor(
		createObjectAttributesEditor($viewInformation, $objectAttributesAttribute, $definedMeaningIdAttribute, 0, $definitionMeaningName),	
		$wgPopupAnnotationName
	));

	return $editor;	
}	

function getTranslatedTextEditor(Attribute $attribute, UpdateController $updateController, UpdateAttributeController $updateAttributeController, ViewInformation $viewInformation) {
	global
		$languageAttribute, $textAttribute;

	if ($viewInformation->filterLanguageId == 0 || $viewInformation->showRecordLifeSpan) {
		$editor = new RecordSetTableEditor($attribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, true, $updateController);
		
		if ($viewInformation->filterLanguageId == 0)
			$editor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));
			
		$editor->addEditor(new TextEditor($textAttribute, new SimplePermissionController(true), true));
		addTableMetadataEditors($editor, $viewInformation);
	}
	else 
		$editor = new TextEditor($attribute, new SimplePermissionController(true), true, false, 0, $updateAttributeController);

	return $editor;
}

function setObjectAttributesEditor(Editor $objectAttributesEditor, ViewInformation $viewInformation, Fetcher $objectIdFetcher, $levelDefinedMeaningName, Fetcher $dmObjectIdFetcher) {
	$objectAttributesEditor->addEditor(getTextAttributeValuesEditor($viewInformation, new TextAttributeValuesController($objectIdFetcher), $levelDefinedMeaningName, $dmObjectIdFetcher));
	$objectAttributesEditor->addEditor(getTranslatedTextAttributeValuesEditor($viewInformation, new TranslatedTextAttributeValuesController($objectIdFetcher, $viewInformation->filterLanguageId), $levelDefinedMeaningName, $dmObjectIdFetcher));
	$objectAttributesEditor->addEditor(getURLAttributeValuesEditor($viewInformation, new URLAttributeValuesController($objectIdFetcher), $levelDefinedMeaningName, $dmObjectIdFetcher));
	$objectAttributesEditor->addEditor(getOptionAttributeValuesEditor($viewInformation, new OptionAttributeValuesController($objectIdFetcher), $levelDefinedMeaningName, $dmObjectIdFetcher));
}

function createObjectAttributesEditor(ViewInformation $viewInformation, Attribute $attribute, Attribute $idAttribute, $levelsFromDefinedMeaning, $levelName) {
	global
		$objectAttributesAttribute, $definedMeaningIdAttribute;
	
	$result = new RecordUnorderedListEditor($attribute, 5); 
	
	setObjectAttributesEditor(
		$result, 
		$viewInformation, 
		new ObjectIdFetcher(0, $idAttribute), 
		$levelName, 
		new ObjectIdFetcher($levelsFromDefinedMeaning, $definedMeaningIdAttribute)
	);
	
	return $result;
}

function getAlternativeDefinitionsEditor(ViewInformation $viewInformation) {
	global
		$alternativeDefinitionsAttribute, $alternativeDefinitionAttribute, $sourceAttribute;

	$editor = new RecordSetTableEditor(
		$alternativeDefinitionsAttribute, 
		new SimplePermissionController(true), 
		new ShowEditFieldChecker(true), 
		new AllowAddController(true), 
		true, 
		false, 
		new DefinedMeaningAlternativeDefinitionsController($viewInformation->filterLanguageId)
	);
	
	$editor->addEditor(getTranslatedTextEditor(
		$alternativeDefinitionAttribute, 
		new DefinedMeaningAlternativeDefinitionController(),
		new DefinedMeaningFilteredAlternativeDefinitionController($viewInformation), 
		$viewInformation)
	);
	$editor->addEditor(new DefinedMeaningReferenceEditor($sourceAttribute, new SimplePermissionController(false), true));
	
	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getExpressionTableCellEditor(Attribute $attribute, ViewInformation $viewInformation) {
	global
		$languageAttribute, $spellingAttribute;

	if ($viewInformation->filterLanguageId == 0) {
		$editor = new RecordTableCellEditor($attribute);
		$editor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));
		$editor->addEditor(new SpellingEditor($spellingAttribute, new SimplePermissionController(false), true));
	}
	else	
		$editor = new SpellingEditor($attribute, new SimplePermissionController(false), true);
	
	return $editor;
}

function getClassAttributesEditor(ViewInformation $viewInformation) {
	global
		$definedMeaningIdAttribute, $classAttributesAttribute, $classAttributeLevelAttribute, $classAttributeAttributeAttribute, $classAttributeTypeAttribute;

	$tableEditor = new RecordSetTableEditor($classAttributesAttribute, new SimplePermissionController(true), new ShowEditFieldForClassesChecker(0, $definedMeaningIdAttribute), new AllowAddController(true), true, false, new ClassAttributesController());
	$tableEditor->addEditor(new ClassAttributesLevelDefinedMeaningEditor($classAttributeLevelAttribute, new SimplePermissionController(false), true));
	$tableEditor->addEditor(new DefinedMeaningReferenceEditor($classAttributeAttributeAttribute, new SimplePermissionController(false), true));
	$tableEditor->addEditor(new ClassAttributesTypeEditor($classAttributeTypeAttribute, new SimplePermissionController(false), true));
	$tableEditor->addEditor(new PopupEditor(getOptionAttributeOptionsEditor(), 'Options'));

	addTableMetadataEditors($tableEditor, $viewInformation);
	
	return $tableEditor;
}

function getSynonymsAndTranslationsEditor(ViewInformation $viewInformation) {
	global
		$synonymsAndTranslationsAttribute, $identicalMeaningAttribute, $expressionIdAttribute, 
		$expressionAttribute, $wgPopupAnnotationName,
		$syntransIdAttribute, $synTransMeaningName, $objectAttributesAttribute;

	$tableEditor = new RecordSetTableEditor(
		$synonymsAndTranslationsAttribute, 
		new SimplePermissionController(true), 
		new ShowEditFieldChecker(true), 
		new AllowAddController(true), 
		true, 
		false, 
		new SynonymTranslationController($viewInformation->filterLanguageId)
	);
	
	$tableEditor->addEditor(getExpressionTableCellEditor($expressionAttribute, $viewInformation));
	$tableEditor->addEditor(new BooleanEditor($identicalMeaningAttribute, new SimplePermissionController(true), true, true));
	$tableEditor->addEditor(new PopUpEditor(
		createObjectAttributesEditor($viewInformation, $objectAttributesAttribute, $syntransIdAttribute, 1, $synTransMeaningName), 
		$wgPopupAnnotationName
	));

	addTableMetadataEditors($tableEditor, $viewInformation);

	return $tableEditor;
}

function getDefinedMeaningRelationsEditor(ViewInformation $viewInformation) {
	global
		$relationsAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute, $objectAttributesAttribute,
		$relationsObjectAttributesEditor, $relationIdAttribute, $relationMeaningName, $wgPopupAnnotationName;

	$editor = new RecordSetTableEditor($relationsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningRelationController());
	$editor->addEditor(new RelationTypeReferenceEditor($relationTypeAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new DefinedMeaningReferenceEditor($otherDefinedMeaningAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new PopUpEditor(
		createObjectAttributesEditor($viewInformation, $objectAttributesAttribute, $relationIdAttribute, 1, $relationMeaningName), 
		$wgPopupAnnotationName
	));

	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getDefinedMeaningReciprocalRelationsEditor(ViewInformation $viewInformation) {
	global
		$reciprocalRelationsAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute, $objectAttributesAttribute,
		$relationsObjectAttributesEditor, $relationIdAttribute, $relationMeaningName, $wgPopupAnnotationName;

	$editor = new RecordSetTableEditor($reciprocalRelationsAttribute, new SimplePermissionController(false), new ShowEditFieldChecker(true), new AllowAddController(false), false, false, null);
	$editor->addEditor(new DefinedMeaningReferenceEditor($otherDefinedMeaningAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new RelationTypeReferenceEditor($relationTypeAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new PopUpEditor(
		createObjectAttributesEditor($viewInformation, $objectAttributesAttribute, $relationIdAttribute, 1, $relationMeaningName), 
		$wgPopupAnnotationName
	));

	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getDefinedMeaningClassMembershipEditor(ViewInformation $viewInformation) {
	global
		$classMembershipAttribute, $classAttribute;

	$editor = new RecordSetTableEditor($classMembershipAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningClassMembershipController());
	$editor->addEditor(new ClassReferenceEditor($classAttribute, new SimplePermissionController(false), true));

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
	global
		$collectionMembershipAttribute, $collectionMeaningAttribute, $sourceIdentifierAttribute, 
		$gotoSourceAttribute, $wgGotoSourceTemplates;

	$editor = new RecordSetTableEditor($collectionMembershipAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningCollectionController());
	$editor->addEditor(new CollectionReferenceEditor($collectionMeaningAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new ShortTextEditor($sourceIdentifierAttribute, new SimplePermissionController(true), true));
	
	if (count($wgGotoSourceTemplates) > 0)
		$editor->addEditor(new GotoSourceEditor($gotoSourceAttribute, new SimplePermissionController(true), true));

	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getTextAttributeValuesEditor(ViewInformation $viewInformation, $controller, $levelDefinedMeaningName, Fetcher $objectIdFetcher) {
	global
		$textAttributeAttribute, $textAttribute, $textAttributeValuesAttribute, $textValueObjectAttributesEditor,
		$wgPopupAnnotationName;

	$editor = new RecordSetTableEditor($textAttributeValuesAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);
	$editor->addEditor(new TextAttributeEditor($textAttributeAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(new TextEditor($textAttribute, new SimplePermissionController(true), true));
	$editor->addEditor(new PopUpEditor($textValueObjectAttributesEditor, $wgPopupAnnotationName));

	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getURLAttributeValuesEditor(ViewInformation $viewInformation, UpdateController $controller, $levelDefinedMeaningName, Fetcher $objectIdFetcher) {
	global
		$urlAttributeAttribute, $urlAttribute, $urlAttributeValuesAttribute, $urlValueObjectAttributesEditor, 
		$wgPopupAnnotationName;

	$editor = new RecordSetTableEditor($urlAttributeValuesAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);
	$editor->addEditor(new URLAttributeEditor($urlAttributeAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(new URLEditor($urlAttribute, new SimplePermissionController(true), true));
	$editor->addEditor(new PopUpEditor($urlValueObjectAttributesEditor, $wgPopupAnnotationName));

	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getTranslatedTextAttributeValuesEditor(ViewInformation $viewInformation, UpdateController $controller, $levelDefinedMeaningName, Fetcher $objectIdFetcher) {
	global
		$translatedTextAttributeAttribute, $translatedTextValueAttribute, $translatedTextAttributeValuesAttribute, 
		$translatedTextValueObjectAttributesEditor, $wgPopupAnnotationName;

	$editor = new RecordSetTableEditor($translatedTextAttributeValuesAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);
	$editor->addEditor(new TranslatedTextAttributeEditor($translatedTextAttributeAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(getTranslatedTextEditor(
		$translatedTextValueAttribute, 
		new TranslatedTextAttributeValueController(),
		new FilteredTranslatedTextAttributeValueController($viewInformation->filterLanguageId), 
		$viewInformation
	));
	$editor->addEditor(new PopUpEditor($translatedTextValueObjectAttributesEditor, $wgPopupAnnotationName));

	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getOptionAttributeValuesEditor(ViewInformation $viewInformation, UpdateController $controller, $levelDefinedMeaningName, Fetcher $objectIdFetcher) {
	global
		$optionAttributeAttribute, $optionAttributeOptionAttribute, $optionAttributeValuesAttribute, 
		$optionValueObjectAttributesEditor, $wgPopupAnnotationName;

	$editor = new RecordSetTableEditor($optionAttributeValuesAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);

	$editor->addEditor(new OptionAttributeEditor($optionAttributeAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(new OptionSelectEditor($optionAttributeOptionAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new PopUpEditor($optionValueObjectAttributesEditor, $wgPopupAnnotationName));

	addTableMetadataEditors($editor, $viewInformation);

	return $editor;
}

function getOptionAttributeOptionsEditor() {
	global
		$optionAttributeAttribute, $optionAttributeOptionAttribute, $languageAttribute, $optionAttributeOptionsAttribute;

	$editor = new RecordSetTableEditor($optionAttributeOptionsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new OptionAttributeOptionsController());
	$editor->addEditor(new DefinedMeaningReferenceEditor($optionAttributeOptionAttribute, new SimplePermissionController(false), true)); 
	$editor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));

	return $editor;
}

function getExpressionMeaningsEditor(Attribute $attribute, $allowAdd, ViewInformation $viewInformation) {
	global
		$definedMeaningIdAttribute;
	
	$definedMeaningEditor = getDefinedMeaningEditor($viewInformation);

	$definedMeaningCaptionEditor = new DefinedMeaningHeaderEditor($definedMeaningIdAttribute, new SimplePermissionController(false), true, 75);
	$definedMeaningCaptionEditor->setAddText("New exact meaning");

	$expressionMeaningsEditor = new RecordSetListEditor($attribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController($allowAdd), false, $allowAdd, new ExpressionMeaningController($viewInformation->filterLanguageId), 3, false);
	$expressionMeaningsEditor->setCaptionEditor($definedMeaningCaptionEditor);
	$expressionMeaningsEditor->setValueEditor($definedMeaningEditor);
	
	return $expressionMeaningsEditor;
}

function getExpressionsEditor($spelling, ViewInformation $viewInformation) {
	global
		$expressionMeaningsAttribute, $expressionExactMeaningsAttribute, $expressionApproximateMeaningsAttribute, $expressionAttribute, $languageAttribute, $expressionsAttribute;

	$expressionMeaningsRecordEditor = new RecordUnorderedListEditor($expressionMeaningsAttribute, 3);
	
	$exactMeaningsEditor = getExpressionMeaningsEditor($expressionExactMeaningsAttribute, true, $viewInformation);
	$expressionMeaningsRecordEditor->addEditor($exactMeaningsEditor);
	$expressionMeaningsRecordEditor->addEditor(getExpressionMeaningsEditor($expressionApproximateMeaningsAttribute, false, $viewInformation));
	
	$expressionMeaningsRecordEditor->expandEditor($exactMeaningsEditor);
	
	if ($viewInformation->filterLanguageId == 0) {
		$expressionEditor = new RecordSpanEditor($expressionAttribute, ': ', ' - ');
		$expressionEditor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));

		$expressionsEditor = new RecordSetListEditor(
			$expressionsAttribute, 
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
		$expressionEditor = new RecordSubRecordEditor($expressionAttribute);
		$expressionEditor->setSubRecordEditor($expressionMeaningsRecordEditor);
		
		$expressionsEditor = new RecordSetFirstRecordEditor(
			$expressionsAttribute, 
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

class AttributeEditorMap {
	protected $attributeEditorMap = array();
	
	public function addEditor($editor) {
		$attributeId = $editor->getAttribute()->id;
		$this->attributeEditorMap[$attributeId] = $editor;
	}
	
	public function getEditorForAttributeId($attributeId) {
		# FIXME: check if this actually exists	
		return @$this->attributeEditorMap[$attributeId];
	}
}

function getDefinedMeaningEditor(ViewInformation $viewInformation) {
	global
		$wdDefinedMeaningAttributesOrder, $definedMeaningIdAttribute, $definedMeaningMeaningName,
		$definedMeaningAttribute, $possiblySynonymousIdAttribute, $possiblySynonymousAttribute, 
		$possibleSynonymAttribute, $relationMeaningName, $objectAttributesAttribute, $definedMeaningAttributesAttribute;
	
	$definitionEditor = getDefinitionEditor($viewInformation);
	$alternativeDefinitionsEditor = getAlternativeDefinitionsEditor($viewInformation);
	$classAttributesEditor = getClassAttributesEditor($viewInformation);		
	$synonymsAndTranslationsEditor = getSynonymsAndTranslationsEditor($viewInformation);
	$relationsEditor = getDefinedMeaningRelationsEditor($viewInformation);
	$reciprocalRelationsEditor = getDefinedMeaningReciprocalRelationsEditor($viewInformation);
	$classMembershipEditor = getDefinedMeaningClassMembershipEditor($viewInformation);
	$collectionMembershipEditor = getDefinedMeaningCollectionMembershipEditor($viewInformation);
	
	$possiblySynonymousEditor = getGroupedRelationTypeEditor(
		$possiblySynonymousAttribute, 
		$possiblySynonymousIdAttribute, 
		$possibleSynonymAttribute, 
		$viewInformation->possiblySynonymousRelationTypeId,
		$viewInformation, 
		createObjectAttributesEditor($viewInformation, $objectAttributesAttribute, $possiblySynonymousIdAttribute, 1, $relationMeaningName)
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
	$availableEditors->addEditor(createObjectAttributesEditor($viewInformation, $definedMeaningAttributesAttribute, $definedMeaningIdAttribute, 0, $definedMeaningMeaningName));

	if ($viewInformation->possiblySynonymousRelationTypeId != 0)
		$availableEditors->addEditor($possiblySynonymousEditor);

	$definedMeaningEditor = new RecordUnorderedListEditor($definedMeaningAttribute, 4);
	
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

function createURLViewer($attribute) {
	return new URLEditor($attribute, new SimplePermissionController(false), false);
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
	global
		$languageAttribute, $textAttribute;
	
	$result = createTableViewer($attribute);
	$result->addEditor(createLanguageViewer($languageAttribute));
	$result->addEditor(createLongTextViewer($textAttribute));
	
	return $result;
}

?>