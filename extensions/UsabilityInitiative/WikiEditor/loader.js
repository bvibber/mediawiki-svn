/**
* javascript loader for wikiEditor config classes
*/
// For non-script loader javascript path
mw.setConfig('loaderContext', wgScriptPath + '/extensions/UsabilityInitiative/WikiEditor/');

mw.addClassFilePaths({
	"wikiEditor.config.highlight" : "Modules/Highlight/Highlight.js",
	"wikiEditor.config.preview" : "Modules/Preview/Preview.js",
	"wikiEditor.config.publish" : "Modules/Publish/Publish.js",
	"wikiEditor.config.toc" : "Modules/Toc/Toc.js",
	"wikiEditor.config.toolbar" : "Modules/Toolbar/Toolbar.js",
	"wikiEditor.config.templateEditor" : "Modules/TemplateEditor/TemplateEditor.js"
});

mw.addModuleLoader( 'WikiEditor', function( callback ){
	// NOTE: we should check config and skip stuff we don't want need
	var libReq = [
		//Get the core library dependencies
		"$j.ui",
		"$j.fn.datePicker",
		"$j.cookie",
		"$j.ui.dialog",
		"$j.ui.datepicker",
		"$j.ui.draggable",
		"$j.ui.resizable",
		"$j.ui.tabs",
		
		"$j.whileAsync" ,
		"$j.fn.autoEllipsis" ,
		"$j.browserTest" ,
		"$j.fn.collapsibleTabs" ,
		"$j.fn.delayedBind" ,
		"inherit",
		"$j.fn.namespaceSelector" ,
		"$j.suggestions" ,
		"$j.wikiEditor",
		
		// NOTE we should only include the modules we need
		"$j.wikiEditor.modules.highlight" ,	
		"$j.wikiEditor.modules.toolbar",
		"$j.wikiEditor.modules.dialogs",
		"$j.wikiEditor.modules.toc" ,
		"$j.wikiEditor.modules.preview" ,
		"$j.wikiEditor.modules.templateEditor" ,
		"$j.wikiEditor.modules.publish",
		
		//Also load per module configuration
		"wikiEditor.config.highlight",
		"wikiEditor.config.preview",
		"wikiEditor.config.publish",
		"wikiEditor.config.toc",
		"wikiEditor.config.toolbar" ,
		"wikiEditor.config.templateEditor" 		
	];
	
	//Load the combined css ( once we have a style sheet loader we could do
	// module dependency mapping and get proper set of style sheets 
	mw.getStyleSheet( wgScriptPath + '/extensions/UsabilityInitiative/css/combined.min.css' );
	
	
	mw.load( libReq, function(){
		mw.log('wikiEditor done ' );
		callback( 'WikiEditor' );
	} );
	
	
});