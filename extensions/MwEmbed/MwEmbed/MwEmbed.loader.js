/**
*
* Core "loader.js" for mwEmbed
*/

/**
* mwEmbed default config values.  
*/  	
mw.setDefaultConfig ( {
	// Default coreComponents: 
	'coreComponents' : mwCoreComponentList,

	// Default enabled modules: 
	'enabledModules' : mwEnabledModuleList, 
	
	// Default jquery ui skin name
	'jQueryUISkin' : 'redmond',

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
	'commons_apiurl' : 'http://commons.wikimedia.org/w/api.php',
	
	// Set the default loader group strategy
	'loader.groupStrategy' : 'module',
		
	// Default appendJS string ( not used outside of wikimedia gadget system ) 
	'Mw.AppendWithJS' : false			
} );



