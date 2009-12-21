/** 
* Timed text edit interface based off of participatory culture foundation timed text mockups. 
*/
mw.addMessages( {
	"mew-timedtext-editor" : "Timed text editor",
	"mwe-stage-transcribe" : "Transcribe",
	"mwe-stage-sync"	: "Sync",
	"mwe-stage-translate" : "Translate",
	"mwe-stage-upload" : "Upload from local file"
} );

mw.TimedTextEdit = function(){
	return this.init();
}
mw.TimedTextEdit.prototype = {
	// The target container for the interface: 
	target_container: null,
	
	// Interface steps can be "transcribe", "sync", "translate" 
	textEditStages:{
		'transcribe':{
			'icon' : 'comment'
		},
		'sync':{
			'icon' : 'clock'
		},
		'translate':{
			'icon' : 'flag'
		},
		'upload':{
			'icon' : 'folder-open'
		}
	},
	
	/**
	 * @constructor
	 */
	init: function( options ){
		
	},
	
	/**
	 * Show the editor UI
	 */
	showUI: function(){				
		// Setup the parent container:
		this.createDialogContainer();
		
		// Setup the timedText editor interface
		this.initDialog();	
	}, 
	
	/**
	 * Setup the dialog layout: s
	 */
	initDialog: function(){
		var _this =this;		
		
		_this.createTabs();
				
	},
	
	/**
	 * Creates interface tabs from the textEditStages 
	 */
	createTabs: function(){
		var _this = this;
		$tabContainer = $j('<div id="TimedTextEdit-tabs"><ul></ul></div>');
		for(var edit_stage_id in this.textEditStages){
			var editStage = this.textEditStages[ edit_stage_id ];
			// Append the menu item: 
			$tabContainer.find('ul').append(
				$j('<li>').append(
					$j('<a>')
					.attr( 'href', '#' + edit_stage_id )
					.append(
						$j('<span style="float:left;">')
						.addClass( 'ui-icon ui-icon-' + editStage.icon )
						,
						$j('<span>')
						.text( gM('mwe-stage-' + edit_stage_id) ) 
					)		
				)		
			);
			// Append the menu item content container
			$tabContainer.append( 
				$j('<div>')
				.attr( 'id', edit_stage_id )
				.append(
					_this.createInterface( edit_stage_id )
				)	
			);
		}		
		//debugger
		// Add the tags to the target:
		$j( _this.target_container ).append( $tabContainer );
		
		//Add tabs interface
		$j('#TimedTextEdit-tabs').tabs( {			
			select: function( event, ui ) {
				_this.selectTab( $j( ui.tab ).attr( 'href' ).replace('#','') );
			}
		});
		
	},
	selectTab: function( tab_id ){
		mw.log('sel: ' + tab_id);
		
	},
	
	/**
	 * Creates an interface for a given stage id
	 * @return the jquery interface
	 * @type {Object}
	 */
	createInterface: function( edit_stage_id){
		
	},
	createDialogContainer: function(){
		var _this = this;
		//Setup the target container: 
		_this.target_container = '#timedTextEdit_target';
		$j( _this.target_container ).remove();
		$j( 'body' ).append(
			$j('<div>')
				.attr({
					'id' : 'timedTextEdit_target',
					'title' : gM( 'mew-timedtext-editor' ) 
				})	
				.addClass('TimedTextEdit')			
		);
		
		// Build cancel button 
		var cancelButton = {};
		cancelButton[ gM( 'mwe-cancel' ) ] = function() {
			_this.onCancelClipEdit();
		}
		
		$j( _this.target_container ).dialog( {
			bgiframe: true,
			autoOpen: true,
			modal: true,
			draggable: false,
			resizable: false,
			buttons: cancelButton,
			close: function() {
				// if we are 'editing' we should confirm they want to exist:
				$j( this ).parents( '.ui-dialog' ).fadeOut( 'slow' );
			}
		} );		
		
		$j( _this.target_container ).dialogFitWindow();
		
		// Add the window resize hook to keep dialog layout
		$j( window ).resize( function() {
			$j( _this.target_container ).dialogFitWindow();
		} );
		
	}, 
	
	onCancelClipEdit: function(){
		var _this = this;
		// cancel edit
		//debugger;	
		$j( _this.target_container ).dialog( 'close' );
	}
}