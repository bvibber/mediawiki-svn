<?php
/* vim: noet ts=4 sw=4
 * http://www.mediawiki.org/wiki/Extension:Uniwiki_Javascript
 * http://www.gnu.org/licenses/gpl-3.0.txt */

if (!defined('MEDIAWIKI'))
	die();


$wgExtensionCredits['other'][] = array(
	'name'        => "Uniwiki Javascript",
	'author'      => "Merrick Schaefer, Mark Johnston, Evan Wheeler and Adam Mckaig (at UNICEF)",
	'description' => "Adds uniwiki.js to each page (which currently just provides lightweight i18n support in Javascript), and serves as a placeholder for future Javascript code shared between Uniwiki extensions"
);


$wgHooks['BeforePageDisplay'][] = "UW_Javascript_addJS";
function UW_Javascript_addJS($out) {
	global $wgScriptPath;
	$src = "$wgScriptPath/extensions/uniwiki/Javascript/uniwiki.js";
	$out->addScript ("<script type='text/javascript' src='$src'></script>");
	return true;
}

