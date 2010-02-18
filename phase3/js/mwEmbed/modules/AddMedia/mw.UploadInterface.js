/**
* This handles upload interfaces
*
* There are several interface types: 
*
* Inline interface
*     Dispatches updates to an inline html target
*
* Dialog interface
*     There is only one upload and it results in dialogs taking up the full screen
*
* Iframe interface
*     Dispatches updates to an iframe target for upload proxy
* 
*/

/**
 * Base UploadInterface object  
 */
mw.UploadInterface = { 
	factory : function( interfaceType ){
		switch( interfaceType ){
			case 'iframe':
				return new mw.iframeInterface( );
			break;
			case 'dialog':
			default:				
				return new mw.DialogInterface( );	
			break;			
		}
	}
};

/**
 * Dialog Interface
 */
mw.DialogInterface = function( ) {
	return this;
}
mw.DialogInterface.prototype = {
	
	// The following are really state of the upload, not the interface.
	// we are currently only managing one, so this is okay... for now.
	uploadBeginTime: null,
	
	setup: function( options ){		
		var _this = this;
		
		// Start the "upload" time
		this.uploadBeginTime = (new Date()).getTime();
		
		// Remove the old instance if present
		if( $j( '#upProgressDialog' ).length != 0 ) {
			$j( '#upProgressDialog' ).dialog( 'destroy' ).remove();
		}
		
		// Add a new one
		$j( 'body' ).append( 
			$j( '<div />')
			.attr( 'id', "upProgressDialog" )
		);
		if( typeof options == 'undefined' || !options.title ){
			options.title = gM('mwe-upload-in-progress');
		}
		$j( '#upProgressDialog' ).dialog( {
			title : options.title,
			bgiframe: true,
			modal: true,
			draggable: true,
			width: 400,
			heigh: 200,
			beforeclose: function( event, ui ) {
				// If the upload is not complete, ask the user if they want to cancel
				if ( event.button == 0 && _this.action_done === false ) {
					_this.onCancel( this );
					return false;
				} else {
					// Complete already, allow close
					return true;
				}
			},
			buttons: _this.getCancelButton()
		} );
		mw.log( 'upProgressDialog::dialog done' );		

		var $progressContainer = $j('<div />')
			.attr('id', 'up-pbar-container')
			.css({
				'height' : '15px'
			});
		// add the progress bar	
		$progressContainer.append(
			$j('<div />')
				.attr('id', 'up-progressbar')
				.css({
					'height' : '15px'
				})
		);
		// Add the status container
		$progressContainer.append( $j('<span />' )
			.attr( 'id', 'up-status-container')
			.css( 'float', 'left' )
			.append(
				$j( '<span />' )
				.attr( 'id' , 'up-pstatus')
				.text( '0% -' ),
				
				$j( '<span />' )
				.attr( 'id', 'up-status-state' )
				.text( gM( 'mwe-uploaded-status' ) )				
			)
		);
		// Add the estimated time remaining 
		$progressContainer.append(
			$j('<span />')
			.attr( 'id', 'up-etr' )
			.css( 'float', 'right' )
			.text( gM( 'mwe-uploaded-time-remaining', '' ) )
		)
		// Add the status container to dialog div
		$j( '#upProgressDialog' ).empty().append( $progressContainer	);
		
		// Open the empty progress window
		$j( '#upProgressDialog' ).dialog( 'open' );

		// Create progress bar
		$j( '#up-progressbar' ).progressbar({
			value: 0
		});
	},
	
	/**
	 * Update the progress bar to a given completion fraction (between 0 and 1)
     * NOTE: This progress bar is used for encoding AND for upload with no clear Distinction (might want to fix) 
     * @param {Float} progress Progress float
	 */
	updateProgress: function( fraction, start_time ) {
		var _this = this;
		
		$j( '#up-progressbar' ).progressbar( 'value', parseInt( fraction * 100 ) );
		$j( '#up-pstatus' ).html( parseInt( fraction * 100 ) + '% - ' );

		if ( _this.uploadBeginTime) {
			var elapsedMilliseconds = ( new Date() ).getTime() - _this.uploadBeginTime;
			if (fraction > 0.0 && elapsedMilliseconds > 0) { // or some other minimums for good data
				var fractionPerMillisecond = fraction / elapsedMilliseconds;
				var remainingSeconds = parseInt( ( ( 1.0 - fraction ) / fractionPerMillisecond ) / 1000 ); 
				$j( '#up-etr' ).html( gM( 'mwe-uploaded-time-remaining', mw.seconds2npt(remainingSeconds) ) );
			}
		}
	
	},
	
	/**
	 * UI cancel button handler.
	 * Show a dialog box asking the user whether they want to cancel an upload.
	 * @param Element dialogElement Dialog element to be canceled 
	 */
	onCancel: function( dialogElement ) {
		//confirm:
		if ( confirm( gM( 'mwe-cancel-confim' ) ) ) {
			// NOTE: (cancel the encode / upload)
			$j( dialogElement ).dialog( 'close' );
		}
	},
	
	/**
	 * Set the dialog to loading
	 * @param optional loadingText text to set dialog to. 
	 */
	setLoading: function( loadingText ){
		this.action_done = false;
		//Update the progress dialog (no bar without XHR request)
		$j( '#upProgressDialog' ).loadingSpinner();
	},

	/**
	 * Set the interface with a "title", "msg text" and buttons prompts
	 * list of buttons below it.
	 *
	 * @param title_txt Plain text
	 * @param msg HTML
	 * @param buttons See http://docs.jquery.com/UI/Dialog#option-buttons
	 */
	setPrompt: function( title_txt, msg, buttons ) {
		var _this = this;

		if ( !title_txt )
			title_txt = _this.getProgressTitle();

		if ( !msg )
			msg = mw.loading_spinner( 'left:40%;top:40px;' );

		if ( !buttons ) {
			// If no buttons are specified, add a close button
			buttons = {};
			buttons[ gM( 'mwe-ok' ) ] =  function() {
				$j( this ).dialog( 'close' ).remove();
			};
		}
		$j( '#upProgressDialog' ).dialog( 'option', 'title',  title_txt );
		$j( '#upProgressDialog' ).html( msg );
		$j( '#upProgressDialog' ).dialog( 'option', 'buttons', buttons );
	},
	
	/**
	 * Set the dialog to "done" 
	 */
	close: function(){
		this.action_done = true;
		$j( '#upProgressDialog' ).dialog( 'destroy' ).remove();
	},
	/**
	* Get a standard cancel button in the jQuery.ui dialog format
	*/
	getCancelButton: function() {
		var _this = this;
		mw.log( 'f: getCancelButton()' );
		var cancelBtn = [];
		cancelBtn[ gM( 'mwe-cancel' ) ] = function() {
			$j( dlElm ).dialog( 'close' );
		};
		return cancelBtn;
	}	
}

/**
 * Iframe Interface ( sends updates to an iframe for remoteing upload progress events )
 */
mw.iframeInterface = function( ) {
	return this;
}
mw.iframeInterface.prototype = {

};


