<?php
//give us true for mediaWiki
define( 'MEDIAWIKI', true );

define('MWEMBED_STANDALONE', true);

//setup the globals: 	(for documentation see: DefaultSettings.php )

$wgJSAutoloadLocalClasses = array();

$IP = realpath(dirname(__FILE__).'/../');

//$wgMwEmbedDirectory becomes the root $IP
$wgMwEmbedDirectory = '';

$wgUseFileCache = true;

$wgEnableScriptLoaderJsFile = false;

$wgEnableScriptLocalization = false;

$wgStyleVersion = '218';

$wgEnableScriptMinify = true;

//get the autoLoadClasses
require_once( realpath( dirname(__FILE__) ) . '/jsAutoloadLocalClasses.php' );
	
//get the JSmin class:
require_once( realpath( dirname(__FILE__) ) . '/minify/JSMin.php' );

?>