/**
* TimedText loader.    
*/
mw.addClassFilePaths( {
	"mw.TimedText" : "modules/TimedText/mw.TimedText.js",
	"mw.style.TimedText" : "modules/TimedText/css/mw.style.TimedText.css",
		
	"mw.TimedTextEdit" : "modules/TimedText/mw.TimedTextEdit.js",
	"mw.style.TimedTextEdit" : "modules/TimedText/css/mw.style.TimedTextEdit.css",
	
	"RemoteMwTimedText" : "modules/TimedText/remotes/RemoteMwTimedText.js"
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