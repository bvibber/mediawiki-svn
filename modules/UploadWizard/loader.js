/*
* Loader for UploadWizard module:
*/

// Scope everythign in "mw"  ( keeps the global namespace clean ) 
( function( mw ) {

	mw.addMessages( {
		"mwe-loading-upwiz" : "Loading upload wizard"
	});
	
	// Add class file paths ( From ROOT )
	mw.addClassFilePaths( {
		"mw.LanguageUpWiz"			: "modules/UploadWizard/mw.LanguageUpWiz.js",
		"mw.UploadWizard"		: "modules/UploadWizard/mw.UploadWizard.js",
		"mw.style.uploadWizard" : "modules/UploadWizard/css/uploadWizard.css",
		
		"mw.UploadApiProcessor"		: "modules/UploadWizard/mw.UploadApiProcessor.js",
		"mw.IframeTransport"		: "modules/UploadWizard/mw.IframeTransport.js",
		"mw.ApiUploadHandler"		: "modules/UploadWizard/mw.ApiUploadHandler.js",
		"mw.DestinationChecker"		: "modules/UploadWizard/mw.DestinationChecker.js",

		"mw.MockUploadHandler"		: "modules/UploadWizard/mw.MockUploadHandler.js"			
		
	});	
	
	//Set a variable for the base upload interface for easy inclution
	// 
	// var baseUploadlibraries = [
	// 	[
	// 		'mw.UploadHandler',
	// 		'mw.UploadInterface',
	// 		'$j.ui'
	// 	],
	// 	[
	// 		'$j.ui.progressbar',
	// 		'$j.ui.dialog',
	// 		'$j.ui.draggable',
	// 		'$j.fn.autocomplete'
	// 	]
	// ];
	// 	
	// var mwBaseFirefoggReq = baseUploadlibraries.slice( 0 )
	// mwBaseFirefoggReq[0].push('mw.Firefogg');
	// 

	var libraries = [ 
		[
			'$j.ui',
			'$j.ui.progressbar',
			'$j.ui.dialog',
			'$j.ui.draggable',			
			'$j.ui.datepicker',
			'$j.effects.core',
			'$j.effects.slide',
			'$j.fn.autocomplete',
			'mw.style.autocomplete'
		],
		[
			'mw.LanguageUpWiz',
			'mw.IframeTransport',
			'mw.ApiUploadHandler',
			'mw.DestinationChecker',
			'mw.UploadWizard',
			'mw.style.uploadWizard'
		],
	];

	var testLibraries = libraries.slice( 0 )
	testLibraries.push( [ 'mw.MockUploadHandler' ] );
 
	/**
	* Note: We should move relevant parts of these style sheets to the addMedia/css folder 
	* phase 2: We should separate out sheet sets per sub-module:
	*/ 
	
	mw.addModuleLoader( 'UploadWizard.UploadWizard', function( callback ) {
		//Clone the array: 
		//var request = mwBaseFirefoggReq.slice( 0 ) ;
		
		//Add uploadwizard classes to a new "request" var: 
		//request.push( libraries );
		
		mw.load( libraries, function() {
			callback( 'UploadWizard.UploadWizard' );
		} );

	} );
	
	mw.addModuleLoader( 'UploadWizard.UploadWizardTest', function( callback ) {
		//Clone the array: 
		//var request = mwBaseFirefoggReq.slice( 0 ) ;
	 	
		//Add uploadwizard classes to a new "request" var: 
		//request.push( testLibraries );
		//debugger;
	
		mw.load( testLibraries, function() {
			callback( 'UploadWizard.UploadWizardTest' );
		} );

	} );

} )( window.mw );
