<?php

/*
WikiTeX: expansible LaTeX module for MediaWiki
Copyright (C) 2004  Peter Danenberg

     WikiTeX  is  free  software;  you  can  redistribute it
and/or modify it under the terms of the GNU  General  Public
License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later ver-
sion.

wikitex.php: main parser  functions, registration  interface
with $wgParser

*/

include 'wikitex.inc.php';
settype($obj,	'object');

// register WikiTeX with parser
$wgExtensionFunctions[] = 'voidRegister';

// perform registration
function voidRegister()
{
  global $arrRend, $obj;
  $obj = new objRend($arrRend);
}

class objRend
{
  // register object with parser
  function __construct($arr)
    {
      global $wgParser;

      settype($strVal,	'string');
      settype($strKey,	'string');

      foreach ($arr as $strKey => $strVal) {
	$wgParser->setHook($strKey, $strVal);
      }
    }

  // PHP4 constructor
  function objRend($arr)
  {
    return __contruct($arr);
  }

  // parses strings of the form x=y y="z" z="a b c" [...], and returns an accordant array[x]=y, etc.
  function arrParse($str)
    {
      settype($arr,	'array');
      settype($arrAttr,	'array');
      settype($strKey,	'string');
      settype($strVal,	'string');
			
      preg_match_all('/(\S*)\=(?(?=\")\"([^"]*)\"|(.*))\s*(?=\S*\=|$)/Us', $str, $arr, PREG_PATTERN_ORDER);
      foreach ($arr[1] as $strKey => $strVal) {
	// In x="y", "y" is returned as the second node; x=y, y the third.  Otherwise blank.
	$arrAttr[$strVal] = trim($arr[(empty($arr[2][$strKey]) ? 3 : 2)][$strKey], '"');
      }
      return $arrAttr;
    }

  // post-processing; security
  function strPost($str, &$strClass, &$strFile)
    {
      global
	$arrErr, $strErr;
			
      settype($strBlack,	'string');
      settype($arrBlack,	'array');

      // generic security basis for all classes
      $arrBlack['rend']	= array('\catcode', '\include', '\includeonly', '\input',
				'\newcommand', '\newenvironment', '\newtheorem', '\newfont',
				'\renewcommand', '\renewenvironment', '\typein', '\typeout', '\write');

      // specific security recommendations
      $arrBlack['music']	= array('#');
    
      // merge arrays, if specific present
      if (!empty($arr[$strClass])) {
	$arrBlack['rend'] = array_merge($arrBlack[$strClass], $arrBlack['rend']);
      }
    
      foreach($arrBlack['rend'] as $strBlack) {
	if (stristr($str, $strBlack) !== false) {
	  $strClass = 'error';
	  $strFile = file_get_contents($strErr);
	  return $arrErr['rend'];
	}
      }

      // seems to remain an artifact \' after stripslashes
      return strtr($str, array('\\\'' => '\''));
    }

  // substitute the parameters in the given template
  function strSub($str, $arr, $strToken)
    {
      settype($strKey,	'string');
      settype($strVal,	'string');
			
      foreach($arr as $strKey => $strVal) {
	$str = strtr($str, array(sprintf($strToken, $strKey) => $strVal));
      }

      return $str;
    }
		
  // receive raw text in, serialize to hash-encoded temp file, funnel to bash
  // and receive tag anew.
  function strRend($strTex, $arr)
    {
      global $arrErr, $strErr, $strRendPath, $wgScriptPath; // global err, path def's

      settype($obj,	'object'); // file resource
      settype($arrBash,	'array'); // standard out parameters
      settype($arrTag,	'array');
      settype($str,	'string');
      settype($strHash,	'string'); // outfile hash
      settype($strTag,	'string');
      settype($strKey,	'string');
      settype($strVal,	'string');
      settype($strTemplate,'string'); // template glob
      settype($strErrClass,'array');
      settype($strDir,	'string'); // temp directory
      settype($strURI,	'string'); // relative URI
      settype($strBash,	'string'); // shell command
      settype($strRend,	'string'); // replacement
			
      $strTemplate= "$strRendPath/wikitex.%s.inc*";
      $strErrClass= 'error';
      $strDir	= "$strRendPath/tmp/";
      $strURI	= "$wgScriptPath/extensions/wikitex/tmp/";
      $strBash	= "$strRendPath/wikitex.sh %s %s %s";	// usage: wikitex FILE MODULE OUTPATH
      $arrTag['rend']	= '<img src="%src%" alt="%alt%" title="%alt%" />';
      $arrTag['music']	= '<a href="%midi%"><img src="%src%" alt="%alt%" title="%alt%" style="border: none" /></a>';
      $strRend	= '%%%s%%';

      // check class template against glob: "wikitex.<class>.inc*"
      if(!($str = file_get_contents(current(glob(sprintf($strTemplate, $arr['class'])))))) {
	// invoke generic error template
	$str = file_get_contents($strErr);

	// generate error message
	$arr['value'] = sprintf($arrErr['class'], $arr['class']);

	// set generic error class
	$arr['class'] = $strErrClass;
      }
			
      // post-processing: black-list control, actualizing the file template
      $arr['value'] = $this->strPost($strTex, $arr['class'], $str);

      // simulate an 'alt' parm for use in images; alt data consists of the verbatim
      // text.
      $arrBash['alt'] = htmlspecialchars($strTex, ENT_QUOTES);

      // check for defaults defined in wikitex.inc.php, and realize them
      if (!empty($arrDef[$arr['class']])) {
	$this->vDefault($arr, $arrDef[$arrAttrib['class']]);
      }

      // token substitution; where each value of $arr substituted in $str,
      // %value%, %style%, etc.
      $strTex = $this->strSub($str, $arr, $strRend);

      // derive the outfile hash
      $strHash = md5($strTex);

      // TODO: graceless exception on inaccessibility
      if($obj = fopen($strDir . $strHash, 'w')) {
	fwrite($obj, $strTex);
	fclose($obj);
      }

      // collect an array of parameters from the shell in the form: x=y
      $arrBash = array_merge($arrBash, $this->arrParse(shell_exec(escapeshellcmd(sprintf($strBash, $strHash, $arr['class'], $strURI)))));
			
      // choose tag based on return value from bash; i.e. whether an error was detected,
      // or default tag, if no particular present
      $strTag = (empty($arrTag[$arrBash['class']]) ? $arrTag['rend'] : $arrTag[$arrBash['class']]);

      // gestalt the out-tag
      foreach($arrBash as $strKey => $strVal) {
	$strTag = strtr($strTag, array(sprintf($strRend, $strKey) => $strVal));
      }

      return $strTag;
    }
}

function strChem($str)
{
  global $obj;
  return $obj->strRend($str, array('class' => 'chem'));
}

function strChess($str)
{
  global $obj;
  return $obj->strRend($str, array('class' => 'chess'));
}

function strFeyn($str)
{
  global $obj;
  return $obj->strRend($str, array('class' => 'feyn'));
}

function strGo($str)
{
  global $obj;
  return $obj->strRend($str, array('class' => 'go'));
}

function strGreek($str)
{
  global $obj;
  return $obj->strRend($str, array('class' => 'greek'));
}

function strLing($str)
{
  global $obj;
  return $obj->strRend($str, array('class' => 'ling'));
}

function strMath($str)
{
  global $obj;
  return $obj->strRend($str, array('class' => 'math'));
}

function strMusic($str)
{
  global $obj;
  return $obj->strRend($str, array('class' => 'music'));
}

function strTeng($str)
{
  global $obj;
  return $obj->strRend($str, array('class' => 'teng'));
}

function strTipa($str)
{
  global $obj;
  return $obj->strRend($str, array('class' => 'tipa'));
}

function strXym($str)
{
  global $obj;
  return $obj->strRend($str, array('class' => 'xym'));
}

?>
