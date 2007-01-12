<?php

require_once("WiktionaryZAttributes.php");

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

class SimplePermissionController {
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
			$definedMeaningIdAttribute, $languageAttribute, $textAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$languageId = $record->getAttributeValue($languageAttribute);
		$text = $record->getAttributeValue($textAttribute);

		if ($languageId != 0 && $text != "")
			addDefinedMeaningDefinition($definedMeaningId, $languageId, $text);
	}

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $languageAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		removeDefinedMeaningDefinition($definedMeaningId, $languageId);
	}

	public function update($keyPath, $record) {
		global
			$definedMeaningIdAttribute, $languageAttribute, $textAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		$text = $record->getAttributeValue($textAttribute);

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
			$definedMeaningIdAttribute, $alternativeDefinitionAttribute, $languageAttribute, $textAttribute,
			$sourceAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$alternativeDefinition = $record->getAttributeValue($alternativeDefinitionAttribute);
		$sourceId = $record->getAttributeValue($sourceAttribute);

		if ($this->filterLanguageId == 0) {
			if ($alternativeDefinition->getRecordCount() > 0) {
				$definitionRecord = $alternativeDefinition->getRecord(0);
	
				$languageId = $definitionRecord->getAttributeValue($languageAttribute);
				$text = $definitionRecord->getAttributeValue($textAttribute);
	
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
			$expressionIdAttribute, $definitionIdAttribute, $languageAttribute, $textAttribute;

		$definitionId = $keyPath->peek(0)->getAttributeValue($definitionIdAttribute);
		$languageId = $record->getAttributeValue($languageAttribute);
		$text = $record->getAttributeValue($textAttribute);

		if ($languageId != 0 && $text != "")
			addTranslatedTextIfNotPresent($definitionId, $languageId, $text);
	}

	public function remove($keyPath) {
		global
			$definitionIdAttribute, $languageAttribute;

		$definitionId = $keyPath->peek(1)->getAttributeValue($definitionIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);

		removeTranslatedText($definitionId, $languageId);
	}

	public function update($keyPath, $record) {
		global
			$definitionIdAttribute, $languageAttribute, $textAttribute;

		$definitionId = $keyPath->peek(1)->getAttributeValue($definitionIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		$text = $record->getAttributeValue($textAttribute);

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
			$definitionIdAttribute, $languageAttribute, $textAttribute;

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
			$definedMeaningIdAttribute, $expressionAttribute, $languageAttribute, $spellingAttribute, $identicalMeaningAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$expressionValue = $record->getAttributeValue($expressionAttribute);
		
		if ($this->filterLanguageId == 0) {
			$languageId = $expressionValue->getAttributeValue($languageAttribute);
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
			$expressionIdAttribute, $definedMeaningAttribute, $definitionAttribute, $translatedTextAttribute, $languageAttribute, $textAttribute;

		$definition = $record->getAttributeValue($definedMeaningAttribute)->getAttributeValue($definitionAttribute);
		$translatedContent = $definition->getAttributeValue($translatedTextAttribute);
		$expressionId = $keyPath->peek(0)->getAttributeValue($expressionIdAttribute);

		if ($this->filterLanguageId == 0) {
			if ($translatedContent->getRecordCount() > 0) {
				$definitionRecord = $translatedContent->getRecord(0);
	
				$text = $definitionRecord->getAttributeValue($textAttribute);
				$languageId = $definitionRecord->getAttributeValue($languageAttribute);
	
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

	public function __construct($spelling) {
		$this->spelling = $spelling;
	}

	public function add($keyPath, $record) {
		global
			$expressionAttribute, $expressionMeaningsAttribute, $expressionExactMeaningsAttribute, 
			$definedMeaningAttribute, $definitionAttribute, 
			$languageAttribute, $textAttribute, $translatedTextAttribute;

		$expressionLanguageId = $record->getAttributeValue($expressionAttribute)->getAttributeValue($languageAttribute);
		$expressionMeanings = $record->getAttributeValue($expressionMeaningsAttribute)->getAttributeValue($expressionExactMeaningsAttribute);

		if ($expressionLanguageId != 0 && $expressionMeanings->getRecordCount() > 0) {
			$expressionMeaning = $expressionMeanings->getRecord(0);

			$definition = $expressionMeaning->getAttributeValue($definedMeaningAttribute)->getAttributeValue($definitionAttribute);
			$translatedContent = $definition->getAttributeValue($translatedTextAttribute);

			if ($translatedContent->getRecordCount() > 0) {
				$definitionRecord = $translatedContent->getRecord(0);

				$text = $definitionRecord->getAttributeValue($textAttribute);
				$languageId = $definitionRecord->getAttributeValue($languageAttribute);

				if ($languageId != 0 && $text != "") {
					$expression = findOrCreateExpression($this->spelling, $expressionLanguageId);
					createNewDefinedMeaning($expression->id, $languageId, $text);
				}
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
			$textAttribute, $textAttributeAttribute;
		$objectId = $this->objectIdFetcher->fetch($keyPath);
		$textAttributeId = $record->getAttributeValue($textAttributeAttribute);
		$text = $record->getAttributeValue($textAttribute);
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
		$text = $record->getAttributeValue($textAttribute);		
		
		updateTextAttributeValue($text, $textId);
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
			$translatedTextValueAttribute, $languageAttribute,
			$textAttribute, $translatedTextAttributeAttribute;

		$objectId = $this->objectIdFetcher->fetch($keyPath);
		$textValue = $record->getAttributeValue($translatedTextValueAttribute);
		$textAttributeId = $record->getAttributeValue($translatedTextAttributeAttribute);

		if ($textAttributeId != 0) {
			if ($this->filterLanguageId == 0) {
				if ($textValue->getRecordCount() > 0) {
					$textValueRecord = $textValue->getRecord(0);
		
					$languageId = $textValueRecord->getAttributeValue($languageAttribute);
					$text = $textValueRecord->getAttributeValue($textAttribute);
					
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
			$translatedTextAttributeIdAttribute, $languageAttribute, $textAttribute;

		$valueId = $keyPath->peek(0)->getAttributeValue($translatedTextAttributeIdAttribute);
		$languageId = $record->getAttributeValue($languageAttribute);
		$text = $record->getAttributeValue($textAttribute);
		$translatedTextAttribute = getTranslatedTextAttribute($valueId);

		if ($languageId != 0 && $text != "")
			addTranslatedTextIfNotPresent($translatedTextAttribute->value_tcid, $languageId, $text);
	}

	public function remove($keyPath) {
		global
			$translatedTextAttributeIdAttribute, $languageAttribute;

		$valueId = $keyPath->peek(1)->getAttributeValue($translatedTextAttributeIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		$translatedTextAttribute = getTranslatedTextAttribute($valueId);

		removeTranslatedText($translatedTextAttribute->value_tcid, $languageId);
	}

	public function update($keyPath, $record) {
		global
			$translatedTextAttributeIdAttribute, $languageAttribute, $textAttribute;

		$valueId = $keyPath->peek(1)->getAttributeValue($translatedTextAttributeIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		$text = $record->getAttributeValue($textAttribute);
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
			$translatedTextAttributeIdAttribute, $languageAttribute, $textAttribute;

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
			$classAttributeIdAttribute, $optionAttributeOptionAttribute, $languageAttribute;

		$attributeId = $keyPath->peek(0)->getAttributeValue($classAttributeIdAttribute);
		$optionMeaningId = $record->getAttributeValue($optionAttributeOptionAttribute);
		$languageId = $record->getAttributeValue($languageAttribute);

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

?>
