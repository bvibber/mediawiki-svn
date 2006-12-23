<?php

require_once('Editor.php');
require_once('WiktionaryZAttributes.php');
require_once('WikiDataBootstrappedMeanings.php');
require_once('Fetcher.php');

initializeObjectAttributeEditors(true, true);
function initializeObjectAttributeEditors($showRecordLifeSpan, $showAuthority) {
	global
		$objectAttributesAttribute,
		$definedMeaningObjectAttributesEditor, $definedMeaningIdAttribute,
		$definitionObjectAttributesEditor, $definedMeaningIdAttribute,
		$synonymsAndTranslationsObjectAttributesEditor, $syntransIdAttribute,
		$relationsObjectAttributesEditor, $relationIdAttribute,
		$textValueObjectAttributesEditor, $textAttributeIdAttribute,
		$translatedTextValueObjectAttributesEditor, $translatedTextAttributeIdAttribute,
		$optionValueObjectAttributesEditor, $optionAttributeIdAttribute,
		$definedMeaningMeaningName, $definitionMeaningName,
		$relationMeaningName, $synTransMeaningName,
		$annotationMeaningName;
		
	$definedMeaningObjectAttributesEditor =	new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$definitionObjectAttributesEditor =	new RecordUnorderedListEditor($objectAttributesAttribute, 5); 
	$synonymsAndTranslationsObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$relationsObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$textValueObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$translatedTextValueObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$optionValueObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	
	setObjectAttributesEditor($definedMeaningObjectAttributesEditor, $showRecordLifeSpan, $showAuthority, new ObjectIdFetcher(0, $definedMeaningIdAttribute), $definedMeaningMeaningName, new ObjectIdFetcher(0, $definedMeaningIdAttribute));
	setObjectAttributesEditor($definitionObjectAttributesEditor, $showRecordLifeSpan, $showAuthority, new DefinitionObjectIdFetcher(0, $definedMeaningIdAttribute), $definitionMeaningName, new ObjectIdFetcher(0, $definedMeaningIdAttribute));
	setObjectAttributesEditor($synonymsAndTranslationsObjectAttributesEditor, $showRecordLifeSpan, $showAuthority, new ObjectIdFetcher(0, $syntransIdAttribute), $synTransMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($relationsObjectAttributesEditor, $showRecordLifeSpan, $showAuthority, new ObjectIdFetcher(0, $relationIdAttribute), $relationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($textValueObjectAttributesEditor, $showRecordLifeSpan, $showAuthority, new ObjectIdFetcher(0, $textAttributeIdAttribute), $annotationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($translatedTextValueObjectAttributesEditor, $showRecordLifeSpan, $showAuthority, new ObjectIdFetcher(0, $translatedTextAttributeIdAttribute), $annotationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($optionValueObjectAttributesEditor, $showRecordLifeSpan, $showAuthority, new ObjectIdFetcher(0, $optionAttributeIdAttribute), $annotationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
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
		$editor->addEditor(createUserViewer($authorityAttribute));
} 

function addTableMedataEditors($editor, $showRecordLifeSpan, $showAuthority) {
	addTableLifeSpanEditor($editor, $showRecordLifeSpan);
	addTableAuthorityEditor($editor, $showAuthority);
}

function getDefinitionEditor($attribute, $controller, $showRecordLifeSpan, $showAuthority) {
	global
		$translatedTextAttribute, $definitionObjectAttributesEditor;

	$editor = new RecordDivListEditor($attribute);
	$editor->addEditor(getTranslatedTextEditor($translatedTextAttribute, new DefinedMeaningDefinitionController(), $showRecordLifeSpan, $showAuthority));
	$editor->addEditor(new PopUpEditor($definitionObjectAttributesEditor, 'Annotation'));

	return $editor;		
}	

function getTranslatedTextEditor($attribute, $controller, $showRecordLifeSpan, $showAuthority) {
	global
		$languageAttribute, $textAttribute;

	$editor = new RecordSetTableEditor($attribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, true, $controller);
	$editor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new TextEditor($textAttribute, new SimplePermissionController(true), true));

	addTableMedataEditors($editor, $showRecordLifeSpan, $showAuthority);

	return $editor;
}

function setObjectAttributesEditor($objectAttributesEditor, $showRecordLifeSpan, $showAuthority, $objectIdFetcher, $levelDefinedMeaningName, $dmObjectIdFetcher) {
	$objectAttributesEditor->addEditor(getTextAttributeValuesEditor($showRecordLifeSpan, $showAuthority, new TextAttributeValuesController($objectIdFetcher), $levelDefinedMeaningName, $dmObjectIdFetcher));
	$objectAttributesEditor->addEditor(getTranslatedTextAttributeValuesEditor($showRecordLifeSpan, $showAuthority, new TranslatedTextAttributeValuesController($objectIdFetcher), $levelDefinedMeaningName, $dmObjectIdFetcher));
	$objectAttributesEditor->addEditor(getOptionAttributeValuesEditor($showRecordLifeSpan, $showAuthority, new OptionAttributeValuesController($objectIdFetcher), $levelDefinedMeaningName, $dmObjectIdFetcher));
}

function getAlternativeDefinitionsEditor($showRecordLifeSpan, $showAuthority) {
	global
		$alternativeDefinitionsAttribute, $alternativeDefinitionAttribute, $sourceAttribute;

	$editor = new RecordSetTableEditor($alternativeDefinitionsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningAlternativeDefinitionsController());
//		$editor = new RecordSetTableEditor($alternativeDefinitionsAttribute, new AlternativeDefinitionsPermissionController(), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningAlternativeDefinitionsController());
	$editor->addEditor(getTranslatedTextEditor($alternativeDefinitionAttribute, new DefinedMeaningAlternativeDefinitionController(), $showRecordLifeSpan, $showAuthority));
	$editor->addEditor(new DefinedMeaningReferenceEditor($sourceAttribute, new SimplePermissionController(false), true));
	
	addTableMedataEditors($editor, $showRecordLifeSpan, $showAuthority);

	return $editor;
}

function getExpressionTableCellEditor($attribute) {
	global
		$languageAttribute, $spellingAttribute;

	$editor = new RecordTableCellEditor($attribute);
	$editor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new SpellingEditor($spellingAttribute, new SimplePermissionController(false), true));
	
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

	addTableMedataEditors($tableEditor, $showRecordLifeSpan, $showAuthority);
	
	return $tableEditor;
}

function getSynonymsAndTranslationsEditor($showRecordLifeSpan, $showAuthority) {
	global
		$synonymsAndTranslationsAttribute, $identicalMeaningAttribute, $expressionIdAttribute, 
		$expressionAttribute, $synonymsAndTranslationsObjectAttributesEditor;

	$tableEditor = new RecordSetTableEditor($synonymsAndTranslationsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new SynonymTranslationController());
	$tableEditor->addEditor(getExpressionTableCellEditor($expressionAttribute));
	$tableEditor->addEditor(new BooleanEditor($identicalMeaningAttribute, new SimplePermissionController(true), true, true));
	$tableEditor->addEditor(new PopUpEditor($synonymsAndTranslationsObjectAttributesEditor, 'Annotation'));

	addTableMedataEditors($tableEditor, $showRecordLifeSpan, $showAuthority);

	return $tableEditor;
}

function getDefinedMeaningRelationsEditor($showRecordLifeSpan, $showAuthority) {
	global
		$relationsAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute,
		$relationsObjectAttributesEditor;

	$editor = new RecordSetTableEditor($relationsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningRelationController());
	$editor->addEditor(new RelationTypeReferenceEditor($relationTypeAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new DefinedMeaningReferenceEditor($otherDefinedMeaningAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new PopUpEditor($relationsObjectAttributesEditor, 'Annotation'));

	addTableMedataEditors($editor, $showRecordLifeSpan, $showAuthority);

	return $editor;
}

function getDefinedMeaningReciprocalRelationsEditor($showRecordLifeSpan, $showAuthority) {
	global
		$reciprocalRelationsAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute,
		$relationsObjectAttributesEditor;

	$editor = new RecordSetTableEditor($reciprocalRelationsAttribute, new SimplePermissionController(false), new ShowEditFieldChecker(true), new AllowAddController(false), false, false, null);
	$editor->addEditor(new DefinedMeaningReferenceEditor($otherDefinedMeaningAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new RelationTypeReferenceEditor($relationTypeAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new PopUpEditor($relationsObjectAttributesEditor, 'Annotation'));

	addTableMedataEditors($editor, $showRecordLifeSpan, $showAuthority);

	return $editor;
}

function getDefinedMeaningClassMembershipEditor($showRecordLifeSpan, $showAuthority) {
	global
		$classMembershipAttribute, $classAttribute;

	$editor = new RecordSetTableEditor($classMembershipAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningClassMembershipController());
	$editor->addEditor(new ClassReferenceEditor($classAttribute, new SimplePermissionController(false), true));

	addTableMedataEditors($editor, $showRecordLifeSpan, $showAuthority);

	return $editor;
}

function getDefinedMeaningCollectionMembershipEditor($showRecordLifeSpan, $showAuthority) {
	global
		$collectionMembershipAttribute, $collectionMeaningAttribute, $sourceIdentifierAttribute;

	$editor = new RecordSetTableEditor($collectionMembershipAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningCollectionController());
	$editor->addEditor(new CollectionReferenceEditor($collectionMeaningAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new ShortTextEditor($sourceIdentifierAttribute, new SimplePermissionController(true), true));

	addTableMedataEditors($editor, $showRecordLifeSpan, $showAuthority);

	return $editor;
}

function getTextAttributeValuesEditor($showRecordLifeSpan, $showAuthority, $controller, $levelDefinedMeaningName, $objectIdFetcher) {
	global
		$textAttributeAttribute, $textAttribute, $textAttributeValuesAttribute, $textValueObjectAttributesEditor;

	$editor = new RecordSetTableEditor($textAttributeValuesAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);
	$editor->addEditor(new TextAttributeEditor($textAttributeAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(new TextEditor($textAttribute, new SimplePermissionController(true), true));
	$editor->addEditor(new PopUpEditor($textValueObjectAttributesEditor, 'Annotation'));

	addTableMedataEditors($editor, $showRecordLifeSpan, $showAuthority);

	return $editor;
}

function getTranslatedTextAttributeValuesEditor($showRecordLifeSpan, $showAuthority, $controller, $levelDefinedMeaningName, $objectIdFetcher) {
	global
		$translatedTextAttributeAttribute, $translatedTextValueAttribute, $translatedTextAttributeValuesAttribute, $translatedTextValueObjectAttributesEditor;

	$editor = new RecordSetTableEditor($translatedTextAttributeValuesAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);
	$editor->addEditor(new TranslatedTextAttributeEditor($translatedTextAttributeAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(getTranslatedTextEditor($translatedTextValueAttribute, new TranslatedTextAttributeValueController(), $showRecordLifeSpan, $showAuthority));
	$editor->addEditor(new PopUpEditor($translatedTextValueObjectAttributesEditor, 'Annotation'));

	addTableMedataEditors($editor, $showRecordLifeSpan, $showAuthority);

	return $editor;
}

function getOptionAttributeValuesEditor($showRecordLifeSpan, $showAuthority, $controller, $levelDefinedMeaningName, $objectIdFetcher) {
	global
		$optionAttributeAttribute, $optionAttributeOptionAttribute, $optionAttributeValuesAttribute, $optionValueObjectAttributesEditor;

	$editor = new RecordSetTableEditor($optionAttributeValuesAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);

	$editor->addEditor(new OptionAttributeEditor($optionAttributeAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(new OptionSelectEditor($optionAttributeOptionAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new PopUpEditor($optionValueObjectAttributesEditor, 'Annotation'));

	addTableMedataEditors($editor, $showRecordLifeSpan, $showAuthority);

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

function getExpressionMeaningsEditor($attribute, $allowAdd, $showRecordLifeSpan, $showAuthority) {
	global
		$definedMeaningIdAttribute;
	
	$definedMeaningEditor = getDefinedMeaningEditor($showRecordLifeSpan, $showAuthority);

	$definedMeaningCaptionEditor = new DefinedMeaningHeaderEditor($definedMeaningIdAttribute, new SimplePermissionController(false), true, 75);
	$definedMeaningCaptionEditor->setAddText("New exact meaning");

	$expressionMeaningsEditor = new RecordSetListEditor($attribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController($allowAdd), false, $allowAdd, new ExpressionMeaningController(), 3, false);
	$expressionMeaningsEditor->setCaptionEditor($definedMeaningCaptionEditor);
	$expressionMeaningsEditor->setValueEditor($definedMeaningEditor);
	
	return $expressionMeaningsEditor;
}

function getExpressionsEditor($spelling, $showRecordLifeSpan, $showAuthority) {
	global
		$expressionMeaningsAttribute, $expressionExactMeaningsAttribute, $expressionApproximateMeaningsAttribute, $expressionAttribute, $languageAttribute, $expressionsAttribute;

	$expressionMeaningsRecordEditor = new RecordUnorderedListEditor($expressionMeaningsAttribute, 3);
	
	$exactMeaningsEditor = getExpressionMeaningsEditor($expressionExactMeaningsAttribute, true, $showRecordLifeSpan, $showAuthority);
	$expressionMeaningsRecordEditor->addEditor($exactMeaningsEditor);
	$expressionMeaningsRecordEditor->addEditor(getExpressionMeaningsEditor($expressionApproximateMeaningsAttribute, false, $showRecordLifeSpan, $showAuthority));
	
	$expressionMeaningsRecordEditor->expandEditor($exactMeaningsEditor);
	
	$expressionEditor = new RecordSpanEditor($expressionAttribute, ': ', ' - ');
	$expressionEditor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));

	$expressionsEditor = new RecordSetListEditor($expressionsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), false, false, new ExpressionController($spelling), 2, true);
	$expressionsEditor->setCaptionEditor($expressionEditor);
	$expressionsEditor->setValueEditor($expressionMeaningsRecordEditor);

	return $expressionsEditor;
}

function getDefinedMeaningEditor($showRecordLifeSpan, $showAuthority) {
	global
		$definitionAttribute, $definedMeaningAttribute, $definedMeaningObjectAttributesEditor;
	
	$definitionEditor = getDefinitionEditor($definitionAttribute, new DefinedMeaningDefinitionController(), $showRecordLifeSpan, $showAuthority);
	$classAttributesEditor = getClassAttributesEditor($showRecordLifeSpan, $showAuthority);		
	$synonymsAndTranslationsEditor = getSynonymsAndTranslationsEditor($showRecordLifeSpan, $showAuthority);
	$relationsEditor = getDefinedMeaningRelationsEditor($showRecordLifeSpan, $showAuthority);
	$reciprocalRelationsEditor = getDefinedMeaningReciprocalRelationsEditor($showRecordLifeSpan, $showAuthority);
	$classMembershipEditor = getDefinedMeaningClassMembershipEditor($showRecordLifeSpan, $showAuthority);
	$collectionMembershipEditor = getDefinedMeaningCollectionMembershipEditor($showRecordLifeSpan, $showAuthority);
	
	$definedMeaningEditor = new RecordUnorderedListEditor($definedMeaningAttribute, 4);
	$definedMeaningEditor->addEditor($definitionEditor);
	$definedMeaningEditor->addEditor($classAttributesEditor);
	$definedMeaningEditor->addEditor(getAlternativeDefinitionsEditor($showRecordLifeSpan, $showAuthority));
	$definedMeaningEditor->addEditor($synonymsAndTranslationsEditor);
	$definedMeaningEditor->addEditor($relationsEditor);
	$definedMeaningEditor->addEditor($reciprocalRelationsEditor);
	$definedMeaningEditor->addEditor($classMembershipEditor);
	$definedMeaningEditor->addEditor($collectionMembershipEditor);

	$objectAttributeValuesEditor = $definedMeaningObjectAttributesEditor;	
	$definedMeaningEditor->addEditor($objectAttributeValuesEditor);

	$definedMeaningEditor->expandEditor($definitionEditor);
	$definedMeaningEditor->expandEditor($synonymsAndTranslationsEditor);

//		$definedMeaningEditor->expandEditor($relationsEditor);
//		$definedMeaningEditor->expandEditor($classMembershipEditor);
//		$definedMeaningEditor->expandEditor($collectionMembershipEditor);
//		$definedMeaningEditor->expandEditor($objectAttributeValuesEditor);

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

?>
