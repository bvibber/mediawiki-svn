<?php

require_once('WiktionaryZAttributes.php');
require_once('Record.php');
require_once('RecordSet.php');
require_once('Expression.php');

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
		$definedMeaningAttribute, $definitionAttribute, $alternativeDefinitionsAttribute, $synonymsAndTranslationsAttribute,
		$relationsAttribute, $classMembershipAttribute, $collectionMembershipAttribute, $textAttributeValuesAttribute;

	$record = new ArrayRecord($definedMeaningAttribute->type->getStructure());
	$record->setAttributeValue($definitionAttribute, getDefinedMeaningDefinitionRecordSet($definedMeaningId));
	$record->setAttributeValue($alternativeDefinitionsAttribute, getAlternativeDefinitionsRecordSet($definedMeaningId));
	$record->setAttributeValue($synonymsAndTranslationsAttribute, getSynonymAndTranslationRecordSet($definedMeaningId));
	$record->setAttributeValue($relationsAttribute, getDefinedMeaningRelationsRecordSet($definedMeaningId));
	$record->setAttributeValue($classMembershipAttribute, getDefinedMeaningClassMembershipRecordSet($definedMeaningId));
	$record->setAttributeValue($collectionMembershipAttribute, getDefinedMeaningCollectionMembershipRecordSet($definedMeaningId));
	$record->setAttributeValue($textAttributeValuesAttribute, getDefinedMeaningTextAttributeValuesRecordSet($definedMeaningId));

	return $record;
}

function getAlternativeDefinitionsRecordSet($definedMeaningId) {
	global
		$definitionIdAttribute, $alternativeDefinitionAttribute, $sourceAttribute;

	$recordSet = new ArrayRecordSet(new Structure($definitionIdAttribute, $alternativeDefinitionAttribute, $sourceAttribute), new Structure($definitionIdAttribute));

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT meaning_text_tcid, source_id FROM uw_alt_meaningtexts" .
								" WHERE meaning_mid=$definedMeaningId".
								" AND ". getLatestTransactionRestriction('uw_alt_meaningtexts'));

	while ($alternativeDefinition = $dbr->fetchObject($queryResult)) 
		$recordSet->addRecord(array($alternativeDefinition->meaning_text_tcid, getTranslatedTextRecordSet($alternativeDefinition->meaning_text_tcid), $alternativeDefinition->source_id));

	return $recordSet;
}

function getDefinedMeaningDefinitionRecordSet($definedMeaningId) {
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

function getSynonymAndTranslationRecordSet($definedMeaningId) {
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
	$queryResult = $dbr->query("SELECT uw_expression_ns.expression_id, spelling, language_id, endemic_meaning FROM uw_syntrans, uw_expression_ns WHERE uw_syntrans.defined_meaning_id=$definedMeaningId " .
								"AND uw_expression_ns.expression_id=uw_syntrans.expression_id AND ". getLatestTransactionRestriction('uw_syntrans'));

	while($synonymOrTranslation = $dbr->fetchObject($queryResult)) {
		$expressionRecord = new ArrayRecord($expressionStructure);
		$expressionRecord->setAttributeValuesByOrder(array($synonymOrTranslation->language_id, $synonymOrTranslation->spelling));

		$recordset->addRecord(array($synonymOrTranslation->expression_id, $expressionRecord, $synonymOrTranslation->endemic_meaning));
	}

	return $recordset;
}

function getDefinedMeaningRelationsRecordSet($definedMeaningId) {
	global
		$wgRequest;

	if ($wgRequest->getText('action') == 'history')
		return getDefinedMeaningRelationsHistoryRecordSet($definedMeaningId);
	else
		return getDefinedMeaningRelationsLatestRecordSet($definedMeaningId);
}

function getDefinedMeaningLabelRecord($definedMeaningId) {
	global
		$definedMeaningReferenceStructure, $definedMeaningIdAttribute, $definedMeaningLabelAttribute;
	
	$record = new ArrayRecord($definedMeaningReferenceStructure);
	$record->setAttributeValue($definedMeaningIdAttribute, $definedMeaningId);
	$record->setAttributeValue($definedMeaningLabelAttribute, definedMeaningExpression($definedMeaningId));
	
	return $record;
}

function getDefinedMeaningRelationsLatestRecordSet($definedMeaningId) {
	global
		$relationTypeAttribute, $otherDefinedMeaningAttribute;

	$structure = new Structure($relationTypeAttribute, $otherDefinedMeaningAttribute);
	$recordSet = new ArrayRecordSet($structure, $structure);

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT relationtype_mid, meaning2_mid FROM uw_meaning_relations " .
								"WHERE meaning1_mid=$definedMeaningId  " .
								" AND ". getLatestTransactionRestriction('uw_meaning_relations').
								"ORDER BY relationtype_mid");

	while($definedMeaningRelation = $dbr->fetchObject($queryResult)) 
//		$recordSet->addRecord(array($definedMeaningRelation->relationtype_mid, getDefinedMeaningLabelRecord($definedMeaningRelation->meaning2_mid)));
		$recordSet->addRecord(array($definedMeaningRelation->relationtype_mid, $definedMeaningRelation->meaning2_mid));

	return $recordSet;
}

function getDefinedMeaningRelationsHistoryRecordSet($definedMeaningId) {
	global
		$relationTypeAttribute, $otherDefinedMeaningAttribute, $recordLifeSpanAttribute;

	$structure = new Structure($relationTypeAttribute, $otherDefinedMeaningAttribute, $recordLifeSpanAttribute);
	$recordSet = new ArrayRecordSet($structure, $structure);

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT relationtype_mid, meaning2_mid, add_transaction_id, remove_transaction_id, NOT remove_transaction_id IS NULL AS is_live FROM uw_meaning_relations " .
								"WHERE meaning1_mid=$definedMeaningId ORDER BY is_live, relationtype_mid");

	while($definedMeaningRelation = $dbr->fetchObject($queryResult))
		$recordSet->addRecord(array($definedMeaningRelation->relationtype_mid, $definedMeaningRelation->meaning2_mid,
									getRecordLifeSpanTuple($definedMeaningRelation->add_transaction_id, $definedMeaningRelation->remove_transaction_id)));

	return $recordSet;
}

function getDefinedMeaningCollectionMembershipRecordSet($definedMeaningId) {
	global
		$wgRequest;

	if ($wgRequest->getText('action') == 'history')
		return getDefinedMeaningCollectionMembershipHistoryRecordSet($definedMeaningId);
	else
		return getDefinedMeaningCollectionMembershipLatestRecordSet($definedMeaningId);
}

function getDefinedMeaningCollectionMembershipLatestRecordSet($definedMeaningId) {
	global
		$collectionAttribute, $sourceIdentifierAttribute;

	$structure = new Structure($collectionAttribute, $sourceIdentifierAttribute);
	$recordSet = new ArrayRecordSet($structure, new Structure($collectionAttribute));

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT collection_id, internal_member_id FROM uw_collection_contents WHERE member_mid=$definedMeaningId " .
								"AND ". getLatestTransactionRestriction('uw_collection_contents'));

	while($collection = $dbr->fetchObject($queryResult))
		$recordSet->addRecord(array($collection->collection_id, $collection->internal_member_id));

	return $recordSet;
}

function getDefinedMeaningCollectionMembershipHistoryRecordSet($definedMeaningId) {
	global
		$collectionAttribute, $sourceIdentifierAttribute, $recordLifeSpanAttribute;

	$structure = new Structure($collectionAttribute, $sourceIdentifierAttribute, $recordLifeSpanAttribute);
	$recordSet = new ArrayRecordSet($structure, new Structure($collectionAttribute));

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT collection_id, internal_member_id, add_transaction_id, remove_transaction_id, NOT remove_transaction_id IS NULL as is_live " .
								"FROM uw_collection_contents WHERE member_mid=$definedMeaningId " .
								"ORDER BY is_live, remove_transaction_id DESC");

	while($collection = $dbr->fetchObject($queryResult))
		$recordSet->addRecord(array($collection->collection_id, $collection->internal_member_id,
									getRecordLifeSpanTuple($collection->add_transaction_id, $collection->remove_transaction_id)));

	return $recordSet;
}

function getDefinedMeaningTextAttributeValuesRecordSet($definedMeaningId) {
	global
		$textAttributeValuesStructure, $textAttributeAttribute, $textValueIdAttribute;

	$recordSet = new ArrayRecordSet($textAttributeValuesStructure, new Structure($textAttributeAttribute, $textValueIdAttribute));

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT attribute_mid, value_tcid FROM uw_dm_text_attribute_values" .
								" WHERE defined_meaning_id=$definedMeaningId" .
								" AND " . getLatestTransactionRestriction('uw_dm_text_attribute_values'));

	while ($attributeValue = $dbr->fetchObject($queryResult))
		$recordSet->addRecord(array($attributeValue->attribute_mid, $attributeValue->value_tcid, getTranslatedTextRecordSet($attributeValue->value_tcid)));

	return $recordSet;
}

function getDefinedMeaningClassMembershipRecordSet($definedMeaningId) {
	global
		$wgRequest;

	if ($wgRequest->getText('action') == 'history')
		return getDefinedMeaningClassMembershipHistoryRecordSet($definedMeaningId);
	else
		return getDefinedMeaningClassMembershipLatestRecordSet($definedMeaningId);
}

function getDefinedMeaningClassMembershipLatestRecordSet($definedMeaningId) {
	global
		$classAttribute;

	$structure = new Structure($classAttribute);
	$recordset = new ArrayRecordSet($structure, $structure);

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT class_mid FROM uw_class_membership" .
								" WHERE class_member_mid=$definedMeaningId " .
								" AND ". getLatestTransactionRestriction('uw_class_membership'));

	while($class = $dbr->fetchObject($queryResult))
		$recordset->addRecord(array($class->class_mid));

	return $recordset;
}

function getDefinedMeaningClassMembershipHistoryRecordSet($definedMeaningId) {
	global
		$classAttribute, $recordLifeSpanAttribute;

	$structure = new Structure($classAttribute, $recordLifeSpanAttribute);
	$recordSet = new ArrayRecordSet($structure, $structure);

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT class_mid, add_transaction_id, remove_transaction_id, NOT remove_transaction_id IS NULL AS is_live" .
								" FROM uw_class_membership" .
								" WHERE class_member_mid=$definedMeaningId " .
								" ORDER BY is_live ");

	while($class = $dbr->fetchObject($queryResult))
		$recordSet->addRecord(array($class->class_mid,
									getRecordLifeSpanTuple($class->add_transaction_id, $class->remove_transaction_id)));

	return $recordSet;
}

?>
