<?php

if (!defined('MEDIAWIKI')) die();

$wgExtensionFunctions[] = 'wfSpecialTransaction';

function wfSpecialTransaction() {
	class SpecialTransaction extends SpecialPage {
		function SpecialTransaction() {
			SpecialPage::SpecialPage('Transaction');
		}
		
		function execute( $par ) {
			global
				$wgOut;
			
			require_once("WikiDataTables.php");
			require_once("WiktionaryZAttributes.php");
			require_once("WiktionaryZRecordSets.php");
			require_once("WiktionaryZEditors.php");
			require_once("Transaction.php");
			require_once("Editor.php");
			require_once("Controller.php");
			
			initializeAttributes();
			
			$wgOut->addHTML(getTransactionOverview());
			$wgOut->addHTML(DefaultEditor::getExpansionCss());
			$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");
		}
	}
	
	SpecialPage::addPage(new SpecialTransaction());
}

function initializeAttributes() {
	global
		$operationAttribute, $updatedDefinitionStructure, $updatedDefinitionAttribute, $languageAttribute, $textAttribute, 
		$definedMeaningReferenceAttribute, $definedMeaningIdAttribute, $updatesInTransactionAttribute,
		$expressionAttribute, $identicalMeaningAttribute, $updatedSyntransesAttribute;

	$operationAttribute = new Attribute('operation', 'Operation', 'text');
	
	$updatedDefinitionStructure = new Structure(
		$definedMeaningIdAttribute, 
		$definedMeaningReferenceAttribute, 
		$languageAttribute, 
		$textAttribute
	);		
	
	$updatedDefinitionAttribute = new Attribute('updated-definition', 'Definition', new RecordSetType($updatedDefinitionStructure));

	$updatedSyntransesStructure = new Structure(
		$definedMeaningIdAttribute, 
		$definedMeaningReferenceAttribute, 
		$expressionAttribute, 
		$identicalMeaningAttribute
	); 
	
	$updatedSyntransesAttribute = new Attribute('updated-syntranses', 'Synonyms and translations', new RecordSetType($updatedSyntransesStructure));
	
	$updatesInTransactionStructure = new Structure(
		$updatedDefinitionAttribute,
		$updatedSyntransesAttribute
	);
	
	$updatesInTransactionAttribute = new Attribute('updates-in-transaction', 'Updates in transaction', new RecordType($updatesInTransactionStructure));
}

function getTransactionOverview() {
	global
		$transactionsTable, $transactionAttribute, $transactionIdAttribute, $userAttribute, $userIPAttribute, 
		$timestampAttribute, $summaryAttribute, $updatesInTransactionAttribute, $updatedDefinitionAttribute,
		$updatedSyntransesAttribute;

	$queryTransactionInformation = new QueryLatestTransactionInformation();

	$recordSet = queryRecordSet(
		$queryTransactionInformation,
		$transactionIdAttribute,
		array(
			'transaction_id' => $transactionIdAttribute
		),
		$transactionsTable,
		array(),
		array('transaction_id DESC'),
		10
	);
	
	$recordSet->getStructure()->attributes[] = $transactionIdAttribute;
	expandTransactionIDsInRecordSet($recordSet, $transactionIdAttribute, $transactionAttribute);
	
	$recordSet->getStructure()->attributes[] = $updatesInTransactionAttribute;
	expandUpdatesInTransactionInRecordSet($recordSet);

	$captionEditor = new RecordSpanEditor($transactionAttribute, ': ', ', ', false);
	$captionEditor->addEditor(new TimestampEditor($timestampAttribute, new SimplePermissionController(false), false));
	$captionEditor->addEditor(new UserEditor($userAttribute, new SimplePermissionController(false), false));
	$captionEditor->addEditor(new TextEditor($summaryAttribute, new SimplePermissionController(false), false));
	
	$valueEditor = new RecordUnorderedListEditor($updatesInTransactionAttribute, 5);
	$valueEditor->addEditor(getUpdatedDefinedMeaningDefinitionEditor($updatedDefinitionAttribute));
	$valueEditor->addEditor(getUpdatedSyntransesEditor($updatedSyntransesAttribute));
	
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
		$updatesInTransactionAttribute, $updatedDefinitionAttribute, $updatedSyntransesAttribute;
		
	$record = new ArrayRecord($updatesInTransactionAttribute->type->getStructure());
	$record->setAttributeValue($updatedDefinitionAttribute, getUpdatedDefinedMeaningDefinitionRecordSet($transactionId));
	$record->setAttributeValue($updatedSyntransesAttribute, getUpdatedSyntransesRecordSet($transactionId));
	
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

function getUpdatedDefinedMeaningDefinitionEditor($attribute) {
	global
		$definedMeaningReferenceAttribute, $languageAttribute, $textAttribute, $operationAttribute;
	
	$editor = new RecordSetTableEditor($attribute, new SimplePermissionController(false), false, false, false, null);
	$editor->addEditor(new DefinedMeaningReferenceEditor($definedMeaningReferenceAttribute, new SimplePermissionController(false), false));
	$editor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), false));
	$editor->addEditor(new TextEditor($textAttribute, new SimplePermissionController(false), false));
	$editor->addEditor(new TextEditor($operationAttribute, new SimplePermissionController(false), false));
	
	return $editor;
}

function getUpdatedSyntransesEditor($attribute) {
	global
		$definedMeaningReferenceAttribute, $expressionAttribute, $identicalMeaningAttribute, $operationAttribute;
		
	$editor = new RecordSetTableEditor($attribute, new SimplePermissionController(false), false, false, false, null);
	$editor->addEditor(new DefinedMeaningReferenceEditor($definedMeaningReferenceAttribute, new SimplePermissionController(false), false));
	$editor->addEditor(getExpressionTableCellEditor($expressionAttribute));
	$editor->addEditor(new BooleanEditor($identicalMeaningAttribute, new SimplePermissionController(false), false, false));
	$editor->addEditor(new TextEditor($operationAttribute, new SimplePermissionController(false), false));
	
	return $editor;
}

?>
