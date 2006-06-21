<?php

require_once('languages.php');

function getTextBox($name, $maximumLength = 255, $value = "") {
	return '<input type="text" name="'. $name .'" value="'. $value .'" maxlength="'. $maximumLength .'"/>';
}
 
function getTextArea($name, $text = "", $rows = 5, $columns = 80) {
	return '<textarea name="'.$name. '" rows="'. $rows . '" cols="'. $columns . '">' . htmlspecialchars($text) . '</textarea>';	
}

function checkBoxCheckAttribute($isChecked) {
	if ($isChecked)
		return ' checked="checked"';
	else
		return '';	
}
 
function getCheckBox($name, $isChecked) {
	return '<input type="checkbox" name="'. $name .'"'. checkBoxCheckAttribute($isChecked) . '/>';
}

function getCheckBoxWithOnClick($name, $isChecked, $onClick) {
	return '<input type="checkbox" name="'. $name .'"'. checkBoxCheckAttribute($isChecked) . ' onclick="'. $onClick .'"/>';
}

function getRemoveCheckBox($name) {
	return getCheckBoxWithOnClick($name, false, "removeClicked(this);");
}
  
# $options is an array of [value => text] pairs
function getSelect($name, $options, $selectedValue="") {
	$result = '<select name="'. $name . '">';	  
 
	asort($options);

	foreach($options as $value => $text) {
		if ($value == $selectedValue)
			$selected = ' selected="selected"';
		else
			$selected = '';
			
		$result .= '<option value="'. $value .'"'. $selected .'>'. htmlspecialchars($text) . '</option>';
	}	

	return $result . '</select>';
}

function getSuggest($name, $query) {
	global
		$dbr;

	$result = '<div class="suggest"><input type="hidden" id="'. $name .'-suggest-query" value="'. $query .'"/><input type="hidden" id="'. $name .'" name="'. $name .'" value=""/><a id="'. $name .'-suggest-link" class="suggest-link" href="#'. $name .'-suggest-div" onclick="suggestLinkClicked(event, this);" title="Click to change selection">No selection</a></div>'.
               '<div style="position: relative"><div id="'. $name .'-suggest-div" style="position: absolute; left: 0px; top: 0px; border: 1px solid #000000; display: none; background-color: white; padding: 4px">' .
               '<div><table><tr><td><input type="text" id="'. $name .'-suggest-text" autocomplete="off" onkeyup="suggestTextChanged(this)" style="width: 300px"></input></td><td><a id="'. $name .'-suggest-close" href="#'. $name . 'suggest-link" onclick="suggestCloseClicked(this)">[X]</a></td></tr></table></div>' .
               '<div><table id="'. $name .'-suggest-table"><tr><td></td></tr></table></div>'.
               '</div></div>';
	
	return $result;
}

function getLanguageOptions($languageIdsToExclude = array()) {
	global 
		$wgUser;
		
	$userLanguage = $wgUser->getOption('language');
	$idNameIndex = getLangNames($userLanguage);
	
	$result = array();
	
	foreach($idNameIndex as $id => $name) 
		if (!in_array($id, $languageIdsToExclude)) 
			$result[$id] = $name;
	
	return $result;
}
	
function getLanguageSelect($name, $languageIdsToExclude = array()) {
	global 
		$wgUser;
		
	$userLanguage = $wgUser->getOption('language');
	$userLanguageId = getLanguageIdForCode($userLanguage);

	return getSelect($name, getLanguageOptions($languageIdsToExclude), $userLanguageId);
}

function getSubmitButton($name, $value) {
	return '<input type="submit" name="'. $name .'" value="'. $value .'"/>'; 	
}
 
?>
