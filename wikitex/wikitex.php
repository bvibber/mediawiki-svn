<?php
	/* WikiTeX: expansible LaTeX module for MediaWiki
	Copyright (C) 2004  Peter Danenberg
	
	WikiTeX is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version. */

	include('wikitex_global.php');				# ground initialization
	include(sprintf($arrInc['php'], $strNamespace));	# implementation specific
	include(sprintf($arrInc['mod'], $arrMod['rend']));	# module specific
	
	class objArrContent
	{
		function arrRend($str)
		{
			# Parse the attributes for an element of the form <element class attribute: value "content" />;
			# on analogy with classic SGML: class, style, content.

			settype($arr,		'array');
			settype($arrStyle,	'array');
			settype($arrAttrib,	'array');
			
			preg_match('/\"([^"]+)\"/s', $str, $arr['value']);
			preg_match('/\s*(\w+)/', $str, $arr['class']);
			preg_match_all('/\s*(\w+)\s*\:\s*([\w\s]*)\s*(?=\w+\:|\"|$)/Us', $str, $arr['style'], PREG_SET_ORDER);
			
			$arrAttrib['value'] = $arr['value'][1];	# array to string conversion
			$arrAttrib['class'] = $arr['class'][1];	# array to string conversion
			foreach($arr['style'] as $arrStyle) {
				$arrAttrib['style'][trim($arrStyle[1])] = trim($arrStyle[2]);
			}
			
			return $arrAttrib;
		}
		
		function objArrContent(&$str, &$arr, $strHash)
		{
			global
				$arrInc,
				$arrDir,
				$arrMod,
				$arrTag,
				$arrElement
				;
			
			settype($arr,			'array');
			settype($strBlock,		'string');
			settype($str,			'string');
			settype($strHash,		'string');
			settype($strElement,	'string');
			settype($res,			'object');
			settype($objRend,		'object');
			
			$objRend = new objRend;
			foreach ($arrElement as $strElement => $strBlock) {
				$str = preg_replace(sprintf('/%s/Use', vsprintf($arrTag[$strBlock], array(
					$strElement,
					$strElement					# Cleaned up as needed for inline
					))), '$objRend->strRend("$2", $this->arrRend("$1"), $arr, $strHash)', preg_replace('/\$([[:word:]])/s', '\$ $1', $str)); # prevent the conflatious apprehension of variable.
			}
		}
	}
?>
