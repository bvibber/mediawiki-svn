/**
*
* Core "loader.js" for mwEmbed
*
* This loader along with all the enabled module loaders is combined with mwEmbed.js
*  via the script-loader. 
*
*/

/**
* Core js components 
* 
* These components are pieces of the core mwEmbed lib
* They are in separate files to keep the code easier to maintain. 
*
* All mwEmbed core classes are loaded on every mwEmbed request
* 
* NOTE: All user / application module code should go into /modules
* and enabled in mwEnabledModuleList below.
*/
var mwCoreComponentList = [	
	'mw.Language'
];


/**
* The default set of enabled modules
* ( Modules can also be enabled via mediaWiki extensions ) 
* 
* Each enabledModules array value should be a name
* of a folder in mwEmbed/modules 
*
* Modules must define a loader.js file in the root
*  of the module folder. 
* 
* A loader file should only include:
*  * Class paths of the module classes
*  * Style sheets of the module
*  * Loader function(s) that load module classes
*  * Any code you want run on all pages like checking the DOM 
*  		for a tag that invokes your loader.  
*
* When using the scriptLoader the enabledModules loader code
*  is transcluded into base mwEmbed class include.  
*/
var mwEnabledModuleList = [
	'AddMedia',
	'ClipEdit',
	'EmbedPlayer',
	'ApiProxy',
	'Sequencer',
	'TimedText'
];

/**
* mwEmbed default config values.  
*/  	
mw.setDefaultConfig ( {
	// Default coreComponents: 
	'coreComponents' : mwCoreComponentList,

	// Default enabled modules: 
	'enabledModules' : mwEnabledModuleList, 
	
	// Default jquery ui skin name
	'jQueryUISkin' : 'jqueryUiRedmond',	
	
	/**
	* If jQuery / mwEmbed should be loaded.
	*
	* This flag is automatically set to true if: 
	*  Any script calls mw.ready ( callback_function )
	*  Page DOM includes any tags set in config.rewritePlayerTags at onDomReady 
	*  ( embedPlayer module )
	*
	* This flag increases page performance on pages that do not use mwEmbed 
	* and don't already load jQuery 
	*
	* For example when including the mwEmbed.js in your blog template 
	* mwEmbed will only load extra js on blog posts that include the video tag.
	*
	* NOTE: Future architecture will probably do away with this flag and refactor it into 
	* a smaller 'remotePageMwEmbed.js' script similar to ../remoteMwEmbed.js
	*/ 
	'runSetupMwEmbed' : false,	

	// The mediaWiki path of mwEmbed  
	'mediaWikiEmbedPath' : 'js/mwEmbed/',
	
	// Api actions that must be submitted in a POST, and need an api proxy for cross domain calls
	'apiPostActions': [ 'login', 'purge', 'rollback', 'delete', 'undelete',
		'protect', 'block', 'unblock', 'move', 'edit', 'upload', 'emailuser',
		'import', 'userrights' ],
	
	//If we are in debug mode ( results in fresh debug javascript includes )
	'debug' : false,
	
	// Default request timeout ( for cases where we include js and normal browser timeout can't be used )
	// stored in seconds
	'defaultRequestTimeout' : 30,
	
	// Default user language is "en" Can be overwritten by: 
	// 	"uselang" url param 
	// 	wgUserLang global  
	'userLanguage' : 'en',
	
	// Set the default providers ( you can add more provider via {provider_id}_apiurl = apiUrl	  
	'commons_apiurl' : 'http://commons.wikimedia.org/w/api.php'
} );

/**
* --  Load Class Paths --
* 
* PHP AutoLoader reads this loader.js file along with 
* all the "loader.js" files to determine script-loader 
* class paths
* 
*/

// Set the loaderContext for the classFiles paths call:  
mw.setConfig( 'loaderContext', '' );

/**
 * Core set of mwEmbed classes:
 */
mw.addClassFilePaths( {
	"mwEmbed"				: "mwEmbed.js",
	"window.jQuery"			: "jquery/jquery-1.4.2.js",		
	
	"mw.Language"			: "languages/mw.Language.js",
	"$j.replaceText.js"		: "jquery/plugins/jquery.replaceText.js",
	
	"$j.fn.menu" 			: "jquery/plugins/jquery.menu/jquery.menu.js",
	"mw.style.jquerymenu" 	: "jquery/plugins/jquery.menu/jquery.menu.css",
	
	"$j.fn.pngFix"			: "jquery/plugins/jquery.pngFix.js",
	"$j.fn.autocomplete"	: "jquery/plugins/jquery.autocomplete.js",
	"mw.style.autocomplete"	: "jquery/plugins/jquery.autocomplete.css",
	
	"$j.fn.hoverIntent"		: "jquery/plugins/jquery.hoverIntent.js",
	"$j.fn.datePicker"		: "jquery/plugins/jquery.datePicker.js",
	"$j.ui"					: "jquery/jquery.ui/ui/ui.core.js",	
	
	"mw.style.jqueryUiRedmond" : "jquery/jquery.ui/themes/redmond/jquery-ui-1.7.1.custom.css",
	"mw.style.jqueryUiSmoothness"	: "jquery/jquery.ui/themes/smoothness/jquery-ui-1.7.1.custom.css",
	"mw.style.mwCommon"		: "skins/common/common.css",
	
	"mw.testLang"			:  "tests/testLang.js",		

	"$j.cookie"				: "jquery/plugins/jquery.cookie.js",
	"$j.contextMenu"		: "jquery/plugins/jquery.contextMenu.js",
	"$j.fn.suggestions"		: "jquery/plugins/jquery.suggestions.js",
	"$j.fn.textSelection" 	: "jquery/plugins/jquery.textSelection.js",
	"$j.browserTest"		: "jquery/plugins/jquery.browserTest.js",
	"$j.fn.jWizard"			: "jquery/plugins/jquery.jWizard.js",
	"$j.fn.tipsy"			: "jquery/plugins/jquery.tipsy.js",
	"mw.style.tipsy"		: "jquery/plugins/jquery.tipsy.css",

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

// For now just the special case of $j.ui 
mw.addClassStyleDependency( {
	'$j.ui' : ( 'mw.style.' + mw.getConfig( 'jQueryUISkin' ) )	
} );

