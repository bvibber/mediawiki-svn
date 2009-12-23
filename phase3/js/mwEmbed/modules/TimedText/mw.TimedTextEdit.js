/** 
* Timed text edit interface based off of participatory culture foundation timed text mockups. 
*/
mw.addMessages( {
	"mew-timedtext-editor" : "Timed text editor",
	"mwe-stage-transcribe" : "Transcribe",
	"mwe-stage-sync"	: "Sync",
	"mwe-stage-translate" : "Translate",
	"mwe-stage-upload" : "Upload from local file",
	
	"mwe-select-language": "Select language",
	"mwe-file-language": "Subtitle file language",
	
	"mwe-upload-file": "Upload text file",
	"mwe-uploading-text": "Uploading text file"
} );

mw.TimedTextEdit = function( parentTimedText ){
	return this.init( parentTimedText );
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
	init: function( parentTimedText ){
		this.parentTimedText = parentTimedText;
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
					.attr( 'href', '#tab-' + edit_stage_id )
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
				.attr( 'id', 'tab-' + edit_stage_id )
				.css({
					'height': $j( window ).height() - 270
				})			
			);
		}		
		//debugger
		// Add the tags to the target:
		$j( _this.target_container ).append( $tabContainer );
		
		//Create all the "interfaces" 
		for(var edit_stage_id in this.textEditStages){
			_this.createInterface( edit_stage_id )
		}
		
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
		$target = $j('#tab-' + edit_stage_id);
		if( this[edit_stage_id + 'Interface']){
			this[ edit_stage_id + 'Interface']( $target );
		}else{
			$target.append( ' interface under development' ); 
		}		
	},
	uploadInterface: function( $target ){
		var _this = this;		
		// Check if user has XHR file upload support & we are on the target wiki
		
		// Add Select file:
		$target.append(
			$j('<div>').css({
		  		'width':'300px',
		  		'float': 'left'
			}).append(
				'<input type="file" id="timed_text_file_upload"></input><br />'
			) 
		)			
		
		
		$target.append( 
			//Get a little helper input filed to update the language
			$j('<input id="langKey-input" type="text" maxlength="10" size="3">')
				.change(function(){
					var langKey = $j(this).val();
					if( mw.languages[ langKey ] ){
						$buttonTarget.find('.btnText').text( 
							unescape( mw.languages[ langKey ] )
						);
					}						
				}),
			// Get a jQuery button object with language menu:
			$j.button( {
				'style': { 'float' : 'left' },
				'class': 'language-select-btn',
				'text': gM('mwe-select-language'),
				'icon_id': 'triangle-1-e'			
			} ).attr('id', 'language-select')
			.unbind()
			.buttonHover()
		)

		
		var $buttonTarget = $target.find('.language-select-btn');
		// Add menu container: 
		var loc = $buttonTarget.position();
		$target.append( 
			$j('<div>')		
			.addClass('ui-widget ui-widget-content ui-corner-all')			
			.attr( 'id', 'upload-language-select' )
			.loadingSpinner()			
			.css( {
				'position' 	: 'absolute',
				'z-index' 	: 10,
				'top' 		: ( loc.top + 40 ) + 'px',
				'left' 		: parseInt( loc.left ) + 'px',
				'height'	: '180px',
				'width' 	: '180px',
				'overflow'	: 'auto', 	
				'font-size'	: '12px',	
				'z-index'	: 1005				
			} )
			.hide()	
		);	
		// Add menu binding to button target
		$buttonTarget.menu( {
			'content'	: _this.getLanguageList(),		
			'backLinkText' : gM( 'mwe-back-btn' ),
			'targetMenuContainer': '#upload-language-select'							
		} );
		
		
		//Add upload input bindings: 		
		$j( '#timed_text_file_upload' ).change( function( ev ) {
			if ( $j(this).val() ) {
				var langKey = $j(this).val().split( '.' );
				var extension = langKey.pop();
				langKey = langKey.pop();
				if( mw.languages[ langKey ] ){
					$buttonTarget.find('.btnText').text( 
						unescape( mw.languages[ langKey ] )
					);
				}
			}
		});
		
		//Add an upload button: 
		$target.append( 
			$j('<div style="clear: both"></div><br /><br />'),
			$j.button( {
				'style': { 'float' : 'left' },
				'text': gM('mwe-upload-file'),
				'icon_id': 'disk'			
			} )
			.unbind()
			.buttonHover()
			.click( function(){
				_this.uploadTextFile();
			})
		);
		
	},
	/**
	 * Uploads a text file
	 */
	uploadTextFile: function(){
		//put a dialog ontop
		mw.addLoaderDialog( gM( 'mwe-uploading-text') );
		
		//Get timed text target title
		// NOTE: this should be cleand up with accessors
		var targetTitleKey = this.parentTimedText.embedPlayer.wikiTitleKey;
		
		//Add TimedText NS and language key and ".srt"
		targetTitleKey = 'TimedText:' + targetTitleKey + $j('#langKey-input').val() + '.srt';				
		
		//get a token
		mw.getToken(targetTitleKey, function( token ){
			// Get the file text
			
			mw.log("got token: " + token);
			var request = {
              'action' : 'edit',
              'title' : title,
              'text' : srt,
              'token': token
          };
		})
	},
	/** 
	 * Gets the lanugage set. 
	 * 
	 * Checks off languages that area already "loaded" according to parentTimedText
	 * 
	 * This is cpu intensive function 
	 *	Optimize: switch to html string building, insert and bind 
	 * 		(instead of building html with jquery calls )
	 * 	Optimize: pre-sort both language lists and continue checks where we left off  
	 * 
	 *  ~ what really a lot of time is putting this ~into~ the dom ~
	 */
	getLanguageList: function(){
		var _this = this;
		var $langMenu = $j( '<ul>' );		
		// Loop through all supported languages: 			
		for ( var langKey in mw.languages ) {
			var language = mw.languages [ langKey ];
			var source_icon = 'radio-on';
			//check if the key is in the _this.parentTimedText source array
			for( var i in _this.parentTimedText.textSources ){
				var pSource = _this.parentTimedText.textSources[i];
				if( pSource.lang == langKey){
					source_icon = 'bullet'; 
				}
			}
			// call out to "anonymous" function to variable scope the langKey 
			$langMenu.append( 
				_this.getLangMenuItem( langKey , source_icon)
			)
		}
		return $langMenu;
	},
	getLangMenuItem: function( langKey , source_icon){ 
		return this.parentTimedText.getLi(
			unescape( mw.languages[ langKey ] ),
			source_icon,
			function(){
				mw.log( "Selected: " + langKey );
				// Update the input box text
				$j('#langKey-input').val( langKey );
				// Update the menu item:
				$j( '#language-select' ).val( unescape( mw.languages[ langKey ] ) )
		} ); 	
	},
	/**
	 * Creates the interface dialog container
	 */
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