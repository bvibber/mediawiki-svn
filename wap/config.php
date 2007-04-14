<?php
/*
 * config file for hawpedia
 * $Date: 2006/11/22 21:25:30 $
 */

define("HAWPEDIA_VERSION", "1.0");

define("HAWIKI_TITLE", "Wikipedia");    // browser window title
define("DEFAULT_LANGUAGE", "es");
define('FORCE_DEFAULT_LANGUAGE', TRUE);	// ignore browsers accept-language setting, if set & true.
# define('DISABLE_IMAGES', FALSE);	// do not output images, if set & true.
define('LINKS_HAVE_NO_BRACKETS', TRUE);	// do not output brackets surrounding links, if set & true.
define('IMG_MAX_WIDTH', 128);		// pixels maximum width for images.
define('EXPAND_TEMPLATES', TRUE);	// expand {{...}} sytax, if set & true, else ignore it.

define("SEGLENGTH_WML",  600);
define("SEGLENGTH_HDML", 600);
define("SEGLENGTH_HTML", 2000);
define("SEGLENGTH_VXML", 10000000);

define("HAWPEDIA_VXML_TMP_FILE", "/tmp/hawpedia_vxml_dispatcher.tmp"); 

$supportedLanguages = Array(
"bar" => 1,
"de" => 1,
"en" => 1,
"es" => 1,
"fr" => 0);

?>
