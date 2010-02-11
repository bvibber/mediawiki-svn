/**
* Loader to support dynamic loading of mediaWiki js pages 
*/

mw.setConfig('loaderContext', wgScriptPath + '/js/');

mw.addClassFilePaths( {
	'uploadPage' : 'uploadPage.js',
	'editPage' : 'editPage.js',
	'ajaxCategories': 'ajaxcategories.js',
	'apiProxyPage'	: 'apiProxyPage.js'
} );