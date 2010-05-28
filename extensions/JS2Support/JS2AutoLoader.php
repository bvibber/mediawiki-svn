<?php

/****************************
* JS2 AutoLoader php files
*****************************/
$wgAutoloadClasses = array_merge( $wgAutoloadClasses,  array(
	'JSMin' => $js2Dir . 'mwEmbed/includes/library/JSMin.php',
	'Minify_CSS' => $js2Dir . 'mwEmbed/includes/library/CSS.php',
	'Minify_CommentPreserver' => $js2Dir . 'mwEmbed/includes/library/CommentPreserver.php',
	'Minify_CSS_Compressor' => $js2Dir . 'mwEmbed/includes/library/CSS/Compressor.php',
	'Minify_CSS_UriRewriter' => $js2Dir . 'mwEmbed/includes/library/CSS/UriRewriter.php',
	'JSMinException' => $js2Dir . 'mwEmbed/includes/minify/JSMin.php',
	'jsScriptLoader' => $js2Dir . 'mwEmbed/jsScriptLoader.php',
	'jsClassLoader' => $js2Dir . 'mwEmbed/includes/jsClassLoader.php',
	'simpleFileCache' => $js2Dir . 'mwEmbed/jsScriptLoader.php',
));

// Autoloader for core mediaWiki JavaScript files (path is from the MediaWiki folder)
$wgScriptLoaderNamedPaths = array_merge(  $wgScriptLoaderNamedPaths, array(
	'ajax' => 'skins/common/ajax.js',
	'ajaxwatch' => 'skins/common/ajaxwatch.js',
	'allmessages' => 'skins/common/allmessages.js',
	'block' => 'skins/common/block.js',
	'changepassword' => 'skins/common/changepassword.js',
	'diff' => 'skins/common/diff.js',
	'edit' => 'skins/common/edit.js',
	'enhancedchanges.js' => 'skins/common/enhancedchanges.js',
	'history' => 'skins/common/history.js',
	'htmlform' => 'skins/common/htmlform.js',
	'IEFixes' => 'skins/common/IEFixes.js',
	'metadata' => 'skins/common/metadata.js',
	'mwsuggest' => 'skins/common/mwsuggest.js',
	'prefs' => 'skins/common/prefs.js',
	'preview' => 'skins/common/preview.js',
	'protect' => 'skins/common/protect.js',
	'rightclickedit' => 'skins/common/rightclickedit.js',
	'sticky' => 'skins/common/sticky.js',
	'upload' => 'skins/common/upload.js',
	'wikibits' => 'skins/common/wikibits.js',

	//css named paths
	'mw.style.commonShared' => 'skins/common/shared.css',
	'mw.style.commonPrint' => 'skins/common/commonPrint.css',
	'mw.style.vectorMainLtr' => 'skins/vector/main-ltr.css'

) );

?>
