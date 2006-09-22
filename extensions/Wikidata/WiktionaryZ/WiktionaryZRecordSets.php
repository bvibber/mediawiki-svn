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
	$tables, $meaningRelationsTable, $classMembershipsTable, $collectionMembershipsTable;
	
$meaningRelationsTable = new Table('uw_meaning_relations', true);
$classMembershipsTable = new Table('uw_class_membership', true);
$collectionMembershipsTable = new Table('uw_collection_contents', true);

interface QueryTransactionInformation {
	public function getRestriction($tableName);
	public function versioningAttributes();
	public function versioningFields();
	public function versioningOrderBy();
	public function setVersioningAttributes($record, $row);
}

class QueryLatestTransactionInformation {
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

class QueryHistoryTransactionInformation {
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

function expandDefinedMeaningReferencesInRecordSet($recordSet, $definedMeaningAttributes) {
	$definedMeaningIds = array();
	
	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);
		
		foreach($definedMeaningAttributes as $definedMeaningAttribute) {
			$definedMeaningId = $record->getAttributeValue($definedMeaningAttribute);
			
			if (!in_array($definedMeaningId, $definedMeaningIds))
				$definedMeaningIds[] = $definedMeaningId;
		}
	}
	
	$definedMeaningReferenceRecords = getDefinedMeaningReferenceRecords($definedMeaningIds);

	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);
		
		foreach($definedMeaningAttributes as $definedMeaningAttribute)
			$record->setAttributeValue(
				$definedMeaningAttribute, 
				$definedMeaningReferenceRecords[$record->getAttributeValue($definedMeaningAttribute)]
			);
	} 
}

function getExpressionMeaningsRecordSet($expressionId, $exactMeaning) {
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
		$recordSet->addRecord(array($definedMeaningId, getDefinedMeaningDefinition($definedMeaningId), getDefinedMeaningRecord($definedMeaningId)));
	}

	return $recordSet;
}

function getExpressionMeaningsRecord($expressionId) {
	global
		$expressionMeaningsStructure, $expressionExactMeaningsAttribute, $expressionApproximateMeaningsAttribute;
		
	$record = new ArrayRecord($expressionMeaningsStructure);
	$record->setAttributeValue($expressionExactMeaningsAttribute, getExpressionMeaningsRecordSet($expressionId, true));
	$record->setAttributeValue($expressionApproximateMeaningsAttribute, getExpressionMeaningsRecordSet($expressionId, false));
	
	return $record;
}

function getExpressionsRecordSet($spelling) {
	global
		$expressionIdAttribute, $expressionAttribute, $languageAttribute, $expressionMeaningsAttribute;

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT expression_id, language_id from uw_expression_ns WHERE spelling=BINARY " . $dbr->addQuotes($spelling));
	$result = new ArrayRecordSet(new Structure($expressionIdAttribute, $expressionAttribute, $expressionMeaningsAttribute), new Structure($expressionIdAttribute));
	$expressionStructure = new Structure($languageAttribute);

	while($expression = $dbr->fetchObject($queryResult)) {
		$expressionRecord = new ArrayRecord($expressionStructure);
		$expressionRecord->setAttributeValue($languageAttribute, $expression->language_id);

		$result->addRecord(array($expression->expression_id, $expressionRecord, getExpressionMeaningsRecord($expression->expression_id)));
	}

	return $result;
}

function getDefinedMeaningRecord($definedMeaningId) {
	global
		$wgRequest, $definedMeaningAttribute, $definitionAttribute, $alternativeDefinitionsAttribute, $synonymsAndTranslationsAttribute,
		$relationsAttribute, $classMembershipAttribute, $collectionMembershipAttribute, $textAttributeValuesAttribute;

	if ($wgRequest->getText('action') == 'history')
		$queryTransactionInformation = new QueryHistoryTransactionInformation();
	else
		$queryTransactionInformation = new QueryLatestTransactionInformation(); 

	$record = new ArrayRecord($definedMeaningAttribute->type->getStructure());
	$record->setAttributeValue($definitionAttribute, getDefinedMeaningDefinitionRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($alternativeDefinitionsAttribute, getAlternativeDefinitionsRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($synonymsAndTranslationsAttribute, getSynonymAndTranslationRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($relationsAttribute, getDefinedMeaningRelationsRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($classMembershipAttribute, getDefinedMeaningClassMembershipRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($collectionMembershipAttribute, getDefinedMeaningCollectionMembershipRecordSet($definedMeaningId, $queryTransactionInformation));
	$record->setAttributeValue($textAttributeValuesAttribute, getDefinedMeaningTextAttributeValuesRecordSet($definedMeaningId, $queryTransactionInformation));

	return $record;
}

function getAlternativeDefinitionsRecordSet($definedMeaningId, $queryTransactionInformation) {
	global
		$definitionIdAttribute, $alternativeDefinitionAttribute, $sourceAttribute;

	$recordSet = new ArrayRecordSet(new Structure($definitionIdAttribute, $alternativeDefinitionAttribute, $sourceAttribute), new Structure($definitionIdAttribute));

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT meaning_text_tcid, source_id FROM uw_alt_meaningtexts" .
								" WHERE meaning_mid=$definedMeaningId".
								" AND ". getLatestTransactionRestriction('uw_alt_meaningtexts'));

	while ($alternativeDefinition = $dbr->fetchObject($queryResult)) 
		$recordSet->addRecord(array($alternativeDefinition->meaning_text_tcid, 
									getTranslatedTextRecordSet($alternativeDefinition->meaning_text_tcid), 
									getDefinedMeaningReferenceRecord($alternativeDefinition->source_id)));

	return $recordSet;
}

function getDefinedMeaningDefinitionRecordSet($definedMeaningId, $queryTransactionInformation) {
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	
	return getTranslatedTextRecordSet($definitionId);
}

function getTranslatedTextRecordSet($textId) {
	global
		$wgRequest;

	if ($wgRequest->getText('action') == 'history')
		return getTranslatedTextHistoryRecordSet($textId);
	else
		return getTranslatedTextLatestRecordSet($textId);
}

function getTranslatedTextLatestRecordSet($textId) {
	global
		$languageAttribute, $textAttribute;

	$dbr =& wfGetDB(DB_SLAVE);

	$recordset = new ArrayRecordSet(new Structure($languageAttribute, $textAttribute),
									new Structure($languageAttribute));

	$queryResult = $dbr->query("SELECT language_id, old_text FROM translated_content tc, text t WHERE ".
								"tc.translated_content_id=$textId AND tc.text_id=t.old_id AND " . getViewTransactionRestriction('tc'));

	while ($translatedText= $dbr->fetchObject($queryResult))
		$recordset->addRecord(array($translatedText->language_id, $translatedText->old_text));

	return $recordset;
}

function getTranslatedTextHistoryRecordSet($textId) {
	global
		$languageAttribute, $textAttribute, $recordLifeSpanAttribute;

	$dbr =& wfGetDB(DB_SLAVE);

	$recordSet = new ArrayRecordSet(new Structure($languageAttribute, $textAttribute, $recordLifeSpanAttribute),
									new Structure($languageAttribute));

	$queryResult = $dbr->query("SELECT language_id, old_text, add_transaction_id, remove_transaction_id, NOT remove_transaction_id IS NULL AS is_live" .
								" FROM translated_content tc, text t " .
								" WHERE tc.translated_content_id=$textId AND tc.text_id=t.old_id AND " . getViewTransactionRestriction('tc') .
								" ORDER BY is_live, add_transaction_id DESC");

	while ($translatedText= $dbr->fetchObject($queryResult))
		$recordSet->addRecord(array($translatedText->language_id, $translatedText->old_text,
									getRecordLifeSpanTuple($translatedText->add_transaction_id,
															$translatedText->remove_transaction_id)));

	return $recordSet;
}

function getSynonymAndTranslationRecordSet($definedMeaningId, $queryTransactionInformation) {
	global
		$wgRequest;

	if ($wgRequest->getText('action') == 'history')
		return getSynonymAndTranslationHistoryRecordSet($definedMeaningId);
	else
		return getSynonymAndTranslationLatestRecordSet($definedMeaningId);
}

function getSynonymAndTranslationHistoryRecordSet($definedMeaningId) {
	global
		$expressionIdAttribute, $expressionAttribute, $languageAttribute, $spellingAttribute, $identicalMeaningAttribute,
		$recordLifeSpanAttribute;

	$dbr =& wfGetDB(DB_SLAVE);

	$expressionStructure = $expressionAttribute->type->getStructure();
	$recordset = new ArrayRecordSet(new Structure($expressionIdAttribute, $expressionAttribute, $identicalMeaningAttribute, $recordLifeSpanAttribute),
									new Structure($expressionIdAttribute));
	$queryResult = $dbr->query("SELECT uw_expression_ns.expression_id, spelling, language_id, endemic_meaning, uw_syntrans.add_transaction_id AS syntrans_add, uw_syntrans.remove_transaction_id AS syntrans_remove, NOT uw_syntrans.remove_transaction_id IS NULL is_live " .
								" FROM uw_syntrans, uw_expression_ns " .
								" WHERE uw_syntrans.defined_meaning_id=$definedMeaningId " .
								" AND uw_expression_ns.expression_id=uw_syntrans.expression_id ".
								" ORDER BY is_live, uw_syntrans.add_transaction_id DESC");

	while($synonymOrTranslation = $dbr->fetchObject($queryResult)) {
		$expressionRecord = new ArrayRecord($expressionStructure);
		$expressionRecord->setAttributeValuesByOrder(array($synonymOrTranslation->language_id, $synonymOrTranslation->spelling));

		$recordset->addRecord(array($synonymOrTranslation->expression_id, $expressionRecord, $synonymOrTranslation->endemic_meaning,
									getRecordLifeSpanTuple($synonymOrTranslation->syntrans_add,
															$synonymOrTranslation->syntrans_remove)));
	}

	return $recordset;
}

function getSynonymAndTranslationLatestRecordSet($definedMeaningId) {
	global
		$expressionIdAttribute, $expressionStructure, $expressionAttribute, $languageAttribute, $spellingAttribute, $identicalMeaningAttribute;

	$dbr =& wfGetDB(DB_SLAVE);

	$recordset = new ArrayRecordSet(new Structure($expressionIdAttribute, $expressionAttribute, $identicalMeaningAttribute), new Structure($expressionIdAttribute));
	$queryResult = $dbr->query(
		"SELECT uw_expression_ns.expression_id, spelling, language_id, endemic_meaning" .
		" FROM uw_syntrans, uw_expression_ns" .
		" WHERE uw_syntrans.defined_meaning_id=$definedMeaningId " .
		" AND uw_expression_ns.expression_id=uw_syntrans.expression_id AND ". 
		getLatestTransactionRestriction('uw_syntrans')
	);

	while($synonymOrTranslation = $dbr->fetchObject($queryResult)) {
		$expressionRecord = new ArrayRecord($expressionStructure);
		$expressionRecord->setAttributeValuesByOrder(array($synonymOrTranslation->language_id, $synonymOrTranslation->spelling));

		$recordset->addRecord(array($synonymOrTranslation->expression_id, $expressionRecord, $synonymOrTranslation->endemic_meaning));
	}

	return $recordset;
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

function getDefinedMeaningReferenceRecords($definedMeaningIds) {
	$result = array();
	
	foreach($definedMeaningIds as $definedMeaningId)
		$result[$definedMeaningId] = getDefinedMeaningReferenceRecord($definedMeaningId);
		
	return $result;
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

	// Both the record set as the records in that set use the same Structure object
	// Updating the structure once, updates it for all
	// Should be an general accepted operation on record sets in the future
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
		$textAttributeValuesStructure, $textAttributeAttribute, $textValueIdAttribute;

	$recordSet = new ArrayRecordSet($textAttributeValuesStructure, new Structure($textAttributeAttribute, $textValueIdAttribute));

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT attribute_mid, value_tcid FROM uw_dm_text_attribute_values" .
								" WHERE defined_meaning_id=$definedMeaningId" .
								" AND " . getLatestTransactionRestriction('uw_dm_text_attribute_values'));

	while ($attributeValue = $dbr->fetchObject($queryResult))
		$recordSet->addRecord(array(getDefinedMeaningReferenceRecord($attributeValue->attribute_mid), 
									$attributeValue->value_tcid, 
									getTranslatedTextRecordSet($attributeValue->value_tcid)));

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
