<?php

require_once("Attribute.php");
require_once("WikiDataGlobals.php");
require_once("ViewInformation.php");

/**
 *
 * This file models the structure of the OmegaWiki database in a
 * database-independent fashion. To do so, it follows a simplified
 * relational model, consisting of Attribute objects which are hierarchically
 * grouped together using Structure objects. See Attribute.php for details.
 *
 * The actual data is stored in Records, grouped together as RecordSets.
 * See Record.php and RecordSet.php for details.
 *
 * OmegawikiAttributes2.php was running out of date already, so
 * merging here.
 *
 * TODO:
 * - The current model of a ton of hardcoded globals is highly inadequate
 * and should be replaced with a more abstract schema description. 
 *	-replacing with a single associative array.
 * - Attribute names are in WikidataGlobals.php, but should really be 
 * localizable through MediaWiki's wfMsg() function.
 * 	-this is step 2
 * - Records and RecordSets are currently capable of storing most (not all)
 * data, but can't actually commit them to the database again. To achieve
 * proper separation of architectural layers, the Records should learn
 * to talk directly with the DB layer.
 *	-this is what RecordHelpers are for.
 */

function initializeOmegaWikiAttributes(ViewInformation $viewInformation){
	 global
		$omegaWikiAttributes; // It would be even better if this was
					// passed to objects explicitly
					// but one step at a time...
	$omegaWikiAttributes= new OmegaWikiAttributes($viewInformation); 
	initializeOmegaWikiAttributesOld($viewInformation); //backward compatibility, will be removed.
}


/** 
 * Original initializeOmegaWikiAttributes, Do not call.
 * @deprecated use/update OmegaWikiAttributes->hardValues instead for now.
 */
function initializeOmegaWikiAttributesOld(ViewInformation $viewInformation) {
	global
		$languageAttribute, $spellingAttribute, $textAttribute, 
		$wgLanguageAttributeName, $wgSpellingAttributeName, $wgTextAttributeName;
	
	$languageAttribute = new Attribute("language", $wgLanguageAttributeName, "language");
	$spellingAttribute = new Attribute("spelling", $wgSpellingAttributeName, "spelling");
	$textAttribute = new Attribute("text", $wgTextAttributeName, "text");
	
	global
		$objectAttributesAttribute, $definedMeaningAttributesAttribute, 
		$wgDefinedMeaningAttributesAttributeName, 
		$wgDefinedMeaningAttributesAttributeName, $wgDefinedMeaningAttributesAttributeId, $wgAnnotationAttributeName;
		
	$definedMeaningAttributesAttribute = new Attribute("defined-meaning-attributes", $wgDefinedMeaningAttributesAttributeName, "will-be-specified-below");
	$objectAttributesAttribute = new Attribute("object-attributes", $wgAnnotationAttributeName, "will-be-specified-below");
	
	global
		$expressionIdAttribute, $identicalMeaningAttribute, $wgIdenticalMeaningAttributeName;
		
	$expressionIdAttribute = new Attribute("expression-id", "Expression Id", "expression-id");
	$identicalMeaningAttribute = new Attribute("indentical-meaning", $wgIdenticalMeaningAttributeName, "boolean");
	
	global
		$expressionStructure, $expressionAttribute, $wgExpressionAttributeName;
	
	if ($viewInformation->filterOnLanguage()) 
		$expressionAttribute = new Attribute("expression", $wgSpellingAttributeName, "spelling");
	else {
		$expressionStructure = new Structure("expression", $languageAttribute, $spellingAttribute);
		$expressionAttribute = new Attribute(null, $wgExpressionAttributeName, $expressionStructure);
	}
	
	global
		$definedMeaningIdAttribute, $definedMeaningDefiningExpressionAttribute,
		$definedMeaningCompleteDefiningExpressionStructure,
		$definedMeaningCompleteDefiningExpressionAttribute;
	
	$definedMeaningIdAttribute = new Attribute("defined-meaning-id", "Defined meaning identifier", "defined-meaning-id");
	$definedMeaningDefiningExpressionAttribute = new Attribute("defined-meaning-defining-expression", "Defined meaning defining expression", "short-text");

	$definedMeaningCompleteDefiningExpressionStructure = 
	new Structure("defined-meaning-full-defining-expression",
		  $definedMeaningDefiningExpressionAttribute,
		  $expressionIdAttribute,
		  $languageAttribute
	);

	#	====== refactored up to this point, do not make changes above this line ==== 
	$definedMeaningCompleteDefiningExpressionAttribute=new Attribute(null, "Defining expression", $definedMeaningCompleteDefiningExpressionStructure);


	
	global
		$definedMeaningReferenceStructure, $definedMeaningLabelAttribute, $definedMeaningReferenceType,
		$definedMeaningReferenceAttribute, $wgDefinedMeaningReferenceAttributeName;
		
	$definedMeaningLabelAttribute = new Attribute("defined-meaning-label", "Defined meaning label", "short-text");
	$definedMeaningReferenceStructure = new Structure("defined-meaning", $definedMeaningIdAttribute, $definedMeaningLabelAttribute, $definedMeaningDefiningExpressionAttribute);

	$definedMeaningReferenceType = $definedMeaningReferenceStructure;
	$definedMeaningReferenceAttribute = new Attribute(null, $wgDefinedMeaningReferenceAttributeName, $definedMeaningReferenceType);
	
	global
		$collectionIdAttribute, $collectionMeaningAttribute, $sourceIdentifierAttribute,
		$gotoSourceStructure, $gotoSourceAttribute,
		$wgCollectionAttributeName, $wgSourceIdentifierAttributeName, $wgGotoSourceAttributeName;
	
	$collectionIdAttribute = new Attribute("collection", "Collection", "collection-id");
	$collectionMeaningAttribute = new Attribute("collection-meaning", $wgCollectionAttributeName, $definedMeaningReferenceStructure);
	$sourceIdentifierAttribute = new Attribute("source-identifier", $wgSourceIdentifierAttributeName, "short-text");
	$gotoSourceStructure = new Structure("goto-source",$collectionIdAttribute, $sourceIdentifierAttribute);
	$gotoSourceAttribute = new Attribute(null, $wgGotoSourceAttributeName, $gotoSourceStructure); 
	
	global
		$collectionMembershipAttribute, $wgCollectionMembershipAttributeName, $wgCollectionMembershipAttributeId,
		$collectionMembershipStructure;
	
	$collectionMembershipStructure = new Structure("collection-membership",$collectionIdAttribute, $collectionMeaningAttribute, $sourceIdentifierAttribute);
	$collectionMembershipAttribute = new Attribute(null, $wgCollectionMembershipAttributeName, $collectionMembershipStructure);
	
	global
		 $classMembershipIdAttribute, $classAttribute;
		 
	$classMembershipIdAttribute = new Attribute("class-membership-id", "Class membership id", "integer");	 
	$classAttribute = new Attribute("class", "Class", $definedMeaningReferenceStructure);
		
	global
		$classMembershipStructure, $classMembershipKeyStructure, $classMembershipAttribute, 
		$wgClassMembershipAttributeName, $wgClassMembershipAttributeId;
	
	$classMembershipStructure = new Structure("class-membership", $classMembershipIdAttribute, $classAttribute);
	$classMembershipAttribute = new Attribute(null, $wgClassMembershipAttributeName, $classMembershipStructure);
	
	global
		 $possiblySynonymousIdAttribute, 
		 $possibleSynonymAttribute, 
		 $wgPossibleSynonymAttributeName, $possiblySynonymousStructure, $possiblySynonymousAttribute, 
		 $wgPossiblySynonymousAttributeName, $wgPossiblySynonymousAttributeId;
		 
	$possiblySynonymousIdAttribute = new Attribute("possibly-synonymous-id", "Possibly synonymous id", "integer");	 
	$possibleSynonymAttribute = new Attribute("possible-synonym", $wgPossibleSynonymAttributeName, $definedMeaningReferenceStructure);
	$possiblySynonymousStructure = new Structure("possibly-synonymous", $possiblySynonymousIdAttribute, $possiblySynonymousAttribute);
	$possiblySynonymousAttribute = new Attribute(null, $wgPossiblySynonymousAttributeName, $possiblySynonymousStructure);

	global
		$relationIdAttribute, $relationTypeAttribute, $relationTypeType, $otherDefinedMeaningAttribute,
		$wgRelationTypeAttributeName, $wgOtherDefinedMeaningAttributeName;
	
	$relationIdAttribute = new Attribute("relation-id", "Relation identifier", "object-id");
	$relationTypeAttribute = new Attribute("relation-type", $wgRelationTypeAttributeName, $definedMeaningReferenceStructure); 
	$otherDefinedMeaningAttribute = new Attribute("other-defined-meaning", $wgOtherDefinedMeaningAttributeName, $definedMeaningReferenceType);
	
	global
		$relationsAttribute, $relationStructure, $reciprocalRelationsAttribute, $objectAttributesAttribute, $wgRelationsAttributeName, $wgIncomingRelationsAttributeName, $wgRelationsAttributeId, $wgIncomingRelationsAttributeId,
		$repRelStructure;
		
	$relationStructure = new Structure("relations", $relationIdAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute, $objectAttributesAttribute);
	$relationsAttribute = new Attribute(null, $wgRelationsAttributeName, $relationStructure);
	$reciprocalRelationsAttribute = new Attribute("reciprocal-relations", $wgIncomingRelationsAttributeName, $relationStructure);
	
	global
		$translatedTextIdAttribute, $translatedTextStructure;
		
	$translatedTextIdAttribute = new Attribute("translated-text-id", "Translated text ID", "integer");	
	$translatedTextStructure = new Structure("translated-text", $languageAttribute, $textAttribute);	
	
	global
		$definitionIdAttribute, $alternativeDefinitionAttribute, $sourceAttribute,
		$wgAlternativeDefinitionAttributeName, $wgSourceAttributeName;
	
	$definitionIdAttribute = new Attribute("definition-id", "Definition identifier", "integer");

	if ($viewInformation->filterOnLanguage() && !$viewInformation->hasMetaDataAttributes())
		$alternativeDefinitionAttribute = new Attribute("alternative-definition", $wgAlternativeDefinitionAttributeName, "text");
	else
		$alternativeDefinitionAttribute = new Attribute("alternative-definition", $wgAlternativeDefinitionAttributeName, $translatedTextStructure);
	
	$sourceAttribute = new Attribute("source-id", $wgSourceAttributeName, $definedMeaningReferenceType);
	
	global
		$alternativeDefinitionsAttribute, $wgAlternativeDefinitionsAttributeName, $wgAlternativeDefinitionsAttributeId,
		$alternativeDefinitionsStructure;
		
	$alternativeDefinitionsStructure =  new Structure("alternative-definitions", $definitionIdAttribute, $alternativeDefinitionAttribute, $sourceAttribute);
		
	$alternativeDefinitionsAttribute = new Attribute(null, $wgAlternativeDefinitionsAttributeName, $alternativeDefinitionsStructure);
	
	global
		$synonymsAndTranslationsAttribute, $syntransIdAttribute, 
		$wgSynonymsAttributeName, $wgSynonymsAndTranslationsAttributeName, $wgSynonymsAndTranslationsAttributeId,
		$synonymsTranslationsStructure;
	
	if ($viewInformation->filterOnLanguage())
		$synonymsAndTranslationsCaption = $wgSynonymsAttributeName;
	else
		$synonymsAndTranslationsCaption = $wgSynonymsAndTranslationsAttributeName;

	$syntransIdAttribute = new Attribute("syntrans-id", "$synonymsAndTranslationsCaption identifier", "integer");
	$synonymsTranslationsStructure = new Structure("synonyms-translations", $syntransIdAttribute, $expressionAttribute, $identicalMeaningAttribute, $objectAttributesAttribute);
	$synonymsAndTranslationsAttribute = new Attribute(null, "$synonymsAndTranslationsCaption", $synonymsTranslationsStructure);
	
	global
		$translatedTextAttributeIdAttribute, $translatedTextValueIdAttribute, 
		$translatedTextAttributeObjectAttribute, $translatedTextAttributeAttribute, $translatedTextValueAttribute, $translatedTextAttributeValuesAttribute, 
		$translatedTextAttributeValuesStructure, $wgTranslatedTextAttributeValuesAttributeName, $wgTranslatedTextAttributeAttributeName, $wgTranslatedTextAttributeValueAttributeName;
	
	$translatedTextAttributeIdAttribute = new Attribute("translated-text-attribute-id", "Attribute identifier", "object-id");
	$translatedTextAttributeObjectAttribute = new Attribute("translated-text-attribute-object-id", "Attribute object", "object-id");
	$translatedTextAttributeAttribute = new Attribute("translated-text-attribute", $wgTranslatedTextAttributeAttributeName, $definedMeaningReferenceType);
	$translatedTextValueIdAttribute = new Attribute("translated-text-value-id", "Translated text value identifier", "translated-text-value-id");
	
	if ($viewInformation->filterOnLanguage() && !$viewInformation->hasMetaDataAttributes())
		$translatedTextValueAttribute = new Attribute("translated-text-value", $wgTranslatedTextAttributeValueAttributeName, "text");
	else
		$translatedTextValueAttribute = new Attribute("translated-text", $wgTranslatedTextAttributeValueAttributeName, $translatedTextStructure);
	
	$translatedTextAttributeValuesStructure = new Structure("translated-text-attribute-values",$translatedTextAttributeIdAttribute, $translatedTextAttributeObjectAttribute, $translatedTextAttributeAttribute, $translatedTextValueIdAttribute, $translatedTextValueAttribute, $objectAttributesAttribute);
	$translatedTextAttributeValuesAttribute = new Attribute(null, $wgTranslatedTextAttributeValuesAttributeName, $translatedTextAttributeValuesStructure);
	
	global
		$textAttributeIdAttribute, $textAttributeObjectAttribute, $textAttributeAttribute, $textAttributeValuesStructure, 
		$textAttributeValuesAttribute, 
		$wgTextAttributeValuesAttributeName, $wgTextAttributeAttributeName;
	
	$textAttributeIdAttribute = new Attribute("text-attribute-id", "Attribute identifier", "object-id");
	$textAttributeObjectAttribute = new Attribute("text-attribute-object-id", "Attribute object", "object-id");
	$textAttributeAttribute = new Attribute("text-attribute", $wgTextAttributeAttributeName, $definedMeaningReferenceStructure);
	$textAttributeValuesStructure = new Structure("text-attribute-values", $textAttributeIdAttribute, $textAttributeObjectAttribute, $textAttributeAttribute, $textAttribute, $objectAttributesAttribute);	
	$textAttributeValuesAttribute = new Attribute(null, $wgTextAttributeValuesAttributeName, $textAttributeValuesStructure);

	global
		$linkAttributeIdAttribute, $linkAttributeObjectAttribute, $linkAttributeAttribute, $linkAttributeValuesStructure, $linkAttributeValuesAttribute,
		$wgLinkAttributeValuesAttributeName, $wgLinkAttributeAttributeName, 
		$linkAttribute, $linkLabelAttribute, $linkURLAttribute;
		
	$linkLabelAttribute = new Attribute("label", "Label", "short-text"); 
	$linkURLAttribute = new Attribute("url", "URL", "url");
	$linkAttribute = new Attribute("link", "Link", new Structure($linkLabelAttribute, $linkURLAttribute));
	
	$linkAttributeIdAttribute = new Attribute("link-attribute-id", "Attribute identifier", "object-id");
	$linkAttributeObjectAttribute = new Attribute("link-attribute-object-id", "Attribute object", "object-id");
	$linkAttributeAttribute = new Attribute("link-attribute", $wgLinkAttributeAttributeName, $definedMeaningReferenceStructure);
	$linkAttributeValuesStructure = new Structure("link-attribute-values", $linkAttributeIdAttribute, $linkAttributeObjectAttribute, $linkAttributeAttribute, $linkAttribute, $objectAttributesAttribute);	
	$linkAttributeValuesAttribute = new Attribute(null, $wgLinkAttributeValuesAttributeName, $linkAttributeValuesStructure);
	
	global
		$optionAttributeIdAttribute, $optionAttributeAttribute, $optionAttributeObjectAttribute, $optionAttributeOptionAttribute, $optionAttributeValuesAttribute,
		$wgOptionAttributeAttributeName, $wgOptionAttributeOptionAttributeName, $wgOptionAttributeValuesAttributeName, $optionAttributeValuesStructure;
	
	$optionAttributeIdAttribute = new Attribute('option-attribute-id', 'Attribute identifier', 'object-id');
	$optionAttributeObjectAttribute = new Attribute('option-attribute-object-id', 'Attribute object', 'object-id');
	$optionAttributeAttribute = new Attribute('option-attribute', $wgOptionAttributeAttributeName, $definedMeaningReferenceType);
	$optionAttributeOptionAttribute = new Attribute('option-attribute-option', $wgOptionAttributeOptionAttributeName, $definedMeaningReferenceType);
	$optionAttributeValuesStructure = new Structure('option-attribute-values', $optionAttributeIdAttribute, $optionAttributeAttribute, $optionAttributeObjectAttribute, $optionAttributeOptionAttribute, $objectAttributesAttribute);
	$optionAttributeValuesAttribute = new Attribute(null, $wgOptionAttributeValuesAttributeName, $optionAttributeValuesStructure);
	
	global
		$optionAttributeOptionIdAttribute, $optionAttributeOptionsAttribute, $wgOptionAttributeOptionsAttributeName;
		
	$optionAttributeOptionIdAttribute = new Attribute('option-attribute-option-id', 'Option identifier', 'object-id');
	$optionAttributeOptionsStructure = new Structure('option-attribute-options', $optionAttributeOptionIdAttribute, $optionAttributeAttribute, $optionAttributeOptionAttribute, $languageAttribute);
	$optionAttributeOptionsAttribute = new Attribute(null, $wgOptionAttributeOptionsAttributeName, $optionAttributeOptionsStructure);
	
	global
		$definitionAttribute, $translatedTextAttribute, $classAttributesAttribute,
		$wgDefinitionAttributeName, $wgTranslatedTextAttributeName;
	
	if ($viewInformation->filterOnLanguage() && !$viewInformation->hasMetaDataAttributes())
		$translatedTextAttribute = new Attribute("translated-text", $wgTextAttributeName, "text");	
	else
		$translatedTextAttribute = new Attribute(null, $wgTranslatedTextAttributeName, $translatedTextStructure);
		
	$definitionAttribute = new Attribute(null, $wgDefinitionAttributeName, new Structure("definition", $translatedTextAttribute, $objectAttributesAttribute));

	global
		$classAttributesStructure,
	//	$classAttributeClassAttribute, 
		$classAttributeIdAttribute, $classAttributeAttributeAttribute, $classAttributeLevelAttribute, $classAttributeTypeAttribute,
		$wgClassAttributeAttributeAttributeName, $wgClassAttributeLevelAttributeName, 
		$wgClassAttributeTypeAttributeName, $wgClassAttributesAttributeName, $wgClassAttributesAttributeId;
	
	$classAttributeIdAttribute = new Attribute("class-attribute-id", "Class attribute identifier", "object-id");
	$classAttributeAttributeAttribute = new Attribute("class-attribute-attribute", $wgClassAttributeAttributeAttributeName, $definedMeaningReferenceStructure);
	$classAttributeLevelAttribute = new Attribute("class-attribute-level", $wgClassAttributeLevelAttributeName, $definedMeaningReferenceStructure);
	$classAttributeTypeAttribute = new Attribute("class-attribute-type", $wgClassAttributeTypeAttributeName, "short-text");
	$classAttributesStructure = new Structure("class-attributes", $classAttributeIdAttribute, $classAttributeAttributeAttribute, $classAttributeLevelAttribute, $classAttributeTypeAttribute, $optionAttributeOptionsAttribute);
	$classAttributesAttribute = new Attribute(null, $wgClassAttributesAttributeName, $classAttributesStructure);
	
	global
		$definedMeaningAttribute, $wgDefinedMeaningAttributeName;

	$definedMeaningAttribute = new Attribute(null, $wgDefinedMeaningAttributeName, 
		new Structure(
			"defined-meaning",
			$definitionAttribute, 
			$classAttributesAttribute, 
			$alternativeDefinitionsAttribute, 
			$synonymsAndTranslationsAttribute, 
			$relationsAttribute, 
			$reciprocalRelationsAttribute, 
			$classMembershipAttribute, 
			$collectionMembershipAttribute, 
			$definedMeaningAttributesAttribute)
	);

	global
		$expressionsAttribute, $expressionMeaningStructure, $expressionExactMeaningsAttribute, $expressionApproximateMeaningsAttribute,
		$wgExactMeaningsAttributeName, $wgApproximateMeaningsAttributeName;
		
	$expressionMeaningStructure = new Structure("expression-exact-meanings", $definedMeaningIdAttribute, $textAttribute, $definedMeaningAttribute); 	
	$expressionExactMeaningsAttribute = new Attribute(null, $wgExactMeaningsAttributeName, $expressionMeaningStructure);
	$expressionApproximateMeaningsAttribute = new Attribute("expression-approximate-meanings", $wgApproximateMeaningsAttributeName, $expressionMeaningStructure);
	
	global
		$expressionMeaningsAttribute, $expressionMeaningsStructure, $expressionApproximateMeaningAttribute,
		$wgExpressionMeaningsAttributeName, $wgExpressionsAttributeName,
		$expressionsStructure;
	
	$expressionMeaningsStructure = new Structure("expression-meanings", $expressionExactMeaningsAttribute, $expressionApproximateMeaningAttribute);
	$expressionMeaningsAttribute = new Attribute(null, $wgExpressionMeaningsAttributeName, $expressionMeaningsStructure);
	
	$expressionsStructure = new Structure("expressions", $expressionIdAttribute, $expressionAttribute, $expressionMeaningsAttribute);
	$expressionsAttribute = new Attribute(null, $wgExpressionsAttributeName, $expressionsStructure);
	
	global
		$objectIdAttribute, $objectAttributesStructure, $wgAnnotationAttributeName;
	
	$objectIdAttribute = new Attribute("object-id", "Object identifier", "object-id");
	$objectAttributesStructure = new Structure("object-attributes", $objectIdAttribute, $textAttributeValuesAttribute, $translatedTextAttributeValuesAttribute, $optionAttributeValuesAttribute);
	$objectAttributesAttribute->setAttributeType($objectAttributesStructure);
	$definedMeaningAttributesAttribute->setAttributeType($objectAttributesStructure);
	
	$annotatedAttributes = array(
		$definitionAttribute, 
		$synonymsAndTranslationsAttribute, 
		$relationsAttribute,
		$reciprocalRelationsAttribute
	);
	
	foreach ($viewInformation->getPropertyToColumnFilters() as $propertyToColumnFilter) {
		$attribute = $propertyToColumnFilter->getAttribute();
		$attribute->setAttributeType($objectAttributesStructure);
		
		foreach ($annotatedAttributes as $annotatedAttribute) 		
			$annotatedAttribute->type->addAttribute($attribute);
	}
}


class OmegaWikiAttributes {
	protected $attributes = array();

	function __construct(ViewInformation $viewInformation) {
		$this->hardValues($viewInformation);
	}

	/** Hardcoded schema for now. Later refactor to load from file or DB 
	 * 
	 * Naming: keys are previous name minus -"Attribute"
	 * 	(-"Structure" is retained, -"Attributes" is retained)
	*/
	private function hardValues($viewInformation) {
	
		$a=&$this->attributes; # <- wrist/RSI protection 
		$t=$this; #<-idem
	
		global
			$wgLanguageAttributeName, $wgSpellingAttributeName, $wgTextAttributeName;
	
		$t->language = new Attribute("language", $wgLanguageAttributeName, "language");
		$t->spelling = new Attribute("spelling", $wgSpellingAttributeName, "spelling");
		$t->text= new Attribute("text", $wgTextAttributeName, "text");

		/*	
		$a->definedMeaningAttributes = new Attribute("defined-meaning-attributes", $wgDefinedMeaningAttributesAttributeName, "will-be-specified-below");
		$a["objectAttributes"] = new Attribute("object-attributes", $wgAnnotationAttributeName, "will-be-specified-below");

		# TODO replace with i18n
		global
			$expressionIdAttribute, $identicalMeaningAttribute;
			
		$a["expressionId"] = new Attribute("expression-id", "Expression Id", "expression-id");
		$a["identicalMeaning"] = new Attribute("indentical-meaning", $wgIdenticalMeaningAttributeName, "boolean");
		
		#TODO replace with i18n
		global
			 $wgExpressionAttributeName;
		
		if ($viewInformation->filterOnLanguage()) 
			$a["expression"] = new Attribute("expression", $wgSpellingAttributeName, "spelling");
		else {
			$a["expressionStructure"] = new Structure("expression", $this->language, $this->spelling);
			$a["expression"] = new Attribute(null, $wgExpressionAttributeName, $expressionStructure);
		}
		
		global
			$definedMeaningIdAttribute, $definedMeaningDefiningExpressionAttribute,
			$definedMeaningCompleteDefiningExpressionStructure,
			$definedMeaningCompleteDefiningExpressionAttribute;
		
		$a["definedMeaningId"] = new Attribute("defined-meaning-id", "Defined meaning identifier", "defined-meaning-id");
		$a["definedMeaningDefiningExpression"] = new Attribute("defined-meaning-defining-expression", "Defined meaning defining expression", "short-text");

		$a["definedMeaningCompleteDefiningExpressionStructure"] = 
		new Structure("defined-meaning-full-defining-expression",
			  $definedMeaningDefiningExpressionAttribute,
			  $expressionIdAttribute,
			  $
		);
		$a["definedMeaningCompleteDefiningExpression"]=new Attribute(null, "Defining expression", $definedMeaningCompleteDefiningExpressionStructure);
		);
		*/
	}


	protected function __set($key,$value) {
		$attributes=&$this->attributes;
		$attributes[$key]=$value;
		
	}
	
	public function __get($key) {
		$attributes=&$this->attributes;
		if (!array_key_exists($key, $attributes)) {
			throw new Exception("Key does not exist");
		}
		return $attributes[$key];
	}	
}



