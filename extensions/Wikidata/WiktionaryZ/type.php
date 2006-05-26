<?php

require_once('languages.php');

function booleanAsText($value) {
	if ($value)
		return "Yes";
	else
		return "No";		
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
		case "boolean": return booleanAsText($value);
		case "spelling": return spellingAsLink($value);
		case "language": return languageIdAsText($value);
		default: return $value;
	}
}

?>
