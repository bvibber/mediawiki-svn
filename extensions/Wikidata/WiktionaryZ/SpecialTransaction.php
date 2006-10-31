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
			require_once("WiktionaryZRecordSets.php");
			require_once("WiktionaryZAttributes.php");
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
		$definedMeaningReferenceAttribute, $definedMeaningIdAttribute, $updatesInTransactionAttribute;

	$operationAttribute = new Attribute('operation', 'Operation', 'text');
	$updatedDefinitionStructure = new Structure($definedMeaningIdAttribute, $definedMeaningReferenceAttribute, $languageAttribute, $textAttribute);		
	$updatedDefinitionAttribute = new Attribute('updated-definition', 'Definition', new RecordSetType($updatedDefinitionStructure));
	$updatesInTransactionAttribute = new Attribute('updates-in-transaction', 'Updates in transaction', new RecordType(new Structure($updatedDefinitionAttribute)));
}

function getTransactionOverview() {
	global
		$transactionsTable, $transactionAttribute, $transactionIdAttribute, $userAttribute, $userIPAttribute, 
		$timestampAttribute, $summaryAttribute, $updatesInTransactionAttribute, $updatedDefinitionAttribute;

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
		$updatesInTransactionAttribute, $updatedDefinitionAttribute;
		
	$record = new ArrayRecord($updatesInTransactionAttribute->type->getStructure());
	$record->setAttributeValue($updatedDefinitionAttribute, getUpdatedDefinedMeaningDefinitionRecordSet($transactionId));
	
	return $record;
}

function getUpdatedDefinedMeaningDefinitionRecordSet($transactionId) {
	global
		$updatedDefinitionAttribute, $languageAttribute, $textAttribute, $definedMeaningIdAttribute, 
		$definedMeaningReferenceAttribute, $updatedDefinitionStructure, $operationAttribute;
		
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT MIN(defined_meaning_id) AS defined_meaning_id, language_id, old_text, IF(translated_content.add_transaction_id=$transactionId, 'Added', 'Removed') AS operation " .
		" FROM uw_defined_meaning, translated_content, text " .
		" WHERE uw_defined_meaning.meaning_text_tcid=translated_content.translated_content_id ".
		" AND translated_content.text_id=text.old_id " .
		" AND (translated_content.add_transaction_id=$transactionId OR translated_content.remove_transaction_id=$transactionId) " .
		" GROUP BY defined_meaning_id, language_id, operation " 
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

?>
