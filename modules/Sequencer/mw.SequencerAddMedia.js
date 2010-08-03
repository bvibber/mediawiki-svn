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
					.css('color', '#888')
					.focus( function(){
						// on the first focus clear the input and update the color
						if( !widgetFocus ){
							$j(this)
							.css('color', '#000')
							.val('');		
						}
						widgetFocus = true;
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
				
				//input button
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
		
		if( ! this.remoteSearchDriver ){
			// set the tool target to loading
			mw.load( 'AddMedia.addMediaWizard', function(){
				this.remoteSearchDriver = new mw.RemoteSearchDriver({
					'target_container' : _this.sequencer.getEditToolTarget(),
					'target_search_input' : _this.sequencer.getMenuTarget().find('input.searchMedia'),					
					'displaySearchInput': false,
					'default_query' : inputValue,
					'displayResourceInfoIcons' : false,
					'resourceSelectionCallback' : function( resource ){ 
						_this.insertResourceDialog( resource )
						return false;
					} 
				})
				.createUI()
			});
		} else {
			this.remoteSearchDriver.createUI()
		}
	}, 
	
	/**
	 * Create an insert resource dialog, expose basic in-out points or / duration 
	 * 
	 *  buttons include insert at end or insert after current
	 */
	insertResourceDialog: function( resource ){
		var buttons = {};
		// insert after last selected clip ( or at end ) 
		buttons[ gM('mwe-sequencer-insert') ] = function() {
			mw.log("insert resource into sequence");
			var cat = resource;
			debugger;
		}		
		// cancel
		buttons[ gM('mwe-cancel') ] = function(){
			$j( this ).dialog( 'close' );
		};
		        
		mw.addDialog({
			'title' : gM('mwe-sequencer-insert-resource'),
			'dragable' : true,	
			'height' : 480,
			'width' : 640,
			'resizable' : true,		
			'content' : function(){
				// add the resource on-top and trim 
			},
			'buttons' : buttons
		});
	}
}

} )( window.mw );	