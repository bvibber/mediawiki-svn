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

settype($arrErr,	'array');
settype($strErr,	'string');

$arrErr['rend']		= 'WikiTeX: directive non gratum.';
$arrErr['class']	= 'WikiTeX: unknown scheme ``%s\'\'.';

$strErr	= "$strRendPath/wikitex.error.inc.tex";
	
?>
