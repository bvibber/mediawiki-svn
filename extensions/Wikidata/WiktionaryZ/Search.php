<?php

require_once("Transaction.php");
require_once("RecordSet.php");
require_once("Editor.php");
require_once("Expression.php");


class Search {
	function Search() {
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
		$wgOut->addHTML($this->searchText($spelling));
	}
	
	function getLanguageSelector() {
		global $wgUser;
		$userlang=$wgUser->getOption('language');
		$skin = $wgUser->getSkin();
		return wfMsg('wz_uilang',"<b>$userlang</b>").  " &mdash; " . $skin->makeLink("Special:Preferences", wfMsg('wz_uilang_set'));
	}
	
	function searchText($text) {
		$dbr =& wfGetDB( DB_SLAVE );
		
		$sql = "SELECT INSTR(LCASE(uw_expression_ns.spelling), LCASE(". $dbr->addQuotes("$text") .")) as position, uw_syntrans.defined_meaning_id AS defined_meaning_id, uw_expression_ns.spelling AS spelling, uw_expression_ns.language_id AS language_id ".
				"FROM uw_expression_ns, uw_syntrans ".
	            "WHERE uw_expression_ns.expression_id=uw_syntrans.expression_id AND uw_syntrans.endemic_meaning=1 " .
	            " AND " . getLatestTransactionRestriction('uw_syntrans').
				" AND spelling LIKE " . $dbr->addQuotes("%$text%") .
				" ORDER BY position ASC, uw_expression_ns.spelling ASC limit 100";
		
		$queryResult = $dbr->query($sql);
		list($relation, $editor) = getDefinedMeaningAsRelation($queryResult);
//		return $sql;
		return $editor->view(new IdStack("expression"), $relation);
	}
}

function getDefinedMeaningAsRelation($queryResult) {
	global
		$idAttribute;

	$dbr =& wfGetDB(DB_SLAVE);
	$spellingAttribute = new Attribute("spelling", "Spelling", "short-text");
	$languageAttribute = new Attribute("language", "Language", "language");
	
	$expressionStructure = new Structure($languageAttribute, $spellingAttribute);
	$expressionAttribute = new Attribute("expression", "Expression", new RecordType($expressionStructure));
	$definitionAttribute = new Attribute("definition", "Definition", "definition");
	
	$relation = new ArrayRecordSet(new Structure($idAttribute, $expressionAttribute, $definitionAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) {
		$expressionRecord = new ArrayRecord($expressionStructure);
		$expressionRecord->setAttributeValue($spellingAttribute, $row->spelling);
		$expressionRecord->setAttributeValue($languageAttribute, $row->language_id);
		
		$relation->addRecord(array($row->defined_meaning_id, $expressionRecord, getDefinedMeaningDefinition($row->defined_meaning_id)));
	}			

	$expressionEditor = new RecordTableCellEditor($expressionAttribute);
	$expressionEditor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), false));
	$expressionEditor->addEditor(new SpellingEditor($spellingAttribute, new SimplePermissionController(false), false));

	$editor = new RecordSetTableEditor(null, new SimplePermissionController(false), false, false, false, null);
	$editor->addEditor($expressionEditor);
	$editor->addEditor(new TextEditor($definitionAttribute, new SimplePermissionController(false), false, true, 75));

	return array($relation, $editor);		
}

?>
