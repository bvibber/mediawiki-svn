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
	$viewInformation->setAttributeSet(new OmegaWikiAttributes($viewInformation)); 
}

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
			
		$t->definedMeaningLabel = new Attribute("definedMeaningLabel", "Defined meaning label", "short-text");
		$t->definedMeaningReferenceStructure = new Structure("definedMeaning", $t->definedMeaningId, $t->definedMeaningLabel, $t->definedMeaningDefiningExpression);
		$definedMeaningReferenceType = $t->definedMeaningReferenceStructure;
		$t->definedMeaningReference = new Attribute("definedMeaningReference", wfMsg("DefinedMeaningReference"), $definedMeaningReferenceType);
		$t->collectionId = new Attribute("collectionId", "Collection", "collection-id");
		$t->collectionMeaning = new Attribute("collectionMeaning", wfMsg("Collection"), $t->definedMeaningReferenceStructure);
		$t->sourceIdentifier = new Attribute("sourceIdentifier", wfMsg("SourceIdentifier"), "short-text");
		$t->gotoSourceStructure = new Structure("gotoSource", $t->collectionId, $t->sourceIdentifier);
		$t->gotoSource = new Attribute("gotoSource", wfMsg("GotoSource"), $t->gotoSourceStructure); 
		$t->collectionMembershipStructure = new Structure("collectionMembership",$t->collectionId, $t->collectionMeaning, $t->sourceIdentifier);
		$t->collectionMembership = new Attribute("collectionMembership", wfMsg("CollectionMembership"), $t->collectionMembershipStructure);
		$t->classMembershipId = new Attribute("classMembershipId", "Class membership id", "integer");	 
		$t->class = new Attribute("class", "Class", $t->definedMeaningReferenceStructure);
		$t->classMembershipStructure = new Structure("classMembership", $t->classMembershipId, $t->class);
		$t->classMembership = new Attribute("classMembership", wfMsg("ClassMembership"), $t->classMembershipStructure);
		
		global
			 $wgPossiblySynonymousAttributeId;
			 
		$t->possiblySynonymousId = new Attribute("possiblySynonymousId", "Possibly synonymous id", "integer");	 
		$t->possibleSynonym = new Attribute("possibleSynonym", wfMsg("PossibleSynonym"), $t->definedMeaningReferenceStructure);
		$t->possiblySynonymousStructure = new Structure("possiblySynonymous", $t->possiblySynonymousId, $t->possibleSynonym);
		$t->possiblySynonymous = new Attribute("possiblySynonymous", wfMsg("PossiblySynonymous"), $t->possiblySynonymousStructure);

		global
			$relationTypeType;
		
		$t->relationId = new Attribute("relationId", "Relation identifier", "object-id");
		$t->relationType = new Attribute("relationType", wfMsg("RelationType"), $t->definedMeaningReferenceStructure); 
		$t->otherDefinedMeaning = new Attribute("otherDefinedMeaning", wfMsg("OtherDefinedMeaning"), $definedMeaningReferenceType);
		
		global
		    $wgRelationsAttributeId, $wgIncomingRelationsAttributeId;
			
		$t->relationStructure = new Structure("relations", $t->relationId, $t->relationType, $t->otherDefinedMeaning, $t->objectAttributes);
		$t->relations = new Attribute("relations", wfMsg("Relations"), $t->relationStructure);
		$t->reciprocalRelations = new Attribute("reciprocalRelations", wfMsg("IncomingRelations"), $t->relationStructure);
		$t->translatedTextId = new Attribute("translatedTextId", "Translated text ID", "integer");	
		$t->translatedTextStructure = new Structure("translatedText", $t->language, $t->text);	
		
		$t->definitionId = new Attribute("definitionId", "Definition identifier", "integer");

		if ($viewInformation->filterOnLanguage() && !$viewInformation->hasMetaDataAttributes())
			$t->alternativeDefinition = new Attribute("alternativeDefinition", wfMsg("AlternativeDefinition"), "text");
		else
			$t->alternativeDefinition = new Attribute("alternativeDefinition", wfMsg("AlternativeDefinition"), $t->translatedTextStructure);
		
		$t->source = new Attribute("sourceId", wfMsg("Source"), $definedMeaningReferenceType);
		
		global
			$wgAlternativeDefinitionsAttributeId;
			
		$t->alternativeDefinitionsStructure =  new Structure("alternativeDefinitions", $t->definitionId, $t->alternativeDefinition, $t->source);
		$t->alternativeDefinitions = new Attribute("alternativeDefinitions", wfMsg("AlternativeDefinitions"), $t->alternativeDefinitionsStructure);
		
		global
			$wgSynonymsAndTranslationsAttributeId;
		
		if ($viewInformation->filterOnLanguage())
			$synonymsAndTranslationsCaption = wfMsg("Synonyms");
		else
			$synonymsAndTranslationsCaption = wfMsg("SynonymsAndTranslations");

		$t->syntransId = new Attribute("syntransId", "$synonymsAndTranslationsCaption identifier", "integer");
		$t->synonymsTranslationsStructure = new Structure("synonymsTranslations", $t->syntransId, $t->expression, $t->identicalMeaning, $t->objectAttributes);
		$t->synonymsAndTranslations = new Attribute("synonymsAndTranslations", "$synonymsAndTranslationsCaption", $t->synonymsTranslationsStructure);
		
		$t->attributeObject = new Attribute("attributeObject", "Attribute object", "object-id");
		$t->attribute = new Attribute("attribute", wfMsg("Attribute"), $definedMeaningReferenceType);
		$t->valueId = new Attribute("valueId", "Value ID", "object-id");

		$t->translatedTextValueId = new Attribute("translatedTextValueId", "Translated text value identifier", "translated-text-value-id");
		
		if ($viewInformation->filterOnLanguage() && !$viewInformation->hasMetaDataAttributes())
			$t->translatedTextValue = new Attribute("translatedTextValue", wfMsg("TranslatedTextAttributeValue"), "text");
		else
			$t->translatedTextValue = new Attribute("translatedTextValue", wfMsg("TranslatedTextAttributeValue"), $t->translatedTextStructure);
		
		$t->translatedTextAttributeValuesStructure = new Structure("translated-text-attribute-values", $t->valueId, $t->attributeObject, $t->attribute, $t->translatedTextValueId, $t->translatedTextValue, $t->objectAttributes);
		$t->translatedTextAttributeValues = new Attribute("translatedTextAttributeValues", wfMsg("TranslatedTextAttributeValues"), $t->translatedTextAttributeValuesStructure);
		
		$t->textAttributeValuesStructure = new Structure("textAttributeValues", $t->valueId, $t->attributeObject, $t->attribute, $t->text, $t->objectAttributes);	
		$t->textAttributeValues = new Attribute("textAttributeValues", wfMsg("TextAttributeValues"), $t->textAttributeValuesStructure);
		
		$t->linkLabel = new Attribute("linkLabel", "Label", "short-text"); 
		$t->linkURL = new Attribute("linkURL", "URL", "url");
		$t->link = new Attribute("link", "Link", new Structure($t->linkLabel, $t->linkURL));
		$t->linkAttributeValuesStructure = new Structure("link-attribute-values", $t->valueId, $t->attributeObject, $t->attribute, $t->link, $t->objectAttributes);	
		$t->linkAttributeValues = new Attribute("linkAttributeValues", wfMsg("LinkAttributeValues"), $t->linkAttributeValuesStructure);
		
		$t->optionAttributeOption = new Attribute("optionAttributeOption", wfMsg("OptionAttributeOption"), $definedMeaningReferenceType);
		$t->optionAttributeValuesStructure = new Structure("optionAttributeValues", $t->valueId, $t->attribute, $t->attributeObject, $t->optionAttributeOption, $t->objectAttributes);
		$t->optionAttributeValues = new Attribute("optionAttributeValues", wfMsg("OptionAttributeValues"), $t->optionAttributeValuesStructure);
		$t->optionAttributeOptionId = new Attribute("optionAttributeOptionId", "Option identifier", "object-id");
		$t->optionAttributeOptionsStructure = new Structure("optionAttributeOptions", $t->optionAttributeOptionId, $t->attribute, $t->optionAttributeOption, $t->language);
		$t->optionAttributeOptions = new Attribute("optionAttributeOptions", wfMsg("OptionAttributeOptions"), $t->optionAttributeOptionsStructure);
		
		if ($viewInformation->filterOnLanguage() && !$viewInformation->hasMetaDataAttributes())
			$t->translatedText = new Attribute("translatedText", wfMsg("Text"), "text");	
		else
			$t->translatedText = new Attribute("translatedText", wfMsg("TranslatedText"), $t->translatedTextStructure);
			
		$t->definition = new Attribute("definition", wfMsg("Definition"), new Structure("definition", $t->translatedText, $t->objectAttributes));

		global
			$wgClassAttributesAttributeId;
		
		$t->classAttributeId = new Attribute("classAttributeId", "Class attribute identifier", "object-id");
		$t->classAttributeLevel = new Attribute("classAttributeLevel", wfMsg("ClassAttributeLevel"), $t->definedMeaningReferenceStructure);
		$t->classAttributeType = new Attribute("classAttributeType", wfMsg("ClassAttributeType"), "short-text");
		$t->classAttributesStructure = new Structure("classAttributes", $t->classAttributeId, $t->attribute, $t->classAttributeLevel, $t->classAttributeType, $t->optionAttributeOptions);
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

		$t->expressionMeaningStructure = new Structure("expressionExactMeanings", $t->definedMeaningId, $t->text, $t->definedMeaning); 	
		$t->expressionExactMeanings = new Attribute("expressionExactMeanings", wfMsg("ExactMeanings"), $t->expressionMeaningStructure);
		$t->expressionApproximateMeanings = new Attribute("expressionApproximateMeanings", wfMsg("ApproximateMeanings"), $t->expressionMeaningStructure);
		$t->expressionMeaningsStructure = new Structure("expressionMeanings", $t->expressionExactMeanings, $t->expressionApproximateMeanings);
		$t->expressionMeanings = new Attribute("expressionMeanings", wfMsg("ExpressionMeanings"), $t->expressionMeaningsStructure);
		$t->expressionsStructure = new Structure("expressions", $t->expressionId, $t->expression, $t->expressionMeanings);
		$t->expressions = new Attribute("expressions", wfMsg("Expressions"), $t->expressionsStructure);
		
		$t->objectId = new Attribute("objectId", "Object identifier", "object-id");
		$t->objectAttributesStructure = new Structure("objectAttributes", $t->objectId, $t->textAttributeValues, $t->translatedTextAttributeValues, $t->optionAttributeValues, $t->linkAttributeValues);
		$t->objectAttributes->setAttributeType($t->objectAttributesStructure);
		$t->definedMeaningAttributes->setAttributeType($t->objectAttributesStructure);
		
		$annotatedAttributes = array(
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
			
			foreach ($annotatedAttributes as $annotatedAttribute) 		
				$annotatedAttribute->type->addAttribute($attribute);
		}
	}
}