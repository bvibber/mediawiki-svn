/**
* 
* Api proxy system
*
* Supports cross domain uploading, and api actions for a approved set of domains.
*
* The framework /will/ support a request approval system for per-user domain approval
* and a central blacklisting of domains controlled by the site 
*
*  Flow outline below:  
* 
* Domain A (lets say en.wiki)  
* 	invokes add-media-wizard and wants to upload to domain B ( commons.wiki )
* 
* 	Domain A loads iframe to domain B ? with request param to to insert from Domain A
*		Domain B checks list of approved domains for (Domain A) & checks the user is logged in ( and if the same account name ) 
*			if user is not logged in 
*				a _link_ to Domain B to new window login page is given
*			if user has not approved domain and (Domain A) is not pre-approved 
*				a new page link is generated with a approve domain request
*					if user approves domain it goes into their User:{username}/apiProxyDomains.js config
*		If Domain A is approved we then: 
* 			loads a "push" and "pull" iframe back to Domain A 
				(Domain B can change the #hash values of these children thereby proxy the data)  
* 	Domain A now gets the iframe "loaded" callback a does a initial echo to confirm cross domain proxy
*		echo sends "echo" to push and (Domain A) js passes the echo onto the "pull"
* 	Domain A now sends api requests to the iframe "push" child and gets results from the iframe "pull"
* 		api actions happen with status updates and everything and we can reuse existing api interface code  
* 
* if the browser supports it we can pass msgs with the postMessage  API
* http://ejohn.org/blog/cross-window-messaging/
*
* NOTE: it would be nice if this supported multiple proxy targets (ie to a bright widgets future) 
*
*/

mw.addMessages( {
	"mwe-setting-up-proxy" : "Setting up proxy...",
	"mwe-re-try" : "Retry API request",
	"mwe-re-trying" : "Retrying API request...",
	"mwe-proxy-not-ready" : "Proxy is not configured",
	"mwe-please-login" : "You are not <a target=\"_new\" href=\"$1\">logged in<\/a> on $2 or mwEmbed has not been enabled. Resolve the issue, and then retry the request.",
	"mwe-remember-loging" : "General security reminder: Only login to web sites when your address bar displays that site's address."
} );




/**
 * apiProxy jQuery binding
 * 
 * Note: probably should split up "server" and "client" binding 
 * 
 * @param {String} mode Mode is either 'server' or 'client'
 * @param {Object} proxyConfig Proxy configuration
 * @param {Function} callback The function called once proxy request is done
 */
( function( $ ) {
	$.apiProxy = function( mode, proxyConfig, callback ) {
		mw.log( 'do apiProxy setup' );				
		// do the proxy setup or 
		if ( mode == 'client' ) {
			// just do the setup (no callbcak for client setup) 
			mw.proxy.client( proxyConfig );
			if ( callback )
				callback();
		} else if ( mode == 'server' ) {
			// Do the request with the callback
			mw.proxy.server( proxyConfig , callback );
		}
	}
} )( jQuery );

( function( $ ) {

	/**
	 * Base API Proxy object
	 * 	 
	 */
	$.proxy = { };
	
	/**
	 * The client setup function:
	 * 
	 * @param {Object} proxyConfig Holds the server_frame & client_frame_path vars 
	 */
	$.proxy.client = function( proxyConfig ) {
		var _this = this;
		// Do client setup: 
		if ( proxyConfig.server_frame )
			$.proxy.server_frame = proxyConfig.server_frame;
		
		if ( proxyConfig.client_frame_path ) {
			$.proxy.client_frame_path = proxyConfig.client_frame_path;
		} else {
			// Set to default via mediaWiki vars: 			
			$.proxy.client_frame_path =  wgServer + wgScriptPath + '/js/mwEmbed/modules/ApiProxy/NestedCallbackIframe.html';
		}
				
		if ( mw.isLocalDomain( $.proxy.server_frame ) ) {
			mw.log( "Error: trying to proxy local domain? " );
			return false;
		}
		return true;
	}
	// Set the frameProxy Flag: 
	var frameProxyOk = false;
	
	/** 
	* Does the frame proxy
	* 	Writes an iframe with a hashed value of the requestQuery
	*
	* @param {Object} requestQuery The api request object 
	*/
	$.proxy.doFrameProxy = function( requestQuery ) {
		
		var hashPack = {
			// Client domain: 
			'clientFrame' : $.proxy.client_frame_path,
			'request' : requestQuery
		}
		
		mw.log( "Do frame proxy request on src: \n" + $.proxy.server_frame + "\n" + JSON.stringify(  requestQuery ) );
					
		// We can't update src's so we have to remove and add all the time :(
		// NOTE: we should support frame msg system 
		$j( '#frame_proxy' ).remove();
		$j( 'body' ).append( '<iframe style="display:none" id="frame_proxy" name="frame_proxy" ' +
				'src="' + $.proxy.server_frame +
				 '#' + escape( JSON.stringify( hashPack ) ) +
				 '"></iframe>' );
				 
		// add an onLoad hook: 
		$j( '#frame_proxy' ).get( 0 ).onload = function() {
			// add a 8 second timeout for setting up the nested child callback (after page load) 
			setTimeout( function() {
				if ( !frameProxyOk ) {
					// we timmed out no api proxy (should make sure the user is "logged in")
					mw.log( "Error:: api proxy timeout are we logged in? mwEmbed is on?" );
					$.proxy.proxyNotReadyDialog();
				}
			}, 8000 );
		}
	}
	var lastApiReq = { };
	
	/**
	* Dialog to send the user if a proxy to the remote server could not be created 
	*/
	$.proxy.proxyNotReadyDialog = function() {
		var buttons = { };
		buttons[ gM( 'mwe-re-try' ) ] = function() {
			mw.addLoaderDialog( gM( 'mwe-re-trying' ) );
			$.proxy.doFrameProxy( lastApiReq );
		}
		buttons[ gM( 'mwe-cancel' ) ] = function() {
			mw.closeLoaderDialog();
		}
		var pUri =  mw.parseUri( $.proxy.server_frame );
		
		// FIXME we should have a Hosted iframe once we deploy mwEmbed on the servers.
		// A hosted iframe would be much faster since than a normal page view 
		
		var login_url = pUri.protocol + '://' + pUri.host;
		login_url += pUri.path.replace( 'MediaWiki:ApiProxy', 'Special:UserLogin' );
		
		mw.addDialog( 
			gM( 'mwe-proxy-not-ready' ), 
			gM( 'mwe-please-login', [ login_url, pUri.host] ) +
				'<p style="font-size:small">' + 
					gM( 'mwe-remember-loging' ) + 
				'</p>',
			buttons
		)
	}
	/**  
	* Takes a requestQuery, executes the query and then calls the callback
	*  sets the local callback to be called once requestQuery completes
	* 
	* @param {Object} requestQuery Api request object
	* @param {Function} callback Function called once the request is complete 
	*/
	$.proxy.doRequest = function( requestQuery, callback ) {
		mw.log( "doRequest:: " + JSON.stringify( requestQuery ) );
		lastApiReq = requestQuery;
		// setup the callback:
		$.proxy.callback = callback;
		// do the proxy req:
		$.proxy.doFrameProxy( requestQuery );
	}
	
	/**
	 * The nested iframe action that passes its result back up to the top frame instance 
	 * 
	 * Entry point for hashResult from nested iframe
	 *
	 * @param {Object} hashResult Value to be sent to parent frame	 
	 */
	$.proxy.nested = function( hashResult ) {
		// Close the loader if present: 
		mw.closeLoaderDialog();
		mw.log( '$.proxy.nested callback :: ' + unescape( hashResult ) );
		frameProxyOk = true;
		
		// Try to parse the hash result 
		try {
			var resultObject = JSON.parse( unescape( hashResult ) );
		} catch ( e ) {
			mw.log( "Error could not parse hashResult" );
		}
		
		// Special callback to frameProxyOk flag 
		// (only used to test the proxy connection)   
		if ( resultObject.state == 'ok' )
			return ;
		
		// Pass the result object to the callback:
		$.proxy.callback( resultObject );
	}
	
				
	
	
	/**
	* API iFrame Server::
	*
	* Handles the server side proxy of requests 
	* it adds child frames pointing to the parent "blank" frames
	*/
	 	 
 	/**
	* Api iFrame request:
	* @param {Object} requestObj Api request object
	*/
	function doApiRequest( clientRequest ) {
					
		// Make sure its a json format 
		clientRequest.request[ 'format' ] = 'json';		

		// Process the API request. We don't use mw.apiReq since we need to "post" 
		$j.post( wgScriptPath + '/api' + wgScriptExtension,
			clientRequest.request,
			function( data ) {					
				// Put it result into nested frame hash string: 
				outputResultsFrame( clientRequest.clientFrame, 'nested_push', JSON.parse( data ) );
			}
		);
	}
	
	/**
	* Outputs the result object to the client domain
	*
	* @param {clientFrame} clientFrame Client frame name 
	* @param {String} nestName Name of iframe	
	* @param {resultObj} the result to pass back to the client domain
	*/ 
	function outputResultsFrame( clientFrame,  nestName, resultObj ) {
		$j( '#nested_push' ).remove();
		// Setup the nested iframe proxy that points back to top domain:
		$j( 'body' ).append( 
			$j('<iframe>').attr({
				'id' 	: nestName,
				'name'	: nestName,
				'src'	: clientFrame + '#' + escape( JSON.stringify( resultObj ) )
			}) 
		); 
	}
	 
	/** 
	* Api server proxy entry point: 
	*
	* @param {Object} proxyConfig The server side proxy configuration
	* @param {Function} callbcak Function to call once server is setup
	*
	*/
	$.proxy.server = function( proxyConfig, callback ) {			
		/** 
		* Clear the body of any html 
		*/
		$j( 'body' ).html( 'Proxy Setup: ' );
		
		var clientRequest = false;
		
		// Read the anchor action from the requesting url
		var hashMsg = unescape( mw.parseUri( document.URL ).anchor );
		try {
			var clientRequest = JSON.parse( hashMsg );
		} catch ( e ) {
			mw.log( "ProxyServer:: could not parse anchor" );
		}
		
		if ( !clientRequest || !clientRequest.clientFrame ) {
			mw.log( "Error: no client domain provided " );
			$j( 'body' ).append( "no client frame provided" ); 
			return false;
		}		
		
		// Make sure we are logged in 
		// (its a normal mediaWiki page so all site vars should be defined)		
		if ( typeof wgUserName != 'undefined' && !wgUserName ) {
			mw.log( 'Error Not logged in' );
			return false;
		}
		
		mw.log( "Setup server on: "  + mw.parseUri( document.URL ).host  );
		mw.log('Client frame: ' + clientRequest.clientFrame );
				
		var clientDomain =  mw.parseUri( clientRequest.clientFrame ).host ;
		
		/**
		*	HERE WE CHECK IF THE DOMAIN IS ALLOWED per the proxyConfig	
		*/		
		// Check master blacklist
		for ( var i in proxyConfig.master_blacklist ) {
			if ( clientDomain == proxyConfig.master_blacklist ) {
				mw.log( 'domain: ' + clientDomain + ' is blacklisted ( no request )' );
				return false;
			}
		}		 
		// Check the master whitelist:
		for ( var i in proxyConfig.master_whitelist ) {
			if ( clientDomain ==  proxyConfig.master_whitelist[ i ] ) {
				// Do the request: 			
				return doApiRequest( clientRequest );
			}
		}
			
		// FIXME Add in user based approval :: 
				
		// offer the user the ability to "approve" requested domain save to
		// their user/ apiProxyDomainList.js page
		
		// FIXME grab the users whitelist for our current domain				
		/*var local_api = wgScriptPath + '/index' + wgScriptExtension + '?title=' +
				'User:' + wgUserName + '/apiProxyDomainList.js' +
				'&action=raw&smaxage=0&gen=js';
		$j.get( local_api, function( data ){
			debugger;
		});*/				
				
	}
	
} )( window.mw );
