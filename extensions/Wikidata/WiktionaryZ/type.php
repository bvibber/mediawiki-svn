<?php

require_once('languages.php');
require_once('forms.php');

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
		
	return $wgUser->getSkin()->makeLink("WiktionaryZ:$value", $value);
} 

function languageIdAsText($languageId) {
	global
		$wgLanguageNames;	

	return $wgLanguageNames[$languageId];
}

function definingExpression($definedMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT spelling from uw_defined_meaning, uw_expression_ns where uw_defined_meaning.defined_meaning_id=$definedMeaningId and uw_expression_ns.expression_id=uw_defined_meaning.expression_id and uw_defined_meaning.is_latest_ver=1 and uw_expression_ns.is_latest=1");
	
	while ($spelling = $dbr->fetchObject($queryResult))
		$result = $spelling->spelling; 
		
	return $result;
}

function definedMeaningExpression($definedMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT spelling from uw_syntrans, uw_expression_ns where defined_meaning_id=$definedMeaningId and uw_expression_ns.expression_id=uw_syntrans.expression_id and uw_expression_ns.language_id=85 limit 1");
	$expression = $dbr->fetchObject($queryResult);
	
	return $expression->spelling;
}

function definingExpressionAsLink($definedMeaningId) {
	return spellingAsLink(definingExpression($definedMeaningId));
}

function definedMeaningAsLink($definedMeaningId) {
	return spellingAsLink(definedMeaningExpression($definedMeaningId));
}

function convertToHTML($value, $type) {
	switch($type) {
		case "boolean": return booleanAsHTML($value);
		case "spelling": return spellingAsLink($value);
		case "defined-meaning": return definedMeaningAsLink($value);
		case "defining-expression": return definingExpressionAsLink($value);
		case "relation-type": return definedMeaningAsLink($value);
		case "attribute": return definedMeaningAsLink($value);
		case "language": return languageIdAsText($value);
		default: return $value;
	}
}

function getInputFieldsForAttribute($namePrefix, $attribute, $value) {
	switch($attribute->type) {
		case "language": return array(getLanguageSelect($namePrefix . $attribute->id));
		case "spelling": return array(getTextBox($namePrefix . $attribute->id));
		case "boolean": return array(getCheckBox($namePrefix . $attribute->id, true));
		case "expression": return array(getLanguageSelect($namePrefix . "language"), getTextBox($namePrefix . "spelling"));
		case "defined-meaning":
		case "defining-expression":
			return array(getSuggest($namePrefix . $attribute->id, "defined-meaning"));
		case "relation-type": return array(getSuggest($namePrefix . $attribute->id, "relation-type"));
		case "attribute": return array(getSuggest($namePrefix . $attribute->id, "attribute"));
		default: return array();
	}	
}

function getFieldValuesForAttribute($namePrefix, $attribute, $namePostFix) {
	global
		$wgRequest;
		
	switch($attribute->type) {
		case "language": return array($wgRequest->getInt($namePrefix . $attribute->id . $namePostFix));
		case "spelling": return array(trim($wgRequest->getText($namePrefix . $attribute->id . $namePostFix)));
		case "boolean": return array($wgRequest->getCheck($namePrefix . $attribute->id . $namePostFix));
		case "expression": return array($wgRequest->getInt($namePrefix . "language" . $namePostFix), trim($wgRequest->getText($namePrefix . "spelling" . $namePostFix)));
		case "defined-meaning": 
		case "defining-expression":
			return array($wgRequest->getInt($namePrefix . $attribute->id . $namePostFix));
		case "relation-type": return array($wgRequest->getInt($namePrefix . $attribute->id . $namePostFix));
		case "attribute": return array($wgRequest->getInt($namePrefix . $attribute->id . $namePostFix));
	}
}


?>
