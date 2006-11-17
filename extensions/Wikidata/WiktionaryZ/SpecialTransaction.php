<?php

if (!defined('MEDIAWIKI')) die();

$wgExtensionFunctions[] = 'wfSpecialTransaction';

function wfSpecialTransaction() {
	class SpecialTransaction extends SpecialPage {
		function SpecialTransaction() {
			SpecialPage::SpecialPage('Transaction');
		}
		
		function execute($parameter) {
			global
				$wgOut, $wgRequest;
			
			require_once("WikiDataTables.php");
			require_once("WiktionaryZAttributes.php");
			require_once("WiktionaryZRecordSets.php");
			require_once("WiktionaryZEditors.php");
			require_once("Transaction.php");
			require_once("Editor.php");
			require_once("Controller.php");
			require_once("type.php");
			
			initializeAttributes();
			
			$fromTransactionId = $wgRequest->getInt('from-transaction');
			$transactionCount = $wgRequest->getInt('transaction-count');
			$userName = $wgRequest->getText('user-name');
			$showRollBackOptions = $wgRequest->getBool('show-roll-back-options');
			
			if ($fromTransactionId == 0)
				$fromTransactionId = getLatestTransactionId();
				
			if ($transactionCount == 0)
				$transactionCount = 10;
			else
				$transactionCount = min($transactionCount, 20);
			
			$wgOut->addHTML('<h1>Recent changes</h1>');
			$wgOut->addHTML(getFilterOptionsPanel($fromTransactionId, $transactionCount, $userName, $showRollBackOptions));

			if ($showRollBackOptions)
				$wgOut->addHTML('<form method="post" action="">');

			$recordSet = getTransactionRecordSet($fromTransactionId, $transactionCount, $userName);	
			
			if (isset($_POST['roll-back']))
				rollBackTransactions($recordSet);

			$wgOut->addHTML(getTransactionOverview($recordSet, $showRollBackOptions));
			
			if ($showRollBackOptions)
				$wgOut->addHTML(
					'<div class="option-panel">'.
						'<table cellpadding="0" cellspacing="0">' .
							'<tr>' .
								'<th>' . wfMsg('summary') . ': </th>' .
								'<td class="option-field">' . getTextBox("summary") .'</td>' .
							'</tr>' .
							'<tr><th/><td>'. getSubmitButton("roll-back", "Roll back") .'</td></tr>'.
						'</table>' .
					'</div>'.
					'</form>'
				);
			
			$wgOut->addHTML(DefaultEditor::getExpansionCss());
			$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");
		}
	}
	
	SpecialPage::addPage(new SpecialTransaction());
}

function getFilterOptionsPanel($fromTransactionId, $transactionCount, $userName, $showRollBackOptions) {
	$countOptions = array();
	
	for ($i = 1; $i <= 20; $i++)
		$countOptions[$i] = $i;
	
	return getOptionPanel(
		array(
			"From transaction" => 
				getSuggest(
					'from-transaction', 
					'transaction', 
					$fromTransactionId, 
					getTransactionLabel($fromTransactionId), 
					array(0, 2, 3)
				),
			"Count" => 
				getSelect('transaction-count',
					$countOptions,
					$transactionCount 
				),
			"User name" => getTextBox('user-name', $userName),
			"Show roll back controls" => getCheckBox('show-roll-back-options', $showRollBackOptions)
		),
		'',
		array("show" => "Show")
	); 
}

function initializeAttributes() {
	global
		$operationAttribute, $isLatestAttribute, $rollbackAttribute, $definedMeaningIdAttribute, $definedMeaningReferenceAttribute, 
		$languageAttribute, $textAttribute, $definedMeaningReferenceStructure;

	$operationAttribute = new Attribute('operation', 'Operation', 'text');
	$isLatestAttribute = new Attribute('is-latest', 'Is latest', 'boolean');
	$rollbackAttribute = new Attribute('roll-back', 'Roll back', 'boolean');

	global
		$updatedDefinitionStructure, $updatedDefinitionAttribute;
	
	$updatedDefinitionStructure = new Structure(
		$definedMeaningIdAttribute, 
		$definedMeaningReferenceAttribute, 
		$languageAttribute, 
		$textAttribute,
		$operationAttribute
	);		
	
	$updatedDefinitionAttribute = new Attribute('updated-definition', 'Definition', new RecordSetType($updatedDefinitionStructure));

	global
		$expressionAttribute, $identicalMeaningAttribute, $updatedSyntransesAttribute;

	$updatedSyntransesStructure = new Structure(
		$definedMeaningIdAttribute, 
		$definedMeaningReferenceAttribute, 
		$expressionAttribute, 
		$identicalMeaningAttribute,
		$operationAttribute
	); 
	
	$updatedSyntransesAttribute = new Attribute('updated-syntranses', 'Synonyms and translations', new RecordSetType($updatedSyntransesStructure));
	
	global
		$relationIdAttribute, $firstMeaningAttribute, $secondMeaningAttribute, $relationTypeAttribute, 
		$updatedRelationsStructure, $updatedRelationsAttribute;
	
	$firstMeaningAttribute = new Attribute('first-meaning', "First defined meaning", new RecordType($definedMeaningReferenceStructure));
	$secondMeaningAttribute = new Attribute('second-meaning', "Second defined meaning", new RecordType($definedMeaningReferenceStructure));

	$updatedRelationsStructure = new Structure(
		$rollbackAttribute,
		$relationIdAttribute,
		$firstMeaningAttribute, 
		$relationTypeAttribute, 
		$secondMeaningAttribute,
		$operationAttribute,
		$isLatestAttribute
	);
	
	$updatedRelationsAttribute = new Attribute('updated-relations', 'Relations', new RecordSetType($updatedRelationsStructure));
	
	global
		$classMembershipIdAttribute, $classAttribute, $classMemberAttribute,
		$updatedClassMembershipStructure, $updatedClassMembershipAttribute;
		
	$classMemberAttribute = new Attribute('class-member', 'Class member', new RecordType($definedMeaningReferenceStructure));
	
	$updatedClassMembershipStructure = new Structure(
		$classMembershipIdAttribute,
		$classAttribute,
		$classMemberAttribute,
		$operationAttribute
	);
	
	$updatedClassMembershipAttribute = new Attribute('updated-class-membership', 'Class membership', new RecordSetType($updatedClassMembershipStructure));
	
	global
		$collectionIdAttribute, $collectionMeaningAttribute, $collectionMemberAttribute, $sourceIdentifierAttribute,
		$updatedCollectionMembershipStructure, $updatedCollectionMembershipAttribute, $collectionMemberIdAttribute;
		
	$collectionMemberAttribute = new Attribute('collection-member', 'Collection member', new RecordType($definedMeaningReferenceStructure));
	$collectionMemberAttribute = new Attribute('collection-member-id', 'Collection member identifier', 'defined-meaning-id');
	
	$updatedCollectionMembershipStructure = new Structure(
		$collectionIdAttribute,
		$collectionMeaningAttribute,
		$collectionMemberIdAttribute,
		$collectionMemberAttribute,
		$sourceIdentifierAttribute,
		$operationAttribute
	);
	
	$updatedCollectionMembershipAttribute = new Attribute('updated-collection-membership', 'Collection membership', new RecordSetType($updatedCollectionMembershipStructure));
	
	global
		$updatesInTransactionAttribute;

	$updatesInTransactionStructure = new Structure(
		$updatedDefinitionAttribute,
		$updatedSyntransesAttribute,
		$updatedRelationsAttribute,
		$updatedClassMembershipAttribute
	);
	
	$updatesInTransactionAttribute = new Attribute('updates-in-transaction', 'Updates in transaction', new RecordType($updatesInTransactionStructure));
}

function getTransactionRecordSet($fromTransactionId, $transactionCount, $userName) {
	global
		$transactionAttribute, $transactionIdAttribute, $transactionsTable, $updatesInTransactionAttribute;
		
	$queryTransactionInformation = new QueryLatestTransactionInformation();

	$restrictions = array("transaction_id <= $fromTransactionId");
	
	if ($userName != "")
		$restrictions[] = "EXISTS (SELECT user_name FROM user WHERE user.user_id=transactions.user_id AND user.user_name='" . $userName . "')"; 

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$transactionIdAttribute,
		array(
			'transaction_id' => $transactionIdAttribute
		),
		$transactionsTable,
		$restrictions,
		array('transaction_id DESC'),
		$transactionCount
	);
	
	$recordSet->getStructure()->attributes[] = $transactionIdAttribute;
	expandTransactionIDsInRecordSet($recordSet, $transactionIdAttribute, $transactionAttribute);
	
	$recordSet->getStructure()->attributes[] = $updatesInTransactionAttribute;
	expandUpdatesInTransactionInRecordSet($recordSet);

	return $recordSet;	
}

function getTransactionOverview($recordSet, $showRollBackOptions) {
	global
		$transactionAttribute, $userAttribute,  $timestampAttribute, $summaryAttribute, 
		$updatesInTransactionAttribute, $updatedDefinitionAttribute, $updatedSyntransesAttribute, 
		$updatedRelationsAttribute, $updatedClassMembershipAttribute, $updatedCollectionMembershipAttribute;

	$captionEditor = new RecordSpanEditor($transactionAttribute, ': ', ', ', false);
	$captionEditor->addEditor(new TimestampEditor($timestampAttribute, new SimplePermissionController(false), false));
	$captionEditor->addEditor(new UserEditor($userAttribute, new SimplePermissionController(false), false));
	$captionEditor->addEditor(new TextEditor($summaryAttribute, new SimplePermissionController(false), false));
	
	$valueEditor = new RecordUnorderedListEditor($updatesInTransactionAttribute, 5);
	$valueEditor->addEditor(getUpdatedDefinedMeaningDefinitionEditor($updatedDefinitionAttribute));
	$valueEditor->addEditor(getUpdatedSyntransesEditor($updatedSyntransesAttribute));
	$valueEditor->addEditor(getUpdatedRelationsEditor($updatedRelationsAttribute, $showRollBackOptions));
	$valueEditor->addEditor(getUpdatedClassMembershipEditor($updatedClassMembershipAttribute));
	$valueEditor->addEditor(getUpdatedCollectionMembershipEditor($updatedCollectionMembershipAttribute));
	
	$editor = new RecordSetListEditor(null, new SimplePermissionController(false), false, false, false, null, 4, false);
	$editor->setCaptionEditor($captionEditor);
	$editor->setValueEditor($valueEditor);
	
	return $editor->view(new IdStack("transaction"), $recordSet);
}

function expandUpdatesInTransactionInRecordSet($recordSet) {
	global
		$transactionIdAttribute, $updatesInTransactionAttribute;
	
	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);
		$record->setAttributeValue(
			$updatesInTransactionAttribute, 
			getUpdatesInTransactionRecord($record->getAttributeValue($transactionIdAttribute))
		);
	}
}

function getUpdatesInTransactionRecord($transactionId) {
	global	
		$updatesInTransactionAttribute, $updatedDefinitionAttribute, $updatedSyntransesAttribute, 
		$updatedRelationsAttribute, $updatedClassMembershipAttribute, $updatedCollectionMembershipAttribute;
		
	$record = new ArrayRecord($updatesInTransactionAttribute->type->getStructure());
	$record->setAttributeValue($updatedDefinitionAttribute, getUpdatedDefinedMeaningDefinitionRecordSet($transactionId));
	$record->setAttributeValue($updatedSyntransesAttribute, getUpdatedSyntransesRecordSet($transactionId));
	$record->setAttributeValue($updatedRelationsAttribute, getUpdatedRelationsRecordSet($transactionId));
	$record->setAttributeValue($updatedClassMembershipAttribute, getUpdatedClassMembershipRecordSet($transactionId));
	$record->setAttributeValue($updatedCollectionMembershipAttribute, getUpdatedCollectionMembershipRecordSet($transactionId));
	
	return $record;
}

function getUpdatedDefinedMeaningDefinitionRecordSet($transactionId) {
	global
		$languageAttribute, $textAttribute, $definedMeaningIdAttribute, 
		$definedMeaningReferenceAttribute, $updatedDefinitionStructure, $operationAttribute;
		
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT defined_meaning_id, language_id, old_text, " . getOperationSelectColumn('translated_content', $transactionId) . 
		" FROM uw_defined_meaning, translated_content, text " .
		" WHERE uw_defined_meaning.meaning_text_tcid=translated_content.translated_content_id ".
		" AND translated_content.text_id=text.old_id " .
		" AND " . getInTransactionRestriction('translated_content', $transactionId) .
		" AND " . getAtTransactionRestriction('uw_defined_meaning', $transactionId)
	);
		
	$recordSet = new ArrayRecordSet($updatedDefinitionStructure, new Structure($definedMeaningIdAttribute));
	
	while ($definition = $dbr->fetchObject($queryResult)) {
		$record = new ArrayRecord($updatedDefinitionStructure);
		$record->setAttributeValue($definedMeaningIdAttribute, $definition->defined_meaning_id);
		$record->setAttributeValue($definedMeaningReferenceAttribute, getDefinedMeaningReferenceRecord($definition->defined_meaning_id));
		$record->setAttributeValue($languageAttribute, $definition->language_id);
		$record->setAttributeValue($textAttribute, $definition->old_text);
		$record->setAttributeValue($operationAttribute, $definition->operation);
		
		$recordSet->add($record);	
	}
	
	return $recordSet;
}

function getUpdatedSyntransesRecordSet($transactionId) {
	global
		$updatedSyntransesStructure, $definedMeaningIdAttribute, $definedMeaningReferenceAttribute, 
		$operationAttribute, $expressionAttribute, $expressionStructure, $languageAttribute, $spellingAttribute,
		$identicalMeaningAttribute;
	
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT defined_meaning_id, language_id, spelling, identical_meaning, " . getOperationSelectColumn('uw_syntrans', $transactionId) . 
		" FROM uw_syntrans, uw_expression_ns " .
		" WHERE uw_syntrans.expression_id=uw_expression_ns.expression_id " .
		" AND " . getInTransactionRestriction('uw_syntrans', $transactionId) .
		" AND " . getAtTransactionRestriction('uw_expression_ns', $transactionId)
	);
		
	$recordSet = new ArrayRecordSet($updatedSyntransesStructure, new Structure($definedMeaningIdAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) {
		$expressionRecord = new ArrayRecord($expressionStructure);
		$expressionRecord->setAttributeValue($languageAttribute, $row->language_id);
		$expressionRecord->setAttributeValue($spellingAttribute, $row->spelling);

		$record = new ArrayRecord($updatedSyntransesStructure);
		$record->setAttributeValue($definedMeaningIdAttribute, $row->defined_meaning_id);
		$record->setAttributeValue($definedMeaningReferenceAttribute, getDefinedMeaningReferenceRecord($row->defined_meaning_id));
		$record->setAttributeValue($expressionAttribute, $expressionRecord);
		$record->setAttributeValue($identicalMeaningAttribute, $row->identical_meaning);
		$record->setAttributeValue($operationAttribute, $row->operation);
		
		$recordSet->add($record);	
	}
	
	return $recordSet;
}

function getUpdatedRelationsRecordSet($transactionId) {
	global
		$updatedRelationsStructure, $relationIdAttribute, $firstMeaningAttribute, $secondMeaningAttribute, 
		$relationTypeAttribute, $operationAttribute, $isLatestAttribute, $rollbackAttribute;

	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT relation_id, meaning1_mid, meaning2_mid, relationtype_mid, " . getOperationSelectColumn('uw_meaning_relations', $transactionId) . 
		", (add_transaction_id=$transactionId AND remove_transaction_id IS NULL) OR (remove_transaction_id=$transactionId AND NOT EXISTS(" .
			"SELECT latest_relations.relation_id " .
			" FROM uw_meaning_relations AS latest_relations" .
			" WHERE relation_id=latest_relations.relation_id" .
			" AND (latest_relations.add_transaction_id > $transactionId) " .
		")) AS is_latest " .  
		" FROM uw_meaning_relations " .
		" WHERE " . getInTransactionRestriction('uw_meaning_relations', $transactionId)
	);
		
	$recordSet = new ArrayRecordSet($updatedRelationsStructure, new Structure($relationIdAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) {
		$record = new ArrayRecord($updatedRelationsStructure);
		$record->setAttributeValue($relationIdAttribute, $row->relation_id);
		$record->setAttributeValue($firstMeaningAttribute, getDefinedMeaningReferenceRecord($row->meaning1_mid));
		$record->setAttributeValue($secondMeaningAttribute, getDefinedMeaningReferenceRecord($row->meaning2_mid));
		$record->setAttributeValue($relationTypeAttribute, getDefinedMeaningReferenceRecord($row->relationtype_mid));
		$record->setAttributeValue($operationAttribute, $row->operation);
		$record->setAttributeValue($isLatestAttribute, $row->is_latest);
		$record->setAttributeValue($rollbackAttribute, $row->is_latest);
		
		$recordSet->add($record);	
	}
	
	return $recordSet;
}

function getUpdatedClassMembershipRecordSet($transactionId) {
	global
		$updatedClassMembershipStructure, $classMembershipIdAttribute, $classAttribute, $classMemberAttribute, 
		$operationAttribute;
	
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT class_membership_id, class_mid, class_member_mid, " . getOperationSelectColumn('uw_class_membership', $transactionId) . 
		" FROM uw_class_membership " .
		" WHERE " . getInTransactionRestriction('uw_class_membership', $transactionId)
	);
		
	$recordSet = new ArrayRecordSet($updatedClassMembershipStructure, new Structure($classMembershipIdAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) {
		$record = new ArrayRecord($updatedClassMembershipStructure);
		$record->setAttributeValue($classMembershipIdAttribute, $row->class_membership_id);
		$record->setAttributeValue($classAttribute, getDefinedMeaningReferenceRecord($row->class_mid));
		$record->setAttributeValue($classMemberAttribute, getDefinedMeaningReferenceRecord($row->class_member_mid));
		$record->setAttributeValue($operationAttribute, $row->operation);
		
		$recordSet->add($record);	
	}
	
	return $recordSet;
}

function getUpdatedCollectionMembershipRecordSet($transactionId) {
	global
		$updatedCollectionMembershipStructure, $collectionIdAttribute, $collectionMeaningAttribute, 
		$collectionMemberAttribute, $sourceIdentifierAttribute, $operationAttribute, $collectionMemberIdAttribute;
	
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT uw_collection_contents.collection_id, collection_mid, member_mid, internal_member_id, " . getOperationSelectColumn('uw_collection_contents', $transactionId) . 
		" FROM uw_collection_contents, uw_collection_ns " .
		" WHERE uw_collection_contents.collection_id=uw_collection_ns.collection_id " .
		" AND " . getInTransactionRestriction('uw_collection_contents', $transactionId) .
		" AND " . getAtTransactionRestriction('uw_collection_ns', $transactionId)
	);
		
	$recordSet = new ArrayRecordSet($updatedCollectionMembershipStructure, new Structure($collectionIdAttribute, $collectionMemberIdAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) {
		$record = new ArrayRecord($updatedCollectionMembershipStructure);
		$record->setAttributeValue($collectionIdAttribute, $row->collection_id);
		$record->setAttributeValue($collectionMeaningAttribute, getDefinedMeaningReferenceRecord($row->collection_mid));
		$record->setAttributeValue($collectionMemberIdAttribute, $row->member_mid);
		$record->setAttributeValue($collectionMemberAttribute, getDefinedMeaningReferenceRecord($row->member_mid));
		$record->setAttributeValue($sourceIdentifierAttribute, $row->internal_member_id);
		$record->setAttributeValue($operationAttribute, $row->operation);
		
		$recordSet->add($record);	
	}
	
	return $recordSet;
}

function getUpdatedDefinedMeaningDefinitionEditor($attribute) {
	global
		$definedMeaningReferenceAttribute, $languageAttribute, $textAttribute, $operationAttribute;
	
	$editor = createTableViewer($attribute);
	$editor->addEditor(createDefinedMeaningReferenceViewer($definedMeaningReferenceAttribute));
	$editor->addEditor(createLanguageViewer($languageAttribute));
	$editor->addEditor(createLongTextViewer($textAttribute));
	$editor->addEditor(createShortTextViewer($operationAttribute));
	
	return $editor;
}

function getUpdatedSyntransesEditor($attribute) {
	global
		$definedMeaningReferenceAttribute, $expressionAttribute, $identicalMeaningAttribute, $operationAttribute;
		
	$editor = createTableViewer($attribute);
	$editor->addEditor(createDefinedMeaningReferenceViewer($definedMeaningReferenceAttribute));
	$editor->addEditor(getExpressionTableCellEditor($expressionAttribute));
	$editor->addEditor(new BooleanEditor($identicalMeaningAttribute, new SimplePermissionController(false), false, false));
	$editor->addEditor(createShortTextViewer($operationAttribute));
	
	return $editor;
}

function getUpdatedRelationsEditor($attribute, $showRollBackOptions) {
	global
		$firstMeaningAttribute, $relationTypeAttribute, $secondMeaningAttribute, $operationAttribute, 
		$isLatestAttribute, $rollbackAttribute;
		
	$editor = createTableViewer($attribute);
	
	if ($showRollBackOptions)
		$editor->addEditor(new RollbackEditor($rollbackAttribute));
		
	$editor->addEditor(createDefinedMeaningReferenceViewer($firstMeaningAttribute));
	$editor->addEditor(createDefinedMeaningReferenceViewer($relationTypeAttribute));
	$editor->addEditor(createDefinedMeaningReferenceViewer($secondMeaningAttribute));
	$editor->addEditor(createShortTextViewer($operationAttribute));
	$editor->addEditor(createBooleanViewer($isLatestAttribute));
	
	return $editor;
}

function getUpdatedClassMembershipEditor($attribute) {
	global
		$classAttribute, $classMemberAttribute, $operationAttribute;
		
	$editor = createTableViewer($attribute);
	$editor->addEditor(createDefinedMeaningReferenceViewer($classAttribute));
	$editor->addEditor(createDefinedMeaningReferenceViewer($classMemberAttribute));
	$editor->addEditor(createShortTextViewer($operationAttribute));
	
	return $editor;
}

function getUpdatedCollectionMembershipEditor($attribute) {
	global
		$collectionMeaningAttribute, $collectionMemberAttribute, $sourceIdentifierAttribute, $operationAttribute;
		
	$editor = createTableViewer($attribute);
	$editor->addEditor(createDefinedMeaningReferenceViewer($collectionMeaningAttribute));
	$editor->addEditor(createDefinedMeaningReferenceViewer($collectionMemberAttribute));
	$editor->addEditor(createShortTextViewer($sourceIdentifierAttribute));
	$editor->addEditor(createShortTextViewer($operationAttribute));
	
	return $editor;
}

function simpleRecord($structure, $attribute, $value) {
	$result = new ArrayRecord($structure);
	$result->setAttributeValue($attribute, $value);
	
	return $result;
}

function rollBackTransactions($recordSet) {
	global
		$wgRequest, $wgUser,
		$transactionIdAttribute, $updatesInTransactionAttribute, $updatedRelationsAttribute;
		
	$summary = $wgRequest->getText('summary');
	startNewTransaction($wgUser->getID(), wfGetIP(), $summary);
		
	$idStack = new IdStack('update-transaction');
	$transactionKeyStructure = $recordSet->getKey();
	
	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$transactionRecord = $recordSet->getRecord($i);

		$transactionId = $transactionRecord->getAttributeValue($transactionIdAttribute);
		$idStack->pushKey(simpleRecord($transactionKeyStructure, $transactionIdAttribute, $transactionId));

		$updatesInTransaction = $transactionRecord->getAttributeValue($updatesInTransactionAttribute);
		$idStack->pushAttribute($updatesInTransactionAttribute);

		$updatedRelations = $updatesInTransaction->getAttributeValue($updatedRelationsAttribute);
		$idStack->pushAttribute($updatedRelationsAttribute);
		rollBackRelations($idStack, $updatedRelations);
		$idStack->popAttribute();
		
		$idStack->popAttribute();
		$idStack->popKey();
	}
}

function shouldRollBack($idStack) {
	global
		$rollbackAttribute;
	
	$idStack->pushAttribute($rollbackAttribute);				
	$result = isset($_POST[$idStack->getId()]);
	$idStack->popAttribute();		
	
	return $result;
}

function getMeaningId($record, $referenceAttribute) {
	global
		$definedMeaningIdAttribute;
	
	return $record->getAttributeValue($referenceAttribute)->getAttributeValue($definedMeaningIdAttribute);
}

function rollBackRelations($idStack, $relations) {
	global
		$relationIdAttribute, $isLatestAttribute, $firstMeaningAttribute, $secondMeaningAttribute, $relationTypeAttribute,
		$operationAttribute;
	
	$relationsKeyStructure = $relations->getKey();
	
	for ($i = 0; $i < $relations->getRecordCount(); $i++) {
		$relationRecord = $relations->getRecord($i);

		$relationId = $relationRecord->getAttributeValue($relationIdAttribute);
		$isLatest = $relationRecord->getAttributeValue($isLatestAttribute);

		if ($isLatest) {
			$idStack->pushKey(simpleRecord($relationsKeyStructure, $relationIdAttribute, $relationId));
			
			if (shouldRollBack($idStack)) 
				rollBackRelation(
					$relationId,
					getMeaningId($relationRecord, $firstMeaningAttribute),
					getMeaningId($relationRecord, $relationTypeAttribute),
					getMeaningId($relationRecord, $secondMeaningAttribute),
					$relationRecord->getAttributeValue($operationAttribute)
				);
				
			$idStack->popKey();
		}
	}	
}

function rollBackRelation($relationId, $firstMeaningId, $relationTypeId, $secondMeaningId, $operation) {
	$dbr = &wfGetDB(DB_MASTER);
	
	if ($operation == 'Added')
		removeRelationWithId($relationId);
	else	
		addRelation($firstMeaningId, $relationTypeId, $secondMeaningId, $operation);	
	
//	echo(
//		"Relation ID: " . $relationId . "\n" .
//		"First meaning ID: ". $firstMeaningId . "\n" .
//		"Relation type ID: ". $relationTypeId . "\n" .
//		"Second meaning ID: ". $secondMeaningId . "\n" . 
//		"Operation: " . $operation . "\n" 
//	);
}

?>
