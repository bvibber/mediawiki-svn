// Add support for html5 / mwEmbed elements to IE
// For discussion and comments, see: http://remysharp.com/2009/01/07/html5-enabling-script/
'video audio source track'.replace(/\w+/g,function( n ){ document.createElement( n ) } );

/**
 * mwEmbed.core includes shared mwEmbed utilities  
 * 
 * @license
 * mwEmbed
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * 
 * @copyright (C) 2010 Kaltura 
 * @author Michael Dale ( michael.dale at kaltura.com )
 * 
 * @url http://www.kaltura.org/project/HTML5_Video_Media_JavaScript_Library
 *
 * Libraries used include code license in headers
 */


/**
 * Setup the "mw" global:
 */
if ( typeof window.mw == 'undefined' ) {
	window.mw = { };
}

/**
 * Set the mwEmbedVersion
 */
var MW_EMBED_VERSION = '1.1g';

// Globals to pre-set ready functions in dynamic loading of mwEmbed
if( typeof preMwEmbedReady == 'undefined'){
	var preMwEmbedReady = [];	
}
// Globals to pre-set config values in dynamic loading of mwEmbed
if( typeof preMwEmbedConfig == 'undefined') {
	var preMwEmbedConfig = [];
}

/**
 * Global mw object:
 */
( function( mw ) {

	/**
	 * Add a function to be run during setup ( prior to mw.ready) this is useful
	 * for building out interfaces that should be ready before mw.ready is
	 * called.
	 * 
	 * @param {callback}
	 *            Function Callback function must accept a ready function
	 *            callback to be called once setup is done
	 */
	var mwSetupFunctions = []; // Array of setup functions
	mw.addSetupHook = function( callback ) {
		mwSetupFunctions.push ( callback ) ;
	};
	
	// flag for mwSetup being run: 
	mwSetupFlag = false;
	mw.setupMwEmbed = function(){
		mw.log( 'mw.setupMwEmbed' );
		// Only run the setup once:
		if( mwSetupFlag ) {
			return ;
		}
		mwSetupFlag = true;			
		// check if mediaWiki has already been setup. 
		if (! window.mediaWiki ) {
			// @@todo load mediaWiki from load.php
			
			// Issue a call to load.php to get the set of modules in a way that 'mediaWiki' understands. 
		} else { 
			mw.setupWithmediaWikiJS();
		}
	};	
	mw.setupWithmediaWikiJS =  function(){
		// Update mediaWiki object with mwEmbed tools / helpers
		
		// Now with window.mediaWiki we can run all the calls and trigger the mw.ready
		
		// run setup callbacks
		function runSetupFunctions() {							
			if( mwSetupFunctions.length ) {
				mwSetupFunctions.shift()( function() {
					runSetupFunctions();
				} );
			}else{
				mw.runReadyFunctions();
			}
		}
		runSetupFunctions();		 
	};	
	
	
	/**
	 * Enables load hooks to run once mwEmbeed is "ready" Will ensure jQuery is
	 * available, is in the $j namespace and mw interfaces and configuration has
	 * been loaded and applied
	 * 
	 * This is different from jQuery(document).ready() ( jQuery ready is not
	 * friendly with dynamic includes and not friendly with core interface
	 * asynchronous build out. )
	 * 
	 * @param {Function}
	 *            callback Function to run once DOM and jQuery are ready
	 */
	var mwOnLoadFunctions = [];	// Setup the local mwOnLoadFunctions array:		
	var mwReadyFlag = false; // mw Ready flag ( set once mwEmbed is ready )
	mw.ready = function( callback ) {						
		if( mwReadyFlag === false ) {		
			// Add the callbcak to the onLoad function stack
			mwOnLoadFunctions.push ( callback );						
		} else { 
			// If mwReadyFlag is already "true" issue the callback directly:
			callback();
		}		
	}
	
	/**
	 * Runs all the queued functions called by mwEmbedSetup
	 */ 
	mw.runReadyFunctions = function ( ) {
		mw.log('mw.runReadyFunctions: ' + mwOnLoadFunctions.length );				
		// Run any pre-setup ready functions
		while( preMwEmbedReady.length ){
			preMwEmbedReady.shift()();
		}		
		// Run all the queued functions:
		while( mwOnLoadFunctions.length ) {
			mwOnLoadFunctions.shift()();
		}							
		// Sets mwReadyFlag to true so that future mw.ready run the
		// callback directly
		mwReadyFlag = true;			
		
	}	
	
	
	/* User config */
	
	var setupUserConfigFlag = false;
	mw.setupUserConfig = function( callback ) {	
		if( setupUserConfigFlag ) {
			if( callback ) { 
				callback();
			}
			return ;
		}
		// Do Setup user config:
		mw.load( [ '$j.cookie', 'JSON' ], function() {			
			if( $j.cookie( 'mwUserConfig' ) ) {
				mwUserConfig = JSON.parse( $j.cookie( 'mwUserConfig' ) );
			}									
			setupUserConfigFlag = true;
			if( callback ) {
				callback();	
			}			
		});				
	}

	/**
	 * Save a user configuration var to a cookie & local global variable Loads
	 * the cookie plugin if not already loaded
	 * 
	 * @param {String}
	 *            name Name of user configuration value
	 * @param {String}
	 *            value Value of configuration name
	 */
	mw.setUserConfig = function ( name, value, cookieOptions ) {
		if( ! setupUserConfigFlag ) { 
			mw.log( "Error: userConfig not setup" );
			return false; 		
		}		
		// Update local value
		mwUserConfig[ name ] = value;
		
		// Update the cookie ( '$j.cookie' & 'JSON' should already be loaded )
		$j.cookie( 'mwUserConfig', JSON.stringify( mwUserConfig ) );
	}
	
	/**
	 * Save a user configuration var to a cookie & local global variable
	 * 
	 * @param {String}
	 *            name Name of user configuration value
	 * @return value of the configuration name false if the configuration name
	 *         could not be found
	 */	
	mw.getUserConfig = function ( name ) {
		if( mwUserConfig[ name ] )
			return mwUserConfig[ name ];
		return false;
	}
	
	
	/**
	 * Aliased functions 
	 * 
	 * Wrap mediaWiki functionality while we port over the libraries 
	 */
	mw.setConfig = function( name, value ){
		mediaWiki.config.set( name, value );
	};
	mw.getConfig = function( name, value ){
		mediaWiki.config.get( name, value );
	};
	mw.setDefaultConfig = function( name, value ){
		//@@ FIXME only set if not already present: 
		mediaWiki.config.set( name, value );
	};
	mw.load = function( resources, callback ){
		mediaWiki.using( resources, callback, function(){
			// failed to load
		})
	};
	mw.addModuleLoader = function ( name, loaderFunction ) {
		mediaWiki.register( name, 0, loaderFunction );
	}
	
	
	/**
	 * Utility Functions
	 */		
	
	/**
	 * Given a float number of seconds, returns npt format response. ( ignore
	 * days for now )
	 * 
	 * @param {Float}
	 *            sec Seconds
	 * @param {Boolean}
	 *            show_ms If milliseconds should be displayed.
	 * @return {Float} String npt format
	 */
	mw.seconds2npt = function( sec, show_ms ) {
		if ( isNaN( sec ) ) {
			mw.log("Warning: trying to get npt time on NaN:" + sec);			
			return '0:00:00';
		}
		
		var tm = mw.seconds2Measurements( sec )
				
		// Round the number of seconds to the required number of significant
		// digits
		if ( show_ms ) {
			tm.seconds = Math.round( tm.seconds * 1000 ) / 1000;
		} else {
			tm.seconds = Math.round( tm.seconds );
		}
		if ( tm.seconds < 10 ){
			tm.seconds = '0' +	tm.seconds;
		}
		if( tm.hours == 0 ){
			hoursStr = ''
		} else {
			if ( tm.minutes < 10 )
				tm.minutes = '0' + tm.minutes;
			
			hoursStr = tm.hours + ":"; 
		}
		return hoursStr + tm.minutes + ":" + tm.seconds;
	}
	
	/**
	 * Given seconds return array with 'days', 'hours', 'min', 'seconds'
	 * 
	 * @param {float}
	 *            sec Seconds to be converted into time measurements
	 */
	mw.seconds2Measurements = function ( sec ){
		var tm = {};
		tm.days = Math.floor( sec / ( 3600 * 24 ) )
		tm.hours = Math.floor( sec / 3600 );
		tm.minutes = Math.floor( ( sec / 60 ) % 60 );
		tm.seconds = sec % 60;
		return tm;
	}
	
	/**
	 * Take hh:mm:ss,ms or hh:mm:ss.ms input, return the number of seconds
	 * 
	 * @param {String}
	 *            npt_str NPT time string
	 * @return {Float} Number of seconds
	 */
	mw.npt2seconds = function ( npt_str ) {
		if ( !npt_str ) {
			// mw.log('npt2seconds:not valid ntp:'+ntp);
			return false;
		}
		// Strip {npt:}01:02:20 or 32{s} from time if present
		npt_str = npt_str.replace( /npt:|s/g, '' );
	
		var hour = 0;
		var min = 0;
		var sec = 0;
	
		times = npt_str.split( ':' );
		if ( times.length == 3 ) {
			sec = times[2];
			min = times[1];
			hour = times[0];
		} else if ( times.length == 2 ) {
			sec = times[1];
			min = times[0];
		} else {
			sec = times[0];
		}
		// Sometimes a comma is used instead of period for ms
		sec = sec.replace( /,\s?/, '.' );
		// Return seconds float
		return parseInt( hour * 3600 ) + parseInt( min * 60 ) + parseFloat( sec );
	}
	
	/**
	 * addLoaderDialog small helper for displaying a loading dialog
	 * 
	 * @param {String}
	 *            dialogHtml text Html of the loader msg
	 */
	mw.addLoaderDialog = function( dialogHtml ) {
		$dialog = mw.addDialog( {
			'title' : dialogHtml, 
			'content' : dialogHtml + '<br>' + 
				$j('<div />')
				.loadingSpinner()
				.html() 
		});
		return $dialog;
	}
	
	/**
	 * Mobile HTML5 has special properties for html5 video::
	 * 
	 * NOTE: should be phased out in favor of browser feature detection where possible
	 */
	mw.isMobileHTML5 = function() {
		// check mobile safari foce ( for debug )
		if( mw.getConfig( 'forceMobileHTML5' ) || document.URL.indexOf('forceMobileHTML5') != -1 ){
			return true;
		}
		if (( navigator.userAgent.indexOf('iPhone') != -1) || 
			( navigator.userAgent.indexOf('iPod') != -1) || 
			( navigator.userAgent.indexOf('iPad') != -1) ||
			( mw.isAndroid2() )  
		) {
			return true;
		}
		return false;
	};
	// Android 2 has some restrictions vs other mobile platforms 
	mw.isAndroid2 = function(){
		if ( navigator.userAgent.indexOf('Android 2.') != -1) {
			return true;
		}
		return false;
	};
	
	/**
	 * Add a (temporary) dialog window:
	 * 
	 * @param {Object} with following keys: 
	 *            title: {String} Title string for the dialog
	 *            content: {String} to be inserted in msg box
	 *            buttons: {Object} A button object for the dialog Can be a string
	 *            				for the close button
	 * 			  any jquery.ui.dialog option 
	 */
	mw.addDialog = function ( options ) {
		// Remove any other dialog
		$j( '#mwTempLoaderDialog' ).remove();			
		
		if( !options){
			options = {};
		}
	
		// Extend the default options with provided options
		var options = $j.extend({
			'bgiframe': true,
			'draggable': true,
			'resizable': false,
			'modal': true
		}, options );
		
		if( ! options.title || ! options.content ){
			mw.log("Error: mwEmbed addDialog missing required options ( title, content ) ")
			return ;
		}
		
		// Append the dialog div on top:
		$j( 'body' ).append( 
			$j('<div />') 
			.attr( {
				'id' : "mwTempLoaderDialog",
				'title' : options.title
			})
			.css({
				'display': 'none'
			})
			.append( options.content )
		);
	
		// Build the uiRequest
		var uiRequest = [ '$j.ui.dialog' ];
		if( options.draggable ){
			uiRequest.push( '$j.ui.draggable' )
		}
		if( options.resizable ){
			uiRequest.push( '$j.ui.resizable' );
		}
		
		// Special button string 
		if ( typeof options.buttons == 'string' ) {
			var buttonMsg = options.buttons;
			buttons = { };
			options.buttons[ buttonMsg ] = function() {
				$j( this ).dialog( 'close' );
			}
		}				
		
		// Load the dialog resources
		mw.load([
			[
				'$j.ui'
			],
			uiRequest
		], function() {
			$j( '#mwTempLoaderDialog' ).dialog( options );
		} );
		return $j( '#mwTempLoaderDialog' );
	}
	
	/**
	 * Close the loader dialog created with addLoaderDialog
	 */
	mw.closeLoaderDialog = function() {
		// Make sure the dialog resource is present
		if( !mw.isset( '$j.ui.dialog' ) ) {
			return false;
		}
		$j( '#mwTempLoaderDialog' ).dialog( 'destroy' ).remove();
	}
	
	
	/**
	 * Similar to php isset function checks if the variable exists. Does a safe
	 * check of a descendant method or variable
	 * 
	 * @param {String}
	 *            objectPath
	 * @return {Boolean} true if objectPath exists false if objectPath is
	 *         undefined
	 */	
	mw.isset = function( objectPath ) {
		if ( !objectPath ) {
			return false;
		}			
		var pathSet = objectPath.split( '.' );
		var cur_path = '';
				
		for ( var p = 0; p < pathSet.length; p++ ) {
			cur_path = ( cur_path == '' ) ? cur_path + pathSet[p] : cur_path + '.' + pathSet[p];
			eval( 'var ptest = typeof ( ' + cur_path + ' ); ' );
			if ( ptest == 'undefined' ) {			
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Wait for a object to be defined and the call the callback
	 * 
	 * @param {Object}
	 *            objectName Name of object to be defined
	 * @param {Function}
	 *            callback Function to call once object is defined
	 * @param {Null}
	 *            callNumber Used internally to keep track of number of times
	 *            waitForObject has been called
	 */
	var waitTime = 1200; // About 30 seconds
	mw.waitForObject = function( objectName, callback, _callNumber) {	
		// mw.log( 'waitForObject: ' + objectName + ' cn: ' + _callNumber);
				
		// Increment callNumber:
		if( !_callNumber ) { 
			_callNumber = 1;
		} else {
			_callNumber++;
		}
		
		if( _callNumber > waitTime ) {
			mw.log( "Error: waiting for object: " + objectName + ' timeout ' );
			callback( false ); 
			return ;
		}
		
		// If the object is defined ( or we are done loading from a callback )
		if ( mw.isset( objectName ) || mwLoadDoneCB[ objectName ] == 'done' ) {			
			callback( objectName )
		}else{
			setTimeout( function( ) {
				mw.waitForObject( objectName, callback, _callNumber);
			}, 25);
		}
	}
	
	/**
	 * Check if an object is empty or if its an empty string.
	 * 
	 * @param {Object}
	 *            object Object to be checked
	 */ 
	mw.isEmpty = function( object ) {		
		if( typeof object == 'string' ) { 
			if( object == '' ) return true;
			// Non empty string:
			return false;
		}
		
		// If an array check length:
		if( Object.prototype.toString.call( object ) === "[object Array]"
			&& object.length == 0 ) {
			return true;
		}
		
		// Else check as an object:
		for( var i in object ) { return false; }
		
		// Else object is empty:
		return true;
	}
	
	/**
	 * Log a string msg to the console
	 * 
	 * all mw.log statements will be removed on minification so lots of mw.log
	 * calls will not impact performance in non debug mode
	 * 
	 * @param {String}
	 *            string String to output to console
	 */
	mw.log = function( string ) {
		// Add any prepend debug strings if necessary
		if ( mw.getConfig( 'pre-append-log' ) ){
			string = mw.getConfig( 'pre-append-log' ) + string;		
		}
		
		if ( window.console ) {
			window.console.log( string );
		} else {	
			/**
			 * Old IE and non-Firebug debug: ( commented out for now )
			 */						
			/*var log_elm = document.getElementById('mv_js_log'); 
			if(!log_elm) {				
				document.getElementsByTagName("body")[0].innerHTML += '<div ' +
					'style="position:absolute;z-index:500;bottom:0px;left:0px;right:0px;height:200px;">' + 
					'<textarea id="mv_js_log" cols="120" rows="12"></textarea>' + 
				'</div>';
			}
			var log_elm = document.getElementById('mv_js_log'); 
			if(log_elm) {
				log_elm.value+=string+"\n"; 
			}*/			
		}
	}	
	
	
	/**
	 * This will get called when the DOM is ready Will check configuration and
	 * issue a mw.setupMwEmbed call if needed
	 */
	// Flag to register the domReady has been called
	var mwDomReadyFlag = false;
	mw.domReady = function ( ) {
		if( mwDomReadyFlag ) {
			return ;		
		}		
		// Set the onDomReady Flag
		mwDomReadyFlag = true;			
		mw.setupMwEmbed();	
	}
	
	/**
	 * Set DOM-ready call We copy jQuery( document ).ready here since sometimes
	 * mwEmbed.js is included without jQuery and we need our own "ready" system so
	 * that mwEmbed interfaces can support async built out and the include of
	 * jQuery.
	 */
	var mwDomIsReady = false;
	function runMwDomReady(){
		mwDomIsReady  = true;
		if( mw.domReady ){
			mw.domReady()
		}
	}
	// Check if already ready:
	if ( document.readyState === "complete" ) {
		runMwDomReady();
	}

	// Cleanup functions for the document ready method
	if ( document.addEventListener ) {
		DOMContentLoaded = function() {
			document.removeEventListener( "DOMContentLoaded", DOMContentLoaded, false );
			runMwDomReady();
		};

	} else if ( document.attachEvent ) {
		DOMContentLoaded = function() {
			// Make sure body exists, at least, in case IE gets a little overzealous
			// (ticket #5443).
			if ( document.readyState === "complete" ) {
				document.detachEvent( "onreadystatechange", DOMContentLoaded );
				runMwDomReady();
			}
		};
	}
	// Mozilla, Opera and webkit currently support this event
	if ( document.addEventListener ) {
		// Use the handy event callback
		document.addEventListener( "DOMContentLoaded", DOMContentLoaded, false );
		
		// A fallback to window.onload, that will always work
		window.addEventListener( "load", mw.domReady, false );

	// If IE event model is used
	} else if ( document.attachEvent ) {
		// ensure firing before onload,
		// maybe late but safe also for iframes
		document.attachEvent("onreadystatechange", DOMContentLoaded);
		
		// A fallback to window.onload, that will always work
		window.attachEvent( "onload", runMwDomReady );

		// If IE and not a frame
		// continually check to see if the document is ready
		var toplevel = false;

		try {
			toplevel = window.frameElement == null;
		} catch(e) {}

		if ( document.documentElement.doScroll && toplevel ) {
			doScrollCheck();
		}
	}
	// The DOM ready check for Internet Explorer
	function doScrollCheck() {
		if ( mwDomIsReady ) {
			return;
		}

		try {
			// If IE is used, use the trick by Diego Perini
			// http://javascript.nwbox.com/IEContentLoaded/
			document.documentElement.doScroll("left");
		} catch( error ) {
			setTimeout( doScrollCheck, 1 );
			return;
		}

		// and execute any waiting functions
		runMwDomReady();
	}

	
} )( window.mw );




