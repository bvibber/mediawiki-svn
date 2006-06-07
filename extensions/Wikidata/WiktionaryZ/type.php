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

function convertToHTML($value, $type) {
	switch($type) {
		case "boolean": return booleanAsHTML($value);
		case "spelling": return spellingAsLink($value);
		case "defined-meaning": return spellingAsLink($value);
		case "relation-type": return spellingAsLink($value);
		case "attribute": return spellingAsLink($value);
		case "language": return languageIdAsText($value);
		default: return $value;
	}
}

function getInputFieldForType($name, $type, $value) {
	switch($type) {
		case "language": return getLanguageSelect($name);
		case "spelling": return getTextBox($name);
		case "boolean": return getCheckBox($name, true);
		case "defined-meaning": return getSuggest($name, "defined-meaning");
		case "relation-type": return getSuggest($name, "relation-type");
		case "attribute": return getSuggest($name, "attribute");
	}	
}

function getFieldValueForType($name, $type) {
	global
		$wgRequest;
		
	switch($type) {
		case "language": return $wgRequest->getInt($name);
		case "spelling": return trim($wgRequest->getText($name));
		case "boolean": return $wgRequest->getCheck($name);
		case "defined-meaning": return $wgRequest->getInt($name);
		case "relation-type": return $wgRequest->getInt($name);
		case "attribute": return $wgRequest->getInt($name);
	}
}


?>
