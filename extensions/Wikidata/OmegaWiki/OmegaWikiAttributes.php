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
 * 
 * --- Comment by Peter-Jan Roes: 
 * I don't agree that proper separation of architectural layers means that
 * Records and RecordSets should know how to save themselves to the database.
 * Actually, altering Records and RecordSets this way increases the entanglement
 * of architectural layers: Record(Set)s will need to know about a database,
 * which is not to be preferred.
 *  
 * Instead I think it is better to introduce another entity that carries this
 * responsibility, for instance HierarchicalQueryEngine. This entity will know
 * how to query an ordinary relational database (like MySQL) into the hierarchical
 * data structure formed by Records and RecordSets. The same entity will be able
 * to save back data to the database.   
 * 
 */
function initializeOmegaWikiAttributes(ViewInformation $viewInformation){
//	initializeOmegaWikiAttributesOld($viewInformation); //backward compatibility, will be removed!
	$viewInformation->setAttributeSet(new OmegaWikiAttributes($viewInformation)); 
}

/** 
 * Original initializeOmegaWikiAttributes, Do not call.
 * @deprecated use/update OmegaWikiAttributes->hardValues instead for now.
 */
/*function initializeOmegaWikiAttributesOld(ViewInformation $viewInformation) {
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
		
	$definedMeaningAttributesAttribute = new Attribute("definedMeaningAttributes", $wgDefinedMeaningAttributesAttributeName, "will-be-specified-below");
	$objectAttributesAttribute = new Attribute("objectAttributes", $wgAnnotationAttributeName, "will-be-specified-below");
	
	global
		$expressionIdAttribute, $identicalMeaningAttribute, $wgIdenticalMeaningAttributeName;
		
	$expressionIdAttribute = new Attribute("expressionId", "Expression Id", "expression-id");
	$identicalMeaningAttribute = new Attribute("indenticalMeaning", $wgIdenticalMeaningAttributeName, "boolean");
	
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
	
	$definedMeaningIdAttribute = new Attribute("definedMeaningId", "Defined meaning identifier", "defined-meaning-id");
	$definedMeaningDefiningExpressionAttribute = new Attribute("definedMeaningDefiningExpression", "Defined meaning defining expression", "short-text");

	$definedMeaningCompleteDefiningExpressionStructure = 
		new Structure("definedMeaningFullDefiningExpression",
		  $definedMeaningDefiningExpressionAttribute,
		  $expressionIdAttribute,
		  $languageAttribute
		);

	#	====== refactored up to this point, do not make changes above this line ==== 
	$definedMeaningCompleteDefiningExpressionAttribute = new Attribute("definedMeaningCompleteDefiningExpression", "Defining expression", $definedMeaningCompleteDefiningExpressionStructure);

	global
		$definedMeaningReferenceStructure, $definedMeaningLabelAttribute, $definedMeaningReferenceType,
		$definedMeaningReferenceAttribute, $wgDefinedMeaningReferenceAttributeName;
		
	$definedMeaningLabelAttribute = new Attribute("definedMeaningLabel", "Defined meaning label", "short-text");
	$definedMeaningReferenceStructure = new Structure("defined-meaning", $definedMeaningIdAttribute, $definedMeaningLabelAttribute, $definedMeaningDefiningExpressionAttribute);

	$definedMeaningReferenceType = $definedMeaningReferenceStructure;
	$definedMeaningReferenceAttribute = new Attribute(null, $wgDefinedMeaningReferenceAttributeName, $definedMeaningReferenceType);
	
	global
		$collectionIdAttribute, $collectionMeaningAttribute, $sourceIdentifierAttribute,
		$gotoSourceStructure, $gotoSourceAttribute,
		$wgCollectionAttributeName, $wgSourceIdentifierAttributeName, $wgGotoSourceAttributeName;
	
	$collectionIdAttribute = new Attribute("collection", "Collection", "collection-id");
	$collectionMeaningAttribute = new Attribute("collectionMeaning", $wgCollectionAttributeName, $definedMeaningReferenceStructure);
	$sourceIdentifierAttribute = new Attribute("sourceIdentifier", $wgSourceIdentifierAttributeName, "short-text");
	$gotoSourceStructure = new Structure("goto-source",$collectionIdAttribute, $sourceIdentifierAttribute);
	$gotoSourceAttribute = new Attribute("gotoSource", $wgGotoSourceAttributeName, $gotoSourceStructure); 
	
	global
		$collectionMembershipAttribute, $wgCollectionMembershipAttributeName, $wgCollectionMembershipAttributeId,
		$collectionMembershipStructure;
	
	$collectionMembershipStructure = new Structure("collection-membership",$collectionIdAttribute, $collectionMeaningAttribute, $sourceIdentifierAttribute, $gotoSourceAttribute);
	$collectionMembershipAttribute = new Attribute("collectionMembership", $wgCollectionMembershipAttributeName, $collectionMembershipStructure);
	
	global
		 $classMembershipIdAttribute, $classAttribute;
		 
	$classMembershipIdAttribute = new Attribute("classMembershipId", "Class membership id", "integer");	 
	$classAttribute = new Attribute("class", "Class", $definedMeaningReferenceStructure);
		
	global
		$classMembershipStructure, $classMembershipKeyStructure, $classMembershipAttribute, 
		$wgClassMembershipAttributeName, $wgClassMembershipAttributeId;
	
	$classMembershipStructure = new Structure("classMembership", $classMembershipIdAttribute, $classAttribute);
	$classMembershipAttribute = new Attribute(null, $wgClassMembershipAttributeName, $classMembershipStructure);
	
	global
		 $possiblySynonymousIdAttribute, 
		 $possibleSynonymAttribute, 
		 $wgPossibleSynonymAttributeName, $possiblySynonymousStructure, $possiblySynonymousAttribute, 
		 $wgPossiblySynonymousAttributeName, $wgPossiblySynonymousAttributeId;
		 
	$possiblySynonymousIdAttribute = new Attribute("possiblySynonymousId", "Possibly synonymous id", "integer");	 
	$possibleSynonymAttribute = new Attribute("possibleSynonym", $wgPossibleSynonymAttributeName, $definedMeaningReferenceStructure);
	$possiblySynonymousStructure = new Structure("possibly-synonymous", $possiblySynonymousIdAttribute, $possibleSynonymAttribute);
	$possiblySynonymousAttribute = new Attribute("possiblySynonymous", $wgPossiblySynonymousAttributeName, $possiblySynonymousStructure);

	global
		$relationIdAttribute, $relationTypeAttribute, $relationTypeType, $otherDefinedMeaningAttribute,
		$wgRelationTypeAttributeName, $wgOtherDefinedMeaningAttributeName;
	
	$relationIdAttribute = new Attribute("relationId", "Relation identifier", "object-id");
	$relationTypeAttribute = new Attribute("relationType", $wgRelationTypeAttributeName, $definedMeaningReferenceStructure); 
	$otherDefinedMeaningAttribute = new Attribute("otherDefinedMeaning", $wgOtherDefinedMeaningAttributeName, $definedMeaningReferenceType);
	
	global
		$relationsAttribute, $relationStructure, $reciprocalRelationsAttribute, $objectAttributesAttribute, $wgRelationsAttributeName, $wgIncomingRelationsAttributeName, $wgRelationsAttributeId, $wgIncomingRelationsAttributeId,
		$repRelStructure;
		
	$relationStructure = new Structure("relations", $relationIdAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute, $objectAttributesAttribute);
	$relationsAttribute = new Attribute(null, $wgRelationsAttributeName, $relationStructure);
	$reciprocalRelationsAttribute = new Attribute("reciprocalRelations", $wgIncomingRelationsAttributeName, $relationStructure);
	
	global
		$translatedTextIdAttribute, $translatedTextStructure;
		
	$translatedTextIdAttribute = new Attribute("translatedTextId", "Translated text ID", "integer");	
	$translatedTextStructure = new Structure("translated-text", $languageAttribute, $textAttribute);	
	
	global
		$definitionIdAttribute, $alternativeDefinitionAttribute, $sourceAttribute,
		$wgAlternativeDefinitionAttributeName, $wgSourceAttributeName;
	
	$definitionIdAttribute = new Attribute("definitionId", "Definition identifier", "integer");

	if ($viewInformation->filterOnLanguage() && !$viewInformation->hasMetaDataAttributes())
		$alternativeDefinitionAttribute = new Attribute("alternativeDefinition", $wgAlternativeDefinitionAttributeName, "text");
	else
		$alternativeDefinitionAttribute = new Attribute("alternativeDefinition", $wgAlternativeDefinitionAttributeName, $translatedTextStructure);
	
	$sourceAttribute = new Attribute("sourceId", $wgSourceAttributeName, $definedMeaningReferenceType);
	
	global
		$alternativeDefinitionsAttribute, $wgAlternativeDefinitionsAttributeName, $wgAlternativeDefinitionsAttributeId,
		$alternativeDefinitionsStructure;
		
	$alternativeDefinitionsStructure =  new Structure("alternative-definitions", $definitionIdAttribute, $alternativeDefinitionAttribute, $sourceAttribute);
	$alternativeDefinitionsAttribute = new Attribute("alternativeDefinitions", $wgAlternativeDefinitionsAttributeName, $alternativeDefinitionsStructure);
	
	global
		$synonymsAndTranslationsAttribute, $syntransIdAttribute, 
		$wgSynonymsAttributeName, $wgSynonymsAndTranslationsAttributeName, $wgSynonymsAndTranslationsAttributeId,
		$synonymsTranslationsStructure;
	
	if ($viewInformation->filterOnLanguage())
		$synonymsAndTranslationsCaption = $wgSynonymsAttributeName;
	else
		$synonymsAndTranslationsCaption = $wgSynonymsAndTranslationsAttributeName;

	$syntransIdAttribute = new Attribute("syntransId", "$synonymsAndTranslationsCaption identifier", "integer");
	$synonymsTranslationsStructure = new Structure("synonyms-translations", $syntransIdAttribute, $expressionAttribute, $identicalMeaningAttribute, $objectAttributesAttribute);
	$synonymsAndTranslationsAttribute = new Attribute("synonymsAndTranslations", "$synonymsAndTranslationsCaption", $synonymsTranslationsStructure);
	
	global
		$attributeObjectAttribute;
		
	$attributeObjectAttribute = new Attribute("attributeObject", "Attribute object", "object-id");
	
	global
		$translatedTextAttributeIdAttribute, $translatedTextValueIdAttribute, 
		$translatedTextAttributeAttribute, $translatedTextValueAttribute, $translatedTextAttributeValuesAttribute, 
		$translatedTextAttributeValuesStructure, $wgTranslatedTextAttributeValuesAttributeName, $wgTranslatedTextAttributeAttributeName, $wgTranslatedTextAttributeValueAttributeName;
	
	$translatedTextAttributeIdAttribute = new Attribute("translatedTextAttributeId", "Attribute identifier", "object-id");
	$translatedTextAttributeAttribute = new Attribute("translatedTextAttribute", $wgTranslatedTextAttributeAttributeName, $definedMeaningReferenceType);
	$translatedTextValueIdAttribute = new Attribute("translatedTextValueId", "Translated text value identifier", "translated-text-value-id");
	
	if ($viewInformation->filterOnLanguage() && !$viewInformation->hasMetaDataAttributes())
		$translatedTextValueAttribute = new Attribute("translated-text-value", $wgTranslatedTextAttributeValueAttributeName, "text");
	else
		$translatedTextValueAttribute = new Attribute("translated-text", $wgTranslatedTextAttributeValueAttributeName, $translatedTextStructure);
	
	$translatedTextAttributeValuesStructure = new Structure("translated-text-attribute-values",$translatedTextAttributeIdAttribute, $attributeObjectAttribute, $translatedTextAttributeAttribute, $translatedTextValueIdAttribute, $translatedTextValueAttribute, $objectAttributesAttribute);
	$translatedTextAttributeValuesAttribute = new Attribute("translatedTextAttributeValues", $wgTranslatedTextAttributeValuesAttributeName, $translatedTextAttributeValuesStructure);
	
	global
		$textAttributeIdAttribute, $textAttributeObjectAttribute, $textAttributeAttribute, $textAttributeValuesStructure, 
		$textAttributeValuesAttribute, 
		$wgTextAttributeValuesAttributeName, $wgTextAttributeAttributeName;
	
	$textAttributeIdAttribute = new Attribute("textAttributeId", "Attribute identifier", "object-id");
	$textAttributeObjectAttribute = new Attribute("textAttributeObject", "Attribute object", "object-id");
	$textAttributeAttribute = new Attribute("textAttribute", $wgTextAttributeAttributeName, $definedMeaningReferenceStructure);
	$textAttributeValuesStructure = new Structure("text-attribute-values", $textAttributeIdAttribute, $textAttributeObjectAttribute, $textAttributeAttribute, $textAttribute, $objectAttributesAttribute);	
	$textAttributeValuesAttribute = new Attribute("textAttributeValues", $wgTextAttributeValuesAttributeName, $textAttributeValuesStructure);

	global
		$linkAttributeIdAttribute, $linkAttributeObjectAttribute, $linkAttributeAttribute, $linkAttributeValuesStructure, $linkAttributeValuesAttribute,
		$wgLinkAttributeValuesAttributeName, $wgLinkAttributeAttributeName, 
		$linkAttribute, $linkLabelAttribute, $linkURLAttribute;
		
	$linkLabelAttribute = new Attribute("linkLabel", "Label", "short-text"); 
	$linkURLAttribute = new Attribute("linkURL", "URL", "url");
	$linkAttribute = new Attribute("link", "Link", new Structure($linkLabelAttribute, $linkURLAttribute));
	
	$linkAttributeIdAttribute = new Attribute("linkAttributeId", "Attribute identifier", "object-id");
	$linkAttributeObjectAttribute = new Attribute("linkAttributeObject", "Attribute object", "object-id");
	$linkAttributeAttribute = new Attribute("linkAttribute", $wgLinkAttributeAttributeName, $definedMeaningReferenceStructure);
	$linkAttributeValuesStructure = new Structure("link-attribute-values", $linkAttributeIdAttribute, $linkAttributeObjectAttribute, $linkAttributeAttribute, $linkAttribute, $objectAttributesAttribute);	
	$linkAttributeValuesAttribute = new Attribute("linkAttributeValues", $wgLinkAttributeValuesAttributeName, $linkAttributeValuesStructure);
	
	global
		$optionAttributeIdAttribute, $optionAttributeAttribute, $optionAttributeObjectAttribute, $optionAttributeOptionAttribute, $optionAttributeValuesAttribute,
		$wgOptionAttributeAttributeName, $wgOptionAttributeOptionAttributeName, $wgOptionAttributeValuesAttributeName, $optionAttributeValuesStructure;
	
	$optionAttributeIdAttribute = new Attribute("optionAttributeId", "Attribute identifier", "object-id");
	$optionAttributeObjectAttribute = new Attribute("optionAttributeObject", "Attribute object", "object-id");
	$optionAttributeAttribute = new Attribute("optionAttribute", $wgOptionAttributeAttributeName, $definedMeaningReferenceType);
	$optionAttributeOptionAttribute = new Attribute("optionAttributeOption", $wgOptionAttributeOptionAttributeName, $definedMeaningReferenceType);
	$optionAttributeValuesStructure = new Structure('option-attribute-values', $optionAttributeIdAttribute, $optionAttributeAttribute, $optionAttributeObjectAttribute, $optionAttributeOptionAttribute, $objectAttributesAttribute);
	$optionAttributeValuesAttribute = new Attribute("optionAttributeValues", $wgOptionAttributeValuesAttributeName, $optionAttributeValuesStructure);
	
	global
		$optionAttributeOptionIdAttribute, $optionAttributeOptionsAttribute, $wgOptionAttributeOptionsAttributeName;
		
	$optionAttributeOptionIdAttribute = new Attribute("optionAttributeOptionId", "Option identifier", "object-id");
	$optionAttributeOptionsStructure = new Structure("option-attribute-options", $optionAttributeOptionIdAttribute, $optionAttributeAttribute, $optionAttributeOptionAttribute, $languageAttribute);
	$optionAttributeOptionsAttribute = new Attribute("optionAttributeOptions", $wgOptionAttributeOptionsAttributeName, $optionAttributeOptionsStructure);
	
	global
		$definitionAttribute, $translatedTextAttribute, $classAttributesAttribute,
		$wgDefinitionAttributeName, $wgTranslatedTextAttributeName;
	
	if ($viewInformation->filterOnLanguage() && !$viewInformation->hasMetaDataAttributes())
		$translatedTextAttribute = new Attribute("translatedText", $wgTextAttributeName, "text");	
	else
		$translatedTextAttribute = new Attribute("translatedText", $wgTranslatedTextAttributeName, $translatedTextStructure);
		
	$definitionAttribute = new Attribute("definition", $wgDefinitionAttributeName, new Structure("definition", $translatedTextAttribute, $objectAttributesAttribute));

	global
		$classAttributesStructure,
	//	$classAttributeClassAttribute, 
		$classAttributeIdAttribute, $classAttributeAttributeAttribute, $classAttributeLevelAttribute, $classAttributeTypeAttribute,
		$wgClassAttributeAttributeAttributeName, $wgClassAttributeLevelAttributeName, 
		$wgClassAttributeTypeAttributeName, $wgClassAttributesAttributeName, $wgClassAttributesAttributeId;
	
	$classAttributeIdAttribute = new Attribute("classAttributeId", "Class attribute identifier", "object-id");
	$classAttributeAttributeAttribute = new Attribute("classAttributeAttribute", $wgClassAttributeAttributeAttributeName, $definedMeaningReferenceStructure);
	$classAttributeLevelAttribute = new Attribute("classAttributeLevel", $wgClassAttributeLevelAttributeName, $definedMeaningReferenceStructure);
	$classAttributeTypeAttribute = new Attribute("classAttributeType", $wgClassAttributeTypeAttributeName, "short-text");
	$classAttributesStructure = new Structure("class-attributes", $classAttributeIdAttribute, $classAttributeAttributeAttribute, $classAttributeLevelAttribute, $classAttributeTypeAttribute, $optionAttributeOptionsAttribute);
	$classAttributesAttribute = new Attribute("classAttributes", $wgClassAttributesAttributeName, $classAttributesStructure);
	
	global
		$definedMeaningAttribute, $wgDefinedMeaningAttributeName;

	$definedMeaningAttribute = new Attribute("definedMeaning", $wgDefinedMeaningAttributeName, 
		new Structure(
			"defined-meaning",
			$definedMeaningCompleteDefiningExpressionAttribute,
			$definitionAttribute, 
			$classAttributesAttribute, 
			$alternativeDefinitionsAttribute, 
			$synonymsAndTranslationsAttribute, 
			$relationsAttribute, 
			$reciprocalRelationsAttribute, 
			$classMembershipAttribute, 
			$collectionMembershipAttribute, 
			$definedMeaningAttributesAttribute
		)
	);

	global
		$expressionsAttribute, $expressionMeaningStructure, $expressionExactMeaningsAttribute, $expressionApproximateMeaningsAttribute,
		$wgExactMeaningsAttributeName, $wgApproximateMeaningsAttributeName;
		
	$expressionMeaningStructure = new Structure("expression-exact-meanings", $definedMeaningIdAttribute, $textAttribute, $definedMeaningAttribute); 	
	$expressionExactMeaningsAttribute = new Attribute("expressionExactMeanings", $wgExactMeaningsAttributeName, $expressionMeaningStructure);
	$expressionApproximateMeaningsAttribute = new Attribute("expressionApproximateMeanings", $wgApproximateMeaningsAttributeName, $expressionMeaningStructure);
	
	global
		$expressionMeaningsAttribute, $expressionMeaningsStructure, 
		$wgExpressionMeaningsAttributeName, $wgExpressionsAttributeName,
		$expressionsStructure;
	
	$expressionMeaningsStructure = new Structure("expression-meanings", $expressionExactMeaningsAttribute, $expressionApproximateMeaningsAttribute);
	$expressionMeaningsAttribute = new Attribute("expressionMeanings", $wgExpressionMeaningsAttributeName, $expressionMeaningsStructure);
	
	$expressionsStructure = new Structure("expressions", $expressionIdAttribute, $expressionAttribute, $expressionMeaningsAttribute);
	$expressionsAttribute = new Attribute("expressions", $wgExpressionsAttributeName, $expressionsStructure);
	
	global
		$objectIdAttribute, $objectAttributesStructure, $wgAnnotationAttributeName;
	
	$objectIdAttribute = new Attribute("objectId", "Object identifier", "object-id");
	$objectAttributesStructure = new Structure("object-attributes", $objectIdAttribute, $textAttributeValuesAttribute, $translatedTextAttributeValuesAttribute, $linkAttributeValuesAttribute, $optionAttributeValuesAttribute);
	$objectAttributesAttribute->setAttributeType($objectAttributesStructure);
	$definedMeaningAttributesAttribute->setAttributeType($objectAttributesStructure);
	
	$annotatedAttributes = array(
		$definitionAttribute, 
		$synonymsAndTranslationsAttribute, 
		$relationsAttribute,
		$reciprocalRelationsAttribute,
		$objectAttributesAttribute,
		$textAttributeValuesAttribute,
		$linkAttributeValuesAttribute,
		$translatedTextAttributeValuesAttribute,
		$optionAttributeValuesAttribute,
		$definedMeaningAttribute
	);
	
	foreach ($viewInformation->getPropertyToColumnFilters() as $propertyToColumnFilter) {
		$attribute = $propertyToColumnFilter->getAttribute();
		$attribute->setAttributeType($objectAttributesStructure);
		
		foreach ($annotatedAttributes as $annotatedAttribute)  		
			$annotatedAttribute->type->addAttribute($attribute);
	}

}
*/

class OmegaWikiAttributes extends AttributeSet {
	function __construct(ViewInformation $viewInformation) {
		$this->hardValues($viewInformation);
	}

	/** Hardcoded schema for now. Later refactor to load from file or DB 
	 * 
	 * Naming: keys are previous name minus -"Attribute"
	 * 	(-"Structure" is retained, -"Attributes" is retained)
	*/
	private function hardValues($viewInformation) {
	
		$t=$this; #<-keep things short to declutter
	
		$t->language = new Attribute("language", wfMsg("Language"), "language");
		$t->spelling = new Attribute("spelling", wfMsg("Spelling"), "spelling");
		$t->text = new Attribute("text", wfMsg("Text"), "text");
		$t->definedMeaningAttributes = new Attribute("definedMeaningAttributes", wfMsg("DefinedMeaningAttributes"), "will-be-specified-below");
		$t->objectAttributes = new Attribute("objectAttributes", wfMsg("Annotation"), "will-be-specified-below");
		$t->expressionId = new Attribute("expressionId", "Expression Id", "expression-id");
		$t->identicalMeaning = new Attribute("indenticalMeaning", wfMsg("IdenticalMeaning"), "boolean");
		
		if ($viewInformation->filterOnLanguage()) 
			$t->expression = new Attribute("expression", wfMsg("Spelling"), "spelling");
		else {
			$t->expressionStructure = new Structure("expression", $t->language, $t->spelling);
			$t->expression = new Attribute("expression", wfMsg("Expression"), $t->expressionStructure);
		}
		
		$t->definedMeaningId = new Attribute("defined-meaning-id", "Defined meaning identifier", "defined-meaning-id");
		$t->definedMeaningDefiningExpression = new Attribute("defined-meaning-defining-expression", "Defined meaning defining expression", "short-text");
		$t->definedMeaningCompleteDefiningExpressionStructure = 
			new Structure("defined-meaning-full-defining-expression",
				  $t->definedMeaningDefiningExpression,
				  $t->expressionId,
				  $t->language
			);
		#try this
		$t->definedMeaningCompleteDefiningExpressionStructure->setStructureType("expression");
		$t->definedMeaningCompleteDefiningExpression =new Attribute("definedMeaningCompleteDefiningExpression", "Defining expression", $t->definedMeaningCompleteDefiningExpressionStructure);
		global
			  $definedMeaningReferenceType;
			
		$t->definedMeaningLabel = new Attribute("defined-meaning-label", "Defined meaning label", "short-text");
		$t->definedMeaningReferenceStructure = new Structure("defined-meaning", $t->definedMeaningId, $t->definedMeaningLabel, $t->definedMeaningDefiningExpression);
		$definedMeaningReferenceType = $t->definedMeaningReferenceStructure;
		$t->definedMeaningReference = new Attribute("definedMeaningReference", wfMsg("DefinedMeaningReference"), $definedMeaningReferenceType);
		$t->collectionId = new Attribute("collectionId", "Collection", "collection-id");
		$t->collectionMeaning = new Attribute("collectionMeaning", wfMsg("Collection"), $t->definedMeaningReferenceStructure);
		$t->sourceIdentifier = new Attribute("sourceIdentifier", wfMsg("SourceIdentifier"), "short-text");
		$t->gotoSourceStructure = new Structure("goto-source",$t->collectionId, $t->sourceIdentifier);
		$t->gotoSource = new Attribute("gotoSource", wfMsg("GotoSource"), $t->gotoSourceStructure); 
		$t->collectionMembershipStructure = new Structure("collection-membership",$t->collectionId, $t->collectionMeaning, $t->sourceIdentifier);
		$t->collectionMembership = new Attribute("collectionMembership", wfMsg("CollectionMembership"), $t->collectionMembershipStructure);
		$t->classMembershipId = new Attribute("classMembershipId", "Class membership id", "integer");	 
		$t->class = new Attribute("class", "Class", $t->definedMeaningReferenceStructure);
		$t->classMembershipStructure = new Structure("class-membership", $t->classMembershipId, $t->class);
		$t->classMembership = new Attribute("classMembership", wfMsg("ClassMembership"), $t->classMembershipStructure);
		
		global
			 $wgPossiblySynonymousAttributeId;
			 
		$t->possiblySynonymousId = new Attribute("possiblySynonymousId", "Possibly synonymous id", "integer");	 
		$t->possibleSynonym = new Attribute("possibleSynonym", wfMsg("PossibleSynonym"), $t->definedMeaningReferenceStructure);
		# Bug found ... This never worked before: (!)
		#$t->possiblySynonymousStructure = new Structure("possibly-synonymous", $t->possiblySynonymousId, $t->possiblySynonymous);
		$t->possiblySynonymousStructure = new Structure("possibly-synonymous", $t->possiblySynonymousId, $t->possibleSynonym);
		$t->possiblySynonymous = new Attribute("possiblySynonymous", wfMsg("PossiblySynonymous"), $t->possiblySynonymousStructure);

		global
			$relationTypeType;
		
		$t->relationId = new Attribute("relationId", "Relation identifier", "object-id");
		$t->relationType = new Attribute("relationType", wfMsg("RelationType"), $t->definedMeaningReferenceStructure); 
		$t->otherDefinedMeaning = new Attribute("otherDefinedMeaning", wfMsg("OtherDefinedMeaning"), $definedMeaningReferenceType);
		
		global
		    $wgRelationsAttributeId, $wgIncomingRelationsAttributeId ;
			
		$t->relationStructure = new Structure("relations", $t->relationId, $t->relationType, $t->otherDefinedMeaning, $t->objectAttributes);
		$t->relations = new Attribute("relations", wfMsg("Relations"), $t->relationStructure);
		$t->reciprocalRelations = new Attribute("reciprocalRelations", wfMsg("IncomingRelations"), $t->relationStructure);
		$t->translatedTextId = new Attribute("translatedTextId", "Translated text ID", "integer");	
		$t->translatedTextStructure = new Structure("translated-text", $t->language, $t->text);	
		
		$t->definitionId = new Attribute("definitionId", "Definition identifier", "integer");

		if ($viewInformation->filterOnLanguage() && !$viewInformation->hasMetaDataAttributes())
			$t->alternativeDefinition = new Attribute("alternativeDefinition", wfMsg("AlternativeDefinition"), "text");
		else
			$t->alternativeDefinition = new Attribute("alternativeDefinition", wfMsg("AlternativeDefinition"), $t->translatedTextStructure);
		
		$t->source = new Attribute("sourceId", wfMsg("Source"), $definedMeaningReferenceType);
		
		global
			$wgAlternativeDefinitionsAttributeId;
			
		$t->alternativeDefinitionsStructure =  new Structure("alternative-definitions", $t->definitionId, $t->alternativeDefinition, $t->source);
		$t->alternativeDefinitions = new Attribute("alternativeDefinitions", wfMsg("AlternativeDefinitions"), $t->alternativeDefinitionsStructure);
		
		global
			$wgSynonymsAndTranslationsAttributeId;
		
		if ($viewInformation->filterOnLanguage())
			$synonymsAndTranslationsCaption = wfMsg("Synonyms");
		else
			$synonymsAndTranslationsCaption = wfMsg("SynonymsAndTranslations");

		$t->attributeObject = new Attribute("attributeObject", "Attribute object", "object-id");

		$t->syntransId = new Attribute("syntransId", "$synonymsAndTranslationsCaption identifier", "integer");
		$t->synonymsTranslationsStructure = new Structure("synonyms-translations", $t->syntransId, $t->expression, $t->identicalMeaning, $t->objectAttributes);
		$t->synonymsAndTranslations = new Attribute("synonymsAndTranslations", "$synonymsAndTranslationsCaption", $t->synonymsTranslationsStructure);
		$t->translatedTextAttributeId = new Attribute("translatedTextAttributeId", "Attribute identifier", "object-id");
		$t->translatedTextAttribute = new Attribute("translatedTextAttribute", wfMsg("TranslatedTextAttribute"), $definedMeaningReferenceType);
		$t->translatedTextValueId = new Attribute("translatedTextValueId", "Translated text value identifier", "translated-text-value-id");
		
		if ($viewInformation->filterOnLanguage() && !$viewInformation->hasMetaDataAttributes())
			$t->translatedTextValue = new Attribute("translatedTextValue", wfMsg("TranslatedTextAttributeValue"), "text");
		else
			$t->translatedTextValue = new Attribute("translatedTextValue", wfMsg("TranslatedTextAttributeValue"), $t->translatedTextStructure);
		
		$t->translatedTextAttributeValuesStructure = new Structure("translated-text-attribute-values",$t->translatedTextAttributeId, $t->attributeObject, $t->translatedTextAttribute, $t->translatedTextValueId, $t->translatedTextValue, $t->objectAttributes);
		$t->translatedTextAttributeValues = new Attribute("translatedTextAttributeValues", wfMsg("TranslatedTextAttributeValues"), $t->translatedTextAttributeValuesStructure);
		$t->textAttributeId = new Attribute("textAttributeId", "Attribute identifier", "object-id");
		$t->textAttributeObject = new Attribute("textAttributeObject", "Attribute object", "object-id");
		$t->textAttribute = new Attribute("textAttribute", wfMsg("TextAttribute"), $t->definedMeaningReferenceStructure);
		$t->textAttributeValuesStructure = new Structure("text-attribute-values", $t->textAttributeId, $t->textAttributeObject, $t->textAttribute, $t->text, $t->objectAttributes);	
		$t->textAttributeValues = new Attribute("textAttributeValues", wfMsg("TextAttributeValues"), $t->textAttributeValuesStructure);
		$t->linkLabel = new Attribute("linkLabel", "Label", "short-text"); 
		$t->linkURL = new Attribute("linkURL", "URL", "url");
		$t->link = new Attribute("link", "Link", new Structure($t->linkLabel, $t->linkURL));
		
		$t->linkAttributeId = new Attribute("linkAttributeId", "Attribute identifier", "object-id");
		$t->linkAttributeObject = new Attribute("linkAttributeObject", "Attribute object", "object-id");
		$t->linkAttribute = new Attribute("linkAttribute", wfMsg("LinkAttribute"), $t->definedMeaningReferenceStructure);
		$t->linkAttributeValuesStructure = new Structure("link-attribute-values", $t->linkAttributeId, $t->linkAttributeObject, $t->linkAttribute, $t->link, $t->objectAttributes);	
		$t->linkAttributeValues = new Attribute("linkAttributeValues", wfMsg("LinkAttributeValues"), $t->linkAttributeValuesStructure);
		$t->optionAttributeId = new Attribute("optionAttributeId", "Attribute identifier", "object-id");
		$t->optionAttributeObject = new Attribute("optionAttributeObject", "Attribute object", "object-id");
		$t->optionAttribute = new Attribute("optionAttribute", wfMsg("OptionAttribute"), $definedMeaningReferenceType);
		$t->optionAttributeOption = new Attribute("optionAttributeOption", wfMsg("OptionAttributeOption"), $definedMeaningReferenceType);
		$t->optionAttributeValuesStructure = new Structure("optionAttributeValues", $t->optionAttributeId, $t->optionAttribute, $t->optionAttributeObject, $t->optionAttributeOption, $t->objectAttributes);
		$t->optionAttributeValues = new Attribute("optionAttributeValues", wfMsg("OptionAttributeValues"), $t->optionAttributeValuesStructure);
		$t->optionAttributeOptionId = new Attribute("optionAttributeOptionId", "Option identifier", "object-id");
		$t->optionAttributeOptionsStructure = new Structure("option-attribute-options", $t->optionAttributeOptionId, $t->optionAttribute, $t->optionAttributeOption, $t->language);
		$t->optionAttributeOptions = new Attribute("optionAttributeOptions", wfMsg("OptionAttributeOptions"), $t->optionAttributeOptionsStructure);
		
		if ($viewInformation->filterOnLanguage() && !$viewInformation->hasMetaDataAttributes())
			$t->translatedText = new Attribute("translatedText", wfMsg("Text"), "text");	
		else
			$t->translatedText = new Attribute("translatedText", wfMsg("TranslatedText"), $t->translatedTextStructure);
			
		$t->definition = new Attribute("definition", wfMsg("Definition"), new Structure("definition", $t->translatedText, $t->objectAttributes));

		global
			$wgClassAttributesAttributeId;
		
		$t->classAttributeId = new Attribute("classAttributeId", "Class attribute identifier", "object-id");
		$t->classAttributeAttribute = new Attribute("classAttributeAttribute", wfMsg("ClassAttributeAttribute"), $t->definedMeaningReferenceStructure);
		$t->classAttributeLevel = new Attribute("classAttributeLevel", wfMsg("ClassAttributeLevel"), $t->definedMeaningReferenceStructure);
		$t->classAttributeType = new Attribute("classAttributeType", wfMsg("ClassAttributeType"), "short-text");
		$t->classAttributesStructure = new Structure("class-attributes", $t->classAttributeId, $t->classAttributeAttribute, $t->classAttributeLevel, $t->classAttributeType, $t->optionAttributeOptions);
		$t->classAttributes = new Attribute("classAttributes", wfMsg("ClassAttributes"), $t->classAttributesStructure);

		$t->definedMeaning = new Attribute("definedMeaning", wfMsg("DefinedMeaning"), 
			new Structure(
				"defined-meaning",
				$t->definedMeaningCompleteDefiningExpression,
				$t->definition, 
				$t->classAttributes, 
				$t->alternativeDefinitions, 
				$t->synonymsAndTranslations, 
				$t->relations, 
				$t->reciprocalRelations, 
				$t->classMembership, 
				$t->collectionMembership, 
				$t->definedMeaningAttributes
			)
		);

		$t->expressionMeaningStructure = new Structure("expression-exact-meanings", $t->definedMeaningId, $t->text, $t->definedMeaning); 	
		$t->expressionExactMeanings = new Attribute("expressionExactMeanings", wfMsg("ExactMeanings"), $t->expressionMeaningStructure);
		$t->expressionApproximateMeanings = new Attribute("expressionApproximateMeanings", wfMsg("ApproximateMeanings"), $t->expressionMeaningStructure);
		# bug found here also: $t->expressionAoproximateMeaning_S_	
		$t->expressionMeaningsStructure = new Structure("expression-meanings", $t->expressionExactMeanings, $t->expressionApproximateMeanings);
		$t->expressionMeanings = new Attribute("expressionMeanings", wfMsg("ExpressionMeanings"), $t->expressionMeaningsStructure);
		$t->expressionsStructure = new Structure("expressions", $t->expressionId, $t->expression, $t->expressionMeanings);
		$t->expressions = new Attribute("expressions", wfMsg("Expressions"), $t->expressionsStructure);
		$t->objectId = new Attribute("objectId", "Object identifier", "object-id");
		$t->objectAttributesStructure = new Structure("object-attributes", $t->objectId, $t->textAttributeValues, $t->translatedTextAttributeValues, $t->optionAttributeValues, $t->linkAttributeValues);
		$t->objectAttributes->setAttributeType($t->objectAttributesStructure);
		$t->definedMeaningAttributes->setAttributeType($t->objectAttributesStructure);
		
		$t->annotatedAttributes = array(
			$t->definedMeaning,
			$t->definition, 
			$t->synonymsAndTranslations, 
			$t->relations,
			$t->reciprocalRelations,
			$t->objectAttributes,
			$t->textAttributeValues,
			$t->translatedTextAttributeValues,
			$t->optionAttributeValues
		);
		
		foreach ($viewInformation->getPropertyToColumnFilters() as $propertyToColumnFilter) {
			$attribute = $propertyToColumnFilter->getAttribute();
			$attribute->setAttributeType($t->objectAttributesStructure);
			
			foreach ($t->annotatedAttributes as $annotatedAttribute) 		
				$annotatedAttribute->type->addAttribute($attribute);
		}
	}
}