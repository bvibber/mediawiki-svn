/**
* Loader to support dynamic loading of mediaWiki js pages 
*/

mw.setConfig('loaderContext', wgScriptPath + '/js/');

mw.addClassFilePaths( {
	'uploadPage' => 'js/uploadPage.js',
	'editPage' => 'js/editPage.js',
	'ajaxCategories' => 'js/ajaxcategories.js',
	'apiProxyPage'	=> 'js/apiProxyPage.js'
} );