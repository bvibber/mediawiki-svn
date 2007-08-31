<?php
/**
 * An extension that shows interwiki tabs above Image: pages
 * Original code by Joe Beaudoin Jr. from www.battlestarwiki.org (joe(AT)frakmedia(DOT)net)
 * Modified for more generic usage by Roan Kattouw (AKA Catrope) (roan(DOT)kattouw(AT)home(DOT)nl)
 * For information how to install and use this extension, see the README file.
 *
 * Copyright (C) Joe Beaudoin Jr. and Roan Kattouw 2007
 */

$wgExtensionFunctions[] = 'createImageTabs_setup';
$wgExtensionCredits['other'][] = array(
	'name' => 'Imagetabs',
	'author' => 'Joe Beaudoin Jr. and Roan Kattouw',
	'description' => 'Adds tabs with interwiki links above Image: pages',
	'version' => '1.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Imagetabs'
);

function createImageTabs_setup()
{
	global $wgHooks;
	$wgHooks['SkinTemplateContentActions'][] = 'createImageTabs_hook';
}

function createImageTabs_hook(&$content_actions)
{
	global $wgEnableInterwikiImageTabs, $wgInterwikiImageTabs, $wgTitle, $wgLocalInterwiki;
	if($wgEnableInterwikiImageTabs && $wgTitle->getNamespace() == NS_IMAGE)
	{
		$i = 0;
		foreach($wgInterwikiImageTabs as $prefix => $caption)
		{
			// Go to prefix:Image:title. Image: is automatically translated if necessary.
			$titleObj = Title::newFromText($prefix . ":Image:" . $wgTitle->getText());
			// Check that we don't link to ourselves
			if($titleObj->getInterwiki() != $wgLocalInterwiki && $titleObj->getFullURL() != $wgTitle->getFullURL())
				$content_actions['interwikitab-'.$i++] = array(
					'class' => false,
					'text' => $caption,
					'href' => $titleObj->getFullURL()
				);
		}
	}
	return true;
}

?>