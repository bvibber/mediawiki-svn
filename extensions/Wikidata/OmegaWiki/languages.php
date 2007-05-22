<?php

function getOwLanguageNames() {
	global $wgUser;
	$owLanguageNames = getLangNames($wgUser->getOption('language'));
	return $owLanguageNames;
}

/* Return an array containing all language names translated into the language
	indicated by $code, with fallbacks in English where the language names
	aren't present in that language. */
function getLangNames($code) {
	$dbr = &wfGetDB(DB_SLAVE);
	$names = array();
	$sql = getSQLForLanguageNames($code);
	$lang_res = $dbr->query($sql);
	while ($lang_row = $dbr->fetchObject($lang_res))
		$names[$lang_row->row_id] = $lang_row->language_name;
	return $names;
}

function getLanguageIdForCode($code) {
	$dbr =& wfGetDB( DB_SLAVE );
	$id_res=$dbr->query("select language_id from language where wikimedia_key='".$code."'");
	$id_row=$dbr->fetchObject($id_res);
	return $id_row->language_id;
}

/* Return SQL query string for fetching language names. */
function getSQLForLanguageNames($lang_code) {
	/* Use a simpler query if the user's language is English. */
	/* Use a simpler query if the user's language is English. */
	if ($lang_code == 'en' || !($lang_id = getLanguageIdForCode($lang_code)))
		return 'SELECT language.language_id AS row_id,language_names.language_name' .
			' FROM language' .
			' JOIN language_names ON language.language_id = language_names.language_id' .
			' WHERE language_names.name_language_id = ' . getLanguageIdForCode('en');
	/* Fall back on English in cases where a language name is not present in the
		user's preferred language. */
	else
		return 'SELECT language.language_id AS row_id,COALESCE(ln1.language_name,ln2.language_name) AS language_name' .
			' FROM language' .
			' LEFT JOIN language_names AS ln1 ON language.language_id = ln1.language_id AND ln1.name_language_id = ' . $lang_id .
			' JOIN language_names AS ln2 ON language.language_id = ln2.language_id AND ln2.name_language_id = ' . getLanguageIdForCode('en');
}

function getLanguageIdForName($name) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT language_id FROM language_names WHERE language_name=".$dbr->addQuotes($name));
	
	if ($languageId = $dbr->fetchObject($queryResult))
		return $languageId->language_id;
	else
		return 0;	
}

?>
