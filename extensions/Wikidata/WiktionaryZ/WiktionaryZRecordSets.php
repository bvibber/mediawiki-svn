<?php

require_once('WiktionaryZAttributes.php');
require_once('Record.php');
require_once('RecordSet.php');
require_once('Expression.php');
require_once('Transaction.php');
require_once('WikiDataTables.php');

function queryRecordSet($transactionInformation, $keyAttribute, $fieldAttributeMapping, $table, $restrictions, $orderBy = array(), $count = -1, $offset = 0) {
	$dbr =& wfGetDB(DB_SLAVE);
	
	$selectFields = array_keys($fieldAttributeMapping);
	$attributes = array_values($fieldAttributeMapping);
	$tableNames = array($table->name);

	if ($table->isVersioned) {
		$restrictions[] = $transactionInformation->getRestriction($table);
		$tableNames = array_merge($tableNames, $transactionInformation->getTables());
		$orderBy = array_merge($orderBy, $transactionInformation->versioningOrderBy());
		$groupBy = $transactionInformation->versioningGroupBy($table);
		$selectFields = array_merge($selectFields, $transactionInformation->versioningFields($table->name));
		$allAttributes = array_merge($attributes, $transactionInformation->versioningAttributes());
	}
	else {
		$allAttributes = $attributes;
		$groupBy = array();
	}
	
	$query = "SELECT ". implode(", ", $selectFields) . 
			" FROM ". implode(", ", $tableNames);

	if (count($restrictions) > 0)
		$query .= " WHERE ". implode(' AND ', $restrictions);
	
	if (count($groupBy) > 0)
		$query .= " GROUP BY " . implode(', ', $groupBy);

	if (count($orderBy) > 0)
		$query .= " ORDER BY " . implode(', ', $orderBy);
		
	if ($count != -1) 
		$query .= " LIMIT " . $offset . ", " . $count;
	
	$queryResult = $dbr->query($query);
	
	$structure = new Structure($allAttributes);
	$recordSet = new ArrayRecordSet($structure, new Structure($keyAttribute));

	while ($row = $dbr->fetchRow($queryResult)) {
		$record = new ArrayRecord($structure);

		for ($i = 0; $i < count($attributes); $i++)
			$record->setAttributeValue($attributes[$i], $row[$i]);
			
		$transactionInformation->setVersioningAttributes($record, $row);	
		$recordSet->add($record);
	} 
		
	return $recordSet;
}

function getUniqueIdsInRecordSet($recordSet, $idAttributes) {
	$ids = array();
	
	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);
		
		foreach($idAttributes as $idAttribute) 
			$ids[] = $record->getAttributeValue($idAttribute);
	}
	
	return array_unique($ids);
}

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

function expandTranslatedContentInRecord($record, $idAttribute, $translatedContentAttribute, $queryTransactionInformation) {
	$record->setAttributeValue(
		$translatedContentAttribute, 
		getTranslatedContentRecordSet($record->getAttributeValue($idAttribute), $queryTransactionInformation)
	);
}

function expandTranslatedContentsInRecordSet($recordSet, $idAttribute, $translatedContentAttribute, $queryTransactionInformation) {
	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) 
		expandTranslatedContentInRecord($recordSet->getRecord($i), $idAttribute, $translatedContentAttribute, $queryTransactionInformation);
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

function getExpressionMeaningsRecordSet($expressionId, $exactMeaning, $queryTransactionInformation) {
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
		$recordSet->addRecord(array($definedMeaningId, getDefinedMeaningDefinition($definedMeaningId), getDefinedMeaningRecord($definedMeaningId, $queryTransactionInformation)));
	}

	return $recordSet;
}

function getExpressionMeaningsRecord($expressionId, $queryTransactionInformation) {
	global
		$expressionMeaningsStructure, $expressionExactMeaningsAttribute, $expressionApproximateMeaningsAttribute;
		
	$record = new ArrayRecord($expressionMeaningsStructure);
	$record->setAttributeValue($expressionExactMeaningsAttribute, getExpressionMeaningsRecordSet($expressionId, true, $queryTransactionInformation));
	$record->setAttributeValue($expressionApproximateMeaningsAttribute, getExpressionMeaningsRecordSet($expressionId, false, $queryTransactionInformation));
	
	return $record;
}

function getExpressionsRecordSet($spelling, $queryTransactionInformation) {
	global
		$expressionIdAttribute, $expressionAttribute, $languageAttribute, $expressionMeaningsAttribute;

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT expression_id, language_id " .
		" FROM uw_expression_ns" .
		" WHERE spelling=BINARY " . $dbr->addQuotes($spelling) .
		" AND " . getLatestTransactionRestriction('uw_expression_ns') .
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

		$result->addRecord(array($expression->expression_id, $expressionRecord, getExpressionMeaningsRecord($expression->expression_id, $queryTransactionInformation)));
	}

	return $result;
}

function getDefinedMeaningRecord($definedMeaningId, $queryTransactionInformation) {
	global
		$definedMeaningAttribute, $definitionAttribute, $classAttributesAttribute, 
		$alternativeDefinitionsAttribute, $synonymsAndTranslationsAttribute,
		$relationsAttribute, $reciprocalRelationsAttribute,
		$classMembershipAttribute, $collectionMembershipAttribute, $objectAttributesAttribute;

	$record = new ArrayRecord($definedMeaningAttribute->type->getStructure());
	$record->setAttributeValue($definitionAttribute, getDefinedMeaningDefinitionRecord($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($classAttributesAttribute, getClassAttributesRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($alternativeDefinitionsAttribute, getAlternativeDefinitionsRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($synonymsAndTranslationsAttribute, getSynonymAndTranslationRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($relationsAttribute, getDefinedMeaningRelationsRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($reciprocalRelationsAttribute, getDefinedMeaningReciprocalRelationsRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($classMembershipAttribute, getDefinedMeaningClassMembershipRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($collectionMembershipAttribute, getDefinedMeaningCollectionMembershipRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($objectAttributesAttribute, getObjectAttributesRecord($definedMeaningId, $queryTransactionInformation));

	return $record;
}

function getClassAttributesRecordSet($definedMeaningId, $queryTransactionInformation) {
	global
		$classAttributesTable, $classAttributeIdAttribute, $classAttributeLevelAttribute, $classAttributeAttributeAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$classAttributeIdAttribute,
		array(
			'object_id' => $classAttributeIdAttribute,
			'level_mid' => $classAttributeLevelAttribute,
			'attribute_mid' => $classAttributeAttributeAttribute
		),
		$classAttributesTable,
		array("class_mid=$definedMeaningId")
	);
	
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($classAttributeLevelAttribute ,$classAttributeAttributeAttribute));
	return $recordSet;
}

function getAlternativeDefinitionsRecordSet($definedMeaningId, $queryTransactionInformation) {
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
	
	expandTranslatedContentsInRecordSet($recordSet, $definitionIdAttribute, $alternativeDefinitionAttribute, $queryTransactionInformation);									
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($sourceAttribute));

	return $recordSet;
}

function getDefinedMeaningDefinitionRecord($definedMeaningId, $queryTransactionInformation) {
	global
		$definitionAttribute, $translatedTextAttribute, $objectAttributesAttribute;
		
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	$record = new ArrayRecord($definitionAttribute->type->getStructure());
	$record->setAttributeValue($translatedTextAttribute, getTranslatedContentRecordSet($definitionId, $queryTransactionInformation));
	$record->setAttributeValue($objectAttributesAttribute, getObjectAttributesRecord($definitionId, $queryTransactionInformation));

	return $record;
}

function getObjectAttributesRecord($objectId, $queryTransactionInformation) {
	global
		$objectAttributesAttribute, $objectIdAttribute, $textAttributeValuesAttribute, $translatedTextAttributeValuesAttribute; 
		
	$record = new ArrayRecord($objectAttributesAttribute->type->getStructure());
	
	$record->setAttributeValue($objectIdAttribute, $objectId);
	$record->setAttributeValue($textAttributeValuesAttribute, getTextAttributesValuesRecordSet($objectId, $queryTransactionInformation));
	$record->setAttributeValue($translatedTextAttributeValuesAttribute, getTranslatedTextAttributeValuesRecordSet($objectId, $queryTransactionInformation));	

	return $record;
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

function getSynonymAndTranslationRecordSet($definedMeaningId, $queryTransactionInformation) {
	global
		$syntransTable, $syntransIdAttribute, $expressionAttribute, $identicalMeaningAttribute, $objectAttributesAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$syntransIdAttribute,
		array(
			'syntrans_sid' => $syntransIdAttribute, 
			'expression_id' => $expressionAttribute,
			'identical_meaning' => $identicalMeaningAttribute
		),
		$syntransTable,
		array("defined_meaning_id=$definedMeaningId")
	);
	
	expandExpressionReferencesInRecordSet($recordSet, array($expressionAttribute));

	//add object attributes attribute to the generated structure 
	//and expand the records
	$recordSet->getStructure()->attributes[] = $objectAttributesAttribute;
	expandObjectAttributesAttribute($recordSet, $syntransIdAttribute, $queryTransactionInformation);
	return $recordSet;
}

function expandObjectAttributesAttribute($recordSet, $objectIdAttribute, $queryTransactionInformation) {
	global
		$objectAttributesAttribute;
		
	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);
		$record->setAttributeValue($objectAttributesAttribute, getObjectAttributesRecord($record->getAttributeValue($objectIdAttribute), $queryTransactionInformation));		
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

function getDefinedMeaningRelationsRecordSet($definedMeaningId, $queryTransactionInformation) {
	global
		$meaningRelationsTable, $relationIdAttribute, $relationTypeAttribute, 
		$objectAttributesAttribute, $otherDefinedMeaningAttribute;

//	$startTime = microtime(true);
	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$relationIdAttribute,
		array(
			'relation_id' => $relationIdAttribute, 
			'relationtype_mid' => $relationTypeAttribute, 
			'meaning2_mid' => $otherDefinedMeaningAttribute
		),
		$meaningRelationsTable,
		array("meaning1_mid=$definedMeaningId"),
		array('add_transaction_id')
	);
	
//	echo "<!--" . (microtime(true) - $startTime). " -->";
	
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($relationTypeAttribute, $otherDefinedMeaningAttribute));

	//add object attributes attribute to the generated structure 
	//and expand the records
	$recordSet->getStructure()->attributes[] = $objectAttributesAttribute;
	expandObjectAttributesAttribute($recordSet, $relationIdAttribute, $queryTransactionInformation);
	
	return $recordSet;
}

function getDefinedMeaningReciprocalRelationsRecordSet($definedMeaningId, $queryTransactionInformation) {
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
	expandObjectAttributesAttribute($recordSet, $relationIdAttribute, $queryTransactionInformation);
	
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

function getTextAttributesValuesRecordSet($objectId, $queryTransactionInformation) {
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
	expandObjectAttributesAttribute($recordSet, $textAttributeIdAttribute, $queryTransactionInformation);	
	
	return $recordSet;
}

function getTranslatedTextAttributeValuesRecordSet($objectId, $queryTransactionInformation) {
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
	
	expandTranslatedContentsInRecordSet($recordSet, $translatedTextValueIdAttribute, $translatedTextValueAttribute, $queryTransactionInformation);
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($translatedTextAttributeAttribute));

	//add object attributes attribute to the generated structure 
	//and expand the records
	$recordSet->getStructure()->attributes[] = $objectAttributesAttribute;
	expandObjectAttributesAttribute($recordSet, $translatedTextAttributeIdAttribute, $queryTransactionInformation);
	return $recordSet;
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
