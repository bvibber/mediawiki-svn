<?php

/*
WikiTeX: expansible LaTeX module for MediaWiki
Copyright (C) 2004  Peter Danenberg

     WikiTeX  is  free  software;  you  can  redistribute it
and/or modify it under the terms of the GNU  General  Public
License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later ver-
sion.

wikitex.inc.php: initialization of wikitex.php

*/

// an abstract method of declaration; too much C.  Rebukes indeed the "loose"
// virtues of PHP.
settype($arrErr,	'array');
settype($strErr,	'string');
settype($arrRend,	'array');
settype($strRendPath,	'string');

// base dir
$strRendPath = "$IP/extensions/wikitex";

// Set class names; which may be customized based on local language, fashion
// or whim.  Henry V was a maker of these when he was courting Gallrix Kate.
$arrRend = array ('batik'	=> 'strBatik',
		  'chem'	=> 'strChem',
		  'chess'	=> 'strChess',
		  'feyn'	=> 'strFeyn',
		  'go'		=> 'strGo',
		  'greek'	=> 'strGreek',
		  'ling'	=> 'strLing',
		  'math'	=> 'strMath',
		  'music'	=> 'strMusic',
		  'svg'		=> 'strSVG',
		  'teng'	=> 'strTeng',
		  'tipa'	=> 'strTipa',
		  'xym'		=> 'strXym');

// liberal Latin in errorous dicta
$arrErr['rend']		= 'WikiTeX: directive non gratum.';
$arrErr['class']	= 'WikiTeX: unknown scheme ``%s\'\'.';

// the stem of errorous givings-out
$strErr	= "$strRendPath/wikitex.error.inc.tex";
	
?>
