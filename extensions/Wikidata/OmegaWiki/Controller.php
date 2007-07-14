<?php

require_once("OmegaWikiAttributes.php");

interface UpdateController {
	public function add($keyPath, $record);
	public function remove($keyPath);
	public function update($keyPath, $record);
}

interface UpdateAttributeController {
	public function update($keyPath, $value);
}

interface PermissionController {
	public function allowUpdateOfAttribute($attribute);
	public function allowUpdateOfValue($idPath, $value);
	public function allowRemovalOfValue($idPath, $value);
}

class SimplePermissionController implements PermissionController {
	protected $allowUpdate;
	protected $allowRemove;
	
	public function __construct($allowUpdate, $allowRemove = true) {
		$this->allowUpdate = $allowUpdate;
		$this->allowRemove = $allowRemove;
	}	
	
	public function allowUpdateOfAttribute($attribute) {
		return $this->allowUpdate;
	}

	public function allowUpdateOfValue($idPath, $value) {
		return $this->allowUpdate;
	}
	
	public function allowRemovalOfValue($idPath, $value) {
		return $this->allowRemove;
	}
}

class DefinedMeaningDefinitionController implements UpdateController {
	public function add($keyPath, $record) {
		global
			$definedMeaningIdAttribute;
		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$languageId = $record->language;
		$text = $record->text;

		if ($languageId != 0 && $text != "")
			addDefinedMeaningDefinition($definedMeaningId, $languageId, $text);
	}

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$languageId = $keyPath->peek(0)->language;
		removeDefinedMeaningDefinition($definedMeaningId, $languageId);
	}

	public function update($keyPath, $record) {
		global
			$definedMeaningIdAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$languageId = $keyPath->peek(0)->language;
		$text = $record->text;

		if ($text != "")
			updateDefinedMeaningDefinition($definedMeaningId, $languageId, $text);
	}
}

class DefinedMeaningFilteredDefinitionController implements UpdateAttributeController {
	protected $filterLanguageId;
	
	public function __construct($filterLanguageId) {
		$this->filterLanguageId = $filterLanguageId;
	}

	public function update($keyPath, $value) {
		global
			$definedMeaningIdAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		
		if ($value != "")
			updateOrAddDefinedMeaningDefinition($definedMeaningId, $this->filterLanguageId, $value);
	}
}

class DefinedMeaningAlternativeDefinitionsController implements UpdateController {
	protected $filterLanguageId;
	
	public function __construct($filterLanguageId) {
		$this->filterLanguageId = $filterLanguageId;
	}
	
	public function add($keyPath, $record)  {
		global
			$definedMeaningIdAttribute, $alternativeDefinitionAttribute,
			$sourceAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$alternativeDefinition = $record->getAttributeValue($alternativeDefinitionAttribute);
		$sourceId = $record->getAttributeValue($sourceAttribute);

		if ($this->filterLanguageId == 0) {
			if ($alternativeDefinition->getRecordCount() > 0) {
				$definitionRecord = $alternativeDefinition->getRecord(0);
	
				$languageId = $definitionRecord->language;
				$text = $definitionRecord->text;
	
				if ($languageId != 0 && $text != '')
					addDefinedMeaningAlternativeDefinition($definedMeaningId, $languageId, $text, $sourceId);
			}
		}
		else if ($alternativeDefinition != '') 
			addDefinedMeaningAlternativeDefinition($definedMeaningId, $this->filterLanguageId, $alternativeDefinition, $sourceId);
	}

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $definitionIdAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$definitionId = $keyPath->peek(0)->getAttributeValue($definitionIdAttribute);
		removeDefinedMeaningAlternativeDefinition($definedMeaningId, $definitionId);
	}

	public function update($keyPath, $record) {
	}
}

class DefinedMeaningAlternativeDefinitionController implements UpdateController {
	public function add($keyPath, $record) {
		global
			$expressionIdAttribute, $definitionIdAttribute ;

		$definitionId = $keyPath->peek(0)->getAttributeValue($definitionIdAttribute);
		$languageId = $record->language;
		$text = $record->text;

		if ($languageId != 0 && $text != "")
			addTranslatedTextIfNotPresent($definitionId, $languageId, $text);
	}

	public function remove($keyPath) {
		global
			$definitionIdAttribute;

		$definitionId = $keyPath->peek(1)->getAttributeValue($definitionIdAttribute);
		$languageId = $keyPath->peek(0)->language;

		removeTranslatedText($definitionId, $languageId);
	}

	public function update($keyPath, $record) {
		global
			$definitionIdAttribute;

		$definitionId = $keyPath->peek(1)->getAttributeValue($definitionIdAttribute);
		$languageId = $keyPath->peek(0)->language;
		$text = $record->text;

		if ($text != "")
			updateTranslatedText($definitionId, $languageId, $text);
	}
}

class DefinedMeaningFilteredAlternativeDefinitionController implements UpdateAttributeController {
	protected $filterLanguageId;
	
	public function __construct($filterLanguageId) {
		$this->filterLanguageId = $filterLanguageId;
	}
	
	public function update($keyPath, $value) {
		global
			$definitionIdAttribute;

		$definitionId = $keyPath->peek(0)->getAttributeValue($definitionIdAttribute);

		if ($value != "")
			updateTranslatedText($definitionId, $this->filterLanguageId, $value);
	}
}

class SynonymTranslationController implements UpdateController {
	protected $filterLanguageId;
	
	public function __construct($filterLanguageId) {
		$this->filterLanguageId = $filterLanguageId;
	}
	
	public function add($keyPath, $record) {
		global
			$definedMeaningIdAttribute, $expressionAttribute, $spellingAttribute, $identicalMeaningAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$expressionValue = $record->getAttributeValue($expressionAttribute);
		
		if ($this->filterLanguageId == 0) {
			$languageId = $expressionValue->language;
			$spelling = $expressionValue->getAttributeValue($spellingAttribute);
		}
		else {
			$languageId	= $this->filterLanguageId;
			$spelling = $expressionValue;
		}
		
		$identicalMeaning = $record->getAttributeValue($identicalMeaningAttribute);

		if ($languageId != 0 && $spelling != '') {
			$expression = findOrCreateExpression($spelling, $languageId);
			$expression->assureIsBoundToDefinedMeaning($definedMeaningId, $identicalMeaning);
		}
	}

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $syntransIdAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$syntransId = $keyPath->peek(0)->getAttributeValue($syntransIdAttribute);
		removeSynonymOrTranslationWithId($syntransId);
	}

	public function update($keyPath, $record) {
		global
			$definedMeaningIdAttribute, $syntransIdAttribute, $identicalMeaningAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$syntransId = $keyPath->peek(0)->getAttributeValue($syntransIdAttribute);
		$identicalMeaning = $record->getAttributeValue($identicalMeaningAttribute);
		updateSynonymOrTranslationWithId($syntransId, $identicalMeaning);
	}
}

class ClassAttributesController implements UpdateController {
	public function add($keyPath, $record) {
		global
			$definedMeaningIdAttribute, $classAttributeLevelAttribute, $classAttributeAttributeAttribute, $classAttributeTypeAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$attributeLevelId = $record->getAttributeValue($classAttributeLevelAttribute);
		$attributeMeaningId = $record->getAttributeValue($classAttributeAttributeAttribute);
		$attributeType = $record->getAttributeValue($classAttributeTypeAttribute);

		if (($attributeLevelId != 0) && ($attributeMeaningId != 0))
			addClassAttribute($definedMeaningId, $attributeLevelId, $attributeMeaningId, $attributeType);
	}

	public function remove($keyPath) {
		global
			$classAttributeIdAttribute;
			
		$classAttributeId = $keyPath->peek(0)->getAttributeValue($classAttributeIdAttribute);
		removeClassAttributeWithId($classAttributeId);
	}

	public function update($keyPath, $record) {
	}	
}

class DefinedMeaningRelationController implements UpdateController {
	public function add($keyPath, $record) {
		global
			$definedMeaningIdAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$relationTypeId = $record->getAttributeValue($relationTypeAttribute);
		$otherDefinedMeaningId = $record->getAttributeValue($otherDefinedMeaningAttribute);

		if ($relationTypeId != 0 && $otherDefinedMeaningId != 0)
			addRelation($definedMeaningId, $relationTypeId, $otherDefinedMeaningId);
	}

	public function remove($keyPath) {
		global
			$relationIdAttribute;
			
		$relationId = $keyPath->peek(0)->getAttributeValue($relationIdAttribute);
		removeRelationWithId($relationId);
	}

	public function update($keyPath, $record) {
	}
}

class GroupedRelationTypeController implements UpdateController {
	protected $relationTypeId;
	protected $groupedRelationIdAttribute;
	protected $otherDefinedMeaningAttribute;
	
	public function __construct($relationTypeId, $groupedRelationIdAttribute, $otherDefinedMeaningAttribute) {
		$this->relationTypeId = $relationTypeId;
		$this->groupedRelationIdAttribute = $groupedRelationIdAttribute;
		$this->otherDefinedMeaningAttribute = $otherDefinedMeaningAttribute;
	}
	
	public function add($keyPath, $record) {
		global
			$definedMeaningIdAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$otherDefinedMeaningId = $record->getAttributeValue($this->otherDefinedMeaningAttribute);

		if ($otherDefinedMeaningId != 0)
			addRelation($definedMeaningId, $this->relationTypeId, $otherDefinedMeaningId);
	}

	public function remove($keyPath) {
		$relationId = $keyPath->peek(0)->getAttributeValue($this->groupedRelationIdAttribute);
		removeRelationWithId($relationId);
	}

	public function update($keyPath, $record) {
	}
}

class DefinedMeaningClassMembershipController implements UpdateController {
	public function add($keyPath, $record) {
		global
			$definedMeaningIdAttribute, $classAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$classId = $record->getAttributeValue($classAttribute);

		if ($classId != 0)
			addClassMembership($definedMeaningId, $classId);
	}

	public function remove($keyPath) {
//		global
//			$definedMeaningIdAttribute, $classAttribute;
//			
//		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);	
//		$classId = $keyPath->peek(0)->getAttributeValue($classAttribute);	
//
//		removeClassMembership($definedMeaningId, $classId);
		global
			$classMembershipIdAttribute;
			
		removeClassMembershipWithId($keyPath->peek(0)->getAttributeValue($classMembershipIdAttribute));
	}

	public function update($keyPath, $record) {
	}
}

class DefinedMeaningCollectionController implements UpdateController {
	public function add($keyPath, $record) {
		global
			$expressionIdAttribute, $definedMeaningIdAttribute, $collectionMeaningAttribute, $sourceIdentifierAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$collectionMeaningId = $record->getAttributeValue($collectionMeaningAttribute);
		$internalId = $record->getAttributeValue($sourceIdentifierAttribute);
		
		if ($collectionMeaningId != 0)
			addDefinedMeaningToCollectionIfNotPresent($definedMeaningId, $collectionMeaningId, $internalId);
	}

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $collectionIdAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$collectionId = $keyPath->peek(0)->getAttributeValue($collectionIdAttribute);

		removeDefinedMeaningFromCollection($definedMeaningId, $collectionId);
	}

	public function update($keyPath, $record) {
		global
			$definedMeaningIdAttribute, $collectionIdAttribute, $sourceIdentifierAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$collectionId = $keyPath->peek(0)->getAttributeValue($collectionIdAttribute);
		$sourceId = $record->getAttributeValue($sourceIdentifierAttribute);

//		if ($sourceId != "")
			updateDefinedMeaningInCollection($definedMeaningId, $collectionId, $sourceId);
	}
}

class ExpressionMeaningController implements UpdateController {
	protected $filterLanguageId;
	
	public function __construct($filterLanguageId) {
		$this->filterLanguageId = $filterLanguageId;
	}

	public function add($keyPath, $record) {
		global
			$expressionIdAttribute, $definedMeaningAttribute, $definitionAttribute, $translatedTextAttribute;

		$definition = $record->getAttributeValue($definedMeaningAttribute)->getAttributeValue($definitionAttribute);
		$translatedContent = $definition->getAttributeValue($translatedTextAttribute);
		$expressionId = $keyPath->peek(0)->getAttributeValue($expressionIdAttribute);

		if ($this->filterLanguageId == 0) {
			if ($translatedContent->getRecordCount() > 0) {
				$definitionRecord = $translatedContent->getRecord(0);
	
				$text = $definitionRecord->text;
				$languageId = $definitionRecord->language;
	
				if ($languageId != 0 && $text != "")  
					createNewDefinedMeaning($expressionId, $languageId, $text);
			}
		}
		else if ($translatedContent != "") 
			createNewDefinedMeaning($expressionId, $this->filterLanguageId, $translatedContent);
	}

	public function remove($keyPath) {
	}

	public function update($keyPath, $record) {
	}
}

class ExpressionController implements UpdateController {
	protected $spelling;
	protected $filterLanguageId;

	public function __construct($spelling, $filterLanguageId) {
		$this->spelling = $spelling;
		$this->filterLanguageId = $filterLanguageId;
	}

	public function add($keyPath, $record) {
		global
			$expressionAttribute, $expressionMeaningsAttribute, $expressionExactMeaningsAttribute, 
			$definedMeaningAttribute, $definitionAttribute, 
			$translatedTextAttribute;

		if ($this->filterLanguageId == 0)
			$expressionLanguageId = $record->getAttributeValue($expressionAttribute)->language;
		else
			$expressionLanguageId = $this->filterLanguageId; 
				
		$expressionMeanings = $record->getAttributeValue($expressionMeaningsAttribute)->getAttributeValue($expressionExactMeaningsAttribute);

		if ($expressionLanguageId != 0 && $expressionMeanings->getRecordCount() > 0) {
			$expressionMeaning = $expressionMeanings->getRecord(0);

			$definition = $expressionMeaning->getAttributeValue($definedMeaningAttribute)->getAttributeValue($definitionAttribute);
			$translatedContent = $definition->getAttributeValue($translatedTextAttribute);
			
 			if ($this->filterLanguageId == 0) {					
				if ($translatedContent->getRecordCount() > 0) {
					$definitionRecord = $translatedContent->getRecord(0);
	
					$text = $definitionRecord->text;
					$languageId = $definitionRecord->language;
	
					if ($languageId != 0 && $text != "") {
						$expression = findOrCreateExpression($this->spelling, $expressionLanguageId);
						createNewDefinedMeaning($expression->id, $languageId, $text);
					}
				}
			}
			else if ($translatedContent != "") {
				$expression = findOrCreateExpression($this->spelling, $expressionLanguageId);
				createNewDefinedMeaning($expression->id, $this->filterLanguageId, $translatedContent);
			}
		}
	}

	public function remove($keyPath) {
	}

	public function update($keyPath, $record) {
	}
}

abstract class ObjectAttributeValuesController implements UpdateController {
	protected $objectIdFetcher;
	
	public function __construct($objectIdFetcher) {
		$this->objectIdFetcher = $objectIdFetcher;
	}
}

class TextAttributeValuesController extends ObjectAttributeValuesController {
	public function add($keyPath, $record)  {
		global
			$textAttributeAttribute;
		$objectId = $this->objectIdFetcher->fetch($keyPath);
		$textAttributeId = $record->getAttributeValue($textAttributeAttribute);
		$text = $record->text;
		if ($textAttributeId != 0 && $text != '')		
			addTextAttributeValue($objectId, $textAttributeId, $text);
	}

	public function remove($keyPath) {
		global
			$textAttributeIdAttribute;
		$textId = $keyPath->peek(0)->getAttributeValue($textAttributeIdAttribute);
		removeTextAttributeValue($textId);
	}

	public function update($keyPath, $record) {
		global
			$textAttributeIdAttribute, $textAttribute;
			
		$textId = $keyPath->peek(0)->getAttributeValue($textAttributeIdAttribute);
		$text = $record->text;
		
		updateTextAttributeValue($text, $textId);
	}
}

class LinkAttributeValuesController extends ObjectAttributeValuesController {
	public function add($keyPath, $record)  {
		global
			$linkAttribute, $linkAttributeAttribute, $linkLabelAttribute, $linkURLAttribute;
			
		$objectId = $this->objectIdFetcher->fetch($keyPath);
		$linkAttributeId = $record->getAttributeValue($linkAttributeAttribute);
		$linkValue = $record->getAttributeValue($linkAttribute);
		$label = $linkValue->getAttributeValue($linkLabelAttribute);
		$url = $linkValue->getAttributeValue($linkURLAttribute);		
		
		if ($linkAttributeId != 0 && $url != "")		
			addLinkAttributeValue($objectId, $linkAttributeId, $url, $label);
	}

	public function remove($keyPath) {
		global
			$linkAttributeIdAttribute;
			
		$linkId = $keyPath->peek(0)->getAttributeValue($linkAttributeIdAttribute);
		removeLinkAttributeValue($linkId);
	}

	public function update($keyPath, $record) {
		global
			$linkAttributeIdAttribute, $linkAttribute, $linkLabelAttribute, $linkURLAttribute;
			
		$linkId = $keyPath->peek(0)->getAttributeValue($linkAttributeIdAttribute);
		$linkValue = $record->getAttributeValue($linkAttribute);
		$label = $linkValue->getAttributeValue($linkLabelAttribute);
		$url = $linkValue->getAttributeValue($linkURLAttribute);		
				
		if ($url != "")
			updateLinkAttributeValue($linkId, $url, $label);
	}
}

class TranslatedTextAttributeValuesController extends ObjectAttributeValuesController {
	protected $filterLanguageId;
	
	public function __construct($objectIdFetcher, $filterLanguageId) {
		parent::__construct($objectIdFetcher);
		
		$this->filterLanguageId = $filterLanguageId;
	}
	
	public function add($keyPath, $record)  {
		global
			$translatedTextValueAttribute,
			$translatedTextAttributeAttribute;

		$objectId = $this->objectIdFetcher->fetch($keyPath);
		$textValue = $record->getAttributeValue($translatedTextValueAttribute);
		$textAttributeId = $record->getAttributeValue($translatedTextAttributeAttribute);

		if ($textAttributeId != 0) {
			if ($this->filterLanguageId == 0) {
				if ($textValue->getRecordCount() > 0) {
					$textValueRecord = $textValue->getRecord(0);
		
					$languageId = $textValueRecord->languageAttribute;
					$text = $textValueRecord->textAttribute;
					
					if ($languageId != 0 && $text != '')
						addTranslatedTextAttributeValue($objectId, $textAttributeId, $languageId, $text);
				}
			}
			else if ($textValue != '')
				addTranslatedTextAttributeValue($objectId, $textAttributeId, $this->filterLanguageId, $textValue);
		}
	}

	public function remove($keyPath) {
		global
			$translatedTextAttributeIdAttribute;

		$valueId = $keyPath->peek(0)->getAttributeValue($translatedTextAttributeIdAttribute);
		removeTranslatedTextAttributeValue($valueId);
	}

	public function update($keyPath, $record) {
	}
}

class TranslatedTextAttributeValueController implements UpdateController {
	public function add($keyPath, $record) {
		global
			$translatedTextAttributeIdAttribute;

		$valueId = $keyPath->peek(0)->getAttributeValue($translatedTextAttributeIdAttribute);
		$languageId = $record->language;
		$text = $record->text;
		$translatedTextAttribute = getTranslatedTextAttribute($valueId);

		if ($languageId != 0 && $text != "")
			addTranslatedTextIfNotPresent($translatedTextAttribute->value_tcid, $languageId, $text);
	}

	public function remove($keyPath) {
		global
			$translatedTextAttributeIdAttribute;

		$valueId = $keyPath->peek(1)->getAttributeValue($translatedTextAttributeIdAttribute);
		$languageId = $keyPath->peek(0)->language;
		$translatedTextAttribute = getTranslatedTextAttribute($valueId);

		removeTranslatedText($translatedTextAttribute->value_tcid, $languageId);
	}

	public function update($keyPath, $record) {
		global
			$translatedTextAttributeIdAttribute;

		$valueId = $keyPath->peek(1)->getAttributeValue($translatedTextAttributeIdAttribute);
		$languageId = $keyPath->peek(0)->language;
		$text = $record->text;
		$translatedTextAttribute = getTranslatedTextAttribute($valueId);

		if ($text != "")
			updateTranslatedText($translatedTextAttribute->value_tcid, $languageId, $text);
	}
}

class FilteredTranslatedTextAttributeValueController implements UpdateAttributeController {
	protected $filterLanguageId;
	
	public function __construct($filterLanguageId) {
		$this->filterLanguageId = $filterLanguageId;
	}
	
	public function update($keyPath, $value) {
		global
			$translatedTextAttributeIdAttribute ;

		$valueId = $keyPath->peek(0)->getAttributeValue($translatedTextAttributeIdAttribute);
		$translatedTextAttribute = getTranslatedTextAttribute($valueId);

		if ($value != "")
			updateTranslatedText($translatedTextAttribute->value_tcid, $this->filterLanguageId, $value);
	}
}

class OptionAttributeValuesController extends ObjectAttributeValuesController {
	public function add($keyPath, $record) {
		global
			$optionAttributeOptionAttribute;

		$objectId = $this->objectIdFetcher->fetch($keyPath);
		$optionId = $record->getAttributeValue($optionAttributeOptionAttribute);

		if ($optionId)
			addOptionAttributeValue($objectId,$optionId);
	}

	public function remove($keyPath) {
		global
			$optionAttributeIdAttribute;

		$valueId = $keyPath->peek(0)->getAttributeValue($optionAttributeIdAttribute);
		removeOptionAttributeValue($valueId);
	}

	public function update($keyPath, $record) {}
}

class OptionAttributeOptionsController implements UpdateController {
	public function add($keyPath, $record) {
		global
			$classAttributeIdAttribute, $optionAttributeOptionAttribute;

		$attributeId = $keyPath->peek(0)->getAttributeValue($classAttributeIdAttribute);
		$optionMeaningId = $record->getAttributeValue($optionAttributeOptionAttribute);
		$languageId = $record->language;

		if ($optionMeaningId)
			addOptionAttributeOption($attributeId, $optionMeaningId, $languageId);
	}

	public function remove($keyPath) {
		global
			$optionAttributeOptionIdAttribute;

		$optionId = $keyPath->peek(0)->getAttributeValue($optionAttributeOptionIdAttribute);
		removeOptionAttributeOption($optionId);
	}

	public function update($keyPath, $record) {
	}
}

class AlternativeDefinitionsPermissionController implements PermissionController {
	public function allowUpdateOfAttribute($attribute) {
		return true;	
	}
	
	public function allowUpdateOfValue($idPath, $value) {
		return $this->allowAnyChangeOfValue($value);
	}
	
	public function allowRemovalOfValue($idPath, $value) {
		return $this->allowAnyChangeOfValue($value);
	}

	protected function allowAnyChangeOfValue($value) {
		global
			$sourceAttribute;
			
		$source = $value->getAttributeValue($sourceAttribute);	
			
		return $source == null || $source == 0;
	}
}


