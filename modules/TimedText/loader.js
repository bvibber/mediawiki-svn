/**
* TimedText loader.    
*/
mw.addClassFilePaths( {
	"mw.TimedText" : "modules/TimedText/mw.TimedText.js",	
	"mw.TimedTextEdit" : "modules/TimedText/mw.TimedTextEdit.js",
	"mw.style.TimedTextEdit" : "modules/TimedText/css/mw.TimedTextEdit.css",
	
	"$j.fn.menu" : "modules/TimedText/jquery.menu/jquery.menu.js",
	"mw.style.jquerymenu" : "modules/TimedText/jquery.menu/jquery.menu.css",
	
	
	"RemoteMwTimedText" : "modules/TimedText/remotes/RemoteMwTimedText.js"
});

// TimedText module
mw.addModuleLoader( 'TimedText', function( callback ) {
	mw.load( [ 
		'$j.fn.menu', 
		'mw.TimedText',
		"mw.style.jquerymenu"
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