<?php
/* vim: noet ts=4 sw=4
 * http://www.gnu.org/licenses/gpl-3.0.txt */

if (!defined("MEDIAWIKI"))
	die();

/* ---- CREDITS ---- */

$wgExtensionCredits['other'][] = array(
	'name'        => "Uniwiki Format Search",
	'author'      => "Merrick Schaefer, Mark Johnston, Evan Wheeler and Adam Mckaig (at UNICEF)",
	'description' => "Minor changes to clean up the search results page"
);

/* ---- HOOKS ---- */

$wgHooks['BeforePageDisplay'][] = "UW_FormatSearch_CSS";
function UW_FormatSearch_CSS (&$out) {
	global $wgScriptPath;
	$href = "$wgScriptPath/extensions/uniwiki/FormatSearch/style.css";
	$out->addScript ("<link rel='stylesheet' href='$href' />");
	return true;
}
