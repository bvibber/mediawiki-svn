/** 
 * Name all the css and script used in UsabilityInitiative 
 */  

mw.addResourcePaths( {
	"mw.style.usabilitySugest" : "css/suggestions.css",
	"mw.style.vectorCollapsibleNav" : "css/vector.collapsibleNav.css",
	"mw.style.vectorFooterCleanup" : "css/vector.footerCleanup.css",
	"mw.style.wikiEditor" : "css/wikiEditor.css",
	"mw.style.wikiEditorDialogs" : "css/wikiEditor.dialogs.css",
	"mw.style.wikiEditorPreview" : "css/wikiEditor.preview.css",
	"mw.style.wikiEditorToc" : "css/wikiEditor.toc.css",
	"mw.style.wikiEditorToolbar" :  "css/wikiEditor.toolbar.css",
	"mw.style.vectorJquery-ui" : "css/vector/jquery-ui-1.7.2.css",
	
	"$j.whileAsync" : "js/plugins/jquery.async.js",
	
	"$j.fn.autoEllipsis" : "js/plugins/jquery.autoEllipsis.js",
	
	"$j.fn.collapsibleTabs" :	"js/plugins/jquery.collapsibleTabs.js",
	"$j.fn.color" : "js/plugins/jquery.color.js",
	
	"$j.fn.delayedBind" :	"js/plugins/jquery.delayedBind.js",
		
		
	"$j.wikiEditor" : "js/plugins/jquery.wikiEditor.js",
	
	"$j.wikiEditor.modules.dialogs" : "js/plugins/jquery.wikiEditor.dialogs.js",
	
	"$j.wikiEditor.modules.templateEditor" : "js/plugins/jquery.wikiEditor.templateEditor.js",
	"$j.wikiEditor.modules.templates" :  "js/plugins/jquery.wikiEditor.templates.js",
	
	"$j.wikiEditor.modules.highlight" : "js/plugins/jquery.wikiEditor.highlight.js",	
	"$j.wikiEditor.modules.toolbar"	: "js/plugins/jquery.wikiEditor.toolbar.js",
	"$j.wikiEditor.modules.toc" : "js/plugins/jquery.wikiEditor.toc.js",
	"$j.wikiEditor.modules.preview" : "js/plugins/jquery.wikiEditor.preview.js",	
	"$j.wikiEditor.modules.publish" : "js/plugins/jquery.wikiEditor.publish.js",
	
	"wikiEditor.config.highlight" : "WikiEditor/Modules/Highlight/Highlight.js",
	"wikiEditor.config.preview" : "WikiEditor/Modules/Preview/Preview.js",
	"wikiEditor.config.publish" : "WikiEditor/Modules/Publish/Publish.js",
	"wikiEditor.config.toc" : "WikiEditor/Modules/Toc/Toc.js",
	"wikiEditor.config.toolbar" : "WikiEditor/Modules/Toolbar/Toolbar.js",
	"wikiEditor.config.templateEditor" : "WikiEditor/Modules/TemplateEditor/TemplateEditor.js",
	"wikiEditor.config.templates" :  "WikiEditor/Modules/Templates/Templates.js"
} );

mw.addModuleLoader( 'WikiEditor', function( callback ){

	//@@todo we should check config and skip stuff we don't want need
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
		"$j.fn.suggestions" ,
		"$j.wikiEditor",
		
		//@@NOTE we should only include the modules we need
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
	mw.load( libReq, function(){
		mw.log('wikiEditor done ' );
		callback( 'WikiEditor' );
	});	
});

// Hack to support usability.js remaping of mw functions
mw.usability = {};

mw.usability.load = mw.load;

mw.usability.addMessages = mw.addMessages;

mw.usability.getMsg = gM;
