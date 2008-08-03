<?php

/**
* CustomToolbox plugin for the GroupPermissionsManager extension
* This requires the GroupPermissionsManager extension to function, as well as MediaWiki version 1.13+
* Licensed under the GPL
*/

if(!defined('MEDIAWIKI')) {
	echo("This file is an extension to the MediaWiki software and is not a valid access point");
	die(1);
}

//MediaWiki version incompatibility check
if(!versionCheck('1.13')) {
	return;
}

$wgExtensionCredits['other'][] = array(
	'name'           => 'Custom Toolbox',
	'author'         => 'Ryan Schmidt',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:GroupPermissionsManager',
	'version'        => '1.0',
	'description'    => 'Allows adding additional items to the Toolbox',
	'descriptionmsg' => 'grouppermissions-desc4',
);

$wgHooks['SkinTemplateToolboxEnd'][] = 'efGPManagerCustomToolboxAppend';

function efGPManagerCustomToolboxAppend(&$skin) {
	global $wgOut;
	$tb = explode("\n", wfMsg('toolbox_append'));
	$new = array();
	foreach($tb as &$nt) {
		if(strpos('*', $nt) === 0) {
			$nt = trim($nt, '*');
			$parts = explode('|', $nt);
			foreach($parts as &$part)
				$part = trim($part);
			$href = wfMsgForContent($nt[0]);
			$text = wfMsgForContent($nt[1]);
			$perm = array_key_exists(2, $nt) ? $nt[2] : 'read';
			if(!$wgUser->isAllowed($perm))
				continue;
			if(wfEmptyMsg($nt[0], $href))
				$href = $nt[0];
			if(wfEmptyMsg($nt[1], $text))
				$text = $nt[1];
			$id = Santizer::escapeId($nt[1]);
			$new[] = array($href, $text, $id);
		}
	}
	foreach($new as $t) {
		$wgOut->addHTML('<li id="t-'.$t[2].'"><a href="'.htmlspecialchars($t[0]).'">'.$t[1].'</a></li>');
	}
	return true;
}