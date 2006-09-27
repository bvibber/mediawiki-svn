<?php

require_once('WiktionaryZAttributes.php');
require_once('Record.php');
require_once('RecordSet.php');
require_once('Expression.php');
require_once('Transaction.php');

class Table {
	public $name;
	public $isVersioned;
	
	public function __construct($name, $isVersioned) {
		$this->name = $name;
		$this->isVersioned = $isVersioned;
	}
}

global
	$tables, $meaningRelationsTable, $classMembershipsTable, $collectionMembershipsTable, $syntransTable, 
	$translatedContentTable, $alternativeDefinitionsTable, $textAttributeValuesTable;
	
$meaningRelationsTable = new Table('uw_meaning_relations', true);
$classMembershipsTable = new Table('uw_class_membership', true);
$collectionMembershipsTable = new Table('uw_collection_contents', true);
$syntransTable = new Table('uw_syntrans', true);
$translatedContentTable = new Table('translated_content', true);
$alternativeDefinitionsTable = new Table('uw_alt_meaningtexts', true);
$textAttributeValuesTable = new Table('uw_dm_text_attribute_values', true);

interface QueryTransactionInformation {
	public function getRestriction($tableName);
	public function versioningAttributes();
	public function versioningFields($tableName);
	public function versioningOrderBy();
	public function setVersioningAttributes($record, $row);
}

class QueryLatestTransactionInformation implements QueryTransactionInformation {
	public function getRestriction($tableName) {
		return getLatestTransactionRestriction($tableName);
	}
	
	public function versioningAttributes() {
		return array();
	}
	
	public function versioningFields($tableName) {
		return array();
	}
	
	public function versioningOrderBy() {
		return array();
	}
	
	public function setVersioningAttributes($record, $row) {
	}
}

class QueryHistoryTransactionInformation implements QueryTransactionInformation {
	public function getRestriction($tableName) {
		return "1";
	}
	
	public function versioningAttributes() {
		global
			$recordLifeSpanAttribute;
			
		return array($recordLifeSpanAttribute);
	}

	public function versioningFields($tableName) {
		return array($tableName . '.add_transaction_id', $tableName . '.remove_transaction_id', $tableName . '.remove_transaction_id IS NULL AS is_live');
	}

	public function versioningOrderBy() {
		return array('is_live DESC', 'add_transaction_id DESC');
	}
	
	public function setVersioningAttributes($record, $row) {
		global
			$recordLifeSpanAttribute;
			
		$record->setAttributeValue($recordLifeSpanAttribute, getRecordLifeSpanTuple($row['add_transaction_id'], $row['remove_transaction_id']));
	}
}

class QueryAtTransactionInformation implements QueryTransactionInformation {
	protected $transactionId;
	
	public function __construct($transactionId) {
		$this->transactionId = $transactionId;
	}
	
	public function getRestriction($tableName) {
		return getAtTransactionRestriction($tableName, $this->transactionId);
	}
	
	public function versioningAttributes() {
		global
			$recordLifeSpanAttribute;
			
		return array($recordLifeSpanAttribute);
	}
	
	public function versioningFields($tableName) {
		return array($tableName . '.add_transaction_id', $tableName . '.remove_transaction_id', $tableName . '.remove_transaction_id IS NULL AS is_live');
	}
	
	public function versioningOrderBy() {
		return array();
	}
	
	public function setVersioningAttributes($record, $row) {
		global
			$recordLifeSpanAttribute;
			
		$record->setAttributeValue($recordLifeSpanAttribute, getRecordLifeSpanTuple($row['add_transaction_id'], $row['remove_transaction_id']));
	}
}

function queryRecordSet($transactionInformation, $keyAttribute, $fieldAttributeMapping, $table, $restrictions, $orderBy = array()) {
	$dbr =& wfGetDB(DB_SLAVE);
	
	$selectFields = array_keys($fieldAttributeMapping);
	$attributes = array_values($fieldAttributeMapping);

	if ($table->isVersioned) {
		$restrictions[] = $transactionInformation->getRestriction($table->name);
		$orderBy = array_merge($orderBy, $transactionInformation->versioningOrderBy());
		$selectFields = array_merge($selectFields, $transactionInformation->versioningFields($table->name));
		$allAttributes = array_merge($attributes, $transactionInformation->versioningAttributes());
	}
	else
		$allAttributes = $attributes;
	
	$query = "SELECT ". implode(", ", $selectFields) . 
			" FROM ". $table->name .
			" WHERE ". implode(' AND ', $restrictions);
	
	if (count($orderBy) > 0)
		$query .= " ORDER BY " . implode(', ', $orderBy);
	
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
		
		foreach($idAttributes as $idAttribute) {
			$id = $record->getAttributeValue($idAttribute);
			
			if (!in_array($id, $ids))
				$ids[] = $id;
		}
	}
	
	return $ids;
}

function getDefinedMeaningReferenceRecords($definedMeaningIds) {
	$result = array();
	
	foreach($definedMeaningIds as $definedMeaningId)
		$result[$definedMeaningId] = getDefinedMeaningReferenceRecord($definedMeaningId);
		
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
		$queryResult = $dbr->query("SELECT expression_id, language_id, spelling" .
									" FROM uw_expression_ns" .
									" WHERE expression_id IN (". implode(', ', $expressionIds) .")" .
									" AND ". getLatestTransactionRestriction('uw_expression_ns'));
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
		$queryResult = $dbr->query("SELECT old_id, old_text" .
									" FROM text" .
									" WHERE old_id IN (". implode(', ', $textIds) .")");
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
		$endemicMeaning = 1;
	else
		$endemicMeaning = 0;
		
	$recordSet = new ArrayRecordSet($expressionMeaningStructure, new Structure($definedMeaningIdAttribute));

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT defined_meaning_id FROM uw_syntrans" .
								" WHERE expression_id=$expressionId AND endemic_meaning=" . $endemicMeaning .
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
	$queryResult = $dbr->query("SELECT expression_id, language_id from uw_expression_ns WHERE spelling=BINARY " . $dbr->addQuotes($spelling));
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
		$definedMeaningAttribute, $definitionAttribute, $alternativeDefinitionsAttribute, $synonymsAndTranslationsAttribute,
		$relationsAttribute, $reciprocalRelationsAttribute,
		$classMembershipAttribute, $collectionMembershipAttribute, $textAttributeValuesAttribute;

	$record = new ArrayRecord($definedMeaningAttribute->type->getStructure());
	$record->setAttributeValue($definitionAttribute, getDefinedMeaningDefinitionRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($alternativeDefinitionsAttribute, getAlternativeDefinitionsRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($synonymsAndTranslationsAttribute, getSynonymAndTranslationRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($relationsAttribute, getDefinedMeaningRelationsRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($reciprocalRelationsAttribute, getDefinedMeaningReciprocalRelationsRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($classMembershipAttribute, getDefinedMeaningClassMembershipRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($collectionMembershipAttribute, getDefinedMeaningCollectionMembershipRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($textAttributeValuesAttribute, getDefinedMeaningTextAttributeValuesRecordSet($definedMeaningId, $queryTransactionInformation));

	return $record;
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

function getDefinedMeaningDefinitionRecordSet($definedMeaningId, $queryTransactionInformation) {
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	
	return getTranslatedContentRecordSet($definitionId, $queryTransactionInformation);
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
		$syntransTable, $syntransIdAttribute, $expressionAttribute, $identicalMeaningAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$syntransIdAttribute,
		array(
			'syntrans_sid' => $syntransIdAttribute, 
			'expression_id' => $expressionAttribute,
			'endemic_meaning' => $identicalMeaningAttribute
		),
		$syntransTable,
		array("defined_meaning_id=$definedMeaningId")
	);
	
	expandExpressionReferencesInRecordSet($recordSet, array($expressionAttribute));
	
	return $recordSet;
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
		$meaningRelationsTable, $relationIdAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute;

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
		array('relationtype_mid')
	);
	
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($relationTypeAttribute, $otherDefinedMeaningAttribute));
	
	return $recordSet;
}

function getDefinedMeaningReciprocalRelationsRecordSet($definedMeaningId, $queryTransactionInformation) {
	global
		$meaningRelationsTable, $relationIdAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute;

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

function getDefinedMeaningTextAttributeValuesRecordSet($definedMeaningId, $queryTransactionInformation) {
	global
		$textAttributeValuesTable, $textAttributeAttribute, $textValueAttribute, $textValueIdAttribute;

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$textValueIdAttribute,
		array(
			'attribute_mid' => $textAttributeAttribute,
			'value_tcid' => $textValueIdAttribute
		),
		$textAttributeValuesTable,
		array("defined_meaning_id=$definedMeaningId")
	);
	
	$recordSet->getStructure->attributes[] = $textValueAttribute;
	
	expandTranslatedContentsInRecordSet($recordSet, $textValueIdAttribute, $textValueAttribute, $queryTransactionInformation);
	expandDefinedMeaningReferencesInRecordSet($recordSet, array($textAttributeAttribute));

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
