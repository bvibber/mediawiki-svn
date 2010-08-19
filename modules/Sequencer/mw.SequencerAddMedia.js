/**
* Sequencer add media support ( ties into mwEmbed AddMedia Module )
*/

//Wrap in mw closure
( function( mw ) {
	
mw.SequencerAddMedia = function( sequencer ) {
	return this.init( sequencer );
};

// Set up the mvSequencer object
mw.SequencerAddMedia.prototype = {
	
	init: function( sequencer ){
		this.sequencer = sequencer;
	},
	
	// Get the menu widget that drives the search and upload tab selection
	getMenuWidget: function(){
		var _this = this;
		var widgetFocus = false;
		return $j('<span />')
			.append( 
				$j('<form />').append( 
					$j('<input />')
					.addClass( 'searchMedia')
					.val(
						gM( 'mwe-sequencer-url-or-search')
					)
					.css({'color': '#888', 'zindex': 2})
					.focus( function(){
						// on the first focus clear the input and update the color
						if( !widgetFocus ){
							$j(this)
							.css('color', '#000')
							.val('');		
						}
						widgetFocus = true;
					})
					.click(function(){
						$j(this).focus();						
					})
					// add the sequencer input binding
					.sequencerInput(  _this.sequencer  )
				)
				.css({
					'width': '125px',
					'display': 'inline'
				})
				.submit(function(){
					_this.proccessRequest();
					return false;
				}),
				
				// Input button
				$j.button({				
					// The text of the button link
					'text' : gM('mwe-sequencer-get-media'),				
					// The icon id that precedes the button link:
					'icon_id' : 'plus' 
				})
				.click(function(){
					// only do the search if the user has given the search input focus  
					if( widgetFocus ){
						_this.proccessRequest();
					}
					// don't follow the button link
					return false;					
				})
			)
	}, 
	proccessRequest: function(){
		var _this = this;
		// get the input text
		var inputValue = this.sequencer.getMenuTarget().find('input.searchMedia').val();
		
		this.sequencer.getEditToolTarget()
			.empty()
			.loadingSpinner();
		
		if( ! _this.remoteSearchDriver ){
			// set the tool target to loading
			mw.load( 'AddMedia.addMediaWizard', function(){
				_this.remoteSearchDriver = new mw.RemoteSearchDriver({
					'target_container' : _this.sequencer.getEditToolTarget(),
					'target_search_input' : _this.sequencer.getMenuTarget().find('input.searchMedia'),					
					'displaySearchInput': false,
					'default_query' : inputValue,
					'displayResourceInfoIcons' : false,
					'resourceSelectionCallback' : function( resource ){
						mw.addLoaderDialog( gM( 'mwe-sequencer-loading-asset' ) );
						// Get convert resource to smilClip and insert into the timeline
						_this.getSmilClipFromResource( resource, function( smilClip ) {
							_this.sequencer.getTimeline().insertSmilClipEdit( smilClip );
							mw.closeLoaderDialog();
						});						
						return false;
					},
					'displaySearchResultsCallback' : function(){
						_this.addSearchResultsDrag();
					}
				});
				// Create the search user interface: 
				_this.remoteSearchDriver.createUI();		
			});
		} else {
			this.remoteSearchDriver.createUI()
		}		
	
	},	
	/**
	 * Get the resource object from a provided asset
	 */
	getResourceFromAsset: function( asset ){
		var _this = this;
		if( ! $j( asset ).attr('id') ){
			mw.log( "Error getResourceFromAsset:: missing asset id" +  $j( asset ).attr('id') );
			return false;
		}
		return _this.remoteSearchDriver.getResourceFromId( $j( asset ).attr('id') );
	},	
	
	/**
	 * Add search results drab binding so they can be dragged into the sequencer
	 */
	addSearchResultsDrag: function(){
		var _this = this;
		// Bind all the clips: 
		this.sequencer.getEditToolTarget()
			.find(".rsd_res_item")
			.draggable({
				connectToSortable: $j( _this.sequencer.getTimeline().getTracksContainer().find('.clipTrackSet') ),
				start: function( event, ui ){
					// give the target timeline some extra space: 
					_this.sequencer.getTimeline().expandTrackSetSize();
				},
				helper: function() {					
					// Append a li to the sortable list					
					return $j( this )
						.clone ()						
						.appendTo( 'body' )
						.css({ 
							'z-index' : 99999 
						})
						.get( 0 );	
				},
				revert: 'invalid'		
			});
	},
	
	/** 
	 * Take a dom element asset from search results and
	 *  convert to a smil ref node that can be inserted into 
	 *  a smil xml tree
	 */
	getSmilClipFromAsset: function( assetElement, callback ){
		var resource = this.getResourceFromAsset( assetElement )
		this.getSmilClipFromResource ( resource, callback );
	},
	/**
	 * Take an addMedia 'resource' and convert to a smil 
	 *  ref node that can be inserted into a smil xml tree
	 */
	getSmilClipFromResource: function( resource, callback ){
		var tagType = 'ref';
		if( resource.mime.indexOf( 'image/' ) != -1 ){
			tagType = 'img';		
		}
		if( resource.mime.indexOf( 'video/') != -1 ){
			tagType = 'video';
		}
		if( resource.mime.indexOf( 'audio/') != -1 ){
			tagType = 'audio';
		}
		var $smilRef = $j( '<' + tagType + ' />')	
		
		// Set the default duration 
		if( tagType == 'img' ){
			$smilRef.attr( 'dur', mw.getConfig( 'Sequencer.AddMediaImageDuration' ) );
		}		
		
		// Set all available params
		var resourceAttributeMap = {
			'type' :  'mime',
			'title' : 'title',
			'src' : 'src',
			'poster' : 'poster'
		}
		for( var i in resourceAttributeMap ){
			if( resource[i] ){
				$smilRef.attr( resourceAttributeMap[i], resource[i] );
			}
		}			
		var resourceParamMap = {
			'content_provider_id' :  'apiProvider',
			'id' : 'id',
			'titleKey' : 'apiTitleKey'
		}
		for( var i in resourceParamMap ){
			if( resource[i] ){
				$smilRef.append(
					$j( '<param />')
					.attr({
						'name' : resourceParamMap[i],
						'value' : resource[i]
					})
				)
			}
		}
		// Make sure we have source for the asset.   
		if( $smilRef.attr('src') ){
			callback( $smilRef.get(0) )
		} else {
			// the resource includes a pointer to its parent search object
			// from the search object grab the image object for the target resolution 
			resource.pSobj.getImageObj( 
				resource,
				{
					'width' : mw.getConfig( 'Sequencer.AddMediaImageWidth' )
				},
				function( imageObj ){
					$smilRef.attr('src', imageObj.url )
					callback( $smilRef.get(0) );
				}
			)			
		}
	}
}

} )( window.mw );	