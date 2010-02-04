/**
*
* Core "loader.js" for mwEmbed
*
* This loader along with all the enabled module loaders is combined with mwEmbed.js
*  via the script-loader. 
*
*/

/**
* The set of modules that you want enable. 
* 
* Each enabledModules array value should be a name
* of a folder in mwEmbed/modules 
*
* Modules must define a loader.js file in the root
*  of the module folder. 
* 
* A loader file should only include:
*  Class paths of the module classes
*  Sytle sheets of the module
*  Loader function(s) that load module classes 
*
* When using the scriptLoader the enabledModules loader code
*  is transcluded into base mwEmbed class include.  
*/
var mwEnabledModuleList =  [
	'AddMedia',
	'ClipEdit',
	'EmbedPlayer',
	'ApiProxy',
	'Sequencer',
	'TimedText'	
];

mw.setConfig( 'enabledModules', mwEnabledModuleList );

/**
* --  Load Class Paths --
* 
* PHP AutoLoader reads this loader.js file along with 
* all the "loader.js" files to determin script-loader 
* class paths
* 
*/

// Set the loaderContext for the classFiles paths call:  
mw.setConfig('loaderContext', '' );

/**
 * Core set of mwEmbed classes:
 */
mw.addClassFilePaths( {
	"mwEmbed"				: "mwEmbed.js",
	"window.jQuery"			: "jquery/jquery-1.3.2.js",
	
	"ctrlBuilder"			: "skins/ctrlBuilder.js",
	"kskinConfig"			: "skins/kskin/kskinConfig.js",
	"mvpcfConfig"			: "skins/mvpcf/mvpcfConfig.js",
	
	"$j.fn.pngFix"			: "jquery/plugins/jquery.pngFix.js",
	"$j.fn.autocomplete"	: "jquery/plugins/jquery.autocomplete.js",
	"$j.fn.hoverIntent"		: "jquery/plugins/jquery.hoverIntent.js",
	"$j.fn.datePicker"		: "jquery/plugins/jquery.datePicker.js",
	"$j.ui"					: "jquery/jquery.ui/ui/ui.core.js",	
	
	"mw.testLang"			:  "tests/testLang.js",		

	"$j.cookie"				: "jquery/plugins/jquery.cookie.js",
	"$j.contextMenu"		: "jquery/plugins/jquery.contextMenu.js",
	"$j.fn.suggestions"		: "jquery/plugins/jquery.suggestions.js",
	"$j.fn.textSelection" 	: "jquery/plugins/jquery.textSelection.js",
	"$j.browserTest"		: "jquery/plugins/jquery.browserTest.js",

	"$j.effects.blind"		: "jquery/jquery.ui/ui/effects.blind.js",
	"$j.effects.drop"		: "jquery/jquery.ui/ui/effects.drop.js",
	"$j.effects.pulsate"	: "jquery/jquery.ui/ui/effects.pulsate.js",
	"$j.effects.transfer"	: "jquery/jquery.ui/ui/effects.transfer.js",
	"$j.ui.droppable"		: "jquery/jquery.ui/ui/ui.droppable.js",
	"$j.ui.slider"			: "jquery/jquery.ui/ui/ui.slider.js",
	"$j.effects.bounce"		: "jquery/jquery.ui/ui/effects.bounce.js",
	"$j.effects.explode"	: "jquery/jquery.ui/ui/effects.explode.js",
	"$j.effects.scale"		: "jquery/jquery.ui/ui/effects.scale.js",
	"$j.ui.datepicker"		: "jquery/jquery.ui/ui/ui.datepicker.js",
	"$j.ui.progressbar"		: "jquery/jquery.ui/ui/ui.progressbar.js",
	"$j.ui.sortable"		: "jquery/jquery.ui/ui/ui.sortable.js",
	"$j.effects.clip"		: "jquery/jquery.ui/ui/effects.clip.js",
	"$j.effects.fold"		: "jquery/jquery.ui/ui/effects.fold.js",
	"$j.effects.shake"		: "jquery/jquery.ui/ui/effects.shake.js",
	"$j.ui.dialog"			: "jquery/jquery.ui/ui/ui.dialog.js",
	"$j.ui.resizable"		: "jquery/jquery.ui/ui/ui.resizable.js",
	"$j.ui.tabs"			: "jquery/jquery.ui/ui/ui.tabs.js",
	"$j.effects.core"		: "jquery/jquery.ui/ui/effects.core.js",
	"$j.effects.highlight"	: "jquery/jquery.ui/ui/effects.highlight.js",
	"$j.effects.slide"		: "jquery/jquery.ui/ui/effects.slide.js",
	"$j.ui.accordion"		: "jquery/jquery.ui/ui/ui.accordion.js",
	"$j.ui.draggable"		: "jquery/jquery.ui/ui/ui.draggable.js",
	"$j.ui.selectable"		: "jquery/jquery.ui/ui/ui.selectable.js"	

} );