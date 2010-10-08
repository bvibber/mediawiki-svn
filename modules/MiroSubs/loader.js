/**
* Miro Sub module
*/

// Wrap in mw to not pollute global namespace
( function( mw ) {
	mw.addMessages( {
		"mwe-mirosubs-add-universal-subtitles" :   "New subtitles editor",
		"mwe-mirosubs-loading-universal-subtitles" : "Loading <i>universal subtitles</i> editor"
	});
	// add as loader depenency  'mw.style.mirosubsMenu' 
	
	mw.addResourcePaths( {
		"goog" : 'mirosubs/base.min.js',
		"mirosubs" : "mirosubs/mirosubs-api.min.js",
		"mw.MiroSubsConfig" : 'mw.MiroSubsConfig.js',
		"mw.style.mirosubsMenu" : 'css/mw.style.mirosubsMenu.css'
	});
	
	mw.setDefaultConfig( {
		'MiroSubs.EnableMiroEditor': true
	})
	
	mw.addModuleLoader( 'MiroSubs', function(){
		var resourceList = [ "mirosubs", "mw.MiroSubsConfig" ];
		return resourceList;
	});
	$j( mw ).bind( 'newEmbedPlayerEvent', function( event, embedPlayer ) {
		$j( embedPlayer ).bind( 'TimedText.BuildCCMenu', function( event, langMenu ){
			// load the miro subs menu style ( will be part of the loader dependency later on) 
			mw.load(  'mw.style.mirosubsMenu'  );
			$j( langMenu ).append( 
				$j.getLineItem( gM( 'mwe-mirosubs-add-universal-subtitles'), 'mirosubs', function() {					
					// Show loader
					mw.addLoaderDialog( gM('mwe-mirosubs-loading-universal-subtitles') );
					
					// Load miro subs:
					mw.load( 'MiroSubs', function(){			
						
						mw.MiroSubsConfig.getConfig( embedPlayer , function( config ){ 
							// Hide loader
							mw.closeLoaderDialog();
							
							// Give the browser time to update
							setTimeout( function(){
								// Show the dialog		
								mirosubs.api.openDialog( config );
							}, 200);
						});
					})
					return false;
				})
			);
		});
	});
		
	
} )( window.mw );