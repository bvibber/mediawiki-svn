<?php

require_once("WiktionaryZAttributes.php");

interface Controller {
	public function add($keyPath, $record);
	public function remove($keyPath);
	public function update($keyPath, $record);
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

class DefinedMeaningDefinitionController implements Controller {
	public function add($keyPath, $record) {
		global
			$expressionIdAttribute, $definedMeaningIdAttribute, $languageAttribute, $textAttribute;

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

class DefinedMeaningAlternativeDefinitionsController {
	public function add($keyPath, $record)  {
		global
			$expressionIdAttribute, $definedMeaningIdAttribute, $alternativeDefinitionAttribute, $languageAttribute, $textAttribute,
			$sourceAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$alternativeDefinition = $record->getAttributeValue($alternativeDefinitionAttribute);
		$sourceId = $record->getAttributeValue($sourceAttribute);

		if ($alternativeDefinition->getRecordCount() > 0) {
			$definitionRecord = $alternativeDefinition->getRecord(0);

			$languageId = $definitionRecord->getAttributeValue($languageAttribute);
			$text = $definitionRecord->getAttributeValue($textAttribute);

			if ($languageId != 0 && $text != '')
				addDefinedMeaningAlternativeDefinition($definedMeaningId, $languageId, $text, $sourceId);
		}
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

class DefinedMeaningAlternativeDefinitionController implements Controller {
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

class SynonymTranslationController implements Controller {
	public function add($keyPath, $record) {
		global
			$definedMeaningIdAttribute, $expressionAttribute, $languageAttribute, $spellingAttribute, $identicalMeaningAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$expressionRecord = $record->getAttributeValue($expressionAttribute);
		$languageId = $expressionRecord->getAttributeValue($languageAttribute);
		$spelling = $expressionRecord->getAttributeValue($spellingAttribute);
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

class DefinedMeaningRelationController implements Controller {
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

class DefinedMeaningClassMembershipController implements Controller {
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

class DefinedMeaningCollectionController implements Controller {
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

class ExpressionMeaningController implements Controller {
	public function add($keyPath, $record) {
		global
			$expressionIdAttribute, $definedMeaningAttribute, $definitionAttribute, $languageAttribute, $textAttribute;

		$definition = $record->getAttributeValue($definedMeaningAttribute)->getAttributeValue($definitionAttribute);

		if ($definition->getRecordCount() > 0) {
			$definitionRecord = $definition->getRecord(0);

			$text = $definitionRecord->getAttributeValue($textAttribute);
			$languageId = $definitionRecord->getAttributeValue($languageAttribute);

			if ($languageId != 0 && $text != "") {
				$expressionId = $keyPath->peek(0)->getAttributeValue($expressionIdAttribute);

				createNewDefinedMeaning($expressionId, $languageId, $text);
			}
		}
	}

	public function remove($keyPath) {
	}

	public function update($keyPath, $record) {
	}
}

class ExpressionController implements Controller {
	protected $spelling;

	public function __construct($spelling) {
		$this->spelling = $spelling;
	}

	public function add($keyPath, $record) {
		global
			$expressionAttribute, $expressionMeaningsAttribute, $expressionExactMeaningsAttribute, 
			$definedMeaningAttribute, $definitionAttribute, 
			$languageAttribute, $textAttribute;

		$expressionLanguageId = $record->getAttributeValue($expressionAttribute)->getAttributeValue($languageAttribute);
		$expressionMeanings = $record->getAttributeValue($expressionMeaningsAttribute)->getAttributeValue($expressionExactMeaningsAttribute);

		if ($expressionLanguageId != 0 && $expressionMeanings->getRecordCount() > 0) {
			$expressionMeaning = $expressionMeanings->getRecord(0);

			$definition = $expressionMeaning->getAttributeValue($definedMeaningAttribute)->getAttributeValue($definitionAttribute);

			if ($definition->getRecordCount() > 0) {
				$definitionRecord = $definition->getRecord(0);

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

class DefinedMeaningTextAttributeValuesController {
	public function add($keyPath, $record)  {
		global
			$expressionIdAttribute, $definedMeaningIdAttribute, $textValueAttribute, $languageAttribute,
			$textAttribute, $textAttributeAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$textValue = $record->getAttributeValue($textValueAttribute);
		$textAttributeId = $record->getAttributeValue($textAttributeAttribute);

		if ($textAttributeId != 0 && $textValue->getRecordCount() > 0) {
			$textValueRecord = $textValue->getRecord(0);

			$languageId = $textValueRecord->getAttributeValue($languageAttribute);
			$text = $textValueRecord->getAttributeValue($textAttribute);

			if ($languageId != 0 && $text != '')
				addDefinedMeaningTextAttributeValue($definedMeaningId, $textAttributeId, $languageId, $text);
		}
	}

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $textAttributeAttribute, $textValueIdAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$attributeId = $keyPath->peek(0)->getAttributeValue($textAttributeAttribute);
		$textId = $keyPath->peek(0)->getAttributeValue($textValueIdAttribute);

		removeDefinedMeaningTextAttributeValue($definedMeaningId, $attributeId, $textId);
	}

	public function update($keyPath, $record) {
	}
}

class DefinedMeaningTextAttributeValueController implements Controller {
	public function add($keyPath, $record) {
		global
			$expressionIdAttribute, $textValueIdAttribute, $languageAttribute, $textAttribute;

		$textId = $keyPath->peek(0)->getAttributeValue($textValueIdAttribute);
		$languageId = $record->getAttributeValue($languageAttribute);
		$text = $record->getAttributeValue($textAttribute);

		if ($languageId != 0 && $text != "")
			addTranslatedTextIfNotPresent($textId, $languageId, $text);
	}

	public function remove($keyPath) {
		global
			$textValueIdAttribute, $languageAttribute;

		$textId = $keyPath->peek(1)->getAttributeValue($textValueIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);

		removeTranslatedText($textId, $languageId);
	}

	public function update($keyPath, $record) {
		global
			$textValueIdAttribute, $languageAttribute, $textAttribute;

		$textId = $keyPath->peek(1)->getAttributeValue($textValueIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		$text = $record->getAttributeValue($textAttribute);

		if ($text != "")
			updateTranslatedText($textId, $languageId, $text);
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
