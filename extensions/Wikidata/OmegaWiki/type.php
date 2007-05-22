<?php

require_once('languages.php');
require_once('forms.php');
require_once('Attribute.php');
require_once('Record.php');
require_once('Transaction.php');
require_once('WikiDataAPI.php');

require_once("Wikidata.php");
$wdDataSetContext=DefaultWikidataApplication::getDataSetContext();

function booleanAsText($value) {
	if ($value)
		return "Yes";
	else
		return "No";		
}

function booleanAsHTML($value) {
	if ($value)
		return '<input type="checkbox" checked="checked" disabled="disabled"/>';
	else
		return '<input type="checkbox" disabled="disabled"/>';
}

function pageAsURL($nameSpace, $title) {
	global
		$wgScript;
	
	return $wgScript. '/' . $nameSpace . ':' . htmlspecialchars($title);
}

function spellingAsURL($spelling) {
	return pageAsURL("Expression", $spelling);
}

function definedMeaningReferenceAsURL($definedMeaningId, $definingExpression) {
	return pageAsURL("DefinedMeaning", "$definingExpression ($definedMeaningId)");
}

function definedMeaningIdAsURL($definedMeaningId) {
	return definedMeaningReferenceAsURL($definedMeaningId, definingExpression($definedMeaningId));
}

function createLink($url, $text) {
	return '<a href="'. $url . '">' . htmlspecialchars($text) . '</a>';	
} 

function spellingAsLink($spelling) {
	return createLink(spellingAsURL($spelling), $spelling);
}

function definedMeaningReferenceAsLink($definedMeaningId, $definingExpression, $label) {
	return createLink(definedMeaningReferenceAsURL($definedMeaningId, $definingExpression), $label);
}

function languageIdAsText($languageId) {
	global $wgUser,$wgOwLanguageNames;
	return $wgOwLanguageNames[$languageId];
}

function collectionIdAsText($collectionId) {
	if ($collectionId > 0) 
		return definedMeaningExpression(getCollectionMeaningId($collectionId));
	else
		return "";
}

function timestampAsText($timestamp) {
	return
		substr($timestamp, 0, 4) . '-' . substr($timestamp, 4, 2) . '-' . substr($timestamp, 6, 2) . ' ' .
		substr($timestamp, 8, 2) . ':' . substr($timestamp, 10, 2) . ':' . substr($timestamp, 12, 2);
}

function definingExpressionRow($definedMeaningId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT {$dc}_expression_ns.expression_id, spelling, language_id " .
								" FROM {$dc}_defined_meaning, {$dc}_expression_ns " .
								" WHERE {$dc}_defined_meaning.defined_meaning_id=$definedMeaningId " .
								" AND {$dc}_expression_ns.expression_id={$dc}_defined_meaning.expression_id".
								" AND " . getLatestTransactionRestriction("{$dc}_defined_meaning").
								" AND " . getLatestTransactionRestriction("{$dc}_expression_ns"));
	$expression = $dbr->fetchObject($queryResult);
	return array($expression->expression_id, $expression->spelling, $expression->language_id); 
}

function definingExpression($definedMeaningId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT spelling " .
								" FROM {$dc}_defined_meaning, {$dc}_expression_ns " .
								" WHERE {$dc}_defined_meaning.defined_meaning_id=$definedMeaningId " .
								" AND {$dc}_expression_ns.expression_id={$dc}_defined_meaning.expression_id".
								" AND " . getLatestTransactionRestriction("{$dc}_defined_meaning").
								" AND " . getLatestTransactionRestriction("{$dc}_expression_ns"));
	$expression = $dbr->fetchObject($queryResult);
	return $expression->spelling; 
}

function definedMeaningExpressionForLanguage($definedMeaningId, $languageId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT spelling" .
		" FROM {$dc}_syntrans, {$dc}_expression_ns " .
		" WHERE defined_meaning_id=$definedMeaningId" .
		" AND {$dc}_expression_ns.expression_id={$dc}_syntrans.expression_id" .
		" AND {$dc}_expression_ns.language_id=$languageId" .
		" AND {$dc}_syntrans.identical_meaning=1" .
		" AND " . getLatestTransactionRestriction("{$dc}_syntrans") .
		" AND " . getLatestTransactionRestriction("{$dc}_expression_ns") .
		" LIMIT 1"
	);

	if ($expression = $dbr->fetchObject($queryResult))
		return $expression->spelling;
	else
		return "";
}

function definedMeaningExpressionForAnyLanguage($definedMeaningId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT spelling " .
		" FROM {$dc}_syntrans, {$dc}_expression_ns" .
		" WHERE defined_meaning_id=$definedMeaningId" .
		" AND {$dc}_expression_ns.expression_id={$dc}_syntrans.expression_id" .
		" AND {$dc}_syntrans.identical_meaning=1" .
		" AND " . getLatestTransactionRestriction("{$dc}_syntrans") .
		" AND " . getLatestTransactionRestriction("{$dc}_expression_ns") .
		" LIMIT 1");

	if ($expression = $dbr->fetchObject($queryResult))
		return $expression->spelling;
	else
		return "";
}

function definedMeaningExpression($definedMeaningId) {
	global
		$wgUser;
	
	$userLanguage = getLanguageIdForCode($wgUser->getOption('language'));
	
	list($definingExpressionId, $definingExpression, $definingExpressionLanguage) = definingExpressionRow($definedMeaningId);
	
	if ($definingExpressionLanguage == $userLanguage && expressionIsBoundToDefinedMeaning($definingExpressionId, $definedMeaningId))  
		return $definingExpression;
	else {	
		if ($userLanguage > 0)
			$result = definedMeaningExpressionForLanguage($definedMeaningId, $userLanguage);
		else
			$result = "";
		
		if ($result == "") {
			$result = definedMeaningExpressionForLanguage($definedMeaningId, 85);
			
			if ($result == "") {
				$result = definedMeaningExpressionForAnyLanguage($definedMeaningId);
				
				if ($result == "")
					$result = $definingExpression;
			}
		}
	}

	return $result;
}

function getTextValue($textId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT old_text from text where old_id=$textId");

	return $dbr->fetchObject($queryResult)->old_text; 
}

function definingExpressionAsLink($definedMeaningId) {
	return spellingAsLink(definingExpression($definedMeaningId));
}

function definedMeaningAsLink($definedMeaningId) {
	if ($definedMeaningId > 0) 
		return createLink(definedMeaningIdAsURL($definedMeaningId), definedMeaningExpression($definedMeaningId));
	else
		return "";
}

function collectionAsLink($collectionId) {
	return definedMeaningAsLink(getCollectionMeaningId($collectionId));
}

function convertToHTML($value, $type) {
	switch($type) {
		case "boolean": return booleanAsHTML($value);
		case "spelling": return spellingAsLink($value);
		case "collection": return collectionAsLink($value);
		case "defined-meaning": return definedMeaningAsLink($value);
		case "defining-expression": return definingExpressionAsLink($value);
		case "relation-type": return definedMeaningAsLink($value);
		case "attribute": return definedMeaningAsLink($value);
		case "language": return languageIdAsText($value);
		case "short-text":
		case "text": return htmlspecialchars($value);
		default: return htmlspecialchars($value);
	}
}

function getInputFieldForType($name, $type, $value) {
	switch($type) {
		case "language": return getLanguageSelect($name);
		case "spelling": return getTextBox($name, $value);
		case "boolean": return getCheckBox($name, $value);
		case "defined-meaning":
		case "defining-expression":
			return getSuggest($name, "defined-meaning");
		case "relation-type": return getSuggest($name, "relation-type");
		case "attribute": return getSuggest($name, "attribute");
		case "collection": return getSuggest($name, "collection");
		case "short-text": return getTextBox($name, $value);
		case "text": return getTextArea($name, $value);
	}	
}
function getInputFieldValueForType($name, $type) {
	global
		$wgRequest;
		
	switch($type) {
		case "language": return $wgRequest->getInt($name);
		case "spelling": return trim($wgRequest->getText($name));
		case "boolean": return $wgRequest->getCheck($name);
		case "defined-meaning": 
		case "defining-expression":
			return $wgRequest->getInt($name);
		case "relation-type": return $wgRequest->getInt($name);
		case "attribute": return $wgRequest->getInt($name);
		case "collection": return $wgRequest->getInt($name);
		case "short-text":
		case "text": return trim($wgRequest->getText($name));
	}
}

?>
