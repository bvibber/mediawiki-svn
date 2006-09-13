<?php

require_once('languages.php');
require_once('forms.php');
require_once('Attribute.php');
require_once('Record.php');

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

function spellingAsLink($value) {
	global
		$wgUser;
		
//	return $wgUser->getSkin()->makeLink("WiktionaryZ:$value", htmlspecialchars($value));
	return createLink("WiktionaryZ", $value, $value);
}

function createLink($nameSpace, $tag, $text) {
	global
		$wgUser, $wgScript;
		
	return '<a href="'. $wgScript. '/' . $nameSpace . ':' . $tag . '">' . htmlspecialchars($text) . '</a>';	
//	return $wgUser->getSkin()->makeLink("$nameSpace:$tag", htmlspecialchars($text));
} 

function languageIdAsText($languageId) {
	global
		$wgLanguageNames;	

	return $wgLanguageNames[$languageId];
}

function definingExpression($definedMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT spelling, language_id " .
								" FROM uw_defined_meaning, uw_expression_ns " .
								" WHERE uw_defined_meaning.defined_meaning_id=$definedMeaningId " .
								" AND uw_expression_ns.expression_id=uw_defined_meaning.expression_id".
								" AND " . getLatestTransactionRestriction('uw_defined_meaning'));
	$expression = $dbr->fetchObject($queryResult);
	return array($expression->spelling, $expression->language_id); 
}

function definedMeaningExpressionForLanguage($definedMeaningId, $languageId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT spelling FROM uw_syntrans, uw_expression_ns " .
								"WHERE defined_meaning_id=$definedMeaningId AND uw_expression_ns.expression_id=uw_syntrans.expression_id AND uw_expression_ns.language_id=$languageId AND uw_syntrans.endemic_meaning=1 LIMIT 1");

	if ($expression = $dbr->fetchObject($queryResult))
		return $expression->spelling;
	else
		return "";
}

function definedMeaningExpressionForAnyLanguage($definedMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT spelling from uw_syntrans, uw_expression_ns where defined_meaning_id=$definedMeaningId and uw_expression_ns.expression_id=uw_syntrans.expression_id and uw_syntrans.endemic_meaning=1 limit 1");

	if ($expression = $dbr->fetchObject($queryResult))
		return $expression->spelling;
	else
		return "";
}

function definedMeaningExpression($definedMeaningId) {
	global
		$wgUser;
	
	$userLanguage = getLanguageIdForCode($wgUser->getOption('language'));
	
	list($definingExpression, $definingExpressionLanguage) = definingExpression($definedMeaningId);

	if ($definingExpressionLanguage == $userLanguage)  
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
	list($definingExpression, $definingExpressionLanguage) = definingExpression($definedMeaningId);
	return spellingAsLink($definingExpression);
}

function definedMeaningAsLink($definedMeaningId) {
	global
		$wgUser;

	if ($definedMeaningId > 0) {
		$prettyDefinedMeaningExpression = definedMeaningExpression($definedMeaningId);
		list($definingExpression, $definingExpressionLanguage) = definingExpression($definedMeaningId);
		
		return createLink("DefinedMeaning", "$definingExpression ($definedMeaningId)", $prettyDefinedMeaningExpression);
	}
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
