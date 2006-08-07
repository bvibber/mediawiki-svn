<?php

require_once('languages.php');

function getTextBox($name, $value = "", $maximumLength = 255) {
	return '<input type="text" name="'. $name .'" value="'. $value .'" maxlength="'. $maximumLength .'" style="width: 100%; padding: 0px; margin: 0px;"/>';
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
	// href="#'. $name .'-suggest-div" 
	//$result = '<span class="suggest"><input type="hidden" id="'. $name .'-suggest-query" value="'. $query .'"/><input type="hidden" id="'. $name .'" name="'. $name .'" value=""/><a id="'. $name .'-suggest-link" class="suggest-link" onclick="suggestLinkClicked(event, this);" title="Click to change selection"><table class="suggest-table" cellspacing="0" cellpadding="0"><tr><td id="'. $name .'-suggest-value" style="width: 100%; padding-left: 2px; padding-right: 4px;"/><td style="margin: 0px; padding: 1px;"><img src="extensions/Wikidata/Images/ArrowButtonDown.png"/></td></tr></table></a></span>';
	$result = '<span class="suggest">' .
					'<input type="hidden" id="'. $name .'-suggest-query" value="'. $query .'"/>' .
					'<input type="hidden" id="'. $name .'" name="'. $name .'" value=""/>' .
					'<a id="'. $name .'-suggest-link" class="suggest-link" onclick="suggestLinkClicked(event, this);" title="Click to change selection">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
//						'<span class="suggest-box">' .
//							'<span class="suggest-value" id="'. $name .'-suggest-value">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>' .
////							'<span class="suggest-button"><input type="image" src="extensions/Wikidata/Images/ArrowButtonDown.png"/></span>' .
//							'<span class="suggest-button"><img src="extensions/Wikidata/Images/ArrowButtonDown.png"/></span>' .
//						'</span>' .
//							'<span class="suggest-value" id="'. $name .'-suggest-value">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>' .
					'</a>' .
//				'<select id="'. $name .'-suggest-link" class="suggest-link" onclick="suggestLinkClicked(event, this);" size="0"></select>'.
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
 
?>
