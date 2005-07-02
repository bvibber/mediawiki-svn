<?php

/**
 * This file contains the main include file for the Inputbox extension of 
 * MediaWiki. 
 *
 * Usage: require_once("path/to/inputbox.php" in LocalSettings.php
 *
 * @author Erik Moeller <moeller@scireview.de>
 * @copyright Public domain
 * @license Public domain
 * @package MediaWikiExtensions
 * @version 0.1
 */
 
/**
 * Register the Inputbox extension with MediaWiki
 */ 
$wgExtensionFunctions[] = 'registerInputboxExtension';

/**
 * Sets the tag that this extension looks for and the function by which it
 * operates
 */
function registerInputboxExtension()
{
    global $wgParser;
    $wgParser->setHook('inputbox', 'renderInputbox');
}


/**
 * Renders an inputbox based on information provided by $input.
 */
function renderInputbox($input)
{
	getBoxOption($type,$input,"type");	
	getBoxOption($width,$input,"width");	
	if($type=="search") {	
		$inputbox=getSearchForm($width);
	} elseif($type=="create") {
		$inputbox=getCreateForm($width);
	}
	if(isset($inputbox)) {
		return $inputbox;
	} else {
		# Careful with HTML insertions here
		return "<br /> <font color='red'>Input box not defined.</font>";
	}
}

function getSearchForm($width=45) {

	global $wgArticlePath,$wgUser;
	#$searchpath=str_replace('$1','Special:Search',$wgArticlePath);
	$sk=$wgUser->getSkin();
	$searchpath=$sk->escapeSearchLink();
	$tryexact=wfMsg('tryexact');
	$searchfulltext=wfMsg('searchfulltext');
	$searchform=<<<ENDFORM
<table border="0" width="100%">
<tr>
<td align="center">
<form name="searchbox" action="$searchpath" id="searchbox">
	<input id="searchboxInput" name="search" type="text"
	value="" size="$width"/><br />
	<input type='submit' name="go" class="searchboxGoButton" id="searchboxGoButton"
	value="$tryexact"
	/>&nbsp;<input type='submit' name="fulltext"
	class="searchboxSearchButton"
	value="$searchfulltext" />
</form>
</td>
</tr>
</table>
ENDFORM;
	return $searchform;
}

function getCreateForm($width=45) {
	
	global $wgScript;
	$editpath="{$wgScript}?action=edit";
	$createarticle=wfMsg("createarticle");
	$createform=<<<ENDFORM
<table border="0" width="100%">
<tr>
<td align="center">
<form name="createbox" action="$editpath" method="post" id="createbox">
	<input id="createboxInput" name="title" type="text"
	value="" size="$width"/><br />
	<input type='submit' name="create" class="createboxButton" id="createboxButton"
	value="$createarticle"/>
</form>
</td>
</tr>
</table>
ENDFORM;
	return $createform;
}

function getBoxOption(&$value,&$input,$name) {

	if(preg_match("/$name\s*=\s*(.*)/mi",$input,$matches)) {
		$value=$matches[1];
	} 
}
