<?php

require_once('Editor.php');
require_once('OmegaWikiAttributes.php');
require_once('WikiDataBootstrappedMeanings.php');
require_once('Fetcher.php');
require_once('WikiDataGlobals.php');
require_once('GotoSourceTemplate.php');

function initializeObjectAttributeEditors($filterLanguageId, $showRecordLifeSpan) {
	$showAuthority=false;
	global
		$objectAttributesAttribute, $definedMeaningAttributesAttribute,
		$definedMeaningObjectAttributesEditor, $definedMeaningIdAttribute,
		$definitionObjectAttributesEditor, $definedMeaningIdAttribute,
		$synonymsAndTranslationsObjectAttributesEditor, $syntransIdAttribute,
		$relationsObjectAttributesEditor, $relationIdAttribute,
		$possiblySynonymousObjectAttributesEditor, $possiblySynonymousIdAttribute,
		$textValueObjectAttributesEditor, $textAttributeIdAttribute,
		$urlValueObjectAttributesEditor, $urlAttributeIdAttribute,
		$translatedTextValueObjectAttributesEditor, $translatedTextAttributeIdAttribute,
		$optionValueObjectAttributesEditor, $optionAttributeIdAttribute,
		$definedMeaningMeaningName, $definitionMeaningName,
		$relationMeaningName, $synTransMeaningName,
		$annotationMeaningName;
		
	$definedMeaningObjectAttributesEditor =	new RecordUnorderedListEditor($definedMeaningAttributesAttribute, 5);
	$definitionObjectAttributesEditor =	new RecordUnorderedListEditor($objectAttributesAttribute, 5); 
	$synonymsAndTranslationsObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$possiblySynonymousObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$relationsObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$textValueObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$urlValueObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$translatedTextValueObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$optionValueObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	
	setObjectAttributesEditor($definedMeaningObjectAttributesEditor, $filterLanguageId, $showRecordLifeSpan, $showAuthority, new ObjectIdFetcher(0, $definedMeaningIdAttribute), $definedMeaningMeaningName, new ObjectIdFetcher(0, $definedMeaningIdAttribute));
	setObjectAttributesEditor($definitionObjectAttributesEditor, $filterLanguageId, $showRecordLifeSpan, $showAuthority, new DefinitionObjectIdFetcher(0, $definedMeaningIdAttribute), $definitionMeaningName, new ObjectIdFetcher(0, $definedMeaningIdAttribute));
	setObjectAttributesEditor($synonymsAndTranslationsObjectAttributesEditor, $filterLanguageId, $showRecordLifeSpan, $showAuthority, new ObjectIdFetcher(0, $syntransIdAttribute), $synTransMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($possiblySynonymousObjectAttributesEditor, $filterLanguageId, $showRecordLifeSpan, $showAuthority, new ObjectIdFetcher(0, $possiblySynonymousIdAttribute), $relationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($relationsObjectAttributesEditor, $filterLanguageId, $showRecordLifeSpan, $showAuthority, new ObjectIdFetcher(0, $relationIdAttribute), $relationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($textValueObjectAttributesEditor, $filterLanguageId, $showRecordLifeSpan, $showAuthority, new ObjectIdFetcher(0, $textAttributeIdAttribute), $annotationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($urlValueObjectAttributesEditor, $filterLanguageId, $showRecordLifeSpan, $showAuthority, new ObjectIdFetcher(0, $textAttributeIdAttribute), $annotationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($translatedTextValueObjectAttributesEditor, $filterLanguageId, $showRecordLifeSpan, $showAuthority, new ObjectIdFetcher(0, $translatedTextAttributeIdAttribute), $annotationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($optionValueObjectAttributesEditor, $filterLanguageId, $showRecordLifeSpan, $showAuthority, new ObjectIdFetcher(0, $optionAttributeIdAttribute), $annotationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
}

function getTransactionEditor($attribute) {
	global
		$userAttribute, $timestampAttribute;

	$transactionEditor = new RecordTableCellEditor($attribute);
	$transactionEditor->addEditor(createUserViewer($userAttribute));
	$transactionEditor->addEditor(new TimestampEditor($timestampAttribute, new SimplePermissionController(false), true));

	return $transactionEditor;
}

function createTableLifeSpanEditor($attribute) {
	global
		$addTransactionAttribute, $removeTransactionAttribute;
	
	$result = new RecordTableCellEditor($attribute);
	$result->addEditor(getTransactionEditor($addTransactionAttribute));
	$result->addEditor(getTransactionEditor($removeTransactionAttribute));
	
	return $result;
}

function addTableLifeSpanEditor($editor, $showRecordLifeSpan) {
	global
		$recordLifeSpanAttribute, $addTransactionAttribute, $removeTransactionAttribute, $wgRequest;

	if ($wgRequest->getText('action') == 'history' && $showRecordLifeSpan) 
		$editor->addEditor(createTableLifeSpanEditor($recordLifeSpanAttribute));
}

function addTableAuthorityEditor($editor, $showAuthority) {
	global
		$authorityAttribute;
	
	if ($showAuthority)
		$editor->addEditor(createShortTextViewer($authorityAttribute));
} 

function addTableMetadataEditors($editor, $showRecordLifeSpan) {
	addTableLifeSpanEditor($editor, $showRecordLifeSpan);
}

function getDefinitionEditor($filterLanguageId, $showRecordLifeSpan, $showAuthority) {
	global
		$definitionAttribute, $translatedTextAttribute, $definitionObjectAttributesEditor, $wgPopupAnnotationName;

	if ($filterLanguageId == 0)
		$controller = new DefinedMeaningDefinitionController();
	else
		$controller = new DefinedMeaningFilteredDefinitionController($filterLanguageId);

	$editor = new RecordDivListEditor($definitionAttribute);
	$editor->addEditor(getTranslatedTextEditor($translatedTextAttribute, $controller, $filterLanguageId, $showRecordLifeSpan));
	$editor->addEditor(new PopUpEditor($definitionObjectAttributesEditor, $wgPopupAnnotationName));

	return $editor;	
}	

function getTranslatedTextEditor($attribute, $controller, $filterLanguageId, $showRecordLifeSpan) {
	global
		$languageAttribute, $textAttribute;

	if ($filterLanguageId == 0 || $showRecordLifeSpan) {
		$editor = new RecordSetTableEditor($attribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, true, $controller);
		
		if ($filterLanguageId == 0)
			$editor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));
			
		$editor->addEditor(new TextEditor($textAttribute, new SimplePermissionController(true), true));
		addTableMetadataEditors($editor, $showRecordLifeSpan);
	}
	else 
		$editor = new TextEditor($attribute, new SimplePermissionController(true), true, false, 0, $controller);

	return $editor;
}

function setObjectAttributesEditor($objectAttributesEditor, $filterLanguageId, $showRecordLifeSpan, $showAuthority, $objectIdFetcher, $levelDefinedMeaningName, $dmObjectIdFetcher) {
	$objectAttributesEditor->addEditor(getTextAttributeValuesEditor($showRecordLifeSpan, $showAuthority, new TextAttributeValuesController($objectIdFetcher), $levelDefinedMeaningName, $dmObjectIdFetcher));
	$objectAttributesEditor->addEditor(getTranslatedTextAttributeValuesEditor($filterLanguageId, $showRecordLifeSpan, $showAuthority, new TranslatedTextAttributeValuesController($objectIdFetcher, $filterLanguageId), $levelDefinedMeaningName, $dmObjectIdFetcher));
	$objectAttributesEditor->addEditor(getURLAttributeValuesEditor($showRecordLifeSpan, $showAuthority, new URLAttributeValuesController($objectIdFetcher), $levelDefinedMeaningName, $dmObjectIdFetcher));
	$objectAttributesEditor->addEditor(getOptionAttributeValuesEditor($showRecordLifeSpan, $showAuthority, new OptionAttributeValuesController($objectIdFetcher), $levelDefinedMeaningName, $dmObjectIdFetcher));
}

function getAlternativeDefinitionsEditor($filterLanguageId, $showRecordLifeSpan, $showAuthority) {
	global
		$alternativeDefinitionsAttribute, $alternativeDefinitionAttribute, $sourceAttribute;

	if ($filterLanguageId == 0)
		$alternativeDefinitionController = new DefinedMeaningAlternativeDefinitionController();
	else
		$alternativeDefinitionController = new DefinedMeaningFilteredAlternativeDefinitionController($filterLanguageId);

	$editor = new RecordSetTableEditor($alternativeDefinitionsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningAlternativeDefinitionsController($filterLanguageId));
	$editor->addEditor(getTranslatedTextEditor($alternativeDefinitionAttribute, $alternativeDefinitionController, $filterLanguageId, $showRecordLifeSpan));
	$editor->addEditor(new DefinedMeaningReferenceEditor($sourceAttribute, new SimplePermissionController(false), true));
	
	addTableMetadataEditors($editor, $showRecordLifeSpan);

	return $editor;
}

function getExpressionTableCellEditor($attribute, $filterLanguageId) {
	global
		$languageAttribute, $spellingAttribute;

	if ($filterLanguageId == 0) {
		$editor = new RecordTableCellEditor($attribute);
		$editor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));
		$editor->addEditor(new SpellingEditor($spellingAttribute, new SimplePermissionController(false), true));
	}
	else	
		$editor = new SpellingEditor($attribute, new SimplePermissionController(false), true);
	
	return $editor;
}

function getClassAttributesEditor($showRecordLifeSpan, $showAuthority) {
	global
		$definedMeaningIdAttribute, $classAttributesAttribute, $classAttributeLevelAttribute, $classAttributeAttributeAttribute, $classAttributeTypeAttribute;

	$tableEditor = new RecordSetTableEditor($classAttributesAttribute, new SimplePermissionController(true), new ShowEditFieldForClassesChecker(0, $definedMeaningIdAttribute), new AllowAddController(true), true, false, new ClassAttributesController());
	$tableEditor->addEditor(new ClassAttributesLevelDefinedMeaningEditor($classAttributeLevelAttribute, new SimplePermissionController(false), true));
	$tableEditor->addEditor(new DefinedMeaningReferenceEditor($classAttributeAttributeAttribute, new SimplePermissionController(false), true));
	$tableEditor->addEditor(new ClassAttributesTypeEditor($classAttributeTypeAttribute, new SimplePermissionController(false), true));
	$tableEditor->addEditor(new PopupEditor(getOptionAttributeOptionsEditor(), 'Options'));

	addTableMetadataEditors($tableEditor, $showRecordLifeSpan);
	
	return $tableEditor;
}

function getSynonymsAndTranslationsEditor($filterLanguageId, $showRecordLifeSpan, $showAuthority) {
	global
		$synonymsAndTranslationsAttribute, $identicalMeaningAttribute, $expressionIdAttribute, 
		$expressionAttribute, $synonymsAndTranslationsObjectAttributesEditor, $wgPopupAnnotationName;

	$tableEditor = new RecordSetTableEditor($synonymsAndTranslationsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new SynonymTranslationController($filterLanguageId));
	$tableEditor->addEditor(getExpressionTableCellEditor($expressionAttribute, $filterLanguageId));
	$tableEditor->addEditor(new BooleanEditor($identicalMeaningAttribute, new SimplePermissionController(true), true, true));
	$tableEditor->addEditor(new PopUpEditor($synonymsAndTranslationsObjectAttributesEditor, $wgPopupAnnotationName));

	addTableMetadataEditors($tableEditor, $showRecordLifeSpan);

	return $tableEditor;
}

function getDefinedMeaningRelationsEditor($showRecordLifeSpan, $showAuthority) {
	global
		$relationsAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute,
		$relationsObjectAttributesEditor, $wgPopupAnnotationName;

	$editor = new RecordSetTableEditor($relationsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningRelationController());
	$editor->addEditor(new RelationTypeReferenceEditor($relationTypeAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new DefinedMeaningReferenceEditor($otherDefinedMeaningAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new PopUpEditor($relationsObjectAttributesEditor, $wgPopupAnnotationName));

	addTableMetadataEditors($editor, $showRecordLifeSpan);

	return $editor;
}

function getDefinedMeaningReciprocalRelationsEditor($showRecordLifeSpan, $showAuthority) {
	global
		$reciprocalRelationsAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute,
		$relationsObjectAttributesEditor, $wgPopupAnnotationName;

	$editor = new RecordSetTableEditor($reciprocalRelationsAttribute, new SimplePermissionController(false), new ShowEditFieldChecker(true), new AllowAddController(false), false, false, null);
	$editor->addEditor(new DefinedMeaningReferenceEditor($otherDefinedMeaningAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new RelationTypeReferenceEditor($relationTypeAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new PopUpEditor($relationsObjectAttributesEditor, $wgPopupAnnotationName));

	addTableMetadataEditors($editor, $showRecordLifeSpan);

	return $editor;
}

function getDefinedMeaningClassMembershipEditor($showRecordLifeSpan, $showAuthority) {
	global
		$classMembershipAttribute, $classAttribute;

	$editor = new RecordSetTableEditor($classMembershipAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningClassMembershipController());
	$editor->addEditor(new ClassReferenceEditor($classAttribute, new SimplePermissionController(false), true));

	addTableMetadataEditors($editor, $showRecordLifeSpan);

	return $editor;
}

function getGroupedRelationTypeEditor($groupedRelationsAttribute, $groupedRelationIdAttribute, $otherDefinedMeaningAttribute, $relationTypeId, $showRecordLifeSpan, $showAuthority, $objectAttributesEditor) {
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

	addTableMetadataEditors($editor, $showRecordLifeSpan);

	return $editor;
}

function getDefinedMeaningCollectionMembershipEditor($showRecordLifeSpan, $showAuthority) {
	global
		$collectionMembershipAttribute, $collectionMeaningAttribute, $sourceIdentifierAttribute, 
		$gotoSourceAttribute, $wgGotoSourceTemplates;

	$editor = new RecordSetTableEditor($collectionMembershipAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningCollectionController());
	$editor->addEditor(new CollectionReferenceEditor($collectionMeaningAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new ShortTextEditor($sourceIdentifierAttribute, new SimplePermissionController(true), true));
	
	if (count($wgGotoSourceTemplates) > 0)
		$editor->addEditor(new GotoSourceEditor($gotoSourceAttribute, new SimplePermissionController(true), true));

	addTableMetadataEditors($editor, $showRecordLifeSpan);

	return $editor;
}

function getTextAttributeValuesEditor($showRecordLifeSpan, $showAuthority, $controller, $levelDefinedMeaningName, $objectIdFetcher) {
	global
		$textAttributeAttribute, $textAttribute, $textAttributeValuesAttribute, $textValueObjectAttributesEditor,
		$wgPopupAnnotationName;

	$editor = new RecordSetTableEditor($textAttributeValuesAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);
	$editor->addEditor(new TextAttributeEditor($textAttributeAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(new TextEditor($textAttribute, new SimplePermissionController(true), true));
	$editor->addEditor(new PopUpEditor($textValueObjectAttributesEditor, $wgPopupAnnotationName));

	addTableMetadataEditors($editor, $showRecordLifeSpan);

	return $editor;
}

function getURLAttributeValuesEditor($showRecordLifeSpan, $showAuthority, $controller, $levelDefinedMeaningName, $objectIdFetcher) {
	global
		$urlAttributeAttribute, $urlAttribute, $urlAttributeValuesAttribute, $urlValueObjectAttributesEditor, 
		$wgPopupAnnotationName;

	$editor = new RecordSetTableEditor($urlAttributeValuesAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);
	$editor->addEditor(new URLAttributeEditor($urlAttributeAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(new URLEditor($urlAttribute, new SimplePermissionController(true), true));
	$editor->addEditor(new PopUpEditor($urlValueObjectAttributesEditor, $wgPopupAnnotationName));

	addTableMetadataEditors($editor, $showRecordLifeSpan);

	return $editor;
}

function getTranslatedTextAttributeValuesEditor($filterLanguageId, $showRecordLifeSpan, $showAuthority, $controller, $levelDefinedMeaningName, $objectIdFetcher) {
	global
		$translatedTextAttributeAttribute, $translatedTextValueAttribute, $translatedTextAttributeValuesAttribute, 
		$translatedTextValueObjectAttributesEditor, $wgPopupAnnotationName;

	if ($filterLanguageId == 0)
		$translatedTextAttributeValueController = new TranslatedTextAttributeValueController();
	else
		$translatedTextAttributeValueController = new FilteredTranslatedTextAttributeValueController($filterLanguageId); 

	$editor = new RecordSetTableEditor($translatedTextAttributeValuesAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);
	$editor->addEditor(new TranslatedTextAttributeEditor($translatedTextAttributeAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(getTranslatedTextEditor($translatedTextValueAttribute, $translatedTextAttributeValueController, $filterLanguageId, $showRecordLifeSpan));
	$editor->addEditor(new PopUpEditor($translatedTextValueObjectAttributesEditor, $wgPopupAnnotationName));

	addTableMetadataEditors($editor, $showRecordLifeSpan);

	return $editor;
}

function getOptionAttributeValuesEditor($showRecordLifeSpan, $showAuthority, $controller, $levelDefinedMeaningName, $objectIdFetcher) {
	global
		$optionAttributeAttribute, $optionAttributeOptionAttribute, $optionAttributeValuesAttribute, 
		$optionValueObjectAttributesEditor, $wgPopupAnnotationName;

	$editor = new RecordSetTableEditor($optionAttributeValuesAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);

	$editor->addEditor(new OptionAttributeEditor($optionAttributeAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(new OptionSelectEditor($optionAttributeOptionAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new PopUpEditor($optionValueObjectAttributesEditor, $wgPopupAnnotationName));

	addTableMetadataEditors($editor, $showRecordLifeSpan);

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

function getExpressionMeaningsEditor($attribute, $allowAdd, $filterLanguageId, $possiblySynonymousRelationTypeId, $showRecordLifeSpan, $showAuthority) {
	global
		$definedMeaningIdAttribute;
	
	$definedMeaningEditor = getDefinedMeaningEditor($filterLanguageId, $possiblySynonymousRelationTypeId, $showRecordLifeSpan, $showAuthority);

	$definedMeaningCaptionEditor = new DefinedMeaningHeaderEditor($definedMeaningIdAttribute, new SimplePermissionController(false), true, 75);
	$definedMeaningCaptionEditor->setAddText("New exact meaning");

	$expressionMeaningsEditor = new RecordSetListEditor($attribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController($allowAdd), false, $allowAdd, new ExpressionMeaningController($filterLanguageId), 3, false);
	$expressionMeaningsEditor->setCaptionEditor($definedMeaningCaptionEditor);
	$expressionMeaningsEditor->setValueEditor($definedMeaningEditor);
	
	return $expressionMeaningsEditor;
}

function getExpressionsEditor($spelling, $filterLanguageId, $possiblySynonymousRelationTypeId, $showRecordLifeSpan, $showAuthority) {
	global
		$expressionMeaningsAttribute, $expressionExactMeaningsAttribute, $expressionApproximateMeaningsAttribute, $expressionAttribute, $languageAttribute, $expressionsAttribute;

	$expressionMeaningsRecordEditor = new RecordUnorderedListEditor($expressionMeaningsAttribute, 3);
	
	$exactMeaningsEditor = getExpressionMeaningsEditor($expressionExactMeaningsAttribute, true, $filterLanguageId, $possiblySynonymousRelationTypeId, $showRecordLifeSpan, $showAuthority);
	$expressionMeaningsRecordEditor->addEditor($exactMeaningsEditor);
	$expressionMeaningsRecordEditor->addEditor(getExpressionMeaningsEditor($expressionApproximateMeaningsAttribute, false, $filterLanguageId, $possiblySynonymousRelationTypeId, $showRecordLifeSpan, $showAuthority));
	
	$expressionMeaningsRecordEditor->expandEditor($exactMeaningsEditor);
	
	if ($filterLanguageId == 0) {
		$expressionEditor = new RecordSpanEditor($expressionAttribute, ': ', ' - ');
		$expressionEditor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));

		$expressionsEditor = new RecordSetListEditor($expressionsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), false, false, new ExpressionController($spelling, $filterLanguageId), 2, true);
		$expressionsEditor->setCaptionEditor($expressionEditor);
		$expressionsEditor->setValueEditor($expressionMeaningsRecordEditor);
	}
	else {
		$expressionEditor = new RecordSubRecordEditor($expressionAttribute);
		$expressionEditor->setSubRecordEditor($expressionMeaningsRecordEditor);
		
		$expressionsEditor = new RecordSetFirstRecordEditor($expressionsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), false, false, new ExpressionController($spelling, $filterLanguageId));
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

function getDefinedMeaningEditor($filterLanguageId, $possiblySynonymousRelationTypeId, $showRecordLifeSpan, $showAuthority) {
	global
		$wdDefinedMeaningAttributesOrder,
		$definedMeaningAttribute, $possiblySynonymousIdAttribute, $possiblySynonymousAttribute, 
		$possibleSynonymAttribute, $definedMeaningObjectAttributesEditor, $possiblySynonymousObjectAttributesEditor;
	
	$definitionEditor = getDefinitionEditor($filterLanguageId, $showRecordLifeSpan, $showAuthority);
	$alternativeDefinitionsEditor = getAlternativeDefinitionsEditor($filterLanguageId, $showRecordLifeSpan, $showAuthority);
	$classAttributesEditor = getClassAttributesEditor($showRecordLifeSpan, $showAuthority);		
	$synonymsAndTranslationsEditor = getSynonymsAndTranslationsEditor($filterLanguageId, $showRecordLifeSpan, $showAuthority);
	$relationsEditor = getDefinedMeaningRelationsEditor($showRecordLifeSpan, $showAuthority);
	$reciprocalRelationsEditor = getDefinedMeaningReciprocalRelationsEditor($showRecordLifeSpan, $showAuthority);
	$classMembershipEditor = getDefinedMeaningClassMembershipEditor($showRecordLifeSpan, $showAuthority);
	$collectionMembershipEditor = getDefinedMeaningCollectionMembershipEditor($showRecordLifeSpan, $showAuthority);
	$possiblySynonymousEditor = getGroupedRelationTypeEditor(
		$possiblySynonymousAttribute, 
		$possiblySynonymousIdAttribute, 
		$possibleSynonymAttribute, 
		$possiblySynonymousRelationTypeId, 
		$showRecordLifeSpan, 
		$showAuthority, 
		$possiblySynonymousObjectAttributesEditor
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
	$availableEditors->addEditor($definedMeaningObjectAttributesEditor);

	if ($possiblySynonymousRelationTypeId != 0)
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
	return new RecordSetTableEditor($attribute, new SimplePermissionController(false), new ShowEditFieldChecker(true), new AllowAddController(false), false, false, null);
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

