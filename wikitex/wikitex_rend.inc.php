<?php
	# see _global.php
	$arrInc['rend'] = sprintf($arrInc['tex'], sprintf('%s.%%s', sprintf($arrMod['mod'], $arrMod['rend'])));
	$arrMod['bash'] = sprintf('%%s%s.sh %%s %%s %%s', $strNamespace);	# ./wikitex.sh FILE MODULE OUTPATH
	$arrDir['bash'] = preg_replace('/(.*\/).*/', '$1', $_SERVER['SCRIPT_FILENAME']);
	
	# expansible
	$arrDef['music']['style']['tempo']	= 60;

	# Out-tag Gestalt; see wikitex.inc.php.  Parameters of form %parm%.
	$arrTag['rend']		= '<img src="%src%" alt="%alt%" title="%alt%" />';
	$arrTag['music']	= '<a href="%midi%"><img src="%src%" alt="%alt%" title="%alt%" style="border: none" /></a>';

	$arrRend['rend']	= '%%%s%%';
	$arrRend['style']	= '%%style:%s%%';
	
	$arrErr['rend']		= 'WikiTeX: directive non gratum.';
	$arrErr['class']	= 'WikiTeX: unknown scheme ``%s\'\'.';
	$arrErr['noclass']	= 'WikiTeX: missing scheme.';
	
	$arrBlack['rend']			= array(	# base for all classes
		'\catcode',
		'\include',
		'\includeonly',
		'\input',
#		'\newcommand',
		'\newenvironment',
		'\newtheorem',
		'\newfont',
		'\renewcommand',
		'\renewenvironment',
		'\typein',
		'\typeout',
		'\write'
		);

	$arrBlack['music']	= array(
		'#'
		);
?>