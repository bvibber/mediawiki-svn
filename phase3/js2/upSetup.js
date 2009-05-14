// simple script to invoke the upload with config:  

mwAddOnloadHook( function(){
	//make sure we have mvFirefogg & mvUploader loaded:
	mvJsLoader.doLoad( {
		'mvFirefogg' : 'libAddMedia/mvFirefogg.js',	
		'mvUploader' : 'libAddMedia/mvUploader.js'
	},function(){		
		mvUp = new mvUploader( { 'api_url' : wgServer + wgScriptPath + '/api.php' } );		
	});
});