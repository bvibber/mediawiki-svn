<?php

/**************************** 
* JS2 AutoLoader.php
*****************************/
$mwEmbedAutoLoadClasses = array(
	'JSMin' => $dir . 'mwEmbed/includes/library/JSMin.php',
	'Minify_CSS' => $dir . 'mwEmbed/includes/library/CSS.php',
	'Minify_CommentPreserver' => $dir . 'mwEmbed/includes/library/CommentPreserver.php',
	'Minify_CSS_Compressor' => $dir . 'mwEmbed/includes/library/CSS/Compressor.php',
	'Minify_CSS_UriRewriter' => $dir . 'mwEmbed/includes/library/CSS/UriRewriter.php',
	'JSMinException' => $dir . 'mwEmbed/includes/minify/JSMin.php',
	'jsScriptLoader' => $dir . 'mwEmbed/jsScriptLoader.php',
	'jsClassLoader' => $dir . 'mwEmbed/includes/jsClassLoader.php',
	'simpleFileCache' => $dir . 'mwEmbed/jsScriptLoader.php',
);
$wgAutoloadClasses = array_merge( $wgAutoloadClasses,  $mwEmbedAutoLoadClasses);

// Autoloader for core mediaWiki JavaScript files (path is from the MediaWiki folder)
$wgJSAutoloadLocalClasses = array(
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

	// js2 entry points
	'uploadPage' => $dir . 'uploadPage.js',
	'editPage' =>  $dir . 'editPage.js',
	'ajaxCategories' =>  $dir . 'ajaxcategories.js',
	'apiProxyPage'	=>  $dir . 'apiProxyPage.js'
);
?>
