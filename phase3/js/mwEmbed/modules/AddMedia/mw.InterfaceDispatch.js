/**
* This Interface dispatcher handles interface updates for upload dialogs
*
* Initial dispatch handlers include:
*
* Inline dispatch
*     Dispatches updates to an inline html target
*
* Dialog dispatch
*     There is only one upload and it results in dialogs taking up the full screen
*
* Iframe hash dispatch
*     Dispatches updates to an iframe target for upload proxy
* 
* Simple Methods setLoading, setInterfacePrompt, setup, setProgress, close
*/

/**
 * Base UploadInterfaceDispatch object (extened by  
 */
mw.InterfaceDispatch = { 
	factory : function( interfaceType ){
		switch( interfaceType ){
			case 'dialog':
			default:				
				return new mw.DialogDispatch( );	
			break;
		}
	}
};

/**
 * Dialog Dispatch
 */
mw.DialogDispatch = function( ) {
	return this;
}
mw.DialogDispatch.prototype = {
	
	setup: function( options ){
		var _this = this;
		// Remove the old instance if present
		if( $j( '#upProgressDialog' ).length != 0 ) {
			$j( '#upProgressDialog' ).dialog( 'destroy' ).remove();
		}
		// Add a new one
		$j( 'body' ).append( 
			$j( '<div />')
			.attr( 'id', "upProgressDialog" )
		);
		//if( typeof options == 'undefined' ){
		//	options.title = 'temp title';
		//}
		$j( '#upProgressDialog' ).dialog( {
			title : 'temp',
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
	setInterfacePrompt: function( title_txt, msg, buttons ) {
		var _this = this;

		if ( !title_txt )
			title_txt = _this.getProgressTitle();

		if ( !msg )
			msg = mw.loading_spinner( 'left:40%;top:40px;' );

		if ( !buttons ) {
			// If no buttons are specified, add a close button
			buttons = {};
			buttons[ gM( 'mwe-ok' ) ] =  function() {
				$j( this ).dialog( 'close' );
			};
		}
		$j( '#upProgressDialog' ).dialog( 'option', 'title',  title_txt );
		$j( '#upProgressDialog' ).html( msg );
		$j( '#upProgressDialog' ).dialog( 'option', 'buttons', buttons );
	},
	
	/**
	 * Set the dialog to "done" 
	 */
	setDone: function(){
		this.action_done = true;
	},
	
	/**
	 * Given the result of an action=upload API request, display the error message
	 * to the user.
	 * 
	 * @param 
	 */
	showApiError: function( apiRes ) {
		var _this = this;
		if ( apiRes.error || ( apiRes.upload && apiRes.upload.result == "Failure" ) ) {
			// Generate the error button
			
			var buttons = {};
			buttons[ gM( 'mwe-return-to-form' ) ] = function() {
				_this.form_post_override = false;
				$j( this ).dialog( 'close' );
			};

			// Check a few places for the error code
			var error_code = 0;
			var errorReplaceArg = '';
			if ( apiRes.error && apiRes.error.code ) {
				error_code = apiRes.error.code;
			} else if ( apiRes.upload.code ) {
				if ( typeof apiRes.upload.code == 'object' ) {
					if ( apiRes.upload.code[0] ) {
						error_code = apiRes.upload.code[0];
					}
					if ( apiRes.upload.code['status'] ) {
						error_code = apiRes.upload.code['status'];
						if ( apiRes.upload.code['filtered'] )
							errorReplaceArg = apiRes.upload.code['filtered'];
					}
				} else {
					apiRes.upload.code;
				}
			}

			var error_msg = '';
			if ( typeof apiRes.error == 'string' )
				error_msg = apiRes.error;

			// There are many possible error messages here, so we don't load all
			// message text in advance, instead we use mw.getRemoteMsg() for some.
			//
			// This code is similar to the error handling code formerly in
			// SpecialUpload::processUpload()
			var error_msg_key = {
				'2' : 'largefileserver',
				'3' : 'emptyfile',
				'4' : 'minlength1',
				'5' : 'illegalfilename'
			};

			// NOTE:: handle these error types
			var error_onlykey = {
				'1': 'BEFORE_PROCESSING',
				'6': 'PROTECTED_PAGE',
				'7': 'OVERWRITE_EXISTING_FILE',
				'8': 'FILETYPE_MISSING',
				'9': 'FILETYPE_BADTYPE',
				'10': 'VERIFICATION_ERROR',
				'11': 'UPLOAD_VERIFICATION_ERROR',
				'12': 'UPLOAD_WARNING',
				'13': 'INTERNAL_ERROR',
				'14': 'MIN_LENGTH_PARTNAME'
			}

			if ( !error_code || error_code == 'unknown-error' ) {
				if ( typeof JSON != 'undefined' ) {
					mw.log( 'Error: apiRes: ' + JSON.stringify( apiRes ) );
				}
				if ( apiRes.upload.error == 'internal-error' ) {
					// Do a remote message load
					errorKey = apiRes.upload.details[0];
					mw.getRemoteMsg( errorKey, function() {
						_this.setInterfacePrompt( gM( 'mwe-uploaderror' ), gM( errorKey ), buttons );

					});
					return false;
				}

				_this.setInterfacePrompt(
						gM('mwe-uploaderror'),
						gM('mwe-unknown-error') + '<br>' + error_msg,
						buttons );
				return false;
			}

			if ( apiRes.error && apiRes.error.info ) {
				_this.setInterfacePrompt( gM( 'mwe-uploaderror' ), apiRes.error.info, buttons );
				return false;
			}

			if ( typeof error_code == 'number'
				&& typeof error_msg_key[error_code] == 'undefined' )
			{
				if ( apiRes.upload.code.finalExt ) {
					_this.setInterfacePrompt(
						gM( 'mwe-uploaderror' ),
						gM( 'mwe-wgfogg_warning_bad_extension', apiRes.upload.code.finalExt ),
						buttons );
				} else {
					_this.setInterfacePrompt(
						gM( 'mwe-uploaderror' ),
						gM( 'mwe-unknown-error' ) + ' : ' + error_code,
						buttons );
				}
				return false;
			}

			mw.log( 'get key: ' + error_msg_key[ error_code ] )
			mw.getRemoteMsg( error_msg_key[ error_code ], function() {
				_this.setInterfacePrompt(
					gM( 'mwe-uploaderror' ),
					gM( error_msg_key[ error_code ], errorReplaceArg ),
					buttons );
			});
			mw.log( "api.error" );
			return false;
		}

		// Check upload.error
		if ( apiRes.upload && apiRes.upload.error ) {
			mw.log( ' apiRes.upload.error: ' +  apiRes.upload.error );
			_this.setInterfacePrompt(
				gM( 'mwe-uploaderror' ),
				gM( 'mwe-unknown-error' ) + '<br>',
				buttons );
			return false;
		}

		// Check for warnings:
		if ( apiRes.upload && apiRes.upload.warnings ) {
			var wmsg = '<ul>';
			for ( var wtype in apiRes.upload.warnings ) {
				var winfo = apiRes.upload.warnings[wtype]
				wmsg += '<li>';
				switch ( wtype ) {
					case 'duplicate':
					case 'exists':
						if ( winfo[1] && winfo[1].title && winfo[1].title.mTextform ) {
							wmsg += gM( 'mwe-file-exists-duplicate' ) + ' ' +
								'<b>' + winfo[1].title.mTextform + '</b>';
						} else {
							//misc error (weird that winfo[1] not present
							wmsg += gM( 'mwe-upload-misc-error' ) + ' ' + wtype;
						}
						break;
					case 'file-thumbnail-no':
						wmsg += gM( 'mwe-file-thumbnail-no', winfo );
						break;
					default:
						wmsg += gM( 'mwe-upload-misc-error' ) + ' ' + wtype;
						break;
				}
				wmsg += '</li>';
			}
			wmsg += '</ul>';
			if ( apiRes.upload.sessionkey )
				_this.warnings_sessionkey = apiRes.upload.sessionkey;

			// Create the "ignore warning" button
			var buttons = {};
			buttons[ gM( 'mwe-ignorewarning' ) ] = function() {
				//check if we have a stashed key:
				if ( _this.warnings_sessionkey ) {
					//set to "loading"
					$j( '#upProgressDialog' ).html( mw.loading_spinner() );
					//setup request:
					var request = {
						'action': 'upload',
						'sessionkey': _this.warnings_sessionkey,
						'ignorewarnings': 1,
						'filename': $j( '#wpDestFile' ).val(),
						'token' :  _this.editToken,
						'comment' :  $j( '#wpUploadDescription' ).val()
					};
					//run the upload from stash request
					mw.getJSON(_this.api_url, request, function( data ) {
							_this.processApiResult( data );
					} );
				} else {
					mw.log( 'No session key re-sending upload' )
					//do a stashed upload
					$j( '#wpIgnoreWarning' ).attr( 'checked', true );
					$j( _this.editForm ).submit();
				}
			};
			// Create the "return to form" button
			buttons[ gM( 'mwe-return-to-form' ) ] = function() {
				$j( this ).dialog( 'close' );
				_this.form_post_override = false;
			}
			// Show warning
			_this.setInterfacePrompt(
				gM( 'mwe-uploadwarning' ),
				$j('<div />')
				.append(
					$j( '<h3 />' )
					.text( gM( 'mwe-uploadwarning' ) ),
					
					$j('<span />')
					.html( wmsg )
				),
				buttons );
			return false;
		}
		// No error!
		return true;
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

