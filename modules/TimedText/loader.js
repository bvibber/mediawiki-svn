/**
* TimedText loader.    
*/
mw.addClassFilePaths( {
	"mw.TimedText" : "modules/TimedText/mw.TimedText.js",
	"mw.TimedTextEdit" : "modules/TimedText/mw.TimedTextEdit.js",
	"$j.fn.menu" : "modules/TimedText/jquery.menu.js" 
});

//Add css dependency: 
mw.addClassStyleSheets( {
	"$j.fn.menu" 	: "modules/TimedText/jquery.menu.css",
	"mw.TimedTextEdit": "modules/TimedText/mw.TimedTextEdit.css"
});

// TimedText module
mw.addModuleLoader( 'TimedText', function( callback ){
	mw.load( [ '$j.fn.menu', 'mw.TimedText' ], function(){
		callback( 'TimedText' );
	} );
});

// TimedText editor: 
mw.addModuleLoader( 'TimedText.Edit', function( callback ){
	mw.load([ 
		[
			'$j.ui',
			'mw.TimedTextEdit'
		],
		[
			'$j.ui.dialog',
			'$j.ui.tabs'
		]
	], function( ){
		callback( 'TimedText.Edit' );
	});
});