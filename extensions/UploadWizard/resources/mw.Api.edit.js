// library to assist with edits

// dependencies: [ mw.Api, jQuery ]
	
( function( mw, $ ) {
	$.extend( mw.Api.prototype, { 
		/**
		 * Api helper to grab an edit token
	 	 *
		 * token callback has signature ( String token )
		 * error callback has signature ( String code, Object results, XmlHttpRequest xhr, Exception exception )
	 	 * Note that xhr and exception are only available for 'http_*' errors
		 *  code may be any http_* error code (see mw.Api), or 'token_missing'
		 *
		 * @param {Function} received token callback
		 * @param {Function} error callback
		 */
		getEditToken: function( tokenCallback, err ) {
			
			var parameters = {			
				'prop': 'info',
				'intoken': 'edit',
				/* we need some kind of dummy page to get a token from. This will return a response 
				   complaining that the page is missing, but we should also get an edit token */
				'titles': 'DummyPageForEditToken'
			};

			var ok = function( data ) {
				var token;
				$.each( data.query.pages, function( i, page ) {
					if ( page['edittoken'] ) {
						token = page['edittoken'];
						return false;
					}
				} );
				if ( mw.isDefined( token ) ) { 
					tokenCallback( token );
				} else {
					err( 'token-missing', data );
				}
			};

			var ajaxOptions = { 'ok': ok, 'err': err };

			this.get( parameters, ajaxOptions );
		}
		
	} );

}) ( window.mw, jQuery );
