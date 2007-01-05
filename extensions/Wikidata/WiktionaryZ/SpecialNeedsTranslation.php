<?php
	if (!defined('MEDIAWIKI')) die();

	$wgExtensionFunctions[] = 'wfSpecialNeedsTranslation';

	function wfSpecialNeedsTranslation() {
		class SpecialNeedsTranslation extends SpecialPage {
			function SpecialNeedsTranslation() {
				SpecialPage::SpecialPage('NeedsTranslation');
			}

			function execute($par) {
				global $wgOut, $wgRequest;

				require_once("forms.php");
				require_once("type.php");
				require_once("WiktionaryZAttributes.php");

				initializeWiktionaryZAttributes(false, false);
				$wgOut->setPageTitle('Expressions needing translation');

				$sourceLanguageId = $_GET['from-lang'];
				$destinationLanguageId = $_GET['to-lang'];
				$collectionId = $_GET['collection'];

				$wgOut->addHTML(getOptionPanel(
					array(
						'Destination language' => getSuggest('to-lang', 'language', array(), $destinationLanguageId, languageIdAsText($destinationLanguageId)),
						'Source language' => getSuggest('from-lang', 'language', array(), $sourceLanguageId, languageIdAsText($sourceLanguageId)),
						'Collection' => getSuggest('collection', 'collection', array(), $collectionId, collectionIdAsText($collectionId))
					)
				));

				if ($destinationLanguageId == '')
					$wgOut->addHTML('<p>Please specify a destination language.</p>');
				else
					$this->showExpressionsNeedingTranslation($sourceLanguageId,$destinationLanguageId,$collectionId);
			}

			protected function showExpressionsNeedingTranslation($sourceLanguageId, $destinationLanguageId,$collectionId) {
				global
					$definedMeaningIdAttribute, $expressionIdAttribute, $expressionAttribute, $expressionStructure, $spellingAttribute, $languageAttribute;

				require_once("Transaction.php");
				require_once("WiktionaryZAttributes.php");
				require_once("RecordSet.php");
				require_once("Editor.php");
				require_once("Expression.php");

				$dbr = &wfGetDB(DB_SLAVE);

				$sql = 'SELECT source_expression.expression_id AS source_expression_id, source_expression.language_id AS source_language_id, source_expression.spelling AS source_spelling, source_syntrans.defined_meaning_id AS source_defined_meaning_id' .
					' FROM (uw_syntrans source_syntrans, uw_expression_ns source_expression)';

				if ($collectionId != '')
					$sql .= ' JOIN uw_collection_contents ON source_syntrans.defined_meaning_id = member_mid';

				$sql .= ' WHERE source_syntrans.expression_id = source_expression.expression_id';

				if ($sourceLanguageId != '')
					$sql .= ' AND source_expression.language_id = ' . $sourceLanguageId;
				if ($collectionId != '')
					$sql .= ' AND uw_collection_contents.collection_id = ' . $collectionId .
						' AND ' . getLatestTransactionRestriction('uw_collection_contents');

				$sql .= ' AND NOT EXISTS (' .
					' SELECT * FROM uw_syntrans destination_syntrans, uw_expression_ns destination_expression' .
					' WHERE destination_syntrans.expression_id = destination_expression.expression_id AND destination_expression.language_id = ' .$destinationLanguageId .
					' AND source_syntrans.defined_meaning_id = destination_syntrans.defined_meaning_id' .
					' AND ' . getLatestTransactionRestriction('destination_syntrans') .
					' AND ' . getLatestTransactionRestriction('destination_expression') .
					')' .
					' AND ' . getLatestTransactionRestriction('source_syntrans') .
					' AND ' . getLatestTransactionRestriction('source_expression') .
					' LIMIT 100';

				$queryResult = $dbr->query($sql);
				$definitionAttribute = new Attribute("definition", "Definition", "definition");
				$recordSet = new ArrayRecordSet(new Structure($definedMeaningIdAttribute, $expressionIdAttribute, $expressionAttribute, $definitionAttribute), new Structure($definedMeaningIdAttribute, $expressionIdAttribute));

				while ($row = $dbr->fetchObject($queryResult)) {
					$expressionRecord = new ArrayRecord($expressionStructure);
					$expressionRecord->setAttributeValue($languageAttribute, $row->source_language_id);
					$expressionRecord->setAttributeValue($spellingAttribute, $row->source_spelling);

					$recordSet->addRecord(array($row->source_defined_meaning_id, $row->source_expression_id, $expressionRecord, getDefinedMeaningDefinition($row->source_defined_meaning_id)));
				}

				$expressionEditor = new RecordTableCellEditor($expressionAttribute);
				$expressionEditor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), false));
				$expressionEditor->addEditor(new SpellingEditor($spellingAttribute, new SimplePermissionController(false), false));

				$editor = new RecordSetTableEditor(null, new SimplePermissionController(false), new ShowEditFieldChecker(true), new AllowAddController(false), false, false, null);
				$editor->addEditor($expressionEditor);
				$editor->addEditor(new TextEditor($definitionAttribute, new SimplePermissionController(false), false, true, 75));

				global $wgOut;

				$wgOut->addHTML($editor->view(new IdStack("expression"), $recordSet));
			}
		}

		SpecialPage::addPage(new SpecialNeedsTranslation);
	}
?>
