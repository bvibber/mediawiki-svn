<?php
	settype($arrElement,	'array');
	settype($arrTag,		'array');

	$arrElement['rend'] = 'blockinline';
	
	$arrTag['inline']		= '\<%s([^>]*)\/\>';
	$arrTag['block']		= '\<%s([^>]*)\>(.*)\<\/%s\>';
	$arrTag['blockinline']	= '\<%s([^>]*)(?:\/\>|\>(.*)\<\/%s\>)';
?>
