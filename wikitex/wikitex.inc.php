<?php
	/* WikiTeX: expansible LaTeX module for MediaWiki
	Copyright (C) 2004  Peter Danenberg
	
	WikiTeX is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version. */
	
	settype($arrElement,	'array');
	settype($arrTag,		'array');

	$arrElement['rend'] = 'blockinline';
	
	$arrTag['inline']		= '\<%s([^>]*)\/\>';
	$arrTag['block']		= '\<%s([^>]*)\>(.*)\<\/%s\>';
	$arrTag['blockinline']	= '\<%s([^>]*)(?:\/\>|\>(.*)\<\/%s\>)';
?>
