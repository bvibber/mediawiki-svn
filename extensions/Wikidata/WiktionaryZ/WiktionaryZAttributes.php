<?php

require_once("attribute.php");

global
	$languageAttribute, $spellingAttribute, $textAttribute;

$languageAttribute = new Attribute("language", "Language", "language");
$spellingAttribute = new Attribute("spelling", "Spelling", "spelling");
$textAttribute = new Attribute("text", "Text", "text");

global
	$expressionIdAttribute, $identicalMeaningAttribute;
	
$expressionIdAttribute = new Attribute("expression-id", "Expression Id", "expression-id");
$identicalMeaningAttribute = new Attribute("indentical-meaning", "Identical meaning?", "boolean");

global
	$expressionAttribute;
	
$expressionAttribute = new Attribute("expression", "Expression", new TupleType(new Heading($languageAttribute, $spellingAttribute)));

global
	$collectionAttribute, $sourceIdentifierAttribute;

$collectionAttribute = new Attribute("collection", "Collection", "collection");
$sourceIdentifierAttribute = new Attribute("source-identifier", "Source identifier", "short-text"); 

global
	$collectionMembershipAttribute;

$collectionMembershipAttribute = new Attribute("collection-membership", "Collection membership", new RelationType(new Heading($collectionAttribute, $sourceIdentifierAttribute)));

global
	 $classAttribute;
	 
$classAttribute = new Attribute("class", "Class", "class");
	
global
	$classMembershipAttribute;

$classMembershipAttribute = new Attribute("class-membership", "Class membership", new RelationType(new Heading($classAttribute)));

global
	$relationTypeAttribute, $otherDefinedMeaningAttribute;
	
$relationTypeAttribute = new Attribute("relation-type", "Relation type", "relation-type"); 
$otherDefinedMeaningAttribute = new Attribute("other-defined-meaning", "Other defined meaning", "defining-expression");

global
	$relationsAttribute, $relationHeading;
	
$relationHeading = new Heading($relationTypeAttribute, $otherDefinedMeaningAttribute);	
$relationsAttribute = new Attribute("relations", "Relations", new RelationType($relationHeading));

global
	$translatedTextHeading;
	
$translatedTextHeading = new Heading($languageAttribute, $textAttribute);	

global
	$definitionIdAttribute, $alternativeDefinitionAttribute;

$definitionIdAttribute = new Attribute("definition-id", "Definition identifier", "integer");
$alternativeDefinitionAttribute = new Attribute("alternative-definition", "Alternative definition", new RelationType($translatedTextHeading));

global
	$alternativeDefinitionsAttribute;
	
$alternativeDefinitionsAttribute = new Attribute("alternative-definitions", "Alternative definitions", new RelationType(new Heading($definitionIdAttribute, $alternativeDefinitionAttribute)));

global
	$definitionAttribute, $synonymsAndTranslationsAttribute;
	
$definitionAttribute = new Attribute("definition", "Definition", new RelationType($translatedTextHeading));
$synonymsAndTranslationsAttribute = new Attribute("synonyms-translations", "Synonyms and translations", new RelationType(new Heading($expressionIdAttribute, $expressionAttribute, $identicalMeaningAttribute)));

global
	$definedMeaningIdAttribute, $expressionMeaningsAttribute;

$definedMeaningIdAttribute = new Attribute("defined-meaning-id", "Defined meaning identifier", "defined-meaning-id");
$expressionMeaningsAttribute = new Attribute("expression-meanings", "Defined meanings", new RelationType(new Heading($definedMeaningIdAttribute, $definedMeaningAttribute)));

global
	$textValueIdAttribute, $textAttributeAttribute, $textValueAttribute, $textAttributeValuesAttribute, $textAttributeValuesHeading;
	
$textAttributeAttribute = new Attribute("text-attribute", "Text attribute", "text-attribute");
$textValueIdAttribute = new Attribute("text-value-id-attribute", "Text value identifier", "text-value-id");
$textValueAttribute = new Attribute("text-value", "Text value", new RelationType($translatedTextHeading));

$textAttributeValuesHeading = new Heading($textAttributeAttribute, $textValueIdAttribute, $textValueAttribute);
$textAttributeValuesAttribute = new Attribute("text-attribute-values", "Text attribute values", new RelationType($textAttributeValuesHeading));

global
	$definedMeaningAttribute;
		
$definedMeaningAttribute = new Attribute("defined-meaning", "Defined meaning", new TupleType(new Heading($definitionAttribute, $alternativeDefinitionsAttribute, $synonymsAndTranslationsAttribute, $relationsAttribute, $classMembershipAttribute, $collectionMembershipAttribute, $textAttributeValuesAttribute)));

global
	$expressionsAttribute;
	
$expressionsAttribute = new Attribute("expressions", "Expressions", new RelationType(new Heading($expressionIdAttribute, $expressionAttribute, $expressionMeaningsAttribute)));

?>
