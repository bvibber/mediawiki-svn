<?php

/**************************** 
* JS2 AutoLoader.php
*****************************/
$wgAutoloadClasses = array_merge( $wgAutoloadClasses,  
	 array(
		'JSMin' => $dir . 'js/mwEmbed/includes/library/JSMin.php',
		'Minify_CSS' => $dir . 'js/mwEmbed/includes/library/CSS.php',
		'Minify_CommentPreserver' => $dir . 'js/mwEmbed/includes/library/CommentPreserver.php',
		'Minify_CSS_Compressor' => $dir . 'js/mwEmbed/includes/library/CSS/Compressor.php',
		'Minify_CSS_UriRewriter' => $dir . 'js/mwEmbed/includes/library/CSS/UriRewriter.php',
		'JSMinException' => $dir . 'js/mwEmbed/includes/minify/JSMin.php',
		'jsScriptLoader' => $dir . 'js/mwEmbed/jsScriptLoader.php',
		'jsClassLoader' => $dir . 'js/mwEmbed/includes/jsClassLoader.php',
		'simpleFileCache' => $dir . 'js/mwEmbed/jsScriptLoader.php',
	)
);

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

	// JS2 entry points
	'uploadPage' => $dir . 'uploadPage.js',
	'editPage' =>  $dir . 'editPage.js',
	'ajaxCategories' =>  $dir . 'ajaxcategories.js',
	'apiProxyPage'	=>  $dir . 'apiProxyPage.js',
	
	// Css bindings
	'mw.style.shared' => 'skins/common/shared.css',
	'mw.style.commonPrint' => 'skins/common/commonPrint.css',
	'mw.style.vectorMainLTR' => 'skins/vector/main-ltr.css',

	//monobook css
	'mw.sytle.mbMain' => 'skins/monobook/main.css',
	'mw.style.mbIE5' => 'skins/monobook/IE50Fixes.css',
	'mw.style.mbIE55' => 'skins/monobook/IE55Fixes.css',
	'mw.style.mbIE60' => 'skins/skins/monobook/IE60Fixes.css',
	'mw.style.mbIE7' => 'skins/monobook/IE70Fixes.css',
	
);
?>
