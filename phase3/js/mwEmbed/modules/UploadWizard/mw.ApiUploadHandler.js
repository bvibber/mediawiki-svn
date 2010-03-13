/**
 * An attempt to refactor out the stuff that does API-via-iframe transport
 * In the hopes that this will eventually work for AddMediaWizard too
 */

// n.b. if there are message strings, or any assumption about HTML structure of the form.
// then we probably did it wrong

/**
 * Represents an object which configures a form to upload its files via an iframe talking to the MediaWiki API.
 * @param an UploadInterface object, which contains a .form property which points to a real HTML form in the DOM
 */
mw.ApiUploadHandler = function( ui ) {
	var _this = this;

	_this.ui = ui;

	var form = _this.ui.form;

	_this.transportedCallbacks = [];
	_this.progressCallbacks = [];
	_this.errorCallbacks = [];

	_this.configureForm();

	// hardcoded for now
	// can also use Xhr Binary depending on config
	_this.transport = new mw.IframeTransport(
		_this.ui.form, 
		function( fraction ){ _this.progress( fraction ) },
		function( result ) { _this.transported( result ) }
	);

};

mw.ApiUploadHandler.prototype = {
	/**
	 * Allow other parties to register interest in how we are progressing
	 * @param callback which accepts a float between 0 and 1 as our current progress
	 */
	addProgressCb: function( fn ) {
		var _this = this;
		_this.progressCallbacks.push( function( progress ) { fn( progress ) } ); 				
	},

	/**
	 * Allow other parties to register interest in when we finish uploading
	 * @param callback
	 */
	addTransportedCb: function( f ) {
		var _this = this;
		_this.transportedCallbacks.push( f );
	},

	/**
	 * Allow other parties to register interest in when we have an error
	 * @param callback
	 */
	addErrorCb: function( f ) {
		var _this = this;
		_this.errorCallbacks.push( f );
	},

	/**
	 * Configure an HTML form so that it will submit its files to our transport (an iframe)
	 * with proper params for the API
	 * @param callback
	 */
	configureForm: function() {
		var apiUrl = mw.getLocalApiUrl(); // XXX or? throw new Error( "configuration", "no API url" );
		if ( ! ( mw.getConfig( 'token' ) ) ) {
			throw new Error( "configuration", "no edit token" );	
		}

		var _this = this;
		mw.log( "configuring form for Upload API" );

		// Set the form action
		try {
			$j( _this.ui.form ) 	
				.attr( 'action', apiUrl )
				.attr( 'method', 'POST' )
				.attr( 'enctype', 'multipart/form-data' );
		} catch ( e ) {
			alert( "oops, form modification didn't work in ApiUploadHandler" );
			mw.log( "IE for some reason error's out when you change the action" );
			// well, if IE fucks this up perhaps we should do something to make sure it writes correctly
			// from the outset?
		}
		
		_this.addFormInputIfMissing( 'token', mw.getConfig( 'token' ));
		_this.addFormInputIfMissing( 'action', 'upload' );
		_this.addFormInputIfMissing( 'format', 'jsonfm' );
		
		// XXX only for testing, so it stops complaining about dupes
		if ( mw.getConfig( 'debug' )) {
			_this.addFormInputIfMissing( 'ignorewarnings', '1' );
		}
	},

	/**
	 * Add a hidden input to a form  if it was not already there.
	 * @param name  the name of the input
	 * @param value the value of the input
	 */
	addFormInputIfMissing: function( name, value ) {
		var _this = this;
		var $jForm = $j( _this.ui.form );
		if ( $jForm.find( "[name='" + name + "']" ).length == 0 ) {
			$jForm.append( 
				$j( '<input />' )
				.attr( { 
					'type': "hidden",
					'name' : name, 
					'value' : value 
				} )
			);
		}
	},

	/**
	 * Kick off the upload!
	 */
	start: function() {
		var _this = this;
		mw.log( "api: upload start!" );
		_this.beginTime = ( new Date() ).getTime();
		_this.ui.start();
		_this.ui.busy();
		$j( this.ui.form ).submit();
	},

	/**
	 * Central dispatch function for every other object interested in our progress
	 * @param fraction	float between 0 and 1, representing progress
	 */
	progress: function( fraction ) {
		mw.log( "api: upload progress!" );
		var _this = this;
		_this.ui.progress( fraction );
		for ( var i = 0; i < _this.progressCallbacks.length; i++ ) {
			_this.progressCallbacks[i]( fraction );
		}
	},

	/** 
	 * Central dispatch function for everyone else interested if we've transported
	 * @param result  javascript object representing MediaWiki API result.
	 */
	transported: function( result ) {
		mw.log( "api: upload transported!" );
		var _this = this;
		_this.ui.transported();
		for ( var i = 0; i < _this.transportedCallbacks.length; i++ ) {
			_this.transportedCallbacks[i]( result );
		}
	},

	/** 
	 * Central dispatch function for everyone else interested if we've had an error
	 * @param error  the error
	 */
	error: function( error ) {
		mw.log( "api: error!" );
		var _this = this;
		_this.ui.error( error );
		for ( var i = 0; i < _this.errorCallbacks.length; i++ ) {
			_this.errorCallbacks[i]( error );
		}
	}
};



