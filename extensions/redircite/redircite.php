<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * @author Roan Kattouw <roan.kattouw@home.nl>
 * @copyright Copyright (C) 2007 Roan Kattouw
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 *
 * An extension that allows for abbreviated inline citations.
 * Idea by Joe Beaudoin Jr. from The Great Machine wiki <http://tgm.firstones.com/>
 * Code by Roan Kattouw (AKA Catrope) <roan.kattouw@home.nl>
 * For information on how to install and use this extension, see the README file.
 *
 */

$wgExtensionFunctions[] = 'redircite_setup';
$wgExtensionCredits['other'][] = array(
	'name' => 'redircite',
	'author' => 'Roan Kattouw',
	'description' => 'Allows for abbreviated inline citations',
	'version' => '1.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Redircite',
);

function redircite_setup()
{
	global $wgParser, $wgHooks;
	$wgParser->setHook('redircite', 'redircite_render');
	$wgHooks['ParserAfterTidy'][] = 'redircite_afterTidy';
}

$markerList = array();
function redircite_render($input, $args, $parser)
{
	// Generate HTML code and add it to the $markerList array
	// Add "xx-redircite-marker-NUMBER-redircite-xx" to the output,
	// which will be translated to the HTML stored in $markerList by
	// redircite_afterTidy()
	global $markerList;
	$lparse = clone $parser;
	$link1 = $lparse->parse("[[$input]]", $parser->mTitle, $parser->mOptions, false, false);
	$link1text = $link1->getText();
	$title1 = Title::newFromText($input);
	if(!$title1) // Page doesn't exist
		// Just output a normal (red) link
		return $link1text;
	$articleObj = new Article($title1);
	$title2 = Title::newFromRedirect($articleObj->fetchContent());
	if(!$title2) // Page is not a redirect
		// Just output a normal link
		return $link1text;

	$link2 = $lparse->parse("[[{$title2->getPrefixedText()}|$input]]", $parser->mTitle, $parser->mOptions, false, false);
	$link2text = $link2->getText();

	$marker = "xx-redircite-marker-" . count($markerList) . "-redircite-xx";
	$markerList[] = "<span onmouseout='this.firstChild.innerHTML = \"$input\";' onmouseover='this.firstChild.innerHTML = \"{$title2->getPrefixedText()}\";'>$link2text</span>";
	return $marker;
}

function redircite_afterTidy(&$parser, &$text)
{
	// Translate the markers added by redircite_render() to the HTML
	// associated with them through $markerList
	global $markerList;
	foreach($markerList as $i => $output)
		$text = preg_replace("/xx-redircite-marker-$i-redircite-xx/", $output, $text);
	return true;
}
