/**
* TimedText loader.    
*/
mw.addClassFilePaths( {
	"mw.TimedText" : "mw.TimedText.js",
	"mw.style.TimedText" : "css/mw.style.TimedText.css",
		
	"mw.TimedTextEdit" : "mw.TimedTextEdit.js",
	"mw.style.TimedTextEdit" : "css/mw.style.TimedTextEdit.css",
	
	"RemoteMwTimedText" : "remotes/RemoteMwTimedText.js"
});

// TimedText module
mw.addModuleLoader( 'TimedText', function( callback ) {
	mw.load( [ 
		'$j.fn.menu', 
		'mw.TimedText',
		'mw.style.TimedText',
		'mw.style.jquerymenu'
	], function() {
		callback( 'TimedText' );
	} );
});

// TimedText editor: 
mw.addModuleLoader( 'TimedText.Edit', function( callback ) {
	mw.load([ 
		[
			'$j.ui',
			'$j.fn.menu', 
			"mw.style.jquerymenu",
			
			'mw.TimedText',
			'mw.style.TimedText',
			
			'mw.TimedTextEdit',
			'mw.style.TimedTextEdit'
		],
		[
			'$j.ui.dialog',
			'$j.ui.tabs'
		]
	], function( ) {
		callback( 'TimedText.Edit' );
	});
});