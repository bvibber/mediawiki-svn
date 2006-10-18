<?php

if (!defined('MEDIAWIKI')) die();

$wgExtensionFunctions[] = 'wfSpecialSuggest';

function wfSpecialSuggest() {
	class SpecialSuggest extends SpecialPage {
		function SpecialSuggest() {
			SpecialPage::SpecialPage('Suggest');
		}
		
		function execute( $par ) {
			global
				$wgOut,	$IP;
				
			$wgOut->disable();
			
			require_once("$IP/includes/Setup.php");
			require_once("Attribute.php");
			require_once("RecordSet.php");
			require_once("Editor.php");
			require_once("HTMLtable.php");
			require_once("Expression.php");
			require_once("Transaction.php");			
			echo getSuggestions();
		}
	}
	
	SpecialPage::addPage(new SpecialSuggest());
}

function getSuggestions() {
	global
		$idAttribute;

	$search = ltrim($_GET['search-text']);
	$prefix = $_GET['prefix'];
	$query = $_GET['query'];
	
	$dbr =& wfGetDB( DB_SLAVE );
	$rowText = 'spelling';
	
	switch ($query) {
		case 'relation-type':
			$sql = getSQLForCollectionOfType('RELT');
			break;
		case 'class':
			$sql = getSQLForCollectionOfType('CLAS');
			break;
		case 'text-attribute':	
			$sql = getSQLForCollectionOfType('TATT');
			break;
		case 'language':
			$sql = getSQLForLanguage();
			$rowText = 'language_name';
			break;
		case 'defined-meaning':
			$sql = "SELECT syntrans.defined_meaning_id AS defined_meaning_id, expression.spelling AS spelling, expression.language_id AS language_id ".
					"FROM uw_expression_ns expression, uw_syntrans syntrans ".
	            	"WHERE expression.expression_id=syntrans.expression_id AND syntrans.identical_meaning=1 " .
	            	" AND " . getLatestTransactionRestriction('syntrans').
	            	" AND " . getLatestTransactionRestriction('expression');
	        break;	
	    case 'collection':
	    	$sql = "SELECT collection_id, spelling ".
	    			"FROM uw_expression_ns expression, uw_collection_ns collection, uw_syntrans syntrans ".
	    			"WHERE expression.expression_id=syntrans.expression_id AND syntrans.defined_meaning_id=collection.collection_mid ".
	    			"AND syntrans.identical_meaning=1" .
	    			" AND " . getLatestTransactionRestriction('syntrans') .
	    			" AND " . getLatestTransactionRestriction('expression') .
	    			" AND " . getLatestTransactionRestriction('collection');
	    	break;
	    case 'transaction':
	    	$sql = "SELECT transaction_id, user_id, user_ip, " .
	    			" CONCAT(SUBSTRING(timestamp, 1, 4), '-', SUBSTRING(timestamp, 5, 2), '-', SUBSTRING(timestamp, 7, 2), ' '," .
	    			" SUBSTRING(timestamp, 9, 2), ':', SUBSTRING(timestamp, 11, 2), ':', SUBSTRING(timestamp, 13, 2)) AS time, comment" .
	    			" FROM transactions WHERE 1";
	    	$rowText = "CONCAT(SUBSTRING(timestamp, 1, 4), '-', SUBSTRING(timestamp, 5, 2), '-', SUBSTRING(timestamp, 7, 2), ' '," .
	    			" SUBSTRING(timestamp, 9, 2), ':', SUBSTRING(timestamp, 11, 2), ':', SUBSTRING(timestamp, 13, 2))";
	    	break;
	}
	                          
	if ($search != '') {
		if ($query == 'transaction')
			$searchCondition = " AND $rowText LIKE " . $dbr->addQuotes("%$search%");
		else if ($query == 'language')
			$searchCondition = " HAVING $rowText LIKE " . $dbr->addQuotes("$search%");
		else	
			$searchCondition = " AND $rowText LIKE " . $dbr->addQuotes("$search%");
	}
	else
		$searchCondition = "";
	
	if ($query == 'transaction')
		$orderBy = 'transaction_id DESC';
	else
		$orderBy = $rowText;
	
	$sql .= $searchCondition . " ORDER BY $orderBy LIMIT 10";
	$queryResult = $dbr->query($sql);
	$idAttribute = new Attribute("id", "ID", "id");
	
	switch($query) {
		case 'relation-type':
			list($recordSet, $editor) = getRelationTypeAsRecordSet($queryResult);
			break;		
		case 'class':
			list($recordSet, $editor) = getClassAsRecordSet($queryResult);
			break;
		case 'text-attribute':
			list($recordSet, $editor) = getTextAttributeAsRecordSet($queryResult);
			break;
		case 'defined-meaning':
			list($recordSet, $editor) = getDefinedMeaningAsRecordSet($queryResult);
			break;	
		case 'collection':
			list($recordSet, $editor) = getCollectionAsRecordSet($queryResult);
			break;	
		case 'language':
			list($recordSet, $editor) = getLanguageAsRecordSet($queryResult);
			break;
		case 'transaction':
			list($recordSet, $editor) = getTransactionAsRecordSet($queryResult);
			break;
	}
	
	return getRelationAsSuggestionTable($editor, new IdStack($prefix .'table'), $recordSet);
}

function getSQLForCollectionOfType($collectionType) {
	return "SELECT member_mid, spelling, collection_mid " .
            "FROM uw_collection_contents, uw_collection_ns, uw_syntrans syntrans, uw_expression_ns expression " .
            "WHERE uw_collection_contents.collection_id=uw_collection_ns.collection_id and uw_collection_ns.collection_type='$collectionType' " .
            
            "AND syntrans.defined_meaning_id=uw_collection_contents.member_mid " .
            "AND expression.expression_id=syntrans.expression_id AND syntrans.identical_meaning=1 ".
            "AND " . getLatestTransactionRestriction('syntrans') .
            "AND " . getLatestTransactionRestriction('expression') .
            "AND " . getLatestTransactionRestriction('uw_collection_contents');
}

function getSQLForLanguage() {
	global
		$wgUser;
	
	$userLanguage = $wgUser->getOption('language');

	if ($userLanguage == 'en')
		return "SELECT language.language_id AS row_id,language_names.language_name " .
			"FROM language " .
			"JOIN language_names ON language.language_id = language_names.language_id " .
			"WHERE language_names.name_language_id = " . getLanguageIdForCode('en');
	else
		return "SELECT language.language_id AS row_id,COALESCE(ln1.language_name,ln2.language_name) AS language_name " .
			"FROM language " .
			"LEFT JOIN language_names AS ln1 ON language.language_id = ln1.language_id AND ln1.name_language_id = " . getLanguageIdForCode($userLanguage) . " " .
			"JOIN language_names AS ln2 ON language.language_id = ln2.language_id AND ln2.name_language_id = " . getLanguageIdForCode('en');
}

function getRelationTypeAsRecordSet($queryResult) {
	global
		$idAttribute;
	
	$dbr =& wfGetDB(DB_SLAVE);
	
	$relationTypeAttribute = new Attribute("relation-type", "Relation type", "short-text");
	$collectionAttribute = new Attribute("collection", "Collection", "short-text");
	
	$recordSet = new ArrayRecordSet(new Structure($idAttribute, $relationTypeAttribute, $collectionAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) 
		$recordSet->addRecord(array($row->member_mid, $row->spelling, definedMeaningExpression($row->collection_mid)));			

	$editor = new RecordSetTableEditor(null, new SimplePermissionController(false), false, false, false, null);
	$editor->addEditor(new ShortTextEditor($relationTypeAttribute, new SimplePermissionController(false), false));
	$editor->addEditor(new ShortTextEditor($collectionAttribute, new SimplePermissionController(false), false));
	
	return array($recordSet, $editor);		
}

function getClassAsRecordSet($queryResult) {
	global
		$idAttribute;
	
	$dbr =& wfGetDB(DB_SLAVE);
	$classAttribute = new Attribute("class", "Class", "short-text");
	$collectionAttribute = new Attribute("collection", "Collection", "short-text");
	
	$recordSet = new ArrayRecordSet(new Structure($idAttribute, $classAttribute, $collectionAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) 
		$recordSet->addRecord(array($row->member_mid, $row->spelling, definedMeaningExpression($row->collection_mid)));

	$editor = new RecordSetTableEditor(null, new SimplePermissionController(false), false, false, false, null);
	$editor->addEditor(new ShortTextEditor($classAttribute, new SimplePermissionController(false), false));
	$editor->addEditor(new ShortTextEditor($collectionAttribute, new SimplePermissionController(false), false));

	return array($recordSet, $editor);		
}

function getTextAttributeAsRecordSet($queryResult) {
	global
		$idAttribute;
	
	$dbr =& wfGetDB(DB_SLAVE);
	$textAttributeAttribute = new Attribute("text-attribute", "Text attribute", "short-text");
	$collectionAttribute = new Attribute("collection", "Collection", "short-text");
	
	$recordSet = new ArrayRecordSet(new Structure($idAttribute, $textAttributeAttribute, $collectionAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) 
		$recordSet->addRecord(array($row->member_mid, $row->spelling, definedMeaningExpression($row->collection_mid)));			

	$editor = new RecordSetTableEditor(null, new SimplePermissionController(false), false, false, false, null);
	$editor->addEditor(new ShortTextEditor($textAttributeAttribute, new SimplePermissionController(false), false));
	$editor->addEditor(new ShortTextEditor($collectionAttribute, new SimplePermissionController(false), false));

	return array($recordSet, $editor);		
}

function getDefinedMeaningAsRecordSet($queryResult) {
	global
		$idAttribute;

	$dbr =& wfGetDB(DB_SLAVE);
	$spellingAttribute = new Attribute("spelling", "Spelling", "short-text");
	$languageAttribute = new Attribute("language", "Language", "language");
	
	$expressionStructure = new Structure($spellingAttribute, $languageAttribute);
	$definedMeaningAttribute = new Attribute("defined-meaning", "Defined meaning", new RecordType($expressionStructure));
	$definitionAttribute = new Attribute("definition", "Definition", "definition");
	
	$recordSet = new ArrayRecordSet(new Structure($idAttribute, $definedMeaningAttribute, $definitionAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) {
		$definedMeaningRecord = new ArrayRecord($expressionStructure);
		$definedMeaningRecord->setAttributeValue($spellingAttribute, $row->spelling);
		$definedMeaningRecord->setAttributeValue($languageAttribute, $row->language_id);
		
		$recordSet->addRecord(array($row->defined_meaning_id, $definedMeaningRecord, getDefinedMeaningDefinition($row->defined_meaning_id)));
	}			

	$expressionEditor = new RecordTableCellEditor($definedMeaningAttribute);
	$expressionEditor->addEditor(new ShortTextEditor($spellingAttribute, new SimplePermissionController(false), false));
	$expressionEditor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), false));

	$editor = new RecordSetTableEditor(null, new SimplePermissionController(false), false, false, false, null);
	$editor->addEditor($expressionEditor);
	$editor->addEditor(new TextEditor($definitionAttribute, new SimplePermissionController(false), false, true, 75));

	return array($recordSet, $editor);		
}

function getCollectionAsRecordSet($queryResult) {
	global
		$idAttribute;

	$dbr =& wfGetDB(DB_SLAVE);
	$collectionAttribute = new Attribute("collection", "Collection", "short-text");
	
	$recordSet = new ArrayRecordSet(new Structure($idAttribute, $collectionAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) 
		$recordSet->addRecord(array($row->collection_id, $row->spelling));			

	$editor = new RecordSetTableEditor(null, new SimplePermissionController(false), false, false, false, null);
	$editor->addEditor(new ShortTextEditor($collectionAttribute, new SimplePermissionController(false), false));

	return array($recordSet, $editor);		
}

function getLanguageAsRecordSet($queryResult) {
	global
		$idAttribute;

	$dbr =& wfGetDB(DB_SLAVE);
	$languageAttribute = new Attribute("language", "Language", "short-text");
	
	$recordSet = new ArrayRecordSet(new Structure($idAttribute, $languageAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) 
		$recordSet->addRecord(array($row->row_id, $row->language_name));			

	$editor = new RecordSetTableEditor(null, new SimplePermissionController(false), false, false, false, null);
	$editor->addEditor(new ShortTextEditor($languageAttribute, new SimplePermissionController(false), false));

	return array($recordSet, $editor);		
}

function getTransactionAsRecordSet($queryResult) {
	global
		$idAttribute, $userAttribute;
	
	$dbr =& wfGetDB(DB_SLAVE);
	
	$timestampAttribute = new Attribute("timestamp", "Time", "timestamp");
	$summaryAttribute = new Attribute("summary", "Summary", "short-text");
	
	$recordSet = new ArrayRecordSet(new Structure($idAttribute, $userAttribute, $timestampAttribute, $summaryAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) 
		$recordSet->addRecord(array($row->transaction_id, getUserLabel($row->user_id, $row->user_ip), $row->time, $row->comment));			
	
	$editor = new RecordSetTableEditor(null, new SimplePermissionController(false), false, false, false, null);
	$editor->addEditor(new ShortTextEditor($timestampAttribute, new SimplePermissionController(false), false));
	$editor->addEditor(new ShortTextEditor($idAttribute, new SimplePermissionController(false), false));
	$editor->addEditor(new ShortTextEditor($userAttribute, new SimplePermissionController(false), false));
	$editor->addEditor(new ShortTextEditor($summaryAttribute, new SimplePermissionController(false), false));

	return array($recordSet, $editor);		
}

?>