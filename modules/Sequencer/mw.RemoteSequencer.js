/**
* Stop-gap for php sequencer support does some transformations 
* to normal page views to support sequences edits
* 
* Supports basic "sequencer" functionality as a javascript rewrite system.
*/

mw.addMessageKeys( [
	"mwe-sequencer-no-sequence-create",
	"mwe-sequencer-create-sequence"
]);

mw.RemoteSequencer = function( options ) {
	return this.init( options ); 
};
mw.RemoteSequencer.prototype = {
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
				})
			
			$j( this.target ).html(
				gM("mwe-sequencer-no-sequence-create", 
					$j('<a />').attr('href','#').click(function() {
						_this.showEditor();
					})
				)
			);
		} else {
			// Display embedding help			
		}
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
			.loadingSpinner()
		)
		
		mw.load( 'Sequencer', function(){ 	 				
			$j('#edit_sequence_container').sequencer({	
				'title' : _this.getTitle(),
				'newSequence' : ( wgArticleId == 0 ),
				'server': {
					'type' : 'mediaWiki',
					'url' : _this.getApiUrl(),
					'titleKey' : wgTitle,			
				},
	    		// Set the add media wizard to only include commons:   
	    		'addMedia' : {
	    			 'enabled_providers':[ 'wiki_commons' ],	    			 
	    			 'default_query' : _this.getTitle()
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
	},
	
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
	