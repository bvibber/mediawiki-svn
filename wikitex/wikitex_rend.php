<?php
	/* WikiTeX: expansible LaTeX module for MediaWiki
	Copyright (C) 2004  Peter Danenberg
	
	WikiTeX is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version. */

	include(sprintf($arrInc['php'], sprintf($arrMod['mod'], $arrMod['rend'])));
	
	class objRend
	{
		function arrParse($str)
		{
			# parses strings of the form x=y y="z" z="a b c" [...], and returns an accordant array[x]=y, etc.
			settype($arr,		'array');
			settype($arrAttr,	'array');
			settype($strKey,	'string');
			settype($strVal,	'string');
			
			preg_match_all('/(\S*)\=(?(?=\")\"([^"]*)\"|(.*))\s*(?=\S*\=|$)/Us', $str, $arr, PREG_PATTERN_ORDER);
			foreach ($arr[1] as $strKey => $strVal) {
				# In x="y", "y" is returned as the second node; x=y, y the third.  Otherwise blank.
				$arrAttr[$strVal] = trim($arr[(empty($arr[2][$strKey]) ? 3 : 2)][$strKey], '"');
			}
			return $arrAttr;
		}
	
		function strPost($str, &$strClass, &$strFile)
		{
			# TeX post-processing
			global
				$arrErr,
				$arrBlack,
				$arrMod,
				$arrInc
				;
			
			settype($strBlack,	'string');
			settype($arr,		'array');
			
			$arr = array_merge((!empty($arrBlack[$strClass])
				? empty($arrBlack[$strClass])
				: array()), $arrBlack[$arrMod['rend']]);
			
			foreach($arr as $strBlack) {
				if (stristr($str, $strBlack) !== false) {
					$strClass = 'error';
					$strFile = file_get_contents(sprintf($arrInc['rend'], $arrMod['error']));
					return $arrErr[$arrMod['rend']];
				}
			}

			return strtr($str, array(		# seems to remain an artifact \' after stripslashes
				'\\\'' => '\''
				));
		}

		function strSub($str, $arr, $strToken)
		{
			# substitute the parameters in the given template
			settype($strKey,	'string');
			settype($strVal,	'string');
			
			foreach($arr as $strKey => $strVal) {
				$str = strtr($str, array(
					sprintf($strToken, $strKey) => $strVal
					));
			}
			return $str;
		}
		
		function vDefault(&$arr, $arrDef)
		{
			# set the default for which attribute array
			settype($strKey,	'string');
			settype($strVal,	'string');
			
			foreach($arrDef as $strKey => $strVal) {
				if (empty($arr[$strKey])) {
					$arr[$strKey] = $strVal;
				}
			}
		}

		function strRend($strTex, $arrAttrib, &$arr, $strTexHash)
		{
			global
				$arrDir,
				$arrInc,
				$arrMod,
				$arrTag,
				$arrDef,
				$arrAux,
				$arrErr,
				$arrRend
				;
			
			settype($res,				'object');
			settype($arrStyle,			'array');
			settype($arrBash,			'array');	# standard out parameters
			settype($str,				'string');
			settype($strHash,			'string');
			settype($strTag,			'string');
			settype($strKey,			'string');
			settype($strVal,			'string');
			
			if(!($str = @file_get_contents(sprintf($arrInc['rend'], $arrAttrib['class'])))) {
				$str = file_get_contents(sprintf($arrInc['rend'], $arrMod['error']));
				$arrAttrib['value'] = (empty($arrAttrib['class'])
					? $arrErr['noclass']
					: sprintf($arrErr['class'], $arrAttrib['class']));
				$arrAttrib['class'] = $arrMod['error'];
			}
			
			$arrAttrib['style']	= !empty($arrAttrib['style'])
				? $arrAttrib['style']
				: array();
			$arrAttrib['value']	= $this->strPost((empty($arrAttrib['value']) # value takes precedence over content
				? $strTex
				: $arrAttrib['value']), $arrAttrib['class'], $str);

			$arrBash['alt'] = rtrim(preg_replace('/\s*(.*)/', "$1 ", htmlspecialchars($arrAttrib['value'], ENT_QUOTES)));	# simulate stdout parm for alt image descriptors

			!empty($arrDef[$arrAttrib['class']])	# set defaults
				? $this->vDefault($arrAttrib, $arrDef[$arrAttrib['class']])
				: NULL;
			
			$strTex = $this->strSub(
				$this->strSub($str, $arrAttrib, $arrRend[$arrMod['rend']]),	# substitute hard tokens
					$arrAttrib['style'], $arrRend[$arrMod['style']]);		# substitute style tokens

			$strHash = md5($strTex);				# gestalt the outfile
			if($res = fopen(vsprintf('%s%s', array (
				$arrDir['temp'],
				$strHash
				)), 'w')) {
					fwrite($res, $strTex);
					fclose($res);
			}
			
			$arrBash = array_merge($arrBash, $this->arrParse(shell_exec(escapeshellcmd(vsprintf($arrMod['bash'], array(	# return an array of stdout parms in the form x=y
				$arrDir['bash'],
				$strHash,
				$arrAttrib['class'],
				$arrDir['out']
				))))));
			
			$strTag = (empty($arrTag[$arrBash['class']])	# tag-gestalt; listened-to from bash
				? $arrTag[$arrMod['rend']]
				: $arrTag[$arrBash['class']]);
			
			foreach($arrBash as $strKey => $strVal) {
				$strTag = strtr($strTag, array(
					sprintf($arrRend[$arrMod['rend']], $strKey) => $strVal
					));
			}
			
			array_push($arr, $strTag);	# for wiki's post-parse
			return $strTexHash;			# the hash placeholder
		}
	}
?>