<?php

require_once('OmegaWikiAttributes.php');
require_once('Record.php');
require_once('RecordSet.php');
require_once('Expression.php');
require_once('Transaction.php');
require_once('WikiDataTables.php');
require_once('RecordSetQueries.php');

function getSynonymSQLForLanguage($languageId, &$definedMeaningIds) {
	return 
		"SELECT uw_defined_meaning.defined_meaning_id AS defined_meaning_id, uw_expression_ns.spelling AS label " .
		" FROM uw_defined_meaning, uw_syntrans, uw_expression_ns " .
		" WHERE uw_defined_meaning.defined_meaning_id IN (" . implode(", ", $definedMeaningIds) . ")" .
		" AND " . getLatestTransactionRestriction('uw_syntrans') .
		" AND " . getLatestTransactionRestriction('uw_expression_ns') .
		" AND " . getLatestTransactionRestriction('uw_defined_meaning') .
		" AND uw_expression_ns.language_id=" . $languageId .
		" AND uw_expression_ns.expression_id=uw_syntrans.expression_id " .
		" AND uw_defined_meaning.defined_meaning_id=uw_syntrans.defined_meaning_id " . 
		" AND uw_syntrans.identical_meaning=1 " .
		" GROUP BY uw_defined_meaning.defined_meaning_id";
}

function getSynonymSQLForAnyLanguage(&$definedMeaningIds) {
	return 
		"SELECT uw_defined_meaning.defined_meaning_id AS defined_meaning_id, uw_expression_ns.spelling AS label " .
		" FROM uw_defined_meaning, uw_syntrans, uw_expression_ns " .
		" WHERE uw_defined_meaning.defined_meaning_id IN (" . implode(", ", $definedMeaningIds) . ")" .
		" AND " . getLatestTransactionRestriction('uw_syntrans') .
		" AND " . getLatestTransactionRestriction('uw_expression_ns') .
		" AND " . getLatestTransactionRestriction('uw_defined_meaning') .
		" AND uw_expression_ns.expression_id=uw_syntrans.expression_id " .
		" AND uw_defined_meaning.defined_meaning_id=uw_syntrans.defined_meaning_id " . 
		" AND uw_syntrans.identical_meaning=1 " .
		" GROUP BY uw_defined_meaning.defined_meaning_id";
}

function getDefiningSQLForLanguage($languageId, &$definedMeaningIds) {
	return 
		"SELECT uw_defined_meaning.defined_meaning_id AS defined_meaning_id, uw_expression_ns.spelling AS label " .
		" FROM uw_defined_meaning, uw_syntrans, uw_expression_ns " .
		" WHERE uw_defined_meaning.defined_meaning_id IN (" . implode(", ", $definedMeaningIds) . ")" .
		" AND " . getLatestTransactionRestriction('uw_syntrans') .
		" AND " . getLatestTransactionRestriction('uw_expression_ns') .
		" AND " . getLatestTransactionRestriction('uw_defined_meaning') .
		" AND uw_expression_ns.expression_id=uw_syntrans.expression_id " .
		" AND uw_defined_meaning.defined_meaning_id=uw_syntrans.defined_meaning_id " . 
		" AND uw_syntrans.identical_meaning=1 " .
		" AND uw_defined_meaning.expression_id=uw_expression_ns.expression_id " .
		" AND uw_expression_ns.language_id=" . $languageId .
		" GROUP BY uw_defined_meaning.defined_meaning_id";
}

function fetchDefinedMeaningReferenceRecords($sql, &$definedMeaningIds, &$definedMeaningReferenceRecords) {
	global
		$definedMeaningReferenceStructure, $definedMeaningIdAttribute, $definedMeaningLabelAttribute,
		$definedMeaningDefiningExpressionAttribute;

	$foundDefinedMeaningIds = array();	

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query($sql);

	while ($row = $dbr->fetchObject($queryResult)) {
		$definedMeaningId = $row->defined_meaning_id;
		
		$record = new ArrayRecord($definedMeaningReferenceStructure);
		$record->setAttributeValue($definedMeaningIdAttribute, $definedMeaningId);
		$record->setAttributeValue($definedMeaningLabelAttribute, $row->label);
				
		$definedMeaningReferenceRecords[$definedMeaningId] = $record;
		$foundDefinedMeaningIds[] = $definedMeaningId;
	}
	
	$definedMeaningIds = array_diff($definedMeaningIds, $foundDefinedMeaningIds);
}

function fetchDefinedMeaningDefiningExpressions(&$definedMeaningIds, &$definedMeaningReferenceRecords) {
	global
		$definedMeaningReferenceStructure, $definedMeaningIdAttribute, $definedMeaningLabelAttribute,
		$definedMeaningDefiningExpressionAttribute;
	
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT uw_defined_meaning.defined_meaning_id AS defined_meaning_id, uw_expression_ns.spelling" .
		" FROM uw_defined_meaning, uw_expression_ns " .
		" WHERE uw_defined_meaning.expression_id=uw_expression_ns.expression_id " .
		" AND " . getLatestTransactionRestriction('uw_defined_meaning') .
		" AND " . getLatestTransactionRestriction('uw_expression_ns') . 
		" AND uw_defined_meaning.defined_meaning_id IN (". implode(", ", $definedMeaningIds) .")"
	);

	while ($row = $dbr->fetchObject($queryResult)) {
		$definedMeaningReferenceRecord = $definedMeaningReferenceRecords[$row->defined_meaning_id];
		
		if ($definedMeaningReferenceRecord == null) {
			$definedMeaningReferenceRecord = new ArrayRecord($definedMeaningReferenceStructure);
			$definedMeaningReferenceRecord->setAttributeValue($definedMeaningIdAttribute, $row->defined_meaning_id);
			$definedMeaningReferenceRecord->setAttributeValue($definedMeaningLabelAttribute, $row->spelling);
			$definedMeaningReferenceRecords[$row->defined_meaning_id] = $definedMeaningReferenceRecord; 
		}
		
		$definedMeaningReferenceRecord->setAttributeValue($definedMeaningDefiningExpressionAttribute, $row->spelling);
	}	
}

function getNullDefinedMeaningReferenceRecord() {
	global
		$definedMeaningReferenceStructure, $definedMeaningIdAttribute, $definedMeaningLabelAttribute,
		$definedMeaningDefiningExpressionAttribute;
	
	$record = new ArrayRecord($definedMeaningReferenceStructure);
	$record->setAttributeValue($definedMeaningIdAttribute, 0);
	$record->setAttributeValue($definedMeaningLabelAttribute, "");
	$record->setAttributeValue($definedMeaningDefiningExpressionAttribute, "");
	
	return $record;
}

function getDefinedMeaningReferenceRecords($definedMeaningIds) {
	global
		$wgUser;
	
//	$startTime = microtime(true);

	$result = array();
	$definedMeaningIdsForExpressions = $definedMeaningIds;

	if (count($definedMeaningIds) > 0) {
		$userLanguage = getLanguageIdForCode($wgUser->getOption('language'));
		
		if ($userLanguage > 0)
			$definingLanguage = $userLanguage;
		else
			$definingLanguage = 85;
			
		fetchDefinedMeaningReferenceRecords(
			getDefiningSQLForLanguage($definingLanguage, $definedMeaningIds),
			$definedMeaningIds,
			$result
		);
	
		if (count($definedMeaningIds) > 0) {
			if ($userLanguage > 0)
				fetchDefinedMeaningReferenceRecords(
					getSynonymSQLForLanguage($userLanguage, $definedMeaningIds),
					$definedMeaningIds,
					$result
				);
	
			if (count($definedMeaningIds) > 0) {
				fetchDefinedMeaningReferenceRecords(
					getSynonymSQLForLanguage(85, $definedMeaningIds),
					$definedMeaningIds,
					$result
				);
		
				if (count($definedMeaningIds) > 0) {
					fetchDefinedMeaningReferenceRecords(
						getSynonymSQLForAnyLanguage($definedMeaningIds),
						$definedMeaningIds,
						$result
					);
				}
			}
		}
		
		fetchDefinedMeaningDefiningExpressions($definedMeaningIdsForExpressions, $result);
		$result[0] = getNullDefinedMeaningReferenceRecord();
	}

//	$queriesTime = microtime(true) - $startTime;
//	echo "<!-- Defined meaning reference queries: " . $queriesTime . " -->\n";

	return $result;
}

function expandDefinedMeaningReferencesInRecordSet($recordSet, $definedMeaningAttributes) {
	$definedMeaningReferenceRecords = getDefinedMeaningReferenceRecords(getUniqueIdsInRecordSet($recordSet, $definedMeaningAttributes));

	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);
		
		foreach($definedMeaningAttributes as $definedMeaningAttribute)
			$record->setAttributeValue(
				$definedMeaningAttribute, 
				$definedMeaningReferenceRecords[$record->getAttributeValue($definedMeaningAttribute)]
			);
	} 
}

function expandTranslatedContentInRecord($record, $idAttribute, $translatedContentAttribute, $filterLanguageId, $queryTransactionInformation) {
	$record->setAttributeValue(
		$translatedContentAttribute, 
		getTranslatedContentValue($record->getAttributeValue($idAttribute), $filterLanguageId, $queryTransactionInformation)
	);
}

function expandTranslatedContentsInRecordSet($recordSet, $idAttribute, $translatedContentAttribute, $filterLanguageId, $queryTransactionInformation) {
	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) 
		expandTranslatedContentInRecord($recordSet->getRecord($i), $idAttribute, $translatedContentAttribute, $filterLanguageId, $queryTransactionInformation);
}									

function getExpressionReferenceRecords($expressionIds) {
	global
		$expressionStructure, $languageAttribute, $spellingAttribute;
	
	if (count($expressionIds) > 0) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query(
			"SELECT expression_id, language_id, spelling" .
			" FROM uw_expression_ns" .
			" WHERE expression_id IN (". implode(', ', $expressionIds) .")" .
			" AND ". getLatestTransactionRestriction('uw_expression_ns')
		);
		
		$result = array();
	
		while ($row = $dbr->fetchObject($queryResult)) {
			$record = new ArrayRecord($expressionStructure);
			$record->setAttributeValue($languageAttribute, $row->language_id);
			$record->setAttributeValue($spellingAttribute, $row->spelling);
			
			$result[$row->expression_id] = $record;
		}
			
		return $result;
	}
	else
		return array();
}

function expandExpressionReferencesInRecordSet($recordSet, $expressionAttributes) {
	$expressionReferenceRecords = getExpressionReferenceRecords(getUniqueIdsInRecordSet($recordSet, $expressionAttributes));

	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);
		
		foreach($expressionAttributes as $expressionAttribute)
			$record->setAttributeValue(
				$expressionAttribute, 
				$expressionReferenceRecords[$record->getAttributeValue($expressionAttribute)]
			);
	} 
}

function getExpressionSpellings($expressionIds) {
	global
		$expressionAttribute;
	
	if (count($expressionIds) > 0) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query(
			"SELECT expression_id, spelling" .
			" FROM uw_expression_ns" .
			" WHERE expression_id IN (". implode(', ', $expressionIds) .")" .
			" AND ". getLatestTransactionRestriction('uw_expression_ns')
		);
		
		$result = array();
	
		while ($row = $dbr->fetchObject($queryResult)) 
			$result[$row->expression_id] = $row->spelling;
			
		return $result;
	}
	else
		return array();
}

function expandExpressionSpellingsInRecordSet($recordSet, $expressionAttributes) {
	$expressionSpellings = getExpressionSpellings(getUniqueIdsInRecordSet($recordSet, $expressionAttributes));

	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);
		
		foreach($expressionAttributes as $expressionAttribute)
			$record->setAttributeValue(
				$expressionAttribute, 
				$expressionSpellings[$record->getAttributeValue($expressionAttribute)]
			);
	} 
}

function getTextReferences($textIds) {
	if (count($textIds) > 0) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query(
			"SELECT old_id, old_text" .
			" FROM text" .
			" WHERE old_id IN (". implode(', ', $textIds) .")"
		);
		
		$result = array();
	
		while ($row = $dbr->fetchObject($queryResult)) 
			$result[$row->old_id] = $row->old_text;
			
		return $result;
	}
	else
		return array();
}

function expandTextReferencesInRecordSet($recordSet, $textAttributes) {
	$textReferences = getTextReferences(getUniqueIdsInRecordSet($recordSet, $textAttributes));

	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);
		
		foreach($textAttributes as $textAttribute)
			$record->setAttributeValue(
				$textAttribute, 
				$textReferences[$record->getAttributeValue($textAttribute)]
			);
	} 
}

function getExpressionMeaningsRecordSet($expressionId, $exactMeaning, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation) {
	global
		$expressionMeaningStructure, $definedMeaningIdAttribute;

	if ($exactMeaning)
		$identicalMeaning = 1;
	else
		$identicalMeaning = 0;
		
	$recordSet = new ArrayRecordSet($expressionMeaningStructure, new Structure($definedMeaningIdAttribute));

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT defined_meaning_id FROM uw_syntrans" .
								" WHERE expression_id=$expressionId AND identical_meaning=" . $identicalMeaning .
								" AND ". getLatestTransactionRestriction('uw_syntrans'));

	while($definedMeaning = $dbr->fetchObject($queryResult)) {
		$definedMeaningId = $definedMeaning->defined_meaning_id;
		$recordSet->addRecord(
			array(
				$definedMeaningId, 
				getDefinedMeaningDefinition($definedMeaningId), 
				getDefinedMeaningRecord($definedMeaningId, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation)
			)
		);
	}

	return $recordSet;
}

function getExpressionMeaningsRecord($expressionId, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation) {
	global
		$expressionMeaningsStructure, $expressionExactMeaningsAttribute, $expressionApproximateMeaningsAttribute;
		
	$record = new ArrayRecord($expressionMeaningsStructure);
	$record->setAttributeValue($expressionExactMeaningsAttribute, getExpressionMeaningsRecordSet($expressionId, true, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation));
	$record->setAttributeValue($expressionApproximateMeaningsAttribute, getExpressionMeaningsRecordSet($expressionId, false, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation));
	
	return $record;
}

function getExpressionsRecordSet($spelling, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation) {
	global
		$expressionIdAttribute, $expressionAttribute, $languageAttribute, $expressionMeaningsAttribute;

	if ($filterLanguageId != 0)
		$languageRestriction = " AND language_id=$filterLanguageId";
	else
		$languageRestriction = "";

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT expression_id, language_id " .
		" FROM uw_expression_ns" .
		" WHERE spelling=BINARY " . $dbr->addQuotes($spelling) .
		" AND " . getLatestTransactionRestriction('uw_expression_ns') .
		$languageRestriction .
		" AND EXISTS (" .
			"SELECT expression_id " .
			" FROM uw_syntrans " .
			" WHERE uw_syntrans.expression_id=uw_expression_ns.expression_id" .
			" AND ". getLatestTransactionRestriction('uw_syntrans') 
		.")"
	);
	
	$result = new ArrayRecordSet(new Structure($expressionIdAttribute, $expressionAttribute, $expressionMeaningsAttribute), new Structure($expressionIdAttribute));
	$expressionStructure = new Structure($languageAttribute);

	while($expression = $dbr->fetchObject($queryResult)) {
		$expressionRecord = new ArrayRecord($expressionStructure);
		$expressionRecord->setAttributeValue($languageAttribute, $expression->language_id);

		$result->addRecord(array(
			$expression->expression_id, 
			$expressionRecord, 
			getExpressionMeaningsRecord($expression->expression_id, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation)
		));
	}

	return $result;
}

function getExpressionIdThatHasSynonyms($spelling, $languageId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT expression_id, language_id " .
		" FROM uw_expression_ns" .
		" WHERE spelling=BINARY " . $dbr->addQuotes($spelling) .
		" AND " . getLatestTransactionRestriction('uw_expression_ns') .
		" AND language_id=$languageId" .
		" AND EXISTS (" .
			"SELECT expression_id " .
			" FROM uw_syntrans " .
			" WHERE uw_syntrans.expression_id=uw_expression_ns.expression_id" .
			" AND ". getLatestTransactionRestriction('uw_syntrans') 
		.")"
	);
	
	if ($expression = $dbr->fetchObject($queryResult)) 
		return $expression->expression_id;
	else
		return 0;
}
 
function getDefinedMeaningRecord($definedMeaningId, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation) {
	global
		$definedMeaningAttribute, $definitionAttribute, $classAttributesAttribute, 
		$alternativeDefinitionsAttribute, $synonymsAndTranslationsAttribute,
		$relationsAttribute, $reciprocalRelationsAttribute,
		$classMembershipAttribute, $collectionMembershipAttribute, $definedMeaningAttributesAttribute,
		$possiblySynonymousAttribute;

	$record = new ArrayRecord($definedMeaningAttribute->type->getStructure());
	$record->setAttributeValue($definitionAttribute, getDefinedMeaningDefinitionRecord($definedMeaningId, $filterLanguageId, $queryTransactionInformation));
	$record->setAttributeValue($classAttributesAttribute, getClassAttributesRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($alternativeDefinitionsAttribute, getAlternativeDefinitionsRecordSet($definedMeaningId, $filterLanguageId, $queryTransactionInformation));
	$record->setAttributeValue($synonymsAndTranslationsAttribute, getSynonymAndTranslationRecordSet($definedMeaningId, $filterLanguageId, $queryTransactionInformation));
	
	$filterRelationTypes = array();

	if ($possiblySynonymousRelationTypeId != 0) {
		$record->setAttributeValue($possiblySynonymousAttribute, getPossiblySynonymousRecordSet($definedMeaningId, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation));
		$filterRelationTypes[] = $possiblySynonymousRelationTypeId;
	}
	
	$record->setAttributeValue($relationsAttribute, getDefinedMeaningRelationsRecordSet($definedMeaningId, $filterLanguageId, $filterRelationTypes, $queryTransactionInformation));
	$record->setAttributeValue($reciprocalRelationsAttribute, getDefinedMeaningReciprocalRelationsRecordSet($definedMeaningId, $filterLanguageId, $queryTransactionInformation));
	$record->setAttributeValue($classMembershipAttribute, getDefinedMeaningClassMembershipRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($collectionMembershipAttribute, getDefinedMeaningCollectionMembershipRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($definedMeaningAttributesAttribute, getObjectAttributesRecord($definedMeaningId, $filterLanguageId, $queryTransactionInformation));

	return $record;
}

function getClassAttributesRecordSet($definedMeaningId, $queryTransactionInformation) {
	global
		$classAttributesTable, $classAttributeIdAttribute, $classAttributeLevelAttribute, $classAttributeAttributeAttribute, $classAttributeTypeAttribute, $optionAttributeOptionsAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$classAttributeIdAttribute,
		array(
			'object_id' => $classAttributeIdAttribute,
			'level_mid' => $classAttributeLevelAttribute,
			'attribute_mid' => $classAttributeAttributeAttribute,
			'attribute_type' => $classAttributeTypeAttribute
		),
		$classAttributesTable,
		array("class_mid=$definedMeaningId")
	);
	
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($classAttributeLevelAttribute ,$classAttributeAttributeAttribute));
	expandOptionAttributeOptionsInRecordSet($recordSet, $classAttributeIdAttribute, $queryTransactionInformation);

	return $recordSet;
}

function expandOptionAttributeOptionsInRecordSet($recordSet, $attributeIdAttribute, $queryTransactionInformation) {
	global
		$definedMeaningIdAttribute, $optionAttributeOptionsAttribute;

	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);

		$record->setAttributeValue($optionAttributeOptionsAttribute, getOptionAttributeOptionsRecordSet($record->getAttributeValue($attributeIdAttribute), $queryTransactionInformation));
	}
}

function getAlternativeDefinitionsRecordSet($definedMeaningId, $filterLanguageId, $queryTransactionInformation) {
	global
		$alternativeDefinitionsTable, $definitionIdAttribute, $alternativeDefinitionAttribute, $sourceAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$definitionIdAttribute,
		array(
			'meaning_text_tcid' => $definitionIdAttribute, 
			'source_id' => $sourceAttribute
		),
		$alternativeDefinitionsTable,
		array("meaning_mid=$definedMeaningId")
	);

	$recordSet->getStructure()->attributes[] = $alternativeDefinitionAttribute;
	
	expandTranslatedContentsInRecordSet($recordSet, $definitionIdAttribute, $alternativeDefinitionAttribute, $filterLanguageId, $queryTransactionInformation);									
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($sourceAttribute));

	return $recordSet;
}

function getDefinedMeaningDefinitionRecord($definedMeaningId, $filterLanguageId, $queryTransactionInformation) {
	global
		$definitionAttribute, $translatedTextAttribute, $objectAttributesAttribute;
		
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	$record = new ArrayRecord($definitionAttribute->type->getStructure());
	$record->setAttributeValue($translatedTextAttribute, getTranslatedContentValue($definitionId, $filterLanguageId, $queryTransactionInformation));
	$record->setAttributeValue($objectAttributesAttribute, getObjectAttributesRecord($definitionId, $filterLanguageId, $queryTransactionInformation));

	return $record;
}

function getObjectAttributesRecord($objectId, $filterLanguageId, $queryTransactionInformation) {
	global
		$objectAttributesAttribute, $objectIdAttribute, 
		$urlAttributeValuesAttribute, $textAttributeValuesAttribute, 
		$translatedTextAttributeValuesAttribute, $optionAttributeValuesAttribute; 
		
	$record = new ArrayRecord($objectAttributesAttribute->type->getStructure());
	
	$record->setAttributeValue($objectIdAttribute, $objectId);
	$record->setAttributeValue($textAttributeValuesAttribute, getTextAttributesValuesRecordSet($objectId, $filterLanguageId, $queryTransactionInformation));
	$record->setAttributeValue($translatedTextAttributeValuesAttribute, getTranslatedTextAttributeValuesRecordSet($objectId, $filterLanguageId, $queryTransactionInformation));
	$record->setAttributeValue($urlAttributeValuesAttribute, getURLAttributeValuesRecordSet($objectId, $filterLanguageId, $queryTransactionInformation));	
	$record->setAttributeValue($optionAttributeValuesAttribute, getOptionAttributeValuesRecordSet($objectId, $filterLanguageId, $queryTransactionInformation));	

	return $record;
}

function getTranslatedContentValue($translatedContentId, $filterLanguageId, $queryTransactionInformation) {
	global
		$textAttribute;
	
	if ($filterLanguageId == 0)
		return getTranslatedContentRecordSet($translatedContentId, $queryTransactionInformation);
	else {
		$recordSet = getFilteredTranslatedContentRecordSet($translatedContentId, $filterLanguageId, $queryTransactionInformation);
		
		if (count($queryTransactionInformation->versioningAttributes()) > 0) 
			return $recordSet;
		else {
			if ($recordSet->getRecordCount() > 0) 
				return $recordSet->getRecord(0)->getAttributeValue($textAttribute);
			else	
				return "";
		}
	}
}

function getTranslatedContentRecordSet($translatedContentId, $queryTransactionInformation) {
	global
		$translatedContentTable, $languageAttribute, $textAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$languageAttribute,
		array(
			'language_id' => $languageAttribute, 
			'text_id' => $textAttribute
		),
		$translatedContentTable,
		array("translated_content_id=$translatedContentId")
	);
	
	expandTextReferencesInRecordSet($recordSet, array($textAttribute));
	
	return $recordSet;
} 

function getFilteredTranslatedContentRecordSet($translatedContentId, $filterLanguageId, $queryTransactionInformation) {
	global
		$translatedContentTable, $languageAttribute, $textAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$languageAttribute,
		array(
			'language_id' => $languageAttribute, 
			'text_id' => $textAttribute
		),
		$translatedContentTable,
		array(
			"translated_content_id=$translatedContentId",
			"language_id=$filterLanguageId"
		)
	);
	
	expandTextReferencesInRecordSet($recordSet, array($textAttribute));
	
	return $recordSet;
}

function getSynonymAndTranslationRecordSet($definedMeaningId, $filterLanguageId, $queryTransactionInformation) {
	global
		$syntransTable, $syntransIdAttribute, $expressionAttribute, $identicalMeaningAttribute, $objectAttributesAttribute;

	$restrictions = array("defined_meaning_id=$definedMeaningId");
	
	if ($filterLanguageId != 0) 
		$restrictions[] =
			"expression_id IN (" .
				"SELECT expressions.expression_id" .
				" FROM uw_expression_ns AS expressions" .
				" WHERE expressions.expression_id=expression_id" .
				" AND language_id=$filterLanguageId" .
				" AND " . getLatestTransactionRestriction('expressions') .
			")";
	
	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$syntransIdAttribute,
		array(
			'syntrans_sid' => $syntransIdAttribute, 
			'expression_id' => $expressionAttribute,
			'identical_meaning' => $identicalMeaningAttribute
		),
		$syntransTable,
		$restrictions
	);
	
	if ($filterLanguageId == 0)
		expandExpressionReferencesInRecordSet($recordSet, array($expressionAttribute));
	else
		expandExpressionSpellingsInRecordSet($recordSet, array($expressionAttribute));

	//add object attributes attribute to the generated structure 
	//and expand the records
	$recordSet->getStructure()->attributes[] = $objectAttributesAttribute;
	expandObjectAttributesAttribute($recordSet, $syntransIdAttribute, $filterLanguageId, $queryTransactionInformation);
	return $recordSet;
}

function expandObjectAttributesAttribute($recordSet, $objectIdAttribute, $filterLanguageId, $queryTransactionInformation) {
	global
		$objectAttributesAttribute;
		
	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);
		$record->setAttributeValue(
			$objectAttributesAttribute, 
			getObjectAttributesRecord(
				$record->getAttributeValue($objectIdAttribute), 
				$filterLanguageId, 
				$queryTransactionInformation
			)
		);		
	}
}

function getDefinedMeaningReferenceRecord($definedMeaningId) {
	global
		$definedMeaningReferenceStructure, $definedMeaningIdAttribute, $definedMeaningLabelAttribute,
		$definedMeaningDefiningExpressionAttribute;
	
	$record = new ArrayRecord($definedMeaningReferenceStructure);
	$record->setAttributeValue($definedMeaningIdAttribute, $definedMeaningId);
	$record->setAttributeValue($definedMeaningLabelAttribute, definedMeaningExpression($definedMeaningId));
	$record->setAttributeValue($definedMeaningDefiningExpressionAttribute, definingExpression($definedMeaningId));
	
	return $record;
}

function getDefinedMeaningRelationsRecordSet($definedMeaningId, $filterLanguageId, $filterRelationTypes, $queryTransactionInformation) {
	global
		$meaningRelationsTable, $relationIdAttribute, $relationTypeAttribute, 
		$objectAttributesAttribute, $otherDefinedMeaningAttribute;

	$restrictions = array("meaning1_mid=$definedMeaningId");

	if (count($filterRelationTypes) > 0) 
		$restrictions[] = "relationtype_mid NOT IN (". implode(", ", $filterRelationTypes) .")";

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$relationIdAttribute,
		array(
			'relation_id' => $relationIdAttribute, 
			'relationtype_mid' => $relationTypeAttribute, 
			'meaning2_mid' => $otherDefinedMeaningAttribute
		),
		$meaningRelationsTable,
		$restrictions,
		array('add_transaction_id')
	);
	
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($relationTypeAttribute, $otherDefinedMeaningAttribute));

	//add object attributes attribute to the generated structure 
	//and expand the records
	$recordSet->getStructure()->attributes[] = $objectAttributesAttribute;
	expandObjectAttributesAttribute($recordSet, $relationIdAttribute, $filterLanguageId, $queryTransactionInformation);
	
	return $recordSet;
}

function getDefinedMeaningReciprocalRelationsRecordSet($definedMeaningId, $filterLanguageId, $queryTransactionInformation) {
	global
		$meaningRelationsTable, $relationIdAttribute, $relationTypeAttribute, 
		$otherDefinedMeaningAttribute, $objectAttributesAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$relationIdAttribute,
		array(
			'relation_id' => $relationIdAttribute, 
			'relationtype_mid' => $relationTypeAttribute, 
			'meaning1_mid' => $otherDefinedMeaningAttribute
		),
		$meaningRelationsTable,
		array("meaning2_mid=$definedMeaningId"),
		array('relationtype_mid')
	);
	
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($relationTypeAttribute, $otherDefinedMeaningAttribute));

	//add object attributes attribute to the generated structure 
	//and expand the records
	$recordSet->getStructure()->attributes[] = $objectAttributesAttribute;
	expandObjectAttributesAttribute($recordSet, $relationIdAttribute, $filterLanguageId, $queryTransactionInformation);
	
	return $recordSet;
}

function getPossiblySynonymousRecordSet($definedMeaningId, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation) {
	global
		$meaningRelationsTable, $possiblySynonymousIdAttribute, $possibleSynonymAttribute, 
		$objectAttributesAttribute, $otherDefinedMeaningAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$possiblySynonymousIdAttribute,
		array(
			'relation_id' => $possiblySynonymousIdAttribute, 
			'meaning2_mid' => $possibleSynonymAttribute
		),
		$meaningRelationsTable,
		array(
			"meaning1_mid=$definedMeaningId",
			"relationtype_mid=" . $possiblySynonymousRelationTypeId
		),
		array('add_transaction_id')
	);
	
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($possibleSynonymAttribute));

	//add object attributes attribute to the generated structure 
	//and expand the records
	$recordSet->getStructure()->attributes[] = $objectAttributesAttribute;
	expandObjectAttributesAttribute($recordSet, $possiblySynonymousIdAttribute, $filterLanguageId, $queryTransactionInformation);
	
	return $recordSet;
}

function getDefinedMeaningCollectionMembershipRecordSet($definedMeaningId, $queryTransactionInformation) {
	global
		$collectionMembershipsTable, $collectionIdAttribute, $collectionMeaningAttribute, $sourceIdentifierAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$collectionIdAttribute,
		array(
			'collection_id' => $collectionIdAttribute,
			'internal_member_id' => $sourceIdentifierAttribute
		),
		$collectionMembershipsTable,
		array("member_mid=$definedMeaningId")
	);

	$recordSet->getStructure()->atttributes[] = $collectionMeaningAttribute;

	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);
		$record->setAttributeValue($collectionMeaningAttribute, getCollectionMeaningId($record->getAttributeValue($collectionIdAttribute)));	
	}
	
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($collectionMeaningAttribute));
	
	return $recordSet;
}

function getTextAttributesValuesRecordSet($objectId, $filterLanguageId, $queryTransactionInformation) {
	global
		$textAttributeValuesTable, $textAttributeIdAttribute, $textAttributeObjectAttribute,
		$textAttributeAttribute, $textAttribute, $objectAttributesAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$textAttributeIdAttribute,
		array(
			'value_id' => $textAttributeIdAttribute,
			'object_id' => $textAttributeObjectAttribute,
			'attribute_mid' => $textAttributeAttribute,
			'text' => $textAttribute
		),
		$textAttributeValuesTable,
		array("object_id=$objectId")
	);
	
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($textAttributeAttribute));

	//add object attributes attribute to the generated structure 
	//and expand the records
	$recordSet->getStructure()->attributes[] = $objectAttributesAttribute;
	expandObjectAttributesAttribute($recordSet, $textAttributeIdAttribute, $filterLanguageId, $queryTransactionInformation);	
	
	return $recordSet;
}

function getURLAttributeValuesRecordSet($objectId, $filterLanguageId, $queryTransactionInformation) {
	global
		$urlAttributeValuesTable, $urlAttributeIdAttribute, $urlAttributeObjectAttribute,
		$urlAttributeAttribute, $urlAttribute, $objectAttributesAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$urlAttributeIdAttribute,
		array(
			'value_id' => $urlAttributeIdAttribute,
			'object_id' => $urlAttributeObjectAttribute,
			'attribute_mid' => $urlAttributeAttribute,
			'url' => $urlAttribute
		),
		$urlAttributeValuesTable,
		array("object_id=$objectId")
	);
	
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($urlAttributeAttribute));

	//add object attributes attribute to the generated structure 
	//and expand the records
	$recordSet->getStructure()->attributes[] = $objectAttributesAttribute;
	expandObjectAttributesAttribute($recordSet, $urlAttributeIdAttribute, $filterLanguageId, $queryTransactionInformation);	
	
	return $recordSet;
}

function getTranslatedTextAttributeValuesRecordSet($objectId, $filterLanguageId, $queryTransactionInformation) {
	global
		$translatedTextAttributeIdAttribute, $translatedContentAttributeValuesTable, $translatedTextAttributeAttribute,
		$objectAttributesAttribute, $translatedTextValueAttribute, $translatedTextValueIdAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$translatedTextAttributeIdAttribute,
		array(
			'value_id' => $translatedTextAttributeIdAttribute,
			'attribute_mid' => $translatedTextAttributeAttribute,
			'value_tcid' => $translatedTextValueIdAttribute
		),
		$translatedContentAttributeValuesTable,
		array("object_id=$objectId")
	);
	
	$recordSet->getStructure()->attributes[] = $translatedTextValueAttribute;
	
	expandTranslatedContentsInRecordSet($recordSet, $translatedTextValueIdAttribute, $translatedTextValueAttribute, $filterLanguageId, $queryTransactionInformation);
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($translatedTextAttributeAttribute));

	//add object attributes attribute to the generated structure 
	//and expand the records
	$recordSet->getStructure()->attributes[] = $objectAttributesAttribute;
	expandObjectAttributesAttribute($recordSet, $translatedTextAttributeIdAttribute, $filterLanguageId, $queryTransactionInformation);
	return $recordSet;
}

function getOptionAttributeOptionsRecordSet($attributeId, $queryTransactionInformation) {
	global
		$optionAttributeOptionIdAttribute, $optionAttributeAttribute, $optionAttributeOptionAttribute, $languageAttribute, $optionAttributeOptionsTable;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$optionAttributeOptionIdAttribute,
		array(
			'option_id' => $optionAttributeOptionIdAttribute,
			'attribute_id' => $optionAttributeAttribute,
			'option_mid' => $optionAttributeOptionAttribute,
			'language_id' => $languageAttribute
		),
		$optionAttributeOptionsTable,
		array('attribute_id = ' . $attributeId)
	);

	expandDefinedMeaningReferencesInRecordSet($recordSet, array($optionAttributeOptionAttribute));

	return $recordSet;
}

function getOptionAttributeValuesRecordSet($objectId, $filterLanguageId, $queryTransactionInformation) {
	global
		$optionAttributeIdAttribute, $optionAttributeObjectAttribute, $optionAttributeOptionIdAttribute, $optionAttributeAttribute,$optionAttributeOptionAttribute, $optionAttributeValuesTable, $objectAttributesAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$optionAttributeIdAttribute,
		array(
			'value_id' => $optionAttributeIdAttribute,
			'object_id' => $optionAttributeObjectAttribute,
			'option_id' => $optionAttributeOptionIdAttribute
		),
		$optionAttributeValuesTable,
		array('object_id = ' . $objectId)
	);

	expandOptionsInRecordSet($recordSet, $queryTransactionInformation);
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($optionAttributeAttribute, $optionAttributeOptionAttribute));

	/* Add object attributes attribute to the generated structure
		and expand the records. */
	$recordSet->getStructure()->attributes[] = $objectAttributesAttribute;
	expandObjectAttributesAttribute($recordSet, $optionAttributeIdAttribute, $filterLanguageId, $queryTransactionInformation);

	return $recordSet;
}

/* XXX: This can probably be combined with other functions. In fact, it probably should be. Do it. */
function expandOptionsInRecordSet($recordSet, $queryTransactionInformation) {
	global
		$optionAttributeOptionIdAttribute, $optionAttributeIdAttribute, $optionAttributeAttribute, $optionAttributeOptionAttribute, $optionAttributeOptionsTable, $classAttributesTable;

	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);

		$optionRecordSet = queryRecordSet(
			$queryTransactionInformation,
			$optionAttributeOptionIdAttribute,
			array(
				'attribute_id' => $optionAttributeIdAttribute,
				'option_mid' => $optionAttributeOptionAttribute
			),
			$optionAttributeOptionsTable,
			array('option_id = ' . $record->getAttributeValue($optionAttributeOptionIdAttribute))
		);

		$optionRecord = $optionRecordSet->getRecord(0);
		$record->setAttributeValue(
			$optionAttributeOptionAttribute, 
			$optionRecord->getAttributeValue($optionAttributeOptionAttribute)
		);

		$optionRecordSet = queryRecordSet(
			$queryTransactionInformation,
			$optionAttributeIdAttribute,
			array('attribute_mid' => $optionAttributeAttribute),
			$classAttributesTable,
			array('object_id = ' . $optionRecord->getAttributeValue($optionAttributeIdAttribute))
		);

		$optionRecord = $optionRecordSet->getRecord(0);
		$record->setAttributeValue(
			$optionAttributeAttribute,
			$optionRecord->getAttributeValue($optionAttributeAttribute)
		);
	} 
}

function getDefinedMeaningClassMembershipRecordSet($definedMeaningId, $queryTransactionInformation) {
	global
		$classMembershipsTable, $classMembershipIdAttribute, $classAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$classMembershipIdAttribute,
		array(
			'class_membership_id' => $classMembershipIdAttribute, 
			'class_mid' => $classAttribute
		),
		$classMembershipsTable,
		array("class_member_mid=$definedMeaningId")
	);
	
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($classAttribute));
	
	return $recordSet;
}

?>
