<?php

global
	$wgAlternativeDefinitionAttributeName,
	$wgAlternativeDefinitionsAttributeName,
	$wgAnnotationAttributeName,
	$wgApproximateMeaningsAttributeName,
	$wgAuthorityAttributeName,
	$wgClassAttributeAttributeAttributeName,
	$wgClassAttributesAttributeName,
	$wgClassAttributeLevelAttributeName,
	$wgClassAttributeTypeAttributeName,
	$wgClassMembershipAttributeName,
	$wgCollectionAttributeName,
	$wgCollectionMembershipAttributeName,
	$wgDefinedMeaningAttributesAttributeName,
	$wgDefinedMeaningAttributeName,
	$wgDefinedMeaningReferenceAttributeName,
	$wgDefinitionAttributeName,
	$wgLanguageAttributeName,
	$wgExactMeaningsAttributeName,
	$wgExpressionAttributeName,
	$wgExpressionMeaningsAttributeName,
	$wgExpressionsAttributeName,
	$wgGotoSourceAttributeName,
	$wgIdenticalMeaningAttributeName,
	$wgIncomingRelationsAttributeName, 
	$wgLevelAnnotationAttributeName,
	$wgOptionAttributeOptionAttributeName,
	$wgOptionAttributeOptionsAttributeName,
	$wgOptionAttributeValuesAttributeName,
	$wgOtherDefinedMeaningAttributeName,
	$wgPopupAnnotationName, 
	$wgPossibleSynonymAttributeName,
	$wgPossiblySynonymousAttributeName,
	$wgRelationsAttributeName, 
	$wgRelationTypeAttributeName, 
	$wgSourceAttributeName,
	$wgSourceIdentifierAttributeName,
	$wgSpellingAttributeName,
	$wgSynonymsAttributeName,
	$wgSynonymsAndTranslationsAttributeName,
	$wgTextAttributeAttributeName,
	$wgTextAttributeName,
	$wgTextAttributeValuesAttributeName,
	$wgTranslatedTextAttributeAttributeName,
	$wgTranslatedTextAttributeName,
	$wgTranslatedTextAttributeValueAttributeName,
	$wgTranslatedTextAttributeValuesAttributeName,
	$wgUrlAttributeAttributeName,
	$wgUrlAttributeValuesAttributeName;

$wgAlternativeDefinitionAttributeName = "Alternative definition";
$wgAlternativeDefinitionsAttributeName = "Alternative definitions";	
$wgAnnotationAttributeName = "Annotation";
$wgApproximateMeaningsAttributeName = "Approximate meanings";	
$wgAuthorityAttributeName = "Authority";
$wgClassAttributeAttributeAttributeName = "Attribute";
$wgClassAttributesAttributeName = "Class attributes";
$wgClassAttributeLevelAttributeName = "Level";
$wgClassAttributeTypeAttributeName = "Type";
$wgClassMembershipAttributeName = "Class membership";
$wgCollectionAttributeName = "Collection";
$wgCollectionMembershipAttributeName = "Collection membership";
$wgDefinitionAttributeName = "Definition";
$wgDefinedMeaningAttributesAttributeName = "Annotation";
$wgDefinedMeaningAttributeName = "Defined meaning";
$wgDefinedMeaningReferenceAttributeName = "Defined meaning";
$wgExactMeaningsAttributeName = "Exact meanings";
$wgExpressionAttributeName = "Expression";
$wgExpressionMeaningsAttributeName = "Expression meanings";
$wgExpressionsAttributeName = "Expressions";
$wgIdenticalMeaningAttributeName = "Identical meaning?";
$wgIncomingRelationsAttributeName = "Incoming relations";
$wgGotoSourceAttributeName = "Go to source";
$wgLanguageAttributeName = "Language";
$wgLevelAnnotationAttributeName = "Annotation";
$wgOptionAttributeAttributeName = "Property";
$wgOptionAttributeOptionAttributeName = "Option";
$wgOptionAttributeOptionsAttributeName = "Options";
$wgOptionAttributeValuesAttributeName = "Option properties";
$wgOtherDefinedMeaningAttributeName = "Other defined meaning";
$wgPopupAnnotationName = "Annotation";
$wgPossibleSynonymAttributeName = "Possible synonym";
$wgPossiblySynonymousAttributeName = "Possibly synonymous";
$wgRelationsAttributeName = "Relations";
$wgRelationTypeAttributeName = "Relation type";
$wgSpellingAttributeName = "Spelling";
$wgSynonymsAttributeName = "Synonyms"; 
$wgSynonymsAndTranslationsAttributeName = "Synonyms and translations";
$wgSourceAttributeName = "Source";
$wgSourceIdentifierAttributeName = "Source identifier";
$wgTextAttributeAttributeName = "Property";
$wgTextAttributeName = "Text";
$wgTextAttributeValuesAttributeName = "String properties";
$wgTranslatedTextAttributeAttributeName = "Property";
$wgTranslatedTextAttributeName = "Translated text";
$wgTranslatedTextAttributeValueAttributeName = "Text";
$wgTranslatedTextAttributeValuesAttributeName = "Text properties";
$wgUrlAttributeAttributeName = "Property";
$wgUrlAttributeValuesAttributeName = "URL properties";

global
	$wgAlternativeDefinitionsAttributeId,
	$wgClassAttributesAttributeId,
	$wgClassMembershipAttributeId,
	$wgCollectionMembershipAttributeId,
	$wgDefinedMeaningAttributesAttributeId,
	$wgDefinitionAttributeId,
	$wgIncomingRelationsAttributeId,
	$wgPossiblySynonymousAttributeId,
	$wgRelationsAttributeId, 
	$wgSynonymsAndTranslationsAttributeId;
	
$wgAlternativeDefinitionsAttributeId = "alternative-definitions";
$wgClassAttributesAttributeId = "class-attributes";
$wgClassMembershipAttributeId = "class-membership";
$wgCollectionMembershipAttributeId = "collection-membership";
$wgDefinedMeaningAttributesAttributeId = "defined-meaning-attributes";
$wgDefinitionAttributeId = "definition";
$wgIncomingRelationsAttributeId = "reciprocal-relations";
$wgPossiblySynonymousAttributeId = "possibly-synonymous";
$wgRelationsAttributeId = "relations"; 
$wgSynonymsAndTranslationsAttributeId = "synonyms-translations";

global
	$wgDefinedMeaningAttributesOrder;
	
$wgDefinedMeaningAttributesOrder = array(
	$wgDefinitionAttributeId,
	$wgClassAttributesAttributeId,
	$wgAlternativeDefinitionsAttributeId,
	$wgSynonymsAndTranslationsAttributeId,
	$wgPossiblySynonymousAttributeId,
	$wgRelationsAttributeId, 
	$wgIncomingRelationsAttributeId,
	$wgClassMembershipAttributeId,
	$wgCollectionMembershipAttributeId,
	$wgDefinedMeaningAttributesAttributeId
);

require_once("GotoSourceTemplate.php");

global
	$wgGotoSourceTemplates;

$wgGotoSourceTemplates = array();	// Map of collection id => GotoSourceTemplate

global
	$wgDefinedMeaningPageTitlePrefix,
	$wgExpressionPageTitlePrefix;
	
$wgDefinedMeaningPageTitlePrefix = "";
$wgExpressionPageTitlePrefix = "Multiple meanings";
	
?>