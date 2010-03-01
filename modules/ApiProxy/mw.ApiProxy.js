/**
* 
* Api proxy system
*
* Supports cross domain uploading, and api actions for a approved set of domains.
* 
* All cross domain communication is done with iframe children ( Avoids polling 
* which is resource intensive and can lose messages ) 
*
* The framework ~will eventually~ support a request approval system for per-user domain approval
* and a central blacklisting of domains controlled by the site 
*  
* NOTE: If the browser supports it we should pass msgs with postMessage  API
* http://ejohn.org/blog/cross-window-messaging/ ( rather than using these iframes ) 
*
* NOTE: refactor efforts will include separating out "core" proxy functions and
* having a separate class for "server" and "client" api usage  
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

 /**
 * Set the base API Proxy object
 * 	 
 */
mw.ApiProxy = { };
	
// ApiProxy scoped functions: 
( function( $ ) {
	 	
	// Callback function for client requests 
	var proxyCallback = null;
	 
	// FrameProxy Flag: 	
	var frameProxyOk = false;
	
	// The last run api request data object
	var currentApiReq = { };
	
	// The url for the last api request target.
	var currentServerApiUrl = null; 
	
	// Time we should wait for proxy page callback
	// ( note this time starts form when the page is "done"
	// loading so it should only be the time need to load some 
	// cached js for the callback)	
	// 15 seconds
	var proxyPageLoadTimeout = 15000;
			
	/**  
	* Takes a requestQuery, executes the query and then calls the callback
	*  sets the local callback to be called once requestQuery completes
	* 
	* @param {String} apiUrl Url to the api we want to do the request on.  
	* @param {Object} requestQuery Api request object
	* @param {Function} callback Function called once the request is complete 
	*/
	$.doRequest = function( apiUrl, requestQuery, callback ) {			
		
		// Reset local vars:
		proxyCallback = false;
		frameProxyOk = false;
			
		// Sanity check: 
		if ( mw.isLocalDomain( apiUrl ) ) {
			mw.log( "Error: trying to proxy local domain? " );
			return false;
		}
		
		mw.log( "doRequest:: " + JSON.stringify( requestQuery ) );
		
        // Set local scope current request
        // ( presently the api proxy only support sequential requests
        // for multiple simultaneous requests we will need to do some minor refactoring ) 
		currentApiReq = requestQuery;
		currentServerApiUrl = apiUrl;
		
		// Setup the callback:
		proxyCallback = callback;
		
		// Do the proxy req:
		doFrameProxy( requestQuery );
	}

	/**
	 * The nested iframe action that passes its result back up to the top frame instance 
	 * 
	 * Entry point for hashResult from nested iframe
	 *
	 * @param {Object} hashResult Value to be sent to parent frame	 
	 */
	$.nested = function( hashResult ) {
		// Close the loader if present: 
		mw.closeLoaderDialog();
		
		mw.log( '$.proxy.nested callback :: ' + decodeURIComponent( hashResult ) );
		frameProxyOk = true;
		
		// Try to parse the hash result 
		try {
			var resultObject = JSON.parse( decodeURIComponent( hashResult ) );
		} catch ( e ) {
			mw.log( "Error could not parse hashResult" );
		}
		
		// Special callback to frameProxyOk flag 
		// (only used to quickly test the proxy connection)   
		if ( resultObject.state == 'ok' ) {
			return ;
		}
		
		// Pass the result object to the callback:
		proxyCallback( resultObject );
	}
	
	/**
	* Generates a remote browse file iframe window
	*	usefull for "uploading" cross domain
	* 
	* @param {Function} callback Function to host the 
	*/
	$.browseFile = function( options ) {
	
		// Set frame proxy ok state flag: 
		frameProxyOk = false;
		
		if( ! options ) {
			options = {};
		}
			
		if( ! options.target ) {
			mw.log( "Error: no target for file browse iframe" ) ;
			return false;
		}
		if( ! options.api_url ) {
			mw.log( "Error: no api url to target" );
			return false; 
		}
		
		// Update the current apiUrl:
		currentServerApiUrl = options.api_url;
		
		if( ! options.width ) {
			options.width = 270;		
		}
		if( ! options.height ) {
			options.height = 27;
		}
		var iFrameName = ( options.iframeName ) ? options.iframeName : 'fileBrowse_' + $j('iframe').length;		
		// Setup an object to be packaged into the frame
		var iFrameRequest ={
			'clientFrame' : getClientFrame(),
			'action' : 'browseFile'			
		};
		
		var frameStyle = 'display:none;border:none;overflow:hidden;'
			+ 'width:'+ parseInt( options.width ) + 'px;'
			+ 'height:' + parseInt( options.height ) +'px';
			
		// Empty the target ( so that the iframe can be put there )
		$j( options.target ).empty();
		
		// Append the browseFile iframe to the target:  
		appendIframe( {
			'persist' : true,
			'style' : frameStyle,
			'name' : iFrameName,
			'src' : getServerFrame(  options.api_url ),
			'request' : iFrameRequest,
			'target' : options.target
		},  function( ) {
			// Add a 10 second timeout for setting up the nested child callback (after iframe load)		
			setTimeout( function() {
				if ( ! frameProxyOk ) {
					// we timmed out no api proxy (should make sure the user is "logged in")
					mw.log( "Error:: api proxy timeout are we logged in? mwEmbed is on?" );
					proxyNotReadyDialog();
				}
			}, proxyPageLoadTimeout );
		} );
		// Add a loading spinner to the target
		$j( options.target ).append( 
			$j('<div />').loadingSpinner()
		);
		
		var uploadDialogInterface = new mw.DialogInterface({
			'uploadHandlerAction' : function( action ){
				mw.log(	'apiProxy uploadActionHandler:: ' + action );
				// Send action to remote frame 
				mw.ApiProxy.sendServerMsg( {
					'api_url' : options.api_url, 
					'frameName' : iFrameName,
					'frameMsg' : {
						'action' : 'uploadHandlerAction',
						'uiAction' :  action
					}
				} );
			}
		});
		
		// Setup the proxy scope callback to display the upload unhide the iframe upload form 
		proxyCallback = function( iframeData ) {
			// Process fileBrowse callbacks ::
			
			// check for basic status "ok"
			if( iframeData['status'] == 'ok' ) {
				// Hide the loading spinner
				$j( options.target ).find('.loading_spinner').fadeOut('fast');
				mw.log("iframe ready callback");
				$j( '#' + iFrameName ).fadeIn( 'fast' );	
				return ;
			}
			
			// Else check for event 
			if( iframeData['event'] ) {
				switch( iframeData['event'] ) {
					case 'selectFileCb':
						if( options.selectFileCb ) {
							options.selectFileCb( iframeData['fileName'] );
						}
					break
					case 'uploadUI':
						if( uploadDialogInterface[ iframeData['method'] ] ){
							var args = iframeData['arguments'];				
							mw.log( "Do dialog interface: " + iframeData['method'] + ' args: ' + args[0] + ', ' + args[1] + ', ' + args[2] );		
							uploadDialogInterface[ iframeData['method'] ](
								args[0], args[1], args[2]
							);
						}
					break;	
					default:
						mw.log(" Error unreconginzed event " + iframeData['event'] );
				}				
			}						
		}
		
		// Return the name of the browseFile frame
		return iFrameName;
	}
	
	/**
	 * Output a server msg to a server iFrame 
	 * ( such as a hosted browse file or dialog prompt ) 
	 * 
	 * @param {Object} options Arguments to setup send server msg
	 * 	api_url The api url of the server to send the frame msg to
	 *  frameName The frame name to send the msg to
	 *  frameMsg The msg object to send to frame 
	 */
	$.sendServerMsg = function( options ){
		if( !options.api_url || ! options.frameMsg || !options.frameName ){
			mw.log( "Error missing required option");
			return false;
		}
		
		// Send a msg to the server frameName from the server domain
		// Setup an object to be packaged into the frame
		var iFrameRequest = {
			'clientFrame' : getClientFrame(),
			'action' : 'sendFrameMsg',
			'frameName' : options.frameName,
			'frameMsg' : options.frameMsg
		};
		
		// Send the iframe request:   	
		appendIframe( {
			'persist' : true,
			'src' : getServerFrame(  options.api_url ),
			'request' : iFrameRequest,
			'target' : options.target
		}, function( ) {
			mw.log( "sendServerMsg iframe done loading" );
		} );
	}
	
	/**
	* Handle a server msg
	* 
	* @param {Object} frameMsg  
	*/
	$.handleServerMsg = function( frameMsg ){
		mw.log( "handleServerMsg:: " + JSON.stringify( frameMsg ) );
		if( ! frameMsg.action ){
			mw.log(" missing frameMsg action " );
			return false;
		}	
		switch( frameMsg.action ){
			case 'fileSubmit':
				serverSubmitFile( frameMsg.formData );
			break;
			case 'uploadHandlerAction': 
				serverSendUploadHandlerAction( frameMsg.uiAction );
			break;
		}
	}
	
	/** 
	* Api server proxy entry point: 
	* validates the server frame request
	* and process the request type
	*/
	$.server = function() {		
		// Validate the server request:
		if( ! validateIframeRequest() ) {
			mw.log( "Not a valid iframe request");
			return false;
		}				
		// Inform the client frame that we passed validation
		sendClientMsg( { 'state':'ok' } );
		
		return serverHandleRequest();											
	}
	
	/**
	* Local scoped helper functions:
	*/	
	
	/**
	* Get the client frame path ( within mwEmbed )
	*/
	function getClientFrame() {
		return mw.getMwEmbedPath() + 'modules/ApiProxy/NestedCallbackIframe.html';
	}
	
	/**
	* Get the server Frame path per requested Api url
	* (presently hard coded to MediaWiki:ApiProxy per /remotes/medaiWiki.js )
	* 
	* NOTE: we should have a Hosted page once we deploy mwEmbed on the servers.
	* A hosted page would be much faster since it would not have to load all the 
	* normal page view assets prior to being rewrite for api proxy usage.   
	* 
	* NOTE: We add the gadget incase the user has not enabled the gadget on the 
	* domain they want to iframe to. There is no cost if they  already have the
	* gadget on. This can be removed once deployed as well.  
	* 
	* @param {URL} apiUrl The url of the api server
	*/
	//var gadgetWithJS = '?withJS=MediaWiki:Gadget-mwEmbed.js';
	var gadgetWithJS = '';
	function getServerFrame( apiUrl ) {
		// Set to local scope currentServerApiUrl if unset by argument
		if( !apiUrl) {
			apiUrl = currentServerApiUrl;
		}
		var parsedUrl = mw.parseUri( apiUrl );
		
		return 	parsedUrl.protocol + '://' + parsedUrl.authority 
			+ '/w/index.php/MediaWiki:ApiProxy' + gadgetWithJS;
	}
	
	/** 
	* Do the frame proxy
	* 	Writes an iframe with a hashed value of the requestQuery
	*
	* @param {Object} requestQuery The api request object 
	*/
	function doFrameProxy ( requestQuery ) {
		
		var iframeRequest = {
			// Client domain frame ( will be approved by the server before sending and reciving msgs )
			'clientFrame' : getClientFrame(),
			'action' : 'apiRequest',
			'request' : requestQuery
		}
		  
		mw.log( "Do frame proxy request on src: \n" + getServerFrame() + "\n" + JSON.stringify(  requestQuery ) );
		appendIframe({
			'persist' : true,
			'src' : getServerFrame(),
			'request' :  iframeRequest
		}, function() {		
			// Add a 10 second timeout for setting up the nested child callback (after page load)
			
			// NOTE: once we have a real entry point instead of ( mediaWiki:ApiProxy rewrite) 
			// this number can be reduced since it won't have to load all the style sheets and
			// javascript for a normal page view 
			
			setTimeout( function() {
				if ( !frameProxyOk ) {
					// We timed out no api proxy (should make sure the user is "logged in")
					mw.log( "Error:: api proxy timeout are we logged in? mwEmbed is on?" );
					proxyNotReadyDialog();
				}
				// NOTE:: todo we could remove the iframe from the dom once the
				//  request process is complete. 
				// ( should be part of supporting multiple requests at once refactor) 
				
			}, proxyPageLoadTimeout );
		})
	}
	
	/**
	* Validate an iframe request 
	* checks the url hash for required parameters 
	* checks  master_blacklist 
	* checks  master_whitelist
	*/
	function validateIframeRequest() {				
		var clientRequest = getClientRequest();
				
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
							
		/**
		* HERE WE CHECK IF THE DOMAIN IS ALLOWED per the proxyConfig	
		*/		
		return isAllowedClientFrame( clientRequest.clientFrame );							
	}
	
	/**
	 * Check if a domain is allowed.
	 * @param {Object} clientFrame
	 */
	function isAllowedClientFrame( clientFrame ) {
		var clientDomain =  mw.parseUri( clientFrame ).host ;
		// Get the proxy config
		var proxyConfig = mw.getConfig( 'apiProxyConfig' );
		
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
				return true;
			}
		}
		// FIXME Add in user based approval :: 
		
		// FIXME offer the user the ability to "approve" requested domain save to
		// their user preference setup )
		
		// FIXME grab the users whitelist for our current domain			
		return false;		
	}
	
	/**
	* Get the client request from the document hash
	* @return {Object} the object result of parsing the document anchor msg
	*/
	function getClientRequest() {
		// Read the anchor data package from the requesting url
		var hashMsg = unescape( mw.parseUri( document.URL ).anchor );
		try {
			return JSON.parse( hashMsg );
		} catch ( e ) {
			mw.log( "ProxyServer:: could not parse anchor" );
			return false;
		}		
	}
	
	/**
	* Dialog to send the user if a proxy to the remote server could not be created 
	*/
	function proxyNotReadyDialog() {
		var buttons = { };
		buttons[ gM( 'mwe-re-try' ) ] = function() {
			mw.addLoaderDialog( gM( 'mwe-re-trying' ) );
			doFrameProxy( currentApiReq );
		}
		buttons[ gM( 'mwe-cancel' ) ] = function() {
			mw.closeLoaderDialog();
		}
		
		// Setup the login link: 
		var pUri =  mw.parseUri( getServerFrame() );			
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
	* API iFrame Server::
	*
	* Handles the server side proxy of requests 
	* it adds child frames pointing to the parent "blank" frames
	*/
	
	/**
	* serverHandleRequest handle a given request from the client 
	* maps the request to serverBrowseFile or serverApiRequest
	* 
	* NOTE: mw.ApiProxy.server entry point validates the request
	*/
	function serverHandleRequest( ) {		
		var clientRequest = getClientRequest();
		mw.log(" handle client request :: " +  	JSON.stringify( clientRequest ) );
		//debugger;
		// Process request type:
		switch( clientRequest['action'] ){
			case 'browseFile':
				return serverBrowseFile();
			break;			
			case 'apiRequest':
				return serverApiRequest();
			break;
			case 'sendFrameMsg':
				return serverSendFrameMsg();
			break;			
		}
		mw.log( "Error could not handle client request" );
		return false;
	}
	
 	/**
	* Api iFrame request:
	*/
	function serverApiRequest( ) {		
		// Get the client request
		var clientRequest = getClientRequest();
		
		// Make sure its a json format 
		clientRequest.request[ 'format' ] = 'json';		
		
		mw.log(" do post request to: " + wgScriptPath + '/api' + wgScriptExtension );
		
		for( var i in clientRequest.request ) {
			mw.log("req: " + i + " = " + clientRequest.request[i] );  
		} 
		
		// Process the API request. We don't use mw.getJSON since we need to "post"
		$j.post( wgScriptPath + '/api' + wgScriptExtension,
			clientRequest.request,
			function( data ) {
				mw.log(" server api request got data: " + data );	
				// Send the result data to the client 
				sendClientMsg( JSON.parse( data ) );
			}
		);
	}
	
	/**
	 *  Send a msg to a server frame
	 *  
	 *  Server frame instances that handle msgs
	 *  should accept processMsg calls 
	 */
	function serverSendFrameMsg( ){
		var clientRequest = getClientRequest();
		
		// Make sure the requested frame exists:
		if( ! clientRequest.frameMsg || ! clientRequest.frameName ) {
			mw.log("Error serverSendFrameMsg without frame msg or frameName" );
			return false;
		}				
		// Send the message to the target frame
		top[ clientRequest.frameName ].mw.ApiProxy.handleServerMsg( clientRequest.frameMsg );
	}
	
	/**
	* Setup the browse file proxy on the "server"
	* 
	* Sets the page content to browser file 
	*/
	function serverBrowseFile( ) {
		
		// If wgEnableFirefogg is not boolean false, set to true
		if ( typeof wgEnableFirefogg == 'undefined' ) {
			wgEnableFirefogg = true;
		}
		
		// Setup the browse file html
		serverBrowseFileSetup();
		
		// Load the mw.upload library with iframe interface (similar to uploadPage.js)		
		// Check if firefogg is enabled:
		// NOTE: the binding function should be made identical.  
		if( wgEnableFirefogg ) {
			mw.load( 'AddMedia.firefogg', function() {	
				var uploadConfig = getUploadFileConfig( );
								
				$j( '#wpUploadFile' ).firefogg( uploadConfig );
				
				// Update status 
				sendClientMsg( {'status':'ok'} );
			});
		} else {
			mw.load( 'AddMedia.UploadHandler', function() {	
				var uploadConfig = getUploadFileConfig();
									
				$j( '#mw-upload-form' ).uploadHandler( uploadConfig );
				
				// Update status
				sendClientMsg( {'status':'ok'} );
			});
		}		
	};
	
	/**
	 * Setup the browse file html
	 * @return browse file config
	 */
	function serverBrowseFileSetup( ){
		// Get the proxy config
		var proxyConfig = mw.getConfig( 'apiProxyConfig' );
		//check for fw ( file width )
		if( ! proxyConfig.fileWidth ) {
			proxyConfig.fileWidth = 130;
		}
		//Build a form with bindings similar to uploadPage.js ( but only the browse button ) 		
		$j('body').html(
			$j('<form />')
			.attr( {
				'name' : "mw-upload-form",
				'id' : "mw-upload-form",
				'enctype' : "multipart/form-data",
				'method' : "post",
				// Submit to the local domain 
				'action' : 	mw.getLocalApiUrl()
			} )
			.append(
				//Add a single "browse for file" button
				$j('<input />')
				.attr({
					'type' : "file",					
					'name' : "wpUploadFile",
					'id' : "wpUploadFile"
				})
				.css({
					'width' : proxyConfig.fileWidth
				})
			)
		);		
	}
	
	/**
	* Browse file upload config generator
	*/
	function getUploadFileConfig(){
		var uploadIframeUI = new mw.UploadIframeUI( function( method ){
			// Get all the arguments after the "method"
			var args = $j.makeArray( arguments ).splice( 1 );			
			// Send the client the msg:
			sendClientMsg( {
				'event' : 'uploadUI',
				'method' : method,
				// Get all the arguments after the "method"
				'arguments' : args
			} );
		} );
				
		var uploadConfig = {		
			// Set the interface type
			'ui' : uploadIframeUI,
			
			// Set the select file callback to update clientFrame
			'selectFileCb' : function( fileName ) {
				sendClientMsg( {
					'event': 'selectFileCb',
					'fileName' : fileName
				} );
			}
		}
		return 	uploadConfig;
	}
		
	/**
	* Server send interface action
	*/
	function serverSendUploadHandlerAction( action ) {
		// Get a refrence to the uploadHandler:
		// NOTE: this should not be hard-coded
		var selector = ( wgEnableFirefogg ) ? '#wpUploadFile' : '#mw-upload-form';
		var uploadHandler = $j( selector ).get(0).uploadHandler;		
		if( uploadHandler ){	 
			uploadHandler.uploadHandlerAction( action );
		} else {
			mw.log( "Error: could not find upload handler" );
		}
	}
	
	/**
	* Server submit file
	* @param {Object} options Options for submiting file
	*/
	function serverSubmitFile( formData ){
		// Add the FileName and and the description to the form
		var $form = $j('#mw-upload-form');
		// Add the filename and description if missing 
		if( ! $form.find("[name='filename']").length ){
			$form.append(
				$j( '<input />' )
				.attr( {
					'id' : 'wpDestFile',
					'name' : 'filename',
					'type' : 'hidden'
 				} )
			);
		}
		if( ! $form.find("[name='description']").length ){
			$form.append(
				$j( '<input />' )
				.attr( {
					'id' : 'wpUploadDescription',
					'name' : 'comment',
					'type' : 'hidden'
				} )
			);
		}						
			
		// Update filename and description ( if set )
		if( formData.filename ) {
			$form.find( "[name='filename']" ).val( formData.filename )
		}
		if( formData.description ) {
			$form.find( "[name='description']" ).val( formData.description )
		}
		
		// Do submit the form
		$form.submit();		
	};
	
	/**
	* Outputs the result object to the client domain
	*
	* @param {msgObj} msgObj Msg to send to client domain
	*/ 
	function sendClientMsg( msgObj ) {
		
		// Get a local reference to the client request		
		var clientFrame = getClientRequest()['clientFrame'];
		
		// Double check that the client is an approved domain before outputting the iframe
		if( ! isAllowedClientFrame ( clientFrame ) ) {
			mw.log( "cant send msg to " + clientFrame );
			return false;
		}
		var nestName = 'NestedFrame_' + $j( 'iframe' ).length;
		
		// Append the iframe to body
		appendIframe( { 
			'src' : clientFrame,
			'request' : msgObj	
		} );				
	};
	
	/** 
	 * Appends an iframe to the body from a given set of options
	 * 
	 * NOTE: this uses string html building instead of jquery build-out
	 * because IE does not allow setting of iframe attributes  
	 * 
	 * @param {Object} options Iframe attribute options 
	 * 	name - the name of the iframe
	 *  src - the url for the iframe
	 *  request - the request object to be packaged into the hash url
	 *  persist - set to true if the iframe should not 
	 * 			  be removed from the dom after its done loading
	 * @param {Function} callback Function called once iframe is loaded
	 */
	function appendIframe( options, callback ){		
		var s = '<iframe ';
		var iframeAttr = ['id', 'name', 'style'];
		
		// Check for frame name:
		if( ! options[ 'name' ] ) {
			options[ 'name' ] = 'mwApiProxyFrame_' + $j('iframe').length;	
		}
		
		// Add the frame name / id:
		s += 'name="' +  mw.escapeQuotes( options[ 'name' ] ) + '" ';
		s += 'id="' +  mw.escapeQuotes( options[ 'name' ] ) + '" ';		
		
		// Check for style: 
		if( ! options['style'] ){
			options['style'] = 'display:none';
		}
		// Add style attribute:
		s += 'style="' + mw.escapeQuotes( options[ 'style' ] ) + '" ';
		
		// Special handler for src and packaged hash request: 
		if( options.src ){
			s += 'src="' + options.src;
			if( options.request ){
				s += '#' + encodeURIComponent( JSON.stringify(  options.request ) );
			}
			s += '" ';
		}
		// Close up the iframe: 
		s += '></iframe>';
		
		if( ! options[ 'target' ] ){
			options[ 'target' ] = 'body';
		} 		
		// Append to body if no target set
		$j( options['target'] ).append( s );
		
		// Setup the onload callback
		$j( '#' + options['name'] ).get( 0 ).onload = function() {
			if( ! options.persist ){
				// Schedule the removal of the iframe
				setTimeout( function() {
					$j('#' +  options[ 'name' ] ).remove();
				}, 10 );
			}
			// Call the onload callback if set:
			if( callback ){
				callback();		
			}
		};				
	}
		
} )( window.mw.ApiProxy );
