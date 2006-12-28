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
				$wgOut;
			
			require_once("WikiDataTables.php");
			require_once("WiktionaryZAttributes.php");
			require_once("WiktionaryZRecordSets.php");
			require_once("WiktionaryZEditors.php");
			require_once("Transaction.php");
			require_once("Editor.php");
			require_once("Controller.php");
			require_once("type.php");
			
			initializeAttributes();
			
			$fromTransactionId = (int) $_GET['from-transaction'];
			$transactionCount = (int) $_GET['transaction-count'];
			$userName = "" . $_GET['user-name'];
			$showRollBackOptions = isset($_GET['show-roll-back-options']);
			
			if (isset($_POST['roll-back'])) {
				$fromTransactionId = (int) $_POST['from-transaction'];
				$transactionCount = (int) $_POST['transaction-count'];
				$userName = "" . $_POST['user-name'];
				
				if ($fromTransactionId != 0) {
					$recordSet = getTransactionRecordSet($fromTransactionId, $transactionCount, $userName);	
					rollBackTransactions($recordSet);
					$fromTransactionId = 0;
					$userName = "";
				}
			}

			if ($fromTransactionId == 0)
				$fromTransactionId = getLatestTransactionId();
				
			if ($transactionCount == 0)
				$transactionCount = 10;
			else
				$transactionCount = min($transactionCount, 20);
			
			$wgOut->addHTML('<h1>Recent changes</h1>');
			$wgOut->addHTML(getFilterOptionsPanel($fromTransactionId, $transactionCount, $userName, $showRollBackOptions));

			if ($showRollBackOptions) 
				$wgOut->addHTML(
					'<form method="post" action="">' .
					'<input type="hidden" name="from-transaction" value="'. $fromTransactionId .'"/>'. 
					'<input type="hidden" name="transaction-count" value="'. $transactionCount .'"/>'. 
					'<input type="hidden" name="user-name" value="'. $userName .'"/>' 
				);

			$recordSet = getTransactionRecordSet($fromTransactionId, $transactionCount, $userName);	
			
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
					array(), 
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
		$operationAttribute, $isLatestAttribute, $definedMeaningIdAttribute, $definedMeaningReferenceAttribute, 
		$languageAttribute, $textAttribute, $definedMeaningReferenceStructure, $rollBackStructure, $rollBackAttribute;

	$operationAttribute = new Attribute('operation', 'Operation', 'text');
	$isLatestAttribute = new Attribute('is-latest', 'Is latest', 'boolean');

	$rollBackStructure = new Structure($isLatestAttribute, $operationAttribute);
	$rollBackAttribute = new Attribute('roll-back', 'Roll back', new RecordType($rollBackStructure));
	
	global
		$translatedContentHistoryStructure, $translatedContentHistoryKeyStructure, $translatedContentHistoryAttribute, 
		$recordLifeSpanAttribute, $addTransactionIdAttribute, $translatedContentIdAttribute;
	
	$addTransactionIdAttribute = new Attribute('add-transaction-id', 'Add transaction ID', 'identifier');
		
	$translatedContentHistoryStructure = new Structure($addTransactionIdAttribute, $textAttribute, $recordLifeSpanAttribute);
	$translatedContentHistoryKeyStructure = new Structure($addTransactionIdAttribute);
	$translatedContentHistoryAttribute = new Attribute('translated-content-history', 'History', new RecordSetType($translatedContentHistoryStructure));
	$translatedContentIdAttribute = new Attribute('translated-content-id', 'Translated content ID', 'object-id');

	global
		$rollBackTranslatedContentStructure, $rollBackTranslatedContentAttribute;

	$rollBackTranslatedContentStructure = new Structure($isLatestAttribute, $operationAttribute, $translatedContentHistoryAttribute);
	$rollBackTranslatedContentAttribute = new Attribute('roll-back', 'Roll back', new RecordType($rollBackTranslatedContentStructure));

	global
		$updatedDefinitionStructure, $updatedDefinitionAttribute;
	
	$updatedDefinitionStructure = new Structure(
		$rollBackTranslatedContentAttribute,
		$definedMeaningIdAttribute, 
		$definedMeaningReferenceAttribute, 
		$translatedContentIdAttribute,
		$languageAttribute, 
		$textAttribute,
		$operationAttribute,
		$isLatestAttribute
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
		$rollBackAttribute,
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
		$rollBackAttribute,
		$classMembershipIdAttribute,
		$classAttribute,
		$classMemberAttribute,
		$operationAttribute,
		$isLatestAttribute
	);
	
	$updatedClassMembershipAttribute = new Attribute('updated-class-membership', 'Class membership', new RecordSetType($updatedClassMembershipStructure));
	
	global
		$collectionIdAttribute, $collectionMeaningAttribute, $collectionMemberAttribute, $sourceIdentifierAttribute,
		$updatedCollectionMembershipStructure, $updatedCollectionMembershipAttribute, $collectionMemberIdAttribute;
		
	$collectionMemberAttribute = new Attribute('collection-member', 'Collection member', new RecordType($definedMeaningReferenceStructure));
	$collectionMemberIdAttribute = new Attribute('collection-member-id', 'Collection member identifier', 'defined-meaning-id');
	
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
	$valueEditor->addEditor(getUpdatedDefinedMeaningDefinitionEditor($updatedDefinitionAttribute, $showRollBackOptions));
	$valueEditor->addEditor(getUpdatedSyntransesEditor($updatedSyntransesAttribute));
	$valueEditor->addEditor(getUpdatedRelationsEditor($updatedRelationsAttribute, $showRollBackOptions));
	$valueEditor->addEditor(getUpdatedClassMembershipEditor($updatedClassMembershipAttribute, $showRollBackOptions));
	$valueEditor->addEditor(getUpdatedCollectionMembershipEditor($updatedCollectionMembershipAttribute));
	
	$editor = new RecordSetListEditor(null, new SimplePermissionController(false), new ShowEditFieldChecker(true), new AllowAddController(false), false, false, null, 4, false);
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

function getTranslatedContentHistory($translatedContentId, $languageId, $isLatest) {
	global
		$translatedContentHistoryStructure, $translatedContentHistoryKeyStructure,
		$textAttribute, $addTransactionIdAttribute, $recordLifeSpanAttribute;
		
	$recordSet = new ArrayRecordSet($translatedContentHistoryStructure, $translatedContentHistoryKeyStructure);
	
	if ($isLatest) {
		$dbr = &wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query(
			"SELECT old_text, add_transaction_id, remove_transaction_id " .
			" FROM translated_content, text" .
			" WHERE translated_content.translated_content_id=$translatedContentId" .
			" AND translated_content.language_id=$languageId " .
			" AND translated_content.text_id=text.old_id " .
			" ORDER BY add_transaction_id DESC"
		);
		
		while ($row = $dbr->fetchObject($queryResult)) {
			$record = new ArrayRecord($translatedContentHistoryStructure);
			$record->setAttributeValue($textAttribute, $row->old_text);
			$record->setAttributeValue($addTransactionIdAttribute, (int) $row->add_transaction_id);
			$record->setAttributeValue($recordLifeSpanAttribute, getRecordLifeSpanTuple((int) $row->add_transaction_id, (int) $row->remove_transaction_id));
			
			$recordSet->add($record);	
		}
	}
	
	return $recordSet;
}

function getUpdatedTextRecord($text, $history) {
	global
		$updatedTextStructure, $textAttribute, $translatedContentHistoryAttribute;
		
	$result = new ArrayRecord($updatedTextStructure);
	$result->setAttributeValue($textAttribute, $text);	
	$result->setAttributeValue($translatedContentHistoryAttribute, $history);
	
	return $result;
}

function getUpdatedDefinedMeaningDefinitionRecordSet($transactionId) {
	global
		$languageAttribute, $textAttribute, $definedMeaningIdAttribute, 
		$definedMeaningReferenceAttribute, $updatedDefinitionStructure, $translatedContentIdAttribute,
		$operationAttribute, $isLatestAttribute, $rollBackTranslatedContentAttribute, $rollBackTranslatedContentStructure;
		
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT defined_meaning_id, translated_content_id, language_id, old_text, " . 
			getOperationSelectColumn('translated_content', $transactionId) . ', ' .
			getIsLatestSelectColumn('translated_content', array('translated_content_id', 'language_id'), $transactionId) . 
		" FROM uw_defined_meaning, translated_content, text " .
		" WHERE uw_defined_meaning.meaning_text_tcid=translated_content.translated_content_id ".
		" AND translated_content.text_id=text.old_id " .
		" AND " . getInTransactionRestriction('translated_content', $transactionId) .
		" AND " . getAtTransactionRestriction('uw_defined_meaning', $transactionId)
	);
		
	$recordSet = new ArrayRecordSet($updatedDefinitionStructure, new Structure($definedMeaningIdAttribute, $languageAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) {
		$record = new ArrayRecord($updatedDefinitionStructure);
		$record->setAttributeValue($definedMeaningIdAttribute, $row->defined_meaning_id);
		$record->setAttributeValue($definedMeaningReferenceAttribute, getDefinedMeaningReferenceRecord($row->defined_meaning_id));
		$record->setAttributeValue($translatedContentIdAttribute, $row->translated_content_id);
		$record->setAttributeValue($languageAttribute, $row->language_id);
		$record->setAttributeValue($textAttribute, $row->old_text);
		$record->setAttributeValue($operationAttribute, $row->operation);
		$record->setAttributeValue($isLatestAttribute, $row->is_latest);
		$record->setAttributeValue($rollBackTranslatedContentAttribute, simpleRecord($rollBackTranslatedContentStructure, array($row->is_latest, $row->operation, getTranslatedContentHistory($row->translated_content_id, $row->language_id, $row->is_latest))));
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

function getIsLatestSelectColumn($table, $idFields, $transactionId) {
	$idSelectColumns = array();
	$idRestrictions = array();
	
	foreach ($idFields as $idField) {
		$idSelectColumns[] = "latest_$table.$idField";
		$idRestrictions[] = "$table.$idField=latest_$table.$idField";
	}
	
	return 
		"($table.add_transaction_id=$transactionId AND $table.remove_transaction_id IS NULL) OR ($table.remove_transaction_id=$transactionId AND NOT EXISTS(" .
			"SELECT " . implode(', ', $idSelectColumns) .
			" FROM $table AS latest_$table" .
			" WHERE " . implode(' AND ', $idRestrictions) .
			" AND (latest_$table.add_transaction_id >= $transactionId) " .
		")) AS is_latest ";
}

function getUpdatedRelationsRecordSet($transactionId) {
	global
		$updatedRelationsStructure, $relationIdAttribute, $firstMeaningAttribute, $secondMeaningAttribute, 
		$relationTypeAttribute, $operationAttribute, $isLatestAttribute, $rollBackAttribute, $rollBackStructure;

	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT relation_id, meaning1_mid, meaning2_mid, relationtype_mid, " . 
			getOperationSelectColumn('uw_meaning_relations', $transactionId) . ', ' .
			getIsLatestSelectColumn('uw_meaning_relations', array('relation_id'), $transactionId) . 
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
		$record->setAttributeValue($rollBackAttribute, simpleRecord($rollBackStructure, array($row->is_latest, $row->operation)));
		
		$recordSet->add($record);	
	}
	
	return $recordSet;
}

function getUpdatedClassMembershipRecordSet($transactionId) {
	global
		$updatedClassMembershipStructure, $classMembershipIdAttribute, $classAttribute, $classMemberAttribute, 
		$operationAttribute, $isLatestAttribute, $rollBackAttribute, $rollBackStructure;
	
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT class_membership_id, class_mid, class_member_mid, " . 
		getOperationSelectColumn('uw_class_membership', $transactionId) . ', ' .
		getIsLatestSelectColumn('uw_class_membership', array('class_membership_id'), $transactionId) . 
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
		$record->setAttributeValue($isLatestAttribute, $row->is_latest);
		$record->setAttributeValue($rollBackAttribute, simpleRecord($rollBackStructure, array($row->is_latest, $row->operation)));
		
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

function getTranslatedContentHistorySelector($attribute) {
	global
		$textAttribute, $recordLifeSpanAttribute;
	
	$result = createSuggestionsTableViewer($attribute);
	$result->addEditor(createLongTextViewer($textAttribute));
	$result->addEditor(createTableLifeSpanEditor($recordLifeSpanAttribute));

	$result = new RecordSetRecordSelector($result);
	
	return $result;
}

function getUpdatedDefinedMeaningDefinitionEditor($attribute, $showRollBackOptions) {
	global
		$definedMeaningReferenceAttribute, $languageAttribute, $textAttribute, 
		$operationAttribute, $isLatestAttribute, $rollBackTranslatedContentAttribute, $translatedContentHistoryAttribute;
	
	$editor = createTableViewer($attribute);
	
	if ($showRollBackOptions) {
		$rollBackEditor = new RollbackEditor($rollBackTranslatedContentAttribute, true);
		$rollBackEditor->setSuggestionsEditor(getTranslatedContentHistorySelector($translatedContentHistoryAttribute));
		
		$editor->addEditor($rollBackEditor);
	}
		
	$editor->addEditor(createDefinedMeaningReferenceViewer($definedMeaningReferenceAttribute));
	$editor->addEditor(createLanguageViewer($languageAttribute));
	$editor->addEditor(createLongTextViewer($textAttribute));
	$editor->addEditor(createShortTextViewer($operationAttribute));
	$editor->addEditor(createBooleanViewer($isLatestAttribute));
	
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
		$isLatestAttribute, $rollBackAttribute;
		
	$editor = createTableViewer($attribute);
	
	if ($showRollBackOptions)
		$editor->addEditor(new RollbackEditor($rollBackAttribute, false));
		
	$editor->addEditor(createDefinedMeaningReferenceViewer($firstMeaningAttribute));
	$editor->addEditor(createDefinedMeaningReferenceViewer($relationTypeAttribute));
	$editor->addEditor(createDefinedMeaningReferenceViewer($secondMeaningAttribute));
	$editor->addEditor(createShortTextViewer($operationAttribute));
	$editor->addEditor(createBooleanViewer($isLatestAttribute));
	
	return $editor;
}

function getUpdatedClassMembershipEditor($attribute, $showRollBackOptions) {
	global
		$classAttribute, $classMemberAttribute, $operationAttribute, $isLatestAttribute, $rollBackAttribute;
		
	$editor = createTableViewer($attribute);
	
	if ($showRollBackOptions)
		$editor->addEditor(new RollbackEditor($rollBackAttribute, false));
		
	$editor->addEditor(createDefinedMeaningReferenceViewer($classAttribute));
	$editor->addEditor(createDefinedMeaningReferenceViewer($classMemberAttribute));
	$editor->addEditor(createShortTextViewer($operationAttribute));
	$editor->addEditor(createBooleanViewer($isLatestAttribute));
	
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

function simpleRecord($structure, $values) {
	$attributes = $structure->attributes;
	$result = new ArrayRecord($structure);
	
	for ($i = 0; $i < count($attributes); $i++) 
		$result->setAttributeValue($attributes[$i], $values[$i]);	
	
	return $result;
}

function rollBackTransactions($recordSet) {
	global
		$wgRequest, $wgUser,
		$transactionIdAttribute, $updatesInTransactionAttribute, 
		$updatedDefinitionAttribute, $updatedRelationsAttribute, $updatedClassMembershipAttribute;
		
	$summary = $wgRequest->getText('summary');
	startNewTransaction($wgUser->getID(), wfGetIP(), $summary);
		
	$idStack = new IdStack('transaction');
	$transactionKeyStructure = $recordSet->getKey();
	
	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$transactionRecord = $recordSet->getRecord($i);

		$transactionId = $transactionRecord->getAttributeValue($transactionIdAttribute);
		$idStack->pushKey(simpleRecord($transactionKeyStructure, array($transactionId)));

		$updatesInTransaction = $transactionRecord->getAttributeValue($updatesInTransactionAttribute);
		$idStack->pushAttribute($updatesInTransactionAttribute);

		$updatedDefinitions = $updatesInTransaction->getAttributeValue($updatedDefinitionAttribute);
		$idStack->pushAttribute($updatedDefinitionAttribute);
		rollBackDefinitions($idStack, $updatedDefinitions);
		$idStack->popAttribute();

		$updatedRelations = $updatesInTransaction->getAttributeValue($updatedRelationsAttribute);
		$idStack->pushAttribute($updatedRelationsAttribute);
		rollBackRelations($idStack, $updatedRelations);
		$idStack->popAttribute();
		
		$updatedClassMemberships = $updatesInTransaction->getAttributeValue($updatedClassMembershipAttribute);
		$idStack->pushAttribute($updatedClassMembershipAttribute);
		rollBackClassMemberships($idStack, $updatedClassMemberships);
		$idStack->popAttribute();

		$idStack->popAttribute();
		$idStack->popKey();
	}
}

function getRollBackAction($idStack, $rollBackAttribute) {
	$idStack->pushAttribute($rollBackAttribute);				
	$result = $_POST[$idStack->getId()];
	$idStack->popAttribute();		
	
	return $result;
}

function getMeaningId($record, $referenceAttribute) {
	global
		$definedMeaningIdAttribute;
	
	return $record->getAttributeValue($referenceAttribute)->getAttributeValue($definedMeaningIdAttribute);
}

function rollBackDefinitions($idStack, $definitions) {
	global
		$definedMeaningIdAttribute,	$languageAttribute, $translatedContentIdAttribute, 
		$isLatestAttribute, $operationAttribute, $rollBackTranslatedContentAttribute;
	
	$definitionsKeyStructure = $definitions->getKey();
	
	for ($i = 0; $i < $definitions->getRecordCount(); $i++) {
		$definitionRecord = $definitions->getRecord($i);

		$definedMeaningId = $definitionRecord->getAttributeValue($definedMeaningIdAttribute);
		$languageId = $definitionRecord->getAttributeValue($languageAttribute);
		$isLatest = $definitionRecord->getAttributeValue($isLatestAttribute);

		if ($isLatest) {
			$idStack->pushKey(simpleRecord($definitionsKeyStructure, array($definedMeaningId, $languageId)));

			rollBackTranslatedContent(
				$idStack, 
				getRollBackAction($idStack, $rollBackTranslatedContentAttribute), 
				$definitionRecord->getAttributeValue($translatedContentIdAttribute),
				$languageId,
				$definitionRecord->getAttributeValue($operationAttribute)
			);

			$idStack->popKey();
		}
	}	
}

function rollBackTranslatedContent($idStack, $rollBackAction, $translatedContentId, $languageId, $operation) {
	global	
		$rollBackTranslatedContentAttribute, $translatedContentHistoryAttribute;
	
	if ($rollBackAction == 'previous-version') {
		$idStack->pushAttribute($rollBackTranslatedContentAttribute);
		$idStack->pushAttribute($translatedContentHistoryAttribute);

		$version = (int) $_POST[$idStack->getId()];
		
		if ($version > 0)
			rollBackTranslatedContentToVersion($translatedContentId, $languageId, $version);		
		
		$idStack->popAttribute();
		$idStack->popAttribute();
	}
	else if ($rollBackAction == 'remove') 
		removeTranslatedText($translatedContentId, $languageId);
}

function getTranslatedContentFromHistory($translatedContentId, $languageId, $addTransactionId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT old_text " .
		" FROM translated_content, text " .
		" WHERE translated_content.translated_content_id=$translatedContentId " .
		" AND translated_content.text_id=text.old_id " .
		" AND translated_content.add_transaction_id=$addTransactionId");
		
	$row = $dbr->fetchObject($queryResult);
	
	return $row->old_text;
}

function rollBackTranslatedContentToVersion($translatedContentId, $languageId, $addTransactionId) {
	removeTranslatedText($translatedContentId, $languageId);
	addTranslatedText(
		$translatedContentId, 
		$languageId, 
		getTranslatedContentFromHistory($translatedContentId, $languageId, $addTransactionId)
	);
}

function rollBackRelations($idStack, $relations) {
	global
		$relationIdAttribute, $isLatestAttribute, $firstMeaningAttribute, $secondMeaningAttribute, $relationTypeAttribute,
		$operationAttribute, $rollBackAttribute;
	
	$relationsKeyStructure = $relations->getKey();
	
	for ($i = 0; $i < $relations->getRecordCount(); $i++) {
		$relationRecord = $relations->getRecord($i);

		$relationId = $relationRecord->getAttributeValue($relationIdAttribute);
		$isLatest = $relationRecord->getAttributeValue($isLatestAttribute);

		if ($isLatest) {
			$idStack->pushKey(simpleRecord($relationsKeyStructure, array($relationId)));
			
			rollBackRelation(
				getRollBackAction($idStack, $rollBackAttribute),
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

function shouldRemove($rollBackAction, $operation) {
	return $operation == 'Added' && $rollBackAction == 'remove';
}

function shouldRestore($rollBackAction, $operation) {
	return $operation == 'Removed' && $rollBackAction == 'previous-version';
}

function rollBackRelation($rollBackAction, $relationId, $firstMeaningId, $relationTypeId, $secondMeaningId, $operation) {
	if (shouldRemove($rollBackAction, $operation))
		removeRelationWithId($relationId);
	else if (shouldRestore($rollBackAction, $operation))	
		addRelation($firstMeaningId, $relationTypeId, $secondMeaningId);	
}

function rollBackClassMemberships($idStack, $classMemberships) {
	global
		$classMemebrshipIdAttribute, $isLatestAttribute, $classAttribute, $classMemberAttribute,
		$operationAttribute, $classMembershipIdAttribute, $rollBackAttribute;
	
	$classMembershipsKeyStructure = $classMemberships->getKey();
	
	for ($i = 0; $i < $classMemberships->getRecordCount(); $i++) {
		$classMembershipRecord = $classMemberships->getRecord($i);

		$classMembershipId = $classMembershipRecord->getAttributeValue($classMembershipIdAttribute);
		$isLatest = $classMembershipRecord->getAttributeValue($isLatestAttribute);

		if ($isLatest) {
			$idStack->pushKey(simpleRecord($classMembershipsKeyStructure, array($classMembershipId)));
			
			rollBackClassMembership(
				getRollBackAction($idStack, $rollBackAttribute),
				$classMembershipId,
				getMeaningId($classMembershipRecord, $classAttribute),
				getMeaningId($classMembershipRecord, $classMemberAttribute),
				$classMembershipRecord->getAttributeValue($operationAttribute)
			);
				
			$idStack->popKey();
		}
	}	
}

function rollBackClassMembership($rollBackAction, $classMembershipId, $classId, $classMemberId, $operation) {
	if (shouldRemove($rollBackAction, $operation))
		removeClassMembershipWithId($classMembershipId);
	else if (shouldRestore($rollBackAction, $operation))	
		addClassMembership($classMemberId, $classId);	
}

?>
