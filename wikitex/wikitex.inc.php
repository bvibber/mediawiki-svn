<?php

/*
WikiTeX: expansible LaTeX module for MediaWiki
Copyright (C) 2004-5  Peter Danenberg

     WikiTeX is licensed under  the  Open  Software  License
v. 2.1;  to  view  a  copy  of  this license, see COPYING or
visit:

     http://www.opensource.org/licenses/osl-2.1.php

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
$arrRend = array ('amsmath'	=> 'strMath',
		  'batik'	=> 'strBatik',
		  'chem'	=> 'strXym',
		  'chess'	=> 'strChess',
		  'feyn'	=> 'strFeyn',
		  'go'		=> 'strGo',
		  'greek'	=> 'strGreek',
		  'graph'	=> 'strGraph',
		  'ling'	=> 'strLing',
		  'music'	=> 'strMusic',
		  'plot'	=> 'strPlot',
		  'ppch'	=> 'strPPCH',
		  'schem'	=> 'strSchem',
		  'svg'		=> 'strSVG',
		  'teng'	=> 'strTeng',
		  'tipa'	=> 'strTipa');

// liberal Latin in errorous dicta
$arrErr['rend']	= 'directive non gratum.';
$arrErr['bash'] = '<span class="errwikitex">WikiTeX: wikitex.sh is not executable.</span>';

// the stem of errorous givings-out
$strErr	= "$strRendPath/wikitex.error.inc.tex";
	
?>
