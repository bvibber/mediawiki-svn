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
	$rowText = 'expression1.spelling';
	
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
			$sql = "SELECT syntrans1.defined_meaning_id AS row_id, expression1.spelling AS relation, expression1.language_id AS language_id ".
					"FROM uw_expression_ns expression1, uw_syntrans syntrans1 ".
	            	"WHERE expression1.expression_id=syntrans1.expression_id ";
	        break;	
	    case 'collection':
	    	$sql = "SELECT collection.collection_id AS row_id, expression1.spelling AS relation ".
	    			"FROM uw_expression_ns expression1, uw_collection_ns collection, uw_syntrans syntrans ".
	    			"WHERE expression1.expression_id=syntrans.expression_id AND syntrans.defined_meaning_id=collection.collection_mid ".
	    			"AND collection.is_latest=1 AND syntrans.is_latest_set=1 AND expression1.is_latest=1 ";
	    	break;
	}
	                          
	if ($search != '')
		$searchCondition = "AND $rowText LIKE " . $dbr->addQuotes("$search%");
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
	return "SELECT member_mid AS row_id, expression1.spelling AS relation, expression2.spelling AS collection " .
            "FROM uw_collection_contents, uw_collection_ns, uw_syntrans syntrans1, uw_expression_ns expression1, uw_syntrans syntrans2, uw_expression_ns expression2 " .
            "WHERE uw_collection_contents.collection_id=uw_collection_ns.collection_id and uw_collection_ns.collection_type='$collectionType' " .
            
            "AND syntrans1.defined_meaning_id=uw_collection_contents.member_mid " .
            "AND expression1.expression_id=syntrans1.expression_id and expression1.language_id=85 " .
            
            "AND syntrans2.defined_meaning_id=uw_collection_ns.collection_mid " .
            "AND expression2.expression_id=syntrans2.expression_id and expression2.language_id=85 " .

			"AND uw_collection_contents.is_latest_set=1 ";
}

function getRelationTypeAsRelation($queryResult) {
	global
		$idAttribute;
	
	$dbr =& wfGetDB(DB_SLAVE);
	
	$relationTypeAttribute = new Attribute("relation-type", "Relation type", "short-text");
	$collectionAttribute = new Attribute("collection", "Collection", "short-text");
	
	$relation = new ArrayRecordSet(new Structure($idAttribute, $relationTypeAttribute, $collectionAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) 
		$relation->addRecord(array($row->row_id, $row->relation, $row->collection));			

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
		$relation->addRecord(array($row->row_id, $row->relation, $row->collection));			

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
		$relation->addRecord(array($row->row_id, $row->relation, $row->collection));			

	$editor = new RecordSetTableEditor(null, false, false, false, null);
	$editor->addEditor(new ShortTextEditor($textAttributeAttribute, false, false));
	$editor->addEditor(new ShortTextEditor($collectionAttribute, false, false));

	return array($relation, $editor);		
}

function getDefinedMeaningAsRelation($queryResult) {
	global
		$idAttribute;

	$dbr =& wfGetDB(DB_SLAVE);
	$definedMeaningAttribute = new Attribute("defined-meaning", "Defined meaning", "short-text");
	$languageAttribute = new Attribute("language", "Language", "language");
	$definitionAttribute = new Attribute("definition", "Definition", "definition");
	
	$relation = new ArrayRecordSet(new Structure($idAttribute, $definedMeaningAttribute, $languageAttribute, $definitionAttribute), new Structure($idAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) 
		$relation->addRecord(array($row->row_id, $row->relation, $row->language_id, getDefinedMeaningDefinition($row->row_id)));			

	$editor = new RecordSetTableEditor(null, false, false, false, null);
	$editor->addEditor(new ShortTextEditor($definedMeaningAttribute, false, false));
	$editor->addEditor(new LanguageEditor($languageAttribute, false, false));
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
		$relation->addRecord(array($row->row_id, $row->relation));			

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