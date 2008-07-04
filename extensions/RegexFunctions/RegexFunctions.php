<?php
/*
* RegexFunctions extension by Ryan Schmidt
* Regular Expression parser functions
*/

if( !defined( 'MEDIAWIKI' ) ) {
	echo "This file is an extension of the MediaWiki software and cannot be used standalone\n";
	die( 1 );
}

//credits and hooks
$wgExtensionFunctions[] = 'wfRegexFunctions';
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'RegexFunctions',
	'author' => 'Ryan Schmidt',
	'url' => 'http://www.mediawiki.org/wiki/Extension:RegexFunctions',
	'version' => '1.0',
	'description' => 'Regular Expression parser functions',
);

$wgHooks['LanguageGetMagic'][] = 'wfRegexFunctionsLanguageGetMagic';

//default globals
//how many functions are allowed in a single page? Keep this at least above 3 for usability
$wgRegexFunctionsPerPage = 10;
//should we allow modifiers in the functions, e.g. the /g and /i modifiers for global and case-insensitive?
//This does NOT enable the 'e' modifier for preg_replace, see the next variable for that
$wgRegexFunctionsAllowModifiers = true;
//should we allow the 'e' modifier in preg_replace? Requires AllowModifiers to be true.
//Don't enable this unless you trust every single editor on your wiki, as it opens up a potential XSS vector
$wgRegexFunctionsAllowE = false;
//limit for rsplit and rreplace functions. -1 is unlimited
$wgRegexFunctionsLimit = -1;
//array of functions to disable, aka these functions cannot be used :)
$wgRegexFunctionsDisable = array();

function wfRegexFunctions() {
	global $wgParser, $wgExtRegexFunctions;

	$wgExtRegexFunctions = new ExtRegexFunctions();
	$wgParser->setFunctionHook( 'rmatch', array(&$wgExtRegexFunctions, 'rmatch') );
	$wgParser->setFunctionHook( 'rsplit', array(&$wgExtRegexFunctions, 'rsplit') );
	$wgParser->setFunctionHook( 'rreplace', array(&$wgExtRegexFunctions, 'rreplace') );
}

function wfRegexFunctionsLanguageGetMagic( &$magicWords, $langCode ) {
	switch ( $langCode ) {
	default:
		$magicWords['rmatch'] = array( 0, 'rmatch' );
		$magicWords['rsplit'] = array( 0, 'rsplit' );
		$magicWords['rreplace'] = array( 0, 'rreplace' );
	}
	return true;
}

Class ExtRegexFunctions {
	var $num = 0;
	function rmatch ( &$parser, $string = '', &$pattern = '', &$return = '', $notfound = '', $offset = 0 ) {
		global $wgRegexFunctionsPerPage, $wgRegexFunctionsAllowModifiers, $wgRegexFunctionsDisable;
		if(in_array('rmatch', $wgRegexFunctionsDisable))
			return;
		$this->num++;
		if($this->num > $wgRegexFunctionsPerPage)
			return;
		if(!$wgRegexFunctionsAllowModifiers)
			$pattern = str_replace('/', '\/', $pattern);
		$num = preg_match( $pattern, $string, $matches, PREG_OFFSET_CAPTURE, $offset );
		if($num === false)
			return;
		if($num === 0)
			return $notfound;
		$mn = 0;
		foreach($matches as $match) {
			if($mn > 9)
				break;
			$return = str_replace('$'.$mn, $matches[$mn][0], $return);
			$return = str_replace('\\\\'.$mn, $matches[$mn][1], $return);
			$mn++;
		}
		return $return;
	}
	function rsplit ( &$parser, $string = '', &$pattern = '', $piece = 0 ) {
		global $wgRegexFunctionsPerPage, $wgRegexFunctionsAllowModifiers, $wgRegexFunctionsLimit, $wgRegexFunctionsDisable;
		if(in_array('rmatch', $wgRegexFunctionsDisable))
			return;
		$this->num++;
		if($this->num > $wgRegexFunctionsPerPage)
			return;
		if(!$wgRegexFunctionsAllowModifiers)
			$pattern = str_replace('/', '\/', $pattern);
		$res = preg_split( $pattern, $string, $wgRegexFunctionsLimit );
		return $res[$piece];
	}
	function rreplace ( &$parser, $string = '', &$pattern = '', &$replace = '' ) {
		global $wgRegexFunctionsPerPage, $wgRegexFunctionsAllowModifiers, $wgRegexFunctionsAllowE, $wgRegexFunctionsLimit, $wgRegexFunctionsDisable;
		if(in_array('rmatch', $wgRegexFunctionsDisable))
			return;
		$this->num++;
		if($this->num > $wgRegexFunctionsPerPage)
			return;
		if(!$wgRegexFunctionsAllowModifiers)
			$pattern = str_replace('/', '\/', $pattern);
		elseif(!$wgRegexFunctionsAllowE)
			$pattern = preg_replace('/(\/.*?)e(.*?)$/i', '$1$2', $pattern);
		$res = preg_replace($pattern, $replace, $string, $wgRegexFunctionsLimit);
		return $res;
	}
}