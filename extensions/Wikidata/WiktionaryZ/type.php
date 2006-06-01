<?php

require_once('languages.php');

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
		case "language": return languageIdAsText($value);
		default: return $value;
	}
}

?>
