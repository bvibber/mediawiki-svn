<?php

require_once('languages.php');

function getTextBox($name, $value = "", $maximumLength = 255) {
	return '<input type="text" id="'. $name .'" name="'. $name .'" value="'. $value .'" maxlength="'. $maximumLength .'" style="width: 100%; padding: 0px; margin: 0px;"/>';
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

function getSuggest($name, $query, $value=0, $label='') {
	if ($label == "")
		$label = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	 
	$result = 
		'<span class="suggest">' .
			'<input type="hidden" id="'. $name .'-suggest-query" value="'. $query .'"/>' .
			'<input type="hidden" id="'. $name .'" name="'. $name .'" value="'. $value .'"/>' .
			'<a id="'. $name .'-suggest-link" class="suggest-link" onclick="suggestLinkClicked(event, this);" title="Click to change selection">' . $label . '</a>' .
		'</span>'.
        '<div class="suggest-drop-down" style="position: relative"><div id="'. $name .'-suggest-div" style="position: absolute; left: 0px; top: 0px; border: 1px solid #000000; display: none; background-color: white; padding: 4px">' .
        	'<div><table><tr><td><input type="text" id="'. $name .'-suggest-text" autocomplete="off" onkeyup="suggestTextChanged(this)" style="width: 300px"></input></td><td><a id="'. $name .'-suggest-clear" href="#'. $name . '-suggest-link" onclick="suggestClearClicked(event, this)">Clear</a></td><td><a id="'. $name .'-suggest-close" href="#'. $name . '-suggest-link" onclick="suggestCloseClicked(event, this)">[X]</a></td></tr></table></div>' .
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

function getOptionPanel($fields, $action = '') {
	global 
		$wgTitle;

	$result = 
		'<div class="option-panel">' .
			'<form method="GET" action="">' .
				'<table cellpadding="0" cellspacing="0">' .
					'<input type="hidden" name="title" value="' . $wgTitle->getNsText() . ':' . $wgTitle->getText() . '"/>';

	if ($action && $action != '')
		$result .= '<input type="hidden" name="action" value="' . $action . '"/>';

	foreach($fields as $caption => $field) 
		$result .= '<tr><th>' . $caption . ':</th><td class="option-field">' . $field . '</td></tr>';

	$result .=
					'<tr><th/><td>' . getSubmitButton("show","Show") . '</td></tr>' .
				'</table>' .
			'</form>' .
		'</div>';
		
	return $result;
}
?>
