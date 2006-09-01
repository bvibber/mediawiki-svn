<?php

require_once('Transaction.php');
require_once('Expression.php');
require_once('forms.php');
require_once('Attribute.php');
require_once('WiktionaryZAttributes.php');
require_once('Record.php');
require_once('RecordSet.php');
require_once('type.php');
require_once('languages.php');
require_once('Editor.php');
require_once('HTMLtable.php');


/**
 * Load and modify content in a WiktionaryZ-enabled
 * namespace.
 *
 * @package MediaWiki
 */
class WiktionaryZ {

	function WiktionaryZ() {
		global $wgMessageCache;
		$wgMessageCache->addMessages(
			array(
				'wz_uilang'=>'Your user interface language: $1',
				'wz_uilang_set'=>'Set your preferences',
				'wz_save' => 'Save',
				'wz_history' => 'History'
			)
		);

	}

	function view() {
		global
			$wgOut, $wgTitle;

		$wgOut->addHTML($this->getLanguageSelector());
		$spelling = $wgTitle->getText();
		$wgOut->addHTML($this->getExpressionsEditor($spelling)->view(new IdStack("expression"), $this->getExpressionsRecordSet($spelling)));
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");

		# We may later want to disable the regular page component
		# $wgOut->setPageTitleArray($this->mTitle->getTitleArray());
	}

	function history() {
		global
			$wgOut, $wgTitle;

		$wgOut->addHTML($this->getLanguageSelector());
		$spelling = $wgTitle->getText();
		$wgOut->addHTML($this->getExpressionsEditor($spelling)->view(new IdStack("expression"), $this->getExpressionsRecordSet($spelling)));
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");

		$titleArray = $wgTitle->getTitleArray();
		$titleArray["actionprefix"] = wfMsg('wz_history');
		$wgOut->setPageTitleArray($titleArray);
	}

	function getLanguageSelector() {
		global $wgUser;
		$userlang=$wgUser->getOption('language');
		$skin = $wgUser->getSkin();
		return wfMsg('wz_uilang',"<b>$userlang</b>").  " &mdash; " . $skin->makeLink("Special:Preferences", wfMsg('wz_uilang_set'));
	}

	function getTransactionEditor($attribute) {
		global
			$userAttribute, $timestampAttribute;

		$transactionEditor = new RecordTableCellEditor($attribute);
		$transactionEditor->addEditor(new UserEditor($userAttribute, new SimplePermissionController(false), true));
		$transactionEditor->addEditor(new ShortTextEditor($timestampAttribute, new SimplePermissionController(false), true));

		return $transactionEditor;
	}

	function addTableLifeSpanEditor($editor) {
		global
			$recordLifeSpanAttribute, $addTransactionAttribute, $removeTransactionAttribute, $wgRequest;

		if ($wgRequest->getText('action') == 'history') {
			$lifeSpanEditor = new RecordTableCellEditor($recordLifeSpanAttribute);
			$lifeSpanEditor->addEditor($this->getTransactionEditor($addTransactionAttribute));
			$lifeSpanEditor->addEditor($this->getTransactionEditor($removeTransactionAttribute));

			$editor->addEditor($lifeSpanEditor);
		}
	}

	function getTranslatedTextEditor($attribute, $controller) {
		global
			$languageAttribute, $textAttribute;

		$editor = new RecordSetTableEditor($attribute, new SimplePermissionController(true), true, true, true, $controller);
		$editor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));
		$editor->addEditor(new TextEditor($textAttribute, new SimplePermissionController(true), true));

		$this->addTableLifeSpanEditor($editor);

		return $editor;
	}

	function getAlternativeDefinitionsEditor() {
		global
			$alternativeDefinitionsAttribute, $alternativeDefinitionAttribute, $sourceAttribute;

		$editor = new RecordSetTableEditor($alternativeDefinitionsAttribute, new SimplePermissionController(true), true, true, false, new DefinedMeaningAlternativeDefinitionsController());
//		$editor = new RecordSetTableEditor($alternativeDefinitionsAttribute, new AlternativeDefinitionsPermissionController(), true, true, false, new DefinedMeaningAlternativeDefinitionsController());
		$editor->addEditor($this->getTranslatedTextEditor($alternativeDefinitionAttribute, new DefinedMeaningAlternativeDefinitionController()));
		$editor->addEditor(new DefinedMeaningEditor($sourceAttribute, new SimplePermissionController(false), true));

		return $editor;
	}

	function getSynonymsAndTranslationsEditor() {
		global
			$synonymsAndTranslationsAttribute, $identicalMeaningAttribute, $expressionIdAttribute, $expressionAttribute, $languageAttribute,
			$spellingAttribute;

		$expressionEditor = new RecordTableCellEditor($expressionAttribute);
		$expressionEditor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));
		$expressionEditor->addEditor(new SpellingEditor($spellingAttribute, new SimplePermissionController(false), true));

		$tableEditor = new RecordSetTableEditor($synonymsAndTranslationsAttribute, new SimplePermissionController(true), true, true, false, new SynonymTranslationController());
		$tableEditor->addEditor($expressionEditor);
		$tableEditor->addEditor(new BooleanEditor($identicalMeaningAttribute, new SimplePermissionController(true), true, true));

		$this->addTableLifeSpanEditor($tableEditor);

		return $tableEditor;
	}

	function getDefinedMeaningRelationsEditor() {
		global
			$relationsAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute;

		$editor = new RecordSetTableEditor($relationsAttribute, new SimplePermissionController(true), true, true, false, new DefinedMeaningRelationController());
		$editor->addEditor(new RelationTypeEditor($relationTypeAttribute, new SimplePermissionController(false), true));
		$editor->addEditor(new DefinedMeaningEditor($otherDefinedMeaningAttribute, new SimplePermissionController(false), true));

		$this->addTableLifeSpanEditor($editor);

		return $editor;
	}

	function getDefinedMeaningClassMembershipEditor() {
		global
			$classMembershipAttribute, $classAttribute;

		$editor = new RecordSetTableEditor($classMembershipAttribute, new SimplePermissionController(true), true, true, false, new DefinedMeaningClassMembershipController());
		$editor->addEditor(new AttributeEditor($classAttribute, new SimplePermissionController(false), true));

		$this->addTableLifeSpanEditor($editor);

		return $editor;
	}

	function getDefinedMeaningCollectionMembershipEditor() {
		global
			$collectionMembershipAttribute, $collectionAttribute, $sourceIdentifierAttribute;

		$editor = new RecordSetTableEditor($collectionMembershipAttribute, new SimplePermissionController(true), true, true, false, new DefinedMeaningCollectionController());
		$editor->addEditor(new CollectionEditor($collectionAttribute, new SimplePermissionController(false), true));
		$editor->addEditor(new ShortTextEditor($sourceIdentifierAttribute, new SimplePermissionController(true), true));

		$this->addTableLifeSpanEditor($editor);

		return $editor;
	}

	function getDefinedMeaningTextAttributeValuesEditor() {
		global
			$textAttributeAttribute, $textValueAttribute, $textAttributeValuesAttribute;

		$editor = new RecordSetTableEditor($textAttributeValuesAttribute, new SimplePermissionController(true), true, true, false, new DefinedMeaningTextAttributeValuesController());
		$editor->addEditor(new TextAttributeEditor($textAttributeAttribute, new SimplePermissionController(false), true));
		$editor->addEditor($this->getTranslatedTextEditor($textValueAttribute, new DefinedMeaningTextAttributeValueController()));

		return $editor;
	}

	function getDefinedMeaningRecord($definedMeaningId, $expressionId) {
		global
			$definedMeaningAttribute, $definitionAttribute, $alternativeDefinitionsAttribute, $synonymsAndTranslationsAttribute,
			$relationsAttribute, $classMembershipAttribute, $collectionMembershipAttribute, $textAttributeValuesAttribute;

		$record = new ArrayRecord($definedMeaningAttribute->type->getStructure());
		$record->setAttributeValue($definitionAttribute, $this->getDefinedMeaningDefinitionRecordSet($definedMeaningId));
		$record->setAttributeValue($alternativeDefinitionsAttribute, $this->getAlternativeDefinitionsRecordSet($definedMeaningId));
		$record->setAttributeValue($synonymsAndTranslationsAttribute, $this->getSynonymAndTranslationRecordSet($definedMeaningId, $expressionId));
		$record->setAttributeValue($relationsAttribute, $this->getDefinedMeaningRelationsRecordSet($definedMeaningId));
		$record->setAttributeValue($classMembershipAttribute, $this->getDefinedMeaningClassMembershipRecordSet($definedMeaningId));
		$record->setAttributeValue($collectionMembershipAttribute, $this->getDefinedMeaningCollectionMembershipRecordSet($definedMeaningId));
		$record->setAttributeValue($textAttributeValuesAttribute, $this->getDefinedMeaningTextAttributeValuesRecordSet($definedMeaningId));

		return $record;
	}

	function getDefinedMeaningsRecordSet($expressionId) {
		global
			$definedMeaningIdAttribute, $textAttribute, $definedMeaningAttribute;

		$recordset = new ArrayRecordSet(new Structure($definedMeaningIdAttribute, $textAttribute, $definedMeaningAttribute), new Structure($definedMeaningIdAttribute));

		$definedMeaningIds = $this->getDefinedMeaningsForExpression($expressionId);

		foreach($definedMeaningIds as $definedMeaningId)
			$recordset->addRecord(array($definedMeaningId, getDefinedMeaningDefinition($definedMeaningId), $this->getDefinedMeaningRecord($definedMeaningId, $expressionId)));

		return $recordset;
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

			$result->addRecord(array($expression->expression_id, $expressionRecord, $this->getDefinedMeaningsRecordSet($expression->expression_id)));
		}

		return $result;
	}

	function getExpressionsEditor($spelling) {
		global
			$expressionsAttribute, $definedMeaningAttribute, $expressionAttribute, $expressionMeaningsAttribute,
			$languageAttribute, $textAttribute, $definitionAttribute;

		$definitionEditor = $this->getTranslatedTextEditor($definitionAttribute, new DefinedMeaningDefinitionController());
		$synonymsAndTranslationsEditor = $this->getSynonymsAndTranslationsEditor();
		$relationsEditor = $this->getDefinedMeaningRelationsEditor();
		$classMembershipEditor = $this->getDefinedMeaningClassMembershipEditor();
		$collectionMembershipEditor = $this->getDefinedMeaningCollectionMembershipEditor();
		$textAttributeValuesEditor = $this->getDefinedMeaningTextAttributeValuesEditor();

		$definedMeaningEditor = new RecordListEditor($definedMeaningAttribute, true, false, true, null);
		$definedMeaningEditor->addEditor($definitionEditor);
		$definedMeaningEditor->addEditor($this->getAlternativeDefinitionsEditor());
		$definedMeaningEditor->addEditor($synonymsAndTranslationsEditor);
		$definedMeaningEditor->addEditor($relationsEditor);
		$definedMeaningEditor->addEditor($classMembershipEditor);
		$definedMeaningEditor->addEditor($collectionMembershipEditor);
		$definedMeaningEditor->addEditor($textAttributeValuesEditor);

		$definedMeaningEditor->expandEditor($definitionEditor);
		$definedMeaningEditor->expandEditor($synonymsAndTranslationsEditor);
//		$definedMeaningEditor->expandEditor($relationsEditor);
//		$definedMeaningEditor->expandEditor($classMembershipEditor);
//		$definedMeaningEditor->expandEditor($collectionMembershipEditor);
//		$definedMeaningEditor->expandEditor($textAttributeValuesEditor);

		$definedMeaningCaptionEditor = new TextEditor($textAttribute, new SimplePermissionController(false), false, true, 100);
		$definedMeaningCaptionEditor->setAddText("New defined meaning");

		$expressionMeaningsEditor = new RecordSetListEditor($expressionMeaningsAttribute, new SimplePermissionController(true), true, false, true, new ExpressionMeaningController(), 3, false);
		$expressionMeaningsEditor->setCaptionEditor($definedMeaningCaptionEditor);
		$expressionMeaningsEditor->setValueEditor($definedMeaningEditor);

		$expressionEditor = new RecordSpanEditor($expressionAttribute, ': ', ' - ');
		$expressionEditor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));

		$expressionsEditor = new RecordSetListEditor($expressionsAttribute, new SimplePermissionController(true), true, false, false, new ExpressionController($spelling), 2, true);
		$expressionsEditor->setCaptionEditor($expressionEditor);
		$expressionsEditor->setValueEditor($expressionMeaningsEditor);

		return $expressionsEditor;
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
			$recordSet->addRecord(array($alternativeDefinition->meaning_text_tcid, $this->getTranslatedTextRecordSet($alternativeDefinition->meaning_text_tcid), $alternativeDefinition->source_id));

		return $recordSet;
	}

	function saveForm() {
		global
			$wgTitle, $wgUser, $wgRequest;

		$summary = $wgRequest->getText('summary');

		startNewTransaction($wgUser->getID(), wfGetIP(), $summary);

		$spelling = $wgTitle->getText();
		$this->getExpressionsEditor($spelling)->save(new IdStack("expression"), $this->getExpressionsRecordSet($spelling));

		Title::touchArray(array($wgTitle));
		$now = wfTimestampNow();
		RecentChange::notifyEdit($now, $wgTitle, false, $wgUser, $summary,
			0, $now, false, '', 0, 0, 0);
	}

	function edit() {
		global
			$wgOut, $wgTitle, $wgUser, $wgRequest;

		if ($wgRequest->getText('save') != '')
			$this->saveForm();

		$userlang = $wgUser->getOption('language');
		$skin = $wgUser->getSkin();
		$spelling = $wgTitle->getText();

		$wgOut->addHTML("Your user interface language preference: <b>$userlang</b> - " . $skin->makeLink("Special:Preferences", "set your preferences"));
		$wgOut->addHTML('<form method="post" action="">');
		$wgOut->addHTML($this->getExpressionsEditor($spelling)->edit(new IdStack("expression"), $this->getExpressionsRecordSet($spelling)));
		$wgOut->addHTML('<div class="save-panel">');
			$wgOut->addHTML('<table cellpadding="0" cellspacing="0"><tr><th>' . wfMsg('summary') . ': </th><td>' . getTextBox("summary") .'</td></tr></table>');
			$wgOut->addHTML(getSubmitButton("save", wfMsg('wz_save')));
		$wgOut->addHTML('</div>');
		$wgOut->addHTML('</form>');
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");

		$titleArray = $wgTitle->getTitleArray();
		$titleArray["actionprefix"] = wfMsg('editing');
		$wgOut->setPageTitleArray($titleArray);
	}

	function getDefinedMeaningsForExpression($expressionId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$definedMeanings = array();
		$queryResult = $dbr->query("SELECT defined_meaning_id FROM uw_syntrans WHERE expression_id=$expressionId" .
									" AND ". getLatestTransactionRestriction('uw_syntrans'));

		while($definedMeaning = $dbr->fetchObject($queryResult))
			$definedMeanings[] = $definedMeaning->defined_meaning_id;

		return $definedMeanings;
	}

	function getDefinedMeaningDefinitionRecordSet($definedMeaningId) {
		$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
		
		return $this->getTranslatedTextRecordSet($definitionId);
	}

	function getTranslatedTextRecordSet($textId) {
		global
			$wgRequest;

		if ($wgRequest->getText('action') == 'history')
			return $this->getTranslatedTextHistoryRecordSet($textId);
		else
			return $this->getTranslatedTextLatestRecordSet($textId);
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

	function getSynonymAndTranslationRecordSet($definedMeaningId, $skippedExpressionId) {
		global
			$wgRequest;

		if ($wgRequest->getText('action') == 'history')
			return $this->getSynonymAndTranslationHistoryRecordSet($definedMeaningId, $skippedExpressionId);
		else
			return $this->getSynonymAndTranslationLatestRecordSet($definedMeaningId, $skippedExpressionId);
	}

	function getSynonymAndTranslationHistoryRecordSet($definedMeaningId, $skippedExpressionId) {
		global
			$expressionIdAttribute, $expressionAttribute, $languageAttribute, $spellingAttribute, $identicalMeaningAttribute,
			$recordLifeSpanAttribute;

		$dbr =& wfGetDB(DB_SLAVE);

		$expressionStructure = $expressionAttribute->type->getStructure();
		$recordset = new ArrayRecordSet(new Structure($expressionIdAttribute, $expressionAttribute, $identicalMeaningAttribute, $recordLifeSpanAttribute),
										new Structure($expressionIdAttribute));
		$queryResult = $dbr->query("SELECT uw_expression_ns.expression_id, spelling, language_id, endemic_meaning, uw_syntrans.add_transaction_id AS syntrans_add, uw_syntrans.remove_transaction_id AS syntrans_remove, NOT uw_syntrans.remove_transaction_id IS NULL is_live " .
									" FROM uw_syntrans, uw_expression_ns " .
									" WHERE uw_syntrans.defined_meaning_id=$definedMeaningId AND uw_syntrans.expression_id!=$skippedExpressionId " .
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

	function getSynonymAndTranslationLatestRecordSet($definedMeaningId, $skippedExpressionId) {
		global
			$expressionIdAttribute, $expressionAttribute, $languageAttribute, $spellingAttribute, $identicalMeaningAttribute;

		$dbr =& wfGetDB(DB_SLAVE);

		$expressionStructure = $expressionAttribute->type->getStructure();
		$recordset = new ArrayRecordSet(new Structure($expressionIdAttribute, $expressionAttribute, $identicalMeaningAttribute), new Structure($expressionIdAttribute));
		$queryResult = $dbr->query("SELECT uw_expression_ns.expression_id, spelling, language_id, endemic_meaning FROM uw_syntrans, uw_expression_ns WHERE uw_syntrans.defined_meaning_id=$definedMeaningId AND uw_syntrans.expression_id!=$skippedExpressionId " .
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
			return $this->getDefinedMeaningRelationsHistoryRecordSet($definedMeaningId);
		else
			return $this->getDefinedMeaningRelationsLatestRecordSet($definedMeaningId);
	}

	function getDefinedMeaningRelationsLatestRecordSet($definedMeaningId) {
		global
			$relationTypeAttribute, $otherDefinedMeaningAttribute;

		$structure = new Structure($relationTypeAttribute, $otherDefinedMeaningAttribute);
		$recordset = new ArrayRecordSet($structure, $structure);

		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT relationtype_mid, meaning2_mid FROM uw_meaning_relations " .
									"WHERE meaning1_mid=$definedMeaningId AND relationtype_mid!=0 " .
									" AND ". getLatestTransactionRestriction('uw_meaning_relations').
									"ORDER BY relationtype_mid");

		while($definedMeaningRelation = $dbr->fetchObject($queryResult))
			$recordset->addRecord(array($definedMeaningRelation->relationtype_mid, $definedMeaningRelation->meaning2_mid));

		return $recordset;
	}

	function getDefinedMeaningRelationsHistoryRecordSet($definedMeaningId) {
		global
			$relationTypeAttribute, $otherDefinedMeaningAttribute, $recordLifeSpanAttribute;

		$structure = new Structure($relationTypeAttribute, $otherDefinedMeaningAttribute, $recordLifeSpanAttribute);
		$recordSet = new ArrayRecordSet($structure, $structure);

		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT relationtype_mid, meaning2_mid, add_transaction_id, remove_transaction_id, NOT remove_transaction_id IS NULL AS is_live FROM uw_meaning_relations " .
									"WHERE meaning1_mid=$definedMeaningId AND relationtype_mid!=0 ORDER BY is_live, relationtype_mid");

		while($definedMeaningRelation = $dbr->fetchObject($queryResult))
			$recordSet->addRecord(array($definedMeaningRelation->relationtype_mid, $definedMeaningRelation->meaning2_mid,
										getRecordLifeSpanTuple($definedMeaningRelation->add_transaction_id, $definedMeaningRelation->remove_transaction_id)));

		return $recordSet;
	}

	function getDefinedMeaningCollectionMembershipRecordSet($definedMeaningId) {
		global
			$wgRequest;

		if ($wgRequest->getText('action') == 'history')
			return $this->getDefinedMeaningCollectionMembershipHistoryRecordSet($definedMeaningId);
		else
			return $this->getDefinedMeaningCollectionMembershipLatestRecordSet($definedMeaningId);
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
			$recordSet->addRecord(array($attributeValue->attribute_mid, $attributeValue->value_tcid, $this->getTranslatedTextRecordSet($attributeValue->value_tcid)));

		return $recordSet;
	}

	function getDefinedMeaningClassMembershipRecordSet($definedMeaningId) {
		global
			$wgRequest;

		if ($wgRequest->getText('action') == 'history')
			return $this->getDefinedMeaningClassMembershipHistoryRecordSet($definedMeaningId);
		else
			return $this->getDefinedMeaningClassMembershipLatestRecordSet($definedMeaningId);
	}

	function getDefinedMeaningClassMembershipLatestRecordSet($definedMeaningId) {
		global
			$classAttribute;

		$structure = new Structure($classAttribute);
		$recordset = new ArrayRecordSet($structure, $structure);

		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT relationtype_mid, meaning2_mid FROM uw_meaning_relations" .
									" WHERE meaning1_mid=$definedMeaningId AND relationtype_mid=0 " .
									" AND ". getLatestTransactionRestriction('uw_meaning_relations'));

		while($class = $dbr->fetchObject($queryResult))
			$recordset->addRecord(array($class->meaning2_mid));

		return $recordset;
	}

	function getDefinedMeaningClassMembershipHistoryRecordSet($definedMeaningId) {
		global
			$classAttribute, $recordLifeSpanAttribute;

		$structure = new Structure($classAttribute, $recordLifeSpanAttribute);
		$recordset = new ArrayRecordSet($structure, $structure);

		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT relationtype_mid, meaning2_mid, add_transaction_id, remove_transaction_id, NOT remove_transaction_id IS NULL AS is_live" .
									" FROM uw_meaning_relations" .
									" WHERE meaning1_mid=$definedMeaningId AND relationtype_mid=0 " .
									" ORDER BY is_live ");

		while($class = $dbr->fetchObject($queryResult))
			$recordset->addRecord(array($class->meaning2_mid,
										getRecordLifeSpanTuple($class->add_transaction_id, $class->remove_transaction_id)));

		return $recordset;
	}

//	function getDefinedMeaningRelations($definedMeaningIds) {
//		$dbr =& wfGetDB(DB_SLAVE);
//	    $definedMeaningRelations = array();
//
//		foreach($definedMeaningIds as $definedMeaningId) {
//			$relations = array();
//			$queryResult = $dbr->query("SELECT relationtype_mid, meaning2_mid from uw_meaning_relations where meaning1_mid=$definedMeaningId and relationtype_mid!=0");
//
//			while($definedMeaningRelation = $dbr->fetchObject($queryResult))
//				$relations[$definedMeaningRelation->relationtype_mid][] = $definedMeaningRelation->meaning2_mid;
//
//			$definedMeaningRelations[$definedMeaningId] = $relations;
//		}
//
//		return $definedMeaningRelations;
//	}
//
//	function getExpressionForMeaningId($mid, $langcode) {
////		$dbr =& wfGetDB(DB_SLAVE);
////		$sql="SELECT spelling from uw_syntrans,uw_expression_ns where defined_meaning_id=".$mid." and uw_expression_ns.expression_id=uw_syntrans.expression_id and uw_expression_ns.language_id=".$langcode." limit 1";
////		$sp_res=$dbr->query($sql);
////		$sp_row=$dbr->fetchObject($sp_res);
////		return $sp_row->spelling;
//		$expressions = $this->getExpressionsForDefinedMeaningIds(array($mid));
//		return $expressions[$mid];
//	}

//	# Fixme, the following function only returns English expressions
//	# Should be expressions in the language of preference, with an appropriate fallback scheme
//	function getExpressionsForDefinedMeaningIds($definedMeaningIds) {
//		$dbr =& wfGetDB(DB_SLAVE);
//		$queryResult = $dbr->query("SELECT defined_meaning_id, spelling from uw_syntrans, uw_expression_ns where defined_meaning_id in (". implode(",", $definedMeaningIds) . ") and uw_expression_ns.expression_id=uw_syntrans.expression_id and uw_expression_ns.language_id=85 and uw_syntrans.endemic_meaning=1");
//		$expressions = array();
//
//		while ($expression = $dbr->fetchObject($queryResult))
//			if (!array_key_exists($expression->defined_meaning_id, $expressions))
//				$expressions[$expression->defined_meaning_id] = $expression->spelling;
//
//		return $expressions;
//	}
	}

class DefinedMeaningDefinitionController implements Controller {
	public function add($keyPath, $record) {
		global
			$expressionIdAttribute, $definedMeaningIdAttribute, $languageAttribute, $textAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$languageId = $record->getAttributeValue($languageAttribute);
		$text = $record->getAttributeValue($textAttribute);

		if ($languageId != 0 && $text != "")
			addDefinedMeaningDefinition($definedMeaningId, $languageId, $text);
	}

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $languageAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		removeDefinedMeaningDefinition($definedMeaningId, $languageId);
	}

	public function update($keyPath, $record) {
		global
			$definedMeaningIdAttribute, $languageAttribute, $textAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		$text = $record->getAttributeValue($textAttribute);

		if ($text != "")
			updateDefinedMeaningDefinition($definedMeaningId, $languageId, $text);
	}
}

class DefinedMeaningAlternativeDefinitionsController {
	public function add($keyPath, $record)  {
		global
			$expressionIdAttribute, $definedMeaningIdAttribute, $alternativeDefinitionAttribute, $languageAttribute, $textAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$alternativeDefinition = $record->getAttributeValue($alternativeDefinitionAttribute);

		if ($alternativeDefinition->getRecordCount() > 0) {
			$definitionRecord = $alternativeDefinition->getRecord(0);

			$languageId = $definitionRecord->getAttributeValue($languageAttribute);
			$text = $definitionRecord->getAttributeValue($textAttribute);

			if ($languageId != 0 && $text != '')
				addDefinedMeaningAlternativeDefinition($definedMeaningId, $languageId, $text, 0);
		}
	}

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $definitionIdAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$definitionId = $keyPath->peek(0)->getAttributeValue($definitionIdAttribute);
		removeDefinedMeaningAlternativeDefinition($definedMeaningId, $definitionId);
	}

	public function update($keyPath, $record) {
	}
}

class DefinedMeaningAlternativeDefinitionController implements Controller {
	public function add($keyPath, $record) {
		global
			$expressionIdAttribute, $definitionIdAttribute, $languageAttribute, $textAttribute;

		$definitionId = $keyPath->peek(0)->getAttributeValue($definitionIdAttribute);
		$languageId = $record->getAttributeValue($languageAttribute);
		$text = $record->getAttributeValue($textAttribute);

		if ($languageId != 0 && $text != "")
			addTranslatedTextIfNotPresent($definitionId, $languageId, $text);
	}

	public function remove($keyPath) {
		global
			$definitionIdAttribute, $languageAttribute;

		$definitionId = $keyPath->peek(1)->getAttributeValue($definitionIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);

		removeTranslatedText($definitionId, $languageId);
	}

	public function update($keyPath, $record) {
		global
			$definitionIdAttribute, $languageAttribute, $textAttribute;

		$definitionId = $keyPath->peek(1)->getAttributeValue($definitionIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		$text = $record->getAttributeValue($textAttribute);

		if ($text != "")
			updateTranslatedText($definitionId, $languageId, $text);
	}
}

class SynonymTranslationController implements Controller {
	public function add($keyPath, $record) {
		global
			$definedMeaningIdAttribute, $expressionAttribute, $languageAttribute, $spellingAttribute, $identicalMeaningAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$expressionRecord = $record->getAttributeValue($expressionAttribute);
		$languageId = $expressionRecord->getAttributeValue($languageAttribute);
		$spelling = $expressionRecord->getAttributeValue($spellingAttribute);
		$identicalMeaning = $record->getAttributeValue($identicalMeaningAttribute);

		if ($languageId != 0 && $spelling != '') {
			$expression = findOrCreateExpression($spelling, $languageId);
			$expression->assureIsBoundToDefinedMeaning($definedMeaningId, $identicalMeaning);
		}
	}

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $expressionIdAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$expressionId = $keyPath->peek(0)->getAttributeValue($expressionIdAttribute);
		removeSynonymOrTranslation($definedMeaningId, $expressionId);
	}

	public function update($keyPath, $record) {
		global
			$definedMeaningIdAttribute, $expressionIdAttribute, $identicalMeaningAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$expressionId = $keyPath->peek(0)->getAttributeValue($expressionIdAttribute);
		$identicalMeaning = $record->getAttributeValue($identicalMeaningAttribute);
		updateSynonymOrTranslation($definedMeaningId, $expressionId, $identicalMeaning);
	}
}

class DefinedMeaningRelationController implements Controller {
	public function add($keyPath, $record) {
		global
			$definedMeaningIdAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$relationTypeId = $record->getAttributeValue($relationTypeAttribute);
		$otherDefinedMeaningId = $record->getAttributeValue($otherDefinedMeaningAttribute);

		if ($relationTypeId != 0 && $otherDefinedMeaningId != 0)
			addRelation($definedMeaningId, $relationTypeId, $otherDefinedMeaningId);
	}

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$record = $keyPath->peek(0);
		$relationTypeId = $record->getAttributeValue($relationTypeAttribute);
		$otherDefinedMeaningId = $record->getAttributeValue($otherDefinedMeaningAttribute);

		removeRelation($definedMeaningId, $relationTypeId, $otherDefinedMeaningId);
	}

	public function update($keyPath, $record) {
	}
}

class DefinedMeaningClassMembershipController implements Controller {
	public function add($keyPath, $record) {
		global
			$definedMeaningIdAttribute, $classAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$classId = $record->getAttributeValue($classAttribute);

		if ($classId != 0)
			addClassMembership($definedMeaningId, $classId);
	}

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $classAttribute;
			
		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);	
		$classId = $keyPath->peek(0)->getAttributeValue($classAttribute);	

		removeClassMembership($definedMeaningId, $classId);
	}

	public function update($keyPath, $record) {
	}
}

class DefinedMeaningCollectionController implements Controller {
	public function add($keyPath, $record) {
		global
			$expressionIdAttribute, $definedMeaningIdAttribute, $collectionAttribute, $sourceIdentifierAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$collectionId = $record->getAttributeValue($collectionAttribute);
		$internalId = $record->getAttributeValue($sourceIdentifierAttribute);

		if ($collectionId != 0)
			addDefinedMeaningToCollectionIfNotPresent($definedMeaningId, $collectionId, $internalId);
	}

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $collectionAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$collectionId = $keyPath->peek(0)->getAttributeValue($collectionAttribute);

		removeDefinedMeaningFromCollection($definedMeaningId, $collectionId);
	}

	public function update($keyPath, $record) {
		global
			$definedMeaningIdAttribute, $collectionAttribute, $sourceIdentifierAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$collectionId = $keyPath->peek(0)->getAttributeValue($collectionAttribute);
		$sourceId = $record->getAttributeValue($sourceIdentifierAttribute);

//		if ($sourceId != "")
			updateDefinedMeaningInCollection($definedMeaningId, $collectionId, $sourceId);
	}
}

class ExpressionMeaningController implements Controller {
	public function add($keyPath, $record) {
		global
			$expressionIdAttribute, $definedMeaningAttribute, $definitionAttribute, $languageAttribute, $textAttribute;

		$definition = $record->getAttributeValue($definedMeaningAttribute)->getAttributeValue($definitionAttribute);

		if ($definition->getRecordCount() > 0) {
			$definitionRecord = $definition->getRecord(0);

			$text = $definitionRecord->getAttributeValue($textAttribute);
			$languageId = $definitionRecord->getAttributeValue($languageAttribute);

			if ($languageId != 0 && $text != "") {
				$expressionId = $keyPath->peek(0)->getAttributeValue($expressionIdAttribute);

				createNewDefinedMeaning($expressionId, $languageId, $text);
			}
		}
	}

	public function remove($keyPath) {
	}

	public function update($keyPath, $record) {
	}
}

class ExpressionController implements Controller {
	protected $spelling;

	public function __construct($spelling) {
		$this->spelling = $spelling;
	}

	public function add($keyPath, $record) {
		global
			$expressionAttribute, $expressionMeaningsAttribute, $definedMeaningAttribute, $definitionAttribute, $languageAttribute, $textAttribute;

		$expressionLanguageId = $record->getAttributeValue($expressionAttribute)->getAttributeValue($languageAttribute);
		$expressionMeanings = $record->getAttributeValue($expressionMeaningsAttribute);

		if ($expressionLanguageId != 0 && $expressionMeanings->getRecordCount() > 0) {
			$expressionMeaning = $expressionMeanings->getRecord(0);

			$definition = $expressionMeaning->getAttributeValue($definedMeaningAttribute)->getAttributeValue($definitionAttribute);

			if ($definition->getRecordCount() > 0) {
				$definitionRecord = $definition->getRecord(0);

				$text = $definitionRecord->getAttributeValue($textAttribute);
				$languageId = $definitionRecord->getAttributeValue($languageAttribute);

				if ($languageId != 0 && $text != "") {
					$expression = findOrCreateExpression($this->spelling, $expressionLanguageId);
					createNewDefinedMeaning($expression->id, $languageId, $text);
				}
			}
		}
	}

	public function remove($keyPath) {
	}

	public function update($keyPath, $record) {
	}
}

class DefinedMeaningTextAttributeValuesController {
	public function add($keyPath, $record)  {
		global
			$expressionIdAttribute, $definedMeaningIdAttribute, $textValueAttribute, $languageAttribute,
			$textAttribute, $textAttributeAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$textValue = $record->getAttributeValue($textValueAttribute);
		$textAttributeId = $record->getAttributeValue($textAttributeAttribute);

		if ($textAttributeId != 0 && $textValue->getRecordCount() > 0) {
			$textValueRecord = $textValue->getRecord(0);

			$languageId = $textValueRecord->getAttributeValue($languageAttribute);
			$text = $textValueRecord->getAttributeValue($textAttribute);

			if ($languageId != 0 && $text != '')
				addDefinedMeaningTextAttributeValue($definedMeaningId, $textAttributeId, $languageId, $text);
		}
	}

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $textAttributeAttribute, $textValueIdAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$attributeId = $keyPath->peek(0)->getAttributeValue($textAttributeAttribute);
		$textId = $keyPath->peek(0)->getAttributeValue($textValueIdAttribute);

		removeDefinedMeaningTextAttributeValue($definedMeaningId, $attributeId, $textId);
	}

	public function update($keyPath, $record) {
	}
}

class DefinedMeaningTextAttributeValueController implements Controller {
	public function add($keyPath, $record) {
		global
			$expressionIdAttribute, $textValueIdAttribute, $languageAttribute, $textAttribute;

		$textId = $keyPath->peek(0)->getAttributeValue($textValueIdAttribute);
		$languageId = $record->getAttributeValue($languageAttribute);
		$text = $record->getAttributeValue($textAttribute);

		if ($languageId != 0 && $text != "")
			addTranslatedTextIfNotPresent($textId, $languageId, $text);
	}

	public function remove($keyPath) {
		global
			$textValueIdAttribute, $languageAttribute;

		$textId = $keyPath->peek(1)->getAttributeValue($textValueIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);

		removeTranslatedText($textId, $languageId);
	}

	public function update($keyPath, $record) {
		global
			$textValueIdAttribute, $languageAttribute, $textAttribute;

		$textId = $keyPath->peek(1)->getAttributeValue($textValueIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		$text = $record->getAttributeValue($textAttribute);

		if ($text != "")
			updateTranslatedText($textId, $languageId, $text);
	}
}

class AlternativeDefinitionsPermissionController implements PermissionController {
	public function allowUpdateOfAttribute($attribute) {
		return true;	
	}
	
	public function allowUpdateOfValue($idPath, $value) {
		return $this->allowAnyChangeOfValue($value);
	}
	
	public function allowRemovalOfValue($idPath, $value) {
		return $this->allowAnyChangeOfValue($value);
	}

	protected function allowAnyChangeOfValue($value) {
		global
			$sourceAttribute;
			
		$source = $value->getAttributeValue($sourceAttribute);	
			
		return $source == null || $source == 0;
	}
}

?>
