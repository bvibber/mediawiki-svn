<?php

global
	$wgLanguageNames, $wgUser;

$wgLanguageNames = getLangNames($wgUser->getOption('language'));

# Falls back to English if no language name translations available for chosen languages
function getLangNames($code) {
	$id = getLanguageIdForCode($code);
	
	if(!$id) 
		$id = getLanguageIdForCode('en');
		
	$names = getLanguageNamesForId($id);
	
	if (empty($names)) {
		$id = getLanguageIdForCode('en');
		$names = getLanguageNamesForId($id);
	}
	
	return $names;
}

function getLanguageIdForCode($code) {
	$dbr =& wfGetDB( DB_SLAVE );
	$id_res=$dbr->query("select language_id from language where wikimedia_key='".$code."'");
	$id_row=$dbr->fetchObject($id_res);
	return $id_row->language_id;
}

function getLanguageNamesForId($id) {
	$dbr =& wfGetDB( DB_SLAVE );
	$langs = array();
	$lang_res = $dbr->query("select language_names.language_id,language_names.language_name,language.wikimedia_key from language,language_names where language_names.name_language_id=".$id." and language.language_id=language_names.name_language_id");
	while($lang_row=$dbr->fetchObject($lang_res)) {
		$langs[$lang_row->language_id]=$lang_row->language_name;
	}
	return $langs;
}

?>
