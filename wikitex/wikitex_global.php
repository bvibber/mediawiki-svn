<?php
#	error_reporting(E_ALL);
	
	settype($arrDir,		'array');	# directory catalog
	settype($arrInc,		'array');	# include catalog
	settype($arrMod,		'array');	# namespaces
	settype($arrDef,		'array');	# defaults (useful for #implied of DTD)
	settype($arrRend,		'array');	# parsing tokens
	settype($arrBlack,		'array');	# function blacklists
	settype($arrErr,		'array');	# error messages
	settype($strNamespace,	'string');
	
	$arrDir['rel']	= 'wikitex/';
	$arrDir['temp']	= vsprintf('%s%s', array(
		$arrDir['rel'],
		'tmp/'
		));

	$arrDir['out']	= vsprintf('%s/%s', array(
		$wgScriptPath,
		$arrDir['temp']
		));

	# basis for invocation of includes:
	$strNamespace	= vsprintf('%s%s', array(
		$arrDir['rel'],
		'wikitex'
		));
	
	$arrInc['html']	= '%s.inc.htm';
	$arrInc['tex']	= '%s.inc.tex';
	$arrInc['php']	= '%s.inc.php';
	$arrInc['mod']	= sprintf('%s_%%s.php', $strNamespace);	# modules
	
	$arrMod['rend']		= 'rend';
	$arrMod['error']	= 'error';
	$arrMod['style']	= 'style';
	$arrMod['mod']		= sprintf('%s_%%s', $strNamespace);	# modules' modules
	
	function vDeb($obj)
	{
		printf('<pre>%s</pre>', htmlentities(print_r($obj, true), ENT_QUOTES));
	}
?>