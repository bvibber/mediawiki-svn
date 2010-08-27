/**
* Stop-gap for php sequencer support does some transformations 
* to page views to support sequence namespace
* 
* Supports basic "sequencer" functionality as a javascript rewrite system.
*/

mw.addMessageKeys( [
	"mwe-sequencer-no-sequence-create",
	"mwe-sequencer-create-sequence",
	"mwe-sequencer-edit-sequence",
	"mwe-sequencer-embed-sequence",
	"mwe-sequencer-embed-sequence-desc",
	"mwe-sequencer-loading-sequencer",
	
	"mwe-sequencer-not-published",
	"mwe-sequencer-published-out-of-date"
]);

mw.MediaWikiRemoteSequencer = function( options ) {
	return this.init( options ); 
};
mw.MediaWikiRemoteSequencer.prototype = {
	/**
	* @constructor
	* @param {Object} options RemoteMwSequencer options
	*/
	init: function( options ) {
		this.action = ( options.action )? options.action : this.action;
		this.title = ( options.title )? options.title : this.title;
		this.target = ( options.target )? options.target : this.target;
	},
	
	drawUI: function() {		
		// Check page action 
		if( this.action == 'view' ) {	
			this.showViewUI();
		}	
	},
	/**
	* Check page for sequence
	* if not present give link to "create" one. 
	*/
	showViewUI: function() {
		var _this = this;
		if( wgArticleId == 0 ) {
			// Update create button 
			$j('#ca-edit span a')
				.text( gM('mwe-sequencer-create-sequence' ))
				.click(function(){
					_this.showEditor();
					return false;
				})
			
			$j( this.target ).html(
				gM("mwe-sequencer-no-sequence-create", 
					$j('<a />').attr('href','#').click(function() {
						_this.showEditor();
						return false;
					})
				)
			);
		} else {						
			// Update edit button 
			$j('#ca-edit span a')
				.text( gM('mwe-sequencer-edit-sequence' ))
				.click(function(){
					_this.showEditor();
					return false;
				})
			
			_this.displayPlayerEmbed();							
		}
	},	
	getSequenceFileKey: function( wgPageName ){
		return 'File:' + wgPageName.replace( 'Sequence:', 'Sequence-') + '.ogv';
	},
	displayPlayerEmbed: function(){
		var _this = this;
		// load the embedPlayer module: 
		mw.load('EmbedPlayer', function(){
			// Check if the sequence has been flattened and is up to date:
			var request = {
				'action': 'query',
				'titles':  _this.getSequenceFileKey( wgPageName ),
				'prop': 'imageinfo|revisions',
				'iiprop': 'url|metadata',			
				'iiurlwidth': '400',		
				'redirects' : true // automatically follow redirects
			};
			var $embedPlayer = $j('<div />'); 
			mw.getJSON( request, function( data ){
				if(!data.query || !data.query.pages || data.query.pages[-1]){
					// no flattened file found
					$embedPlayer.append(
						$j( '<div />').append( 
							gM('mwe-sequencer-not-published')
						)
						.addClass( 'ui-state-highlight' )
					)
					return ;
				}			
				for( var pageId in data.query.pages) {
					var page = data.query.pages[ pageId ];
					
					// Check that the file has a later revision than the
					// page. ( up to date sequences always are later than 
					// the revision of the page saved ).
					if( page.revisions && page.revisions[0] ){
						if( page.revisions[0].revid < wgCurRevisionId ){
							// flattened file out of date
							$embedPlayer.append(
								$j('<div />').append( 
									gM('mwe-sequencer-published-out-of-date')
								).addClass( 'ui-state-highlight' )
							)
						}
					}
					if( page.imageinfo && page.imageinfo[0] ){
						var imageinfo = page.imageinfo[0];
						var duration = 0;
						for( var i=0;i< imageinfo.metadata.length; i++){
							if( imageinfo.metadata[i].name == 'length' ){
								duration = Math.round( 
									imageinfo.metadata[i].value * 1000 
								) / 1000;
							}
						}
						// Append a player to the embedPlayer target 
						// -- special title key sequence name bound
						$embedPlayer.append( 
							$j('<video />')
							.attr({				
								'id' : 'embedSequencePlayer',
								'poster' : imageinfo.thumburl,
								'durationHint' : duration,
								'apiTitleKey' : page.title.replace('File:',''),							
							})
							.addClass('kskin')
							.css({
								'width': imageinfo.thumbwidth,
								'height' : imageinfo.thumbheight
							})
							.append(
								// ogg source
								$j('<source />')
								.attr({
									'type': 'video/ogg',
									'src' : imageinfo.url
								})	
							)
						)
					}
				} 
				// Display embed sequence
				$j( _this.target ).empty().append(
					$j('<div />')
					.addClass( 'sequencer-player')
					.css( {
						'float' : 'left',
						'width' : imageinfo.thumbwidth
					})
					.append( 
						$embedPlayer			
					)
					,
					
					// Embed player
					$j('<div />')
					.addClass( 'sequencer-embed-helper')
					.css({
						'margin-left': '430px' 
					})
					
					// Text embed code
					.append( 
						$j('<h3 />')
						.text( gM('mwe-sequencer-embed-sequence') )
						,
						$j('<span />' )
						.text( gM('mwe-sequencer-embed-sequence-desc') )
						,
						$j('<br />'),
						$j('<textarea />')
						.css({
							'width' : '100%',
							'height' : '200px'
						}).focus(function(){
							$j(this).select();
						})
						.append( 
							_this.getSequenceEmbedCode()
						)
					),
					
					// Add a clear both to give content body height
					$j('<div />').css({'clear': 'both'})
				)
				// Rewrite the player
				$j('#embedSequencePlayer').embedPlayer();				
			}); // load json player data			
		})		
	},
	getSequenceEmbedCode: function(){
		return 'embed code here';
	},	
	showEditor: function(){
		var _this = this;
		
		$j('body').append( 
			$j('<div />')
			.attr('id',"edit_sequence_container")
			.css({
				'position' : 'absolute',
				'font-size' : '.8em',
				'top' : '5px',
				'bottom' : '5px',
				'left' : '5px',
				'right' : '5px',	
				'background': '#FFF'
			})			
			.append(
				$j('<div />').append( 
					gM('mwe-sequencer-loading-sequencer'),
					$j('<span />').loadingSpinner()
				)
				.css( {'width':'200px', 'margin':'auto'})
			)
		)
		
		mw.load( 'Sequencer', function(){ 	 		
			// Send a jquery ui style destroy command
			$j('#edit_sequence_container').sequencer( 'destroy');
			$j('#edit_sequence_container').sequencer({
				// The title for this sequence:
				title : _this.getTitle(),
				// If the sequence is new
				newSequence : ( wgArticleId == 0 ),
				// Server config:
				server: {
					'type' : 'mediaWiki',
					'url' : _this.getApiUrl(),
					'titleKey' : wgPageName,			
				},
	    		// Set the add media wizard to only include commons:   
	    		addMedia : {
	    			 'enabled_providers':[ 'wiki_commons' ],	    			 
	    			 'default_query' : _this.getTitle()	    			 
	    		},
	    		// Function called on sequence exit 
	    		onExitCallback: function( sequenceHasChanged ){	    			
	    			if( sequenceHasChanged ){
	    				window.location.reload();
	    			}
	    			// else do nothing
	    		}
			});
		});
	},
	
	getApiTitleKey: function(){
		return wgTitle;
	},
	getTitle: function(){
		return wgTitle.replace( 'Sequence:', '').replace('_', ' ');
	},
	// Get the api url ( for now use whatever the page context is ) 
	getApiUrl: function(){
		return mw.absoluteUrl( wgScript.replace('index.php', 'api.php') );
	}
	
	// Check page type 
	
	// "view" page 	
	
	// set page content to "loading"
	// get wikitext of page via api
	// grab xml
	// update page with sequence and 
	
	
	//"edit" page
	// grab textbox text, 
	// set page to loading
	// display sequence editor in "body" with -> full-screen link
};	//Setup the remote configuration
	