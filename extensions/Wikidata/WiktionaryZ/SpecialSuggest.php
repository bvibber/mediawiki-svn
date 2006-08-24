<?php

define( 'MEDIAWIKI', true );

//if (!defined('MEDIAWIKI')) die();
//
//global 
//	$IP;
//
//require_once("$IP/includes/SpecialPage.php");
require_once("../../../LocalSettings.php");
require_once("Setup.php");

require_once("Attribute.php");
require_once("RecordSet.php");
require_once("Editor.php");
require_once("HTMLtable.php");
require_once("Expression.php");
require_once("Transaction.php");

//$wgExtensionFunctions[] = 'wfSpecialSuggest';
//
//function wfSpecialSuggest() {
//	class SpecialSuggest extends SpecialPage {
//		function SpecialSuggest() {
//			SpecialPage::SpecialPage('Suggest');
//		}
//		
//		function execute( $par ) {
//			global
//				$wgOut;
//				
//			$wgOut->disable();
//			echo getSuggestions();
//		}
//	}
//	
//	SpecialPage::addPage(new SpecialSuggest());
//}

echo getSuggestions();

function getSuggestions() {
	global
		$idAttribute;

	$search = ltrim($_GET['search']);
	$prefix = $_GET['prefix'];
	$query = $_GET['query'];
	
	$dbr =& wfGetDB( DB_SLAVE );
	$rowText = 'spelling';
	
	switch ($query) {
		case 'relation-type':
			$sql = getSQLForCollectionOfType('RELT');
			break;
		case 'attribute':
			$sql = getSQLForCollectionOfType('ATTR');
			break;
		case 'text-attribute':	
			$sql = getSQLForCollectionOfType('TATT');
			break;
		case 'language':
			$sql = "SELECT language_id AS row_id, language_name " .
					"FROM language_names " .
					"WHERE 1 ";
			$rowText = 'language_name';
			break;
		case 'defined-meaning':
			$sql = "SELECT syntrans.defined_meaning_id AS defined_meaning_id, expression.spelling AS spelling, expression.language_id AS language_id ".
					"FROM uw_expression_ns expression, uw_syntrans syntrans ".
	            	"WHERE expression.expression_id=syntrans.expression_id AND syntrans.endemic_meaning=1 " .
	            	" AND " . getLatestTransactionRestriction('syntrans');
	        break;	
	    case 'collection':
	    	$sql = "SELECT collection_id, spelling ".
	    			"FROM uw_expression_ns expression, uw_collection_ns collection, uw_syntrans syntrans ".
	    			"WHERE expression.expression_id=syntrans.expression_id AND syntrans.defined_meaning_id=collection.collection_mid ".
	    			"AND syntrans.endemic_meaning=1 AND " . getLatestTransactionRestriction('syntrans');
	    	break;
	}
	                          
	if ($search != '')
		$searchCondition = " AND $rowText LIKE " . $dbr->addQuotes("$search%");
	else
		$searchCondition = "";
	
	$sql .= $searchCondition . " ORDER BY $rowText LIMIT 10";
	$queryResult = $dbr->query($sql);
	$idAttribute = new Attribute("id", "ID", "id");
	
	switch($query) {
		case 'relation-type':
			list($relation, $editor) = getRelationTypeAsRelation($queryResult);
			break;		
		case 'attribute':
			list($relation, $editor) = getAttributeAsRelation($queryResult);
			break;
		case 'text-attribute':
			list($relation, $editor) = getTextAttributeAsRelation($queryResult);
			break;
		case 'defined-meaning':
			list($relation, $editor) = getDefinedMeaningAsRelation($queryResult);
			break;	
		case 'collection':
			list($relation, $editor) = getCollectionAsRelation($queryResult);
			break;	
		case 'language':
			list($relation, $editor) = getLanguageAsRelation($queryResult);
			break;
	}
	
	return getRelationAsSuggestionTable($editor, new IdStack($prefix .'table'), $relation);
}

function getSQLForCollectionOfType($collectionType) {
	return "SELECT member_mid, spelling, collection_mid " .
            "FROM uw_collection_contents, uw_collection_ns, uw_syntrans syntrans, uw_expression_ns expression " .
            "WHERE uw_collection_contents.collection_id=uw_collection_ns.collection_id and uw_collection_ns.collection_type='$collectionType' " .
            
            "AND syntrans.defined_meaning_id=uw_collection_contents.member_mid " .
            "AND expression.expression_id=syntrans.expression_id AND syntrans.endemic_meaning=1 ".
            "AND " . getLatestTransactionRestriction('syntrans') .
            "AND " . getLatestTransactionRestriction('uw_collection_contents');
}

function getRelationTypeAsRelation($queryResult) {
	global
		$idAttribute;
	
	$dbr =& wfGetDB(DB_SLAVE);
	
	$relationTypeAttribute = new Attribute("relation-type", "Relation type", "short-text");
	$collectionAttribute = new Attribute("collection", "Collection", "short-text");
	
	$relation = new ArrayRecordSet(new Structure($idAttribute, $relationTypeAttribute, $collectionAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) 
		$relation->addRecord(array($row->member_mid, $row->spelling, definedMeaningExpression($row->collection_mid)));			

	$editor = new RecordSetTableEditor(null, false, false, false, null);
	$editor->addEditor(new ShortTextEditor($relationTypeAttribute, false, false));
	$editor->addEditor(new ShortTextEditor($collectionAttribute, false, false));
	
	return array($relation, $editor);		
}

function getAttributeAsRelation($queryResult) {
	global
		$idAttribute;
	
	$dbr =& wfGetDB(DB_SLAVE);
	$attributeAttribute = new Attribute("attribute", "Attribute", "short-text");
	$collectionAttribute = new Attribute("collection", "Collection", "short-text");
	
	$relation = new ArrayRecordSet(new Structure($idAttribute, $attributeAttribute, $collectionAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) 
		$relation->addRecord(array($row->member_mid, $row->spelling, definedMeaningExpression($row->collection_mid)));

	$editor = new RecordSetTableEditor(null, false, false, false, null);
	$editor->addEditor(new ShortTextEditor($attributeAttribute, false, false));
	$editor->addEditor(new ShortTextEditor($collectionAttribute, false, false));

	return array($relation, $editor);		
}

function getTextAttributeAsRelation($queryResult) {
	global
		$idAttribute;
	
	$dbr =& wfGetDB(DB_SLAVE);
	$textAttributeAttribute = new Attribute("text-attribute", "Text attribute", "short-text");
	$collectionAttribute = new Attribute("collection", "Collection", "short-text");
	
	$relation = new ArrayRecordSet(new Structure($idAttribute, $textAttributeAttribute, $collectionAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) 
		$relation->addRecord(array($row->member_mid, $row->spelling, definedMeaningExpression($row->collection_mid)));			

	$editor = new RecordSetTableEditor(null, false, false, false, null);
	$editor->addEditor(new ShortTextEditor($textAttributeAttribute, false, false));
	$editor->addEditor(new ShortTextEditor($collectionAttribute, false, false));

	return array($relation, $editor);		
}

function getDefinedMeaningAsRelation($queryResult) {
	global
		$idAttribute;

	$dbr =& wfGetDB(DB_SLAVE);
	$spellingAttribute = new Attribute("spelling", "Spelling", "short-text");
	$languageAttribute = new Attribute("language", "Language", "language");
	
	$expressionStructure = new Structure($spellingAttribute, $languageAttribute);
	$definedMeaningAttribute = new Attribute("defined-meaning", "Defined meaning", new RecordType($expressionStructure));
	$definitionAttribute = new Attribute("definition", "Definition", "definition");
	
	$relation = new ArrayRecordSet(new Structure($idAttribute, $definedMeaningAttribute, $definitionAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) {
		$definedMeaningRecord = new ArrayRecord($expressionStructure);
		$definedMeaningRecord->setAttributeValue($spellingAttribute, $row->spelling);
		$definedMeaningRecord->setAttributeValue($languageAttribute, $row->language_id);
		
		$relation->addRecord(array($row->defined_meaning_id, $definedMeaningRecord, getDefinedMeaningDefinition($row->defined_meaning_id)));
	}			

	$definedMeaningEditor = new RecordTableCellEditor($definedMeaningAttribute);
	$definedMeaningEditor->addEditor(new ShortTextEditor($spellingAttribute, false, false));
	$definedMeaningEditor->addEditor(new LanguageEditor($languageAttribute, false, false));

	$editor = new RecordSetTableEditor(null, false, false, false, null);
	$editor->addEditor($definedMeaningEditor);
	$editor->addEditor(new TextEditor($definitionAttribute, false, false, true, 75));

	return array($relation, $editor);		
}

function getCollectionAsRelation($queryResult) {
	global
		$idAttribute;

	$dbr =& wfGetDB(DB_SLAVE);
	$collectionAttribute = new Attribute("collection", "Collection", "short-text");
	
	$relation = new ArrayRecordSet(new Structure($idAttribute, $collectionAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) 
		$relation->addRecord(array($row->collection_id, $row->spelling));			

	$editor = new RecordSetTableEditor(null, false, false, false, null);
	$editor->addEditor(new ShortTextEditor($collectionAttribute, false, false));

	return array($relation, $editor);		
}

function getLanguageAsRelation($queryResult) {
	global
		$idAttribute;

	$dbr =& wfGetDB(DB_SLAVE);
	$languageAttribute = new Attribute("language", "Language", "short-text");
	
	$relation = new ArrayRecordSet(new Structure($idAttribute, $languageAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) 
		$relation->addRecord(array($row->row_id, $row->language_name));			

	$editor = new RecordSetTableEditor(null, false, false, false, null);
	$editor->addEditor(new ShortTextEditor($languageAttribute, false, false));

	return array($relation, $editor);		
}

?>