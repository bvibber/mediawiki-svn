/*
* Loader for libAddMedia module:
*/
mw.addMessages( {
	"mwe-loading-add-media-wiz" : "Loading add media wizard"
});

// Add class file paths ( From ROOT )
mw.addClassFilePaths( {
	"$j.fn.dragDropFile"	: "modules/AddMedia/jquery.dragDropFile.js",	
	"$j.fn.simpleUploadForm": "modules/AddMedia/jquery.simpleUploadForm.js",
	
	"mw.BaseUploadInterface": "modules/AddMedia/mw.BaseUploadInterface.js",
	"mw.Firefogg"			: "modules/AddMedia/mw.Firefogg.js",
	"mw.FirefoggGUI"		: "modules/AddMedia/mw.FirefoggGUI.js",
	"mw.FirefoggRender"		: "modules/libSequencer/mw.FirefoggRender.js",
	"mw.RemoteSearchDriver"	: "modules/AddMedia/mw.RemoteSearchDriver.js",	
	
	"baseRemoteSearch"		: "modules/AddMedia/searchLibs/baseRemoteSearch.js",
	"mediaWikiSearch"		: "modules/AddMedia/searchLibs/mediaWikiSearch.js",
	"metavidSearch"			: "modules/AddMedia/searchLibs/metavidSearch.js",
	"archiveOrgSearch"		: "modules/AddMedia/searchLibs/archiveOrgSearch.js",
	"flickrSearch"			: "modules/AddMedia/searchLibs/flickrSearch.js",
	"baseRemoteSearch"		: "modules/AddMedia/searchLibs/baseRemoteSearch.js"
});

/**
* Note: We should move relevant parts of these style sheets to the addMedia/css folder 
* phase 2: We should separate out sheet sets per sub-module:
*/ 
var addMediaSheets = [
	mw.getConfig( 'jquery_skin_path' ) + 'jquery-ui-1.7.1.custom.css', 
	mw.getMwEmbedPath() + 'skins/' + mw.getConfig( 'skinName' ) + '/styles.css' 	
]

//Setup the addMediaWizard module
mw.addModuleLoader( 'AddMedia.addMediaWizard', function( callback ){
	// Get addMedia style sheets
	mw.getStyleSheet( addMediaSheets );
	// Load all the required libs:
	mw.load( [
		[	'mw.RemoteSearchDriver',
			'$j.cookie',
			'$j.fn.textSelection',
			'$j.ui'
		], [
			'$j.ui.resizable',
			'$j.ui.draggable',
			'$j.ui.dialog',
			'$j.ui.tabs',
			'$j.ui.sortable'
		]
	], function() {
		callback( 'AddMedia.addMediaWizard' );
	} );
});

/*
* Upload interface loader: 
*/

mw.addModuleLoader( 'AddMedia.baseUploadInterface', function( callback ){
	mw.load( [
		[
			'mw.BaseUploadInterface',
			'$j.ui'
		],
		[
			'$j.ui.progressbar',
			'$j.ui.dialog'
		]
	], function() {
		callback( 'AddMedia.baseUploadInterface' );
	});
});

/**
 * The Firefogg loaders
 *
 * Includes both firefogg & firefogg "GUI" which share some loading logic: 
 */ 
var mwBaseFirefoggReq = [
	[
		'mw.BaseUploadInterface',
		'mw.Firefogg',
		'$j.ui'
	],
	[
		'$j.ui.progressbar',
		'$j.ui.dialog',
		'$j.ui.draggable'
	]
];

mw.addModuleLoader( 'AddMedia.firefogg', function( callback ){
	// Get addMedia style sheets
	mw.getStyleSheet( addMediaSheets );
	//Load firefogg libs
	mw.load( mwBaseFirefoggReq, function() {
		callback( 'AddMedia.firefogg' );
	});
} );

mw.addModuleLoader( 'AddMedia.FirefoggGUI', function( callback ){
	// Get addMedia style sheets
	mw.getStyleSheet( addMediaSheets );
	
	//Clone the array: 
	var request = mwBaseFirefoggReq.slice( 0 ) ;	
	//Add firefogg gui classes to a new "request" var: 
	request.push( [
		'mw.FirefoggGUI',
		'$j.cookie',
		'$j.ui.accordion',
		'$j.ui.slider',
		'$j.ui.datepicker'
	] );
	
	mw.load( request, function() {
		callback( 'AddMedia.FirefoggGUI' );
	});
} );

mw.addModuleLoader( 'AddMedia.firefoggRender', function( callback ){
	mw.load( [
		'mw.BaseUploadInterface',
		'mw.Firefogg',
		'mw.FirefoggRender'
	], function() {
		callback( 'AddMedia.firefoggRender' );
	});	
});

	/*
		var queuedFirefoggConf = { };		
		// Take an input player as the selector and expose basic rendering controls
		$.fn.firefoggRender = function( options, callback ) {			
			// Check if we already have render loaded then just pass on updates/actions
			var sElm = $j( this.selector ).get( 0 );
			//add a special attribute to the selector: 
			if ( sElm['fogg_render'] ) {
				if ( sElm['fogg_render'] == 'loading' ) {
					mw.log( "Error: called firefoggRender while loading" );
					return false;
				}
				// Call or update the property:
			}
			sElm['fogg_render'] = 'loading';
			// Add the selector
			options['player_target'] = this.selector;
			mw.load( [
				'mvBaseUploadInterface',
				'mvFirefogg',
				'mvFirefoggRender'
			], function() {
				// Attach the firefoggRender obj to the selected elm: 
				sElm['fogg_render'] = new mvFirefoggRender( options );
				if ( callback && typeof callback == 'function' )
					callback( sElm['fogg_render'] );
			} );
		}

		$.fn.baseUploadInterface = function( options ) {
			mw.load( [
				[
					'mvBaseUploadInterface',
					'$j.ui'
				],
				[
					'$j.ui.progressbar',
					'$j.ui.dialog'
				]
			], function() {
				myUp = new mvBaseUploadInterface( options );
				myUp.setupForm();
			} );
		}
		
	
			// Check if we already have Firefogg loaded (the call just updates the element's
			// properties)			
			var sElm = $j( this.selector ).get( 0 );
			if ( sElm['firefogg'] ) {
				if ( sElm['firefogg'] == 'loading' ) {
					mw.log( "Queued firefogg operations ( firefogg " +
						"not done loading ) " );
					$j.extend( queuedFirefoggConf, options );
					return false;
				}
				// Update properties
				for ( var i in options ) {
					mw.log( "firefogg::updated: " + i + ' to ' + options[i] );
					sElm['firefogg'][i] = options[i];
				}
				return sElm['firefogg'];
			} else {
				// Avoid concurrency
				sElm['firefogg'] = 'loading';
			}
*/