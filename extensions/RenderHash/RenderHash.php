<?php

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'RenderHash',
	'version' => '1.1',
	'author' => 'Brion Vibber',
	'url' => 'http://www.mediawiki.org/wiki/Extension:RenderHash',
	'description' => 'Render Hash',
);

$wgHooks['PageRenderingHash'][] = 'renderHashAppend';
$wgRenderHashAppend = '';

/**
 * Hook to append a configured value to the hash, so that parser cache
 * storage can be kept separate for some class of activity.
 *
 * @param string $hash in-out parameter; user's page rendering hash
 * @return bool true to continue, false to abort operation
 */
function renderHashAppend( &$hash ) {
	global $wgRenderHashAppend;
	$hash .= $wgRenderHashAppend;
	return true;
}
