<?php
mv_nomediawiki_config();

function mv_nomediawiki_config() {
	global $wgJSAutoloadLocalClasses, $wgScriptPath;
	$wgJSAutoloadLocalClasses = array();
	$wgScriptPath = realpath(dirname(__FILE__).'../');
	
	//give us true for mediaWiki
	define( 'MEDIAWIKI', true );
}
?>

