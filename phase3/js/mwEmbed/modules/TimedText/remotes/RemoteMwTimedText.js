/**
* Stop-gap for mediaWiki timed text support 
*
* Does some tranformations to normal wiki timed Text pages to make them look
* like the php output that we will eventually want to support 
*/
mw.addMessages( {
	"mwe-language-subtiles-for-clip": "$1 subtitles for clip: $2",
	"mwe-language-no-subtiles-for-clip": "No $1 subtitles where found for clip: $2"
});

RemoteMwTimedText = {	
	
	embedByTitle: function( fileTitle, callback ){
		//Get all the embed details: 
		var request = {
			'titles' : 'File:' + fileTitle,
			'prop' : 'imageinfo|revisions',
			'iiprop' : 'url|mime|size',
			'iiurlwidth' : mw.getConfig( 'video_size').split('x').pop(),
			'rvprop' : 'content'
		}
		// (only works for commons right now) 
		mw.getJSON( request, function( data ) {
			// check for redirect
			for ( var i in data.query.pages ) {
				var page = data.query.pages[i];
				if ( page.revisions[0]['*'] && page.revisions[0]['*'].indexOf( '#REDIRECT' ) === 0 ) {
					var re = new RegExp( /[^\[]*\[\[([^\]]*)/ );
					var pt = page.revisions[0]['*'].match( re );
					if ( pt[1] ) {
						mw.log( 'found redirect tyring: ' + pt[1] )
						RemoteMwTimedText.embedByTitle( pt[1], callback);
						return ;
					} else {
						mw.log( 'Error: addByTitle could not proccess redirect' );
						callback( false );
						return false;
					}
				}
				mw.log("should proccess data result");
				// Else process the result
				embedAttributes = RemoteMwTimedText.getEmbedAttributes( page );			 
				callback( embedAttributes );
			}
		} );
	},
	
	/**
	* Get the embed code from response resource and sends it a callback
	*/
	getEmbedAttributes: function( page ){
		return {					
				'wikiTitleKey' : page.title.replace(/File:/ig, '' ),
				'link'		 : page.imageinfo[0].descriptionurl,					
				'poster'	 : page.imageinfo[0].thumburl,
				'src'		 : page.imageinfo[0].url,					
				'width' : page.imageinfo[0].width,
				'height': page.imageinfo[0].height,
			};	
	}
}

mw.ready( function(){
	// Check page type 
	if( wgAction == 'view' ){		
		var fileTitleKey = wgTitle.split('.');
		var extension = fileTitleKey.pop();
		var langKey = fileTitleKey.pop();
		fileTitleKey = fileTitleKey.join('.');		
		
		RemoteMwTimedText.embedByTitle( fileTitleKey,  function( embedAttributes ){		
			mw.load( 'EmbedPlayer', function(){
				if( wgArticlePath ){
					var $fileLink = $j('<div>').append(
						$j('<a>').attr({
							'href' : wgArticlePath.replace( '$1', 'File:' + fileTitleKey)
						})
						.text( fileTitleKey.replace('_', ' ') )
					)
				}													
				// Add the embed code: ( jquery wraping of "video" fails )
				$j('#bodyContent').append(
					$j( '<div class="videoLoading">').html(
					'<video id="timed-text-player-embed" '+ 					 
					 'class="kskin" ' +  //We need to centrally store this config somewhere
					 'poster="' + embedAttributes.poster + '" ' +
					 'src="' + embedAttributes.src + '" ' + 
					 'wikiTitleKey="' + embedAttributes.wikiTitleKey + '" >' +					 
					 '</video><br><br><br><br>'					
					)
				);				
				$j('.videoLoading').hide();
				// Rewrite the player (any video tags on the page) 
				$j('#timed-text-player-embed').embedPlayer( function(){
					//Select the timed text for the page: 
					
					//remove the loader
					$j('.loading_spinner').remove();
					
					var player = $j('#timed-text-player-embed').get(0);
					
				
					if( !player.timedText ){
						mw.log("Error: no timedText method on embedPlayer" );
						return ;
					}
					//Make sure the timed text sources are loaded: 
					player.timedText.setupTextSources( function(){
						
						var source = player.timedText.getSourceByLanguage( langKey );
						var pageMsgKey = 'mwe-language-subtiles-for-clip';
						if( ! source ){
							pageMsgKey = "mwe-language-no-subtiles-for-clip"
						}
						//Add the page msg to the top 
						$j('#bodyContent').prepend(
							$j('<h3>')
								.html(  
									gM(pageMsgKey, [ unescape(  mw.languages[ langKey ] ),  $fileLink.html() ] ) 
								)
						);							
						//Select the language if possible: 
						if( source ){						
							player.timedText.selectTextSource( source );
						}					
						// Un-hide the player  
						$j('.videoLoading').show();
					});
				
				});
			} );
			 
		} );				
	}
} );