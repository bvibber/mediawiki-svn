/**
* Sequencer Server bridges a server API with sequence actions like 'load', 'save', 'revision history' etc.   
* ( for now only mediaWiki api is supported )
* We will abstract all the method calls once we add another api backend
*/

//Wrap in mw closure
( function( mw ) {
		
	mw.SequencerServer = function( sequencer ) {
		return this.init( sequencer );
	};

	// Set up the SequencerServer prototype method object
	mw.SequencerServer.prototype = {
			
		// lazy init save token for the server config
		saveToken : null,
		
		/**
		 * init the sequencer
		 */
		init: function( sequencer ){
			this.sequencer = sequencer;
			// Set local config from sequencer options
			var serverConfig = this.sequencer.getOption( 'server' );
			
			// NOTE this should trigger an apiHandler once we have more than one api backend
			if( serverConfig ){
				if( serverConfig.type )
					this.apiType = serverConfig.type;		
				if( serverConfig.url )
					this.apiUrl = serverConfig.url;		
				if( serverConfig.titleKey )
					this.titleKey = serverConfig.titleKey;
			}
		},
		
		// Check if the server exists / is configured 
		exists: function( ){
			if( ! this.apiUrl || ! this.titleKey ){
				return false;
			}
			return true;
		},
		
		/**
		 * Check if the user in the current session can save to the server
		 */
		userCanSave: function( callback ){
			this.getSaveToken( callback );
		},
		
		getSmilXml: function( callback ){
			mw.getTitleText( this.apiUrl, this.titleKey, callback )
		},
		
		// Get a save token, if unable to do so return false
		getSaveToken: function( callback ){
			var _this = this;
			if( this.saveToken != null ){
				callback ( this.saveToken );
				return ;	
			}
			mw.getToken( this.apiUrl, this.titleKey, function( saveToken ){
				_this.saveToken = saveToken;
				callback ( _this.saveToken )
			});
		},
		// Save the sequence		
		save: function( summary, sequenceXML, callback){
			var _this = this;
			this.getSaveToken( function( token ){
				if( !token ){
					callback( false, 'could not get edit token')
					return ;
				}
				var request = {
					'action' : 'edit',
					'summary' : summary,
					'title' : _this.titleKey,
					'text' : sequenceXML,
					'token': token
				};
				mw.getJSON( _this.apiUrl, request, function( data ) {
					if( data.edit && data.edit.result == 'Success' ) {
						callback( true );
					} else {
						// xxx Should have more error handling ( conflict version save etc )
						callback( false, 'failed to save to server');
					}
				})
			})
		}
	}


} )( window.mw );	