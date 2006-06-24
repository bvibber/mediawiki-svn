<?php

define( 'MEDIAWIKI', true );

require_once('../../../LocalSettings.php');
require_once("Setup.php");
require_once("attribute.php");
require_once("relation.php");

//$wgExtensionFunctions[] = 'wfSpecialSuggest';
//
//function wfSpecialSuggest() {
//	global 
//		$IP, $wgMessageCache, $wgContLang, $wgContLanguageCode, $wgOut;
//
////	$dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
////	$code = $wgContLang->lc( $wgContLanguageCode );
////	$file = file_exists( "${dir}cite_text-$code" ) ? "${dir}cite_text-$code" : "${dir}cite_text";
//	
//	require_once "$IP/includes/SpecialPage.php";
//
//	class SpecialSuggest extends SpecialPage {
//		function SpecialSuggest() {
//			SpecialPage::SpecialPage('Suggest');
//		}
//		
//		function execute( $par ) {
//			echo "Test";
//		}
//	}
//	
//	SpecialPage::addPage(new SpecialSuggest());
//}

$search = ltrim($_GET['search']);
$prefix = $_GET['prefix'];
$query = $_GET['query'];

$dbr =& wfGetDB( DB_SLAVE );

if ($search != '')
	$searchCondition = "AND expression1.spelling LIKE " . $dbr->addQuotes("$search%");
else
	$searchCondition = "";

switch ($query) {
	case 'relation-type':
		$sql = getSQLForCollectionOfType('RELT');
		break;
	case 'attribute':
		$sql = getSQLForCollectionOfType('ATTR');
		break;
	case 'defined-meaning':
		$sql = "SELECT syntrans1.defined_meaning_id AS row_id, expression1.spelling AS relation ".
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
                          
$sql .= $searchCondition . " ORDER BY expression1.spelling LIMIT 10";
$queryResult = $dbr->query($sql);
$idAttribute = new Attribute("id", "ID", "id");
$sourceRelation = new ArrayRelation(new Heading($idAttribute), new Heading($idAttribute));

switch($query) {
	case 'relation-type':
		$displayRelation = getRelationTypeAsRelation($queryResult, $sourceRelation);
		break;		
	case 'attribute':
		$displayRelation = getAttributeAsRelation($queryResult, $sourceRelation);
		break;
	case 'defined-meaning':
		$displayRelation = getDefinedMeaningAsRelation($queryResult, $sourceRelation);
		break;	
	case 'collection':
		$displayRelation = getCollectionAsRelation($queryResult, $sourceRelation);
		break;	
}

echo(getRelationAsSuggestionTable($prefix .'table', $sourceRelation, $displayRelation));

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

function getRelationTypeAsRelation($queryResult, $sourceRelation) {
	$dbr =& wfGetDB( DB_SLAVE );
	$relationTypeAttribute = new Attribute("relation-type", "Relation type", "short-text");
	$collectionAttribute = new Attribute("collection", "Collection", "short-text");
	
	$displayRelation = new ArrayRelation(new Heading($relationTypeAttribute, $collectionAttribute), new Heading($relationTypeAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) {
		$sourceRelation->addTuple(array($row->row_id));
		$displayRelation->addTuple(array($row->relation, $row->collection));			
	}

	return $displayRelation;		
}

function getAttributeAsRelation($queryResult, $sourceRelation) {
	$dbr =& wfGetDB( DB_SLAVE );
	$attributeAttribute = new Attribute("attribute", "Attribute", "short-text");
	$collectionAttribute = new Attribute("collection", "Collection", "short-text");
	
	$displayRelation = new ArrayRelation(new Heading($attributeAttribute, $collectionAttribute), new Heading($attributeAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) {
		$sourceRelation->addTuple(array($row->row_id));
		$displayRelation->addTuple(array($row->relation, $row->collection));			
	}

	return $displayRelation;		
}

function getDefinedMeaningAsRelation($queryResult, $sourceRelation) {
	$dbr =& wfGetDB( DB_SLAVE );
	$definedMeaningAttribute = new Attribute("defined-meaning", "Defined meaning", "short-text");
	
	$displayRelation = new ArrayRelation(new Heading($definedMeaningAttribute), new Heading($definedMeaningAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) {
		$sourceRelation->addTuple(array($row->row_id));
		$displayRelation->addTuple(array($row->relation));			
	}

	return $displayRelation;		
}

function getCollectionAsRelation($queryResult, $sourceRelation) {
	$dbr =& wfGetDB( DB_SLAVE );
	$collectionAttribute = new Attribute("collection", "Collection", "short-text");
	
	$displayRelation = new ArrayRelation(new Heading($collectionAttribute), new Heading($collectionAttribute));
	
	while ($row = $dbr->fetchObject($queryResult)) {
		$sourceRelation->addTuple(array($row->row_id));
		$displayRelation->addTuple(array($row->relation));			
	}

	return $displayRelation;		
}
?>
