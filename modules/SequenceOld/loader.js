/*
* Sequencer loader
*/

// Wrap in mw to not pollute global namespace
( function( mw ) {
	mw.addResourcePaths( {
		
		"mw.SequenceEdit"			: "mw.SequenceEdit.js",
		"mw.SeqRemoteSearchDriver" : "mw.SeqRemoteSearchDriver.js",	
		"mw.TimedEffectsEdit"	: "mvTimedEffectsEdit.js",
		"mw.FirefoggRender"		: "mw.FirefoggRender.js",
		
		"RemoteMwSequencer" :	"remotes/RemoteMwSequencer.js",
		
		"mw.style.SequenceEdit" : "css/mw.style.SequenceEdit.css",
		
		"playlistEmbed" : "playlistEmbed.js"
	} );
	
	
	mw.addModuleLoader( 'FirefoggRender', 
		[
			'mw.Firefogg', 
			'mw.FirefoggRender',
			'mw.UploadInterface'
		]);
		
	// xxx Needs to fix sequencer include
	mw.addModuleLoader( 'Sequencer', function( ) {		
		// Make sure we have the required mwEmbed libs:			
		return [
			[	// Load the EmbedPlayer Module ( includes lots of dependent classes )   
				'EmbedPlayer'
			],		
			[										
				'$j.contextMenu',
				'mw.SequenceEdit',
				'mw.style.SequenceEdit'
				'$j.fn.menu', 
			],
			[
				// UI components used in the sequencer interface: 				
				'$j.ui.accordion',
				'$j.ui.dialog',
				'$j.ui.droppable',
				'$j.ui.draggable',
				'$j.ui.progressbar',
				'$j.ui.sortable',
				'$j.ui.resizable',
				'$j.ui.slider',
				'$j.ui.tabs'
			]
		];	
	});
	
} )( window.mw );