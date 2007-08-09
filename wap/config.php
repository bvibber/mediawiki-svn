<?php
/*
 * config file for hawpedia
 * $Date: 2007/04/14 20:38:30 $
 */

define("HAWPEDIA_VERSION",	"1.2B");

define("HAWIKI_TITLE",		"Wikipedia");    // browser window title
define("DEFAULT_LANGUAGE",	"es");
define('FORCE_DEFAULT_LANGUAGE', 'subdomain');	// not defined or FALSE - use browsers accept-language setting,
						//	if supported, else DAFAULT_LANGUAGE;
						// TRUE - always use DEFAULT_LANGUAGE;
						// 'subdomain' - use 1st (leftmost) subdomain as language code
						//	if supported, else DAFAULT_LANGUAGE.
define('DISABLE_IMAGES',	FALSE);	// do not output images, if set & true.
define('LINKS_HAVE_NO_BRACKETS', TRUE);	// do not output brackets surrounding links, if set & true.
define('IMG_MAX_WIDTH',		128);	// pixels maximum width for images (may be further reduced by displa size)
define('EXPAND_TEMPLATES',	false);	// expand {{...}} syntax, if set & true, else remove it.

define("SEGLENGTH_WML",  600);
define("SEGLENGTH_HDML", 600);
define("SEGLENGTH_HTML", 2000);
define("SEGLENGTH_VXML", 10000000);

define("HAWPEDIA_VXML_TMP_FILE", "/tmp/hawpedia_vxml_dispatcher.tmp"); 

$supportedLanguages = Array(
"bar" => 1,
"cs" => 1,
"de" => 1,
"en" => 1,
"es" => 1,
"fr" => 0,
"hu" => 1,
"ksh" => 1,
"nds" => 1,
"pt" => 1,
"sr" => 1,
"zxx" => 0);

?>
