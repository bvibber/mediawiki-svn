/**
* mwEmbed module type loader for usability initiative 
* This file is included as a "core" script on every page so should be kept small.
*
* loader.js defines: 
* 	* all classes names and their paths
*	* module loader functions 
*
* loader.js files not in the core mwEmbed module folder must be defined in the global
* $wgJsModuleLoaderPaths[] = ",
*/
// For non-script loader javascript path debuging
mw.setConfig('loaderContext', wgScriptPath + '/extensions/UsabilityInitiative/js/');

mw.addClassFilePaths({
	"$j.whileAsync" : "plugins/jquery.async.js",
	"$j.fn.autoEllipsis" : "plugins/jquery.autoEllipsis.js",
	"$j.browserTest" : "plugins/jquery.browser.js",
	"$j.fn.collapsibleTabs" :	"plugins/jquery.collapsibleTabs.js",
	"$j.fn.delayedBind" :	"plugins/jquery.delayedBind.js",
	"inherit" :	"plugins/jquery.inherit.js",
	"$j.fn.namespaceSelector" :	"plugins/jquery.namespaceSelect.js",
	"$j.suggestions" :	"plugins/jquery.suggestions.js",
	"$j.wikiEditor" : "plugins/jquery.wikiEditor.js",
	
	"$j.wikiEditor.modules.highlight" : "plugins/jquery.wikiEditor.highlight.js",	
	"$j.wikiEditor.modules.toolbar"	: "plugins/jquery.wikiEditor.toolbar.js",
	"$j.wikiEditor.modules.dialogs" : "plugins/jquery.wikiEditor.dialogs.js",
	"$j.wikiEditor.modules.toc" : "plugins/jquery.wikiEditor.toc.js",
	"$j.wikiEditor.modules.preview" : "plugins/jquery.wikiEditor.preview.js",
	"$j.wikiEditor.modules.templateEditor" : "plugins/jquery.wikiEditor.templateEditor.js",
	"$j.wikiEditor.modules.publish" : "plugins/jquery.wikiEditor.publish.js"
} );


